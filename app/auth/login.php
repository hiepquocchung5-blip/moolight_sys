<?php
/**
 * App Portal - Login UI (V3 Production)
 */
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
            border-color: #3B82F6; /* Cyber-blue focus */
            background: rgba(17, 24, 39, 0.9);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
            outline: none;
        }
        /* Custom scrollbar for browser autofill */
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
    <div class="absolute top-[-5%] right-[-5%] w-[800px] h-[800px] bg-blue-600/15 rounded-full blur-[120px] -z-10 animate-pulse" style="animation-duration: 5s;"></div>
    <div class="absolute bottom-[-10%] left-[-5%] w-[600px] h-[600px] bg-indigo-600/10 rounded-full blur-[100px] -z-10 animate-pulse" style="animation-duration: 7s;"></div>

    <div class="glass-panel p-8 sm:p-10 rounded-3xl w-full max-w-md relative z-10 mx-4">
        
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-gray-800 to-gray-900 shadow-inner border border-gray-700 mb-5 relative group">
                <div class="absolute inset-0 bg-blue-500/20 rounded-2xl blur group-hover:bg-blue-500/40 transition duration-500"></div>
                <i class="ph-fill ph-fingerprint text-3xl text-blue-400 relative z-10"></i>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-wide">Authenticate Entity</h1>
            <p class="text-gray-500 text-sm mt-2">Provide credentials to enter the Nexus.</p>
        </div>

        <?php if ($error_msg): ?>
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 text-sm p-4 rounded-xl mb-6 flex items-start gap-3 animate-fade-in">
                <i class="ph-fill ph-warning-circle text-lg mt-0.5"></i> 
                <span><?= esc($error_msg) ?></span>
            </div>
        <?php endif; ?>
        <?php if ($success_msg): ?>
            <div class="bg-green-500/10 border border-green-500/30 text-green-400 text-sm p-4 rounded-xl mb-6 flex items-start gap-3 animate-fade-in">
                <i class="ph-fill ph-check-circle text-lg mt-0.5"></i> 
                <span><?= esc($success_msg) ?></span>
            </div>
        <?php endif; ?>

        <!-- Form Action uses secure V1.1 URL Generator -->
        <form id="loginForm" action="<?= get_url('app', '/?module=auth&page=process_login') ?>" method="POST" class="space-y-5" onsubmit="return handleLogin()">
            
            <!-- Strict Security Bind Hash -->
            <input type="hidden" name="sec_bind" value="<?= esc($system_bind ?? '') ?>">

            <div class="space-y-1.5">
                <label class="text-[11px] font-bold text-gray-400 uppercase tracking-widest pl-1">Identity (Tag or Email)</label>
                <div class="relative group">
                    <i class="ph-fill ph-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-blue-400 transition-colors"></i>
                    <input type="text" id="identifier" name="identifier" required placeholder="e.g. Cyber_Wolf99" class="glass-input w-full rounded-xl pl-11 pr-4 py-3.5 text-white placeholder-gray-600">
                </div>
            </div>

            <div class="space-y-1.5">
                <div class="flex justify-between items-center pl-1 pr-1">
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Passcode</label>
                    <a href="#" class="text-[11px] text-gray-500 hover:text-blue-400 uppercase tracking-widest font-bold transition-colors">Lost Link?</a>
                </div>
                <div class="relative group">
                    <i class="ph-fill ph-lock-key absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-blue-400 transition-colors"></i>
                    <input type="password" id="login_password" name="password" required placeholder="••••••••" class="glass-input w-full rounded-xl pl-11 pr-10 py-3.5 text-white placeholder-gray-600">
                    <button type="button" onclick="togglePassword('login_password', 'eye_login')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300 transition-colors">
                        <i id="eye_login" class="ph ph-eye"></i>
                    </button>
                </div>
            </div>
            
            <button type="submit" id="submitBtn" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 rounded-xl transition-all shadow-[0_0_20px_rgba(37,99,235,0.3)] hover:shadow-[0_0_30px_rgba(37,99,235,0.5)] flex justify-center items-center gap-2 mt-6 relative overflow-hidden group">
                <span id="btnText" class="relative z-10">Establish Connection</span>
                <i id="btnIcon" class="ph-bold ph-arrow-right relative z-10 transition-transform group-hover:translate-x-1"></i>
            </button>
        </form>

        <div class="mt-6 flex items-center justify-between">
            <span class="w-1/5 border-b border-gray-700"></span>
            <span class="text-[10px] text-gray-500 uppercase tracking-widest font-bold px-2">Fast Connect</span>
            <span class="w-1/5 border-b border-gray-700"></span>
        </div>

        <div class="mt-6 space-y-3">
            <a href="<?= get_url('app', '/?module=auth&page=oauth_redirect&provider=google&sec_bind=' . esc($system_bind ?? '')) ?>" class="w-full border border-gray-700 hover:bg-gray-800 text-white py-3 rounded-xl flex items-center justify-center gap-3 transition-all shadow-sm">
                <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" class="w-5 h-5"> Continue with Google
            </a>

            <a href="<?= get_url('app', '/?module=auth&page=oauth_redirect&provider=telegram&sec_bind=' . esc($system_bind ?? '')) ?>" class="w-full border border-blue-900 bg-blue-500/10 hover:bg-blue-500/20 text-blue-400 py-3 rounded-xl flex items-center justify-center gap-3 transition-all shadow-sm">
                <i class="ph-fill ph-telegram-logo text-xl"></i> Continue with Telegram
            </a>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-800/50 text-center">
            <p class="text-sm text-gray-400">
                Not mapped in the system? 
                <a href="<?= get_url('app', '/?module=auth&page=register&sec_bind=' . esc($system_bind ?? '')) ?>" class="text-blue-400 hover:text-blue-300 font-bold transition-colors">Initialize Entity</a>
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

        // Loading State & Pre-flight check
        function handleLogin() {
            const identifier = document.getElementById('identifier').value;
            const pwd = document.getElementById('login_password').value;

            if (!identifier || !pwd) {
                return false; // Let HTML5 validation handle empty fields
            }

            const btn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const btnIcon = document.getElementById('btnIcon');
            
            // Lock the button to prevent duplicate submissions
            btn.classList.add('opacity-80', 'cursor-not-allowed', 'pointer-events-none');
            
            // Swap Text and Icon to show loading
            btnText.innerText = 'Verifying Signature...';
            btnIcon.className = 'ph-bold ph-spinner animate-spin relative z-10 text-lg';

            return true; // Allow form submission
        }
    </script>
</body>
</html>