<?php
/**
 * PORTAL 1: Landing CMS (V4 Showcase Edition)
 */
session_start();
$active_portal = 'landing';

require_once __DIR__ . '/../functions/global_functions.php';
load_env(__DIR__ . '/../.env');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../functions/currency_engine.php';

if (empty($_SESSION['sec_bind'])) $_SESSION['sec_bind'] = bin2hex(random_bytes(16));
$system_bind = $_SESSION['sec_bind'];

$current_currency = $_SESSION['currency'] ?? 'MMK';
$exchange_rates = get_exchange_rates($pdo);

if (!function_exists('get_ui_price')) {
    function get_ui_price($mmk_amount) {
        global $current_currency, $exchange_rates;
        return format_price(convert_price($mmk_amount, $current_currency, $exchange_rates), $current_currency);
    }
}

// Fetch Showcase Data
$stmt = $pdo->query("SELECT * FROM md_artifacts WHERE is_active = 1 ORDER BY art_id DESC LIMIT 3");
$artifacts = $stmt->fetchAll();

$stmt_sparks = $pdo->query("SELECT * FROM md_neural_sparks ORDER BY spark_id ASC LIMIT 4");
$sparks = $stmt_sparks->fetchAll();

// 24 Enterprise Features Showcase Data
$features = [
    ['icon' => 'ph-shield-check', 'color' => 'green', 'title' => 'Military-Grade Encryption', 'desc' => 'All payloads are secured with SHA-256.'],
    ['icon' => 'ph-fingerprint', 'color' => 'blue', 'title' => 'JWT Stateless Sessions', 'desc' => 'Zero-trust architecture for absolute privacy.'],
    ['icon' => 'ph-globe-hemisphere-east', 'color' => 'indigo', 'title' => '7-Language UI', 'desc' => 'Native auto-translation for global access.'],
    ['icon' => 'ph-currency-circle-dollar', 'color' => 'emerald', 'title' => 'Dynamic Currency', 'desc' => 'Real-time MMK, USD, and EUR conversion.'],
    ['icon' => 'ph-telegram-logo', 'color' => 'sky', 'title' => 'MoonBot Integration', 'desc' => 'Direct Telegram alerts for every transaction.'],
    ['icon' => 'ph-lightning', 'color' => 'yellow', 'title' => 'Instant Delivery', 'desc' => 'Keys deposited straight to your Digital Vault.'],
    ['icon' => 'ph-timer', 'color' => 'red', 'title' => 'Quantum Checkout', 'desc' => '10-minute secure transaction windows.'],
    ['icon' => 'ph-cube', 'color' => 'purple', 'title' => 'Smart Blindboxes', 'desc' => 'Algorithmic drops for high-value RNG loot.'],
    ['icon' => 'ph-hammer', 'color' => 'orange', 'title' => 'Bespoke Forge', 'desc' => 'Request custom software and profiles.'],
    ['icon' => 'ph-chat-teardrop-text', 'color' => 'pink', 'title' => 'Whisper Threads', 'desc' => 'Encrypted direct comms with MoonAdmin.'],
    ['icon' => 'ph-receipt', 'color' => 'teal', 'title' => 'Immutable Ledgers', 'desc' => 'Permanent cryptographic order history.'],
    ['icon' => 'ph-paint-brush', 'color' => 'rose', 'title' => 'Dark Matter UI', 'desc' => 'Immersive glassmorphism aesthetics.'],
    ['icon' => 'ph-rocket-launch', 'color' => 'blue', 'title' => 'CDN Accelerated', 'desc' => 'Sub-millisecond global asset loading.'],
    ['icon' => 'ph-user-focus', 'color' => 'indigo', 'title' => 'Anonymous Mode', 'desc' => 'Browse without tracking or profiling.'],
    ['icon' => 'ph-sparkle', 'color' => 'purple', 'title' => 'AI Neural Sparks', 'desc' => 'Pre-tested prompts for Midjourney & ChatGPT.'],
    ['icon' => 'ph-key', 'color' => 'yellow', 'title' => 'Singularity Keys', 'desc' => 'One-time use elite access codes.'],
    ['icon' => 'ph-infinity', 'color' => 'green', 'title' => 'Infinity Data', 'desc' => 'Lifetime access to shared network drives.'],
    ['icon' => 'ph-device-mobile', 'color' => 'sky', 'title' => 'Mobile Native', 'desc' => 'App-like floating navigation on iOS/Android.'],
    ['icon' => 'ph-scan', 'color' => 'emerald', 'title' => 'Zero-Knowledge', 'desc' => 'We store your tag, not your identity.'],
    ['icon' => 'ph-robot', 'color' => 'orange', 'title' => 'Automated Restocks', 'desc' => 'Bot-driven inventory management.'],
    ['icon' => 'ph-chart-line-up', 'color' => 'pink', 'title' => 'Live Market Trends', 'desc' => 'Dashboard highlights what is popular.'],
    ['icon' => 'ph-intersect', 'color' => 'blue', 'title' => 'Algorithmic Match', 'desc' => 'Smart product recommendations.'],
    ['icon' => 'ph-cloud-check', 'color' => 'teal', 'title' => 'Decentralized Core', 'desc' => 'Redundant backups across the nexus.'],
    ['icon' => 'ph-star', 'color' => 'yellow', 'title' => 'Premium Ranks', 'desc' => 'Tiered access for verified network entities.']
];

