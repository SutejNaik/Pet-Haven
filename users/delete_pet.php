<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../users/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['id'])) {
    $pet_id = intval($_GET['id']);

    // Check if the pet belongs to the user
    $stmt = $conn->prepare("SELECT owner_id FROM pets WHERE id = ?");
    $stmt->bind_param("i", $pet_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $pet = $result->fetch_assoc();

        if ($pet['owner_id'] == $user_id) {
            // Delete the pet record
            $delete = $conn->prepare("DELETE FROM pets WHERE id = ?");
            $delete->bind_param("i", $pet_id);
            $delete->execute();

            $_SESSION['success'] = "Pet deleted successfully.";
        } else {
            $_SESSION['error'] = "You do not have permission to delete this pet.";
        }
    } else {
        $_SESSION['error'] = "Pet not found.";
    }
    $stmt->close();
    $conn->close();
} else {
    $_SESSION['error'] = "Invalid pet ID.";
}

header('Location: ../users/profile.php');
exit();
?>
