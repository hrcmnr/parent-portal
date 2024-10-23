<?php
require 'db_connection.php'; // Include your database connection script

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date = $_POST['date'];
    $username = $_POST['username']; // Get username from form
    $title = $_POST['title'];
    $body = $_POST['body'] ?? null; // Initialize body variable
    $file_path = null; // Initialize file path variable

    // Handle file upload (only if a file is provided)
    if (isset($_FILES["fileToUpload"]) && $_FILES["fileToUpload"]["error"] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/"; // Ensure this directory exists and is writable
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check file size (limit to 5MB)
        if ($_FILES["fileToUpload"]["size"] > 5000000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats (e.g., jpg, png, pdf)
        if ($fileType != "jpg" && $fileType != "png" && $fileType != "pdf") {
            echo "Sorry, only JPG, PNG & PDF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } else {
            // If everything is ok, try to upload the file
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                $file_path = $target_file; // Store file path for database insertion
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }

    // Prepare the SQL statement for insertion, using username instead of email
    $sql = "INSERT INTO requests (date, username, title, body" . ($file_path ? ", file_path" : "") . ") VALUES (:date, :username, :title, :body" . ($file_path ? ", :file_path" : "") . ")";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':date', $date);
    $stmt->bindParam(':username', $username); // Bind the username
    $stmt->bindParam(':title', $title);

    // If body is empty, set it to NULL
    if (empty($body)) {
        $stmt->bindValue(':body', null, PDO::PARAM_NULL);
    } else {
        $stmt->bindParam(':body', $body);
    }

    // Only bind file_path if it's set
    if ($file_path) {
        $stmt->bindParam(':file_path', $file_path);
    }

    // Execute the statement
    if ($stmt->execute()) {
        echo "Request created successfully!";
    } else {
        echo "Error: " . $stmt->errorInfo()[2];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Request</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css"> <!-- Your custom CSS if needed -->
</head>
<body>

<?php include 'admin_sidebar.php'; ?> 

<div class="content container mt-4">
    <div class="card shadow">
        <div class="card-header bg-white text-white">
        <h3 class="mb-0 text-dark">Generate Request</h3>
        </div>
        <div class="card-body">
            <form action="generate_request.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="username">Parent Username:</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="body">Request Body:</label>
                    <textarea id="body" name="body" rows="5" class="form-control"></textarea>
                </div>

                <div class="form-group">
                    <label for="fileToUpload">Attach File:</label>
                    <input type="file" id="fileToUpload" name="fileToUpload" class="form-control-file">
                </div>

                <button type="submit" class="btn btn-primary btn-block">Generate Request</button>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
