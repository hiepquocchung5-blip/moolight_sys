<?php
/**
 * PORTAL 2: User App Portal (Front Controller V4)
 */
session_start();
$active_portal = 'app';

require_once __DIR__ . '/../functions/global_functions.php';
load_env(__DIR__ . '/../.env');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../functions/api_engine.php';

if (empty($_SESSION['sec_bind'])) $_SESSION['sec_bind'] = bin2hex(random_bytes(16));
$system_bind = $_SESSION['sec_bind'];

$module = $_GET['module'] ?? 'dashboard';
$page = $_GET['page'] ?? 'home';
$request_bind = $_GET['sec_bind'] ?? $_POST['sec_bind'] ?? '';
$is_logged_in = isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $module !== 'actions') {
    if (!hash_equals($system_bind, $request_bind)) {
        http_response_code(403); die("Security Violation.");
    }
}

switch ($module) {
    case 'auth':
        if ($is_logged_in && $page !== 'logout') redirect(get_url('app', '/?module=dashboard&page=home'));
        $allowed = ['login', 'register', 'process_login', 'process_register', 'logout', 'oauth_redirect', 'oauth_callback'];
        if (in_array($page, $allowed)) require_once __DIR__ . "/auth/{$page}.php";
        else redirect(get_url('app', '/?module=auth&page=login'));
        break;

    case 'shop':
        if (!$is_logged_in) redirect(get_url('app', '/?module=auth&page=login'));
        // Added checkout_blindbox and process_blindbox
        $allowed = ['view', 'view_spark', 'checkout', 'checkout_spark', 'checkout_blindbox', 'process_checkout', 'process_blindbox', 'success'];
        if (in_array($page, $allowed)) require_once __DIR__ . "/shop/{$page}.php";
        else redirect(get_url('app', '/?module=dashboard&page=market'));
        break;

    case 'actions':
        if ($page === 'switch_locale') require_once __DIR__ . "/actions/switch_locale.php";
        break;

    case 'dashboard':
    default:
        if (!$is_logged_in) redirect(get_url('app', '/?module=auth&page=login'));
        $allowed = ['home', 'vault', 'ledger', 'ledger_detail', 'settings', 'process_settings', 'support', 'process_whisper', 'market', 'forge', 'blindbox'];
        $view = in_array($page, $allowed) ? $page : 'home';
        require_once __DIR__ . "/views/dashboard/{$view}.php";
        break;
}
?>