<?php
// db.php - Central Database Configuration

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'agedprofile';

// Enable error reporting for mysqli to catch issues early
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $user, $pass, $dbname);
    $conn->set_charset("utf8mb4"); // Ensures support for special characters
} catch (Exception $e) {
    die("Database Connection Failed: " . $e->getMessage());
}
?>