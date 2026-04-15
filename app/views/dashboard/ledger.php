<?php
/**
 * App Portal - Dynamic Ledger History
 */
if (!isset($active_portal) || $active_portal !== 'app') die("Pulse lost.");

$token = $_SESSION['api_token'] ?? '';
$api_res = call_moonlight_api('/v1/user/ledger', 'GET', [], $token);

if ($api_res['status_code'] === 401) {
    redirect(get_url('app', '/?module=auth&page=logout&sec_bind=' . esc($system_bind)));
}

$ledgers = $api_res['body']['data'] ?? [];

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

<div class="glass-panel rounded-3xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-400">
            <thead class="bg-gray-900/80 text-xs uppercase tracking-widest text-gray-500 border-b border-gray-800">
                <tr>
                    <th class="px-8 py-5 font-bold">Ledger ID</th>
                    <th class="px-8 py-5 font-bold">Timestamp</th>
                    <th class="px-8 py-5 font-bold">Value Transferred</th>
                    <th class="px-8 py-5 font-bold">Network Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800/50">
                <?php if (empty($ledgers)): ?>
                    <tr><td colspan="4" class="px-8 py-12 text-center text-gray-500 italic">Your ledger is currently empty.</td></tr>
                <?php else: ?>
                    <?php foreach ($ledgers as $ledger): ?>
                        <tr class="hover:bg-gray-800/30 transition-colors">
                            <td class="px-8 py-5 font-mono text-white">#<?= esc($ledger['ledger_id']) ?></td>
                            <td class="px-8 py-5"><?= date('F j, Y, g:i A', strtotime($ledger['created_at'])) ?></td>
                            <td class="px-8 py-5 font-bold text-white"><?= number_format($ledger['total_mmk'], 0) ?> <?= esc($ledger['display_currency']) ?></td>
                            <td class="px-8 py-5">
                                <?php 
                                    $status_colors = [
                                        'pending' => 'bg-orange-500/10 text-orange-400 border-orange-500/20',
                                        'verifying' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                        'complete' => 'bg-green-500/10 text-green-400 border-green-500/20',
                                        'expired' => 'bg-red-500/10 text-red-400 border-red-500/20'
                                    ];
                                    $color = $status_colors[$ledger['status']] ?? 'bg-gray-500/10 text-gray-400';
                                ?>
                                <span class="px-3 py-1.5 rounded-md text-[10px] font-bold uppercase tracking-widest border <?= $color ?>">
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