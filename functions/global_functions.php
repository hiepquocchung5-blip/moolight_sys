<?php
/**
 * Moonlight Digital - Global Functions Engine
 * Core utilities loaded before anything else. Contains our custom .env parser.
 */

/**
 * Custom .env Parser without Composer.
 * Production Safe: Omits putenv() as it is frequently disabled on live servers.
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
            
            // Set variables into PHP environment securely without putenv()
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

/**
 * Output Sanitization Wrapper (Security)
 */
function esc($string) {
    if ($string === null) return '';
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * V1.1 - Professional URL Generator
 * Uses $_ENV instead of getenv() to support servers with disabled putenv
 */
function get_url($portal = 'main', $path = '') {
    $env_key = strtoupper($portal) . '_URL';
    $base = $_ENV[$env_key] ?? null;
    
    // Fallback defaults if .env is missing
    if (!$base) {
        $bases = [
            'MAIN' => 'http://moonlightdigitalmarket.com',
            'APP' => 'http://app.moonlightdigitalmarket.com',
            'API' => 'http://api.moonlightdigitalmarket.com',
            'ADMIN' => 'http://admin.moonlightdigitalmarket.com',
            'PROOFPATH' => 'http://proofpath.moonlightdigitalmarket.com'
        ];
        $base = $bases[strtoupper($portal)] ?? $bases['MAIN'];
    }
    
    return rtrim($base, '/') . '/' . ltrim($path, '/');
}

/**
 * V1.1 - Professional Redirect Engine
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