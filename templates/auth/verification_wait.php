<?php
session_start();
if (!isset($_SESSION['pending_email']) && !isset($_SESSION['success'])) {
    header("Location: login.php");
    exit();
}
$email = $_SESSION['pending_email'] ?? 'your email';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email | Academy.AI Premium</title>
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
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .mesh-gradient {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: 
                radial-gradient(at 50% 50%, rgba(99, 102, 241, 0.15) 0px, transparent 50%);
            z-index: -1;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .premium-btn {
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .premium-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
        }

        .pulse-icon {
            animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse-ring {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.7; }
        }
    </style>
</head>
<body>

    <div class="mesh-gradient"></div>

    <div class="w-full max-w-md px-6 z-10">
        <div class="glass-card rounded-[3rem] p-12 text-center">
            
            <div class="w-24 h-24 bg-indigo-600/10 rounded-[2.5rem] flex items-center justify-center text-5xl mx-auto mb-8 pulse-icon">📩</div>
            
            <h2 class="text-3xl font-bold mb-4">Check your email</h2>
            <p class="text-gray-400 mb-2 font-medium">We've sent a verification link to:</p>
            <p class="text-indigo-400 font-bold mb-8 break-all"><?php echo htmlspecialchars($email); ?></p>
            
            <div class="space-y-4">
                <form action="../../backend/verify_self.php" method="POST">
                    <button type="submit" class="premium-btn w-full py-5 rounded-2xl font-bold text-lg shadow-xl shadow-indigo-600/20 active:scale-95">
                        I've Verified My Email
                    </button>
                </form>
                
                <p class="text-xs text-gray-500 pt-4 leading-relaxed">
                    Once you click the link in your email, come back here and click the button above to enter your premium dashboard.
                </p>
            </div>

            <div class="mt-12 pt-8 border-t border-white/5 space-y-3">
                <p class="text-sm text-gray-500">Didn't receive it?</p>
                <a href="../../backend/resend_verification.php" class="text-sm font-bold text-indigo-400 hover:text-indigo-300">Resend Verification Link</a>
                <br>
                <a href="login.php" class="text-xs text-gray-600 hover:text-gray-400">Back to Login</a>
            </div>
        </div>
    </div>

</body>
</html>
