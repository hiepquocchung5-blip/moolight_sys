<?php
/**
 * Moonlight Digital Market - Global Storefront Footer (V2 Production)
 */
if (!isset($active_portal) || $active_portal !== 'landing') die("Pulse lost.");
?>
    <!-- Call to Action Banner -->
    <section class="mt-24 border-y border-gray-800/50 bg-gradient-to-r from-gray-900 to-[#0B1120] relative overflow-hidden">
        <div class="absolute right-0 top-0 w-1/2 h-full bg-blue-600/5 blur-[120px] -z-10 pointer-events-none"></div>
        <div class="container mx-auto px-6 py-20 flex flex-col md:flex-row justify-between items-center relative z-10">
            <div class="mb-8 md:mb-0 md:max-w-2xl text-center md:text-left">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-bold uppercase tracking-wider mb-4">
                    <i class="ph-fill ph-bell-ringing"></i> Signal Established
                </div>
                <h2 class="text-4xl font-extrabold text-white mb-4 tracking-tight">Never miss a restock.</h2>
                <p class="text-lg text-gray-400">Bind your Telegram account to receive encrypted, instant notifications when rare Singularity artifacts drop into the Nexus.</p>
            </div>
            <!-- Dynamic routing to the App settings portal -->
            <a href="<?= get_url('app', '/?module=dashboard&page=settings') ?>" class="bg-[#229ED9] hover:bg-[#1E8CC0] text-white px-8 py-4 rounded-2xl font-bold transition-all shadow-[0_0_30px_rgba(34,158,217,0.3)] hover:shadow-[0_0_40px_rgba(34,158,217,0.5)] flex items-center gap-3 text-lg group">
                <i class="ph-fill ph-telegram-logo text-3xl group-hover:scale-110 transition-transform"></i> Bind MoonBot
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-[#030712] pt-20 pb-10 border-t border-gray-800/50">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 border-b border-gray-800/50 pb-12 mb-8">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-2 mb-6">
                        <i class="ph-fill ph-moon-stars text-blue-500 text-3xl"></i>
                        <span class="text-2xl font-bold text-white tracking-wider">Moonlight Market</span>
                    </div>
                    <p class="text-gray-500 max-w-sm mb-6 leading-relaxed">The premier digital nexus for AI credentials, singularity gaming keys, and bespoke software upgrades.</p>
                    <div class="flex gap-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-900 border border-gray-800 flex items-center justify-center text-gray-400 hover:text-white hover:border-blue-500 transition"><i class="ph-fill ph-twitter-logo text-lg"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-900 border border-gray-800 flex items-center justify-center text-gray-400 hover:text-white hover:border-[#5865F2] transition"><i class="ph-fill ph-discord-logo text-lg"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-900 border border-gray-800 flex items-center justify-center text-gray-400 hover:text-white hover:border-[#229ED9] transition"><i class="ph-fill ph-telegram-logo text-lg"></i></a>
                    </div>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-6 uppercase tracking-widest text-sm">Nexus Portals</h4>
                    <ul class="space-y-4 text-sm text-gray-500 font-medium">
                        <li><a href="#artifacts" class="hover:text-blue-400 transition">Premium Artifacts</a></li>
                        <li><a href="#sparks" class="hover:text-purple-400 transition">Neural Sparks</a></li>
                        <li><a href="#blindbox" class="hover:text-orange-400 transition">Smart Blindboxes</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-6 uppercase tracking-widest text-sm">Authentication</h4>
                    <ul class="space-y-4 text-sm text-gray-500 font-medium">
                        <li><a href="<?= get_url('app', '/?module=auth&page=login') ?>" class="hover:text-white transition">Account Login</a></li>
                        <li><a href="<?= get_url('app', '/?module=auth&page=register&sec_bind=' . esc($system_bind)) ?>" class="hover:text-white transition">Initialize Entity</a></li>
                        <li><a href="<?= get_url('app', '/?module=dashboard&page=forge') ?>" class="hover:text-white transition">The Forge</a></li>
                    </ul>
                </div>
            </div>
            <div class="text-center text-gray-600 text-xs font-semibold uppercase tracking-widest flex flex-col md:flex-row justify-between items-center">
                <p>&copy; <?= date('Y') ?> Moonlight Digital Market. All Systems Secured.</p>
                <p class="mt-4 md:mt-0 flex items-center gap-2 text-gray-500">
                    <i class="ph-fill ph-shield-check text-green-500 text-lg"></i> Protected by MoonAdmin API & Telegram Webhooks
                </p>
            </div>
        </div>
    </footer>

</body>
</html>