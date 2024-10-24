<?php
// Start the session if it hasn't been started yet
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ensure the session has been initialized before accessing username
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : ''; // Set to empty if not set
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Dashboard</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles_admin.css"> <!-- Link to the external CSS file -->
</head>
<body>
<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar bg-light p-4 d-flex flex-column" style="width: 250px; height: 100vh;">
        <!-- Logo -->
        <img src="../img/csnlogo-removebg.png" alt="Logo" class="img-fluid mb-4" style="max-width: 100%; height: auto;">
        
        <!-- Safely display username or leave it empty -->
        <h2 class="text-center"><?php echo $username; ?></h2>
        
        <div class="flex-grow-1"> <!-- This wrapper will take up available space -->
            <a href="parent_dashboard.php" class="d-block py-2"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="enroll.php" class="d-block py-2"><i class="fas fa-user-plus"></i> Enroll</a>
            <a href="history.php" class="d-block py-2"><i class="fas fa-history"></i> History</a>
            <a href="generate_request.php" class="d-block py-2"><i class="fas fa-paper-plane"></i> Request Form</a>
            <a href="profile_edit_page1.php" class="d-block py-2"><i class="fas fa-user-circle"></i> Parent Profile</a>
        </div>

        <a href="index.php" class="d-block py-2"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
