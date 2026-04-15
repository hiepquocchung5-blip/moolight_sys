<?php
/**
 * PORTAL 2: User App Portal (Front Controller V3.5)
 * Handles highly secure routing using 32-char bind params.
 */
session_start();
$active_portal = 'app';

require_once __DIR__ . '/../functions/global_functions.php';
load_env(__DIR__ . '/../.env');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../functions/api_engine.php';

if (empty($_SESSION['sec_bind'])) {
    $_SESSION['sec_bind'] = bin2hex(random_bytes(16));
}
$system_bind = $_SESSION['sec_bind'];

$module = $_GET['module'] ?? 'dashboard';
$page = $_GET['page'] ?? 'home';
$request_bind = $_GET['sec_bind'] ?? $_POST['sec_bind'] ?? '';

$is_logged_in = isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $module !== 'actions') {
    if (!hash_equals($system_bind, $request_bind)) {
        http_response_code(403);
        die("Security Violation: Invalid Bind Hash Parameter. Pulse Lost.");
    }
}

switch ($module) {
    case 'auth':
        if ($is_logged_in && $page !== 'logout') redirect(get_url('app', '/?module=dashboard&page=home&sec_bind=' . $system_bind));
        $allowed_pages = ['login', 'register', 'process_login', 'process_register', 'logout', 'oauth_redirect', 'oauth_callback'];
        if (in_array($page, $allowed_pages) && file_exists(__DIR__ . "/auth/{$page}.php")) {
            require_once __DIR__ . "/auth/{$page}.php";
        } else {
            redirect(get_url('app', '/?module=auth&page=login'));
        }
        break;

    case 'shop':
        if (!$is_logged_in) redirect(get_url('app', '/?module=auth&page=login'));
        $allowed_shop = ['view', 'view_spark', 'checkout', 'checkout_spark', 'process_checkout'];
        if (in_array($page, $allowed_shop) && file_exists(__DIR__ . "/shop/{$page}.php")) {
            require_once __DIR__ . "/shop/{$page}.php";
        } else {
            redirect(get_url('app', '/?module=dashboard&page=market&error=' . urlencode("Item unavailable.")));
        }
        break;

    // V3.5 Localization Engine Route
    case 'actions':
        if ($page === 'switch_locale' && file_exists(__DIR__ . "/actions/switch_locale.php")) {
            require_once __DIR__ . "/actions/switch_locale.php";
        }
        break;

    case 'dashboard':
    default:
        if (!$is_logged_in) redirect(get_url('app', '/?module=auth&page=login'));
        // Added 'market' to allowed views
        $allowed_views = ['home', 'vault', 'ledger', 'settings', 'process_settings', 'support', 'process_whisper', 'market'];
        $view_file = in_array($page, $allowed_views) ? $page : 'home';
        require_once __DIR__ . "/views/dashboard/{$view_file}.php";
        break;
}
?>