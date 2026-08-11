<?php
session_start();
include '../includes/config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../users/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the pet ID and new status from the form
    $pet_id = $_POST['pet_id'];
    $new_status = $_POST['status'];

    // Validate the status
    if (!in_array($new_status, ['Available', 'Adopted'])) {
        $_SESSION['error'] = "Invalid status.";
        header("Location: profile.php");
        exit();
    }

    // Check if the pet belongs to the logged-in user
    $stmt = $conn->prepare("SELECT owner_id FROM pets WHERE id = ?");
    $stmt->bind_param("i", $pet_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $pet = $result->fetch_assoc();

    if ($pet['owner_id'] != $user_id) {
        $_SESSION['error'] = "You do not have permission to update this pet.";
        header("Location: profile.php");
        exit();
    }

    // Update the pet status
    $update_stmt = $conn->prepare("UPDATE pets SET status = ? WHERE id = ?");
    $update_stmt->bind_param("si", $new_status, $pet_id);
    $update_stmt->execute();

    if ($update_stmt->affected_rows > 0) {
        $_SESSION['success'] = "Pet status updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update pet status.";
    }

    $update_stmt->close();
    $stmt->close();
    $conn->close();

    // Redirect back to the profile page
    header("Location: ../users/profile.php");
    exit();
} else {
    // If the form is not submitted, redirect to the profile page
    header("Location: ../users/profile.php");
    exit();
}
?>