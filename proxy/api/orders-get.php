<?php
require_once "cors.php";
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/auth.php';

$user = require_auth();
$stmt = $conn->prepare('SELECT * FROM `Order` WHERE userId = ? ORDER BY createdAt DESC');
$stmt->bind_param('i', $user['id']);
$stmt->execute();
$res = $stmt->get_result();
$data = [];
while ($row = $res->fetch_assoc()) $data[] = $row;
echo json_encode($data);
?>