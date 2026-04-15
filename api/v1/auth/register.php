<?php
/**
 * API Endpoint: /v1/auth/register (V3 Production)
 * Enterprise-grade registration handler with strict pre-flight validation.
 */
if (!isset($active_portal) || $active_portal !== 'api') {
    http_response_code(403); echo json_encode(["status" => "error", "message" => "Direct access forbidden. Pulse lost."]); exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); echo json_encode(["status" => "error", "message" => "Method not allowed. Use POST."]); exit;
}

// Safely parse input to prevent PHP 8 'Trying to access array offset on null' warnings
$input_data = (isset($input) && is_array($input)) ? $input : [];
$moon_tag = trim($input_data['moon_tag'] ?? '');
$email = trim($input_data['email'] ?? '');
$password = $input_data['password'] ?? '';

// ==========================================
// V3 Strict Payload Validation
// ==========================================
if (empty($moon_tag) || empty($email) || empty($password)) {
    http_response_code(400); echo json_encode(["status" => "error", "message" => "All fields (Moon Tag, Email, Passcode) are strictly required."]); exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400); echo json_encode(["status" => "error", "message" => "Invalid email format detected."]); exit;
}

if (strlen($password) < 8) {
    http_response_code(400); echo json_encode(["status" => "error", "message" => "Passcode fails security policy. Must be at least 8 characters."]); exit;
}

// Cryptographic Hashing
$pass_hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO md_citizens (moon_tag, email, pass_hash) VALUES (:tag, :email, :hash)");
    $stmt->execute(['tag' => $moon_tag, 'email' => $email, 'hash' => $pass_hash]);
    
    // 201 Created is the strictly correct HTTP code for successful insertion
    http_response_code(201); 
    echo json_encode(["status" => "success", "message" => "Entity initialized successfully in the Nexus."]);

} catch (\PDOException $e) {
    // 23000 is the SQLSTATE for Integrity Constraint Violation (Duplicate Key)
    if ($e->getCode() == 23000) {
        http_response_code(409); // 409 Conflict
        echo json_encode(["status" => "error", "message" => "Conflict: Moon Tag or Email is already registered within the system."]);
    } else {
        http_response_code(500); 
        echo json_encode(["status" => "error", "message" => "Internal Database Fault during registration."]);
    }
}
?>