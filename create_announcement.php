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
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">
    <h1>Create Announcement</h1>
    <form action="create_announcement.php" method="POST">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required><br><br>

        <label for="date">Date:</label>
        <input type="date" id="date" name="date" required><br><br>

        <label for="body">Announcement Body:</label>
        <textarea id="body" name="body" rows="5" required></textarea><br><br>

        <button type="submit">Create Announcement</button>
    </form>
</div>

</body>
</html>
