<?php
/**
 * API Endpoint: /v1/user/profile
 * Validates JWT securely and returns the citizen's profile data.
 */
if (!isset($active_portal) || $active_portal !== 'api') {
    http_response_code(403); echo json_encode(["status" => "error", "message" => "Direct access forbidden."]); exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); echo json_encode(["status" => "error", "message" => "Method not allowed"]); exit;
}

// 1. Extract Authorization Header
$headers = apache_request_headers();
$auth_header = $headers['Authorization'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? '';

if (!preg_match('/Bearer\s(\S+)/', $auth_header, $matches)) {
    http_response_code(401); echo json_encode(["status" => "error", "message" => "Missing or invalid Authorization Bearer token."]); exit;
}

$token = $matches[1];
$jwt_secret = getenv('JWT_SECRET');

// 2. Cryptographically Verify JWT
$token_parts = explode('.', $token);
if (count($token_parts) !== 3) {
    http_response_code(401); echo json_encode(["status" => "error", "message" => "Malformed token structure."]); exit;
}

$signature = hash_hmac('sha256', $token_parts[0] . "." . $token_parts[1], $jwt_secret, true);
$base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

if (!hash_equals($base64UrlSignature, $token_parts[2])) {
    http_response_code(401); echo json_encode(["status" => "error", "message" => "Invalid token signature. Access denied."]); exit;
}

// 3. Decode Payload & Check Expiration
$payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $token_parts[1])), true);

if (!isset($payload['exp']) || $payload['exp'] < time()) {
    http_response_code(401); echo json_encode(["status" => "error", "message" => "Token has expired. Please re-authenticate."]); exit;
}

// 4. Fetch Secure Profile Data
$stmt = $pdo->prepare("SELECT cit_id, moon_tag, email, auth_google_uid, auth_tg_uid, rank, created_at FROM md_citizens WHERE cit_id = :id LIMIT 1");
$stmt->execute(['id' => $payload['cit_id']]);
$user = $stmt->fetch();

if ($user) {
    http_response_code(200);
    echo json_encode(["status" => "success", "data" => $user]);
} else {
    http_response_code(404); echo json_encode(["status" => "error", "message" => "Citizen record not found."]);
}
?>