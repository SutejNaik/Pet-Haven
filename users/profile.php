<?php
session_start();
include '../includes/config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../users/login.php");
    exit();
}

if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
    header("Location: ../admin/admin.php"); // Redirect admin to admin panel
    exit();
}



$user_id = $_SESSION['user_id'];

// Fetch user details
$stmt = $conn->prepare("SELECT username, email, contact, address FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

// Fetch pets submitted by the user
$pets_stmt = $conn->prepare("SELECT * FROM pets WHERE owner_id = ?");
$pets_stmt->bind_param("i", $user_id);
$pets_stmt->execute();
$pets_result = $pets_stmt->get_result();

// Fetch adoption history (pets adopted by the user)
$adoptions_stmt = $conn->prepare("SELECT p.* FROM adoptions a JOIN pets p ON a.pet_id = p.id WHERE a.adopter_id = ? AND a.status = 'Approved'");
$adoptions_stmt->bind_param("i", $user_id);
$adoptions_stmt->execute();
$adoptions_result = $adoptions_stmt->get_result();

// Fetch PENDING adoption requests received
$pending_requests_stmt = $conn->prepare("SELECT a.*, u.username, u.contact, u.address, p.name AS pet_name 
    FROM adoptions a 
    JOIN users u ON a.adopter_id = u.id 
    JOIN pets p ON a.pet_id = p.id 
    WHERE p.owner_id = ? AND a.status = 'Pending'");
$pending_requests_stmt->bind_param("i", $user_id);
$pending_requests_stmt->execute();
$pending_requests_result = $pending_requests_stmt->get_result();

// Fetch APPROVED adoption requests received
$approved_requests_stmt = $conn->prepare("SELECT a.*, u.username, u.contact, u.address, p.name AS pet_name 
    FROM adoptions a 
    JOIN users u ON a.adopter_id = u.id 
    JOIN pets p ON a.pet_id = p.id 
    WHERE p.owner_id = ? AND a.status = 'Approved'");
$approved_requests_stmt->bind_param("i", $user_id);
$approved_requests_stmt->execute();
$approved_requests_result = $approved_requests_stmt->get_result();

// Fetch adoption requests sent by the user
$requests_sent_stmt = $conn->prepare("SELECT a.*, p.name AS pet_name, p.image, u.username AS owner_name FROM adoptions a 
    JOIN pets p ON a.pet_id = p.id 
    JOIN users u ON p.owner_id = u.id 
    WHERE a.adopter_id = ?");
$requests_sent_stmt->bind_param("i", $user_id);
$requests_sent_stmt->execute();
$requests_sent_result = $requests_sent_stmt->get_result();

// Fetch user messages
$user_id = $_SESSION['user_id'];
$result = $conn->prepare("SELECT * FROM contact_messages WHERE id = ? ORDER BY created_at DESC");
$result->bind_param("i", $user_id);
$result->execute();
$messages = $result->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<link href="https://fonts.googleapis.com/css2?family=MuseoModerno&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <title>Profile - Pet Haven</title>
    <link rel="stylesheet" href="../assets/css/profile.css">
    <style>
        /* Container Styles */
        .section-container {
            margin: 20px 0;
            padding: 20px;
            border-radius: 8px;
            background: #f9f9f9;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .section-container h3 {
            margin-bottom: 20px;
            color: #333;
            font-size: 24px;
        }

        /* Pets Submitted for Adoption */
        .pet-listed {
            border-left: 5px solid black
        }

        .pet-req {
            border-left: 5px solid red;
        }

        /* Adoption Requests Received */
        .adoption-requests {
            border-left: 5px solid #FF9800;
        }

        /* Approved Adoption Requests */
        .approved-requests {
            border-left: 5px solid #2196F3;
        }

        /* My Adopted Pets */
        .adopted-pets {
            border-left: 5px solid #4CAF50;
        }

        /* Pet and Request Cards */
        .pet-card, .request-card {
            background: white;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .pet-card img, .request-card img {
            max-width: 100%;
            border-radius: 8px;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .modal-buttons {
            margin-top: 20px;
        }

        .modal-buttons button {
            margin: 0 10px;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .modal-buttons .btn {
            background: #4CAF50;
            color: white;
        }

        .modal-buttons .btn-delete {
            background: #f44336;
            color: white;
        }
    </style>
</head>
<body>

<header>
    <div class="logo">🐾 Pet Haven</div>
    <ul class="nav-links">
        <li><a href="../index.php">Home |</a></li>
                <li><a href="../abt.php">About Us |</a></li>
        <li><a href="../pets/pets.php">Adopt |</a></li>
        <li><a href="../pets/submit_pet.php">Submit Pet |</a></li>
        <li><a href="../contact.php">Contact |</a></li>
        <li><a href="logout.php" class="btn">Logout </a></li>
    </ul>
</header>

<div class="container">
    <h2>My Profile</h2>
    <div class="profile-info-wrapper">
        <div class="profile-info">
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Contact:</strong> <?php echo htmlspecialchars($user['contact']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($user['address']); ?></p>
            <a href="edit_profile.php" class="btn">Edit Profile</a>
        </div>

        <div class="avatar-circle">
            <?php echo strtoupper($user['username'][0]); ?>
        </div>
    </div>
</div>

<style>
.container {
    max-width: 80%;
    margin: 40px auto;
    padding: 30px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 15px;
    box-shadow: 0 0 20px rgba(0, 240, 255, 0.3);
    border: 2px solid black;
    backdrop-filter: blur(8px);
}

/* Heading */
h2 {
    color:black;
    text-shadow: 0 0 5px rgba(0, 240, 255, 0.6);
    margin-bottom: 20px;
}

/* Wrapper to place avatar and info side by side */
.profile-info-wrapper {
    display: flex;
    align-items: center;
    gap: 40px; /* space between info and avatar */
}

/* Profile info styling */
.profile-info {
    background: rgba(0, 240, 255, 0.05);
    padding: 20px;
    border-radius: 15px;
    border: 2px solid #00f0ff;
    box-shadow: 0 0 10px rgba(0, 240, 255, 0.2);
    color: #fff;
    flex: 1;  /* take remaining space */
}

.profile-info p {
    margin: 10px 0;
    color: black; /* you can adjust color */
}

.profile-info .btn {
    display: inline-block;
    background: #00f0ff;
    color: black;
    padding: 10px 15px;
    border-radius: 10px;
    font-weight: bold;
    border: 2px solid black;
    text-decoration: none;
    box-shadow: 0 0 10px rgba(0, 240, 255, 0.4);
    animation: float 3s ease-in-out infinite;
    transition: 0.3s;
}

.profile-info .btn:hover {
    background: #39FF14;
    color: #111;
    box-shadow: 0px 0px 15px rgba(57, 255, 20, 0.8);
}

/* Avatar circle */
.avatar-circle {
    width: 100px;
    height: 100px;
    background-color: #00f0ff;
    color: #111;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    font-weight: bold;
    box-shadow: 0 0 15px #00f0ff;
    flex-shrink: 0;
    user-select: none;
    /* Optional margin if needed */
    /* margin-left: 20px; */
}
</style>


<!-- Pets Listed by Me -->
<div class="section-container pet-listed">
    <h3>Pets I Listed for Adoption</h3>
    <div class="pets-list">
        <?php

        $user_id = $_SESSION['user_id'];
        $listed_pets_query = "SELECT * FROM pets WHERE owner_id = $user_id ORDER BY date_posted DESC";
        $listed_pets_result = mysqli_query($conn, $listed_pets_query);

        if ($listed_pets_result && mysqli_num_rows($listed_pets_result) > 0): 
            while ($pet = mysqli_fetch_assoc($listed_pets_result)): ?>
                <div class="pet-card">
                    <img src="../assets/images/<?php echo htmlspecialchars($pet['image']); ?>" alt="Pet">
                    <h4><?php echo htmlspecialchars($pet['name']); ?></h4>
                    <p>Type: <strong><?php echo htmlspecialchars($pet['type']); ?></strong></p>
                    <p>Breed: <strong><?php echo htmlspecialchars($pet['breed']); ?></strong></p>
                    <p>Status: 
                        <?php
                            $status = $pet['status'];
                            if ($status == 'Adopted') {
                                echo "<strong style='color:green;'>Adopted</strong>";
                            } elseif ($status == 'Pending') {
                                echo "<strong style='color:orange;'>Pending</strong>";
                            } else {
                                echo "<strong style='color:cyan;'>Available</strong>";
                            }
                        ?>
                    </p>
<div class="actions">
    <a href="edit_pet.php?id=<?php echo $pet['id']; ?>" class="btn">Edit</a>



    <a href="delete_pet.php?id=<?php echo $pet['id']; ?>" class="btn danger">Delete</a>
</div>

                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>You haven’t listed any pets yet.</p>
        <?php endif; ?>
    </div>
</div>


    <!-- Request sent Pet for Adoption -->
<div class="section-container pet-req">
    <h3>Requests Sent for Adoption</h3>
    <div class="pets-list">
        <?php if ($requests_sent_result->num_rows > 0): ?>
            <?php while ($request = $requests_sent_result->fetch_assoc()): ?>
                <div class="pet-card">
                    <img src="../assets/images/<?php echo htmlspecialchars($request['image']); ?>" alt="Pet">
                    <h4><?php echo htmlspecialchars($request['pet_name']); ?></h4>
                    <p>Owner: <strong><?php echo htmlspecialchars($request['owner_name']); ?></strong></p>
                    <p>Status: 
                        <?php
                            $status = $request['status'];
                            if ($status == 'Rejected') {
                                echo "<strong style='color:red;'>Rejected</strong>";
                            } elseif ($status == 'Approved') {
                                echo "<strong style='color:green;'>Approved</strong>";
                            } else {
                                echo "<strong style='color:orange;'>Pending</strong>";
                            }
                        ?>
                    </p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No adoption requests sent yet.</p>
        <?php endif; ?>
    </div>
</div>

    <!-- Approved Adoptions -->
    <div class="section-container adopted-pets">
        <h3>Pets Adopted</h3>
        <div class="pets-list">
            <?php if ($adoptions_result->num_rows > 0): ?>
                <?php while ($adopted = $adoptions_result->fetch_assoc()): ?>
                    <div class="pet-card">
                        <img src="../assets/images/<?php echo htmlspecialchars($adopted['image']); ?>" alt="Pet">
                        <h4><?php echo htmlspecialchars($adopted['name']); ?></h4>
                        <p><?php echo htmlspecialchars($adopted['breed']); ?> | <?php echo htmlspecialchars($adopted['age']); ?> years old</p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No pets adopted yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Pending Adoption Requests -->
    <div class="section-container adoption-requests">
        <h3>Pending Adoption Requests for your Pet</h3>
        <div class="requests-list">
            <?php if ($pending_requests_result->num_rows > 0): ?>
                <?php while ($request = $pending_requests_result->fetch_assoc()): ?>
                    <div class="request-card">
                        <p><strong>Pet:</strong> <?php echo htmlspecialchars($request['pet_name']); ?></p>
                        <p><strong>Applicant:</strong> <?php echo htmlspecialchars($request['username']); ?></p>
                        <p><strong>Contact:</strong> <?php echo htmlspecialchars($request['contact']); ?></p>
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($request['address']); ?></p>
                        <p><strong>Message:</strong> <?php echo htmlspecialchars($request['message']); ?></p>

                        <!-- Approve/Reject Forms -->
                        <form action="../adoption/approve_adoption.php" method="POST" class="approve-form">
                            <input type="hidden" name="adoption_id" value="<?php echo $request['id']; ?>">
                            <button type="submit" class="btn">Approve</button>
                        </form>
                        <form action="../adoption/delete_adoption.php" method="POST" class="reject-form">
                            <input type="hidden" name="adoption_id" value="<?php echo $request['id']; ?>">
                            <button type="submit" class="btn btn-delete">Reject</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No pending requests found.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Approved Adoption Requests -->
    <div class="section-container approved-requests">
        <h3>Approved Requests to Adopt your Pet</h3>
        <div class="requests-list">
            <?php if ($approved_requests_result->num_rows > 0): ?>
                <?php while ($request = $approved_requests_result->fetch_assoc()): ?>
                    <div class="request-card">
                        <p><strong>Pet:</strong> <?php echo htmlspecialchars($request['pet_name']); ?></p>
                        <p><strong>Adopter:</strong> <?php echo htmlspecialchars($request['username']); ?></p>
                        <p><strong>Contact:</strong> <?php echo htmlspecialchars($request['contact']); ?></p>
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($request['address']); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No approved requests yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmation-modal" class="modal">
    <div class="modal-content">
        <p id="modal-message"></p>
        <div class="modal-buttons">
            <button id="confirm-action" class="btn">Confirm</button>
            <button id="cancel-action" class="btn btn-delete">Cancel</button>
        </div>
    </div>
</div>

<script>
    // Get modal elements
    const modal = document.getElementById('confirmation-modal');
    const modalMessage = document.getElementById('modal-message');
    const confirmActionBtn = document.getElementById('confirm-action');
    const cancelActionBtn = document.getElementById('cancel-action');

    // Function to show the modal
    function showConfirmationModal(message, form) {
        modalMessage.textContent = message;
        modal.style.display = 'flex';

        // Confirm action
        confirmActionBtn.onclick = () => {
            form.submit();
            modal.style.display = 'none';
        };

        // Cancel action
        cancelActionBtn.onclick = () => {
            modal.style.display = 'none';
        };
    }

    // Attach event listeners to all approve/reject forms
    document.querySelectorAll('.approve-form, .reject-form').forEach(form => {
        form.onsubmit = (e) => {
            e.preventDefault(); // Prevent form submission
            const action = form.classList.contains('approve-form') ? 'approve' : 'reject';
            const message = `Are you sure you want to ${action} this adoption request?`;
            showConfirmationModal(message, form);
        };
    });
</script>



<?php include '../includes/footer.php'; ?>
</body>
</html>