<?php
session_start();

if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - AI Study Helper</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="h-full min-h-screen font-sans bg-gradient-to-b from-[#142047] to-[#0F0E0E] overflow-hidden">

<div class="flex min-h-screen">

    <!-- Left Side -->
    <div class="hidden md:flex w-1/2 bg-gradient-to-b from-[#142047] to-[#0F0E0E] items-center justify-center relative">
        <img src="../../assets/images/robot.png" alt="Robot" class="w-3/4 max-w-lg object-contain shadow-2xl rounded-2xl">
    </div>

    <!-- Verify OTP Form -->
    <div class="flex w-full md:w-1/2 items-center justify-center px-6 py-12">

        <div class="bg-black/40 backdrop-blur-md rounded-2xl shadow-xl p-10 w-full max-w-md">

            <h2 class="text-3xl font-bold text-white text-center mb-2">Verify OTP</h2>
            <p class="text-gray-400 text-center mb-6 text-sm">Enter the 6-digit code sent to your email</p>

            <?php if($error): ?>
            <div class="bg-red-500/20 border border-red-500 text-red-400 px-4 py-2 rounded-lg mb-4 text-sm text-center">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <form action="../../backend/verify_otp.php" method="POST" class="space-y-4">

                <div>
                    <input type="text" name="otp" placeholder="Enter 6-digit OTP" maxlength="6" required
                        class="w-full px-4 py-2 rounded-lg bg-gray-800 text-white text-center text-2xl tracking-widest focus:ring-2 focus:ring-blue-500">
                </div>

                <button type="submit"
                    class="w-full bg-gradient-to-r from-blue-950 to-purple-950 text-white py-2 rounded-lg font-semibold">
                    Verify OTP
                </button>

            </form>

            <div class="mt-4 text-center text-gray-400 text-sm">
                <p>Didn't receive the code?</p>
                <form action="../../backend/send_otp.php" method="POST" class="mt-2">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($_SESSION['reset_email']); ?>">
                    <input type="hidden" name="resend" value="1">
                    <button type="submit" class="text-blue-500 hover:underline">
                        Resend OTP
                    </button>
                </form>
            </div>

            <p class="mt-4 text-center text-gray-400 text-sm">
                <a href="forgot_password.php" class="text-blue-500 hover:underline">Start Over</a>
            </p>

        </div>
    </div>

</div>

</body>
</html>
