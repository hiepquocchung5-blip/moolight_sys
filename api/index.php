<?php
/**
 * PORTAL 5: API Engine (V2)
 */
$active_portal = 'api';
header('Content-Type: application/json');

require_once __DIR__ . '/../functions/global_functions.php';
load_env(__DIR__ . '/../.env');
require_once __DIR__ . '/../config/database.php';

$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// V2: Safely decode JSON and handle errors
$raw_input = file_get_contents('php://input');
$input = json_decode($raw_input, true);
if ($raw_input && json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400); 
    echo json_encode(["status" => "error", "message" => "Malformed JSON payload."]); 
    exit;
}

switch ($request_uri) {
    case '/v1/auth/login':
        require_once __DIR__ . '/v1/auth/login.php';
        break;

    case '/v1/auth/register':
        require_once __DIR__ . '/v1/auth/register.php';
        break;

    case '/v1/user/profile':
        require_once __DIR__ . '/v1/user/profile.php';
        break;

    case '/':
        http_response_code(200);
        echo json_encode(["status" => "online", "message" => "Moonlight API V2 is active."]);
        break;

    default:
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "API Endpoint [{$request_uri}] not found."]);
        break;
}
?>