<?php
/**
 * API Endpoint: /v1/auth/social
 * Handles automated registration/login for Google & Telegram OAuth flows.
 */
if (!isset($active_portal) || $active_portal !== 'api') {
    http_response_code(403); echo json_encode(["status" => "error", "message" => "Direct access forbidden."]); exit;
}

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
?>