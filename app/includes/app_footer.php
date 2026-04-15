<?php if (!isset($active_portal) || $active_portal !== 'app') die("Pulse lost."); ?>
        </main>
        <!-- End Dynamic Content -->
    </div>
    
    <footer class="border-t border-gray-800/50 mt-auto py-6">
        <div class="container mx-auto px-6 text-center text-xs text-gray-500 uppercase tracking-widest font-bold flex justify-between items-center">
            <p>&copy; <?= date('Y') ?> Moonlight Digital</p>
            <p class="flex items-center gap-1 text-green-500"><i class="ph-fill ph-shield-check text-lg"></i> Connection Secured</p>
        </div>
    </footer>
</body>
</html>