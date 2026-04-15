<?php
/**
 * Moonlight Digital - Core Database Connection
 * Establishes a secure PDO connection using parsed .env variables via $_ENV.
 */

// Ensure environment variables are loaded via the new $_ENV method
if (!isset($_ENV['DB_HOST'])) {
    header('Content-Type: application/json');
    http_response_code(500);
    // Returning structured JSON instead of plain text to prevent frontend crashes
    die(json_encode(["status" => "error", "message" => "Database Error: Environment variables not initialized."]));
}

$host = $_ENV['DB_HOST'];
$db   = $_ENV['DB_NAME'];
$user = $_ENV['DB_USER'];
$pass = $_ENV['DB_PASS'];
$port = $_ENV['DB_PORT'] ?? '887';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Always fetch as associative array
    PDO::ATTR_EMULATE_PREPARES   => false,            // Use native prepared statements (Security)
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // In production, we log this. We NEVER echo $e->getMessage() to the user.
    error_log("Database Connection Failed: " . $e->getMessage());
    
    header('Content-Type: application/json');
    http_response_code(500);
    die(json_encode(["status" => "error", "message" => "Critical Database Connectivity Failure. Pulse Lost."]));
}
?>