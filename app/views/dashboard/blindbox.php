<?php
/**
 * App Portal - Smart Blindbox (V4 Perfected)
 */
if (!isset($active_portal) || $active_portal !== 'app') die("Pulse lost.");
require_once __DIR__ . '/../../includes/app_header.php';

$current_currency = $_SESSION['currency'] ?? 'MMK';
$exchange_rates = get_exchange_rates($pdo);
$price_mmk = 15000;
$formatted_price = format_price(convert_price($price_mmk, $current_currency, $exchange_rates), $current_currency);
?>

<header class="mb-8 text-center">
    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-orange-500/10 border border-orange-500/20 text-orange-400 text-xs font-bold uppercase tracking-wider mb-4 animate-pulse">
        <i class="ph-fill ph-magic-wand"></i> Algorithmic Drop
    </div>
    <h1 class="text-3xl md:text-5xl font-extrabold text-white tracking-tight mb-3">Smart Blindbox</h1>
    <p class="text-sm md:text-base text-gray-400 max-w-md mx-auto leading-relaxed">Secure a randomized, high-value premium artifact from the global pool. Guaranteed minimum value of 15,000 MMK.</p>
</header>

<div class="glass-panel p-10 md:p-16 rounded-3xl text-center border-t-4 border-orange-500 shadow-2xl relative overflow-hidden max-w-2xl mx-auto group">
    <div class="absolute w-full h-full top-0 left-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10 pointer-events-none"></div>
    <div class="absolute -right-20 -top-20 w-64 h-64 bg-orange-600/20 rounded-full blur-[80px] pointer-events-none group-hover:bg-orange-500/40 transition-all duration-1000"></div>
    
    <div class="inline-flex items-center justify-center w-32 h-32 md:w-40 md:h-40 rounded-[2rem] bg-gradient-to-br from-gray-800 to-[#030712] shadow-[0_0_50px_rgba(249,115,22,0.2)] group-hover:shadow-[0_0_80px_rgba(249,115,22,0.4)] text-orange-500 text-6xl md:text-7xl mb-8 border border-gray-700 relative z-10 group-hover:-translate-y-4 transition-all duration-500 cursor-pointer">
        <i class="ph-duotone ph-package"></i>
        <i class="ph-fill ph-sparkle absolute -top-4 -right-4 text-3xl text-yellow-400 animate-spin-slow"></i>
    </div>
    
    <div class="bg-gray-900/60 border border-gray-800 p-6 rounded-2xl mb-8 relative z-10 shadow-inner inline-block min-w-[250px]">
        <p class="text-[10px] uppercase tracking-widest font-bold text-gray-500 mb-1">Unlock Protocol Cost</p>
        <p class="text-3xl font-black text-white"><?= $formatted_price ?></p>
    </div>

    <div>
        <a href="<?= get_url('app', '/?module=shop&page=checkout_blindbox&sec_bind=' . esc($system_bind)) ?>" class="inline-flex w-full sm:w-auto bg-orange-600 hover:bg-orange-500 text-white font-bold py-4 md:py-5 px-8 md:px-12 rounded-2xl transition-all shadow-[0_0_30px_rgba(249,115,22,0.4)] hover:shadow-[0_0_50px_rgba(249,115,22,0.6)] justify-center items-center gap-3 text-lg md:text-xl relative z-10">
            <i class="ph-bold ph-lock-key-open"></i> Unlock Algorithmic Box
        </a>
    </div>
    
    <div class="mt-8 pt-6 border-t border-gray-800/50 flex justify-center gap-4 text-[10px] text-gray-500 uppercase tracking-widest font-bold relative z-10">
        <span class="flex items-center gap-1"><i class="ph-fill ph-shield-check text-green-400 text-sm"></i> Provably Fair</span>
        <span class="flex items-center gap-1"><i class="ph-fill ph-check-circle text-blue-400 text-sm"></i> Instant Ledger</span>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/app_footer.php'; ?>