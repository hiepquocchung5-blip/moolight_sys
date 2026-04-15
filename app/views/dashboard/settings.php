<?php
/**
 * App Portal - Entity Settings
 */
if (!isset($active_portal) || $active_portal !== 'app') die("Pulse lost.");
require_once __DIR__ . '/../../includes/app_header.php';
?>

<header class="mb-8 flex justify-between items-end">
    <div>
        <h1 class="text-3xl font-extrabold text-white tracking-tight">Entity Settings</h1>
        <p class="text-gray-400 mt-1">Manage your network identity and secure connections.</p>
    </div>
    <div class="hidden sm:block text-right">
        <i class="ph-duotone ph-gear text-5xl text-gray-500/20"></i>
    </div>
</header>

<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
    <!-- Profile Info -->
    <div class="glass-panel p-8 rounded-3xl">
        <h3 class="text-lg font-bold text-white mb-6 border-b border-gray-800 pb-3"><i class="ph-fill ph-identification-card text-blue-500"></i> Primary Identifier</h3>
        
        <div class="space-y-4">
            <div>
                <label class="text-[10px] uppercase tracking-widest font-bold text-gray-500 block mb-1">Moon Tag</label>
                <div class="bg-gray-900/50 border border-gray-800 text-white px-4 py-3 rounded-xl font-mono"><?= esc($moon_tag) ?></div>
            </div>
            <div>
                <label class="text-[10px] uppercase tracking-widest font-bold text-gray-500 block mb-1">Security Clearance</label>
                <div class="bg-gray-900/50 border border-gray-800 text-blue-400 font-bold px-4 py-3 rounded-xl uppercase tracking-widest text-xs"><?= esc($rank) ?> Level</div>
            </div>
        </div>
    </div>

    <!-- Integrations -->
    <div class="glass-panel p-8 rounded-3xl border-t-4 border-t-[#229ED9]">
        <h3 class="text-lg font-bold text-white mb-6 border-b border-gray-800 pb-3"><i class="ph-fill ph-plugs-connected text-[#229ED9]"></i> Signal Integrations</h3>
        
        <p class="text-sm text-gray-400 mb-6 leading-relaxed">Connect your Telegram account to instantly receive automated Restock Alerts and Bespoke Form updates directly to your device.</p>

        <a href="<?= get_url('app', '/?module=auth&page=oauth_redirect&provider=telegram&sec_bind=' . esc($system_bind)) ?>" class="w-full bg-[#229ED9] hover:bg-[#1E8CC0] text-white font-bold py-3.5 rounded-xl transition-all shadow-[0_0_20px_rgba(34,158,217,0.3)] flex justify-center items-center gap-2">
            <i class="ph-fill ph-telegram-logo text-xl"></i> Bind MoonBot Signal
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/app_footer.php'; ?>