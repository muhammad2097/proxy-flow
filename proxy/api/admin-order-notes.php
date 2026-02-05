<?php
require_once "cors.php";
header('Content-Type: application/json');

require_once __DIR__ . '/../config/db.php';

// IMPORTANT: Stop here if this is preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/auth.php';

// Authentication
$user = require_auth();
if ($user['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

// Read PATCH JSON
$raw = file_get_contents("php://input");
$input = json_decode($raw, true);

$notes = $input['notes'] ?? null;
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'id required']);
    exit;
}

$stmt = $conn->prepare("UPDATE `Order` SET notes = ? WHERE id = ?");
$stmt->bind_param("si", $notes, $id);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['error' => 'Update failed', 'details' => $stmt->error]);
    exit;
}

echo json_encode(['success' => true]);
?>
