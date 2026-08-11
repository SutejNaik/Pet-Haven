<?php 
session_start();
include 'includes/config.php'; ?> 

<!DOCTYPE html>
<html lang="en">
<head>
  <link href="https://fonts.googleapis.com/css2?family=MuseoModerno&display=swap" rel="stylesheet">
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pet Haven - Home</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="assets/js/script.js"></script>



  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <style>
    html {
      scroll-behavior: smooth;
    }
    .btn:hover {
      box-shadow: 0 0 15px #00f0ff;
      transform: scale(1.05);
      transition: 0.3s ease;
    }
    .pet-card {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .pet-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 0 20px rgba(0, 240, 255, 0.3);
    }
  </style>
</head>
<body>

<!-- Header / Navbar -->
<header>
<div class="logo">🐾 Pet Haven</div>
    <ul class="nav-links">
            <li><a href="abt.php">About us |</a></li>
    <li><a href="pets/pets.php">Adopt |</a></li>
    <li><a href="pets/submit_pet.php">Submit Pet |</a></li>
    <li><a href="contact.php">Contact |</a></li>
    <li>
        <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])): ?>
            <a href="users/profile.php" class="btn">Profile</a>
        <?php else: ?>
            <a href="users/login.php" class="btn">Login</a>
        <?php endif; ?>
    </li>
</ul>
</ul>
</header>

<!-- Hero Section -->
<section class="hero" data-aos="fade-up">

    <h1>Find  Your  New  Best  Friend! </h1>
    <p>Adopt, don’t shop! Give a loving pet a new home.</p>
    <a href="pets/pets.php" class="btn">Browse Pets</a>
</section>

    <!-- How Adoption Works Section -->
<section class="how-adopt" data-aos="fade-up">

        <div class="content-box">
            <h2>🐾 How Adoption Works? </h2>
            <ul>
                <li><u><strong>Browse Pets:</strong></u> Look through the available pets and find the perfect match. </li>
                <li><u><strong>View Details:</strong></u> Click on a pet to see more information about the pet. </li>
                <li><u><strong>Adoption Process:</strong></u> Owner will directly contact you after you have submitted the adoption form  </li>
                <li><u><strong>Adopt & Update:</strong></u> Once adopted, the owner marks the pet as adopted, and the pet is removed from the listings.</li>
            </ul>
            <p>It's that simple! Give a pet a forever home today. ❤️</p>
        </div>
    </section>


<!-- Main Content -->
<div class="container" data-aos="fade-up">

    <h2>Why  Adopt  from  Pet  Haven? </h2>
    <p>Every pet deserves a loving home. Adopting a pet means saving a life 💖.</p>

<!-- Featured Pets Section -->
<div class="featured-pets" data-aos="fade-up">

    <h2>Meet Our Featured Pets! 🏡</h2>
    <div class="pet-container">
        <?php
        $query = "SELECT * FROM pets WHERE status='Available' ORDER BY RAND() LIMIT 3";
        $result = mysqli_query($conn, $query);

        while ($row = mysqli_fetch_assoc($result)) {
            $formattedDate = date("M j, Y", strtotime($row['date_posted']));

            echo '<div class="pet-card">
                    <img src="assets/images/' . $row['image'] . '" alt="Pet">
                    <h3>' . $row['name'] . '</h3>
                    <p><strong>' . $row['breed'] . ' | ' . $row['age'] . ' </strong></p>
                    <h4>Submitted on: <strong>' . $formattedDate . '</strong></h4>
                    <a href="pets/pet_details.php?id=' . $row['id'] . '" class="btn">View Details</a>
                  </div>';
        }
        ?>
    </div> 
</div> 
</div>

<!-- Place for adoption section -->
<div class="put-for-adoption-section" data-aos="fade-up">

    <div class="put-adoption-steps">
        <h2>Put Your Pet Up for Adoption</h2>
        <ul>
            <li>Step 1: Fill out the pet details</li>
            <li>Step 2: Upload pet images</li>
            <li>Step 3: Submit your pet for adoption</li>
            <li>Step 4: Wait for adoption requests</li>
        </ul>
        <a href="pets/submit_pet.php" class="put-adoption-btn">Submit Your Pet</a>
    </div>
</div>

<!-- Footer -->
<?php include 'includes/footer.php'; ?>

<!-- AOS Script -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 1000,
    once: true
  });
</script>
</body>
</html>
