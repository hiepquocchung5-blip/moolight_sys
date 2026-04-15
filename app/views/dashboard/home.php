<?php
/**
 * App Portal - Command Center (V3 Mobile Rich)
 */
if (!isset($active_portal) || $active_portal !== 'app') die("Pulse lost.");
require_once __DIR__ . '/../../../functions/currency_engine.php';

$token = $_SESSION['api_token'] ?? '';
$api_res = call_moonlight_api('/v1/user/dashboard', 'GET', [], $token);

if ($api_res['status_code'] === 401) {
    redirect(get_url('app', '/?module=auth&page=logout&sec_bind=' . esc($system_bind)));
}

$dashboard_data = $api_res['body']['data'] ?? [];
$error_msg = $_GET['error'] ?? null;
$success_msg = $_GET['success'] ?? null;

// Currency Engine
$current_currency = $_SESSION['currency'] ?? 'MMK';
$exchange_rates = get_exchange_rates($pdo);

require_once __DIR__ . '/../../includes/app_header.php';
?>

<header class="mb-6 md:mb-8">
    <h1 class="text-2xl md:text-3xl font-extrabold text-white tracking-tight">Command Center</h1>
    <p class="text-sm md:text-base text-gray-400 mt-1">Real-time overview of your assets.</p>
</header>

<?php if ($error_msg): ?>
    <div class="bg-red-500/10 border border-red-500/30 text-red-400 text-xs md:text-sm p-3 md:p-4 rounded-xl mb-6 md:mb-8 flex items-center gap-3">
        <i class="ph-fill ph-warning-circle text-lg md:text-xl shrink-0"></i> <?= esc($error_msg) ?>
    </div>
<?php endif; ?>
<?php if ($success_msg): ?>
    <div class="bg-green-500/10 border border-green-500/30 text-green-400 text-xs md:text-sm p-3 md:p-4 rounded-xl mb-6 md:mb-8 flex items-center gap-3">
        <i class="ph-fill ph-check-circle text-lg md:text-xl shrink-0"></i> <?= esc($success_msg) ?>
    </div>
<?php endif; ?>

<!-- Core Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-6 mb-8 md:mb-10">
    <!-- Vault Widget -->
    <div class="glass-panel p-5 md:p-6 rounded-3xl flex items-center justify-between group cursor-pointer shadow-lg" onclick="window.location.href='<?= get_url('app', '/?module=dashboard&page=vault&sec_bind=' . esc($system_bind)) ?>'">
        <div>
            <h3 class="text-[9px] md:text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1 md:mb-2">Items Secured</h3>
            <p class="text-3xl md:text-4xl text-white font-black"><?= esc($dashboard_data['vault_count']) ?></p>
        </div>
        <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-blue-500/10 text-blue-500 flex items-center justify-center text-2xl md:text-3xl group-hover:scale-110 transition-transform">
            <i class="ph-fill ph-vault"></i>
        </div>
    </div>

    <!-- TG Bind Widget -->
    <?php if ($dashboard_data['is_tg_bound']): ?>
        <div class="glass-panel p-5 md:p-6 rounded-3xl flex items-center justify-between border-[#229ED9]/30 bg-[#229ED9]/5 relative overflow-hidden shadow-lg">
            <i class="ph-fill ph-telegram-logo absolute -right-4 -bottom-4 text-7xl text-[#229ED9]/10"></i>
            <div class="relative z-10">
                <h3 class="text-[9px] md:text-[10px] text-[#229ED9] font-bold uppercase tracking-widest mb-1 md:mb-2">MoonBot Status</h3>
                <p class="text-lg md:text-xl text-white font-bold flex items-center gap-2">
                    <i class="ph-fill ph-check-circle text-green-400"></i> Signal Active
                </p>
            </div>
        </div>
    <?php else: ?>
        <div class="glass-panel p-5 md:p-6 rounded-3xl flex items-center justify-between border-orange-500/30 bg-orange-500/5 group cursor-pointer shadow-lg" onclick="window.location.href='<?= get_url('app', '/?module=dashboard&page=settings&sec_bind=' . esc($system_bind)) ?>'">
            <div>
                <h3 class="text-[9px] md:text-[10px] text-orange-400 font-bold uppercase tracking-widest mb-1 md:mb-2 flex items-center gap-1"><i class="ph-fill ph-warning"></i> Action Required</h3>
                <p class="text-base md:text-lg text-white font-bold">Bind Telegram Signal</p>
            </div>
            <i class="ph-bold ph-arrow-right text-orange-400 text-xl group-hover:translate-x-1 transition-transform"></i>
        </div>
    <?php endif; ?>
