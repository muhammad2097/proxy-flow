<?php
// config/jwt.php
$JWT_SECRET = 'proxy-secret-2025';

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode($data) {
    $remainder = strlen($data) % 4;
    if ($remainder) $data .= str_repeat('=', 4 - $remainder);
    return base64_decode(strtr($data, '-_', '+/'));
}

function generate_jwt($payload, $secret) {
    $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);
    $payload_json = json_encode($payload);
    $segments = [
        base64url_encode($header),
        base64url_encode($payload_json)
    ];
    $signing_input = implode('.', $segments);
    $signature = hash_hmac('sha256', $signing_input, $secret, true);
    $segments[] = base64url_encode($signature);
    return implode('.', $segments);
}

function verify_jwt($token, $secret) {
    $parts = explode('.', $token);
    if (count($parts) !== 3) return false;
    list($headb64, $payloadb64, $sigb64) = $parts;
    $sig = base64url_decode($sigb64);
    $verify = hash_hmac('sha256', $headb64.'.'.$payloadb64, $secret, true);
    if (!hash_equals($verify, $sig)) return false;
    $payload = json_decode(base64url_decode($payloadb64), true);
    return $payload;
}
?>
