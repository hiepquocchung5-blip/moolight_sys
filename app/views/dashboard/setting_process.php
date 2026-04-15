<?php
/**
 * App Portal - Process Entity Settings
 */
if (!isset($active_portal) || $active_portal !== 'app') die("Pulse lost.");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_pass = $_POST['current_password'] ?? '';
    $new_pass = $_POST['new_password'] ?? '';
    $confirm_pass = $_POST['confirm_password'] ?? '';

    if (empty($current_pass) || empty($new_pass) || empty($confirm_pass)) {
        redirect(get_url('app', '/?module=dashboard&page=settings&error=' . urlencode('All security fields are required.')));
    }

    if ($new_pass !== $confirm_pass) {
        redirect(get_url('app', '/?module=dashboard&page=settings&error=' . urlencode('New passcodes do not match.')));
    }

    if (strlen($new_pass) < 8) {
        redirect(get_url('app', '/?module=dashboard&page=settings&error=' . urlencode('New passcode must be at least 8 characters.')));
    }

    // Transmit to API with JWT
    $token = $_SESSION['api_token'] ?? '';
    $api_response = call_moonlight_api('/v1/user/settings', 'POST', [
        'current_password' => $current_pass,
        'new_password' => $new_pass
    ], $token);

    if ($api_response['status_code'] === 200) {
        redirect(get_url('app', '/?module=dashboard&page=settings&success=' . urlencode('Security credentials successfully updated.')));
    } else {
        $err = urlencode($api_response['body']['message'] ?? 'Failed to update credentials.');
        redirect(get_url('app', '/?module=dashboard&page=settings&error=' . $err));
    }
} else {
    redirect(get_url('app', '/?module=dashboard&page=settings'));
}
?>