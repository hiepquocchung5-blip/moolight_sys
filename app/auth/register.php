<?php
// Enforce Router Inclusion
if (!isset($active_portal) || $active_portal !== 'app') die("Direct access strictly prohibited.");
$error_msg = $_GET['error'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Initialize Account | MoonAccount</title>
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
            border-color: #A855F7; /* Purple focus for register to distinguish from login */
            background: rgba(17, 24, 39, 0.8);
            box-shadow: 0 0 0 4px rgba(168, 85, 247, 0.1);
            outline: none;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen relative overflow-hidden cyber-grid font-sans py-10">

    <!-- Ambient Animated Background -->
    <div class="absolute bottom-0 right-1/4 w-[800px] h-[800px] bg-purple-600/15 rounded-full blur-[120px] -z-10 animate-pulse" style="animation-duration: 5s;"></div>
    <div class="absolute top-0 left-0 w-[500px] h-[500px] bg-blue-600/10 rounded-full blur-[100px] -z-10"></div>

    <div class="glass-panel p-8 sm:p-10 rounded-3xl w-full max-w-md relative z-10 mx-4">
        
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-gray-800 to-gray-900 shadow-inner border border-gray-700 mb-5 relative group">
                <div class="absolute inset-0 bg-purple-500/20 rounded-2xl blur group-hover:bg-purple-500/30 transition"></div>
                <i class="ph-fill ph-user-plus text-3xl text-purple-400 relative z-10"></i>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-wide">Initialize Entity</h1>
            <p class="text-gray-500 text-sm mt-2">Join the Moonlight Digital Network.</p>
        </div>

        <?php if ($error_msg): ?>
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 text-sm p-4 rounded-xl mb-6 flex items-start gap-3">
                <i class="ph-fill ph-warning-circle text-lg mt-0.5"></i> 
                <span><?= esc($error_msg) ?></span>
            </div>
        <?php endif; ?>

        <form action="<?= get_url('app', '/?module=auth&page=process_register') ?>" method="POST" class="space-y-4">
            <!-- Security Bind Hash -->
            <input type="hidden" name="sec_bind" value="<?= esc($system_bind ?? '') ?>">

            <div class="space-y-1.5">
                <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wider pl-1">Moon Tag</label>
                <div class="relative group">
                    <i class="ph ph-identification-badge absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-purple-400 transition-colors"></i>
                    <input type="text" name="moon_tag" required placeholder="e.g. Cyber_Wolf99" class="glass-input w-full rounded-xl pl-11 pr-4 py-3 text-white placeholder-gray-600">
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wider pl-1">Comm Link (Email)</label>
                <div class="relative group">
                    <i class="ph ph-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-purple-400 transition-colors"></i>
                    <input type="email" name="email" required placeholder="you@network.com" class="glass-input w-full rounded-xl pl-11 pr-4 py-3 text-white placeholder-gray-600">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wider pl-1">Passcode</label>
                    <div class="relative group">
                        <i class="ph ph-lock-key absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-purple-400 transition-colors"></i>
                        <input type="password" id="reg_password" name="password" required placeholder="••••••••" class="glass-input w-full rounded-xl pl-11 pr-10 py-3 text-white placeholder-gray-600">
                        <button type="button" onclick="togglePassword('reg_password', 'eye1')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300">
                            <i id="eye1" class="ph ph-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wider pl-1">Confirm</label>
                    <div class="relative group">
                        <i class="ph ph-check-circle absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-purple-400 transition-colors"></i>
                        <input type="password" id="reg_confirm" name="password_confirm" required placeholder="••••••••" class="glass-input w-full rounded-xl pl-11 pr-10 py-3 text-white placeholder-gray-600">
                        <button type="button" onclick="togglePassword('reg_confirm', 'eye2')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300">
                            <i id="eye2" class="ph ph-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="w-full bg-purple-600 hover:bg-purple-500 text-white font-bold py-3.5 rounded-xl transition-all shadow-[0_0_20px_rgba(147,51,234,0.2)] hover:shadow-[0_0_25px_rgba(147,51,234,0.4)] mt-6">
                Establish Connection
            </button>
        </form>

        <div class="mt-8 flex items-center justify-between">
            <span class="w-1/4 border-b border-gray-700/50"></span>
            <span class="text-[10px] text-gray-500 uppercase tracking-widest font-semibold px-2">Fast Connect</span>
            <span class="w-1/4 border-b border-gray-700/50"></span>
        </div>

        <div class="mt-6 grid grid-cols-2 gap-4">
            <!-- Google OAuth -->
            <a href="<?= get_url('app', '/?module=auth&page=oauth_redirect&provider=google&sec_bind=' . ($system_bind ?? '')) ?>" class="glass-input hover:bg-white/5 flex items-center justify-center gap-2 py-2.5 rounded-xl transition-colors group">
                <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" class="w-4 h-4 group-hover:scale-110 transition-transform">
                <span class="text-xs font-semibold text-gray-300 group-hover:text-white">Google</span>
            </a>

            <!-- Telegram Widget -->
            <a href="<?= get_url('app', '/?module=auth&page=oauth_redirect&provider=telegram&sec_bind=' . ($system_bind ?? '')) ?>" class="glass-input hover:bg-[#229ED9]/10 border hover:border-[#229ED9]/30 flex items-center justify-center gap-2 py-2.5 rounded-xl transition-all group">
                <i class="ph-fill ph-telegram-logo text-[#229ED9] text-lg group-hover:scale-110 transition-transform"></i>
                <span class="text-xs font-semibold text-[#229ED9] group-hover:text-purple-400">Telegram</span>
            </a>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-800/50 text-center">
            <p class="text-sm text-gray-400">
                Already verified? 
                <a href="<?= get_url('app', '/?module=auth&page=login&sec_bind=' . ($system_bind ?? '')) ?>" class="text-purple-400 hover:text-purple-300 font-bold transition-colors">Authenticate here</a>
            </p>
        </div>
    </div>

    <!-- Password Toggle Script -->
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
    </script>
</body>
</html>