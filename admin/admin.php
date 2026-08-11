<?php
session_start();
include '../includes/config.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../users/login.php");
    exit();
}

// Fetch counts for dashboard
$pets_count = $conn->query("SELECT COUNT(*) AS count FROM pets")->fetch_assoc()['count'];
$adoptions_count = $conn->query("SELECT COUNT(*) AS count FROM adoptions WHERE status='Approved'")->fetch_assoc()['count'];
$pending_adoptions = $conn->query("SELECT COUNT(*) AS count FROM adoptions WHERE status='Pending'")->fetch_assoc()['count'];
$messages_count = $conn->query("SELECT COUNT(*) AS count FROM contact_messages")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/adminn.css">
</head>
<body>

<header>
    <div class="logo">🐾 Pet Haven Admin</div>
    <ul class="nav-links">
        <li><a href="../users/logout.php" class="btn">Logout</a></li>
    </ul>
</header>

    <div class="container">
        <h2>Admin Dashboard</h2>
        <div class="dashboard">
            <div class="box">
                <h3>Total Pets</h3>
                <p><?php echo $pets_count; ?></p>
            </div>
            <div class="box">
                <h3>Total Adoptions</h3>
                <p><?php echo $adoptions_count; ?></p>
            </div>
            <div class="box">
                <h3>Pending Adoptions</h3>
                <p><?php echo $pending_adoptions; ?></p>
            </div>
            <div class="box">
                <h3>Messages</h3>
                <p><?php echo $messages_count; ?></p>
            </div>
        </div>
        <div class="links">
            <a href="manage_pets.php" class="btn">Manage Pets</a>
            <a href="manage_adoptions.php" class="btn">Manage Adoptions</a>
            <a href="messages.php" class="btn">View Messages</a>
        </div>
    </div>
        <!-- Footer -->
        <footer>
        <p>© 2025 Pet Haven. All rights reserved by <strong>SUTEJ NAIK</strong>. 🐾</p>
    </footer>
</body>
</html>