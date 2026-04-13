<?php
/**
 * PORTAL 5: API & Webhooks Engine (Standalone)
 * Document Root: /moonlight_root/api/
 */
$active_portal = 'api';

// 1. Set Security & JSON Headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // In production, restrict to your App URL
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); exit;
}

// 2. Boot Core Engine securely
require_once __DIR__ . '/../functions/global_functions.php';
load_env(__DIR__ . '/../.env');
require_once __DIR__ . '/../config/database.php';

// 3. Parse Routing
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$input = json_decode(file_get_contents('php://input'), true);

switch ($request_uri) {
    
    // ==========================================
    // AUTHENTICATION: LOGIN
    // ==========================================
    case '/v1/auth/login':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); echo json_encode(["status" => "error", "message" => "Method not allowed"]); exit;
        }

        $identifier = trim($input['identifier'] ?? '');
        $password = $input['password'] ?? '';

        if (empty($identifier) || empty($password)) {
            http_response_code(400); echo json_encode(["status" => "error", "message" => "Missing credentials."]); exit;
        }

        $stmt = $pdo->prepare("SELECT cit_id, moon_tag, pass_hash, rank FROM md_citizens WHERE email = :id OR moon_tag = :id LIMIT 1");
        $stmt->execute(['id' => $identifier]);
        $citizen = $stmt->fetch();

        if ($citizen && password_verify($password, $citizen['pass_hash'])) {
            $jwt_secret = getenv('JWT_SECRET');
            $payload = [
                'cit_id' => $citizen['cit_id'],
                'moon_tag' => $citizen['moon_tag'],
                'rank' => $citizen['rank'],
                'exp' => time() + 86400 // 24-hour expiration
            ];
            $token = generate_jwt($payload, $jwt_secret);
            
            http_response_code(200);
            echo json_encode([
                "status" => "success", 
                "message" => "Authentication successful", 
                "data" => ["moon_tag" => $citizen['moon_tag'], "token" => $token]
            ]);
        } else {
            http_response_code(401); echo json_encode(["status" => "error", "message" => "Invalid identity or passcode."]);
        }
        break;

    // ==========================================
    // AUTHENTICATION: REGISTER
    // ==========================================
    case '/v1/auth/register':
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
        break;

    // ==========================================
    // AUTHENTICATION: SOCIAL (Google/Telegram)
    // ==========================================
    case '/v1/auth/social':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); echo json_encode(["status" => "error", "message" => "Method not allowed"]); exit;
        }

        $provider = $input['provider'] ?? '';
        $uid = $input['uid'] ?? '';
        $email = $input['email'] ?? '';
        $suggested_tag = $input['suggested_tag'] ?? '';

        if (!$uid || !$email || !in_array($provider, ['google', 'telegram'])) {
            http_response_code(400); echo json_encode(["status" => "error", "message" => "Invalid social payload."]); exit;
        }

        $column = ($provider === 'google') ? 'auth_google_uid' : 'auth_tg_uid';

        $stmt = $pdo->prepare("SELECT cit_id, moon_tag, rank, $column FROM md_citizens WHERE {$column} = :uid OR email = :email LIMIT 1");
        $stmt->execute(['uid' => $uid, 'email' => $email]);
        $citizen = $stmt->fetch();

        try {
            if (!$citizen) {
                // User does not exist. Auto-register them!
                $random_pass = password_hash(bin2hex(random_bytes(20)), PASSWORD_DEFAULT);
                
                $insert = $pdo->prepare("INSERT INTO md_citizens (moon_tag, email, pass_hash, {$column}) VALUES (:tag, :email, :hash, :uid)");
                $insert->execute([
                    'tag' => $suggested_tag,
                    'email' => $email,
                    'hash' => $random_pass,
                    'uid' => $uid
                ]);
                
                $cit_id = $pdo->lastInsertId();
                $moon_tag = $suggested_tag;
                $rank = 'guest';
            } else {
                // User exists. Link account if the UID column is currently empty
                if (empty($citizen[$column])) {
                    $update = $pdo->prepare("UPDATE md_citizens SET {$column} = :uid WHERE cit_id = :id");
                    $update->execute(['uid' => $uid, 'id' => $citizen['cit_id']]);
                }
                
                $cit_id = $citizen['cit_id'];
                $moon_tag = $citizen['moon_tag'];
                $rank = $citizen['rank'];
            }

            // Issue JWT Token
            $jwt_secret = getenv('JWT_SECRET');
            $token = generate_jwt(['cit_id' => $cit_id, 'moon_tag' => $moon_tag, 'rank' => $rank, 'exp' => time() + 86400], $jwt_secret);
            
            http_response_code(200);
            echo json_encode([
                "status" => "success", 
                "message" => "Social connection established.", 
                "data" => ["moon_tag" => $moon_tag, "token" => $token]
            ]);

        } catch (\PDOException $e) {
            http_response_code(500); echo json_encode(["status" => "error", "message" => "Database failure during social sync."]);
        }
        break;

    // ==========================================
    // USER DATA: PROFILE (Strict JWT Validation)
    // ==========================================
    case '/v1/user/profile':
        if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); echo json_encode(["status" => "error", "message" => "Method not allowed"]); exit;
        }

        // 1. Extract Authorization Header
        $headers = apache_request_headers();
        $auth_header = $headers['Authorization'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if (!preg_match('/Bearer\s(\S+)/', $auth_header, $matches)) {
            http_response_code(401); echo json_encode(["status" => "error", "message" => "Missing or invalid Authorization Bearer token."]); exit;
        }

        $token = $matches[1];
        $jwt_secret = getenv('JWT_SECRET');

        // 2. Cryptographically Verify JWT
        $token_parts = explode('.', $token);
        if (count($token_parts) !== 3) {
            http_response_code(401); echo json_encode(["status" => "error", "message" => "Malformed token structure."]); exit;
        }

        $signature = hash_hmac('sha256', $token_parts[0] . "." . $token_parts[1], $jwt_secret, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        if (!hash_equals($base64UrlSignature, $token_parts[2])) {
            http_response_code(401); echo json_encode(["status" => "error", "message" => "Invalid token signature. Access denied."]); exit;
        }

        // 3. Decode Payload & Check Expiration
        $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $token_parts[1])), true);
        
        if (!isset($payload['exp']) || $payload['exp'] < time()) {
            http_response_code(401); echo json_encode(["status" => "error", "message" => "Token has expired. Please re-authenticate."]); exit;
        }

        // 4. Fetch Secure Profile Data
        $stmt = $pdo->prepare("SELECT cit_id, moon_tag, email, auth_google_uid, auth_tg_uid, rank, created_at FROM md_citizens WHERE cit_id = :id LIMIT 1");
        $stmt->execute(['id' => $payload['cit_id']]);
        $user = $stmt->fetch();

        if ($user) {
            // Mask sensitive data if needed, but we already excluded pass_hash in the SELECT query
            http_response_code(200);
            echo json_encode(["status" => "success", "data" => $user]);
        } else {
            http_response_code(404); echo json_encode(["status" => "error", "message" => "Citizen record not found."]);
        }
        break;

    // ==========================================
    // 404 NOT FOUND
    // ==========================================
    default:
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "API Endpoint [{$request_uri}] not found in the Moonlight Network."]);
        break;
}
?>