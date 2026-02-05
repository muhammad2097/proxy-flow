<?php
require_once "cors.php";
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/auth.php';

$user = require_auth();

$stmt = $conn->prepare('SELECT id, name, email, role FROM User WHERE id = ?');
$stmt->bind_param('i', $user['id']);
$stmt->execute();
$res = $stmt->get_result();
echo json_encode($res->fetch_assoc());
?>