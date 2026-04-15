<?php
/**
 * PORTAL 5: API Engine (V3 Production)
 */
$active_portal = 'api';
header('Content-Type: application/json');

require_once __DIR__ . '/../functions/global_functions.php';
load_env(__DIR__ . '/../.env');
require_once __DIR__ . '/../config/database.php';

$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$raw_input = file_get_contents('php://input');
$input = json_decode($raw_input, true);
if ($raw_input && json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400); 
    echo json_encode(["status" => "error", "message" => "Malformed JSON payload."]); 
    exit;
}

switch ($request_uri) {
    // Auth Routes
    case '/v1/auth/login':
        require_once __DIR__ . '/v1/auth/login.php'; break;
    case '/v1/auth/register':
        require_once __DIR__ . '/v1/auth/register.php'; break;

    // Secure User Data Routes
    case '/v1/user/dashboard':
        require_once __DIR__ . '/v1/user/dashboard.php'; break;
    case '/v1/user/settings':
        require_once __DIR__ . '/v1/user/settings.php'; break;
    case '/v1/user/vault':
        require_once __DIR__ . '/v1/user/vault.php'; break;
    case '/v1/user/ledger':
        require_once __DIR__ . '/v1/user/ledger.php'; break;

    case '/':
        http_response_code(200); echo json_encode(["status" => "online"]); break;
    default:
        http_response_code(404); echo json_encode(["status" => "error", "message" => "API Endpoint not found."]); break;
}
?>