</div>

<!-- Dynamic Trending Sparks (Horizontal Scroll on Mobile) -->
<div class="mb-8 md:mb-10">
    <div class="flex justify-between items-end mb-4 md:mb-6">
        <h2 class="text-lg md:text-xl font-bold text-white flex items-center gap-2"><i class="ph-fill ph-fire text-orange-500"></i> Trending Sparks</h2>
        <a href="<?= get_url('main', '/') ?>#sparks" class="text-[10px] md:text-xs text-purple-400 hover:text-white font-bold uppercase tracking-widest transition-colors">Explore All</a>
    </div>
    
    <div class="flex overflow-x-auto gap-4 md:gap-6 pb-4 no-scrollbar -mx-4 px-4 md:mx-0 md:px-0">
        <?php foreach ($dashboard_data['trending_sparks'] as $spark): ?>
            <?php 
                $s_color = ($spark['type'] == 'image') ? 'purple' : 'blue'; 
                $price_display = $spark['is_free'] ? 'Free' : format_price(convert_price($spark['price_mmk'], $current_currency, $exchange_rates), $current_currency);
            ?>
            <a href="<?= get_url('app', '/?module=shop&page=view_spark&id=' . $spark['spark_id']) ?>" class="min-w-[240px] md:min-w-[280px] shrink-0 glass-panel p-5 rounded-3xl group hover:border-<?= $s_color ?>-500/50 transition-colors block">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-<?= $s_color ?>-500/20 text-<?= $s_color ?>-400 flex items-center justify-center text-xl">
                        <i class="ph-fill ph-sparkle"></i>
                    </div>
                    <span class="text-[9px] uppercase tracking-widest font-bold text-gray-500"><?= esc($spark['type']) ?></span>
                </div>
                <h3 class="text-sm font-bold text-white mb-2 truncate group-hover:text-<?= $s_color ?>-400 transition-colors"><?= esc($spark['title']) ?></h3>
                <span class="text-xs font-bold bg-gray-800 text-gray-300 px-2.5 py-1 rounded-md"><?= $price_display ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Latest Network Artifacts -->
<div class="mb-8">
    <div class="flex justify-between items-end mb-4 md:mb-6">
        <h2 class="text-lg md:text-xl font-bold text-white flex items-center gap-2"><i class="ph-fill ph-vault text-blue-500"></i> New In Vault</h2>
        <a href="<?= get_url('main', '/') ?>#artifacts" class="text-[10px] md:text-xs text-blue-400 hover:text-white font-bold uppercase tracking-widest transition-colors">View Market</a>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
        <?php foreach ($dashboard_data['latest_artifacts'] as $art): ?>
            <?php 
                $a_color = $art['delivery_type'] === 'singularity' ? 'purple' : 'blue';
                $a_icon = $art['category'] === 'gaming' ? 'ph-game-controller' : 'ph-robot';
            ?>
            <a href="<?= get_url('app', '/?module=shop&page=view&id=' . $art['art_id']) ?>" class="glass-panel p-4 md:p-5 rounded-3xl flex items-center gap-4 group hover:border-<?= $a_color ?>-500/50 transition-colors block">
                <div class="w-12 h-12 md:w-14 md:h-14 shrink-0 rounded-2xl bg-gray-800 flex items-center justify-center text-xl md:text-2xl text-<?= $a_color ?>-500 group-hover:scale-110 transition-transform">
                    <i class="ph-fill <?= $a_icon ?>"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm md:text-base font-bold text-white truncate"><?= esc($art['title']) ?></h3>
                    <p class="text-[10px] md:text-xs text-gray-500 uppercase tracking-widest font-bold mt-1"><?= esc($art['delivery_type']) ?> Class</p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/app_footer.php'; ?>