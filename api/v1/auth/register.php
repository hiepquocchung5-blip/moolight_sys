<?php
/**
 * API Endpoint: /v1/auth/register (V3 Production)
 * Enterprise-grade registration handler with strict pre-flight validation.
 */

// Force PHP to hide HTML errors so they don't corrupt our JSON response
ini_set('display_errors', 0);

// Helper function to aggressively wipe any accidental whitespace/warnings before sending JSON
function send_clean_json($status_code, $response_array) {
    if (ob_get_length()) ob_clean(); // Wipe the output buffer clean
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
$moon_tag = trim($input_data['moon_tag'] ?? '');
$email = trim($input_data['email'] ?? '');
$password = $input_data['password'] ?? '';

// V3 Strict Payload Validation
if (empty($moon_tag) || empty($email) || empty($password)) {
    send_clean_json(400, ["status" => "error", "message" => "All fields (Moon Tag, Email, Passcode) are strictly required."]);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    send_clean_json(400, ["status" => "error", "message" => "Invalid email format detected."]);
}

if (strlen($password) < 8) {
    send_clean_json(400, ["status" => "error", "message" => "Passcode fails security policy. Must be at least 8 characters."]);
}

$pass_hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO md_citizens (moon_tag, email, pass_hash) VALUES (:tag, :email, :hash)");
    $stmt->execute(['tag' => $moon_tag, 'email' => $email, 'hash' => $pass_hash]);
    
    send_clean_json(201, ["status" => "success", "message" => "Entity initialized successfully in the Nexus."]);

} catch (\PDOException $e) {
    if ($e->getCode() == 23000) {
        send_clean_json(409, ["status" => "error", "message" => "Conflict: Moon Tag or Email is already registered within the system."]);
    } else {
        error_log("Database Fault in API Register: " . $e->getMessage());
        send_clean_json(500, ["status" => "error", "message" => "Internal Database Fault during registration."]);
    }
}