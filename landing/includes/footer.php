<?php
/**
 * Moonlight Digital Market - Global Storefront Footer
 */
if (!isset($active_portal)) die("Pulse lost.");
?>
    <!-- Call to Action Banner -->
    <section class="border-y border-gray-800 bg-gray-900/50 relative overflow-hidden mt-16">
        <div class="absolute right-0 top-0 w-1/2 h-full bg-blue-900/10 blur-[100px] -z-10"></div>
        <div class="container mx-auto px-6 py-16 flex flex-col md:flex-row justify-between items-center">
            <div class="mb-6 md:mb-0 md:max-w-2xl text-center md:text-left">
                <h2 class="text-3xl font-bold text-white mb-3">Never miss a restock.</h2>
                <p class="text-gray-400">Bind your Telegram account to receive instant notifications when rare Singularity artifacts drop.</p>
            </div>
            <a href="<?= get_url('app', '/settings/bind') ?>" class="bg-blue-600 hover:bg-blue-500 text-white px-8 py-3 rounded-xl font-bold transition flex items-center gap-2">
                <i class="ph-fill ph-telegram-logo text-xl"></i> Bind Telegram Bot
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-[#0B0F19] pt-16 pb-8">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-center border-b border-gray-800 pb-8 mb-8">
                <div class="flex items-center gap-2 mb-4 md:mb-0">
                    <i class="ph-fill ph-moon-stars text-blue-500 text-2xl"></i>
                    <span class="text-xl font-bold text-white tracking-wider">Moonlight Market</span>
                </div>
                <div class="flex gap-6">
                    <a href="#" class="text-gray-500 hover:text-white transition"><i class="ph-fill ph-facebook-logo text-2xl"></i></a>
                    <a href="#" class="text-gray-500 hover:text-white transition"><i class="ph-fill ph-twitter-logo text-2xl"></i></a>
                    <a href="#" class="text-gray-500 hover:text-white transition"><i class="ph-fill ph-discord-logo text-2xl"></i></a>
                </div>
            </div>
            <div class="text-center text-gray-600 text-sm flex flex-col md:flex-row justify-between items-center">
                <p>&copy; <?= date('Y') ?> Moonlight Digital Market. All Artifacts Secured.</p>
                <p class="mt-2 md:mt-0 flex items-center gap-1">
                    <i class="ph-fill ph-shield-check text-green-500"></i> Protected by MoonAdmin API
                </p>
            </div>
        </div>
    </footer>

</body>
</html>