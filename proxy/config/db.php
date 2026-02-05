<?php
// config/db.php
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$name = getenv('DB_NAME') ?: 'proxyapp';
$port = getenv('DB_PORT') ?: 3306;

$conn = new mysqli($host, $user, $pass, $name, $port);
if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(['error' => 'DB connection failed: ' . $conn->connect_error]));
}
$conn->set_charset('utf8');
?>
