<?php 
if (!isset($active_portal) || $active_portal !== 'app') die("Pulse lost."); 

// Enhanced Mobile Active State Helper
function is_mobile_active($page_name, $current) {
    if ($page_name === $current) {
        return 'text-blue-400 bg-blue-500/20 border border-blue-500/30 shadow-[0_0_15px_rgba(59,130,246,0.3)] scale-105 rounded-xl';
    }
    return 'text-gray-500 hover:text-gray-300 hover:bg-gray-800/50 rounded-xl border border-transparent';
}
?>
        </main>
        </div>
    
    <footer class="hidden lg:flex border-t border-gray-800/50 mt-auto py-6 relative z-10">
        <div class="container mx-auto px-6 text-center text-xs text-gray-500 uppercase tracking-widest font-bold flex flex-row justify-between items-center gap-4">
            <p>&copy; <?= date('Y') ?> Moonlight Digital</p>
            <p class="flex items-center gap-2 text-green-500 bg-green-500/10 border border-green-500/20 px-3 py-1.5 rounded-full">
                <i class="ph-fill ph-shield-check text-lg"></i> JWT Secured
            </p>
        </div>
    </footer>

    <div class="lg:hidden fixed bottom-4 left-4 right-4 z-[100] rounded-3xl border border-gray-700/50 shadow-[0_20px_50px_rgba(0,0,0,0.8)] flex justify-between items-center p-2 backdrop-blur-2xl bg-gray-900/60 pb-safe">
        
        <a href="<?= get_url('app', '/?module=dashboard&page=home&sec_bind=' . esc($system_bind)) ?>" class="flex flex-col items-center justify-center gap-1 w-14 h-14 transition-all <?= is_mobile_active('home', $current_page) ?>">
            <i class="ph-fill ph-squares-four text-xl"></i>
            <span class="text-[8px] uppercase font-bold tracking-widest">Home</span>
        </a>
        
        <a href="<?= get_url('app', '/?module=dashboard&page=vault&sec_bind=' . esc($system_bind)) ?>" class="flex flex-col items-center justify-center gap-1 w-14 h-14 transition-all <?= is_mobile_active('vault', $current_page) ?>">
            <i class="ph-fill ph-vault text-xl"></i>
            <span class="text-[8px] uppercase font-bold tracking-widest">Vault</span>
        </a>
        
        <a href="<?= get_url('main', '/') ?>" class="flex items-center justify-center -mt-8 w-16 h-16 rounded-full bg-gradient-to-tr from-blue-600 to-purple-600 text-white shadow-[0_0_25px_rgba(59,130,246,0.6)] border-[5px] border-[#030712] transform hover:scale-105 transition-transform shrink-0 relative z-10">
            <i class="ph-bold ph-shopping-cart text-2xl"></i>
        </a>

        <a href="<?= get_url('app', '/?module=dashboard&page=support&sec_bind=' . esc($system_bind)) ?>" class="flex flex-col items-center justify-center gap-1 w-14 h-14 transition-all <?= is_mobile_active('support', $current_page) ?>">
            <i class="ph-fill ph-chat-teardrop-text text-xl"></i>
            <span class="text-[8px] uppercase font-bold tracking-widest">Support</span>
        </a>
        
        <a href="<?= get_url('app', '/?module=dashboard&page=settings&sec_bind=' . esc($system_bind)) ?>" class="flex flex-col items-center justify-center gap-1 w-14 h-14 transition-all <?= is_mobile_active('settings', $current_page) ?>">
            <i class="ph-fill ph-gear text-xl"></i>
            <span class="text-[8px] uppercase font-bold tracking-widest">Settings</span>
        </a>
    </div>

</body>
</html>