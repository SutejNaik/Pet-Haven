<?php
session_start();
include '../includes/config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../users/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$pet_id = $_POST['pet_id'];
$message = $_POST['message'];
$contact = $_POST['contact'];
$address = $_POST['address'];

// Insert adoption request
$stmt = $conn->prepare("INSERT INTO adoptions (pet_id, adopter_id, message, status, contact, address) VALUES (?, ?, ?, 'Pending', ?, ?)");
$stmt->bind_param("iisss", $pet_id, $user_id, $message, $contact, $address);
if ($stmt->execute()) {
    header("Location: ../profile.php");
    exit();
} else {
    echo "Error submitting adoption request.";
}
?>
