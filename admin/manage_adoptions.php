<?php
session_start();
include '../includes/config.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../users/login.php");
    exit();
}


// Fetch all adoption requests
$adoptions_result = $conn->query("
    SELECT a.id, a.message, a.status, p.name AS pet_name, u.username AS adopter_name, u.contact, u.address, u.email
    FROM adoptions a
    JOIN pets p ON a.pet_id = p.id
    JOIN users u ON a.adopter_id = u.id
");



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $adoption_id = $_POST['adoption_id'];
    $new_status = $_POST['status'];

    // Update adoption request status
    $update_stmt = $conn->prepare("UPDATE adoptions SET status = ? WHERE id = ?");
    $update_stmt->bind_param("si", $new_status, $adoption_id);
    if ($update_stmt->execute()) {
        header("Location: manage_adoptions.php");
        exit();
    } else {
        $error = "Failed to update status.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Adoptions</title>
    <link rel="stylesheet" href="../assets/css/adminn.css">
</head>
<body>

<header>
    <div class="logo">🐾 Pet Haven Admin </div>
    <ul class="nav-links">
        <li><a href="admin.php">Home</a></li>
        <li><a href="../users/logout.php" class="btn">Logout</a></li>
    </ul>
</header>

    <div class="adpt-Scontainer">
        <h2>Manage Adoptions</h2>
        <?php if (isset($error)): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>
        <div class="adoptions-list">
<?php while ($row = $adoptions_result->fetch_assoc()): ?>
    <div class="request-card">
        <h4>Pet: <?php echo htmlspecialchars($row['pet_name']); ?></h4>
        <p><strong>Adopter:</strong> <?php echo htmlspecialchars($row['adopter_name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
        <p><strong>Contact:</strong> <?php echo htmlspecialchars($row['contact']); ?></p>
        <p><strong>Message:</strong> <?php echo htmlspecialchars($row['message']); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($row['status']); ?></p>

        <!-- Form to change status -->
        <form method="post" action="manage_adoptions.php">
            <input type="hidden" name="adoption_id" value="<?php echo $row['id']; ?>">
            <select name="status">
                <option value="Pending" <?php if ($row['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                <option value="Approved" <?php if ($row['status'] == 'Approved') echo 'selected'; ?>>Approved</option>
                <option value="Rejected" <?php if ($row['status'] == 'Rejected') echo 'selected'; ?>>Rejected</option>
            </select>
            <button type="submit">Update</button>
        </form>
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
