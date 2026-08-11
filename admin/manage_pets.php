<?php
session_start();
include '../includes/config.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../users/login.php");
    exit();
}

// Fetch all pets
$pets_result = $conn->query("SELECT * FROM pets ORDER BY date_posted DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Pets</title>
    <link rel="stylesheet" href="../assets/css/adminn.css">
    <script>
        function confirmDelete(petId) {
            document.getElementById('deletePetId').value = petId;
            document.getElementById('deleteModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }
    </script>
    <style>
        /* Modal Styling */
        .modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            z-index: 1000;
        }
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: 999;
        }
        .modal button {
            margin: 10px;
            padding: 8px 16px;
            border: none;
            cursor: pointer;
        }
        .btn-delete {
            background: #ff4081;
            color: white;
            border-radius: 5px;
        }
        .btn-cancel {
            background: #ccc;
            color: black;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<header>
    <div class="logo">🐾 Pet Haven Admin</div>
    <ul class="nav-links">
        <li><a href="admin.php">Home</a></li>
        <li><a href="../users/logout.php" class="btn">Logout</a></li>
    </ul>
</header>

    <div class="pet-container">
        <h2>Manage Pets</h2>
        <div class="pets-list">
            <?php while ($pet = $pets_result->fetch_assoc()): ?>
                <div class="pet-card">
                    <img src="../assets/images/<?php echo htmlspecialchars($pet['image']); ?>" alt="Pet">
                    <h4><?php echo htmlspecialchars($pet['name']); ?></h4>
                    <p><?php echo htmlspecialchars($pet['breed']); ?> | <?php echo htmlspecialchars($pet['age']); ?>  </p>
                    <p><?php echo htmlspecialchars($pet['gender']); ?></p>
                    <p>Posted On: <strong><?php echo date("F j, Y", strtotime($pet['date_posted'])); ?></strong></p>
                    <p>Status: <strong><?php echo htmlspecialchars($pet['status']); ?></strong></p>
                    <a href="edit_pet.php?id=<?php echo $pet['id']; ?>" class="btn">Edit</a>
                    <button onclick="confirmDelete(<?php echo $pet['id']; ?>)" class="btn btn-delete">Delete</button>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal-overlay" id="deleteOverlay"></div>
    <div class="modal" id="deleteModal">
        <p>Are you sure you want to delete this pet?</p>
        <form action="delete_pet.php" method="POST">
            <input type="hidden" name="pet_id" id="deletePetId">
            <button type="submit" class="btn btn-delete">Yes, Delete</button>
            <button type="button" class="btn btn-cancel" onclick="closeModal()">Cancel</button>
        </form>
    </div>
        <!-- Footer -->
        <footer>
        <p>© 2025 Pet Haven. All rights reserved by <strong>SUTEJ NAIK</strong>. 🐾</p>
    </footer>
</body>
</html>
