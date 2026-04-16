<?php
/**
 * App Portal - The Bespoke Forge UI (V4 Design)
 */
if (!isset($active_portal) || $active_portal !== 'app') die("Pulse lost.");
require_once __DIR__ . '/../../includes/app_header.php';
?>

<header class="mb-8 text-center">
    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-bold uppercase tracking-wider mb-4 animate-pulse">
        <i class="ph-fill ph-hammer"></i> Custom Build Engine
    </div>
    <h1 class="text-3xl md:text-5xl font-extrabold text-white tracking-tight mb-3">The Forge</h1>
    <p class="text-sm md:text-base text-gray-400 max-w-md mx-auto leading-relaxed">Submit your bespoke requirements directly to MoonAdmin for custom software or profile builds.</p>
</header>

<div class="glass-panel p-8 md:p-12 rounded-3xl border-t-4 border-blue-500 shadow-2xl relative overflow-hidden max-w-2xl mx-auto group">
    <div class="absolute w-full h-full top-0 left-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10 pointer-events-none"></div>
    <i class="ph-duotone ph-hammer absolute -right-10 -bottom-10 text-[200px] text-blue-500/5 group-hover:-rotate-12 transition-transform duration-700 pointer-events-none"></i>
    
    <form id="forgeForm" class="space-y-6 relative z-10">
        <div class="space-y-2">
            <label class="text-[10px] md:text-xs font-bold text-blue-400 uppercase tracking-widest pl-1 flex items-center gap-2"><i class="ph-fill ph-target"></i> Build Type</label>
            <input type="text" id="forgeType" required placeholder="e.g., Netflix Profile, App Source Code..." class="w-full bg-gray-900/80 border border-gray-700 text-white rounded-xl px-4 py-4 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition shadow-inner text-sm md:text-base">
        </div>
        
        <div class="space-y-2">
            <label class="text-[10px] md:text-xs font-bold text-blue-400 uppercase tracking-widest pl-1 flex items-center gap-2"><i class="ph-fill ph-code-block"></i> Technical Details / Email</label>
            <textarea id="forgeDetails" required rows="5" placeholder="Provide the exact email address or technical specifications required for the build..." class="w-full bg-gray-900/80 border border-gray-700 text-white rounded-xl px-4 py-4 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition resize-none shadow-inner text-sm md:text-base"></textarea>
        </div>

        <button type="submit" id="forgeBtn" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 md:py-5 rounded-xl transition-all shadow-[0_0_20px_rgba(37,99,235,0.4)] flex justify-center items-center gap-2 mt-4 text-lg">
            <span id="forgeText">Transmit Request</span>
            <i id="forgeIcon" class="ph-bold ph-paper-plane-right"></i>
        </button>
        
        <div id="forgeStatus" class="hidden mt-4 pt-4 border-t border-gray-800/50 text-center">
            <p class="text-green-400 text-xs md:text-sm font-bold uppercase tracking-widest flex items-center justify-center gap-2">
                <i class="ph-fill ph-check-circle text-lg"></i> Signal Transmitted to Admin
            </p>
        </div>
    </form>
</div>

<script>
document.getElementById('forgeForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('forgeBtn');
    const text = document.getElementById('forgeText');
    const icon = document.getElementById('forgeIcon');
    const type = document.getElementById('forgeType').value;
    const details = document.getElementById('forgeDetails').value;
    
    btn.classList.add('opacity-50', 'pointer-events-none');
    text.innerText = 'Transmitting...';
    icon.className = 'ph-bold ph-spinner animate-spin';

    try {
        const response = await fetch('<?= get_url("api", "/v1/user/forge_submit") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer <?= $_SESSION["api_token"] ?? "" ?>'
            },
            body: JSON.stringify({ requirement: `Type: ${type}\nDetails: ${details}` })
        });
        
        const result = await response.json();
        if(result.status === 'success') {
            document.getElementById('forgeForm').reset();
            document.getElementById('forgeStatus').classList.remove('hidden');
            text.innerText = 'Submit Another Request';
        } else {
            alert(result.message || 'Error transmitting request.');
            text.innerText = 'Transmit Request';
        }
    } catch (err) {
        alert('Network fault.');
        text.innerText = 'Transmit Request';
    }
    
    icon.className = 'ph-bold ph-paper-plane-right';
    btn.classList.remove('opacity-50', 'pointer-events-none');
});
</script>

<?php require_once __DIR__ . '/../../includes/app_footer.php'; ?>