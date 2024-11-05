<?php
// Start the session
session_start();

// Include database connection
include 'db_connection.php';

// Initialize variables
$request_date = '';
$request_type = '';
$description = '';
$success_message = ''; // Variable to hold success message
$error_message = ''; // Variable to hold error message

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $request_date = $_POST['request_date'];
    $request_type = $_POST['request_type'];
    $description = $_POST['description'];

    // Prepare and execute insertion into the database (requests_form table)
    try {
        $sql = "INSERT INTO requests_form (username, request_date, request_type, description) 
                VALUES (:username, :request_date, :request_type, :description)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'username' => $_SESSION['username'], // Assuming username is stored in session
            'request_date' => $request_date,
            'request_type' => $request_type,
            'description' => $description
        ]);
        
        // Set success message
        $success_message = "Your request has been submitted and is pending review.";
    } catch (PDOException $e) {
        $error_message = "Error submitting request: " . htmlspecialchars($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit a Request</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<?php include 'parent_sidebar.php'; ?> <!-- Include the sidebar here -->

<div class="container-fluid">
    <div class="content p-4">
        <h1 class="display-6 mb-4">Submit a Request</h1>

    <!-- Display success message if set -->
    <?php if ($success_message): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($success_message); ?>
        </div>
    <?php endif; ?>

    <!-- Display error message if set -->
    <?php if ($error_message): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="request_date">Request Date:</label>
            <input type="date" name="request_date" id="request_date" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="request_type">Request Type:</label>
            <select name="request_type" id="request_type" class="form-control" required>
                <option value="">-- Select Request Type --</option>
                <option value="Activity Report">Activity Report</option>
                <option value="Meeting With Teacher">Meeting With Teacher</option>
                <option value="Change of Schedule">Change of Schedule</option>
                <option value="Certification">Certification</option>
                <option value="Other">Other</option>
            </select>
        </div>

        <div class="form-group">
            <label for="description">Description:</label>
            <textarea name="description" id="description" class="form-control" rows="4" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Submit Request</button>
    </form>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.5.2.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>


