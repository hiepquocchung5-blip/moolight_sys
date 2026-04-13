<?php
/**
 * App Portal - Process Login (V1.2)
 * Securely matches the frontend form to the API login endpoint.
 */
if (!isset($active_portal) || $active_portal !== 'app') die("Pulse lost.");
require_once __DIR__ . '/../../functions/api_engine.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password = $_POST['password'] ?? '';
    $request_bind = $_POST['sec_bind'] ?? '';

    // 1. Strict CSRF Validation
    if (empty($_SESSION['sec_bind']) || !hash_equals($_SESSION['sec_bind'], $request_bind)) {
        redirect(get_url('app', '/?module=auth&page=login&error=' . urlencode('Security violation: Invalid session bind.')));
    }

    if (empty($identifier) || empty($password)) {
        redirect(get_url('app', '/?module=auth&page=login&error=' . urlencode('Please fill in all fields.')));
    }

    // 2. Transmit to Internal API
    $api_response = call_moonlight_api('/v1/auth/login', 'POST', [
        'identifier' => $identifier,
        'password' => $password
    ]);

    // 3. Handle API Response
    if ($api_response['status_code'] === 200 && isset($api_response['body']['status']) && $api_response['body']['status'] === 'success') {
        
        // Success! Set Browser Sessions securely
        $_SESSION['is_logged_in'] = true;
        $_SESSION['moon_tag'] = $api_response['body']['data']['moon_tag'];
        $_SESSION['api_token'] = $api_response['body']['data']['token'];
        
        // Regenerate session ID for security against session fixation attacks
        session_regenerate_id(true);

        // Redirect to User Dashboard securely using the bind param
        redirect(get_url('app', '/?module=dashboard&page=home&sec_bind=' . $_SESSION['sec_bind']));
        
    } else {
        // Failed! Redirect back to the login page with the API's error message
        $error_msg = urlencode($api_response['body']['message'] ?? 'Authentication Failed. Check your credentials.');
        redirect(get_url('app', '/?module=auth&page=login&error=' . $error_msg));
    }
} else {
    // If someone tries to access this file directly via GET, bounce them back.
    redirect(get_url('app', '/?module=auth&page=login'));
}
?>