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
    <title>Initialize Entity | MoonAccount</title>
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
            background: rgba(17, 24, 39, 0.65);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
        }
        .glass-input {
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        .glass-input:focus {
            border-color: #A855F7; /* Cyber-purple focus */
            background: rgba(17, 24, 39, 0.9);
            box-shadow: 0 0 0 4px rgba(168, 85, 247, 0.15);
            outline: none;
        }
        /* Custom scrollbar for autofill */
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus, 
        input:-webkit-autofill:active{
            -webkit-box-shadow: 0 0 0 30px #0f172a inset !important;
            -webkit-text-fill-color: white !important;
            transition: background-color 5000s ease-in-out 0s;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen relative overflow-hidden cyber-grid font-sans py-12">

    <!-- Ambient Animated Background -->
    <div class="absolute bottom-[-10%] right-[-5%] w-[800px] h-[800px] bg-purple-600/15 rounded-full blur-[120px] -z-10 animate-pulse" style="animation-duration: 6s;"></div>
    <div class="absolute top-[-5%] left-[-5%] w-[500px] h-[500px] bg-blue-600/10 rounded-full blur-[100px] -z-10 animate-pulse" style="animation-duration: 4s;"></div>

    <div class="glass-panel p-8 sm:p-10 rounded-3xl w-full max-w-md relative z-10 mx-4">
        
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-gray-800 to-gray-900 shadow-inner border border-gray-700 mb-5 relative group">
                <div class="absolute inset-0 bg-purple-500/20 rounded-2xl blur group-hover:bg-purple-500/40 transition duration-500"></div>
                <i class="ph-fill ph-user-plus text-3xl text-purple-400 relative z-10"></i>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-wide">Initialize Entity</h1>
            <p class="text-gray-500 text-sm mt-2">Create your MoonAccount to enter the Nexus.</p>
        </div>

        <?php if ($error_msg): ?>
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 text-sm p-4 rounded-xl mb-6 flex items-start gap-3 animate-fade-in">
                <i class="ph-fill ph-warning-circle text-lg mt-0.5"></i> 
                <span><?= esc($error_msg) ?></span>
            </div>
        <?php endif; ?>

        <!-- Form Action uses secure V1.1 URL Generator -->
        <form id="registerForm" action="<?= get_url('app', '/?module=auth&page=process_register') ?>" method="POST" class="space-y-4" onsubmit="return handleRegistration()">
            
            <!-- Strict Security Bind Hash -->
            <input type="hidden" name="sec_bind" value="<?= esc($system_bind ?? '') ?>">

            <div class="space-y-1.5">
                <label class="text-[11px] font-bold text-gray-400 uppercase tracking-widest pl-1">Moon Tag</label>
                <div class="relative group">
                    <i class="ph-fill ph-identification-badge absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-purple-400 transition-colors"></i>
                    <input type="text" name="moon_tag" required minlength="3" maxlength="20" placeholder="e.g. Cyber_Wolf99" class="glass-input w-full rounded-xl pl-11 pr-4 py-3.5 text-white placeholder-gray-600">
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-[11px] font-bold text-gray-400 uppercase tracking-widest pl-1">Comm Link (Email)</label>
                <div class="relative group">
                    <i class="ph-fill ph-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-purple-400 transition-colors"></i>
                    <input type="email" name="email" required placeholder="you@network.com" class="glass-input w-full rounded-xl pl-11 pr-4 py-3.5 text-white placeholder-gray-600">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-widest pl-1">Passcode</label>
                    <div class="relative group">
                        <i class="ph-fill ph-lock-key absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-purple-400 transition-colors"></i>
                        <input type="password" id="reg_password" name="password" required minlength="8" placeholder="••••••••" onkeyup="checkPasswordMatch()" class="glass-input w-full rounded-xl pl-11 pr-10 py-3.5 text-white placeholder-gray-600">
                        <button type="button" onclick="togglePassword('reg_password', 'eye1')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300 transition-colors">
                            <i id="eye1" class="ph ph-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-widest pl-1">Confirm</label>
                    <div class="relative group">
                        <i id="confirm_icon" class="ph-fill ph-check-circle absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-purple-400 transition-colors"></i>
                        <input type="password" id="reg_confirm" name="password_confirm" required minlength="8" placeholder="••••••••" onkeyup="checkPasswordMatch()" class="glass-input w-full rounded-xl pl-11 pr-10 py-3.5 text-white placeholder-gray-600">
                        <button type="button" onclick="togglePassword('reg_confirm', 'eye2')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300 transition-colors">
                            <i id="eye2" class="ph ph-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <p id="match_error" class="text-xs text-red-400 hidden pl-1 animate-pulse">Passcodes do not match.</p>
            
            <button type="submit" id="submitBtn" class="w-full bg-purple-600 hover:bg-purple-500 text-white font-bold py-4 rounded-xl transition-all shadow-[0_0_20px_rgba(147,51,234,0.3)] hover:shadow-[0_0_30px_rgba(147,51,234,0.5)] flex justify-center items-center gap-2 mt-6 relative overflow-hidden group">
                <span id="btnText" class="relative z-10">Establish Connection</span>
                <i id="btnIcon" class="ph-bold ph-arrow-right relative z-10 transition-transform group-hover:translate-x-1"></i>
            </button>
        </form>

        <div class="mt-8 flex items-center justify-between opacity-60 hover:opacity-100 transition-opacity">
            <span class="w-1/4 border-b border-gray-700"></span>
            <span class="text-[10px] text-gray-400 uppercase tracking-widest font-bold px-2">Fast Connect</span>
            <span class="w-1/4 border-b border-gray-700"></span>
        </div>

        <div class="mt-6 grid grid-cols-2 gap-4">
            <!-- Google OAuth -->
            <a href="<?= get_url('app', '/?module=auth&page=oauth_redirect&provider=google&sec_bind=' . esc($system_bind ?? '')) ?>" class="glass-input hover:bg-white/5 flex items-center justify-center gap-2 py-3 rounded-xl transition-all group">
                <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" class="w-4 h-4 group-hover:scale-110 transition-transform">
                <span class="text-xs font-bold text-gray-300 group-hover:text-white">Google</span>
            </a>

            <!-- Telegram Widget -->
            <a href="<?= get_url('app', '/?module=auth&page=oauth_redirect&provider=telegram&sec_bind=' . esc($system_bind ?? '')) ?>" class="glass-input hover:bg-[#229ED9]/10 border hover:border-[#229ED9]/40 flex items-center justify-center gap-2 py-3 rounded-xl transition-all group">
                <i class="ph-fill ph-telegram-logo text-[#229ED9] text-lg group-hover:scale-110 transition-transform"></i>
                <span class="text-xs font-bold text-[#229ED9] group-hover:text-purple-400">Telegram</span>
            </a>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-800/50 text-center">
            <p class="text-sm text-gray-400">
                Already verified? 
                <a href="<?= get_url('app', '/?module=auth&page=login&sec_bind=' . esc($system_bind ?? '')) ?>" class="text-purple-400 hover:text-purple-300 font-bold transition-colors">Authenticate here</a>
            </p>
        </div>
    </div>

    <!-- UI/UX Scripts -->
    <script>
        // Password Visibility Toggle
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('ph-eye', 'ph-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('ph-eye-slash', 'ph-eye');
            }
        }

        // Real-time Password Matching Validation
        function checkPasswordMatch() {
            const pwd = document.getElementById('reg_password').value;
            const confirm = document.getElementById('reg_confirm').value;
            const errorText = document.getElementById('match_error');
            const confirmIcon = document.getElementById('confirm_icon');

            if (confirm.length > 0) {
                if (pwd === confirm) {
                    errorText.classList.add('hidden');
                    confirmIcon.classList.replace('text-gray-500', 'text-green-500');
                    confirmIcon.classList.replace('group-focus-within:text-purple-400', 'text-green-500');
                } else {
                    errorText.classList.remove('hidden');
                    confirmIcon.classList.replace('text-green-500', 'text-red-500');
                }
            } else {
                errorText.classList.add('hidden');
                confirmIcon.classList.replace('text-green-500', 'text-gray-500');
                confirmIcon.classList.replace('text-red-500', 'text-gray-500');
            }
        }

        // Loading State & Pre-flight check
        function handleRegistration() {
            const pwd = document.getElementById('reg_password').value;
            const confirm = document.getElementById('reg_confirm').value;

            if (pwd !== confirm) {
                document.getElementById('match_error').classList.remove('hidden');
                return false; // Prevent form submission
            }

            const btn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const btnIcon = document.getElementById('btnIcon');
            
            // Lock the button to prevent duplicate submissions
            btn.classList.add('opacity-80', 'cursor-not-allowed', 'pointer-events-none');
            
            // Swap Text and Icon
            btnText.innerText = 'Transmitting...';
            btnIcon.className = 'ph-bold ph-spinner animate-spin relative z-10 text-lg';

            return true; // Allow form submission
        }
    </script>
</body>
</html>