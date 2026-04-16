<?php
/**
 * App Portal - Checkout Success UI
 */
if (!isset($active_portal) || $active_portal !== 'app') die("Pulse lost.");
$ledger_id = $_GET['ledger_id'] ?? 'UNKNOWN';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Complete | MoonAccount</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>body { background-color: #030712; color: #E2E8F0; } .glass-panel { background: rgba(17, 24, 39, 0.6); backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.05); }</style>
</head>
<body class="flex items-center justify-center min-h-screen relative overflow-hidden p-4">
    <div class="absolute w-[800px] h-[800px] bg-green-600/10 rounded-full blur-[120px] -z-10 animate-pulse"></div>

    <div class="glass-panel p-8 md:p-12 rounded-3xl w-full max-w-lg text-center border-t-4 border-t-green-500 shadow-2xl relative overflow-hidden">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-500/20 text-green-400 text-4xl mb-6 shadow-inner animate-bounce">
            <i class="ph-fill ph-check-circle"></i>
        </div>
        
        <h1 class="text-3xl font-extrabold text-white tracking-tight mb-2">Protocol Verified</h1>
        <p class="text-gray-400 mb-8">Your transaction proof has been submitted securely into the Nexus.</p>
        
        <div class="bg-gray-900/60 rounded-2xl p-6 border border-gray-800 mb-8 shadow-inner">
            <p class="text-[10px] uppercase tracking-widest font-bold text-gray-500 mb-1">Generated Ledger ID</p>
            <p class="text-2xl font-mono text-white tracking-wider">#<?= esc($ledger_id) ?></p>
            
            <div class="mt-4 pt-4 border-t border-gray-800 text-sm font-bold text-orange-400 flex justify-center items-center gap-2">
                <i class="ph-fill ph-timer"></i> Admin Verification Pending
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="<?= get_url('app', '/?module=dashboard&page=ledger_detail&id=' . urlencode($ledger_id) . '&sec_bind=' . esc($system_bind)) ?>" class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-3.5 px-6 rounded-xl transition-all w-full sm:w-auto">
                View Receipt
            </a>
            <a href="<?= get_url('app', '/?module=dashboard&page=home&sec_bind=' . esc($system_bind)) ?>" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-3.5 px-6 rounded-xl transition-all shadow-[0_0_20px_rgba(37,99,235,0.3)] w-full sm:w-auto">
                Return to Dashboard
            </a>
        </div>
    </div>
</body>
</html>