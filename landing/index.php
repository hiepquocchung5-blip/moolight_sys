<?php
/**
 * PORTAL 1: LANDING CMS (V2 Production)
 * Document Root: /moonlight_root/landing/
 */
session_start();
$active_portal = 'landing';

// Boot Core Engine securely
require_once __DIR__ . '/../functions/global_functions.php';
load_env(__DIR__ . '/../.env');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../functions/currency_engine.php';

// Generate 32-Char Bind Param for secure routing links to App Portal
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
            <span class="marquee-text overflow-hidden whitespace-nowrap opacity-80">Moon_44** just acquired <span class="text-white font-semibold">GTA V Premium</span> • User_09** forged <span class="text-white font-semibold">ChatGPT Pro</span> • New Neural Spark added.</span>
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
                Securely acquire Gemini Pro credentials, Midjourney Cyber Prompts, Elite Gaming Keys, and Universal Data via the Moonlight Network.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-6">
                <a href="#artifacts" class="bg-blue-600 hover:bg-blue-500 text-white px-8 py-4 rounded-2xl font-bold transition-all flex items-center justify-center gap-3 shadow-[0_0_30px_rgba(37,99,235,0.4)] text-lg">
                    <i class="ph-bold ph-rocket-launch text-2xl"></i> Enter The Vault
                </a>
                <!-- Secure Router Link passing the 32-Char Bind Param -->
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
                    <p class="text-gray-500 text-lg">Highly sought-after credentials and gaming singularity keys.</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                
                <!-- Product 1: Bespoke -->
                <div class="glass-panel rounded-3xl overflow-hidden glow-effect flex flex-col group">
                    <div class="h-56 bg-gradient-to-br from-gray-800 to-gray-900 relative flex items-center justify-center border-b border-gray-800 overflow-hidden">
                        <i class="ph-fill ph-robot text-7xl text-gray-700 group-hover:scale-110 group-hover:text-blue-500 transition-all duration-500"></i>
                        <span class="absolute top-4 right-4 bg-blue-500/20 text-blue-400 border border-blue-500/50 text-[10px] uppercase tracking-widest px-3 py-1.5 rounded-lg font-bold backdrop-blur-md">Bespoke</span>
                    </div>
                    <div class="p-8 flex-1 flex flex-col">
                        <h3 class="text-2xl font-bold text-white mb-3">ChatGPT Plus Upgrade</h3>
                        <p class="text-gray-400 mb-8 flex-1 leading-relaxed">Direct secure upgrade to your personal account. Details are submitted via The Forge after checkout.</p>
                        <div class="flex justify-between items-center mt-auto pt-6 border-t border-gray-800/50">
                            <span class="text-2xl font-bold text-white"><?= get_ui_price(42000) ?></span>
                            <a href="<?= get_url('app', '/?module=shop&page=checkout&art_id=1&sec_bind=' . esc($system_bind)) ?>" class="bg-gray-800 hover:bg-blue-600 text-white px-5 py-2.5 rounded-xl transition-all flex items-center gap-2 font-bold shadow-lg">
                                Acquire <i class="ph-bold ph-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Product 2: Singularity -->
                <div class="glass-panel rounded-3xl overflow-hidden glow-effect flex flex-col group">
                    <div class="h-56 bg-gradient-to-br from-gray-800 to-gray-900 relative flex items-center justify-center border-b border-gray-800 overflow-hidden">
                        <i class="ph-fill ph-game-controller text-7xl text-gray-700 group-hover:scale-110 group-hover:text-purple-500 transition-all duration-500"></i>
                        <span class="absolute top-4 right-4 bg-purple-500/20 text-purple-400 border border-purple-500/50 text-[10px] uppercase tracking-widest px-3 py-1.5 rounded-lg font-bold backdrop-blur-md">Singularity</span>
                    </div>
                    <div class="p-8 flex-1 flex flex-col">
                        <h3 class="text-2xl font-bold text-white mb-3">GTA V Premium Edition</h3>
                        <p class="text-gray-400 mb-8 flex-1 leading-relaxed">Instant 16-digit unique PIN delivery. Automatically secured in your MoonAccount vault post-checkout.</p>
                        <div class="flex justify-between items-center mt-auto pt-6 border-t border-gray-800/50">
                            <span class="text-2xl font-bold text-white"><?= get_ui_price(25000) ?></span>
                            <a href="<?= get_url('app', '/?module=shop&page=checkout&art_id=2&sec_bind=' . esc($system_bind)) ?>" class="bg-gray-800 hover:bg-purple-600 text-white px-5 py-2.5 rounded-xl transition-all flex items-center gap-2 font-bold shadow-lg">
                                Acquire <i class="ph-bold ph-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Product 3: Infinity -->
                <div class="glass-panel rounded-3xl overflow-hidden glow-effect flex flex-col group">
                    <div class="h-56 bg-gradient-to-br from-gray-800 to-gray-900 relative flex items-center justify-center border-b border-gray-800 overflow-hidden">
                        <i class="ph-fill ph-code-block text-7xl text-gray-700 group-hover:scale-110 group-hover:text-green-500 transition-all duration-500"></i>
                        <span class="absolute top-4 right-4 bg-green-500/20 text-green-400 border border-green-500/50 text-[10px] uppercase tracking-widest px-3 py-1.5 rounded-lg font-bold backdrop-blur-md">Infinity</span>
                    </div>
                    <div class="p-8 flex-1 flex flex-col">
                        <h3 class="text-2xl font-bold text-white mb-3">SaaS Deployment Kit</h3>
                        <p class="text-gray-400 mb-8 flex-1 leading-relaxed">Universal database structure and PHP routing engine source code. Download link provided instantly.</p>
                        <div class="flex justify-between items-center mt-auto pt-6 border-t border-gray-800/50">
                            <span class="text-2xl font-bold text-white"><?= get_ui_price(85000) ?></span>
                            <a href="<?= get_url('app', '/?module=shop&page=checkout&art_id=3&sec_bind=' . esc($system_bind)) ?>" class="bg-gray-800 hover:bg-green-600 text-white px-5 py-2.5 rounded-xl transition-all flex items-center gap-2 font-bold shadow-lg">
                                Acquire <i class="ph-bold ph-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

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
                <p class="text-gray-400 mb-8 max-w-2xl relative z-10">Premium, highly-engineered text prompts for Midjourney and ChatGPT. Skip the trial and error.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                    <div class="bg-gray-900/80 border border-gray-700/50 p-6 rounded-2xl flex justify-between items-center group hover:border-purple-500/50 transition-colors">
                        <div>
                            <span class="text-xs font-bold text-purple-400 uppercase tracking-widest mb-1 block">Image Data</span>
                            <h4 class="text-lg font-bold text-white">Cyberpunk Neon Cityscape V6</h4>
                        </div>
                        <a href="<?= get_url('app', '/?module=shop&page=checkout&art_id=101&sec_bind=' . esc($system_bind)) ?>" class="bg-gray-800 group-hover:bg-purple-600 text-white px-4 py-2 rounded-xl transition-colors font-semibold text-sm">
                            <?= get_ui_price(5000) ?>
                        </a>
                    </div>
                    <div class="bg-gray-900/80 border border-gray-700/50 p-6 rounded-2xl flex justify-between items-center group hover:border-purple-500/50 transition-colors">
                        <div>
                            <span class="text-xs font-bold text-blue-400 uppercase tracking-widest mb-1 block">Cyber Research</span>
                            <h4 class="text-lg font-bold text-white">Advanced Pentesting Workflows</h4>
                        </div>
                        <a href="<?= get_url('app', '/?module=shop&page=checkout&art_id=102&sec_bind=' . esc($system_bind)) ?>" class="bg-gray-800 group-hover:bg-purple-600 text-white px-4 py-2 rounded-xl transition-colors font-semibold text-sm">
                            <?= get_ui_price(8000) ?>
                        </a>
                    </div>
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
                    <p class="text-lg text-gray-400 mb-8 max-w-xl mx-auto">An algorithmic loot box tailored to your viewing history. Contains random high-value Singularity keys or Premium Infinity data.</p>
                    
                    <a href="<?= get_url('app', '/?module=shop&page=blindbox&sec_bind=' . esc($system_bind)) ?>" class="inline-flex items-center gap-3 bg-orange-600 hover:bg-orange-500 text-white px-8 py-4 rounded-2xl font-bold transition-all shadow-[0_0_30px_rgba(234,88,12,0.4)] hover:shadow-[0_0_40px_rgba(234,88,12,0.6)] text-lg">
                        <i class="ph-bold ph-lock-key-open"></i> Unlock Algorithmic Box - <?= get_ui_price(15000) ?>
                    </a>
                </div>
            </div>
        </div>
    </main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>