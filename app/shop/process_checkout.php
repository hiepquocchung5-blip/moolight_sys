<?php
/**
 * App Portal - Process Checkout Upload
 * Receives the multipart form, creates the ledger, and sends the proof to the API.
 */
if (!isset($active_portal) || $active_portal !== 'app') die("Pulse lost.");
require_once __DIR__ . '/../../functions/api_engine.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['proof_image'])) {
    
    $request_bind = $_POST['sec_bind'] ?? '';
    $art_id = $_POST['art_id'] ?? null;
    $txn_last_6 = $_POST['txn_last_6'] ?? '';
    $pay_method = $_POST['pay_method'] ?? 'KBZPay';

    // 1. Strict CSRF Validation
    if (empty($_SESSION['sec_bind']) || !hash_equals($_SESSION['sec_bind'], $request_bind)) {
        redirect(get_url('app', '/?module=shop&page=checkout&art_id=' . $art_id . '&error=' . urlencode('Security violation.')));
    }

    // 2. We need to create a Treasury Ledger entry first to tie the proof to.
    // Fetch price from DB
    $stmt = $pdo->prepare("SELECT base_price_mmk FROM md_artifacts WHERE art_id = :id");
    $stmt->execute(['id' => $art_id]);
    $artifact = $stmt->fetch();

    if (!$artifact) {
        redirect(get_url('app', '/?module=dashboard&page=home&error=Invalid+Artifact'));
    }

    try {
        $pdo->beginTransaction();

        // Get user ID based on session moon_tag
        $user_stmt = $pdo->prepare("SELECT cit_id FROM md_citizens WHERE moon_tag = :tag");
        $user_stmt->execute(['tag' => $_SESSION['moon_tag']]);
        $cit_id = $user_stmt->fetchColumn();

        // Create Ledger
        $ledger_stmt = $pdo->prepare("INSERT INTO md_treasury_ledgers (cit_id, total_mmk, display_currency, status, checkout_expires_at) VALUES (?, ?, ?, 'pending', DATE_ADD(NOW(), INTERVAL 10 MINUTE))");
        $ledger_stmt->execute([$cit_id, $artifact['base_price_mmk'], $_SESSION['currency'] ?? 'MMK']);
        $ledger_id = $pdo->lastInsertId();

        $pdo->commit();

    } catch (\PDOException $e) {
        $pdo->rollBack();
        redirect(get_url('app', '/?module=shop&page=checkout&art_id=' . $art_id . '&error=' . urlencode('Ledger creation failed.')));
    }

    // 3. Prepare data for the API (Multipart Form Data)
    $api_payload = [
        'ledger_id' => $ledger_id,
        'txn_last_6' => $txn_last_6,
        'pay_method' => $pay_method,
        // Wrap the physical file so our API engine sends it via multipart/form-data
        'proof_image' => new CURLFile(
            $_FILES['proof_image']['tmp_name'], 
            $_FILES['proof_image']['type'], 
            $_FILES['proof_image']['name']
        )
    ];

    // 4. Transmit Image to API Portal
    $api_response = call_moonlight_api('/v1/uploads/proof', 'POST', $api_payload);

    if ($api_response['status_code'] === 200 && $api_response['body']['status'] === 'success') {
        redirect(get_url('app', '/?module=dashboard&page=home&success=' . urlencode('Proof submitted! Awaiting MoonAdmin verification.')));
    } else {
        $error = urlencode($api_response['body']['message'] ?? 'Proof Upload Failed.');
        redirect(get_url('app', '/?module=shop&page=checkout&art_id=' . $art_id . '&error=' . $error));
    }
} else {
    redirect(get_url('app', '/?module=dashboard&page=home'));
}
?>