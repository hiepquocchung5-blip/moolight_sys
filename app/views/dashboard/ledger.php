<?php
/**
 * App Portal - Ledger History
 */
if (!isset($active_portal) || $active_portal !== 'app') die("Pulse lost.");
require_once __DIR__ . '/../../includes/app_header.php';
?>

<header class="mb-8 flex justify-between items-end">
    <div>
        <h1 class="text-3xl font-extrabold text-white tracking-tight">Ledger History</h1>
        <p class="text-gray-400 mt-1">Complete record of your network transactions.</p>
    </div>
    <div class="hidden sm:block text-right">
        <i class="ph-duotone ph-receipt text-5xl text-purple-500/20"></i>
    </div>
</header>

<div class="glass-panel p-16 rounded-3xl text-center border-dashed border-2 border-gray-700/50">
    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-800/80 text-gray-500 text-4xl mb-6 shadow-inner">
        <i class="ph-fill ph-scroll"></i>
    </div>
    <h2 class="text-xl font-bold text-white mb-2">No Transactions Found</h2>
    <p class="text-gray-500 max-w-sm mx-auto">Your ledger is currently empty. Initiate a checkout sequence from the market to generate a ledger.</p>
</div>

<?php require_once __DIR__ . '/../../includes/app_footer.php'; ?>