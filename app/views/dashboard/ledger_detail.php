<?php
/**
 * App Portal - Ledger Detail (Digital Receipt)
 */
if (!isset($active_portal) || $active_portal !== 'app') die("Pulse lost.");

$ledger_id = $_GET['id'] ?? null;
if (!$ledger_id) redirect(get_url('app', '/?module=dashboard&page=ledger'));

$token = $_SESSION['api_token'] ?? '';
$api_res = call_moonlight_api('/v1/user/ledger_detail?id=' . urlencode($ledger_id), 'GET', [], $token);

if ($api_res['status_code'] !== 200) redirect(get_url('app', '/?module=dashboard&page=ledger&error=Receipt_Not_Found'));
$ledger = $api_res['body']['data'];

require_once __DIR__ . '/../../includes/app_header.php';
?>

<div class="max-w-3xl mx-auto">
    <a href="<?= get_url('app', '/?module=dashboard&page=ledger&sec_bind=' . esc($system_bind)) ?>" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-gray-500 hover:text-white transition-colors mb-6">
        <i class="ph-bold ph-arrow-left"></i> Back to History
    </a>

    <div class="glass-panel p-8 md:p-10 rounded-3xl relative overflow-hidden">
        <?php $glow_color = $ledger['status'] === 'complete' ? 'green' : ($ledger['status'] === 'verifying' ? 'blue' : 'orange'); ?>
        <div class="absolute -right-20 -top-20 w-64 h-64 bg-<?= $glow_color ?>-600/10 rounded-full blur-[80px] pointer-events-none"></div>

        <div class="flex justify-between items-start border-b border-gray-800 pb-6 mb-6">
            <div>
                <h1 class="text-2xl font-extrabold text-white tracking-tight">Receipt #<?= esc($ledger['ledger_id']) ?></h1>
                <p class="text-sm text-gray-400 mt-1"><?= date('F j, Y, g:i A', strtotime($ledger['created_at'])) ?></p>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-widest border border-<?= $glow_color ?>-500/30 bg-<?= $glow_color ?>-500/10 text-<?= $glow_color ?>-400">
                    <i class="ph-fill ph-circle text-[8px]"></i> <?= esc($ledger['status']) ?>
                </span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-8">
            <div class="bg-gray-900/50 p-4 rounded-2xl border border-gray-800">
                <p class="text-[10px] uppercase tracking-widest font-bold text-gray-500 mb-1">Total Authorized</p>
                <p class="text-xl font-bold text-white"><?= number_format($ledger['total_mmk'], 0) ?> <?= esc($ledger['display_currency']) ?></p>
            </div>
            <div class="bg-gray-900/50 p-4 rounded-2xl border border-gray-800">
                <p class="text-[10px] uppercase tracking-widest font-bold text-gray-500 mb-1">Payment Method</p>
                <p class="text-xl font-bold text-white"><?= esc($ledger['pay_method'] ?? 'N/A') ?> <span class="text-sm text-gray-500">(...<?= esc($ledger['txn_last_6'] ?? '') ?>)</span></p>
            </div>
        </div>

        <?php if ($ledger['status'] === 'complete'): ?>
            <div class="bg-green-500/10 border border-green-500/30 p-6 rounded-2xl text-center">
                <h3 class="text-green-400 font-bold mb-2">Assets Successfully Deployed</h3>
                <p class="text-sm text-gray-400 mb-4">Your acquired items are now permanently unlocked.</p>
                <a href="<?= get_url('app', '/?module=dashboard&page=vault&sec_bind=' . esc($system_bind)) ?>" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-500 text-white text-sm font-bold py-2.5 px-6 rounded-xl transition-all shadow-[0_0_15px_rgba(34,197,94,0.3)]">
                    Access My Products <i class="ph-bold ph-arrow-right"></i>
                </a>
            </div>
        <?php elseif ($ledger['status'] === 'verifying'): ?>
            <div class="bg-blue-500/5 border border-blue-500/20 p-6 rounded-2xl text-center border-dashed">
                <i class="ph-duotone ph-hourglass-high text-4xl text-blue-500/50 mb-3 animate-pulse"></i>
                <h3 class="text-blue-400 font-bold mb-2">Verification in Progress</h3>
                <p class="text-sm text-gray-400">MoonAdmin is currently verifying your transaction. Your assets will unlock automatically upon approval.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/app_footer.php'; ?>