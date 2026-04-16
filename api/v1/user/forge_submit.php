<?php
/**
 * API Endpoint: /v1/user/forge_submit
 * Parses Bespoke submissions, saves them to threads, and pings the Admin Telegram.
 */
ini_set('display_errors', 0);
require_once __DIR__ . '/../../functions/webhook_engine.php';

function send_clean_json($status_code, $response_array) {
    if (ob_get_length()) ob_clean(); 
    http_response_code($status_code); echo json_encode($response_array); exit;
}
if (!isset($active_portal) || $active_portal !== 'api') send_clean_json(403, ["status" => "error"]);

// Validate JWT
$headers = apache_request_headers();
$auth_header = $headers['Authorization'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if (!preg_match('/Bearer\s(\S+)/', $auth_header, $matches)) send_clean_json(401, ["status" => "error"]);

$token = $matches[1];
$jwt_secret = getenv('JWT_SECRET') ?: ($_ENV['JWT_SECRET'] ?? null);
$token_parts = explode('.', $token);
$signature = hash_hmac('sha256', $token_parts[0] . "." . $token_parts[1], $jwt_secret, true);
$base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
if (!hash_equals($base64UrlSignature, $token_parts[2])) send_clean_json(401, ["status" => "error"]);

$payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $token_parts[1])), true);
if (!isset($payload['exp']) || $payload['exp'] < time()) send_clean_json(401, ["status" => "error"]);

$cit_id = $payload['cit_id'];
$moon_tag = $payload['moon_tag'];

$input_data = (isset($input) && is_array($input)) ? $input : [];
$requirement = trim($input_data['requirement'] ?? '');

if (empty($requirement)) send_clean_json(400, ["status" => "error", "message" => "Empty Forge Data."]);

try {
    // Save to Whisper Threads as a formal request
    $formatted_msg = "🛠️ BESPOKE FORGE SUBMISSION:\n" . $requirement;
    $stmt = $pdo->prepare("INSERT INTO md_whisper_threads (cit_id, message_payload) VALUES (:id, :msg)");
    $stmt->execute(['id' => $cit_id, 'msg' => $formatted_msg]);

    // Blast it straight to the Admin Telegram via the V3 Webhook Engine
    $tg_alert = "🛠️ <b>New Bespoke Forge Request!</b>\n";
    $tg_alert .= "👤 <b>User:</b> {$moon_tag}\n\n";
    $tg_alert .= "<i>{$requirement}</i>";
    
    notify_admin_bot($tg_alert);

    send_clean_json(200, ["status" => "success"]);

} catch (\PDOException $e) {
    error_log("Forge API Error: " . $e->getMessage());
    send_clean_json(500, ["status" => "error"]);
}
?>