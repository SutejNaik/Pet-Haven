<?php
session_start();
include '../includes/config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../users/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $breed = trim($_POST['breed']);
    $gender = trim($_POST['gender']);
    $age = mysqli_real_escape_string($conn, trim($_POST['age']));
    $description = trim($_POST['description']);
    $contact = trim($_POST['contact']);
    $address = trim($_POST['address']);
    $type = trim($_POST['type']);
    $status = 'Available';

    // Handle Image Upload
    $image = $_FILES['image']['name'];
    $target_dir = "../assets/images/";
    $target_file = $target_dir . basename($image);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        // Insert into Database
        $stmt = $conn->prepare("INSERT INTO pets (owner_id, name, type, breed, gender, age, description, image, contact, address, status, date_posted) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("issssssssss", $user_id, $name, $type, $breed, $gender, $age, $description, $image, $contact, $address, $status);

        if ($stmt->execute()) {
            $success = "Your pet has been submitted for adoption successfully! ❤️";
        } else {
            $error = "There was an error submitting the pet. Please try again.";
        }
        $stmt->close();
    } else {
        $error = "Failed to upload the image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/css2?family=MuseoModerno&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <title>Submit Pet for Adoption</title>
    <link rel="stylesheet" href="../assets/css/submit.css">
    <style>
        .notification {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            background-color: #4CAF50;
            color: white;
            padding: 30px 40px;
            border-radius: 10px;
            width: auto;
            max-width: 80%;
            margin: 0 auto;
            font-size: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            z-index: 1000;
            animation: slideIn 0.5s;
        }
        
        .error-notification {
            background-color: #F44336;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translate(-50%, -60%); }
            to { opacity: 1; transform: translate(-50%, -50%); }
        }
        
        .success-btn {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 25px;
            background-color: white;
            border: 2px solid white;
            color: #4CAF50;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
            transition: all 0.3s;
            font-size: 16px;
        }
        
        .success-btn:hover {
            background-color: transparent;
            color: white;
        }
        
        .error-btn {
            color: #F44336;
        }
        
        .error-btn:hover {
            color: white;
            background-color: transparent;
        }
        
        .notification-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 999;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">🐾 Pet Haven</div>
        <ul class="nav-links">
            <li><a href="../index.php">Home |</a></li>
            <li><a href="/pet_haven_/abt.php">About Us |</a></li>
            <li><a href="pets.php">Adopt |</a></li>
            <li><a href="../contact.php">Contact |</a></li>
            <li>
                <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])): ?>
                    <a href="/pet_haven_/users/profile.php" class="btn">Profile</a>
                <?php else: ?>
                    <a href="/pet_haven_/users/login.php" class="btn">Login</a>
                <?php endif; ?>
            </li>
        </ul>
    </header>

    <div class="container">
        <h2>Submit Pet for Adoption</h2>
        
        <form action="submit_pet.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Pet Name:</label>
                <input type="text" id="name" name="name" placeholder="Enter your pet's name" required>
            </div>
            <div class="form-group">
                <label for="type">Pet Type:</label>
                <input type="text" id="type" name="type" placeholder="Enter your pet's type (e.g., dog/cat)" required>
            </div>
            <div class="form-group">
                <label for="breed">Breed:</label>
                <input type="text" id="breed" name="breed" placeholder="Enter your pet's breed" required>
            </div>
            <div class="form-group">
                <label for="gender">Gender:</label>
                <select name="gender" id="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            <div class="form-group">
                <label for="age">Age:</label>
                <input type="text" id="age" name="age" placeholder="Enter pet's age - no. (month or year)" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" placeholder="Describe your pet in a small paragraph" required></textarea>
            </div>
            <div class="form-group">
                <label for="contact">Contact:</label>
                <input type="text" id="contact" name="contact" placeholder="Enter your contact details" required>
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" placeholder="Enter your address" required>
            </div>
            <div class="form-group">
                <label for="image">Image:</label>
                <input type="file" id="pet_image" name="image" accept="image/*" required>
                <img id="preview" alt="Image Preview" style="display:none; max-width: 200px; margin-top: 10px; border: 1px solid #00f0ff; border-radius: 10px;" />
            </div>

            <button type="submit" class="submit-btn">Submit Pet</button>
        </form>
    </div>

    <?php if ($success || $error): ?>
        <div class="notification-overlay"></div>
        <?php if ($success): ?>
            <div class="notification">
                <?php echo $success; ?>
                <div>
                    <a href="pets.php" class="success-btn">View Pets</a>
                    <a href="submit_pet.php" class="success-btn">Add Another</a>
                </div>
            </div>
        <?php elseif ($error): ?>
            <div class="notification error-notification">
                <?php echo $error; ?>
                <div>
                    <a href="javascript:window.location.reload()" class="success-btn error-btn">Try Again</a>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/script.js"></script>
    <script>
        document.getElementById('pet_image').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('preview');

            if (file && file.type.startsWith('image/')) {
                preview.src = URL.createObjectURL(file);
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
                preview.src = '';
            }
        });
    </script>
</body>
</html>