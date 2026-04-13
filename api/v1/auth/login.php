<?php
/**
 * API Endpoint: /v1/auth/login
 * Handles user authentication and JWT issuance.
 */
if (!isset($active_portal) || $active_portal !== 'api') {
    http_response_code(403); echo json_encode(["status" => "error", "message" => "Direct access forbidden."]); exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); echo json_encode(["status" => "error", "message" => "Method not allowed"]); exit;
}

$identifier = trim($input['identifier'] ?? '');
$password = $input['password'] ?? '';

if (empty($identifier) || empty($password)) {
    http_response_code(400); echo json_encode(["status" => "error", "message" => "Missing credentials."]); exit;
}

$stmt = $pdo->prepare("SELECT cit_id, moon_tag, pass_hash, rank FROM md_citizens WHERE email = :id OR moon_tag = :id LIMIT 1");
$stmt->execute(['id' => $identifier]);
$citizen = $stmt->fetch();

if ($citizen && password_verify($password, $citizen['pass_hash'])) {
    $jwt_secret = getenv('JWT_SECRET');
    $payload = [
        'cit_id' => $citizen['cit_id'],
        'moon_tag' => $citizen['moon_tag'],
        'rank' => $citizen['rank'],
        'exp' => time() + 86400 // 24-hour expiration
    ];
    $token = generate_jwt($payload, $jwt_secret);
    
    http_response_code(200);
    echo json_encode([
        "status" => "success", 
        "message" => "Authentication successful", 
        "data" => ["moon_tag" => $citizen['moon_tag'], "token" => $token]
    ]);
} else {
    http_response_code(401); echo json_encode(["status" => "error", "message" => "Invalid identity or passcode."]);
}
?>