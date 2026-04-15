<?php
/**
 * Moonlight Digital Market - Global Storefront Header (V2 Production)
 */
if (!isset($active_portal) || $active_portal !== 'landing') die("Pulse lost. Unauthorized access.");
?>
<!DOCTYPE html>
<html lang="<?= substr($current_lang ?? 'en-GB', 0, 2) ?>" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? esc($page_title) : 'Moonlight Digital Market | Premium Artifacts' ?></title>
    <!-- Tailwind CSS & Phosphor Icons -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        body { background-color: #030712; color: #E2E8F0; overflow-x: hidden; font-family: 'Inter', sans-serif; }
        .moon-accent { color: #60A5FA; }
        .glass-panel { background: rgba(17, 24, 39, 0.6); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .glow-effect:hover { box-shadow: 0 0 30px rgba(96, 165, 250, 0.15); border-color: rgba(96, 165, 250, 0.3); transform: translateY(-2px); transition: all 0.3s ease; }
        .cyber-grid { background-size: 50px 50px; background-image: linear-gradient(to right, rgba(255, 255, 255, 0.02) 1px, transparent 1px), linear-gradient(to bottom, rgba(255, 255, 255, 0.02) 1px, transparent 1px); }
    </style>
</head>
<body class="antialiased relative cyber-grid">

    <!-- Ambient Glow -->
    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-blue-600/10 rounded-full blur-[120px] -z-10 pointer-events-none"></div>

    <!-- Global Header -->
    <header class="glass-panel sticky top-0 z-50 border-b border-gray-800/50 shadow-xl shadow-black/50">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            
            <!-- Logo -->
            <a href="/" class="text-2xl font-bold tracking-wider flex items-center gap-2 group">
                <i class="ph-fill ph-moon-stars text-blue-500 text-3xl group-hover:rotate-12 transition-transform"></i>
                <div>
                    <span class="text-white">Moonlight</span><span class="moon-accent">Market</span>
                </div>
            </a>

            <!-- Navigation & Switchers -->
            <div class="flex items-center space-x-6">
                <nav class="hidden lg:flex space-x-8 text-sm font-semibold text-gray-400">
                    <a href="#artifacts" class="hover:text-blue-400 transition flex items-center gap-1"><i class="ph ph-vault"></i> Artifacts</a>
                    <a href="#sparks" class="hover:text-purple-400 transition flex items-center gap-1"><i class="ph ph-sparkle"></i> Neural Sparks</a>
                    <a href="#blindbox" class="hover:text-orange-400 transition flex items-center gap-1"><i class="ph ph-package"></i> Blindboxes</a>
                </nav>

                <!-- Language Switcher -->
                <div class="relative group hidden sm:block">
                    <button class="text-xs font-bold uppercase tracking-wider border border-gray-700/50 px-3 py-2 rounded-xl bg-gray-800/50 hover:bg-gray-700 flex items-center gap-2 transition">
                        <i class="ph ph-globe text-gray-400 text-lg"></i> <?= esc($current_lang ?? 'en-GB') ?>
                    </button>
                    <!-- Dropdown content omitted for brevity, identical to previous -->
                </div>

                <!-- Currency Switcher -->
                <div class="relative group hidden sm:block">
                    <button class="text-xs font-bold uppercase tracking-wider border border-gray-700/50 px-3 py-2 rounded-xl bg-gray-800/50 hover:bg-gray-700 flex items-center gap-2 transition">
                        <i class="ph ph-currency-circle-dollar text-gray-400 text-lg"></i> <?= esc($current_currency ?? 'MMK') ?>
                    </button>
                    <div class="absolute right-0 mt-2 w-24 bg-gray-900 border border-gray-700 rounded-xl shadow-2xl hidden group-hover:block overflow-hidden z-50">
                        <a href="/switch-currency/MMK" class="block px-4 py-2 text-sm text-gray-300 hover:bg-blue-600 hover:text-white transition">MMK</a>
                        <a href="/switch-currency/USD" class="block px-4 py-2 text-sm text-gray-300 hover:bg-blue-600 hover:text-white transition">USD</a>
                        <a href="/switch-currency/EUR" class="block px-4 py-2 text-sm text-gray-300 hover:bg-blue-600 hover:text-white transition">EUR</a>
                    </div>
                </div>

                <!-- Action Buttons routing to APP_URL -->
                <div class="flex items-center gap-3 border-l border-gray-700/50 pl-6">
                    <a href="<?= get_url('app', '/?module=auth&page=login') ?>" class="text-sm font-bold text-gray-300 hover:text-white transition">
                        Authenticate
                    </a>
                    <a href="<?= get_url('app', '/?module=auth&page=register&sec_bind=' . esc($system_bind)) ?>" class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2.5 rounded-xl text-sm font-bold transition shadow-[0_0_20px_rgba(37,99,235,0.3)] flex items-center gap-2">
                        <i class="ph-bold ph-user-plus"></i> Initialize
                    </a>
                </div>
            </div>
        </div>
    </header>