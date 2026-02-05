<?php
require_once "cors.php";
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/auth.php';

$user = require_auth();
$input = json_decode(file_get_contents('php://input'), true);

$proxyType = $input['proxyType'] ?? null;
$country = $input['country'] ?? null;
$count = $input['count'] ?? null;
$period = $input['period'] ?? null;
$totalPrice = $input['totalPrice'] ?? null;

$status = 'pending';

// Prepare statement
$stmt = $conn->prepare(
    'INSERT INTO `Order` (userId, proxyType, country, count, period, totalPrice, status)
     VALUES (?, ?, ?, ?, ?, ?, ?)'
);

// FIXED — Correct bind_param types (7 params)
$stmt->bind_param(
    'issisds',
    $user['id'],    // i — INT
    $proxyType,     // s — STRING
    $country,       // s — STRING
    $count,         // i — INT
    $period,        // s — STRING
    $totalPrice,    // d — DOUBLE/FLOAT
    $status         // s — STRING
);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Insert failed',
        'details' => $stmt->error  // helpful debugging
    ]);
    exit;
}

echo json_encode(['success' => true]);
?>
