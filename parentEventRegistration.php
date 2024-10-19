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

// Start session to retrieve the parent user's information
session_start();

// Check if the user is logged in and is a parent
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'parent' || $_SESSION['status'] !== 'active') {
    echo "<script>alert('Only active parents can register for events.');</script>";
    exit;
}

$parent_id = $_SESSION['id']; // Parent's ID from session

// Handle registration
if (isset($_POST['register'])) {
    $eventid = $_POST['eventid'];

    // Check current registrations for the event
    $check_slots = "SELECT COUNT(*) AS total FROM eventregistrations WHERE eventid = ?";
    $stmt = $conn->prepare($check_slots);
    $stmt->bind_param('i', $eventid);
    $stmt->execute();
    $stmt->bind_result($total_registrations);
    $stmt->fetch();
    $stmt->close();

    // Get the max slots for the event
    $get_event = "SELECT max_slots FROM events WHERE eventid = ?";
    $stmt = $conn->prepare($get_event);
    $stmt->bind_param('i', $eventid);
    $stmt->execute();
    $stmt->bind_result($max_slots);
    $stmt->fetch();
    $stmt->close();

    if ($total_registrations < $max_slots) {
        // Register the parent for the event
        $sql = "INSERT INTO eventregistrations (parent_id, eventid, registrationdate) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $parent_id, $eventid); // Use parent_id for registration

        if ($stmt->execute()) {
            echo "<script>alert('Successfully registered for the event!');</script>";
        } else {
            echo "<script>alert('Error registering for the event.');</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Slots are already full.');</script>";
    }
}

// Fetch upcoming events
$sql = "SELECT * FROM events WHERE date >= CURDATE()";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Parent Dashboard</title>
</head>
<body>
    <h1>Upcoming Events</h1>
    <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li>
                <h2><?php echo htmlspecialchars($row['title']); ?></h2>
                <p><?php echo htmlspecialchars($row['description']); ?></p>
                <p>Type: <?php echo htmlspecialchars($row['type']); ?></p>
                <p>Date: <?php echo htmlspecialchars($row['date']); ?> Time: <?php echo htmlspecialchars($row['time']); ?></p>
                <p>Location: <?php echo htmlspecialchars($row['location']); ?></p>
                <p>Slots: <?php echo htmlspecialchars($row['max_slots']); ?></p>

                <?php
                // Check available slots
                $check_slots = "SELECT COUNT(*) AS total FROM eventregistrations WHERE eventid = " . $row['eventid'];
                $slots_result = $conn->query($check_slots);
                $slots_data = $slots_result->fetch_assoc();
                $available_slots = $row['max_slots'] - $slots_data['total'];
                ?>

                <p>Available Slots: <?php echo $available_slots; ?></p>

                <?php if ($available_slots > 0): ?>
                    <form method="POST" action="">
                        <input type="hidden" name="eventid" value="<?php echo $row['eventid']; ?>">
                        <button type="submit" name="register">Register</button>
                    </form>
                <?php else: ?>
                    <p>Slots are full.</p>
                <?php endif; ?>
            </li>
        <?php endwhile; ?>
    </ul>
</body>
</html>

<?php $conn->close(); ?>

