<?php
/**
 * API Endpoint: /v1/user/ledger_detail
 */
ini_set('display_errors', 0);

function send_clean_json($status_code, $response_array) {
    if (ob_get_length()) ob_clean(); http_response_code($status_code); echo json_encode($response_array); exit;
}
if (!isset($active_portal) || $active_portal !== 'api') send_clean_json(403, ["status" => "error"]);

$headers = apache_request_headers();
$auth_header = $headers['Authorization'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if (!preg_match('/Bearer\s(\S+)/', $auth_header, $matches)) send_clean_json(401, ["status" => "error", "message" => "Unauthorized."]);

$token = $matches[1];
$jwt_secret = getenv('JWT_SECRET') ?: ($_ENV['JWT_SECRET'] ?? null);
$token_parts = explode('.', $token);
$signature = hash_hmac('sha256', $token_parts[0] . "." . $token_parts[1], $jwt_secret, true);
$base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
if (!hash_equals($base64UrlSignature, $token_parts[2])) send_clean_json(401, ["status" => "error"]);

$payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $token_parts[1])), true);
if (!isset($payload['exp']) || $payload['exp'] < time()) send_clean_json(401, ["status" => "error"]);

$cit_id = $payload['cit_id'];
$ledger_id = $_GET['id'] ?? null;

if (!$ledger_id) send_clean_json(400, ["status" => "error", "message" => "Ledger ID required."]);

try {
    $stmt = $pdo->prepare("
        SELECT l.*, p.pay_method, p.txn_last_6 
        FROM md_treasury_ledgers l 
        LEFT JOIN md_txn_proofs p ON l.ledger_id = p.ledger_id 
        WHERE l.ledger_id = :lid AND l.cit_id = :cid LIMIT 1
    ");
    $stmt->execute(['lid' => $ledger_id, 'cid' => $cit_id]);
    $ledger = $stmt->fetch();

    if ($ledger) {
        send_clean_json(200, ["status" => "success", "data" => $ledger]);
    } else {
        send_clean_json(404, ["status" => "error", "message" => "Ledger not found."]);
    }

} catch (\PDOException $e) {
    send_clean_json(500, ["status" => "error", "message" => "Database Fault."]);
}
?>