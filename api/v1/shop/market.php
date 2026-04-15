<?php
/**
 * API Endpoint: /v1/shop/market
 * Retrieves all active products and AI prompts for the internal App portal.
 */
ini_set('display_errors', 0);

function send_clean_json($status_code, $response_array) {
    if (ob_get_length()) ob_clean(); 
    http_response_code($status_code);
    echo json_encode($response_array);
    exit;
}

if (!isset($active_portal) || $active_portal !== 'api') send_clean_json(403, ["status" => "error"]);

// Verify Token (Users must be logged in to view the internal market)
$headers = apache_request_headers();
$auth_header = $headers['Authorization'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if (!preg_match('/Bearer\s(\S+)/', $auth_header, $matches)) send_clean_json(401, ["status" => "error", "message" => "Unauthorized."]);

try {
    // Fetch Products (Artifacts)
    $stmt_arts = $pdo->query("SELECT * FROM md_artifacts WHERE is_active = 1 ORDER BY art_id DESC");
    $artifacts = $stmt_arts->fetchAll();

    // Fetch AI Prompts (Sparks)
    $stmt_sparks = $pdo->query("SELECT * FROM md_neural_sparks ORDER BY spark_id ASC");
    $sparks = $stmt_sparks->fetchAll();

    send_clean_json(200, [
        "status" => "success",
        "data" => [
            "products" => $artifacts,
            "prompts" => $sparks
        ]
    ]);

} catch (\PDOException $e) {
    error_log("Shop Market API Error: " . $e->getMessage());
    send_clean_json(500, ["status" => "error", "message" => "Database Fault."]);
}
?>