<?php
session_start();
include '../includes/config.php';

$pet_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conn->prepare("SELECT pets.*, users.contact AS owner_contact, users.address AS owner_address, users.id AS owner_id 
                        FROM pets 
                        JOIN users ON pets.owner_id = users.id 
                        WHERE pets.id = ?");
$stmt->bind_param("i", $pet_id);
$stmt->execute();
$pet = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$pet) {
    echo "<p class='error'>Pet not found! <a href='../pets.php'>Go back to available pets</a></p>";
    exit;
}

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;
$owner_id = intval($pet['owner_id']);
$is_owner = ($user_id === $owner_id); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/css2?family=MuseoModerno&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <title>Meet <?php echo htmlspecialchars($pet['name']); ?> - Pet Haven</title>
    <link rel="stylesheet" href="../assets/css/pets.css">
</head>
<body>

<header>
    <div class="logo">🐾 Pet Haven</div>
    <ul class="nav-links">
        <li><a href="../index.php">Home |</a></li>
        <li><a href="../abt.php">About Us |</a></li>
        <li><a href="submit_pet.php">Submit Pet |</a></li>
        <li><a href="../contact.php">Contact |</a></li>
        <li>
            <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])): ?>
                <a href="../users/profile.php" class="btn">Profile</a>
            <?php else: ?>
                <a href="../users/login.php" class="btn">Login</a>
            <?php endif; ?>
        </li>
    </ul>
</header>

<div class="container">
    <h2>🐶 Meet <?php echo htmlspecialchars($pet['name']); ?>! 🐾</h2>
    <p class="intro-text">
        This adorable <strong><?php echo htmlspecialchars($pet['breed']); ?></strong> is looking for a forever home!
        Could you be the one to give <?php echo htmlspecialchars($pet['name']); ?> all the love and care they deserve?
    </p>

    <div class="pet-details">
        <img src="../assets/images/<?php echo htmlspecialchars($pet['image']); ?>" alt="Picture of <?php echo htmlspecialchars($pet['name']); ?>">
        <div class="pet-info">
            <p><strong>🐾 Breed:</strong> <?php echo htmlspecialchars($pet['breed']); ?></p>
            <p><strong>🎂 Age:</strong> <?php echo htmlspecialchars($pet['age']); ?></p>
            <p><strong>Gender:</strong> <?php echo htmlspecialchars($pet['gender']); ?></p>
            <p><strong>📝 About <?php echo htmlspecialchars($pet['name']); ?>:</strong> <?php echo htmlspecialchars($pet['description']); ?></p>
            <p><strong>📍 Location:</strong> <?php echo htmlspecialchars($pet['address']); ?></p>
            <p><strong>📌 Status:</strong> <?php echo htmlspecialchars($pet['status']); ?></p>

            <?php if ($is_owner): ?>
                <p class="owner-message">⚠️ You are the owner of <?php echo htmlspecialchars($pet['name']); ?>, so you cannot adopt them.</p>
            <?php elseif ($pet['status'] == 'Available'): ?>
                <p class="adoption-note">💖 <?php echo htmlspecialchars($pet['name']); ?> is waiting for a loving home! Click the button below to start the adoption process.</p>
                <a href="adopt_pet.php?pet_id=<?php echo $pet['id']; ?>" class="abtn">Adopt <?php echo htmlspecialchars($pet['name']); ?> 🏡</a>
            <?php else: ?>
                <p class="adopted-message">🎉 Great news! <?php echo htmlspecialchars($pet['name']); ?> has already found a loving home! 🏡</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>

