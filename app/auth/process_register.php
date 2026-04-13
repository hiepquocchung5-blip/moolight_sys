<?php
/**
 * App Portal - Process Register (V1.3)
 * Securely matches the frontend register form to the API.
 * Protected by 32-char CSRF Bind Hash and Front Controller routing.
 */

// Enforce that this file can only be accessed through the App Router (app/index.php)
if (!isset($active_portal) || $active_portal !== 'app') {
    die("Pulse lost. Direct access forbidden.");
}

// Load the API engine so we can communicate with the backend
require_once __DIR__ . '/../../functions/api_engine.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and extract inputs
    $moon_tag = trim($_POST['moon_tag'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $request_bind = $_POST['sec_bind'] ?? '';

    // ==========================================
    // 1. STRICT CSRF VALIDATION
    // ==========================================
    // Check if the hidden input matches the secure session hash
    if (empty($_SESSION['sec_bind']) || !hash_equals($_SESSION['sec_bind'], $request_bind)) {
        $error = urlencode('Security violation: Invalid session bind.');
        redirect(get_url('app', '/?module=auth&page=register&error=' . $error));
    }

    // ==========================================
    // 2. PRE-FLIGHT VALIDATION
    // ==========================================
    if (empty($moon_tag) || empty($email) || empty($password)) {
        $error = urlencode('All fields are required to initialize an account.');
        redirect(get_url('app', '/?module=auth&page=register&error=' . $error));
    }

    if ($password !== $password_confirm) {
        $error = urlencode('Passcodes do not match.');
        redirect(get_url('app', '/?module=auth&page=register&error=' . $error));
    }
    
    if (strlen($password) < 8) {
        $error = urlencode('Passcode must be at least 8 characters for network security.');
        redirect(get_url('app', '/?module=auth&page=register&error=' . $error));
    }

    // ==========================================
    // 3. TRANSMIT TO SECURE API PORTAL
    // ==========================================
    $api_response = call_moonlight_api('/v1/auth/register', 'POST', [
        'moon_tag' => $moon_tag,
        'email' => $email,
        'password' => $password
    ]);

    // ==========================================
    // 4. HANDLE API RESPONSE & ROUTING
    // ==========================================
    // Accept both 201 (Created) and 200 (OK) as successful responses from the API
    if (
        ($api_response['status_code'] === 201 || $api_response['status_code'] === 200) && 
        isset($api_response['body']['status']) && 
        $api_response['body']['status'] === 'success'
    ) {
        // Registration Successful! Redirect to Login with success message.
        // Using the Front Controller path `/?module=auth...` to prevent direct file access errors.
        $success = urlencode("Account initialized. Please authenticate to enter the Nexus.");
        redirect(get_url('app', '/?module=auth&page=login&success=' . $success));
        
    } else {
        // Registration Failed! Redirect safely back to the register form.
        $error_msg = urlencode($api_response['body']['message'] ?? 'Registration Failed. Check API output.');
        redirect(get_url('app', '/?module=auth&page=register&error=' . $error_msg));
    }
} else {
    // If someone tries to GET this script directly, bounce them back to the form securely.
    redirect(get_url('app', '/?module=auth&page=register'));
}
?>