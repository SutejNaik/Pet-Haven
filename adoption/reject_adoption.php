<?php
session_start();
include '../includes/config.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../users/login.php");
    exit();
}

$adoption_id = $_POST['adoption_id'];

// Update adoption status to 'Rejected'
$stmt = $conn->prepare("UPDATE adoptions SET status = 'Rejected' WHERE id = ?");
$stmt->bind_param("i", $adoption_id);
if ($stmt->execute()) {
    header("Location: ../profile.php");
    exit();
} else {
    echo "Error rejecting adoption request.";
}
?>
