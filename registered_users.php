<?php
// Include your database connection file
include 'db_connection.php';

// Initialize users variable
$users = [];

// Fetch users from the database
$query = "SELECT id, username, email FROM users";
$stmt = $pdo->query($query);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Update the username if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $new_username = $_POST['username'];

    // Check if the new username already exists
    $checkQuery = "SELECT COUNT(*) FROM users WHERE username = :username AND id != :id";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->execute(['username' => $new_username, 'id' => $user_id]);
    $count = $checkStmt->fetchColumn();

    if ($count == 0) {
        // Update the username in the database
        $updateQuery = "UPDATE users SET username = :username WHERE id = :id";
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->execute(['username' => $new_username, 'id' => $user_id]);
        echo "<div class='alert alert-success'>Username updated successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>This username is already taken.</div>";
    }

    // Refresh the list of users after the update
    $stmt = $pdo->query($query);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Users</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file if needed -->
</head>
<body>
<?php include 'admin_sidebar.php'; ?> <!-- Include the sidebar here -->

<div class="content container mt-4">
    <h1 class="mb-4">Registered Users</h1>
    <table class="table table-bordered table-striped">
        <thead class="thead-light">
            <tr>
                <th>User ID</th>
                <th>CSN ID</th>
                <th>Parent Email</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <form action="registered_users.php" method="POST">
                        <td><?= htmlspecialchars($user['id']); ?></td>
                        <td>
                            <input type="text" name="username" value="<?= htmlspecialchars($user['username']); ?>" class="form-control" required>
                            <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['id']); ?>">
                        </td>
                        <td><?= htmlspecialchars($user['email']); ?></td>
                        <td>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </td>
                    </form>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Bootstrap JS and dependencies (optional) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
