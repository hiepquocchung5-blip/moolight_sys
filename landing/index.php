<?php
/**
 * PORTAL 1: LANDING CMS (Standalone)
 */
session_start();
$active_portal = 'landing';

require_once __DIR__ . '/../functions/global_functions.php';
load_env(__DIR__ . '/../.env');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../functions/currency_engine.php';

// Generate 32-Char Bind Param for secure routing links to App Portal
if (empty($_SESSION['sec_bind'])) {
    $_SESSION['sec_bind'] = bin2hex(random_bytes(16)); 
}
$system_bind = $_SESSION['sec_bind'];

$current_lang = $_SESSION['lang'] ?? 'en-GB';
$current_currency = $_SESSION['currency'] ?? 'MMK';
$exchange_rates = get_exchange_rates($pdo);

$page_title = "Moonlight Digital Market | Elevate Your Realm";

// We pass $system_bind to header.php so it can build secure App URLs
require_once __DIR__ . '/includes/header.php';
?>

    <!-- Live Drops Ticker -->
    <div class="bg-blue-900/40 border-b border-blue-900/50 text-blue-200 text-xs py-2 px-6 flex justify-between items-center z-40 relative backdrop-blur-md">
        <div class="flex items-center gap-3">
            <span class="flex h-2 w-2 relative">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
            </span>
            <span class="font-bold uppercase tracking-widest text-blue-400">Live Network Activity:</span>
            <span class="marquee-text overflow-hidden whitespace-nowrap">Moon_44** just acquired <span class="text-white font-semibold">GTA V Premium</span> • User_09** forged <span class="text-white font-semibold">ChatGPT Pro</span> • New Neural Spark added to Vault.</span>
        </div>
    </div>

    <!-- Hero Section -->
    <main class="container mx-auto px-6 pt-20 pb-16">
        <div class="text-center max-w-4xl mx-auto mb-20">
            <h1 class="text-5xl md:text-6xl font-extrabold text-white mb-6 leading-tight tracking-tight">
                Elevate Your Digital Realm with <br/><span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-500">Premium Artifacts</span>
            </h1>
            <p class="text-lg text-gray-400 mb-10 max-w-2xl mx-auto">
                Securely acquire Gemini Pro credentials, Midjourney Cyber Prompts, Elite Gaming Keys, and Universal E-Books via the Moonlight Network.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="#nexus" class="bg-blue-600 hover:bg-blue-500 text-white px-8 py-3.5 rounded-xl font-semibold transition flex items-center justify-center gap-2 shadow-[0_0_20px_rgba(37,99,235,0.4)]">
                    <i class="ph ph-rocket-launch text-xl"></i> Explore Artifacts
                </a>
                <!-- Secure Router Link passing the 32-Char Bind Param -->
                <a href="<?= get_url('app', '/?module=auth&page=register&sec_bind=' . $system_bind) ?>" class="glass-panel text-white hover:bg-gray-800 px-8 py-3.5 rounded-xl font-semibold transition flex items-center justify-center gap-2">
                    <i class="ph ph-user-plus text-xl text-green-400"></i> Initialize Account
                </a>
            </div>
        </div>

        <!-- Trending Products Grid -->
        <div id="nexus" class="scroll-mt-32">
            <div class="flex justify-between items-end mb-8 border-b border-gray-800 pb-4">
                <div>
                    <h2 class="text-3xl font-bold text-white mb-2">Trending Artifacts</h2>
                    <p class="text-gray-500">Top requested digital goods in the Nexus right now.</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Product Card 1 -->
                <div class="glass-panel rounded-2xl overflow-hidden glow-effect transition duration-300 group flex flex-col">
                    <div class="h-48 bg-gradient-to-br from-gray-800 to-gray-900 relative flex items-center justify-center border-b border-gray-800 overflow-hidden">
                        <i class="ph ph-robot text-6xl text-gray-600 group-hover:scale-110 group-hover:text-blue-500 transition duration-500"></i>
                        <span class="absolute top-3 right-3 bg-blue-600/90 backdrop-blur text-[10px] uppercase tracking-wider px-2.5 py-1 rounded-md text-white font-bold border border-blue-500">Bespoke</span>
                    </div>
                    <div class="p-5 flex-1 flex flex-col">
                        <h3 class="text-lg font-bold text-white mb-2 line-clamp-1">ChatGPT Plus Upgrade</h3>
                        <p class="text-sm text-gray-400 mb-6 flex-1 line-clamp-2">Direct secure upgrade to your personal account via The Forge registration system.</p>
                        <div class="flex justify-between items-center mt-auto">
                            <span class="text-xl font-bold text-white">
                                <?= ($current_currency === 'MMK') ? '' : (($current_currency === 'EUR') ? '€' : '$') ?>
                                <span class="moon-accent"><?= format_price(convert_price(42000, $current_currency, $exchange_rates), $current_currency) ?></span>
                            </span>
                            <!-- Direct secure app link for checkout logic -->
                            <a href="<?= get_url('app', '/?module=shop&page=checkout&art_id=1&sec_bind=' . $system_bind) ?>" class="bg-gray-800 hover:bg-blue-600 text-white px-3 py-2 rounded-lg transition flex items-center gap-1 font-semibold text-sm">
                                Acquire <i class="ph ph-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>