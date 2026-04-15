<?php if (!isset($active_portal) || $active_portal !== 'app') die("Pulse lost."); ?>
        </main>
        <!-- End Dynamic Content -->
    </div>
    
    <footer class="border-t border-gray-800/50 mt-auto py-6 relative z-10">
        <div class="container mx-auto px-6 text-center text-xs text-gray-500 uppercase tracking-widest font-bold flex flex-col md:flex-row justify-between items-center gap-4">
            <p>&copy; <?= date('Y') ?> Moonlight Digital</p>
            <p class="flex items-center gap-2 text-green-500 bg-green-500/10 border border-green-500/20 px-3 py-1.5 rounded-full">
                <i class="ph-fill ph-shield-check text-lg"></i> Connection Secured via JWT
            </p>
        </div>
    </footer>
</body>
</html>