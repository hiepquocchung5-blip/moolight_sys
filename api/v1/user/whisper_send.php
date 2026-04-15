<?php
/**
 * API Endpoint: /v1/user/whisper_send
 */
ini_set('display_errors', 0);
require_once __DIR__ . '/../../functions/webhook_engine.php';

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
$moon_tag = $payload['moon_tag'];

$input_data = (isset($input) && is_array($input)) ? $input : [];
$msg_payload = trim($input_data['payload'] ?? '');

if (empty($msg_payload)) {
    send_clean_json(400, ["status" => "error", "message" => "Payload cannot be empty."]);
}

try {
    // 1. Insert into Database
    $stmt = $pdo->prepare("INSERT INTO md_whisper_threads (cit_id, message_payload) VALUES (:id, :msg)");
    $stmt->execute(['id' => $cit_id, 'msg' => $msg_payload]);

    // 2. Trigger Telegram Alert to Admin
    $alert_msg = "🔔 <b>New Whisper Received</b>\n";
    $alert_msg .= "👤 <b>User:</b> " . esc($moon_tag) . "\n";
    $alert_msg .= "💬 <b>Message:</b>\n<i>" . esc($msg_payload) . "</i>";
    notify_admin_bot($alert_msg);

    send_clean_json(200, ["status" => "success", "message" => "Signal transmitted."]);

} catch (\PDOException $e) {
    error_log("Whisper Send API Error: " . $e->getMessage());
    send_clean_json(500, ["status" => "error", "message" => "Database Fault."]);
}
?>