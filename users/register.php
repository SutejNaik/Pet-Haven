<?php
session_start();
include '../includes/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $contact = trim($_POST['contact']);
    $address = trim($_POST['address']);

    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($contact) || empty($address)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long!";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Check if email, username, or contact already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ? OR contact = ?");
        $stmt->bind_param("sss", $email, $username, $contact);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username, Email, or Contact Number already registered!";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $is_admin = 0; // New users are not admins
            
            // Insert user into database
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, contact, address, is_admin) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssi", $username, $email, $hashed_password, $contact, $address, $is_admin);
            
            if ($stmt->execute()) {
                header("Location: login.php?success=registered");
                exit();
            } else {
                $error = "Registration failed! Try again.";
            }
        }
    }
}
?>


<!-- Header / Navbar -->
<header>
    <div class="logo">🐾 Pet Haven</div>
    <ul class="nav-links">
    <li><a href="../index.php">Home |</a></li>
    <li><a href="../abt.php">About Us |</a></li>
    <li><a href="../pets/pets.php">Adopt |</a></li>
    <li><a href="../pets/submit_pet.php">Submit Pet |</a></li>
    <li><a href="../contact.php">Contact |</a></li>

</ul>
</ul>
</header>

<!DOCTYPE html>
<html lang="en">
<head>
<link href="https://fonts.googleapis.com/css2?family=MuseoModerno&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form action="register.php" method="POST">
    <input type="text" name="username" placeholder="Username" required>
    <input type="email" name="email" placeholder="Email" required>
    <div class="password-field">
    <input type="password" id="password" name="password" placeholder="Password" required>
    <button type="button" class="toggle-btn" onclick="togglePassword('password', this)">Show</button>
</div>
<div class="password-field">
    <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter Password" required>
    <button type="button" class="toggle-btn" onclick="togglePassword('confirm_password', this)">Show</button>
</div>

    <input type="text" name="contact" placeholder="Contact Number" required>
    <input type="text" name="address" placeholder="Address" required>
    <button type="submit" name="Register" class="btn">Register</button>
</form>

        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>



    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/script.js"></script>

</body>
</html>
