<?php
require_once "cors.php";
header("Content-Type: application/json");
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/jwt.php';

// Get input
$input = json_decode(file_get_contents('php://input'), true);
$userId = $input['user_id'] ?? null;
$amount = $input['amount'] ?? null;

// Validate input
if (!$userId || !$amount || $amount <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Valid user ID and positive amount required']);
    exit;
}

// 1. Update the balance in the database
// Using ON DUPLICATE KEY UPDATE so it works even if the user has no balance row yet
$stmt = $conn->prepare('INSERT INTO user_balances (user_id, balance) VALUES (?, ?) ON DUPLICATE KEY UPDATE balance = balance + ?');
$stmt->bind_param('idd', $userId, $amount, $amount);

if ($stmt->execute()) {
    // 2. Fetch the new updated balance to return to the frontend
    $stmt = $conn->prepare('SELECT balance FROM user_balances WHERE user_id = ?');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    echo json_encode([
        'success' => true,
        'new_balance' => $row['balance'],
        'message' => 'Top up successful'
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Database update failed']);
}
?>