<?php
// db_connection.php
$host = 'localhost'; // Database host
$dbname = 'csn-parent-portal'; // Your database name
$user = 'postgres'; // Database username (default is 'postgres' in XAMPP for PostgreSQL)
$password = 'postgres'; // Database password

try {
    // Create a PDO instance
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    // Set error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle connection error
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>