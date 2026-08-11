<?php
session_start();
include '../includes/config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$stmt = $conn->prepare("SELECT username, email, contact, address FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

// Update user details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $contact = htmlspecialchars($_POST['contact']);
    $address = htmlspecialchars($_POST['address']);

    // Check for duplicate username, email, or contact for other users
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE (email = ? OR username = ? OR contact = ?) AND id != ?");
    $check_stmt->bind_param("sssi", $email, $username, $contact, $user_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $error_msg = "Username, email, or contact number already exists. Please use different values.";
    } else {
        // Proceed with update
        $update_stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, contact = ?, address = ? WHERE id = ?");
        $update_stmt->bind_param("ssssi", $username, $email, $contact, $address, $user_id);

        if ($update_stmt->execute()) {
            $success_msg = "Profile updated successfully!";
            // Update the displayed user info
            $user['username'] = $username;
            $user['email'] = $email;
            $user['contact'] = $contact;
            $user['address'] = $address;
        } else {
            $error_msg = "Error updating profile. Please try again.";
        }
    }
}

?>

<header>
    <div class="logo">🐾 Pet Haven</div>
    <ul class="nav-links">
        <li><a href="../index.php">Home |</a></li>
        <li><a href="../pets/pets.php">Adopt |</a></li>
        <li><a href="../pets/submit_pet.php">Submit Pet |</a></li>
        <li><a href="../contact.php">Contact |</a></li>
        <li><a href="profile.php">Profile </a></li>
    </ul>
</header>

<!DOCTYPE html>
<html lang="en">
<head>
<link href="https://fonts.googleapis.com/css2?family=MuseoModerno&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="../assets/css/profile.css">
</head>
<body>
    <div class="container">
        <h2>Edit Profile</h2>

        <?php if (isset($success_msg)): ?>
            <div class="success-msg"><?php echo $success_msg; ?></div>
        <?php endif; ?>

        <?php if (isset($error_msg)): ?>
            <div class="error-msg"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <form action="edit_profile.php" method="POST" class="profile-form">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="contact">Contact:</label>
                <input type="text" id="contact" name="contact" value="<?php echo htmlspecialchars($user['contact']); ?>" required>
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <textarea id="address" name="address" required><?php echo htmlspecialchars($user['address']); ?></textarea>
            </div>
            <button type="submit" class="btn">Update Profile</button>
        </form>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
