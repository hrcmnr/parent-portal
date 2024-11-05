<?php
// Start the session
session_start();

// Include database connection
include 'db_connection.php';

// Fetch all events from the database
try {
    $sql = "SELECT * FROM events";
    $stmt = $pdo->query($sql);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all events
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Error fetching events: " . htmlspecialchars($e->getMessage()) . "</div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Participation - Event List</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<?php include 'admin_sidebar.php'; ?> <!-- Include the admin sidebar here -->

<div class="container-fluid">
    <div class="content p-4">
        <h1 class="display-6 mb-4">Parent Participation - Event List</h1>

<!-- Button Container -->
<div class="d-flex justify-content-between align-items-center mb-4 w-100">
    <!-- Refresh Button -->
    <form method="POST" class="mb-0">
        <button type="submit" class="btn btn-outline-secondary" name="refresh">
            <i class="fas fa-sync-alt"></i> Refresh
        </button>
    </form>

    <!-- Search Form Container -->
    <div class="ms-auto">
        <form method="POST" class="mb-0">
            <div class="input-group" style="width: 250px;">
                <input type="text" name="search_value" 
                    value="<?= isset($searchValue) ? htmlspecialchars($searchValue) : ''; ?>" 
                    class="form-control" placeholder="Search by Title, Description, Date, Time, Location, or Type" 
                    aria-label="Search" required>
                <button type="submit" name="search" class="btn btn-primary">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
        </form>
    </div>
</div>

         <!-- Event Table -->
        <div class="table-responsive mt-4">
             <table class="table table-hover table-bordered mx-auto">
        <thead>
            <tr>
                <th class="text-center"></th> <!-- Centered numbering column -->
                <th class="text-center">Title</th>
                <th class="text-center">Description</th>
                <th class="text-center">Date</th>
                <th class="text-center">Time</th>
                <th class="text-center">Location</th>
                <th class="text-center">Type</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $number = 1; // Initialize row numbering
            foreach ($events as $event): ?>
                <tr>
                    <td class="text-center"><?= $number++; ?></td> <!-- Centered row number -->
                    <td class="text-center"><?= htmlspecialchars($event['title']); ?></td>
                    <td class="text-center"><?= htmlspecialchars($event['description']); ?></td>
                    <td class="text-center"><?= htmlspecialchars($event['date']); ?></td>
                    <td class="text-center"><?= htmlspecialchars($event['time']); ?></td>
                    <td class="text-center"><?= htmlspecialchars($event['location']); ?></td>
                    <td class="text-center"><?= htmlspecialchars($event['type']); ?></td>
                    <td class="text-center">
                            <a href="view_enrollment.php?eventid=<?php echo htmlspecialchars($event['eventid']); ?>" class="btn btn-outline-info">View Enrollees</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>