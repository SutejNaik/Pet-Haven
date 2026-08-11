<?php
session_start();
include 'includes/config.php';

$success = $error = "";
$name = $email = $contact = "";
$user_id = null;

// Auto-fill if user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $user_q = mysqli_query($conn, "SELECT username, email, contact FROM users WHERE id = $user_id");
    if ($user_q && mysqli_num_rows($user_q) > 0) {
        $user = mysqli_fetch_assoc($user_q);
        $name = $user['username'];
        $email = $user['email'];
        $contact = $user['contact'];
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $form_name = trim(htmlspecialchars($_POST['name']));
    $form_email = trim(htmlspecialchars($_POST['email']));
    $form_contact = trim(htmlspecialchars($_POST['contact'])); 
    $form_message = trim(htmlspecialchars($_POST['message']));

    if (!empty($form_name) && !empty($form_email) && !empty($form_contact) && !empty($form_message)) {
        $stmt = $conn->prepare("INSERT INTO contact_messages (user_id, name, email, contact, message, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("issss", $user_id, $form_name, $form_email, $form_contact, $form_message);

        if ($stmt->execute()) {
            $success = "✅ Your message has been sent successfully!";
            $name = $email = $contact = "";
        } else {
            $error = "❌ There was an error sending your message. Please try again.";
        }
    } else {
        $error = "⚠️ All fields are required!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/css2?family=MuseoModerno&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Haven - Contact</title>
    <link rel="stylesheet" href="assets/css/contact.css">
    <script src="assets/js/script.js"></script>
</head>
<body>

<!-- Navigation -->
<header>
    <div class="logo">🐾 Pet Haven</div>
    <ul class="nav-links">
        <li><a href="index.php">Home |</a></li>
        <li><a href="abt.php">About Us |</a></li>
        <li><a href="pets/pets.php">Adopt |</a></li>
        <li><a href="pets/submit_pet.php">Submit Pet |</a></li>
        <li>
            <?php if (isset($_SESSION['username'])): ?>
                <a href="users/profile.php" class="btn">Profile</a>
            <?php else: ?>
                <a href="users/login.php" class="btn">Login</a>
            <?php endif; ?>
        </li>
    </ul>
</header>

<!-- Contact Form -->
<div class="container">
    <h2>Contact Us</h2>

    <?php if (!empty($success)): ?>
        <div class="success-msg"><?php echo $success; ?></div>
    <?php elseif (!empty($error)): ?>
        <div class="error-msg"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="contact.php" method="POST">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" placeholder="Enter your full name" value="<?php echo htmlspecialchars($name); ?>" required>
        </div>
        <div class="form-group">
            <label for="contact">Contact Number:</label>
            <input type="text" id="contact" name="contact" placeholder="Enter your phone number" value="<?php echo htmlspecialchars($contact); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Enter your email address" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>
        <div class="form-group">
            <label for="message">Message:</label>
            <textarea id="message" name="message" placeholder="Type your message here..." required></textarea>
        </div>
        <button type="submit" class="submit-btn">Send Message</button>
    </form>
    <a href="index.php" class="btn back-btn">🏠 Back to Home</a>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
