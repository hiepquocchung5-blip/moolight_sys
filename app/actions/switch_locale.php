<?php
/**
 * App Portal - Currency & Language Switcher
 */
if (!isset($active_portal) || $active_portal !== 'app') die("Pulse lost.");

$type = $_GET['type'] ?? '';
$val = $_GET['val'] ?? '';

if ($type === 'currency') {
    $_SESSION['currency'] = strtoupper($val);
} elseif ($type === 'lang') {
    $_SESSION['lang'] = strtolower($val);
    
    // Set Google Translate standard cookie (expires in 30 days)
    // Values: en, my (Burmese), ar (Arabic), zh-CN (Chinese), ru (Russian), ja (Japanese), th (Thai)
    setcookie('googtrans', '/en/' . $val, time() + (86400 * 30), '/', '.moonlightdigitalmarket.com');
    setcookie('googtrans', '/en/' . $val, time() + (86400 * 30), '/'); // Fallback for local
}

$redirect = $_SERVER['HTTP_REFERER'] ?? get_url('app', '/?module=dashboard&page=home');
redirect($redirect);
?>