<?php
session_start();
require_once __DIR__ . '/../src/Database.php';

use Dell7420\Academy\Database;
$db = new Database();
$conn = $db->connect();

if (!isset($_SESSION['pending_email'])) {
    header("Location: ../templates/auth/login.php");
    exit();
}

$email = $_SESSION['pending_email'];

$stmt = $conn->prepare("SELECT id, name, profile_picture FROM users WHERE email = ? AND is_verified = 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['profile_picture'] = $user['profile_picture'] ?? 'avatar.png';
    unset($_SESSION['pending_email']);
    header("Location: ../templates/dashboard/main.php");
    exit();
} else {
    $_SESSION['error'] = "Email not verified yet. Check your email.";
    header("Location: ../templates/auth/verification_wait.php");
    exit();
}
?>