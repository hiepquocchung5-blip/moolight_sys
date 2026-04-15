<?php
/**
 * App Portal - Process Login (V3 Production)
 * Secure login handler with CSRF protection and exact API error mapping.
 */
if (!isset($active_portal) || $active_portal !== 'app') die("Pulse lost.");
require_once __DIR__ . '/../../functions/api_engine.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password = $_POST['password'] ?? '';
    $request_bind = $_POST['sec_bind'] ?? '';

    // 1. Strict CSRF Validation
    if (empty($_SESSION['sec_bind']) || !hash_equals($_SESSION['sec_bind'], $request_bind)) {
        redirect(get_url('app', '/?module=auth&page=login&error=' . urlencode('Security violation: Session mismatch.')));
    }

    // 2. Pre-Flight Validation (Saves API calls and provides instant feedback)
    if (empty($identifier) || empty($password)) {
        redirect(get_url('app', '/?module=auth&page=login&error=' . urlencode('Identity and Passcode are strictly required.')));
    }

    // 3. Transmit to Secure API Portal
    $api_response = call_moonlight_api('/v1/auth/login', 'POST', [
        'identifier' => $identifier,
        'password' => $password
    ]);

    // 4. Handle API Response & Session Fixation Protection
    if ($api_response['status_code'] === 200 && isset($api_response['body']['status']) && $api_response['body']['status'] === 'success') {
        
        // Prevent Session Fixation attacks by regenerating the ID upon login
        session_regenerate_id(true);
        
        // Store user data in secure session
        $_SESSION['is_logged_in'] = true;
        $_SESSION['moon_tag'] = $api_response['body']['data']['moon_tag'] ?? 'Explorer';
        $_SESSION['rank'] = $api_response['body']['data']['rank'] ?? 'guest';
        $_SESSION['api_token'] = $api_response['body']['data']['token'] ?? null;

        // Route to Dashboard
        redirect(get_url('app', '/?module=dashboard&page=home&sec_bind=' . $_SESSION['sec_bind']));
        
    } else {
        // V3: Extract the exact error from the API for debugging (e.g., "Invalid Passcode")
        $error_msg = urlencode($api_response['body']['message'] ?? 'API HTTP Error ' . $api_response['status_code']);
        redirect(get_url('app', '/?module=auth&page=login&error=' . $error_msg));
    }
} else {
    // Bounce direct GET requests back to the login page safely
    redirect(get_url('app', '/?module=auth&page=login'));
}
?>