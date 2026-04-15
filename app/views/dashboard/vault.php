<?php
/**
 * App Portal - Digital Vault (UI Template)
 * Placeholder for future endpoint integration.
 */
if (!isset($active_portal) || $active_portal !== 'app') die("Pulse lost.");
require_once __DIR__ . '/includes/app_header.php';
?>

<header class="mb-8 flex justify-between items-end">
    <div>
        <h1 class="text-3xl font-extrabold text-white tracking-tight">Digital Vault</h1>
        <p class="text-gray-400 mt-1">Your secured keys, files, and bespoke upgrades.</p>
    </div>
    <div class="hidden sm:block text-right">
        <i class="ph-duotone ph-vault text-5xl text-blue-500/20"></i>
    </div>
</header>

<div class="glass-panel p-16 rounded-3xl text-center border-dashed border-2 border-gray-700/50">
    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-800/80 text-gray-500 text-4xl mb-6 shadow-inner">
        <i class="ph-fill ph-key"></i>
    </div>
    <h2 class="text-xl font-bold text-white mb-2">Vault Empty</h2>
    <p class="text-gray-500 max-w-sm mx-auto mb-8">You have not acquired any artifacts yet. Visit the market to secure your first asset.</p>
    
    <a href="<?= get_url('main', '/') ?>#artifacts" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 px-6 rounded-xl transition-all shadow-[0_0_20px_rgba(37,99,235,0.3)]">
        Browse Market <i class="ph-bold ph-arrow-right"></i>
    </a>
</div>

<?php require_once __DIR__ . '/includes/app_footer.php'; ?>