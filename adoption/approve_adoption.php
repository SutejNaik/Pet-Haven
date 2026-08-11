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
    // Get the adoption ID from the form
    $adoption_id = $_POST['adoption_id'];

    // Fetch the adoption request details
    $stmt = $conn->prepare("SELECT a.*, p.owner_id FROM adoptions a JOIN pets p ON a.pet_id = p.id WHERE a.id = ?");
    $stmt->bind_param("i", $adoption_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $adoption = $result->fetch_assoc();

    // Check if the pet belongs to the logged-in user
    if ($adoption['owner_id'] != $user_id) {
        $_SESSION['error'] = "You do not have permission to approve this request.";
        header("Location: profile.php");
        exit();
    }

    // Update the pet status to "Adopted"
    $update_pet_stmt = $conn->prepare("UPDATE pets SET status = 'Adopted' WHERE id = ?");
    $update_pet_stmt->bind_param("i", $adoption['pet_id']);
    $update_pet_stmt->execute();
    
    // Update the adoption request status to 'Approved' and store approval date
    $update_adoption_stmt = $conn->prepare("UPDATE adoptions SET status = 'Approved', date_approved = NOW() WHERE id = ?");
    $update_adoption_stmt->bind_param("i", $adoption_id);
    $update_adoption_stmt->execute();

    if ($update_pet_stmt->affected_rows > 0 && $update_adoption_stmt->affected_rows > 0) {
        $_SESSION['success'] = "Adoption request approved successfully.";
    } else {
        $_SESSION['error'] = "Failed to approve adoption request.";
    }

    $update_pet_stmt->close();
    $update_adoption_stmt->close();
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
