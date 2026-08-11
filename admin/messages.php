<?php
session_start();
include '../includes/config.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../users/login.php");
    exit();
}

// Fetch all contact messages
$messages_result = $conn->query("SELECT * FROM contact_messages");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Messages</title>
    <link rel="stylesheet" href="../assets/css/adminn.css">
</head>
<body>

<header>
    <div class="logo">🐾 Pet Haven Admin</div>
    <ul class="nav-links">
        <li><a href="admin.php">Home</a></li>
        <li><a href="../users/logout.php" class="btn">Logout</a></li>
    </ul>
</header>

    <div class="msg .container">
        <h2>Messages</h2>
        <div class="messages-list">
            <?php while ($message = $messages_result->fetch_assoc()): ?>
                <div class="message-card">
                    <h4><?php echo htmlspecialchars($message['name']); ?></h4>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($message['email']); ?></p>
                    <p><strong>Contact:</strong> <?php echo htmlspecialchars($message['contact']); ?></p>
                    <p><strong>Message:</strong> <?php echo htmlspecialchars($message['message']); ?></p>
                    <p><strong>Submitted At:</strong> <?php echo htmlspecialchars($message['created_at']); ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
        <!-- Footer -->
        <footer>
        <p>© 2025 Pet Haven. All rights reserved by <strong>SUTEJ NAIK</strong>. 🐾</p>
    </footer>
</body>
</html>
