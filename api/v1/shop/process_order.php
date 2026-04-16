<?php
/**
 * API Endpoint: /v1/shop/process_order
 */
ini_set('display_errors', 0);
require_once __DIR__ . '/../../functions/webhook_engine.php';

function send_clean_json($status_code, $response_array) {
    if (ob_get_length()) ob_clean(); 
    http_response_code($status_code); echo json_encode($response_array); exit;
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
if (!hash_equals($base64UrlSignature, $token_parts[2])) send_clean_json(401, ["status" => "error", "message" => "Invalid signature."]);

$payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $token_parts[1])), true);
if (!isset($payload['exp']) || $payload['exp'] < time()) send_clean_json(401, ["status" => "error", "message" => "Token expired."]);

$cit_id = $payload['cit_id'];
$art_id = $_POST['art_id'] ?? null;
$spark_id = $_POST['spark_id'] ?? null;
$txn_last_6 = $_POST['txn_last_6'] ?? 'FREE';

if (!$art_id && !$spark_id) send_clean_json(400, ["status" => "error", "message" => "Invalid Item."]);

try {
    $pdo->beginTransaction();

    $total_mmk = 0;
    $item_name = "";

    // Calculate Price & Stock
    if ($art_id) {
        $stmt = $pdo->prepare("SELECT base_price_mmk, title FROM md_artifacts WHERE art_id = :id AND is_active = 1 FOR UPDATE");
        $stmt->execute(['id' => $art_id]);
        $art = $stmt->fetch();
        if (!$art) throw new Exception("Artifact offline.");
        $total_mmk = $art['base_price_mmk'];
        $item_name = $art['title'];

        // Check if there is an available vault key for this artifact
        $check_key = $pdo->prepare("SELECT key_id FROM md_vault_keys WHERE art_id = :id AND is_claimed = 0 LIMIT 1");
        $check_key->execute(['id' => $art_id]);
        if (!$check_key->fetchColumn()) throw new Exception("Artifact Out of Stock.");
    } else {
        $stmt = $pdo->prepare("SELECT price_mmk, title FROM md_neural_sparks WHERE spark_id = :id");
        $stmt->execute(['id' => $spark_id]);
        $spark = $stmt->fetch();
        if (!$spark) throw new Exception("Spark offline.");
        $total_mmk = $spark['price_mmk'];
        $item_name = $spark['title'];
    }

    // Handle File Upload if it's not a free item
    $image_path = 'assets/img/free_claim.png';
    if ($total_mmk > 0) {
        if (!isset($_FILES['proof_image']) || $_FILES['proof_image']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Transaction proof image required.");
        }
        $upload_dir = __DIR__ . '/../../../assets/uploads/proofs/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        
        $ext = pathinfo($_FILES['proof_image']['name'], PATHINFO_EXTENSION);
        $filename = 'proof_' . $cit_id . '_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['proof_image']['tmp_name'], $upload_dir . $filename);
        $image_path = 'assets/uploads/proofs/' . $filename;
    }

    // Create Ledger
    $ledger_status = ($total_mmk > 0) ? 'verifying' : 'complete';
    $stmt = $pdo->prepare("INSERT INTO md_treasury_ledgers (cit_id, total_mmk, status, checkout_expires_at) VALUES (:cid, :mmk, :status, DATE_ADD(NOW(), INTERVAL 10 MINUTE))");
    $stmt->execute(['cid' => $cit_id, 'mmk' => $total_mmk, 'status' => $ledger_status]);
    $ledger_id = $pdo->lastInsertId();

    // Store Proof
    $stmt = $pdo->prepare("INSERT INTO md_txn_proofs (ledger_id, pay_method, txn_last_6, proof_image_path) VALUES (:lid, 'KBZPay', :txn, :img)");
    $stmt->execute(['lid' => $ledger_id, 'txn' => $txn_last_6, 'img' => $image_path]);

    // If it's a paid artifact, allocate a vault key but leave it unviewable until Admin verifies
    if ($art_id && $total_mmk > 0) {
        $update_key = $pdo->prepare("UPDATE md_vault_keys SET is_claimed = 1, claimed_by_cit_id = :cid WHERE art_id = :aid AND is_claimed = 0 LIMIT 1");
        $update_key->execute(['cid' => $cit_id, 'aid' => $art_id]);
    }

    $pdo->commit();

    // Alert Admin
    if ($total_mmk > 0) notify_admin_bot("💰 <b>New Pending Order #{$ledger_id}</b>\n📦 Item: {$item_name}\n💳 Amount: {$total_mmk} Ks");

    send_clean_json(200, ["status" => "success", "data" => ["ledger_id" => $ledger_id]]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    send_clean_json(400, ["status" => "error", "message" => $e->getMessage()]);
}
?>