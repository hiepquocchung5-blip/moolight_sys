<?php
/**
 * Moonlight Digital - Unified Global Header (App & Landing)
 * Includes Google Translate, Currency Engine, and Mobile Quick-Bar.
 */
if (!isset($active_portal) || !in_array($active_portal, ['app', 'landing'])) die("Pulse lost.");

$is_logged_in = isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true;
$moon_tag = $_SESSION['moon_tag'] ?? 'Guest';
$rank = $_SESSION['rank'] ?? 'Unverified';
$current_page = $_GET['page'] ?? 'home';
$current_lang = $_SESSION['lang'] ?? 'en';
$current_currency = $_SESSION['currency'] ?? 'MMK';
$system_bind = $_SESSION['sec_bind'] ?? '';

// Language Map
$languages = ['en' => 'English', 'my' => 'မြန်မာ', 'ar' => 'العربية', 'zh-CN' => '中文', 'ru' => 'Русский', 'ja' => '日本語', 'th' => 'ไทย'];

function is_active($page_name, $current) {
    if ($page_name === $current) return 'text-white bg-blue-500/10 border border-blue-500/20 shadow-[inset_0_0_20px_rgba(59,130,246,0.15)]';
    return 'text-gray-400 hover:text-white hover:bg-gray-800/40 border border-transparent';
}
?>
<!DOCTYPE html>
<html lang="<?= esc($current_lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Baganix Prime | Digital Market</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        body { background-color: #030712; color: #E2E8F0; }
        .glass-panel { background: rgba(17, 24, 39, 0.5); backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .cyber-grid { background-size: 50px 50px; background-image: linear-gradient(to right, rgba(255, 255, 255, 0.02) 1px, transparent 1px), linear-gradient(to bottom, rgba(255, 255, 255, 0.02) 1px, transparent 1px); }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        body { top: 0 !important; }
        .skiptranslate iframe { display: none !important; }
        .pb-safe { padding-bottom: env(safe-area-inset-bottom, 80px); }
    </style>
</head>
<body class="min-h-screen relative cyber-grid overflow-x-hidden flex flex-col pb-safe lg:pb-0">
    <div id="google_translate_element" style="display:none;"></div>
    <div class="fixed top-[-10%] left-[-10%] w-[800px] h-[800px] bg-blue-900/10 rounded-full blur-[150px] -z-10 pointer-events-none"></div>

    <!-- Mobile Top Quick Bar (For Settings/Lang/Currency) -->
    <div class="lg:hidden bg-gray-950 border-b border-gray-800 py-1.5 px-4 flex justify-between items-center z-50 relative">
        <span class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">Global Settings</span>
        <div class="flex gap-2">
            <select onchange="window.location.href='<?= get_url('app', '/?module=actions&page=switch_locale&type=lang&val=') ?>'+this.value" class="bg-gray-800 text-[10px] text-white border border-gray-700 rounded px-1.5 py-0.5 outline-none font-bold uppercase">
                <?php foreach($languages as $code => $name): ?>
                    <option value="<?= $code ?>" <?= $current_lang === $code ? 'selected' : '' ?>><?= $name ?></option>
                <?php endforeach; ?>
            </select>
            <select onchange="window.location.href='<?= get_url('app', '/?module=actions&page=switch_locale&type=currency&val=') ?>'+this.value" class="bg-gray-800 text-[10px] text-green-400 border border-gray-700 rounded px-1.5 py-0.5 outline-none font-bold">
                <option value="MMK" <?= $current_currency === 'MMK' ? 'selected' : '' ?>>MMK</option>
                <option value="USD" <?= $current_currency === 'USD' ? 'selected' : '' ?>>USD</option>
                <option value="EUR" <?= $current_currency === 'EUR' ? 'selected' : '' ?>>EUR</option>
            </select>
        </div>
    </div>

    <!-- Main Navbar -->
    <nav class="glass-panel sticky top-0 z-40 border-b border-gray-800/80 shadow-xl shadow-black/20">
        <div class="container mx-auto px-4 md:px-6 py-3 md:py-4 flex justify-between items-center">
            
            <a href="<?= get_url('main', '/') ?>" class="flex items-center gap-3 group cursor-pointer">
                <i class="ph-fill ph-moon-stars text-2xl md:text-3xl text-blue-500 group-hover:rotate-12 transition-transform"></i>
                <span class="font-bold text-white text-lg md:text-xl tracking-wider">Baganix<span class="text-blue-500">Prime</span></span>
            </a>
            
            <div class="flex items-center gap-3 md:gap-5">
                <!-- Desktop Localization -->
                <div class="hidden lg:flex items-center gap-3 pr-4 border-r border-gray-700">
                    <div class="relative group">
                        <button class="text-xs font-bold uppercase tracking-wider px-3 py-1.5 rounded-lg bg-gray-800 hover:bg-gray-700 flex items-center gap-1.5 transition text-white">
                            <i class="ph-fill ph-translate text-blue-400"></i> <?= strtoupper($current_lang) ?>
                        </button>
                        <div class="absolute right-0 mt-2 w-40 bg-gray-900 border border-gray-700 rounded-xl shadow-2xl hidden group-hover:block overflow-hidden">
                            <?php foreach($languages as $code => $name): ?>
                                <a href="<?= get_url('app', '/?module=actions&page=switch_locale&type=lang&val=' . $code) ?>" class="block px-4 py-2 text-xs text-gray-300 hover:bg-blue-600 hover:text-white transition"><?= $name ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="relative group">
                        <button class="text-xs font-bold uppercase tracking-wider px-3 py-1.5 rounded-lg bg-gray-800 hover:bg-gray-700 flex items-center gap-1.5 transition text-white">
                            <i class="ph-fill ph-currency-circle-dollar text-green-400"></i> <?= esc($current_currency) ?>
                        </button>
                        <div class="absolute right-0 mt-2 w-24 bg-gray-900 border border-gray-700 rounded-xl shadow-2xl hidden group-hover:block overflow-hidden">
                            <a href="<?= get_url('app', '/?module=actions&page=switch_locale&type=currency&val=MMK') ?>" class="block px-4 py-2 text-xs text-gray-300 hover:bg-blue-600 hover:text-white transition">MMK</a>
                            <a href="<?= get_url('app', '/?module=actions&page=switch_locale&type=currency&val=USD') ?>" class="block px-4 py-2 text-xs text-gray-300 hover:bg-blue-600 hover:text-white transition">USD</a>
                        </div>
                    </div>
                </div>

                <?php if ($is_logged_in): ?>
                    <div class="flex items-center gap-3 pl-2 md:pl-0">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-bold text-white"><?= esc($moon_tag) ?></p>
                            <p class="text-[10px] uppercase tracking-widest text-blue-400 font-bold"><?= esc($rank) ?></p>
                        </div>
                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center font-bold shadow-lg shadow-blue-500/20">
                            <?= strtoupper(substr($moon_tag, 0, 1)) ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="flex items-center gap-3 pl-2 md:pl-0">
                        <a href="<?= get_url('app', '/?module=auth&page=login') ?>" class="text-xs md:text-sm font-bold text-gray-300 hover:text-white transition">Sign In</a>
                        <a href="<?= get_url('app', '/?module=auth&page=register&sec_bind=' . esc($system_bind)) ?>" class="bg-blue-600 hover:bg-blue-500 text-white px-4 md:px-5 py-2 md:py-2.5 rounded-xl text-xs md:text-sm font-bold transition shadow-[0_0_20px_rgba(37,99,235,0.3)]">Create Account</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Structural Logic: Show Sidebar ONLY if in App AND logged in -->
    <?php if ($active_portal === 'app' && $is_logged_in): ?>
    <div class="container mx-auto px-4 md:px-6 py-6 md:py-8 flex-1 flex flex-col lg:flex-row gap-8 relative z-10">
        
        <aside class="hidden lg:block w-64 shrink-0">
            <div class="glass-panel rounded-3xl p-5 sticky top-28 shadow-2xl border-t border-l border-white/5">
                <p class="text-[10px] uppercase tracking-widest font-bold text-gray-500 mb-4 ml-2">App Menu</p>
                <nav class="space-y-2">
                    <a href="<?= get_url('app', '/?module=dashboard&page=home&sec_bind=' . esc($system_bind)) ?>" class="flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all font-bold <?= is_active('home', $current_page) ?>">
                        <i class="ph-fill ph-squares-four text-xl"></i> Dashboard
                    </a>
                    <a href="<?= get_url('app', '/?module=dashboard&page=market&sec_bind=' . esc($system_bind)) ?>" class="flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all font-bold <?= is_active('market', $current_page) ?>">
                        <i class="ph-fill ph-storefront text-xl"></i> Shop & Market
                    </a>
                    <a href="<?= get_url('app', '/?module=dashboard&page=vault&sec_bind=' . esc($system_bind)) ?>" class="flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all font-bold <?= is_active('vault', $current_page) ?>">
                        <i class="ph-fill ph-package text-xl"></i> My Products
                    </a>
                    <a href="<?= get_url('app', '/?module=dashboard&page=blindbox&sec_bind=' . esc($system_bind)) ?>" class="flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all font-bold <?= is_active('blindbox', $current_page) ?>">
                        <i class="ph-fill ph-gift text-xl text-orange-400"></i> Smart Blindbox
                    </a>
                    <a href="<?= get_url('app', '/?module=dashboard&page=forge&sec_bind=' . esc($system_bind)) ?>" class="flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all font-bold <?= is_active('forge', $current_page) ?>">
                        <i class="ph-fill ph-hammer text-xl text-purple-400"></i> The Forge
                    </a>
                    <a href="<?= get_url('app', '/?module=dashboard&page=ledger&sec_bind=' . esc($system_bind)) ?>" class="flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all font-bold <?= is_active('ledger', $current_page) ?>">
                        <i class="ph-fill ph-receipt text-xl"></i> Order History
                    </a>
                    
                    <a href="<?= get_url('app', '/?module=dashboard&page=support&sec_bind=' . esc($system_bind)) ?>" class="flex items-center justify-between px-4 py-3.5 rounded-2xl transition-all font-bold <?= is_active('support', $current_page) ?>">
                        <div class="flex items-center gap-3"><i class="ph-fill ph-chat-teardrop-text text-xl"></i> Support Chat</div>
                        <span class="flex h-2.5 w-2.5 relative"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span></span>
                    </a>
                    
                    <a href="<?= get_url('app', '/?module=dashboard&page=settings&sec_bind=' . esc($system_bind)) ?>" class="flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all font-bold <?= is_active('settings', $current_page) ?>">
                        <i class="ph-fill ph-gear text-xl"></i> Settings
                    </a>
                </nav>
                
                <div class="mt-8 pt-5 border-t border-gray-800/50">
                    <a href="<?= get_url('app', '/?module=auth&page=logout&sec_bind=' . esc($system_bind)) ?>" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-red-400 hover:text-white hover:bg-red-500/20 transition-all font-bold">
                        <i class="ph-bold ph-power text-xl"></i> Logout
                    </a>
                </div>
            </div>
        </aside>

        <main class="flex-1 w-full max-w-full lg:max-w-[calc(100%-18rem)]">
    <?php else: ?>
    <!-- Landing Page Structure (No Sidebar) -->
    <div class="w-full flex-1 flex flex-col relative z-10">
        <main class="flex-1 w-full">
    <?php endif; ?>