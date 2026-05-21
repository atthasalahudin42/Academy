<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';
use Dell7420\Academy\Database;

$db = new Database();
$conn = $db->connect();
$token = $_GET['token'] ?? '';

error_log("DEBUG verify_login - Token: " . $token . ", Server time: " . date('Y-m-d H:i:s'));

if (empty($token)) {
    die("Invalid link");
}

$current_time = date('Y-m-d H:i:s');
$stmt = $conn->prepare("
    SELECT * FROM users 
    WHERE login_token = ? 
    AND login_token_expiry > ?
");
$stmt->bind_param("ss", $token, $current_time);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    error_log("DEBUG verify_login FAILED - Token: " . $token . " NOW: " . date('Y-m-d H:i:s'));
    die("Invalid or expired login link.");
}

$user = $result->fetch_assoc();

// ✅ Login user via PHP session
$_SESSION['user_id']        = $user['id'];
$_SESSION['user_email']     = $user['email'];
$_SESSION['user_name']      = $user['name'];
$_SESSION['profile_picture'] = $user['profile_picture'] ?? 'avatar.png';

// Clear token
$update = $conn->prepare("
    UPDATE users 
    SET login_token = NULL, login_token_expiry = NULL 
    WHERE id = ?
");
$update->bind_param("i", $user['id']);
$update->execute();

// Pass user info to JS for JWT fetch
$user_email = htmlspecialchars($user['email']);
$user_name  = htmlspecialchars($user['name']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Logging you in...</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-b from-[#142047] to-[#0F0E0E] flex items-center justify-center text-white">
    <div class="text-center">
        <div class="text-4xl mb-4 animate-spin inline-block">⚙️</div>
        <p class="text-xl font-semibold">Logging you in as <span class="text-blue-400"><?php echo $user_name; ?></span>...</p>
        <p class="text-gray-400 mt-2 text-sm">Please wait while we set up your session.</p>
    </div>
    <script>
        // Fetch FastAPI JWT and store it, then redirect
        async function doLogin() {
            try {
                const res = await fetch('http://127.0.0.1:8001/auth/login-via-session', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email: '<?php echo $user_email; ?>', name: '<?php echo $user_name; ?>' })
                });
                if (res.ok) {
                    const data = await res.json();
                    if (data.access_token) {
                        localStorage.setItem('access_token', data.access_token);
                    }
                }
            } catch (e) {
                // FastAPI might not be running; that's okay, PHP session still works
                console.warn('FastAPI not reachable, continuing with PHP session only.');
            }
            window.location.href = '../templates/dashboard/main.php';
        }
        doLogin();
    </script>
</body>
</html>