require_once __DIR__ . '/../app/includes/app_header.php';
?>

<div class="text-center max-w-4xl mx-auto pt-20 pb-16 px-6 relative z-10">
    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-[10px] md:text-xs font-bold uppercase tracking-wider mb-6 animate-pulse">
        <i class="ph-fill ph-rocket-launch"></i> The Next Generation Market
    </div>
    <h1 class="text-4xl md:text-6xl lg:text-7xl font-extrabold text-white mb-6 leading-tight tracking-tight">
        Enter the <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-indigo-400 to-purple-500">Baganix Nexus</span>
    </h1>
    <p class="text-base md:text-xl text-gray-400 mb-10 max-w-2xl mx-auto leading-relaxed">
        Your ultimate portal for premium digital products, exclusive game keys, and highly advanced AI Prompts. All secured by the MoonAdmin protocol.
    </p>
    <div class="flex flex-col sm:flex-row justify-center gap-4">
        <a href="<?= get_url('app', '/?module=auth&page=register&sec_bind=' . esc($system_bind)) ?>" class="bg-blue-600 hover:bg-blue-500 text-white px-8 py-4 rounded-xl font-bold transition-all shadow-[0_0_30px_rgba(37,99,235,0.4)] text-base md:text-lg flex items-center justify-center gap-2">
            Create Account <i class="ph-bold ph-arrow-right"></i>
        </a>
        <a href="#market" class="glass-panel text-white hover:bg-gray-800/80 px-8 py-4 rounded-xl font-bold transition-all flex items-center justify-center gap-2 text-base md:text-lg">
            <i class="ph-bold ph-storefront text-xl"></i> Browse Store
        </a>
    </div>
</div>

