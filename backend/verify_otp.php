<?php
session_start();
require_once __DIR__ . '/../src/Database.php';

use Dell7420\Academy\Database;

$db = new Database();
$conn = $db->connect();

if (!isset($_SESSION['reset_email'])) {
    header("Location: ../templates/auth/forgot_password.php");
    exit();
}

$email = $_SESSION['reset_email'];
$otp   = trim($_POST['otp'] ?? '');

if (empty($otp)) {
    $_SESSION['error'] = "Please enter OTP.";
    header("Location: ../templates/auth/verify_otp.php");
    exit();
}

$stmt = $conn->prepare(
    "SELECT otp_code, otp_expiry FROM users WHERE email=?"
);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    $_SESSION['error'] = "User not found.";
    header("Location: ../templates/auth/verify_otp.php");
    exit();
}


if (
    $user['otp_code'] === $otp &&
    strtotime($user['otp_expiry']) > time()
) {
    $_SESSION['otp_verified'] = true;

    header("Location: ../templates/auth/reset_password.php");
    exit();
} else {
    $_SESSION['error'] = "Invalid or expired OTP.";
    header("Location: ../templates/auth/verify_otp.php");
    exit();
}