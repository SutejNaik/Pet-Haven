<?php 
session_start();
include 'includes/config.php'; ?> 

<!DOCTYPE html>
<html lang="en">
<head>
<link href="https://fonts.googleapis.com/css2?family=MuseoModerno&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
  <link rel="stylesheet" href="assets/css/abt.css" />
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet" />
</head>
<body>

<!-- Header -->
<header>
<div class="logo">🐾 Pet Haven</div>
    <ul class="nav-links">
        <li><a href="index.php">Home |</a></li>
        <li><a href="contact.php">Contact |</a></li>
        <li><a href="pets/pets.php">Adopt |</a></li>
        <li><a href="pets/submit_pet.php">Submit Pet |</a></li>
        <li>
        <?php if (isset($_SESSION['username'])): ?>
           <a href="users/profile.php" class="btn">Profile</a>
        <?php else: ?>
            <a href="users/login.php" class="btn">Login</a>
        <?php endif; ?>
        </li>
        </ul>
    </ul>
        </ul>
</header>
<div class="section section1 fade-in-on-scroll" data-aos="fade-right"> >
    <div class="text right">
        <h2>About Us & Our Aim</h2>
        <p>Pet Haven connects pet owners with people looking to adopt. Whether you're rehoming a pet or finding a new companion, we make the process simple and caring.</p>
    <p>If you have any questions, feel free to </p>
    <a href="contact.php" class="neon-btn">contact us</a>

    </div>
</div>

<div class="section section2 fade-in-on-scroll" data-aos="fade-right">>
    <div class="text left" >
        <h2>Submit Your Pet</h2>
        <p>Need to find a new home for your pet? Submit their details on our platform and let potential adopters reach out to you .</p>
        <a href="pets/submit_pet.php" class="neon-btn">Submit Now</a>
    </div>
</div>

<div class="section section3 fade-in-on-scroll" data-aos="fade-right">>
    <div class="text right">
        <h2>Adopt a Pet</h2>
        <p>Looking to adopt? Discover loving pets waiting for a home and connect with current owners to make adoption easy and meaningful.</p>
        <a href="pets/pets.php" class="neon-btn">View Pets</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
  <script src="assets/js/script.js"></script>
</body>

</body>
</html>
