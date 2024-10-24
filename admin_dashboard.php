<?php
session_start();
require 'db_connection.php'; // Include your database connection script

// Check if the user is logged in as an admin
if (!isset($_SESSION['username']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php'); // Redirect to login if not admin
    exit();
}

// Fetch pre-registrations for approval, filtering out those already approved
$stmt = $pdo->prepare("SELECT id, parent_surname, parent_first_name, parent_email FROM pre_registration WHERE is_approved = FALSE");
$stmt->execute();
$pre_registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <a href="registered_users.php"><i class="fas fa-users"></i> Users</a>
    <a href="#"><i class="fas fa-user-friends"></i> Parent Participation</a>
    <a href="create_announcement.php"><i class="fas fa-bullhorn"></i> Announcement</a>
    <a href="create_event.php"><i class="fas fa-calendar-alt"></i> Create Events</a>
    <a href="admin_dashboard.php"><i class="fas fa-file-alt"></i> View Request</a>
    <a href="client_profile.php"><i class="fas fa-user-circle"></i> Client Profile</a>
    <a href="index.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

    <div class="content">
    <h1>Dashboard</h1>
        <h2 class="mt-5">Pre-Registrations for Approval</h2>
        <table class="table mt-3">
            <thead>
                <tr>
                    <th>Surname</th>
                    <th>First Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($pre_registrations) > 0): ?>
                    <?php foreach ($pre_registrations as $registration): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($registration['parent_surname']); ?></td>
                            <td><?php echo htmlspecialchars($registration['parent_first_name']); ?></td>
                            <td><?php echo htmlspecialchars($registration['parent_email']); ?></td>
                            <td>
                                <!-- View Button -->
                                <a href="view_pre_registration.php?id=<?php echo htmlspecialchars($registration['id']); ?>" class="btn btn-info">View</a>

                                <!-- Approve Button -->
                                <form action="approve_pre_registration.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($registration['id']); ?>">
                                    <button type="submit" class="btn btn-success">Approve</button>
                                </form>

                                <!-- Reject Button -->
                                <form action="reject_pre_registration.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($registration['id']); ?>">
                                    <button type="submit" class="btn btn-danger">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No pre-registrations found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
