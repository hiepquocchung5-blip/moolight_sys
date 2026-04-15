<?php
/**
 * API Endpoint: /v1/user/vault
 */
ini_set('display_errors', 0);

function send_clean_json($status_code, $response_array) {
    if (ob_get_length()) ob_clean(); 
    http_response_code($status_code);
    echo json_encode($response_array);
    exit;
}

if (!isset($active_portal) || $active_portal !== 'api') send_clean_json(403, ["status" => "error", "message" => "Forbidden."]);

// Verify Token
$headers = apache_request_headers();
$auth_header = $headers['Authorization'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if (!preg_match('/Bearer\s(\S+)/', $auth_header, $matches)) send_clean_json(401, ["status" => "error", "message" => "Unauthorized."]);

$token = $matches[1];
$jwt_secret = getenv('JWT_SECRET') ?: ($_ENV['JWT_SECRET'] ?? null);
$token_parts = explode('.', $token);
$signature = hash_hmac('sha256', $token_parts[0] . "." . $token_parts[1], $jwt_secret, true);
$base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
if (!hash_equals($base64UrlSignature, $token_parts[2])) send_clean_json(401, ["status" => "error", "message" => "Invalid signature."]);

$payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $token_parts[1])), true);
if (!isset($payload['exp']) || $payload['exp'] < time()) send_clean_json(401, ["status" => "error", "message" => "Token expired."]);

$cit_id = $payload['cit_id'];

try {
    // Join vault keys with artifact details
    $stmt = $pdo->prepare("
        SELECT a.title, a.category, a.delivery_type, v.payload, v.claimed_at 
        FROM md_vault_keys v 
        JOIN md_artifacts a ON v.art_id = a.art_id 
        WHERE v.claimed_by_cit_id = :id 
        ORDER BY v.claimed_at DESC
    ");
    $stmt->execute(['id' => $cit_id]);
    $artifacts = $stmt->fetchAll();

    send_clean_json(200, ["status" => "success", "data" => $artifacts]);

} catch (\PDOException $e) {
    error_log("Vault API Error: " . $e->getMessage());
    send_clean_json(500, ["status" => "error", "message" => "Database Fault."]);
}
?>