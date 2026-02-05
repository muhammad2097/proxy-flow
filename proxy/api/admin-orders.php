<?php
require_once "cors.php";
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/auth.php';

$user = require_auth();

if ($user['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

$sql = <<<SQL
SELECT o.*, u.name AS userName, u.email AS userEmail
FROM `Order` o
JOIN User u ON o.userId = u.id
ORDER BY o.createdAt DESC
SQL;

$res = $conn->query($sql);

$data = [];
while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
