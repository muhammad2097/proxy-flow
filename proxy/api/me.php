<?php
require_once "cors.php";
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/auth.php';

$user = require_auth();

// Use LEFT JOIN to pull balance from the separate table
// IFNULL ensures that if no balance record exists, it defaults to 0.00
$stmt = $conn->prepare('
    SELECT 
        u.id, 
        u.name, 
        u.email, 
        u.role, 
        IFNULL(b.balance, 0.00) as balance 
    FROM User u
    LEFT JOIN user_balances b ON u.id = b.user_id 
    WHERE u.id = ?
');

$stmt->bind_param('i', $user['id']);
$stmt->execute();
$res = $stmt->get_result();
$userData = $res->fetch_assoc();

if ($userData) {
    // Cast balance to float so it's a number in JSON, not a string
    // $userData['balance'] = (float)$userData['balance'];
    $userData['balance'] = (float)($userData['balance'] ?? 0);
    echo json_encode($userData);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'User not found']);
}
?>