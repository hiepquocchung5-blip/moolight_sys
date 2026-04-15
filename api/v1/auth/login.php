<?php
/**
 * API Endpoint: /v1/auth/login (V3 Production)
 * Secure authentication handler with strict JWT generation and Buffer Wiping.
 */

// 1. Force PHP to hide HTML errors so they don't corrupt our JSON response
ini_set('display_errors', 0);

// 2. Helper function to aggressively wipe any accidental whitespace before sending JSON
function send_clean_json($status_code, $response_array) {
    if (ob_get_length()) ob_clean(); // Destroy any hidden whitespace or PHP warnings
    http_response_code($status_code);
    echo json_encode($response_array);
    exit;
}

if (!isset($active_portal) || $active_portal !== 'api') {
    send_clean_json(403, ["status" => "error", "message" => "Direct access forbidden. Pulse lost."]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_clean_json(405, ["status" => "error", "message" => "Method not allowed. Use POST."]);
}

// Safely parse input to prevent PHP 8 'Trying to access array offset on null' warnings
$input_data = (isset($input) && is_array($input)) ? $input : [];
$identifier = trim($input_data['identifier'] ?? '');
$password = $input_data['password'] ?? '';

// ==========================================
// V3 Strict Payload Validation
// ==========================================
if (empty($identifier) || empty($password)) {
    send_clean_json(400, ["status" => "error", "message" => "Identity and Passcode are strictly required for authentication."]);
}

try {
    // Check database for matching email or moon_tag
    $stmt = $pdo->prepare("SELECT cit_id, moon_tag, pass_hash, rank FROM md_citizens WHERE email = :id OR moon_tag = :id LIMIT 1");
    $stmt->execute(['id' => $identifier]);
    $citizen = $stmt->fetch();

    // Verify Password against hash
    if ($citizen && password_verify($password, $citizen['pass_hash'])) {
        
        // Ensure we can retrieve the JWT Secret (Supports both putenv and $_ENV configurations)
        $jwt_secret = getenv('JWT_SECRET') ?: ($_ENV['JWT_SECRET'] ?? null);
        
        if (!$jwt_secret) {
            error_log("Critical Error: JWT_SECRET missing in environment config.");
            send_clean_json(500, ["status" => "error", "message" => "System Fault: Cryptographic secret missing."]);
        }

        // Generate JWT Payload
        $payload = [
            'cit_id' => $citizen['cit_id'],
            'moon_tag' => $citizen['moon_tag'],
            'rank' => $citizen['rank'],
            'exp' => time() + 86400 // 24-hour expiration token
        ];
        $token = generate_jwt($payload, $jwt_secret);
        
        send_clean_json(200, [
            "status" => "success", 
            "message" => "Secure connection established.", 
            "data" => [
                "moon_tag" => $citizen['moon_tag'], 
                "rank" => $citizen['rank'],
                "token" => $token
            ]
        ]);
    } else {
        // We use 401 Unauthorized for incorrect passwords
        send_clean_json(401, ["status" => "error", "message" => "Authentication rejected: Invalid identity or passcode."]);
    }
} catch (\PDOException $e) {
    // Log the exact database error silently for the admin to read in the server error.log
    error_log("Database Fault in API Login: " . $e->getMessage());
    send_clean_json(500, ["status" => "error", "message" => "Internal Database Fault during authentication verification."]);
}