<div class="container mx-auto px-4 md:px-6 mb-24 relative z-10">
    <div class="text-center mb-10">
        <h2 class="text-2xl md:text-4xl font-bold text-white tracking-tight mb-3">Enterprise Architecture</h2>
        <p class="text-gray-500 text-sm md:text-base">24 reasons why Baganix Prime is the ultimate digital ecosystem.</p>
    </div>
    
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3 md:gap-4">
        <?php foreach ($features as $f): ?>
            <div class="glass-panel p-4 rounded-2xl border border-gray-800/50 hover:border-<?= $f['color'] ?>-500/50 hover:bg-<?= $f['color'] ?>-900/10 transition-all group flex flex-col justify-start">
                <div class="w-8 h-8 md:w-10 md:h-10 rounded-lg bg-<?= $f['color'] ?>-500/10 text-<?= $f['color'] ?>-400 flex items-center justify-center text-xl md:text-2xl mb-3 group-hover:scale-110 transition-transform">
                    <i class="ph-fill <?= $f['icon'] ?>"></i>
                </div>
                <h3 class="text-xs md:text-sm font-bold text-white mb-1 leading-tight"><?= $f['title'] ?></h3>
                <p class="text-[9px] md:text-[10px] text-gray-500 leading-relaxed"><?= $f['desc'] ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div id="market" class="container mx-auto px-4 md:px-6 mb-24 scroll-mt-24 relative z-10">
    <div class="flex items-center gap-3 mb-6 border-b border-gray-800 pb-3">
        <i class="ph-fill ph-package text-blue-500 text-2xl md:text-3xl"></i>
        <h2 class="text-2xl md:text-3xl font-bold text-white tracking-tight">Featured Products</h2>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($artifacts as $art): ?>
            <?php 
                $a_col = $art['delivery_type'] === 'singularity' ? 'purple' : 'blue';
                $a_ico = $art['category'] === 'gaming' ? 'ph-game-controller' : 'ph-package';
            ?>
            <div class="glass-panel rounded-3xl overflow-hidden flex flex-col group border border-gray-800 hover:border-<?= $a_col ?>-500/50 transition-all">
                <div class="h-40 md:h-48 bg-gradient-to-br from-gray-900 to-[#030712] relative flex items-center justify-center border-b border-gray-800 overflow-hidden">
                    <i class="ph-fill <?= $a_ico ?> text-6xl md:text-7xl text-gray-800 group-hover:scale-110 group-hover:text-<?= $a_col ?>-500 transition-all"></i>
                </div>
                <div class="p-5 md:p-6 flex-1 flex flex-col">
                    <h3 class="text-lg md:text-xl font-bold text-white mb-2"><?= esc($art['title']) ?></h3>
                    <p class="text-xs md:text-sm text-gray-500 mb-6 flex-1 line-clamp-2"><?= esc($art['description']) ?></p>
                    <div class="flex justify-between items-center pt-4 border-t border-gray-800/50">
                        <span class="text-lg md:text-xl font-black text-white"><?= get_ui_price($art['base_price_mmk']) ?></span>
                        <a href="<?= get_url('app', '/?module=shop&page=view&id=' . $art['art_id']) ?>" class="bg-gray-800 hover:bg-<?= $a_col ?>-600 text-white px-4 py-2 rounded-xl text-xs md:text-sm font-bold transition-all shadow-lg">View Details</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="container mx-auto px-4 md:px-6 mb-20 grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
    <div class="glass-panel p-8 md:p-10 rounded-3xl border-t-4 border-orange-500 text-center relative overflow-hidden group">
        <div class="absolute inset-0 bg-orange-500/5 group-hover:bg-orange-500/10 transition-colors pointer-events-none"></div>
        <i class="ph-fill ph-gift text-5xl md:text-6xl text-orange-400 mb-4 inline-block group-hover:-translate-y-2 transition-transform"></i>
        <h3 class="text-xl md:text-2xl font-bold text-white mb-2">Smart Blindboxes</h3>
        <p class="text-sm md:text-base text-gray-400 mb-6">Unlock a randomized, high-value premium product from our inventory pool using the App Portal.</p>
        <a href="<?= get_url('app', '/?module=auth&page=register&sec_bind=' . esc($system_bind)) ?>" class="inline-block bg-orange-600/20 text-orange-400 px-6 py-2.5 rounded-xl font-bold hover:bg-orange-600 hover:text-white transition-colors">Try Blindbox in App</a>
    </div>
    <div class="glass-panel p-8 md:p-10 rounded-3xl border-t-4 border-blue-500 text-center relative overflow-hidden group">
        <div class="absolute inset-0 bg-blue-500/5 group-hover:bg-blue-500/10 transition-colors pointer-events-none"></div>
        <i class="ph-fill ph-hammer text-5xl md:text-6xl text-blue-400 mb-4 inline-block group-hover:-translate-y-2 transition-transform"></i>
        <h3 class="text-xl md:text-2xl font-bold text-white mb-2">Bespoke Forge</h3>
        <p class="text-sm md:text-base text-gray-400 mb-6">Need a custom software upgrade or personal Netflix profile? Submit specific requirements directly to Admin.</p>
        <a href="<?= get_url('app', '/?module=auth&page=register&sec_bind=' . esc($system_bind)) ?>" class="inline-block bg-blue-600/20 text-blue-400 px-6 py-2.5 rounded-xl font-bold hover:bg-blue-600 hover:text-white transition-colors">Enter The Forge</a>
    </div>
</div>

<?php require_once __DIR__ . '/../app/includes/app_footer.php'; ?>