<?php
/**
 * API Endpoint: /v1/shop/view_spark
 */
ini_set('display_errors', 0);

function send_clean_json($status_code, $response_array) {
    if (ob_get_length()) ob_clean(); 
    http_response_code($status_code);
    echo json_encode($response_array);
    exit;
}

if (!isset($active_portal) || $active_portal !== 'api') send_clean_json(403, ["status" => "error", "message" => "Forbidden."]);

$spark_id = $_GET['id'] ?? null;

if (!$spark_id) {
    send_clean_json(400, ["status" => "error", "message" => "Spark ID required."]);
}

try {
    $stmt = $pdo->prepare("SELECT * FROM md_neural_sparks WHERE spark_id = :id LIMIT 1");
    $stmt->execute(['id' => $spark_id]);
    $spark = $stmt->fetch();

    if ($spark) {
        // Obfuscate the exact content string so it can't be stolen before purchase
        $spark['content'] = substr($spark['content'], 0, 15) . "... [ENCRYPTED PAYLOAD]";
        send_clean_json(200, ["status" => "success", "data" => $spark]);
    } else {
        send_clean_json(404, ["status" => "error", "message" => "Neural Spark not found."]);
    }

} catch (\PDOException $e) {
    error_log("Spark API Error: " . $e->getMessage());
    send_clean_json(500, ["status" => "error", "message" => "Database Fault."]);
}
?>