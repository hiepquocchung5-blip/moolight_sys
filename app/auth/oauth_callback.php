<?php
/**
 * App Portal - OAuth Callback Processor (No Composer)
 * Catches the Google Code, exchanges it for an Access Token, fetches user info, and calls our internal API.
 */
if (!isset($active_portal)) die("Pulse lost.");
require_once __DIR__ . '/../../functions/api_engine.php';

$provider = $_GET['provider'] ?? '';

if ($provider === 'google') {
    $code = $_GET['code'] ?? null;
    $state = $_GET['state'] ?? null;

    // 1. Validate CSRF State
    if (!$code || $state !== $_SESSION['sec_bind']) {
        redirect(get_url('app', '/?module=auth&page=login&error=Security State Mismatch'));
    }

    // 2. Exchange Auth Code for Access Token via cURL
    $token_url = "https://oauth2.googleapis.com/token";
    $token_post = [
        'client_id' => getenv('GOOGLE_CLIENT_ID'),
        'client_secret' => getenv('GOOGLE_CLIENT_SECRET'),
        'redirect_uri' => getenv('GOOGLE_REDIRECT_URI'),
        'grant_type' => 'authorization_code',
        'code' => $code
    ];

    $ch = curl_init($token_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($token_post));
    $token_response = json_decode(curl_exec($ch), true);
    curl_close($ch);

    if (!isset($token_response['access_token'])) {
        redirect(get_url('app', '/?module=auth&page=login&error=Failed to acquire Google Token'));
    }

    // 3. Fetch User Profile Info using the Access Token
    $profile_url = "https://www.googleapis.com/oauth2/v2/userinfo";
    $ch = curl_init($profile_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer " . $token_response['access_token']]);
    $profile = json_decode(curl_exec($ch), true);
    curl_close($ch);

    // 4. Send the verified Google Data to our Internal API
    if (isset($profile['id']) && isset($profile['email'])) {
        $api_payload = [
            'provider' => 'google',
            'uid' => $profile['id'],
            'email' => $profile['email'],
            // Generate a default MoonTag (e.g., Moon_123456)
            'suggested_tag' => 'Moon_' . substr(preg_replace("/[^0-9]/", "", md5($profile['id'])), 0, 6)
        ];

        $api_response = call_moonlight_api('/v1/auth/social', 'POST', $api_payload);

        if ($api_response['status_code'] === 200 && $api_response['body']['status'] === 'success') {
            $_SESSION['is_logged_in'] = true;
            $_SESSION['moon_tag'] = $api_response['body']['data']['moon_tag'];
            $_SESSION['api_token'] = $api_response['body']['data']['token'];
            redirect(get_url('app', '/?module=dashboard&page=home&sec_bind=' . $_SESSION['sec_bind']));
        } else {
            $err = urlencode($api_response['body']['message'] ?? 'Social Auth Failed');
            redirect(get_url('app', '/?module=auth&page=login&error=' . $err));
        }
    }
}
?>