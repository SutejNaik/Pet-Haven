<?php
session_start();
include '../includes/config.php';

// Fetch all available pets
$pets_result = $conn->query("SELECT * FROM pets WHERE status = 'Available'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/css2?family=MuseoModerno&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <title>Available Pets</title>
    <link rel="stylesheet" href="../assets/css/pets.css">

    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        html {
            scroll-behavior: smooth;
        }

         .pet-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
        }

        .pet-card:hover {
            transform: scale(1.05);
            box-shadow: 0 0 15px #00f0ff, 0 0 5px #39FF14;
        }

        .pet-card img {
            transition: transform 0.3s ease;
        }

        .pet-card:hover img {
            transform: scale(1.05);
        }

        
        .pet-card:hover {
            border: 2px solid #00f0ff;
        }

    </style>
</head>
<body>

<!-- Header / Navbar -->
<header>
    <div class="logo">🐾 Pet Haven</div>
    <ul class="nav-links">
        <li><a href="/pet_haven_/index.php">Home |</a></li>
                <li><a href="/pet_haven_/abt.php">About Us |</a></li>
        <li><a href="/pet_haven_/pets/submit_pet.php">Submit Pet |</a></li>
        <li><a href="/pet_haven_/contact.php">Contact |</a></li>
        <li>
            <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])): ?>
                <a href="/pet_haven_/users/profile.php" class="btn">Profile</a>
            <?php else: ?>
                <a href="/pet_haven_/users/login.php" class="btn">Login</a>
            <?php endif; ?>
        </li>
    </ul>
</header>

<!-- Title Section -->
<div class="title-container" data-aos="fade-down">
    <h2>🐾 Available Pets for Adoption 🐾</h2>
    <p>Find a loving companion and give them a forever home! ❤️</p>
</div>

<!-- Featured Pets Section -->
<div class="featured-pets" data-aos="fade-up">
    <div class="pet-container">
        <?php while ($pet = $pets_result->fetch_assoc()): ?>
            <div class="pet-card" data-aos="zoom-in">
                <img src="../assets/images/<?php echo htmlspecialchars($pet['image']); ?>" alt="Pet Image">
                <h4><?php echo htmlspecialchars($pet['name']); ?></h4>
                <p><?php echo htmlspecialchars($pet['breed']); ?> | <?php echo htmlspecialchars($pet['age']); ?></p>
                <p><?php echo htmlspecialchars($pet['gender']); ?></p>
                <p><strong>Submitted on:</strong> <?php echo date("F j, Y", strtotime($pet['date_posted'])); ?></p>
                <a href="pet_details.php?id=<?php echo $pet['id']; ?>" class="btn">View Details</a>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<!-- AOS JS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 1000,
    once: true
  });
</script>

</body>
</html>