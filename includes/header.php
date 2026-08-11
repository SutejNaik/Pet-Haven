<header>
    <div class="logo">🐾 Pet Haven</div>
    <ul class="nav-links">
        <li><a href="../index.php">Home |</a></li>
        <li><a href="../pets.php">Adopt |</a></li>
        <li><a href="../submit_pet.php">Submit Pet |</a></li>
        <li><a href="../contact.php">Contact |</a></li>
        <?php if (isset($_SESSION['username'])): ?>
            <li><a href="../users/profile.php" class="btn">Profile</a></li>
        <?php else: ?>
            <li><a href="../users/login.php" class="btn">Login</a></li>
        <?php endif; ?>
    </ul>
</header>
