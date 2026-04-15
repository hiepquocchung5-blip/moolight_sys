<?php
if (!isset($active_portal) || $active_portal !== 'app') die("Pulse lost.");
$moon_tag = $_SESSION['moon_tag'] ?? 'Explorer';
$rank = $_SESSION['rank'] ?? 'guest';
$current_page = $_GET['page'] ?? 'home';

// Dynamic Active State Helper
function is_active($page_name, $current) {
    return $page_name === $current ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/30' : 'text-gray-400 hover:text-white hover:bg-gray-800/50';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus Dashboard | MoonAccount</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        body { background-color: #030712; color: #E2E8F0; }
        .glass-panel { background: rgba(17, 24, 39, 0.7); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .cyber-grid { background-size: 50px 50px; background-image: linear-gradient(to right, rgba(255, 255, 255, 0.02) 1px, transparent 1px), linear-gradient(to bottom, rgba(255, 255, 255, 0.02) 1px, transparent 1px); }
    </style>
</head>
<body class="min-h-screen relative cyber-grid overflow-x-hidden flex flex-col">

    <!-- Ambient Glow -->
    <div class="fixed top-[-10%] left-[-10%] w-[800px] h-[800px] bg-blue-900/10 rounded-full blur-[150px] -z-10 pointer-events-none"></div>

    <!-- Top Navbar -->
    <nav class="glass-panel sticky top-0 z-50 border-b border-gray-800/80">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3 group cursor-pointer" onclick="window.location.href='<?= get_url('main', '/') ?>'">
                <i class="ph-fill ph-moon-stars text-3xl text-blue-500 group-hover:rotate-12 transition-transform"></i>
                <span class="font-bold text-white text-xl tracking-wider hidden sm:block">Moon<span class="text-blue-500">Account</span></span>
            </div>
            
            <div class="flex items-center gap-5">
                <span class="bg-gray-800 border border-gray-700 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-widest text-blue-400">
                    Rank: <?= esc($rank) ?>
                </span>
                
                <div class="flex items-center gap-3 border-l border-gray-700 pl-5">
                    <div class="text-right hidden md:block">
                        <p class="text-sm font-bold text-white"><?= esc($moon_tag) ?></p>
                        <p class="text-xs text-green-400 flex items-center justify-end gap-1"><i class="ph-fill ph-circle"></i> Connected</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold shadow-lg shadow-blue-500/20">
                        <?= strtoupper(substr($moon_tag, 0, 1)) ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Layout Grid -->
    <div class="container mx-auto px-6 py-8 flex-1 flex flex-col lg:flex-row gap-8">
        
        <!-- Sidebar Navigation -->
        <aside class="w-full lg:w-64 shrink-0">
            <div class="glass-panel rounded-2xl p-4 sticky top-28">
                <p class="text-[10px] uppercase tracking-widest font-bold text-gray-500 mb-3 ml-2">Nexus Controls</p>
                <nav class="space-y-2">
                    <a href="<?= get_url('app', '/?module=dashboard&page=home&sec_bind=' . esc($system_bind)) ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all font-semibold <?= is_active('home', $current_page) ?>">
                        <i class="ph-fill ph-squares-four text-xl"></i> Command Center
                    </a>
                    <a href="<?= get_url('app', '/?module=dashboard&page=vault&sec_bind=' . esc($system_bind)) ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all font-semibold <?= is_active('vault', $current_page) ?>">
                        <i class="ph-fill ph-vault text-xl"></i> Digital Vault
                    </a>
                    <a href="<?= get_url('app', '/?module=dashboard&page=ledger&sec_bind=' . esc($system_bind)) ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all font-semibold <?= is_active('ledger', $current_page) ?>">
                        <i class="ph-fill ph-receipt text-xl"></i> Ledger History
                    </a>
                    <a href="<?= get_url('app', '/?module=dashboard&page=settings&sec_bind=' . esc($system_bind)) ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all font-semibold <?= is_active('settings', $current_page) ?>">
                        <i class="ph-fill ph-gear text-xl"></i> Entity Settings
                    </a>
                </nav>
                
                <div class="mt-8 pt-4 border-t border-gray-800">
                    <a href="<?= get_url('app', '/?module=auth&page=logout&sec_bind=' . esc($system_bind)) ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-red-400 hover:text-white hover:bg-red-500/20 transition-all font-semibold">
                        <i class="ph-bold ph-power text-xl"></i> Disconnect
                    </a>
                </div>
            </div>
        </aside>

        <!-- Dynamic Page Content Starts Here -->
        <main class="flex-1">
<?php // Content continues in specific view files ?>