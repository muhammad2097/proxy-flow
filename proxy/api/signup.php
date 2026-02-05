<?php
require_once "cors.php";
require_once "cors.php";
header("Content-Type: application/json");
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/jwt.php';

$input = json_decode(file_get_contents('php://input'), true);
$name = $input['name'] ?? null;
$email = $input['email'] ?? null;
$password = $input['password'] ?? null;

if (!$name || !$email || !$password) {
    http_response_code(400);
    echo json_encode(['error' => 'All fields required']);
    exit;
}

$hashed = password_hash($password, PASSWORD_BCRYPT);

$stmt = $conn->prepare('INSERT INTO User (name, email, password, role) VALUES (?, ?, ?, ?)');
$role = 'user';
$stmt->bind_param('ssss', $name, $email, $hashed, $role);
if (!$stmt->execute()) {
    http_response_code(400);
    echo json_encode(['error' => 'Email already exists or insert failed']);
    exit;
}
$userId = $stmt->insert_id;
$token = generate_jwt(['id' => $userId, 'role' => $role], $JWT_SECRET);

echo json_encode(['token' => $token, 'user' => ['id' => $userId, 'name' => $name, 'email' => $email, 'role' => $role]]);
?>