<?php
/**
 * API Endpoint: /v1/auth/register
 * Handles creation of new Moon Accounts.
 */
if (!isset($active_portal) || $active_portal !== 'api') {
    http_response_code(403); echo json_encode(["status" => "error", "message" => "Direct access forbidden."]); exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); echo json_encode(["status" => "error", "message" => "Method not allowed"]); exit;
}

$moon_tag = trim($input['moon_tag'] ?? '');
$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';

if (empty($moon_tag) || empty($email) || empty($password)) {
    http_response_code(400); echo json_encode(["status" => "error", "message" => "Missing required fields."]); exit;
}

$pass_hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO md_citizens (moon_tag, email, pass_hash) VALUES (:tag, :email, :hash)");
    $stmt->execute(['tag' => $moon_tag, 'email' => $email, 'hash' => $pass_hash]);
    
    http_response_code(201);
    echo json_encode(["status" => "success", "message" => "Moon Account established. Welcome to the Nexus."]);
} catch (\PDOException $e) {
    // 23000 is the SQLSTATE for Integrity Constraint Violation (Duplicate Key)
    if ($e->getCode() == 23000) {
        http_response_code(409); echo json_encode(["status" => "error", "message" => "Moon Tag or Email is already registered."]);
    } else {
        http_response_code(500); echo json_encode(["status" => "error", "message" => "System error during registration."]);
    }
}
?>