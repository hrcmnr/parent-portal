<?php
session_start();
require 'db_connection.php'; // Include your database connection script

// Check if the user is logged in and is a parent
if (!isset($_SESSION['email']) || $_SESSION['user_role'] !== 'parent') {
    header('Location: login.php'); // Redirect to login if not logged in or not a parent
    exit();
}

// Fetch the parent's information from the database
$email = $_SESSION['email'];

// Fetch child's information based on the parent's email
$stmt = $pdo->prepare("SELECT child_surname, child_first_name, child_diagnosis FROM pre_registration WHERE parent_email = :email");
$stmt->execute(['email' => $email]);
$children = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file if any -->
</head>
<body>
    <div class="sidebar">
        <h2><?php echo htmlspecialchars($_SESSION['username']); ?></h2>
        <a href="parent_dashboard.php">Dashboard</a>
        <a href="view_activities.php">View Activities</a>
        <a href="index.php">Logout</a>
    </div>

    <div class="content">
        <h2 class="mt-5">Welcome to Your Dashboard</h2>

        <h4>Your Child's Information:</h4>
        <table class="table mt-3">
            <thead>
                <tr>
                    <th>Child's Surname</th>
                    <th>Child's First Name</th>
                    <th>Diagnosis</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($children) > 0): ?>
                    <?php foreach ($children as $child): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($child['child_surname']); ?></td>
                            <td><?php echo htmlspecialchars($child['child_first_name']); ?></td>
                            <td><?php echo htmlspecialchars($child['child_diagnosis']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">No child information found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <h4>Additional Resources:</h4>
        <p>You can find helpful resources and links here to assist you with your child's development.</p>
        <!-- Add more resources or links as needed -->
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
