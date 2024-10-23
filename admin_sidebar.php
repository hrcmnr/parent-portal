<?php
session_start(); // Always start the session at the beginning
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles_admin.css"> <!-- Link to the external CSS file -->
</head>
<body>
<div class="sidebar">
    <h2><?php echo htmlspecialchars($_SESSION['username']); ?></h2>
    <a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="registered_users.php"><i class="fas fa-users"></i> Registered Users</a>
    <a href="#"><i class="fas fa-user-friends"></i> Parent Participation</a>
    <a href="create_announcement.php"><i class="fas fa-bullhorn"></i> Announcement</a>
    <a href="create_event.php"><i class="fas fa-calendar-alt"></i> Create Events</a>
    <a href="generate_request.php"><i class="fas fa-file-alt"></i> Generate Request</a>
    <a href="index.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>
</body>
</html>
