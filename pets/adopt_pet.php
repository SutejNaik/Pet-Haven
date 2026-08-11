<?php
session_start();
include '../includes/config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../users/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$pet_id = $_GET['pet_id'] ?? 0;

// Fetch pet details
$pet = $conn->query("SELECT * FROM pets WHERE id = $pet_id")->fetch_assoc();
$user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();

$step = 1; // Step 1 = adoption form, Step 2 = payment form, Step 3 = final confirmation
$notificationMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Step 1: Adoption form submitted
    if (isset($_POST['step']) && $_POST['step'] == "1") {
        $_SESSION['adoption_data'] = [
            'message' => $_POST['message'],
            'contact' => $_POST['contact'],
            'address' => $_POST['address']
        ];
        $step = 2; // show payment form next
    }

    // Step 2: Payment form submitted
    elseif (isset($_POST['step']) && $_POST['step'] == "2") {
        $adoptionData = $_SESSION['adoption_data'];
        $amount = 500; // fixed adoption fee

        // Dummy payment details
        $card = $_POST['card'];
        $expiry = $_POST['expiry'];
        $cvv = $_POST['cvv'];

        // 1. Insert dummy transaction
        $stmtTrans = $conn->prepare("INSERT INTO transactions (user_id, pet_id, amount, status) VALUES (?, ?, ?, 'Success')");
        $stmtTrans->bind_param("iid", $user_id, $pet_id, $amount);

        if ($stmtTrans->execute()) {
            // 2. Insert adoption request
            $stmt = $conn->prepare("INSERT INTO adoptions (pet_id, adopter_id, message, status, contact, address) VALUES (?, ?, ?, 'Pending', ?, ?)");
            $stmt->bind_param("iisss", $pet_id, $user_id, $adoptionData['message'], $adoptionData['contact'], $adoptionData['address']);
            $stmt->execute();

            $step = 3; // final confirmation
            $notificationMessage = "🎉 Payment Successful! Adoption request submitted.<br>Transaction ID: " . $stmtTrans->insert_id;

            unset($_SESSION['adoption_data']); // clear session
        } else {
            $step = 3;
            $notificationMessage = "❌ Payment failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Adopt Pet</title>
    <link rel="stylesheet" href="../assets/css/pets.css">
    <link href="https://fonts.googleapis.com/css2?family=MuseoModerno&display=swap" rel="stylesheet">

    <style>
        .notification-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex; justify-content: center; align-items: center;
        }
        .notification-box {
            background: white; padding: 20px; border-radius: 10px;
            text-align: center; box-shadow: 0px 4px 8px rgba(0,0,0,0.2);
            max-width: 400px; width: 90%;
        }
        .notification-box p { font-size: 18px; margin-bottom: 15px; }
        .notification-box .btn {
            background: #ff4081; color: white; padding: 10px 20px;
            border: none; border-radius: 5px; text-decoration: none;
            font-size: 16px; cursor: pointer;
        }
        .notification-box .btn:hover { background: #e91e63; }
        .form-group { margin-bottom: 15px; }
        .btn-adopt {
            background: #ff4081; color: white; padding: 10px 20px;
            border: none; border-radius: 5px; cursor: pointer;
        }
        .btn-adopt:hover { background: #e91e63; }
    </style>
</head>
<body>
<!-- Header / Navbar -->
<header>
    <div class="logo">🐾 Pet Haven</div>
    <ul class="nav-links">
        <li><a href="/pet_haven_/index.php">Home |</a></li>
        <li><a href="/pet_haven_/abt.php">About Us |</a></li>
        <li><a href="/pet_haven_/pets/submit_pet.php">Submit Pet |</a></li>
        <li><a href="/pet_haven_/contact.php">Contact |</a></li>
        <li>
            <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])): ?>
                <a href="/pet_haven_/users/profile.php" class="btn">Profile</a>
            <?php else: ?>
                <a href="/pet_haven_/users/login.php" class="btn">Login</a>
            <?php endif; ?>
        </li>
    </ul>
</header>

<div class="container">
    <h2>Adopt <?php echo htmlspecialchars($pet['name']); ?></h2>

    <?php if ($step == 1): ?>
        <!-- Step 1: Adoption Form -->
        <form action="adopt_pet.php?pet_id=<?php echo $pet_id; ?>" method="POST">
            <input type="hidden" name="step" value="1">
            <div class="form-group">
                <label for="message">Message:</label>
                <textarea id="message" name="message" required></textarea>
            </div>
            <div class="form-group">
                <label for="contact">Contact:</label>
                <input type="text" id="contact" name="contact" value="<?php echo htmlspecialchars($user['contact']); ?>" required>
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required>
            </div>
            <button type="submit" class="btn-adopt">Submit Adoption Request</button>
        </form>

    <?php elseif ($step == 2): ?>
        <!-- Step 2: Payment Form -->
        <form action="adopt_pet.php?pet_id=<?php echo $pet_id; ?>" method="POST">
            <input type="hidden" name="step" value="2">
            <h3>Payment Details (Dummy)</h3>
            <p><b>Adoption Fee:</b> ₹500</p>

            <div class="form-group">
                <label>Card Number:</label>
                <input type="text" name="card" maxlength="16" required>
            </div>
            <div class="form-group">
                <label>Expiry (MM/YY):</label>
                <input type="text" name="expiry" required>
            </div>
            <div class="form-group">
                <label>CVV:</label>
                <input type="password" name="cvv" maxlength="3" required>
            </div>

            <button type="submit" class="btn-adopt">Pay & Confirm Adoption</button>
        </form>

    <?php elseif ($step == 3): ?>
        <!-- Step 3: Confirmation -->
        <div class="notification-overlay">
            <div class="notification-box">
                <p><?php echo $notificationMessage; ?></p>
                <a href="../index.php" class="btn">🏡 Go to Home</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
