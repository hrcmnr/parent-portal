<?php
// Include database connection
include 'db_connection.php'; // Ensure this is correct and the file exists

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the input values
    $title = $_POST['title'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $location = $_POST['location'];
    $type = $_POST['type'];
    $max_slots = $_POST['max_slots'];

    // Prepare the SQL statement
    $sql = "INSERT INTO events (title, description, date, time, location, type, max_slots) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    // Use $pdo to prepare the statement
    $stmt = $pdo->prepare($sql);

    if ($stmt === false) {
        echo "<script>alert('Error preparing statement: " . htmlspecialchars($pdo->errorInfo()[2]) . "');</script>";
    } else {
        // Bind parameters
        $stmt->bindParam(1, $title);
        $stmt->bindParam(2, $description);
        $stmt->bindParam(3, $date);
        $stmt->bindParam(4, $time);
        $stmt->bindParam(5, $location);
        $stmt->bindParam(6, $type);
        $stmt->bindParam(7, $max_slots, PDO::PARAM_INT); // Make sure to bind as integer

        // Execute the statement
        if ($stmt->execute()) {
            echo "<script>alert('Event created successfully!');</script>";
        } else {
            echo "<script>alert('Error creating event: " . htmlspecialchars($stmt->errorInfo()[2]) . "');</script>";
        }
        
        // Close the statement (optional with PDO, but a good practice)
        $stmt->closeCursor();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file if needed -->
</head>
<body>

<?php include 'admin_sidebar.php'; ?> 

<div class="content container mt-4">
    <div class="card shadow">
        <div class="card-header bg-white">
            <h3 class="mb-0 text-dark">Create Event</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="form-group">
                    <label for="title">Event Title:</label>
                    <input type="text" id="title" name="title" class="form-control" placeholder="Event Title" required>
                </div>

                <div class="form-group">
                    <label for="description">Event Description:</label>
                    <textarea id="description" name="description" class="form-control" placeholder="Event Description" required></textarea>
                </div>

                <div class="form-group">
                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="time">Time:</label>
                    <input type="time" id="time" name="time" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="location">Event Location:</label>
                    <input type="text" id="location" name="location" class="form-control" placeholder="Event Location" required>
                </div>

                <div class="form-group">
                    <label for="type">Event Type:</label>
                    <select id="type" name="type" class="form-control" required>
                        <option value="">Select Event Type</option>
                        <option value="Seminar">Seminar</option>
                        <option value="Meeting">Meeting</option>
                        <option value="Training">Training</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="max_slots">Number of Slots:</label>
                    <input type="number" id="max_slots" name="max_slots" class="form-control" placeholder="Number of Slots" required min="1">
                </div>

                <button type="submit" class="btn btn-primary btn-block">Create Event</button>
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
