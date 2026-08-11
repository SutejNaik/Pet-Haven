<?php
session_start();
include '../includes/config.php';

// Redirect logged-in admins to the dashboard
if (isset($_SESSION['user_id']) && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
    header("Location: admin.php");
    exit();
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Fetch admin details
    $stmt = $conn->prepare("SELECT id, email, password, is_admin FROM users WHERE email = ? AND is_admin = 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    // Verify password
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['is_admin'] = $admin['is_admin'];

        header("Location: admin.php"); // Redirect to Admin Panel
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../assets/css/adminn.css">
</head>
<body>

<header>
    <div class="logo">🐾 Pet Haven Admin</div>

</header>

    <div class="container">
        <h2>Admin Login</h2>
        <?php if (isset($error)): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="admin_login.php" method="POST">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
    </div>
            <footer>
        <p>© 2025 Pet Haven. All rights reserved by <strong>SUTEJ NAIK</strong>. 🐾</p>
    </footer>
</body>
</html>