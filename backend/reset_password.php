<?php
session_start();
require_once __DIR__ . '/../src/Database.php';

use Dell7420\Academy\Database;

$db = new Database();
$conn = $db->connect();

$email = $_SESSION['reset_email'] ?? '';
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Validate inputs
if (empty($email)) {
    $_SESSION['error'] = "Session expired. Please start over.";
    header("Location: ../templates/auth/forgot_password.php");
    exit();
}

if (empty($password) || empty($confirm_password)) {
    $_SESSION['error'] = "Please fill in all fields.";
    header("Location: ../templates/auth/reset_password.php");
    exit();
}

if ($password !== $confirm_password) {
    $_SESSION['error'] = "Passwords do not match.";
    header("Location: ../templates/auth/reset_password.php");
    exit();
}

if (strlen($password) < 6) {
    $_SESSION['error'] = "Password must be at least 6 characters.";
    header("Location: ../templates/auth/reset_password.php");
    exit();
}

// Hash password and update database
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    "UPDATE users SET password = ?, otp_code = NULL, otp_expiry = NULL WHERE email = ?"
);
$stmt->bind_param("ss", $hashed_password, $email);
$stmt->execute();

// Clear session and redirect to login with success message
unset($_SESSION['reset_email']);
$_SESSION['success'] = "Password updated successfully! You can now login with your new password.";
header("Location: ../templates/auth/login.php");
exit();
