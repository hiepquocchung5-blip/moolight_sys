<?php
/**
 * App Portal - Artifact View UI
 */
if (!isset($active_portal) || $active_portal !== 'app') die("Pulse lost.");
require_once __DIR__ . '/../../functions/currency_engine.php';

$art_id = $_GET['id'] ?? null;
if (!$art_id) redirect(get_url('app', '/?module=dashboard&page=home'));

// Fetch Details from API
$token = $_SESSION['api_token'] ?? '';
$api_res = call_moonlight_api('/v1/shop/view_artifact?id=' . urlencode($art_id), 'GET', [], $token);

if ($api_res['status_code'] !== 200) {
    redirect(get_url('app', '/?module=dashboard&page=home&error=' . urlencode("Artifact offline.")));
}

$artifact = $api_res['body']['data'];

// Formatting
$current_currency = $_SESSION['currency'] ?? 'MMK';
$exchange_rates = get_exchange_rates($pdo);
$price_mmk = $artifact['base_price_mmk'];
$formatted_price = format_price(convert_price($price_mmk, $current_currency, $exchange_rates), $current_currency);

// Styling Rules
$color = $artifact['delivery_type'] === 'singularity' ? 'purple' : ($artifact['delivery_type'] === 'infinity' ? 'green' : 'blue');
$icon = $artifact['category'] === 'gaming' ? 'ph-game-controller' : ($artifact['category'] === 'software' ? 'ph-code-block' : 'ph-robot');

require_once __DIR__ . '/../includes/app_header.php';
?>

<div class="max-w-5xl mx-auto">
    <div class="glass-panel p-8 md:p-12 rounded-3xl relative overflow-hidden">
        <div class="absolute -right-20 -top-20 w-96 h-96 bg-<?= $color ?>-600/10 rounded-full blur-[100px] pointer-events-none"></div>

        <div class="flex flex-col md:flex-row gap-12 relative z-10">
            <div class="w-full md:w-1/3 shrink-0 flex flex-col items-center justify-center bg-gray-900/50 border border-gray-800 rounded-3xl p-10 aspect-square">
                <i class="ph-fill <?= $icon ?> text-9xl text-<?= $color ?>-500 mb-6 drop-shadow-[0_0_30px_rgba(var(--tw-colors-<?= $color ?>-500),0.4)]"></i>
                <span class="bg-<?= $color ?>-500/20 text-<?= $color ?>-400 border border-<?= $color ?>-500/50 text-xs uppercase tracking-widest px-4 py-2 rounded-lg font-bold">
                    <?= esc($artifact['delivery_type']) ?> Class
                </span>
            </div>

            <div class="w-full md:w-2/3 flex flex-col justify-center">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-2 border-b border-gray-800 inline-block pb-2 w-max">
                    Category: <?= esc($artifact['category']) ?>
                </span>
                <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-6 tracking-tight"><?= esc($artifact['title']) ?></h1>
                
                <p class="text-lg text-gray-400 mb-8 leading-relaxed">
                    <?= esc($artifact['description']) ?>
                </p>

                <div class="bg-gray-900/50 border border-gray-800 rounded-2xl p-6 mb-8 flex justify-between items-center">
                    <div>
                        <p class="text-[10px] uppercase tracking-widest font-bold text-gray-500 mb-1">Acquisition Cost</p>
                        <p class="text-3xl font-black text-white"><?= $formatted_price ?></p>
                    </div>
                    <div class="text-right hidden sm:block">
                        <p class="text-[10px] uppercase tracking-widest font-bold text-gray-500 mb-1">Stock Status</p>
                        <p class="text-sm font-bold text-green-400"><i class="ph-fill ph-check-circle"></i> Available</p>
                    </div>
                </div>

                <a href="<?= get_url('app', '/?module=shop&page=checkout&art_id=' . $artifact['art_id'] . '&sec_bind=' . esc($system_bind)) ?>" class="bg-<?= $color ?>-600 hover:bg-<?= $color ?>-500 text-white font-bold py-5 px-8 rounded-2xl transition-all shadow-[0_0_30px_rgba(var(--tw-colors-<?= $color ?>-600),0.3)] hover:shadow-[0_0_40px_rgba(var(--tw-colors-<?= $color ?>-600),0.5)] flex justify-center items-center gap-3 text-lg group">
                    Initiate Checkout <i class="ph-bold ph-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/app_footer.php'; ?>