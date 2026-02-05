<?php
require_once "cors.php";
header('Content-Type: application/json');
require_once __DIR__ . '/../config/jwt.php';

function get_bearer_token() {
    $headers = getallheaders();
    if (!isset($headers['Authorization']) && !isset($headers['authorization'])) return null;
    $auth = $headers['Authorization'] ?? $headers['authorization'];
    if (strpos($auth, 'Bearer ') === 0) return substr($auth, 7);
    return null;
}

function require_auth() {
    global $JWT_SECRET;
    $token = get_bearer_token();
    if (!$token) {
        http_response_code(401);
        echo json_encode(['error' => 'No token']);
        exit;
    }
    $user = verify_jwt($token, $JWT_SECRET);
    if (!$user) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid token']);
        exit;
    }
    return $user;
}
?>