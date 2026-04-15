<?php
/**
 * App Portal - Entity Settings (V3 Production)
 */
if (!isset($active_portal) || $active_portal !== 'app') die("Pulse lost.");
require_once __DIR__ . '/../../includes/app_header.php';

$error_msg = $_GET['error'] ?? null;
$success_msg = $_GET['success'] ?? null;
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

<?php if ($error_msg): ?>
    <div class="bg-red-500/10 border border-red-500/30 text-red-400 text-sm p-4 rounded-xl mb-8 flex items-center gap-3">
        <i class="ph-fill ph-warning-circle text-xl"></i> <?= esc($error_msg) ?>
    </div>
<?php endif; ?>
<?php if ($success_msg): ?>
    <div class="bg-green-500/10 border border-green-500/30 text-green-400 text-sm p-4 rounded-xl mb-8 flex items-center gap-3">
        <i class="ph-fill ph-check-circle text-xl"></i> <?= esc($success_msg) ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
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

<!-- Security Center (Change Password) -->
<div class="glass-panel p-8 rounded-3xl border-l-4 border-l-purple-500">
    <h3 class="text-lg font-bold text-white mb-6 border-b border-gray-800 pb-3"><i class="ph-fill ph-shield-check text-purple-500"></i> Security Center</h3>
    
    <form action="<?= get_url('app', '/?module=dashboard&page=process_settings') ?>" method="POST" class="max-w-xl space-y-4">
        <input type="hidden" name="sec_bind" value="<?= esc($system_bind) ?>">
        
        <div>
            <label class="text-[10px] uppercase tracking-widest font-bold text-gray-500 block mb-1 pl-1">Current Passcode</label>
            <input type="password" name="current_password" required placeholder="••••••••" class="bg-gray-900/50 border border-gray-800 text-white px-4 py-3 rounded-xl w-full focus:border-purple-500 focus:outline-none transition-colors">
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="text-[10px] uppercase tracking-widest font-bold text-gray-500 block mb-1 pl-1">New Passcode</label>
                <input type="password" name="new_password" required minlength="8" placeholder="••••••••" class="bg-gray-900/50 border border-gray-800 text-white px-4 py-3 rounded-xl w-full focus:border-purple-500 focus:outline-none transition-colors">
            </div>
            <div>
                <label class="text-[10px] uppercase tracking-widest font-bold text-gray-500 block mb-1 pl-1">Confirm New</label>
                <input type="password" name="confirm_password" required minlength="8" placeholder="••••••••" class="bg-gray-900/50 border border-gray-800 text-white px-4 py-3 rounded-xl w-full focus:border-purple-500 focus:outline-none transition-colors">
            </div>
        </div>

        <button type="submit" class="mt-4 bg-purple-600 hover:bg-purple-500 text-white font-bold py-3.5 px-6 rounded-xl transition-all shadow-[0_0_20px_rgba(147,51,234,0.3)] flex items-center gap-2">
            Update Credentials <i class="ph-bold ph-check"></i>
        </button>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/app_footer.php'; ?>