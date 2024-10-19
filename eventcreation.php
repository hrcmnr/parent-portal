<?php
// Database connection
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'csn_parent_portal';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

// Check if the user is logged in and is an active admin
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin' || $_SESSION['status'] !== 'active') {
    echo "<script>alert('Only active admins can create events.');</script>";
    exit;
}

// Handle event creation form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $location = $_POST['location'];
    $type = $_POST['type'];
    $max_slots = $_POST['max_slots'];
    $admin_id = $_SESSION['id']; // Admin's ID from session

    // Insert the new event into the database
    $sql = "INSERT INTO events (title, description, date, time, location, type, max_slots, created_by) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssssii', $title, $description, $date, $time, $location, $type, $max_slots, $admin_id);

    if ($stmt->execute()) {
        echo "<script>alert('Event created successfully!');</script>";
    } else {
        echo "<script>alert('Error creating event.');</script>";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Event</title>
</head>
<body>
    <h1>Create Event</h1>
    <form method="POST" action="">
        <input type="text" name="title" placeholder="Event Title" required><br>
        <textarea name="description" placeholder="Event Description" required></textarea><br>
        <input type="date" name="date" required><br>
        <input type="time" name="time" required><br>
        <input type="text" name="location" placeholder="Event Location" required><br>
        <input type="text" name="type" placeholder="Event Type (e.g., Seminar, Meeting)" required><br>
        <input type="number" name="max_slots" placeholder="Number of Slots" required min="1"><br>
        <button type="submit">Create Event</button>
    </form>

    <a href="admin_dashboard.php">Back to Dashboard</a>
</body>
</html>

