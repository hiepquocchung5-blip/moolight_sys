<?php
/**
 * PORTAL 1: LANDING CMS (V3 Database-Driven)
 * Document Root: /moonlight_root/landing/
 */
session_start();
$active_portal = 'landing';

// Boot Core Engine securely
require_once __DIR__ . '/../functions/global_functions.php';
load_env(__DIR__ . '/../.env');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../functions/currency_engine.php';

// Generate 32-Char Bind Param for secure routing
if (empty($_SESSION['sec_bind'])) {
    $_SESSION['sec_bind'] = bin2hex(random_bytes(16)); 
}
$system_bind = $_SESSION['sec_bind'];

// State
$current_lang = $_SESSION['lang'] ?? 'en-GB';
$current_currency = $_SESSION['currency'] ?? 'MMK';
$exchange_rates = get_exchange_rates($pdo);

// Helper for UI prices
function get_ui_price($mmk_amount) {
    global $current_currency, $exchange_rates;
    return format_price(convert_price($mmk_amount, $current_currency, $exchange_rates), $current_currency);
}

// Helper for Dynamic Icons & Colors
function get_art_style($category, $delivery_type) {
    $icon = 'ph-package';
    if ($category === 'gaming') $icon = 'ph-game-controller';
    if ($category === 'streaming') $icon = 'ph-film-strip';
    if ($category === 'ai_plans') $icon = 'ph-robot';
    if ($category === 'software') $icon = 'ph-code-block';

    $color = 'blue';
    if ($delivery_type === 'singularity') $color = 'purple';
    if ($delivery_type === 'infinity') $color = 'green';
    
    return ['icon' => $icon, 'color' => $color];
}

// ==========================================
// FETCH PRODUCTION DATABASE RECORDS
// ==========================================
// 1. Fetch Top 3 Active Premium Artifacts (FIXED: Sorting by art_id instead of created_at)
$stmt = $pdo->query("SELECT * FROM md_artifacts WHERE is_active = 1 ORDER BY art_id DESC LIMIT 3");
$artifacts = $stmt->fetchAll();

// 2. Fetch Top 4 Neural Sparks
$stmt = $pdo->query("SELECT * FROM md_neural_sparks ORDER BY spark_id ASC LIMIT 4");
$sparks = $stmt->fetchAll();


