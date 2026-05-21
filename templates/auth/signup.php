<?php
session_start();
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | Academy.AI Premium</title>
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
            padding: 40px 20px;
        }

        .mesh-gradient {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: 
                radial-gradient(at 100% 0%, rgba(99, 102, 241, 0.15) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(168, 85, 247, 0.1) 0px, transparent 50%);
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
    </style>
</head>
<body>

    <div class="mesh-gradient"></div>

    <div class="w-full max-w-5xl px-4 sm:px-6 flex flex-col md:flex-row items-center gap-8 md:gap-12 z-10">
        
        <!-- Left Side: Branding (reversed for signup) -->
        <div class="flex-1 text-center md:text-left order-2 md:order-1">
            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold tracking-tight mb-4 leading-tight">Elevate Your<br><span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-indigo-400">Potential.</span></h1>
            <p class="text-gray-400 text-base sm:text-lg max-w-md mx-auto md:mx-0 leading-relaxed">Create your free account today and unlock personalized AI study plans, interactive quizzes, and deep performance analytics.</p>
            
            <div class="mt-6 sm:mt-8 space-y-3 sm:space-y-4">
                <div class="flex items-center space-x-4 bg-white/5 p-3 sm:p-4 rounded-2xl border border-white/5">
                    <span class="text-xl sm:text-2xl">✨</span>
                    <p class="text-xs sm:text-sm font-medium">Smart AI Explanations</p>
                </div>
                <div class="flex items-center space-x-4 bg-white/5 p-3 sm:p-4 rounded-2xl border border-white/5">
                    <span class="text-xl sm:text-2xl">🎯</span>
                    <p class="text-xs sm:text-sm font-medium">Topic-specific Quizzes</p>
                </div>
            </div>
        </div>

        <!-- Right Side: Signup Form -->
        <div class="w-full max-w-md order-1 md:order-2">
            <div class="glass-card rounded-[2rem] sm:rounded-[2.5rem] p-6 sm:p-10 md:p-12">
                <h2 class="text-3xl font-bold mb-2">Get Started</h2>
                <p class="text-gray-500 mb-8 font-medium">Start your learning journey now.</p>

                <?php if ($error): ?>
                    <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-2xl mb-6 text-sm font-medium flex items-center">
                        <span class="mr-2">⚠️</span> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form action="../../backend/signup_process.php" method="POST" class="space-y-5">
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-gray-400 ml-1 uppercase tracking-widest">Full Name</label>
                        <input type="text" name="name" required placeholder="John Doe"
                            class="input-field w-full px-6 py-4 rounded-2xl outline-none text-white font-medium">
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-bold text-gray-400 ml-1 uppercase tracking-widest">Email Address</label>
                        <input type="email" name="email" required placeholder="name@example.com"
                            class="input-field w-full px-6 py-4 rounded-2xl outline-none text-white font-medium">
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-bold text-gray-400 ml-1 uppercase tracking-widest">Password</label>
                        <input type="password" name="password" required placeholder="••••••••"
                            class="input-field w-full px-6 py-4 rounded-2xl outline-none text-white font-medium">
                    </div>

                    <div class="flex items-start space-x-3 ml-1 py-2">
                        <input type="checkbox" required class="mt-1 w-4 h-4 rounded border-gray-700 bg-gray-800 text-indigo-600 focus:ring-indigo-500">
                        <p class="text-xs text-gray-500 leading-tight">By signing up, you agree to our <a href="#" class="text-indigo-400 font-bold">Terms</a> and <a href="#" class="text-indigo-400 font-bold">Privacy Policy</a>.</p>
                    </div>

                    <button type="submit" class="premium-btn w-full py-5 rounded-2xl font-bold text-lg shadow-xl shadow-indigo-600/20 mt-2 active:scale-95">
                        Create Free Account
                    </button>
                </form>

                <p class="text-center mt-8 text-gray-500 font-medium">
                    Already using Academy.AI? 
                    <a href="login.php" class="text-indigo-400 font-bold hover:text-indigo-300 ml-1">Sign In</a>
                </p>
            </div>
        </div>
    </div>

</body>
</html>
