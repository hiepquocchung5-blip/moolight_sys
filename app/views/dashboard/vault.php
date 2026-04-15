<?php
/**
 * App Portal - Dynamic Digital Vault
 */
if (!isset($active_portal) || $active_portal !== 'app') die("Pulse lost.");

$token = $_SESSION['api_token'] ?? '';
$api_res = call_moonlight_api('/v1/user/vault', 'GET', [], $token);

if ($api_res['status_code'] === 401) {
    redirect(get_url('app', '/?module=auth&page=logout&sec_bind=' . esc($system_bind)));
}

$artifacts = $api_res['body']['data'] ?? [];

require_once __DIR__ . '/../../includes/app_header.php';
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

<?php if (empty($artifacts)): ?>
    <div class="glass-panel p-16 rounded-3xl text-center border-dashed border-2 border-gray-700/50">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-800/80 text-gray-500 text-4xl mb-6 shadow-inner">
            <i class="ph-fill ph-key"></i>
        </div>
        <h2 class="text-xl font-bold text-white mb-2">Vault Empty</h2>
        <p class="text-gray-500 max-w-sm mx-auto mb-8">You have not acquired any artifacts yet.</p>
        <a href="<?= get_url('main', '/') ?>#artifacts" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 px-6 rounded-xl transition-all shadow-[0_0_20px_rgba(37,99,235,0.3)]">
            Browse Market <i class="ph-bold ph-arrow-right"></i>
        </a>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($artifacts as $art): ?>
            <?php 
                $is_link = filter_var($art['payload'], FILTER_VALIDATE_URL);
                $color = $art['delivery_type'] === 'singularity' ? 'purple' : 'green';
            ?>
            <div class="glass-panel p-6 rounded-3xl relative overflow-hidden group">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-12 h-12 rounded-xl bg-<?= $color ?>-500/10 text-<?= $color ?>-400 flex items-center justify-center text-2xl">
                        <i class="ph-fill <?= $is_link ? 'ph-link' : 'ph-key' ?>"></i>
                    </div>
                    <span class="text-[10px] uppercase tracking-widest font-bold bg-gray-800 text-gray-400 px-2 py-1 rounded border border-gray-700">
                        <?= esc($art['category']) ?>
                    </span>
                </div>
                <h3 class="text-lg font-bold text-white mb-1"><?= esc($art['title']) ?></h3>
                <p class="text-xs text-gray-500 mb-4 border-b border-gray-800 pb-4">Acquired: <?= date('M j, Y', strtotime($art['claimed_at'])) ?></p>
                
                <div class="bg-gray-900/80 border border-gray-800 rounded-xl p-3 relative group-hover:border-<?= $color ?>-500/50 transition-colors">
                    <span class="text-[9px] uppercase tracking-widest font-bold text-gray-500 block mb-1">Secure Payload</span>
                    <?php if ($is_link): ?>
                        <a href="<?= esc($art['payload']) ?>" target="_blank" class="text-blue-400 font-bold hover:underline flex items-center gap-1 text-sm break-all">
                            Access Portal <i class="ph-bold ph-arrow-square-out"></i>
                        </a>
                    <?php else: ?>
                        <div class="font-mono text-white text-sm break-all select-all tracking-wider"><?= esc($art['payload']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/app_footer.php'; ?>