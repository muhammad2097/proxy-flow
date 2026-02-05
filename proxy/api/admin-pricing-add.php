<?php
require_once "cors.php";
header("Content-Type: application/json");

require_once __DIR__ . '/../config/db.php';

// Exit for OPTIONS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

require_once __DIR__ . '/auth.php';

$user = require_auth();

if ($user['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

$raw = file_get_contents("php://input");
$input = json_decode($raw, true);

$type = $input["type"] ?? null;
$country = $input["country"] ?? null;
$week = $input["costPerWeek"] ?? null;
$month = $input["costPerMonth"] ?? null;

$stmt = $conn->prepare("INSERT INTO ProxyPricing (type, country, costPerWeek, costPerMonth) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssdd", $type, $country, $week, $month);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(["error" => "Insert failed", "details" => $stmt->error]);
    exit;
}

echo json_encode(["success" => true, "id" => $stmt->insert_id]);
?>
