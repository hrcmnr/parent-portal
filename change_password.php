<?php
session_start();
require 'db_connection.php'; // Include your database connection script

// Check if the user is logged in and needs to change their password
if (!isset($_SESSION['email'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newPassword = $_POST['new_password'];
    $email = $_SESSION['email'];

    // Hash the new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update the password and set needs_password_change to false
    $stmt = $pdo->prepare("UPDATE users SET password = :password, needs_password_change = FALSE WHERE email = :email");
    
    if ($stmt->execute(['password' => $hashedPassword, 'email' => $email])) {
        // Password changed successfully
        // Optionally, you could log the user in again or just redirect to login
        header('Location: index.php'); // Redirect to login page
        exit();
    } else {
        // Handle error if the password was not updated
        echo "Error updating password. Please try again.";
    }
}
?>
<!-- HTML form for password change -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file if any -->
</head>
<body>
    <h2>Change Password</h2>
    <form action="" method="POST">
        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" required>
        <button type="submit">Change Password</button>
    </form>
</body>
</html>
