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

// Debugging: Check session variables
echo "Role: " . (isset($_SESSION['role']) ? $_SESSION['role'] : 'not set') . "<br>";
echo "User ID: " . (isset($_SESSION['id']) ? $_SESSION['id'] : 'not set') . "<br>";

// Check if the user is logged in as admin
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Unauthorized access.');</script>";
    exit;
}

// Fetch event registrations from the database
$sql = "SELECT e.title, er.registrationdate, er.parent_id 
        FROM eventregistrations er 
        JOIN events e ON er.eventid = e.eventid";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Event Registrations</title>
</head>
<body>
    <h1>Event Registrations</h1>
    <table border="1">
        <tr>
            <th>Event Title</th>
            <th>Parent ID</th>
            <th>Registration Date</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td><?php echo htmlspecialchars($row['parent_id']); ?></td>
                <td><?php echo htmlspecialchars($row['registrationdate']); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <a href="admin_dashboard.php">Back to Dashboard</a>
</body>
</html>

<?php $conn->close(); ?>
