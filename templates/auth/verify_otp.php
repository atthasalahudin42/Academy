<?php
session_start();
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - AI Study Helper</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-b from-[#142047] to-[#0F0E0E] flex items-center justify-center p-6">
    <div class="bg-black/40 backdrop-blur-md rounded-2xl shadow-xl p-10 w-full max-w-md text-center">
        <img src="../../assets/images/robot.png" alt="Robot" class="w-24 h-24 mx-auto mb-6">
        
        <?php if ($success): ?>
            <div class="bg-green-500/20 border border-green-500 text-green-400 p-6 rounded-lg mb-6">
                <h2 class="text-2xl font-bold text-green-400 mb-2">✅ Verified!</h2>
                <p><?php echo htmlspecialchars($success); ?></p>
            </div>
            <a href="login.php" class="bg-gradient-to-r from-green-600 to-green-700 text-white py-3 px-6 rounded-lg font-semibold hover:from-green-700 hover:to-green-800">
                Go to Login
            </a>
        <?php elseif ($error): ?>
            <div class="bg-red-500/20 border border-red-500 text-red-400 p-6 rounded-lg mb-6">
                <h2 class="text-2xl font-bold text-red-400 mb-2">❌ Error</h2>
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
            <a href="../index.php" class="bg-gradient-to-r from-blue-950 to-purple-950 text-white py-3 px-6 rounded-lg font-semibold">
                Back to Home
            </a>
        <?php else: ?>
            <h2 class="text-3xl font-bold text-white mb-4">Verifying...</h2>
            <p class="text-gray-400">Please wait...</p>
        <?php endif; ?>
    </div>
</body>
</html>