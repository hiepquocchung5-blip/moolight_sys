<?php
/**
 * PORTAL 2: User App Portal (Front Controller)
 * Document Root: /moonlight_root/app/
 * Handles highly secure routing using 32-char bind params.
 */
session_start();

$active_portal = 'app';

// 1. Boot Core Engine
require_once __DIR__ . '/../functions/global_functions.php';
load_env(__DIR__ . '/../.env');
require_once __DIR__ . '/../config/database.php';

// 2. Security Engine: 32-Character Bind Token (CSRF Protection)
if (empty($_SESSION['sec_bind'])) {
    $_SESSION['sec_bind'] = bin2hex(random_bytes(16)); // Generates a secure 32-character hex string
}
$system_bind = $_SESSION['sec_bind'];

// 3. Parse Routing Requests
$module = $_GET['module'] ?? 'dashboard';
$page = $_GET['page'] ?? 'home';
$request_bind = $_GET['sec_bind'] ?? $_POST['sec_bind'] ?? '';

// Check Auth State
$is_logged_in = isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true;

// Secure Bind Validation (Strict enforcement for all POST requests)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($system_bind, $request_bind)) {
        http_response_code(403);
        die("Security Violation: Invalid Bind Hash Parameter. Pulse Lost.");
    }
}

// 4. Secure Route Switcher
switch ($module) {
    case 'auth':
        if ($is_logged_in && $page !== 'logout') {
            redirect(get_url('app', '/?module=dashboard&page=home&sec_bind=' . $system_bind));
        }

        if ($page === 'login') {
            require_once __DIR__ . '/auth/login.php';
        } elseif ($page === 'register') {
            require_once __DIR__ . '/auth/register.php';
        } elseif ($page === 'process_login') {
            require_once __DIR__ . '/auth/process_login.php';
        } elseif ($page === 'process_register') {
            require_once __DIR__ . '/auth/process_register.php';
        } elseif ($page === 'oauth_redirect') {
            require_once __DIR__ . '/auth/oauth_redirect.php';
        } elseif ($page === 'oauth_callback') {
            require_once __DIR__ . '/auth/oauth_callback.php';
        } elseif ($page === 'logout') {
            require_once __DIR__ . '/auth/logout.php';
        } else {
            redirect(get_url('app', '/?module=auth&page=login'));
        }
        break;

    case 'shop':
        if (!$is_logged_in) redirect(get_url('app', '/?module=auth&page=login'));
        
        if ($page === 'checkout') {
            require_once __DIR__ . '/shop/checkout.php';
        }
        break;

    case 'dashboard':
    default:
        if (!$is_logged_in) {
            redirect(get_url('app', '/?module=auth&page=login'));
        }
        // If logged in, load the actual dashboard view (we will separate this into views/dashboard.php later)
        echo "<h1>Secure Dashboard Loaded. Welcome " . esc($_SESSION['moon_tag']) . "</h1>";
        echo "<a href='" . get_url('app', '/?module=auth&page=logout&sec_bind=' . $system_bind) . "'>Logout</a>";
        break;
}
?>
         