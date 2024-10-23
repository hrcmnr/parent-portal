<?php
// Include database connection
include 'db_connection.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form inputs
    $title = $_POST['title'];
    $body = $_POST['body'];
    $date = $_POST['date'];

    // Insert the announcement into the database
    $sql = "INSERT INTO announcements (title, body, date) VALUES (:title, :body, :date)";
    $stmt = $pdo->prepare($sql); // Use $pdo instead of $conn
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':body', $body);
    $stmt->bindParam(':date', $date);

    if ($stmt->execute()) {
        echo "Announcement created successfully!";
    } else {
        echo "Error: " . $stmt->errorInfo()[2]; // Display specific error information
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Announcement</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file if needed -->
</head>
<body>

<?php include 'admin_sidebar.php'; ?> <!-- Include the sidebar here -->

<div class="content container mt-4">
    <div class="card shadow">
        <div class="card-header bg-white">
            <h3 class="mb-0 text-dark">Create Announcement</h3>
        </div>
        <div class="card-body">
            <form action="create_announcement.php" method="POST">
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="body">Announcement Body:</label>
                    <textarea id="body" name="body" rows="5" class="form-control" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Submit</button>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies (optional) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>


