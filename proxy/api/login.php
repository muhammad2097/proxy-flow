<?php
require_once "cors.php";
header("Content-Type: application/json");
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/jwt.php';

$input = json_decode(file_get_contents('php://input'), true);
$email = $input['email'] ?? null;
$password = $input['password'] ?? null;

if (!$email || !$password) {
    http_response_code(400);
    echo json_encode(['error' => 'Email & password required']);
    exit;
}

$stmt = $conn->prepare('SELECT id, name, email, password, role FROM User WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || !password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid credentials']);
    exit;
}

$token = generate_jwt(['id' => $user['id'], 'role' => $user['role']], $JWT_SECRET);
echo json_encode(['token' => $token, 'user' => ['id' => $user['id'], 'name' => $user['name'], 'email' => $user['email'], 'role' => $user['role']]]);
?>