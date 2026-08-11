<?php
session_start();
include '../includes/config.php';

// Check admin login (assuming is_admin = 1)
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header('Location: ../users/login.php');
    exit();
}

$pet_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch pet details
$stmt = $conn->prepare("SELECT * FROM pets WHERE id = ?");
$stmt->bind_param("i", $pet_id);
$stmt->execute();
$result = $stmt->get_result();
$pet = $result->fetch_assoc();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Pet not found.";
    header("Location: manage_pets.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $type = $_POST['type'] ?? '';
    $breed = $_POST['breed'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $age = $_POST['age'] ?? '';
    $description = $_POST['description'] ?? '';
    $status = $_POST['status'] ?? 'Available';

    $image_to_save = $pet['image'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['image']['type'], $allowed_types)) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid('pet_') . '.' . $ext;
            $target_path = "../assets/images/" . $new_filename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                if ($pet['image'] && file_exists("../assets/images/" . $pet['image'])) {
                    @unlink("../assets/images/" . $pet['image']);
                }
                $image_to_save = $new_filename;
            } else {
                $_SESSION['error'] = "Failed to upload new image.";
            }
        } else {
            $_SESSION['error'] = "Only JPG, PNG, GIF images are allowed.";
        }
    }

    if (!isset($_SESSION['error'])) {
        $update = $conn->prepare("UPDATE pets SET name = ?, type = ?, breed = ?, gender = ?, age = ?, description = ?, status = ?, image = ? WHERE id = ?");
        $update->bind_param("ssssssssi", $name, $type, $breed, $gender, $age, $description, $status, $image_to_save, $pet_id);
        $update->execute();

        if ($update->affected_rows > 0) {
            $_SESSION['success'] = "Pet details updated.";
        } else {
            $_SESSION['error'] = "No changes made or update failed.";
        }
    }

    // Reload pet data after update
    $stmt = $conn->prepare("SELECT * FROM pets WHERE id = ?");
    $stmt->bind_param("i", $pet_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $pet = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/css2?family=MuseoModerno&display=swap" rel="stylesheet">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Edit Pet</title>
    <link rel="stylesheet" href="../assets/css/adminn.css" />
    <style>
        .notification {
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .success {
            background-color: #e0ffe0;
            color: #28a745;
            box-shadow: 0 0 10px #39FF14;
        }
        .error {
            background-color: #ffe0e0;
            color: #dc3545;
            box-shadow: 0 0 10px red;
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

<div class="container">
    <h2>Edit Pet (Admin)</h2>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="notification error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])): ?>
        <div class="notification success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <script>
    function showFileName() {
        const input = document.getElementById('image');
        const span = document.getElementById('file-name');
        if (input.files.length > 0) {
            const fileName = input.files[0].name;
            span.textContent = fileName + " (New Image)";
        } else {
            span.textContent = "";
        }
    }
    </script>

    <form action="" method="POST" enctype="multipart/form-data">
        <label for="image">Change Pet Image:</label><br />
        <img src="../assets/images/<?php echo htmlspecialchars($pet['image']); ?>" alt="Current Pet Image" style="max-width:150px; margin-bottom:10px; border-radius:10px; box-shadow: 0 0 10px #00E0FF;" />
        <br>
        <input type="file" name="image" id="image" accept="image/*" onchange="showFileName()" />
        <span id="file-name" style="margin-left:10px; color:#39FF14;"></span>

        <label for="name">Name:</label>
        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($pet['name']); ?>" required />

        <label for="type">Type:</label>
        <input type="text" name="type" id="type" value="<?php echo htmlspecialchars($pet['type']); ?>" required />

        <label for="breed">Breed:</label>
        <input type="text" name="breed" id="breed" value="<?php echo htmlspecialchars($pet['breed']); ?>" required />

        <label for="gender">Gender:</label>
        <select name="gender" id="gender" required>
            <option value="Male" <?php if ($pet['gender'] == 'Male') echo 'selected'; ?>>Male</option>
            <option value="Female" <?php if ($pet['gender'] == 'Female') echo 'selected'; ?>>Female</option>
        </select>

        <label for="age">Age:</label>
        <input type="text" name="age" id="age" value="<?php echo htmlspecialchars($pet['age']); ?>" required />

        <label for="description">Description:</label>
        <textarea name="description" id="description" required><?php echo htmlspecialchars($pet['description']); ?></textarea>

        <label for="status">Status:</label>
        <select name="status" id="status" required>
            <option value="Available" <?php if ($pet['status'] == 'Available') echo 'selected'; ?>>Available</option>
            <option value="Adopted" <?php if ($pet['status'] == 'Adopted') echo 'selected'; ?>>Adopted</option>
            <option value="Pending" <?php if ($pet['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
        </select>

        <button type="submit" class="btn">Update Pet Info</button>
        <a href="manage_pets.php" class="btn cancel-btn">Cancel</a>
    </form>
</div>

        <footer>
        <p>© 2025 Pet Haven. All rights reserved by <strong>SUTEJ NAIK</strong>. 🐾</p>
    </footer>
</body>
</html>
