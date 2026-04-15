<?php
/**
 * API Endpoint: /v1/auth/login (V3 Production)
 * Secure authentication handler with strict JWT token generation.
 */
if (!isset($active_portal) || $active_portal !== 'api') {
    http_response_code(403); echo json_encode(["status" => "error", "message" => "Direct access forbidden. Pulse lost."]); exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); echo json_encode(["status" => "error", "message" => "Method not allowed. Use POST."]); exit;
}

$identifier = trim($input['identifier'] ?? '');
$password = $input['password'] ?? '';

// V3 Strict Validation
if (empty($identifier) || empty($password)) {
    http_response_code(400); echo json_encode(["status" => "error", "message" => "Identity and Passcode are strictly required for authentication."]); exit;
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
            http_response_code(500); echo json_encode(["status" => "error", "message" => "System Fault: Cryptographic secret missing."]); exit;
        }

        // Generate JWT Payload
        $payload = [
            'cit_id' => $citizen['cit_id'],
            'moon_tag' => $citizen['moon_tag'],
            'rank' => $citizen['rank'],
            'exp' => time() + 86400 // 24-hour expiration token
        ];
        $token = generate_jwt($payload, $jwt_secret);
        
        http_response_code(200);
        echo json_encode([
            "status" => "success", 
            "message" => "Secure connection established.", 
            "data" => [
                "moon_tag" => $citizen['moon_tag'], 
                "rank" => $citizen['rank'],
                "token" => $token
            ]
        ]);
    } else {
        http_response_code(401); echo json_encode(["status" => "error", "message" => "Authentication rejected: Invalid identity or passcode."]);
    }
} catch (\PDOException $e) {
    http_response_code(500); echo json_encode(["status" => "error", "message" => "Internal Database Fault during authentication verification."]);
}
?>