<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    die("Access denied.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pet_id']) && is_numeric($_POST['pet_id'])) {
    $pet_id = intval($_POST['pet_id']);

    // Check if pet exists
    $check_stmt = $conn->prepare("SELECT * FROM pets WHERE id = ?");
    $check_stmt->bind_param("i", $pet_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        // Delete pet
        $stmt = $conn->prepare("DELETE FROM pets WHERE id = ?");
        $stmt->bind_param("i", $pet_id);

        if ($stmt->execute()) {
            header("Location: manage_pets.php?success=deleted");
            exit();
        } else {
            die("Error deleting pet.");
        }
    } else {
        die("Pet not found.");
    }
} else {
    die("Invalid request.");
}
?>
