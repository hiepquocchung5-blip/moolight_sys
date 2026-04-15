<?php
/**
 * API Endpoint: /v1/auth/login (V3 Production)
 * Secure authentication handler with strict JWT generation and Buffer Wiping.
 */

// Force PHP to hide HTML errors so they don't corrupt our JSON response
ini_set('display_errors', 0);

// Helper function to aggressively wipe any accidental whitespace before sending JSON
function send_clean_json($status_code, $response_array) {
    if (ob_get_length()) ob_clean(); 
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

$input_data = (isset($input) && is_array($input)) ? $input : [];
$identifier = trim($input_data['identifier'] ?? '');
$password = $input_data['password'] ?? '';

// V3 Strict Payload Validation
if (empty($identifier) || empty($password)) {
    send_clean_json(400, ["status" => "error", "message" => "Identity and Passcode are strictly required for authentication."]);
}

try {
    // FIX: Use strictly distinct parameter names (:email_id and :tag_id) for native PDO prepared statements
    $stmt = $pdo->prepare("SELECT cit_id, moon_tag, pass_hash, rank FROM md_citizens WHERE email = :email_id OR moon_tag = :tag_id LIMIT 1");
    
    // FIX: Bind the identifier to BOTH distinct parameters
    $stmt->execute([
        'email_id' => $identifier,
        'tag_id'   => $identifier
    ]);
    
    $citizen = $stmt->fetch();

    // Verify Password against hash
    if ($citizen && password_verify($password, $citizen['pass_hash'])) {
        
        $jwt_secret = getenv('JWT_SECRET') ?: ($_ENV['JWT_SECRET'] ?? null);
        
        if (!$jwt_secret) {
            error_log("Critical Error: JWT_SECRET missing in environment config.");
            send_clean_json(500, ["status" => "error", "message" => "System Fault: Cryptographic secret missing."]);
        }

        $payload = [
            'cit_id' => $citizen['cit_id'],
            'moon_tag' => $citizen['moon_tag'],
            'rank' => $citizen['rank'],
            'exp' => time() + 86400 
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
        send_clean_json(401, ["status" => "error", "message" => "Authentication rejected: Invalid identity or passcode."]);
    }
} catch (\PDOException $e) {
    error_log("Database Fault in API Login: " . $e->getMessage());
    send_clean_json(500, ["status" => "error", "message" => "Internal Database Fault during authentication verification."]);
}