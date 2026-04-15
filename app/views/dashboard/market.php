<?php
/**
 * App Portal - Internal Marketplace (Replaces Landing dependency)
 */
if (!isset($active_portal) || $active_portal !== 'app') die("Pulse lost.");
require_once __DIR__ . '/../../../functions/currency_engine.php';

$token = $_SESSION['api_token'] ?? '';
$api_res = call_moonlight_api('/v1/shop/market', 'GET', [], $token);

if ($api_res['status_code'] === 401) {
    redirect(get_url('app', '/?module=auth&page=logout&sec_bind=' . esc($system_bind)));
}

$market_data = $api_res['body']['data'] ?? ['products' => [], 'prompts' => []];

$current_currency = $_SESSION['currency'] ?? 'MMK';
$exchange_rates = get_exchange_rates($pdo);

function get_ui_price($mmk_amount) {
    global $current_currency, $exchange_rates;
    return format_price(convert_price($mmk_amount, $current_currency, $exchange_rates), $current_currency);
}

require_once __DIR__ . '/../../includes/app_header.php';
?>

<header class="mb-8 md:mb-10 text-center">
    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-bold uppercase tracking-wider mb-4">
        <i class="ph-fill ph-storefront"></i> Network Market Active
    </div>
    <h1 class="text-3xl md:text-5xl font-extrabold text-white tracking-tight mb-3">Discover New Assets</h1>
    <p class="text-sm md:text-lg text-gray-400 max-w-xl mx-auto">Purchase premium accounts, game keys, and AI prompts directly from your secure dashboard.</p>
</header>

<!-- Premium Products Section -->
<div class="mb-12">
    <div class="flex items-center gap-2 mb-6 border-b border-gray-800 pb-3">
        <i class="ph-fill ph-package text-blue-500 text-2xl"></i>
        <h2 class="text-xl md:text-2xl font-bold text-white tracking-tight">Premium Products</h2>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <?php foreach ($market_data['products'] as $art): ?>
            <?php 
                $style = ['icon' => 'ph-package', 'color' => 'blue'];
                if ($art['category'] === 'gaming') $style['icon'] = 'ph-game-controller';
                if ($art['category'] === 'ai_plans') $style['icon'] = 'ph-robot';
                if ($art['delivery_type'] === 'singularity') $style['color'] = 'purple';
                if ($art['delivery_type'] === 'infinity') $style['color'] = 'green';
            ?>
            <a href="<?= get_url('app', '/?module=shop&page=view&id=' . $art['art_id']) ?>" class="glass-panel rounded-3xl overflow-hidden flex flex-col group hover:border-<?= $style['color'] ?>-500/50 transition-all block">
                <div class="h-40 md:h-48 bg-gradient-to-br from-gray-800 to-gray-900 relative flex items-center justify-center border-b border-gray-800">
                    <i class="ph-fill <?= $style['icon'] ?> text-6xl md:text-7xl text-gray-700 group-hover:scale-110 group-hover:text-<?= $style['color'] ?>-500 transition-transform"></i>
                    <span class="absolute top-3 right-3 bg-<?= $style['color'] ?>-500/20 text-<?= $style['color'] ?>-400 border border-<?= $style['color'] ?>-500/50 text-[9px] md:text-[10px] uppercase tracking-widest px-2.5 py-1 rounded-lg font-bold">
                        <?= esc($art['delivery_type']) ?>
                    </span>
                </div>
                <div class="p-5 md:p-6 flex-1 flex flex-col">
                    <h3 class="text-lg md:text-xl font-bold text-white mb-2 truncate"><?= esc($art['title']) ?></h3>
                    <p class="text-xs text-gray-400 mb-6 flex-1 line-clamp-2 leading-relaxed"><?= esc($art['description']) ?></p>
                    <div class="flex justify-between items-center pt-4 border-t border-gray-800/50">
                        <span class="text-lg md:text-xl font-bold text-white"><?= get_ui_price($art['base_price_mmk']) ?></span>
                        <span class="text-<?= $style['color'] ?>-400 text-sm font-bold flex items-center gap-1 group-hover:gap-2 transition-all">Buy <i class="ph-bold ph-arrow-right"></i></span>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- AI Prompts Section -->
<div class="mb-12">
    <div class="flex items-center gap-2 mb-6 border-b border-gray-800 pb-3">
        <i class="ph-fill ph-sparkle text-purple-500 text-2xl"></i>
        <h2 class="text-xl md:text-2xl font-bold text-white tracking-tight">AI Prompts & Data</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
        <?php foreach ($market_data['prompts'] as $spark): ?>
            <?php 
                $s_color = ($spark['type'] == 'image') ? 'purple' : 'blue'; 
                $price_display = $spark['is_free'] ? 'Free' : get_ui_price($spark['price_mmk']);
            ?>
            <a href="<?= get_url('app', '/?module=shop&page=view_spark&id=' . $spark['spark_id']) ?>" class="bg-gray-900/60 border border-gray-700/50 p-5 md:p-6 rounded-2xl flex justify-between items-center group hover:border-<?= $s_color ?>-500/50 transition-colors block">
                <div class="flex-1 pr-4">
                    <span class="text-[10px] font-bold text-<?= $s_color ?>-400 uppercase tracking-widest mb-1 block">
                        <?= str_replace('_', ' ', esc($spark['type'])) ?>
                    </span>
                    <h4 class="text-base md:text-lg font-bold text-white truncate"><?= esc($spark['title']) ?></h4>
                </div>
                <span class="bg-gray-800 group-hover:bg-<?= $s_color ?>-600 text-white px-3 py-1.5 md:px-4 md:py-2 rounded-xl transition-colors font-semibold text-xs md:text-sm shrink-0 shadow-lg">
                    <?= $price_display ?>
                </span>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/app_footer.php'; ?>