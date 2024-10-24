<?php
session_start();
require 'db_connection.php'; // Include the database connection

// Fetch the announcements
try {
    $sql = "SELECT title, body, date FROM announcements ORDER BY date DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching announcements: " . $e->getMessage();
}

// Get username from session
$username = $_SESSION['username'];

// Fetch requests for the logged-in user
$sql = "SELECT * FROM requests WHERE username = :username ORDER BY date DESC";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':username', $username);
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch upcoming events
try {
    $query = "SELECT eventid, title, description, date, time, location, type, max_slots FROM events ORDER BY date ASC";
    $stmt = $pdo->query($query);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching events: " . $e->getMessage();
}

// Sort and display limited requests
usort($requests, function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});
$displayedRequests = array_slice($requests, 0, 3);

// Fetch all requests
function fetchAllRequests($pdo) {
    try {
        $query = "SELECT id, date, username, title, body, file_path FROM requests ORDER BY date DESC";
        $stmt = $pdo->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error fetching all requests: " . $e->getMessage();
        return [];
    }
}
$allRequests = fetchAllRequests($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="styles.css"> <!-- Your custom CSS -->
</head>
<body>

<?php include 'parent_sidebar.php'; ?> <!-- Include the sidebar here -->

<div class="content container mt-4">
    <h1 class="mb-4">Dashboard</h1>

    <!-- Inbox Section -->
    <div class="row align-items-center mb-3">
        <div class="col-12 col-md-6">
            <h3>Inbox</h3>
        </div>
        <div class="col-12 col-md-6 text-right">
            <a href="inbox.php" class="btn btn-secondary">View All</a>
        </div>
    </div>

    <div class="row">
        <?php if (empty($requests)): ?>
            <div class="col-12">
                <div class="alert alert-warning">No requests found.</div>
            </div>
        <?php else: ?>
            <?php foreach ($displayedRequests as $request): ?>
                <div class="col-12 mb-3">
                    <div class="card shadow-sm" data-toggle="modal" data-target="#requestModal-<?= htmlspecialchars($request['id']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($request['title']); ?></h5>
                            <p class="card-text"><strong>Date:</strong> <?= htmlspecialchars($request['date']); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Modal for Request Details -->
                <div class="modal fade" id="requestModal-<?= htmlspecialchars($request['id']); ?>" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><?= htmlspecialchars($request['title']); ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Date:</strong> <?= htmlspecialchars($request['date']); ?></p>
                                <p><strong>Message:</strong> <?= nl2br(htmlspecialchars($request['body'])); ?></p>
                                <?php if (!empty($request['file_path'])): ?>
                                    <p><strong>File:</strong> <a href="<?= htmlspecialchars($request['file_path']); ?>" download><?= htmlspecialchars($request['file_path']); ?></a></p>
                                <?php else: ?>
                                    <p>No File Attached</p>
                                <?php endif; ?>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Announcements and Events Section -->
    <div class="row mt-4">
        <!-- Announcements -->
        <div class="col-12 col-md-6">
            <h4>Announcements</h4>
            <?php if ($announcements): ?>
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($announcements[0]['title']); ?></h5>
                        <p class="card-text"><strong>Date:</strong> <?= htmlspecialchars($announcements[0]['date']); ?></p>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info">No announcements available at the moment.</div>
            <?php endif; ?>
        </div>

        <!-- Events -->
        <div class="col-12 col-md-6">
            <h4>Upcoming Events</h4>
            <?php if ($events): ?>
                <?php foreach ($events as $event): ?>
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($event['title']); ?></h5>
                            <p class="card-text"><strong>Date:</strong> <?= htmlspecialchars($event['date']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info">No upcoming events available at the moment.</div>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
