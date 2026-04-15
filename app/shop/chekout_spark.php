<?php
/**
 * App Portal - Spark Checkout UI
 */
if (!isset($active_portal) || $active_portal !== 'app') die("Pulse lost.");
require_once __DIR__ . '/../../functions/currency_engine.php';

$spark_id = $_GET['spark_id'] ?? null;
if (!$spark_id) redirect(get_url('app', '/?module=dashboard&page=home'));

$token = $_SESSION['api_token'] ?? '';
$api_res = call_moonlight_api('/v1/shop/view_spark?id=' . urlencode($spark_id), 'GET', [], $token);
if ($api_res['status_code'] !== 200) redirect(get_url('app', '/?module=dashboard&page=home'));

$spark = $api_res['body']['data'];
$current_currency = $_SESSION['currency'] ?? 'MMK';
$exchange_rates = get_exchange_rates($pdo);
$formatted_price = $spark['is_free'] ? 'FREE' : format_price(convert_price($spark['price_mmk'], $current_currency, $exchange_rates), $current_currency);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acquire Data | MoonAccount</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { background-color: #030712; color: #E2E8F0; } .glass-panel { background: rgba(17, 24, 39, 0.6); backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.05); } .glass-input { background: rgba(0, 0, 0, 0.3); border: 1px solid rgba(255, 255, 255, 0.1); transition: all 0.3s ease; } .glass-input:focus { border-color: #A855F7; outline: none; }</style>
</head>
<body class="flex items-center justify-center min-h-screen relative overflow-hidden py-10">
    <div class="absolute w-[800px] h-[800px] bg-purple-600/10 rounded-full blur-[120px] -z-10 animate-pulse"></div>

    <div class="glass-panel p-8 rounded-3xl w-full max-w-lg mx-4 border-t-4 border-t-purple-500">
        <div class="bg-gray-900/50 rounded-xl p-5 mb-6 border border-gray-800/50 flex justify-between items-start">
            <div>
                <h3 class="text-white font-bold text-lg"><?= esc($spark['title']) ?></h3>
                <span class="text-xs text-purple-400 uppercase tracking-widest font-bold">Neural Spark Data</span>
            </div>
            <span class="text-2xl font-bold text-white"><?= $formatted_price ?></span>
        </div>

        <form action="<?= get_url('app', '/?module=shop&page=process_checkout') ?>" method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="sec_bind" value="<?= esc($system_bind) ?>">
            <input type="hidden" name="spark_id" value="<?= esc($spark['spark_id']) ?>">
            
            <?php if (!$spark['is_free']): ?>
                <p class="text-xs text-gray-400 uppercase tracking-wider font-bold mb-1">Verify Transaction</p>
                <input type="text" name="txn_last_6" placeholder="Last 6 Digits of TXN ID" required maxlength="6" pattern="\d{6}" class="glass-input w-full rounded-xl px-4 py-3 text-white font-mono tracking-wider">
                <input type="file" name="proof_image" accept="image/png, image/jpeg" required class="w-full glass-input rounded-xl px-4 py-3 text-gray-400 file:bg-purple-600 file:text-white file:border-0 file:rounded-lg file:px-4 file:py-2 file:font-bold file:text-xs file:mr-4">
            <?php endif; ?>

            <button type="submit" class="w-full bg-purple-600 hover:bg-purple-500 text-white font-bold py-4 rounded-xl transition-all shadow-[0_0_20px_rgba(168,85,247,0.3)] mt-4">
                <?= $spark['is_free'] ? 'Claim Free Data' : 'Submit TXN Proof' ?>
            </button>
        </form>
    </div>
</body>
</html>