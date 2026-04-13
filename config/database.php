<?php
/**
 * Moonlight Shared Database Connection
 * Requires global_functions.php to have loaded the .env file.
 */

try {
    $host = $_ENV['DB_HOST'];
    $db   = $_ENV['DB_NAME'];
    $user = $_ENV['DB_USER'];
    $pass = $_ENV['DB_PASS'];
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    // The Global $pdo object
    $pdo = new PDO($dsn, $user, $pass, $options);

} catch (\PDOException $e) {
    // In production, log this instead of displaying
    die("Database Connection Failed: Pulse Lost.");
}
?>