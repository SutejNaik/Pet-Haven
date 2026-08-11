<?php
session_start();
include '../includes/config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../users/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $adoption_id = $_POST['adoption_id'];

    // Fetch the adoption request details and pet owner
    $stmt = $conn->prepare("SELECT a.*, p.owner_id FROM adoptions a JOIN pets p ON a.pet_id = p.id WHERE a.id = ?");
    $stmt->bind_param("i", $adoption_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $adoption = $result->fetch_assoc();

    // Check if the pet belongs to the logged-in user
    if ($adoption['owner_id'] != $user_id) {
        $_SESSION['error'] = "You do not have permission to cancel this request.";
        header("Location: ../users/profile.php");
        exit();
    }

    // Update the status to 'Rejected' instead of 'Deleted'
    $update_stmt = $conn->prepare("UPDATE adoptions SET status = 'Rejected' WHERE id = ?");
    $update_stmt->bind_param("i", $adoption_id);
    $update_stmt->execute();

    if ($update_stmt->affected_rows > 0) {
        $_SESSION['success'] = "Adoption request has been rejected.";
    } else {
        $_SESSION['error'] = "Failed to reject adoption request.";
    }

    $update_stmt->close();
    $stmt->close();
    $conn->close();

    header("Location: ../users/profile.php");
    exit();
} else {
    header("Location: ../users/profile.php");
    exit();
}
?>