<?php
/**
 * App Portal - OAuth Redirector
 * Generates the secure Google OAuth URL and sends the user there.
 */
if (!isset($active_portal)) die("Pulse lost.");

$provider = $_GET['provider'] ?? '';

if ($provider === 'google') {
    $client_id = getenv('GOOGLE_CLIENT_ID');
    $redirect_uri = getenv('GOOGLE_REDIRECT_URI');
    
    // Pass our sec_bind as the 'state' variable to prevent CSRF during the OAuth flow
    $state = $_SESSION['sec_bind'];
    
    $google_oauth_url = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query([
        'client_id' => $client_id,
        'redirect_uri' => $redirect_uri,
        'response_type' => 'code',
        'scope' => 'email profile',
        'state' => $state,
        'prompt' => 'select_account'
    ]);
    
    redirect($google_oauth_url);

} elseif ($provider === 'telegram') {
    // Telegram uses a JS widget, so we redirect them to a dedicated page with the widget
    redirect(get_url('app', '/?module=auth&page=telegram_widget&sec_bind=' . $_SESSION['sec_bind']));
} else {
    redirect(get_url('app', '/?module=auth&page=login&error=Invalid Provider'));
}
?>