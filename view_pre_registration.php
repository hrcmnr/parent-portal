<?php
session_start();
require 'db_connection.php'; // Include your database connection script

// Check if the user is logged in as an admin
if (!isset($_SESSION['username']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php'); // Redirect to login if not admin
    exit();
}

// Fetch the specific pre-registration details based on the provided ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Fetch the pre-registration details from the table
    $stmt = $pdo->prepare("SELECT * FROM pre_registration WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $registration = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$registration) {
        echo "No pre-registration details found.";
        exit();
    }
} else {
    echo "No pre-registration ID provided.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Pre-Registration</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Pre-Registration Details</h2>
        <table class="table table-bordered mt-3">
            <tbody>
                <tr>
                    <th>Parent Surname</th>
                    <td><?php echo htmlspecialchars($registration['parent_surname']); ?></td>
                </tr>
                <tr>
                    <th>Parent First Name</th>
                    <td><?php echo htmlspecialchars($registration['parent_first_name']); ?></td>
                </tr>
                <tr>
                    <th>Parent Middle Name</th>
                    <td><?php echo htmlspecialchars($registration['parent_middle_name']); ?></td>
                </tr>
                <tr>
                    <th>Role</th>
                    <td><?php echo htmlspecialchars($registration['role']); ?></td>
                </tr>
                <tr>
                    <th>Parent Email</th>
                    <td><?php echo htmlspecialchars($registration['parent_email']); ?></td>
                </tr>
                <tr>
                    <th>Parent Picture</th>
                    <td>
                        <?php 
                        // Check if parent picture exists
                        if (!empty($registration['parent_picture']) && file_exists('uploads/' . $registration['parent_picture'])) {
                            echo '<img src="uploads/' . htmlspecialchars($registration['parent_picture']) . '" alt="Parent Picture" style="max-width: 150px;">';
                        } else {
                            echo 'No picture available';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Child Surname</th>
                    <td><?php echo htmlspecialchars($registration['child_surname']); ?></td>
                </tr>
                <tr>
                    <th>Child First Name</th>
                    <td><?php echo htmlspecialchars($registration['child_first_name']); ?></td>
                </tr>
                <tr>
                    <th>Child Middle Name</th>
                    <td><?php echo htmlspecialchars($registration['child_middle_name']); ?></td>
                </tr>
                <tr>
                    <th>Child Diagnosis</th>
                    <td><?php echo htmlspecialchars($registration['child_diagnosis']); ?></td>
                </tr>
                <tr>
                    <th>Child Picture</th>
                    <td>
                        <?php 
                        // Check if child picture exists
                        if (!empty($registration['child_picture']) && file_exists('uploads/' . $registration['child_picture'])) {
                            echo '<img src="uploads/' . htmlspecialchars($registration['child_picture']) . '" alt="Child Picture" style="max-width: 150px;">';
                        } else {
                            echo 'No picture available';
                        }
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <a href="admin_dashboard.php" class="btn btn-primary">Back to Dashboard</a>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
