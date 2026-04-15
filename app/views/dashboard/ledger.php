<?php
/**
 * App Portal - Order History (With Clickable Rows)
 */
if (!isset($active_portal) || $active_portal !== 'app') die("Pulse lost.");

$token = $_SESSION['api_token'] ?? '';
$api_res = call_moonlight_api('/v1/user/ledger', 'GET', [], $token);

if ($api_res['status_code'] === 401) redirect(get_url('app', '/?module=auth&page=logout&sec_bind=' . esc($system_bind)));
$ledgers = $api_res['body']['data'] ?? [];

require_once __DIR__ . '/../../includes/app_header.php';
?>

<header class="mb-6 md:mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-2">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-white tracking-tight">Order History</h1>
        <p class="text-sm md:text-base text-gray-400 mt-1">Complete record of your network transactions.</p>
    </div>
    <div class="hidden sm:block text-right"><i class="ph-duotone ph-receipt text-4xl md:text-5xl text-purple-500/20"></i></div>
</header>

<div class="glass-panel rounded-3xl overflow-hidden shadow-lg">
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-400">
            <thead class="bg-gray-900/80 text-xs uppercase tracking-widest text-gray-500 border-b border-gray-800">
                <tr>
                    <th class="px-6 py-5 font-bold">Ledger ID</th>
                    <th class="px-6 py-5 font-bold">Timestamp</th>
                    <th class="px-6 py-5 font-bold">Total</th>
                    <th class="px-6 py-5 font-bold">Status</th>
                    <th class="px-6 py-5 font-bold text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800/50">
                <?php if (empty($ledgers)): ?>
                    <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500 italic">Your ledger is empty.</td></tr>
                <?php else: ?>
                    <?php foreach ($ledgers as $ledger): ?>
                        <tr class="hover:bg-gray-800/30 transition-colors">
                            <td class="px-6 py-5 font-mono text-white">#<?= esc($ledger['ledger_id']) ?></td>
                            <td class="px-6 py-5"><?= date('F j, Y, g:i A', strtotime($ledger['created_at'])) ?></td>
                            <td class="px-6 py-5 font-bold text-white"><?= number_format($ledger['total_mmk'], 0) ?> <?= esc($ledger['display_currency']) ?></td>
                            <td class="px-6 py-5">
                                <?php 
                                    $status_colors = ['pending' => 'bg-orange-500/10 text-orange-400 border-orange-500/20', 'verifying' => 'bg-blue-500/10 text-blue-400 border-blue-500/20', 'complete' => 'bg-green-500/10 text-green-400 border-green-500/20', 'expired' => 'bg-red-500/10 text-red-400 border-red-500/20'];
                                    $color = $status_colors[$ledger['status']] ?? 'bg-gray-500/10 text-gray-400';
                                ?>
                                <span class="px-3 py-1.5 rounded-md text-[10px] font-bold uppercase tracking-widest border <?= $color ?>"><?= esc($ledger['status']) ?></span>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <a href="<?= get_url('app', '/?module=dashboard&page=ledger_detail&id=' . $ledger['ledger_id'] . '&sec_bind=' . esc($system_bind)) ?>" class="text-blue-400 hover:text-white font-bold text-xs uppercase tracking-widest transition-colors flex items-center justify-end gap-1">View <i class="ph-bold ph-caret-right"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="md:hidden divide-y divide-gray-800/50">
        <?php foreach ($ledgers as $ledger): ?>
            <?php 
                $status_colors = ['pending' => 'text-orange-400', 'verifying' => 'text-blue-400', 'complete' => 'text-green-400', 'expired' => 'text-red-400'];
                $t_color = $status_colors[$ledger['status']] ?? 'text-gray-400';
            ?>
            <a href="<?= get_url('app', '/?module=dashboard&page=ledger_detail&id=' . $ledger['ledger_id'] . '&sec_bind=' . esc($system_bind)) ?>" class="p-4 flex flex-col gap-3 hover:bg-gray-800/30 transition-colors block">
                <div class="flex justify-between items-center">
                    <span class="font-mono text-white text-sm font-bold">#<?= esc($ledger['ledger_id']) ?></span>
                    <span class="text-[10px] font-bold uppercase tracking-widest <?= $t_color ?>"><?= esc($ledger['status']) ?></span>
                </div>
                <div class="flex justify-between items-end">
                    <span class="text-xs text-gray-500"><?= date('M j, Y g:i A', strtotime($ledger['created_at'])) ?></span>
                    <span class="text-sm font-bold text-white flex items-center gap-2"><?= number_format($ledger['total_mmk'], 0) ?> <?= esc($ledger['display_currency']) ?> <i class="ph-bold ph-caret-right text-gray-600"></i></span>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>
<?php require_once __DIR__ . '/../../includes/app_footer.php'; ?>