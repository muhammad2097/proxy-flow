<?php
require_once "cors.php";
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/auth.php';

$user = require_auth();
$input = json_decode(file_get_contents('php://input'), true);

$userId = $user['id'];
$proxyType = $input['proxyType'] ?? null;
$country = $input['country'] ?? null;
$count = $input['count'] ?? null;
$period = $input['period'] ?? null;
$totalPrice = $input['totalPrice'] ?? null;
$status = 'pending';

// 1. Validate Input
if (!$totalPrice || $totalPrice <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid order amount']);
    exit;
}

// Start Transaction to prevent data leakage
$conn->begin_transaction();

try {
    // 2. Lock the balance row for update (FOR UPDATE prevents race conditions)
    $stmt = $conn->prepare("SELECT balance FROM user_balances WHERE user_id = ? FOR UPDATE");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $balanceData = $result->fetch_assoc();

    $currentBalance = $balanceData ? (float)$balanceData['balance'] : 0.00;

    // 3. Strict Balance Check
    if ($currentBalance < $totalPrice) {
        throw new Exception("Insufficient balance. Please top up your account.");
    }

    // 4. Deduct Balance
    $newBalance = $currentBalance - $totalPrice;
    $updateStmt = $conn->prepare("UPDATE user_balances SET balance = ? WHERE user_id = ?");
    $updateStmt->bind_param("di", $newBalance, $userId);
    if (!$updateStmt->execute()) {
        throw new Exception("Failed to update balance");
    }

    // 5. Create the Order
    $orderStmt = $conn->prepare(
        'INSERT INTO `Order` (userId, proxyType, country, count, period, totalPrice, status)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $orderStmt->bind_param('issisds', $userId, $proxyType, $country, $count, $period, $totalPrice, $status);
    
    if (!$orderStmt->execute()) {
        throw new Exception("Order creation failed: " . $orderStmt->error);
    }

    // 6. Log the Transaction (Recommended for auditing)
    $logStmt = $conn->prepare("INSERT INTO transactions (user_id, amount, type, description) VALUES (?, ?, 'debit', ?)");
    $desc = "Purchase: $proxyType ($count units)";
    $logStmt->bind_param("ids", $userId, $totalPrice, $desc);
    $logStmt->execute();

    // Commit all changes
    $conn->commit();

    echo json_encode([
        'success' => true,
        'new_balance' => $newBalance
    ]);

} catch (Exception $e) {
    // If anything fails, rollback every change made in this block
    $conn->rollback();
    http_response_code(400); // 400 for user errors like low balance
    echo json_encode(['error' => $e->getMessage()]);
}
?>


