<?php
session_start();
require_once __DIR__ . '/../src/Database.php';
use Dell7420\Academy\Database;
$db = new Database();
$conn = $db->connect();

if (!isset($_SESSION['pending_user_id'])) {
    header("Location: ../templates/auth/login.php");
    exit();
}

$user_id = $_SESSION['pending_user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // User clicked "Yes, it's me"
    $update = $conn->prepare("UPDATE users SET is_verified = 1 WHERE id = ?");
    $update->bind_param("i", $user_id);
    $update->execute();
    $update->close();

    // Fetch full user info like login_process.php
    $full_stmt = $conn->prepare("SELECT name, email, profile_picture FROM users WHERE id = ?");
    $full_stmt->bind_param("i", $user_id);
    $full_stmt->execute();
    $full_result = $full_stmt->get_result();
    $full_user = $full_result->fetch_assoc();
    $full_stmt->close();
    
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_email'] = $full_user['email'];
    $_SESSION['user_name'] = $full_user['name'];
    $_SESSION['profile_picture'] = $full_user['profile_picture'] ?? 'assets/images/avatar.png';

    // Clear pending session
    unset($_SESSION['pending_user_id']);

    header("Location: ../templates/dashboard/main.php");
    exit();
}

// Get user info for display
$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Identity - AI Study Helper</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; max-width: 500px; margin: 50px auto; padding: 20px; text-align: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .card { background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); border-radius: 20px; padding: 40px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); border: 1px solid rgba(255,255,255,0.2); }
        h2 { color: #1e293b; margin-bottom: 10px; font-size: 28px; }
        p { color: #64748b; margin-bottom: 30px; line-height: 1.6; }
        .buttons { display: flex; gap: 15px; justify-content: center; flex-wrap: wrap; }
        button { padding: 14px 32px; border: none; border-radius: 12px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; min-width: 140px; }
        .yes-btn { background: linear-gradient(135deg, #4F46E5, #7C3AED); color: white; box-shadow: 0 8px 25px rgba(79,70,229,0.4); }
        .yes-btn:hover { transform: translateY(-2px); box-shadow: 0 12px 35px rgba(79,70,229,0.5); }
        .no-btn { background: linear-gradient(135deg, #ef4444, #f87171); color: white; box-shadow: 0 8px 25px rgba(239,68,68,0.4); }
        .no-btn:hover { transform: translateY(-2px); box-shadow: 0 12px 35px rgba(239,68,68,0.5); }
        .footer { font-size: 14px; color: #94a3b8; margin-top: 25px; padding-top: 20px; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Hi <?= htmlspecialchars($user['name']) ?>!</h2>
        <p>Is this you? Confirm to complete email verification and enter dashboard.</p>
        <div class="buttons">
            <form method="post" style="display: inline;">
                <button type="submit" class="yes-btn">Yes, it's me ✅</button>
            </form>
            <form method="post" action="../templates/auth/login.php" style="display: inline;">
                <button type="button" class="no-btn" onclick="if(confirm('Return to login page?')){this.form.submit();}">No, not me ❌</button>
            </form>
        </div>
        <div class="footer">
            Didn't sign up? Just click No or ignore this page.
        </div>
    </div>
</body>
</html>