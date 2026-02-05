<?php
require_once "cors.php";
header('Content-Type: application/json');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/auth.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Auth check
$user = require_auth();
if ($user['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

// Read PATCH JSON
$raw = file_get_contents("php://input");
$input = json_decode($raw, true);

$status = $input['status'] ?? null;
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$id || !$status) {
    http_response_code(400);
    echo json_encode(['error' => 'id and status required']);
    exit;
}

// Update
$stmt = $conn->prepare("UPDATE `Order` SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $id);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['error' => 'Update failed', 'details' => $stmt->error]);
    exit;
}

echo json_encode(['success' => true]);
?>
