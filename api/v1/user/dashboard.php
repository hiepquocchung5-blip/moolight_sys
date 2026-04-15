<?php
/**
 * API Endpoint: /v1/user/dashboard
 * Securely verifies JWT and returns the user's vault counts and ledger history.
 */
ini_set('display_errors', 0);

function send_clean_json($status_code, $response_array) {
    if (ob_get_length()) ob_clean(); 
    http_response_code($status_code);
    echo json_encode($response_array);
    exit;
}

if (!isset($active_portal) || $active_portal !== 'api') send_clean_json(403, ["status" => "error", "message" => "Forbidden."]);

// 1. Extract & Verify Authorization Header
$headers = apache_request_headers();
$auth_header = $headers['Authorization'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? '';

if (!preg_match('/Bearer\s(\S+)/', $auth_header, $matches)) {
    send_clean_json(401, ["status" => "error", "message" => "Missing or invalid Bearer token."]);
}

$token = $matches[1];
$jwt_secret = getenv('JWT_SECRET') ?: ($_ENV['JWT_SECRET'] ?? null);
$token_parts = explode('.', $token);

if (count($token_parts) !== 3) send_clean_json(401, ["status" => "error", "message" => "Malformed token structure."]);

$signature = hash_hmac('sha256', $token_parts[0] . "." . $token_parts[1], $jwt_secret, true);
$base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

if (!hash_equals($base64UrlSignature, $token_parts[2])) send_clean_json(401, ["status" => "error", "message" => "Invalid token signature."]);

$payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $token_parts[1])), true);
if (!isset($payload['exp']) || $payload['exp'] < time()) send_clean_json(401, ["status" => "error", "message" => "Token has expired."]);

$cit_id = $payload['cit_id'];

try {
    // Fetch Vault Stats
    $stmt_vault = $pdo->prepare("SELECT COUNT(*) FROM md_vault_keys WHERE claimed_by_cit_id = :id");
    $stmt_vault->execute(['id' => $cit_id]);
    $vault_count = $stmt_vault->fetchColumn();

    // Fetch Recent Ledgers (Orders)
    $stmt_ledgers = $pdo->prepare("SELECT ledger_id, total_mmk, status, created_at FROM md_treasury_ledgers WHERE cit_id = :id ORDER BY created_at DESC LIMIT 5");
    $stmt_ledgers->execute(['id' => $cit_id]);
    $recent_ledgers = $stmt_ledgers->fetchAll();

    // Fetch Telegram Bind Status
    $stmt_user = $pdo->prepare("SELECT auth_tg_uid FROM md_citizens WHERE cit_id = :id");
    $stmt_user->execute(['id' => $cit_id]);
    $user_data = $stmt_user->fetch();
    
    $is_tg_bound = !empty($user_data['auth_tg_uid']);

    send_clean_json(200, [
        "status" => "success",
        "data" => [
            "vault_count" => $vault_count,
            "recent_ledgers" => $recent_ledgers,
            "is_tg_bound" => $is_tg_bound
        ]
    ]);

} catch (\PDOException $e) {
    error_log("Dashboard API Error: " . $e->getMessage());
    send_clean_json(500, ["status" => "error", "message" => "Database Fault."]);
}