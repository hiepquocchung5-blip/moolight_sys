<?php
// Enforce Router Inclusion
if (!isset($active_portal) || $active_portal !== 'app') die("Direct access strictly prohibited.");
$error_msg = $_GET['error'] ?? null;
$success_msg = $_GET['success'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Access | MoonAccount</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        body { background-color: #030712; color: #E2E8F0; }
        .cyber-grid {
            background-size: 40px 40px;
            background-image: linear-gradient(to right, rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                              linear-gradient(to bottom, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
        }
        .glass-panel {
            background: rgba(17, 24, 39, 0.6);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
        }
        .glass-input {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        .glass-input:focus {
            border-color: #3B82F6;
            background: rgba(17, 24, 39, 0.8);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
            outline: none;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen relative overflow-hidden cyber-grid font-sans">

    <!-- Ambient Animated Background -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[1000px] h-[500px] bg-blue-600/20 rounded-full blur-[120px] -z-10 animate-pulse" style="animation-duration: 4s;"></div>
    <div class="absolute bottom-0 right-0 w-[600px] h-[600px] bg-purple-600/10 rounded-full blur-[100px] -z-10"></div>

    <div class="glass-panel p-8 sm:p-10 rounded-3xl w-full max-w-md relative z-10 mx-4">
        
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-gray-800 to-gray-900 shadow-inner border border-gray-700 mb-5 relative group">
                <div class="absolute inset-0 bg-blue-500/20 rounded-2xl blur group-hover:bg-blue-500/30 transition"></div>
                <i class="ph-fill ph-moon-stars text-3xl text-blue-400 relative z-10"></i>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-wide">Welcome to Moon<span class="text-blue-500">Account</span></h1>
            <p class="text-gray-500 text-sm mt-2">Authenticate to access your artifacts.</p>
        </div>

        <?php if ($error_msg): ?>
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 text-sm p-4 rounded-xl mb-6 flex items-start gap-3">
                <i class="ph-fill ph-warning-circle text-lg mt-0.5"></i> 
                <span><?= esc($error_msg) ?></span>
            </div>
        <?php endif; ?>
        <?php if ($success_msg): ?>
            <div class="bg-green-500/10 border border-green-500/30 text-green-400 text-sm p-4 rounded-xl mb-6 flex items-start gap-3">
                <i class="ph-fill ph-check-circle text-lg mt-0.5"></i> 
                <span><?= esc($success_msg) ?></span>
            </div>
        <?php endif; ?>

        <form action="<?= get_url('app', '/?module=auth&page=process_login') ?>" method="POST" class="space-y-5" onsubmit="showLoading()">
            <!-- Security Bind Hash -->
            <input type="hidden" name="sec_bind" value="<?= esc($system_bind ?? '') ?>">

            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider pl-1">Identity</label>
                <div class="relative group">
                    <i class="ph ph-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-blue-400 transition-colors"></i>
                    <input type="text" name="identifier" required placeholder="Email or Moon_Tag" class="glass-input w-full rounded-xl pl-11 pr-4 py-3.5 text-white placeholder-gray-600">
                </div>
            </div>

            <div class="space-y-1.5">
                <div class="flex justify-between items-center pl-1 pr-1">
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Passcode</label>
                    <a href="#" class="text-xs text-blue-400 hover:text-blue-300 transition-colors">Recover?</a>
                </div>
                <div class="relative group">
                    <i class="ph ph-lock-key absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-blue-400 transition-colors"></i>
                    <input type="password" id="password" name="password" required placeholder="••••••••" class="glass-input w-full rounded-xl pl-11 pr-12 py-3.5 text-white placeholder-gray-600">
                    <button type="button" onclick="togglePassword('password', 'eyeIcon')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300 focus:outline-none">
                        <i id="eyeIcon" class="ph ph-eye text-lg transition-colors"></i>
                    </button>
                </div>
            </div>
            
            <button type="submit" id="loginBtn" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3.5 rounded-xl transition-all shadow-[0_0_20px_rgba(37,99,235,0.2)] hover:shadow-[0_0_25px_rgba(37,99,235,0.4)] flex justify-center items-center gap-2 mt-4">
                <span id="btnText">Initiate Connection</span> <i id="btnIcon" class="ph-bold ph-arrow-right"></i>
            </button>
        </form>

        <div class="mt-8 flex items-center justify-between">
            <span class="w-1/4 border-b border-gray-700/50"></span>
            <span class="text-[10px] text-gray-500 uppercase tracking-widest font-semibold px-2">Or Continue With</span>
            <span class="w-1/4 border-b border-gray-700/50"></span>
        </div>

        <div class="mt-6 grid grid-cols-2 gap-4">
            <!-- Google OAuth -->
            <a href="<?= get_url('app', '/?module=auth&page=oauth_redirect&provider=google&sec_bind=' . ($system_bind ?? '')) ?>" class="glass-input hover:bg-white/5 flex items-center justify-center gap-2 py-3 rounded-xl transition-colors group">
                <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" class="w-5 h-5 group-hover:scale-110 transition-transform">
                <span class="text-sm font-semibold text-gray-300 group-hover:text-white">Google</span>
            </a>

            <!-- Telegram Widget -->
            <a href="<?= get_url('app', '/?module=auth&page=oauth_redirect&provider=telegram&sec_bind=' . ($system_bind ?? '')) ?>" class="glass-input hover:bg-[#229ED9]/10 border hover:border-[#229ED9]/30 flex items-center justify-center gap-2 py-3 rounded-xl transition-all group">
                <i class="ph-fill ph-telegram-logo text-[#229ED9] text-xl group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-semibold text-[#229ED9] group-hover:text-blue-400">Telegram</span>
            </a>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-800/50 text-center">
            <p class="text-sm text-gray-400">
                New to the Nexus? 
                <a href="<?= get_url('app', '/?module=auth&page=register&sec_bind=' . ($system_bind ?? '')) ?>" class="text-blue-400 hover:text-blue-300 font-bold transition-colors">Initialize Account</a>
            </p>
        </div>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('ph-eye');
                icon.classList.add('ph-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('ph-eye-slash');
                icon.classList.add('ph-eye');
            }
        }

        // Loading State UI Enhancer
        function showLoading() {
            const btn = document.getElementById('loginBtn');
            const btnText = document.getElementById('btnText');
            const btnIcon = document.getElementById('btnIcon');
            
            // Prevent multiple clicks
            btn.classList.add('opacity-80', 'cursor-not-allowed', 'pointer-events-none');
            
            // Swap Text and Icon
            btnText.innerText = 'Establishing Link...';
            btnIcon.className = 'ph ph-spinner animate-spin text-xl';
        }
    </script>
</body>
</html>