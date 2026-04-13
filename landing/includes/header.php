<?php
/**
 * Moonlight Digital Market - Global Storefront Header
 */
if (!isset($active_portal)) die("Pulse lost.");
?>
<!DOCTYPE html>
<html lang="<?= substr($current_lang ?? 'en-GB', 0, 2) ?>" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? esc($page_title) : 'Moonlight Digital Market' ?></title>
    <!-- Tailwind CSS & Phosphor Icons -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        body { background-color: #0B0F19; color: #E2E8F0; overflow-x: hidden; }
        .moon-accent { color: #60A5FA; }
        .moon-bg-accent { background-color: #1E3A8A; }
        .glass-panel { background: rgba(17, 24, 39, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(55, 65, 81, 0.5); }
        .glow-effect:hover { box-shadow: 0 0 25px rgba(96, 165, 250, 0.3); border-color: rgba(96, 165, 250, 0.5); }
    </style>
</head>
<body class="font-sans antialiased relative">

    <!-- Background Gradient Glow -->
    <div class="fixed inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-blue-900/20 via-[#0B0F19] to-[#0B0F19] -z-10"></div>

    <!-- Global Header -->
    <header class="glass-panel sticky top-0 z-50">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            
            <!-- Logo -->
            <a href="/" class="text-2xl font-bold tracking-wider flex items-center gap-2">
                <i class="ph-fill ph-moon-stars text-blue-400 text-3xl"></i>
                <div>
                    <span class="text-white">Moonlight</span><span class="moon-accent">Market</span>
                </div>
            </a>

            <!-- Navigation & Switchers -->
            <div class="flex items-center space-x-6">
                <nav class="hidden md:flex space-x-6 text-sm font-medium text-gray-400">
                    <a href="#nexus" class="hover:text-white transition">The Nexus</a>
                    <a href="/prompts" class="hover:text-white transition flex items-center gap-1"><i class="ph ph-sparkle"></i> Neural Sparks</a>
                    <a href="/blindbox" class="hover:text-white transition flex items-center gap-1"><i class="ph ph-package"></i> Blindboxes</a>
                </nav>

                <!-- Language Switcher Dropdown -->
                <div class="relative group">
                    <button class="text-sm border border-gray-700 px-3 py-1.5 rounded-lg bg-gray-800 hover:bg-gray-700 flex items-center gap-2 transition">
                        <i class="ph ph-globe text-gray-400"></i> <?= esc($current_lang ?? 'en-GB') ?>
                    </button>
                    <div class="absolute right-0 mt-2 w-36 bg-gray-800 border border-gray-700 rounded-lg shadow-xl hidden group-hover:block overflow-hidden z-50">
                        <a href="/switch-lang/en-GB" class="block px-4 py-2 text-sm hover:bg-blue-600 hover:text-white transition">English (UK)</a>
                        <a href="/switch-lang/my-MM" class="block px-4 py-2 text-sm hover:bg-blue-600 hover:text-white transition">မြန်မာ (Burmese)</a>
                        <a href="/switch-lang/ar-SA" class="block px-4 py-2 text-sm hover:bg-blue-600 hover:text-white transition">العربية (Arabic)</a>
                    </div>
                </div>

                <!-- Currency Switcher Dropdown -->
                <div class="relative group">
                    <button class="text-sm border border-gray-700 px-3 py-1.5 rounded-lg bg-gray-800 hover:bg-gray-700 flex items-center gap-2 transition">
                        <i class="ph ph-currency-circle-dollar text-gray-400"></i> <?= esc($current_currency ?? 'MMK') ?>
                    </button>
                    <div class="absolute right-0 mt-2 w-24 bg-gray-800 border border-gray-700 rounded-lg shadow-xl hidden group-hover:block overflow-hidden z-50">
                        <a href="/switch-currency/MMK" class="block px-4 py-2 text-sm hover:bg-blue-600 transition">MMK</a>
                        <a href="/switch-currency/USD" class="block px-4 py-2 text-sm hover:bg-blue-600 transition">USD</a>
                        <a href="/switch-currency/EUR" class="block px-4 py-2 text-sm hover:bg-blue-600 transition">EUR</a>
                        <a href="/switch-currency/THB" class="block px-4 py-2 text-sm hover:bg-blue-600 transition">THB</a>
                        <a href="/switch-currency/SGD" class="block px-4 py-2 text-sm hover:bg-blue-600 transition">SGD</a>
                    </div>
                </div>

                <!-- Auth Button using get_url() -->
                <a href="<?= get_url('app', '/auth/login') ?>" class="moon-bg-accent hover:bg-blue-600 text-white px-5 py-2 rounded-lg text-sm font-bold transition shadow-[0_0_15px_rgba(96,165,250,0.3)] flex items-center gap-2">
                    <i class="ph ph-user"></i> Moon Account
                </a>
            </div>
        </div>
    </header>