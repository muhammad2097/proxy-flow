<?php
require_once "cors.php";
header("Content-Type: application/json");
require_once __DIR__ . '/../config/db.php';

// Retrieve user_id from GET parameters
$userId = $_GET['user_id'] ?? null;

if (!$userId) {
    http_response_code(400);
    echo json_encode(['error' => 'User ID is required']);
    exit;
}

// Fetch balance from the database
// We use a LEFT JOIN or a direct query. If the user doesn't exist in the balance table, we return 0.
$stmt = $conn->prepare('SELECT balance FROM user_balances WHERE user_id = ?');
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode([
        'balance' => (float)$row['balance']
    ]);
} else {
    // If no row exists for this user, they have a zero balance
    echo json_encode([
        'balance' => 0.00
    ]);
}

$stmt->close();
$conn->close();
?>