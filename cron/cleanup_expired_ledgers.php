<?php
/**
 * Moonlight Digital - Quantum Cleanup Engine
 * Designed to be run via Linux Crontab every minute:
 * * * * * * php /path/to/moonlight/cron/cleanup_expired_ledgers.php
 */

require_once __DIR__ . '/../functions/global_functions.php';
load_env(__DIR__ . '/../.env');
require_once __DIR__ . '/../config/database.php';

try {
    // 1. Find all pending ledgers where the checkout_expires_at timestamp has passed
    $stmt = $pdo->prepare("UPDATE md_treasury_ledgers 
                           SET status = 'expired' 
                           WHERE status = 'pending' 
                           AND checkout_expires_at < NOW()");
    $stmt->execute();
    $affected = $stmt->rowCount();

    // 2. (Optional) In the future, you can add logic here to return `md_vault_keys` 
    // back to the global inventory if an order expires.

    echo "[" . date('Y-m-d H:i:s') . "] Quantum Cleanup executed. Expired {$affected} ledgers.\n";

} catch (Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] Error during cleanup: " . $e->getMessage() . "\n";
}
?>