<?php
/**
 * API Endpoint: /v1/shop/view_artifact
 */
ini_set('display_errors', 0);

function send_clean_json($status_code, $response_array) {
    if (ob_get_length()) ob_clean(); 
    http_response_code($status_code);
    echo json_encode($response_array);
    exit;
}

if (!isset($active_portal) || $active_portal !== 'api') send_clean_json(403, ["status" => "error", "message" => "Forbidden."]);

$art_id = $_GET['id'] ?? null;

if (!$art_id) {
    send_clean_json(400, ["status" => "error", "message" => "Artifact ID required."]);
}

try {
    $stmt = $pdo->prepare("SELECT * FROM md_artifacts WHERE art_id = :id AND is_active = 1 LIMIT 1");
    $stmt->execute(['id' => $art_id]);
    $artifact = $stmt->fetch();

    if ($artifact) {
        send_clean_json(200, ["status" => "success", "data" => $artifact]);
    } else {
        send_clean_json(404, ["status" => "error", "message" => "Artifact offline or depleted."]);
    }

} catch (\PDOException $e) {
    error_log("Shop API Error: " . $e->getMessage());
    send_clean_json(500, ["status" => "error", "message" => "Database Fault."]);
}
?>