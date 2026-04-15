<?php
/**
 * App Portal - Quantum Checkout UI
 * Fetches the requested artifact and initializes the 10-minute payment session.
 */
if (!isset($active_portal) || $active_portal !== 'app') die("Pulse lost.");
require_once __DIR__ . '/../../functions/currency_engine.php';

$art_id = $_GET['art_id'] ?? null;
$error_msg = $_GET['error'] ?? null;

if (!$art_id) {
    redirect(get_url('app', '/?module=dashboard&page=home&error=' . urlencode('Artifact not specified.')));
}

// Fetch Artifact Details using the global $pdo connection
$stmt = $pdo->prepare("SELECT * FROM md_artifacts WHERE art_id = :id AND is_active = 1 LIMIT 1");
$stmt->execute(['id' => $art_id]);
$artifact = $stmt->fetch();

if (!$artifact) {
    redirect(get_url('app', '/?module=dashboard&page=home&error=' . urlencode('Artifact unavailable or out of stock.')));
}

// Prepare Pricing
$current_currency = $_SESSION['currency'] ?? 'MMK';
$exchange_rates = get_exchange_rates($pdo);
$display_price = convert_price($artifact['base_price_mmk'], $current_currency, $exchange_rates);
$formatted_price = format_price($display_price, $current_currency);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Checkout | MoonAccount</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        body { background-color: #030712; color: #E2E8F0; }
        .glass-panel { background: rgba(17, 24, 39, 0.6); backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .glass-input { background: rgba(0, 0, 0, 0.3); border: 1px solid rgba(255, 255, 255, 0.1); transition: all 0.3s ease; }
        .glass-input:focus { border-color: #3B82F6; background: rgba(17, 24, 39, 0.8); outline: none; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen relative overflow-hidden font-sans py-10">

    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-blue-600/10 rounded-full blur-[120px] -z-10 animate-pulse"></div>

    <div class="glass-panel p-8 rounded-3xl w-full max-w-lg relative z-10 mx-4 shadow-2xl">
        
        <!-- Timer Header -->
        <div class="text-center mb-6 border-b border-gray-800/50 pb-6">
            <h2 class="text-blue-400 text-xs font-bold uppercase tracking-widest mb-2 flex justify-center items-center gap-2">
                <i class="ph-fill ph-timer text-lg"></i> Quantum Session Active
            </h2>
            <div id="quantum-timer" class="text-5xl font-mono font-bold text-white tracking-widest drop-shadow-[0_0_10px_rgba(255,255,255,0.3)]">10:00</div>
            <p class="text-gray-500 text-xs mt-2">Upload payment proof before the session shatters.</p>
        </div>

        <?php if ($error_msg): ?>
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 text-sm p-3 rounded-xl mb-6 flex items-start gap-2">
                <i class="ph-fill ph-warning-circle text-lg mt-0.5"></i> <span><?= esc($error_msg) ?></span>
            </div>
        <?php endif; ?>

        <!-- Ledger Info -->
        <div class="bg-gray-900/50 rounded-xl p-5 mb-6 border border-gray-800/50">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h3 class="text-white font-bold text-lg"><?= esc($artifact['title']) ?></h3>
                    <span class="text-xs text-blue-400 uppercase tracking-widest font-bold"><?= esc($artifact['delivery_type']) ?> Delivery</span>
                </div>
                <span class="text-2xl font-bold text-white"><?= $formatted_price ?></span>
            </div>
        </div>

        <!-- Transfer Instructions -->
        <div class="mb-6">
            <p class="text-xs text-gray-400 uppercase tracking-wider font-bold mb-3">1. Transfer Funds To</p>
            <div class="flex justify-between items-center glass-input p-4 rounded-xl">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-500/20 text-blue-400 flex items-center justify-center text-xl">
                        <i class="ph-fill ph-bank"></i>
                    </div>
                    <div>
                        <span class="block font-bold text-white">KBZPay / AYAPay</span>
                        <span class="block text-xs text-gray-400">Moonlight Corp</span>
                    </div>
                </div>
                <span class="text-white font-mono font-bold tracking-wider">099 1234 5678</span>
            </div>
        </div>

        <!-- Upload Form -->
        <form action="<?= get_url('app', '/?module=shop&page=process_checkout') ?>" method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="sec_bind" value="<?= esc($system_bind) ?>">
            <input type="hidden" name="art_id" value="<?= esc($artifact['art_id']) ?>">
            <input type="hidden" name="pay_method" value="KBZPay">

            <p class="text-xs text-gray-400 uppercase tracking-wider font-bold mb-1">2. Verify Transaction</p>
            
            <div>
                <div class="relative">
                    <i class="ph ph-hash absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                    <input type="text" name="txn_last_6" placeholder="Last 6 Digits of TXN ID" required maxlength="6" pattern="\d{6}" title="Must be exactly 6 digits" class="glass-input w-full rounded-xl pl-11 pr-4 py-3 text-white font-mono tracking-wider placeholder-gray-600">
                </div>
            </div>

            <div>
                <input type="file" name="proof_image" accept="image/png, image/jpeg" required class="w-full glass-input rounded-xl px-4 py-3 text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:uppercase file:bg-blue-600 file:text-white hover:file:bg-blue-500 cursor-pointer transition-all">
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 rounded-xl transition-all shadow-[0_0_20px_rgba(37,99,235,0.3)] hover:shadow-[0_0_30px_rgba(37,99,235,0.5)] flex justify-center items-center gap-2 mt-4">
                Submit TXN Proof <i class="ph-bold ph-upload-simple"></i>
            </button>
        </form>

    </div>

    <!-- The Quantum Timer Script -->
    <script>
        let time = 600; // 10 minutes
        const timerElement = document.getElementById('quantum-timer');

        const interval = setInterval(() => {
            let minutes = Math.floor(time / 60);
            let seconds = time % 60;
            
            seconds = seconds < 10 ? '0' + seconds : seconds;
            timerElement.textContent = `${minutes}:${seconds}`;
            
            if (time <= 0) {
                clearInterval(interval);
                timerElement.textContent = "00:00";
                timerElement.classList.remove('text-white');
                timerElement.classList.add('text-red-500');
                
                // Session Shatters - redirect to dashboard
                setTimeout(() => {
                    window.location.href = "<?= get_url('app', '/?module=dashboard&page=home&error=Checkout+session+expired.') ?>";
                }, 2000);
            }
            time--;
        }, 1000);
    </script>
</body>
</html>