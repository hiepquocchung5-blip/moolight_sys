<?php
/**
 * App Portal - Spark View UI
 */
if (!isset($active_portal) || $active_portal !== 'app') die("Pulse lost.");
require_once __DIR__ . '/../../functions/currency_engine.php';

$spark_id = $_GET['id'] ?? null;
if (!$spark_id) redirect(get_url('app', '/?module=dashboard&page=home'));

$token = $_SESSION['api_token'] ?? '';
$api_res = call_moonlight_api('/v1/shop/view_spark?id=' . urlencode($spark_id), 'GET', [], $token);

if ($api_res['status_code'] !== 200) {
    redirect(get_url('app', '/?module=dashboard&page=home&error=' . urlencode("Spark offline.")));
}

$spark = $api_res['body']['data'];
$color = $spark['type'] === 'image' ? 'purple' : 'blue';

$current_currency = $_SESSION['currency'] ?? 'MMK';
$exchange_rates = get_exchange_rates($pdo);
$formatted_price = $spark['is_free'] ? 'FREE DATA' : format_price(convert_price($spark['price_mmk'], $current_currency, $exchange_rates), $current_currency);

require_once __DIR__ . '/../includes/app_header.php';
?>

<div class="max-w-4xl mx-auto">
    <div class="glass-panel p-8 md:p-12 rounded-3xl relative overflow-hidden border-t-4 border-t-<?= $color ?>-500">
        
        <div class="flex items-center gap-4 mb-8">
            <div class="w-16 h-16 rounded-2xl bg-<?= $color ?>-500/20 text-<?= $color ?>-400 flex items-center justify-center text-3xl">
                <i class="ph-fill ph-sparkle"></i>
            </div>
            <div>
                <span class="text-xs font-bold text-<?= $color ?>-400 uppercase tracking-widest mb-1 block">Neural Spark: <?= str_replace('_', ' ', esc($spark['type'])) ?></span>
                <h1 class="text-3xl md:text-4xl font-extrabold text-white tracking-tight"><?= esc($spark['title']) ?></h1>
            </div>
        </div>

        <div class="bg-gray-900/80 border border-gray-800 rounded-2xl p-6 mb-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gray-800/50 rounded-bl-full pointer-events-none blur-xl"></div>
            <p class="text-[10px] uppercase tracking-widest font-bold text-gray-500 mb-3"><i class="ph-fill ph-lock-key text-gray-400"></i> Encrypted Payload Preview</p>
            <p class="text-white font-mono text-sm leading-relaxed tracking-wider opacity-80 select-none">
                <?= esc($spark['content']) ?>
            </p>
        </div>

        <div class="flex flex-col sm:flex-row justify-between items-center gap-6">
            <div class="w-full sm:w-auto">
                <p class="text-[10px] uppercase tracking-widest font-bold text-gray-500 mb-1">Transfer Protocol</p>
                <p class="text-3xl font-black text-white"><?= $formatted_price ?></p>
            </div>
            <a href="<?= get_url('app', '/?module=shop&page=checkout_spark&spark_id=' . $spark['spark_id'] . '&sec_bind=' . esc($system_bind)) ?>" class="w-full sm:w-auto bg-gray-800 hover:bg-<?= $color ?>-600 border border-gray-700 hover:border-<?= $color ?>-500 text-white font-bold py-4 px-10 rounded-xl transition-all flex justify-center items-center gap-3 group text-lg">
                <i class="ph-bold ph-download-simple"></i> Request Decryption Key
            </a>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/app_footer.php'; ?>