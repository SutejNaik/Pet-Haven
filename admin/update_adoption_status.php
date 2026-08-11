<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../users/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $adoption_id = $_POST['adoption_id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE adoptions SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $adoption_id);
    $stmt->execute();

    header("Location: manage_adoptions.php");
    exit();
}
?>