$page_title = "Moonlight Market | The Digital Nexus";
require_once __DIR__ . '/includes/header.php';
?>

    <!-- Live Drops Ticker -->
    <div class="bg-blue-900/20 border-b border-blue-900/30 text-blue-300 text-xs py-2.5 px-6 flex justify-between items-center relative backdrop-blur-md">
        <div class="container mx-auto flex items-center gap-3">
            <span class="flex h-2 w-2 relative">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
            </span>
            <span class="font-bold uppercase tracking-widest text-blue-400">Live Network:</span>
            <span class="marquee-text overflow-hidden whitespace-nowrap opacity-80">Moon_44** acquired <span class="text-white font-semibold">GTA V Premium</span> • User_09** forged <span class="text-white font-semibold">ChatGPT Pro</span> • DB Synced.</span>
        </div>
    </div>

    <!-- Hero Section -->
    <main class="container mx-auto px-6 pt-24 pb-20 relative z-10">
        <div class="text-center max-w-4xl mx-auto mb-24">
            <h1 class="text-5xl md:text-7xl font-extrabold text-white mb-8 leading-tight tracking-tight">
                Master Your Digital Realm with <br/>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-indigo-400 to-purple-500">Premium Artifacts</span>
            </h1>
            <p class="text-xl text-gray-400 mb-12 max-w-2xl mx-auto leading-relaxed">
                Securely acquire Gemini Pro credentials, Midjourney Cyber Prompts, Elite Gaming Keys, and Universal Data directly from the database vault.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-6">
                <a href="#artifacts" class="bg-blue-600 hover:bg-blue-500 text-white px-8 py-4 rounded-2xl font-bold transition-all flex items-center justify-center gap-3 shadow-[0_0_30px_rgba(37,99,235,0.4)] text-lg">
                    <i class="ph-bold ph-rocket-launch text-2xl"></i> Enter The Vault
                </a>
                <a href="<?= get_url('app', '/?module=auth&page=register&sec_bind=' . esc($system_bind)) ?>" class="glass-panel text-white hover:bg-gray-800/80 px-8 py-4 rounded-2xl font-bold transition-all flex items-center justify-center gap-3 text-lg">
                    <i class="ph-bold ph-user-plus text-2xl text-green-400"></i> Initialize Account
                </a>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- SECTION: PREMIUM ARTIFACTS                 -->
        <!-- ========================================== -->
        <div id="artifacts" class="scroll-mt-32 mb-32">
            <div class="flex justify-between items-end mb-10 border-b border-gray-800/80 pb-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <i class="ph-fill ph-vault text-blue-500 text-2xl"></i>
                        <h2 class="text-4xl font-bold text-white tracking-tight">Premium Artifacts</h2>
                    </div>
                    <p class="text-gray-500 text-lg">Live data synced from the `md_artifacts` database table.</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                
                <?php if (empty($artifacts)): ?>
                    <div class="col-span-3 text-center py-10 text-gray-500 border border-dashed border-gray-800 rounded-3xl">
                        <i class="ph-duotone ph-vault text-6xl mb-4 text-gray-700"></i>
                        <p class="font-bold">The Vault is currently empty.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($artifacts as $art): ?>
                        <?php $style = get_art_style($art['category'], $art['delivery_type']); ?>
                        <div class="glass-panel rounded-3xl overflow-hidden glow-effect flex flex-col group">
                            <div class="h-56 bg-gradient-to-br from-gray-800 to-gray-900 relative flex items-center justify-center border-b border-gray-800 overflow-hidden">
                                <!-- Dynamic Icon & Hover Color -->
                                <i class="ph-fill <?= $style['icon'] ?> text-7xl text-gray-700 group-hover:scale-110 group-hover:text-<?= $style['color'] ?>-500 transition-all duration-500"></i>
                                
                                <!-- Dynamic Badge -->
                                <span class="absolute top-4 right-4 bg-<?= $style['color'] ?>-500/20 text-<?= $style['color'] ?>-400 border border-<?= $style['color'] ?>-500/50 text-[10px] uppercase tracking-widest px-3 py-1.5 rounded-lg font-bold backdrop-blur-md">
                                    <?= esc($art['delivery_type']) ?>
                                </span>
                            </div>
                            <div class="p-8 flex-1 flex flex-col">
                                <h3 class="text-2xl font-bold text-white mb-3"><?= esc($art['title']) ?></h3>
                                <p class="text-gray-400 mb-8 flex-1 leading-relaxed"><?= esc($art['description']) ?></p>
                                <div class="flex justify-between items-center mt-auto pt-6 border-t border-gray-800/50">
                                    <span class="text-2xl font-bold text-white"><?= get_ui_price($art['base_price_mmk']) ?></span>
                                    <a href="<?= get_url('app', '/?module=shop&page=checkout&art_id=' . $art['art_id'] . '&sec_bind=' . esc($system_bind)) ?>" class="bg-gray-800 hover:bg-<?= $style['color'] ?>-600 text-white px-5 py-2.5 rounded-xl transition-all flex items-center gap-2 font-bold shadow-lg">
                                        Acquire <i class="ph-bold ph-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>
        </div>

        <!-- ========================================== -->
        <!-- SECTION: NEURAL SPARKS (PROMPTS)           -->
        <!-- ========================================== -->
        <div id="sparks" class="scroll-mt-32 mb-32">
            <div class="bg-purple-900/10 border border-purple-500/20 rounded-3xl p-10 relative overflow-hidden">
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-purple-600/20 rounded-full blur-[80px]"></div>
                
                <div class="flex items-center gap-3 mb-4 relative z-10">
                    <i class="ph-fill ph-sparkle text-purple-400 text-3xl"></i>
                    <h2 class="text-3xl font-bold text-white">Neural Sparks Forge</h2>
                </div>
                <p class="text-gray-400 mb-8 max-w-2xl relative z-10">Premium text prompts synced from the `md_neural_sparks` table. Skip the trial and error.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                    <?php if (empty($sparks)): ?>
                        <p class="text-gray-500 italic col-span-2">No neural sparks currently available.</p>
                    <?php else: ?>
                        <?php foreach ($sparks as $spark): ?>
                            <?php $s_color = ($spark['type'] == 'image') ? 'purple' : (($spark['type'] == 'study') ? 'green' : 'blue'); ?>
                            <div class="bg-gray-900/80 border border-gray-700/50 p-6 rounded-2xl flex justify-between items-center group hover:border-<?= $s_color ?>-500/50 transition-colors">
                                <div>
                                    <span class="text-xs font-bold text-<?= $s_color ?>-400 uppercase tracking-widest mb-1 block">
                                        <?= str_replace('_', ' ', esc($spark['type'])) ?>
                                    </span>
                                    <h4 class="text-lg font-bold text-white"><?= esc($spark['title']) ?></h4>
                                </div>
                                <a href="<?= get_url('app', '/?module=shop&page=checkout_spark&spark_id=' . $spark['spark_id'] . '&sec_bind=' . esc($system_bind)) ?>" class="bg-gray-800 group-hover:bg-<?= $s_color ?>-600 text-white px-4 py-2 rounded-xl transition-colors font-semibold text-sm">
                                    <?= ($spark['is_free']) ? 'Free Access' : get_ui_price($spark['price_mmk']) ?>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- SECTION: SMART BLINDBOXES                  -->
        <!-- ========================================== -->
        <div id="blindbox" class="scroll-mt-32">
            <div class="bg-gradient-to-br from-orange-900/20 to-gray-900 border border-orange-500/20 rounded-3xl p-10 text-center relative overflow-hidden">
                <i class="ph-duotone ph-package absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-[200px] text-orange-500/5"></i>
                
                <div class="relative z-10">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-orange-500/10 text-orange-400 text-3xl mb-6 border border-orange-500/20">
                        <i class="ph-fill ph-package"></i>
                    </div>
                    <h2 class="text-4xl font-bold text-white mb-4">Smart Blindboxes</h2>
                    <p class="text-lg text-gray-400 mb-8 max-w-xl mx-auto">An algorithmic loot box containing random high-value Singularity keys or Premium Infinity data from the vault.</p>
                    
                    <a href="<?= get_url('app', '/?module=shop&page=blindbox&sec_bind=' . esc($system_bind)) ?>" class="inline-flex items-center gap-3 bg-orange-600 hover:bg-orange-500 text-white px-8 py-4 rounded-2xl font-bold transition-all shadow-[0_0_30px_rgba(234,88,12,0.4)] hover:shadow-[0_0_40px_rgba(234,88,12,0.6)] text-lg">
                        <i class="ph-bold ph-lock-key-open"></i> Unlock Algorithmic Box - <?= get_ui_price(15000) ?>
                    </a>
                </div>
            </div>
        </div>
    </main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>