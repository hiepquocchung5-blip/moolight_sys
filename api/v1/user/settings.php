<?php
/**
 * API Endpoint: /v1/user/settings
 * Securely verifies JWT and updates the user's password.
 */
ini_set('display_errors', 0);

function send_clean_json($status_code, $response_array) {
    if (ob_get_length()) ob_clean(); 
    http_response_code($status_code);
    echo json_encode($response_array);
    exit;
}

if (!isset($active_portal) || $active_portal !== 'api') send_clean_json(403, ["status" => "error", "message" => "Forbidden."]);

// 1. Verify Token
$headers = apache_request_headers();
$auth_header = $headers['Authorization'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if (!preg_match('/Bearer\s(\S+)/', $auth_header, $matches)) send_clean_json(401, ["status" => "error", "message" => "Unauthorized."]);

$token = $matches[1];
$jwt_secret = getenv('JWT_SECRET') ?: ($_ENV['JWT_SECRET'] ?? null);
$token_parts = explode('.', $token);

if (count($token_parts) !== 3) send_clean_json(401, ["status" => "error", "message" => "Malformed token."]);

$signature = hash_hmac('sha256', $token_parts[0] . "." . $token_parts[1], $jwt_secret, true);
$base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
if (!hash_equals($base64UrlSignature, $token_parts[2])) send_clean_json(401, ["status" => "error", "message" => "Invalid signature."]);

$payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $token_parts[1])), true);
if (!isset($payload['exp']) || $payload['exp'] < time()) send_clean_json(401, ["status" => "error", "message" => "Token expired."]);

$cit_id = $payload['cit_id'];
$input_data = (isset($input) && is_array($input)) ? $input : [];

// 2. Process Password Update
$current_pass = $input_data['current_password'] ?? '';
$new_pass = $input_data['new_password'] ?? '';

if (empty($current_pass) || empty($new_pass)) {
    send_clean_json(400, ["status" => "error", "message" => "Required fields missing."]);
}

try {
    // Get current hash
    $stmt = $pdo->prepare("SELECT pass_hash FROM md_citizens WHERE cit_id = :id");
    $stmt->execute(['id' => $cit_id]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($current_pass, $user['pass_hash'])) {
        send_clean_json(401, ["status" => "error", "message" => "Current passcode is incorrect."]);
    }

    // Update with new hash
    $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
    $update = $pdo->prepare("UPDATE md_citizens SET pass_hash = :hash WHERE cit_id = :id");
    $update->execute(['hash' => $new_hash, 'id' => $cit_id]);

    send_clean_json(200, ["status" => "success", "message" => "Security credentials updated."]);

} catch (\PDOException $e) {
    error_log("Settings API Error: " . $e->getMessage());
    send_clean_json(500, ["status" => "error", "message" => "Database Fault."]);
}
?>