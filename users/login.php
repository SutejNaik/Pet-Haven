<?php
session_start();
include '../includes/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Use Prepared Statement to prevent SQL Injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // Store user details in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['contact'] = $user['contact'];
        $_SESSION['is_admin'] = $user['is_admin'];

        // Redirect based on role
        if ($user['is_admin'] == 1) {
            header("Location: ../admin/admin.php"); 
        } else {
            header("Location: ../index.php"); 
        }
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/css2?family=MuseoModerno&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pet Haven</title>
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>
    <!-- Header / Navbar -->
    <header>
        <div class="logo">🐾 Pet Haven</div>
        <ul class="nav-links">
            <li><a href="../index.php">Home</a> |</li>
            <li><a href="../abt.php">About Us</a> |</li>
            <li><a href="../pets/pets.php">Adopt |</a></li>
            <li><a href="../pets/submit_pet.php">Submit Pet |</a></li>
            <li><a href="../contact.php">Contact</a></li>
        </ul>
    </header>

    <div class="container login-container">
        <h2>🐾 Login</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

        <form method="POST">
            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <div class="password-field">
                <input type="password" name="password" id="password" required>
                <button type="button" id="togglePassword" class="toggle-btn">Show</button>
            </div>

            <button type="submit" name="login" class="btn">Login</button>
        </form>

        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>

    <script>
    document.getElementById("togglePassword").addEventListener("click", function () {
        const passwordField = document.getElementById("password");
        const type = passwordField.getAttribute("type") === "password" ? "text" : "password";
        passwordField.setAttribute("type", type);
        this.textContent = type === "password" ? "Show" : "Hide";
    });
    </script>

    <?php include '../includes/footer.php'; ?>
</body>
</html>