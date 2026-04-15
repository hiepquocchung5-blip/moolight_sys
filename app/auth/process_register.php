<?php
/**
 * App Portal - Process Register (V3)
 * Includes strict pre-flight validation before hitting the API.
 */
if (!isset($active_portal) || $active_portal !== 'app') die("Pulse lost.");
require_once __DIR__ . '/../../functions/api_engine.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $moon_tag = trim($_POST['moon_tag'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $request_bind = $_POST['sec_bind'] ?? '';

    // 1. Strict CSRF Validation
    if (empty($_SESSION['sec_bind']) || !hash_equals($_SESSION['sec_bind'], $request_bind)) {
        redirect(get_url('app', '/?module=auth&page=register&error=' . urlencode('Security violation: Session mismatch.')));
    }

    // 2. Pre-Flight Validation (Saves API calls and provides instant feedback)
    if (empty($moon_tag) || empty($email) || empty($password)) {
        redirect(get_url('app', '/?module=auth&page=register&error=' . urlencode('All fields are required to initialize an account.')));
    }

    if ($password !== $password_confirm) {
        redirect(get_url('app', '/?module=auth&page=register&error=' . urlencode('Passcodes do not match.')));
    }
    
    if (strlen($password) < 8) {
        redirect(get_url('app', '/?module=auth&page=register&error=' . urlencode('Passcode must be at least 8 characters.')));
    }

    // 3. Transmit to API
    $api_response = call_moonlight_api('/v1/auth/register', 'POST', [
        'moon_tag' => $moon_tag,
        'email' => $email,
        'password' => $password
    ]);

    // 4. Exact Error Handling
    if (($api_response['status_code'] === 201 || $api_response['status_code'] === 200) && isset($api_response['body']['status']) && $api_response['body']['status'] === 'success') {
        $success = urlencode("Entity initialized. Please authenticate.");
        redirect(get_url('app', '/?module=auth&page=login&success=' . $success));
    } else {
        // Extract the exact error from the API for debugging
        $error_msg = urlencode($api_response['body']['message'] ?? 'API HTTP Error ' . $api_response['status_code']);
        redirect(get_url('app', '/?module=auth&page=register&error=' . $error_msg));
    }
} else {
    redirect(get_url('app', '/?module=auth&page=register'));
}
?>