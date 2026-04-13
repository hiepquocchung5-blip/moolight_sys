<?php
/**
 * Moonlight Digital - API Configuration
 * Defines Base URLs, JWT Secrets, and rate limiting for Portal 5.
 */

// In production, these should be loaded from your .env file via global_functions.php
$api_config = [
    'api_base_url' => getenv('API_URL') ?: 'http://localhost:8888',
    'jwt_secret'   => getenv('JWT_SECRET') ?: 'Moonlight_Cyber_Secret_99821',
    'jwt_expiry'   => 86400, // 24 hours in seconds
    'rate_limit'   => 60 // Max requests per minute
];
?>