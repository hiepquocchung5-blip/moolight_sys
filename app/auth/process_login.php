<?php
/**
 * App Portal - Process Login (V2)
 */
if (!isset($active_portal) || $active_portal !== 'app') die("Pulse lost.");
require_once __DIR__ . '/../../functions/api_engine.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password = $_POST['password'] ?? '';
    $request_bind = $_POST['sec_bind'] ?? '';

    if (empty($_SESSION['sec_bind']) || !hash_equals($_SESSION['sec_bind'], $request_bind)) {
        redirect(get_url('app', '/?module=auth&page=login&error=' . urlencode('Security violation.')));
    }

    $api_response = call_moonlight_api('/v1/auth/login', 'POST', [
        'identifier' => $identifier,
        'password' => $password
    ]);

    if ($api_response['status_code'] === 200 && isset($api_response['body']['status']) && $api_response['body']['status'] === 'success') {
        
        $_SESSION['is_logged_in'] = true;
        $_SESSION['moon_tag'] = $api_response['body']['data']['moon_tag'];
        $_SESSION['api_token'] = $api_response['body']['data']['token'];
        session_regenerate_id(true);

        redirect(get_url('app', '/?module=dashboard&page=home&sec_bind=' . $_SESSION['sec_bind']));
    } else {
        // V2: Exact API Error Handling
        $error_msg = urlencode($api_response['body']['message'] ?? 'API HTTP Error ' . $api_response['status_code']);
        redirect(get_url('app', '/?module=auth&page=login&error=' . $error_msg));
    }
} else {
    redirect(get_url('app', '/?module=auth&page=login'));
}
?>