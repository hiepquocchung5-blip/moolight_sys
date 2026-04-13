<?php
/**
 * Moonlight Digital - Global Functions Engine
 * Core utilities loaded before anything else. Contains our custom .env parser.
 */

/**
 * Custom .env Parser without Composer.
 * Reads the .env file and loads variables into $_ENV and getenv().
 */
function load_env($filePath) {
    if (!file_exists($filePath)) {
        die("Critical System Failure: .env configuration missing.");
    }
    
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) continue;
        
        // Parse Key=Value pairs
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            
            // Set variables into PHP environment
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

/**
 * Output Sanitization Wrapper (Security)
 * Prevents XSS attacks when echoing user data to HTML.
 */
function esc($string) {
    if ($string === null) return '';
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * V1.1 - Professional URL Generator
 * Dynamically builds absolute URLs based on the target portal.
 */
function get_url($portal = 'main', $path = '') {
    $env_key = strtoupper($portal) . '_URL';
    $base = getenv($env_key);
    
    // Fallback defaults if .env is missing
    // if (!$base) {
    //     $bases = [
    //         'MAIN' => 'http://moonlightdigitalmarket.com',
    //         'APP' => 'http://app.moonlightdigitalmarket.com',
    //         'API' => 'http://api.moonlightdigitalmarket.com',
    //         'ADMIN' => 'http://admin.moonlightdigitalmarket.com',
    //         'PROOFPATH' => 'http://proofpath.moonlightdigitalmarket.com' // V1.2 Added Dedicated Image Server
    //     ];
    //     $base = $bases[strtoupper($portal)] ?? $bases['MAIN'];
    // }
    
    return rtrim($base, '/') . '/' . ltrim($path, '/');
}

/**
 * V1.1 - Professional Redirect Engine
 * Handles HTTP headers securely and safely terminates the script.
 */
function redirect($url, $status_code = 302) {
    header("Location: " . $url, true, $status_code);
    exit;
}

/**
 * Secure JWT Generation (Basic Implementation)
 */
function generate_jwt($payload, $secret) {
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($payload)));
    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
}
?>