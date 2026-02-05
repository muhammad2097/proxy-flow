<?php
require_once "cors.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header("Content-Type: application/json");
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/auth.php';

$user = require_auth();
if ($user['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["error" => "Forbidden"]);
    exit;
}

$id = isset($_GET["id"]) ? intval($_GET["id"]) : null;
if (!$id) {
    http_response_code(400);
    echo json_encode(["error" => "id required"]);
    exit;
}

$raw = file_get_contents("php://input");
$input = json_decode($raw, true);

$type = $input["type"] ?? null;
$country = $input["country"] ?? null;
$week = $input["costPerWeek"] ?? null;
$month = $input["costPerMonth"] ?? null;

$stmt = $conn->prepare("UPDATE ProxyPricing SET type=?, country=?, costPerWeek=?, costPerMonth=? WHERE id=?");
$stmt->bind_param("ssddi", $type, $country, $week, $month, $id);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(["error" => "Update failed", "details" => $stmt->error]);
    exit;
}

echo json_encode(["success" => true]);
?>
