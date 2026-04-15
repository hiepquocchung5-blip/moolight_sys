<?php
/**
 * App Portal - Command Center (Home)
 * Fixes: Uses absolute relative paths to reach the /app/includes/ directory reliably.
 */
if (!isset($active_portal) || $active_portal !== 'app') die("Pulse lost.");

// Securely fetch User Data from the API using their JWT Token
$token = $_SESSION['api_token'] ?? '';
$api_res = call_moonlight_api('/v1/user/dashboard', 'GET', [], $token);

if ($api_res['status_code'] === 401) {
    redirect(get_url('app', '/?module=auth&page=logout&sec_bind=' . esc($system_bind)));
}

$dashboard_data = $api_res['body']['data'] ?? ['vault_count' => 0, 'recent_ledgers' => [], 'is_tg_bound' => false];
$error_msg = $_GET['error'] ?? null;
$success_msg = $_GET['success'] ?? null;

// FIX: Corrected path jumping up two directories to reach /includes/
require_once __DIR__ . '/../../includes/app_header.php';
?>

<header class="mb-8">
    <h1 class="text-3xl font-extrabold text-white tracking-tight">Command Center</h1>
    <p class="text-gray-400 mt-1">Real-time overview of your digital assets and ledger status.</p>
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

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <!-- Vault Widget -->
    <div class="glass-panel p-6 rounded-3xl flex items-center justify-between group cursor-pointer" onclick="window.location.href='<?= get_url('app', '/?module=dashboard&page=vault&sec_bind=' . esc($system_bind)) ?>'">
        <div>
            <h3 class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Items Secured</h3>
            <p class="text-4xl text-white font-black"><?= esc($dashboard_data['vault_count']) ?></p>
        </div>
        <div class="w-16 h-16 rounded-2xl bg-blue-500/10 text-blue-500 flex items-center justify-center text-3xl group-hover:scale-110 transition-transform">
            <i class="ph-fill ph-vault"></i>
        </div>
    </div>

    <!-- TG Bind Widget -->
    <?php if ($dashboard_data['is_tg_bound']): ?>
        <div class="glass-panel p-6 rounded-3xl flex items-center justify-between border-[#229ED9]/30 bg-[#229ED9]/5 relative overflow-hidden">
            <i class="ph-fill ph-telegram-logo absolute -right-4 -bottom-4 text-7xl text-[#229ED9]/10"></i>
            <div class="relative z-10">
                <h3 class="text-[10px] text-[#229ED9] font-bold uppercase tracking-widest mb-1">MoonBot Status</h3>
                <p class="text-xl text-white font-bold flex items-center gap-2">
                    <i class="ph-fill ph-check-circle text-green-400"></i> Signal Active
                </p>
            </div>
        </div>
    <?php else: ?>
        <div class="glass-panel p-6 rounded-3xl flex items-center justify-between border-orange-500/30 bg-orange-500/5 group cursor-pointer" onclick="window.location.href='<?= get_url('app', '/?module=dashboard&page=settings&sec_bind=' . esc($system_bind)) ?>'">
            <div>
                <h3 class="text-[10px] text-orange-400 font-bold uppercase tracking-widest mb-1 flex items-center gap-1"><i class="ph-fill ph-warning"></i> Action Required</h3>
                <p class="text-lg text-white font-bold">Bind Telegram Signal</p>
            </div>
            <i class="ph-bold ph-arrow-right text-orange-400 text-xl group-hover:translate-x-1 transition-transform"></i>
        </div>
    <?php endif; ?>
</div>

<!-- Recent Ledgers Table -->
<div class="glass-panel rounded-3xl overflow-hidden">
    <div class="p-6 border-b border-gray-800/80 flex justify-between items-center">
        <h2 class="text-lg font-bold text-white flex items-center gap-2"><i class="ph-fill ph-receipt text-blue-500"></i> Recent Ledgers</h2>
        <a href="<?= get_url('app', '/?module=dashboard&page=ledger&sec_bind=' . esc($system_bind)) ?>" class="text-xs text-blue-400 hover:text-white font-bold uppercase tracking-widest transition-colors">View All</a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-400">
            <thead class="bg-gray-900/50 text-xs uppercase tracking-widest text-gray-500">
                <tr>
                    <th class="px-6 py-4 font-bold">Ledger ID</th>
                    <th class="px-6 py-4 font-bold">Date</th>
                    <th class="px-6 py-4 font-bold">Total</th>
                    <th class="px-6 py-4 font-bold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800/50">
                <?php if (empty($dashboard_data['recent_ledgers'])): ?>
                    <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500 italic">No ledger history found.</td></tr>
                <?php else: ?>
                    <?php foreach ($dashboard_data['recent_ledgers'] as $ledger): ?>
                        <tr class="hover:bg-gray-800/30 transition-colors">
                            <td class="px-6 py-4 font-mono text-white">#<?= esc($ledger['ledger_id']) ?></td>
                            <td class="px-6 py-4"><?= date('M j, Y g:i A', strtotime($ledger['created_at'])) ?></td>
                            <td class="px-6 py-4 font-bold text-white"><?= number_format($ledger['total_mmk'], 0) ?> Ks</td>
                            <td class="px-6 py-4">
                                <?php 
                                    $status_colors = [
                                        'pending' => 'bg-orange-500/10 text-orange-400 border-orange-500/20',
                                        'verifying' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                        'complete' => 'bg-green-500/10 text-green-400 border-green-500/20',
                                        'expired' => 'bg-red-500/10 text-red-400 border-red-500/20'
                                    ];
                                    $color = $status_colors[$ledger['status']] ?? 'bg-gray-500/10 text-gray-400';
                                ?>
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-widest border <?= $color ?>">
                                    <?= esc($ledger['status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/app_footer.php'; ?>