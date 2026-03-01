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

// Updated Query: Joins with user_balances and defaults to 0.00 if no record exists
$stmt = $conn->prepare('
    SELECT u.id, u.name, u.email, u.password, u.role, IFNULL(b.balance, 0.00) as balance 
    FROM User u 
    LEFT JOIN user_balances b ON u.id = b.user_id 
    WHERE u.email = ?
');
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

// Return the user object INCLUDING the balance
echo json_encode([
    'token' => $token, 
    'user' => [
        'id' => $user['id'], 
        'name' => $user['name'], 
        'email' => $user['email'], 
        'role' => $user['role'],
        'balance' => (float)$user['balance'] // Cast to float for JS number safety
    ]
]);
?>