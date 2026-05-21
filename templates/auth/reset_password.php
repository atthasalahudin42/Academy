<?php
session_start();

if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}

$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Academy.AI Premium</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-deep: #0B0F1A;
            --accent-primary: #6366F1;
            --accent-secondary: #A855F7;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: #0B0F1A;
            color: #E2E8F0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            overflow-y: auto;
            padding: 40px 20px;
        }

        .mesh-gradient {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(168, 85, 247, 0.1) 0px, transparent 50%);
            z-index: -1;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .input-field {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .input-field:focus {
            border-color: var(--accent-primary);
            background: rgba(255, 255, 255, 0.05);
            box-shadow: 0 0 15px rgba(99, 102, 241, 0.2);
        }

        .premium-btn {
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .premium-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
            filter: brightness(1.1);
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
    </style>
    <script>
        function togglePassword(id, btn) {
            const input = document.getElementById(id);
            const svgs = btn.querySelectorAll('svg');
            if (input.type === 'password') {
                input.type = 'text';
                svgs[0].classList.add('hidden');
                svgs[1].classList.remove('hidden');
            } else {
                input.type = 'password';
                svgs[0].classList.remove('hidden');
                svgs[1].classList.add('hidden');
            }
        }
    </script>
</head>
<body>

    <div class="mesh-gradient"></div>

    <div class="w-full max-w-5xl px-4 sm:px-6 flex flex-col md:flex-row items-center gap-8 md:gap-12 z-10">
        
        <!-- Left Side: Branding -->
        <div class="flex-1 text-center md:text-left">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-indigo-600 rounded-[1.25rem] sm:rounded-3xl flex items-center justify-center font-bold text-2xl sm:text-3xl mb-6 sm:mb-8 shadow-2xl shadow-indigo-600/40 mx-auto md:mx-0 animate-float">A</div>
            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold tracking-tight mb-4 leading-tight">Secure Your<br><span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-indigo-400">Future.</span></h1>
            <p class="text-gray-400 text-base sm:text-lg max-w-md mx-auto md:mx-0 leading-relaxed">Choose a strong password containing letters, numbers, and symbols to ensure your Academy.AI account remains perfectly secure.</p>
        </div>

        <!-- Right Side: Reset Password Form -->
        <div class="w-full max-w-md">
            <div class="glass-card rounded-[2rem] sm:rounded-[2.5rem] p-6 sm:p-10 md:p-12 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-600/10 rounded-full blur-3xl -mr-16 -mt-16"></div>
                
                <h2 class="text-2xl sm:text-3xl font-bold mb-2">Reset Password</h2>
                <p class="text-gray-500 mb-8 font-medium">Create a strong new password.</p>

                <?php if ($error): ?>
                    <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-2xl mb-6 text-sm font-medium flex items-center">
                        <span class="mr-2">⚠️</span> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-2xl mb-6 text-sm font-medium flex items-center">
                        <span class="mr-2">✅</span> <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <form action="../../backend/reset_password.php" method="POST" class="space-y-5">
                    
                    <div class="space-y-2 relative">
                        <label class="text-sm font-bold text-gray-400 ml-1 uppercase tracking-widest block">New Password</label>
                        <div class="relative">
                            <input type="password" name="password" id="reset-password1" required minlength="6" placeholder="••••••••"
                                class="input-field w-full px-6 py-4 pr-14 rounded-2xl outline-none text-white font-medium">
                            <button type="button" onclick="togglePassword('reset-password1', this)" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white transition-colors">
                                <!-- Eye Closed -->
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.878L3 3m6.878 6.878L19.8 19.8"/>
                                </svg>
                                <!-- Eye Open (Hidden by default) -->
                                <svg class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7 0 7-4.478 0 8.268 2.943 9.542-7 1.274-4.057 5.064-7 0-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-2 relative">
                        <label class="text-sm font-bold text-gray-400 ml-1 uppercase tracking-widest block">Confirm Password</label>
                        <div class="relative">
                            <input type="password" name="confirm_password" id="reset-password2" required minlength="6" placeholder="••••••••"
                                class="input-field w-full px-6 py-4 pr-14 rounded-2xl outline-none text-white font-medium">
                            <button type="button" onclick="togglePassword('reset-password2', this)" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white transition-colors">
                                <!-- Eye Closed -->
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.878L3 3m6.878 6.878L19.8 19.8"/>
                                </svg>
                                <!-- Eye Open (Hidden by default) -->
                                <svg class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7 0 7-4.478 0 8.268 2.943 9.542-7 1.274-4.057 5.064-7 0-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="premium-btn w-full py-5 rounded-2xl font-bold text-lg shadow-xl shadow-indigo-600/20 mt-4 active:scale-95">
                        Reset Password
                    </button>
                </form>

                <p class="text-center mt-8 text-gray-500 font-medium">
                    Remember your password? 
                    <a href="login.php" class="text-indigo-400 font-bold hover:text-indigo-300 ml-1">Sign In</a>
                </p>
            </div>
        </div>
    </div>

    <footer class="fixed bottom-8 text-[10px] uppercase tracking-[0.4em] text-gray-600 font-bold hidden sm:block">
        Academy.AI Premium Experience
    </footer>

</body>
</html>
