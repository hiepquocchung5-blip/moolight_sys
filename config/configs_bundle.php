<?php
/**
 * NOTE: Create these as separate files in your /config folder.
 * They are bundled here for easy reading.
 */

// --- 1. config/landing_config.php ---
$landing_config = [
    'seo_title' => 'Moonlight Digital Market | Premium Accounts & AI',
    'default_lang' => 'en-GB',
    'theme' => 'dark_aurora',
    'google_translate_enabled' => true
];

// --- 2. config/app_config.php ---
$app_config = [
    'session_timeout' => 3600, // 1 hour
    'checkout_timer_seconds' => 600, // 10 minutes (Quantum Checkout)
    'cookie_domain' => '.moonlightdigitalmarket.com', // Share sessions across subdomains
    'allowed_payment_gateways' => ['KBZPay', 'AYAPay', 'UABPay', 'PayPal', 'Visa', 'Mastercard']
];

// --- 3. config/admin_config.php ---
$admin_config = [
    'security_level' => 'strict',
    'allowed_admin_ips' => ['127.0.0.1', '192.168.1.100'], // IP Whitelisting for MoonAdmin
    'dynamic_theme' => 'auto', // Shifts to 'overdrive' on high sales
    'bank_accounts' => [
        'KBZPay' => ['name' => 'Moonlight Corp', 'number' => '09912345678'],
        'AYAPay' => ['name' => 'Moonlight Corp', 'number' => '09987654321']
    ]
];

// --- 4. config/api_config.php ---
$api_config = [
    'rate_limit_requests' => 60, // Per minute
    'enable_telegram_webhooks' => true,
    'jwt_algo' => 'HS256'
];
?>