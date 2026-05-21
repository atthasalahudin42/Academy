<?php
session_start();
require_once __DIR__ . '/../src/Database.php';

use Dell7420\Academy\Database;

// ✅ Always set timezone
date_default_timezone_set('Asia/Karachi');

$db = new Database();
$conn = $db->connect();

// Get token safely
$token = $_GET['token'] ?? '';

if (empty($token)) {
    $_SESSION['error'] = 'Invalid verification link.';
    header('Location: ../templates/auth/login.php');
    exit();
}

// ✅ Use PHP time instead of MySQL NOW()
$current_time = date('Y-m-d H:i:s');

// Check token + expiry
$stmt = $conn->prepare("
    SELECT id 
    FROM users 
    WHERE verification_token = ?
    AND token_expiry > ?
    AND is_verified = 0
");

$stmt->bind_param("ss", $token, $current_time);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = 'Invalid or expired link.';
    header('Location: ../templates/auth/login.php');
    exit();
}

$user = $result->fetch_assoc();
$user_id = $user['id'];

// ✅ Mark user as verified
$update = $conn->prepare("
    UPDATE users 
    SET is_verified = 1,
        verification_token = NULL,
        token_expiry = NULL
    WHERE id = ?
");

$update->bind_param("i", $user_id);
$update->execute();

$stmt->close();
$update->close();

// ✅ Auto-login user
$full_stmt = $conn->prepare("
    SELECT name, email, profile_picture 
    FROM users 
    WHERE id = ?
");

$full_stmt->bind_param("i", $user_id);
$full_stmt->execute();
$full_result = $full_stmt->get_result();
$full_user = $full_result->fetch_assoc();
$full_stmt->close();

// Create session
$_SESSION['user_id'] = $user_id;
$_SESSION['user_email'] = $full_user['email'];
$_SESSION['user_name'] = $full_user['name'];
$_SESSION['profile_picture'] =
    $full_user['profile_picture'] ?? 'assets/images/avatar.png';

// Redirect
header('Location: ../templates/dashboard/main.php');
exit();
?>