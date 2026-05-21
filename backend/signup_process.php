<?php
session_start();

// ✅ SET TIMEZONE (VERY IMPORTANT)
date_default_timezone_set('Asia/Karachi');

// Autoload
require_once __DIR__ . '/../vendor/autoload.php';

use Dell7420\Academy\Database;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// DB connection
$db = new Database();
$conn = $db->connect();

// Get form data
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validate
if (empty($name) || empty($email) || empty($password)) {
    $_SESSION['error'] = 'All fields are required.';
    header('Location: ../templates/auth/signup.php');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'Invalid email format.';
    header('Location: ../templates/auth/signup.php');
    exit();
}

// Check existing email
$check = $conn->prepare('SELECT id FROM users WHERE email=?');
$check->bind_param('s', $email);
$check->execute();

if ($check->get_result()->num_rows > 0) {
    $_SESSION['error'] = 'Email already exists!';
    header('Location: ../templates/auth/signup.php');
    exit();
}
$check->close();

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// ✅ Generate verification token
$verification_token = bin2hex(random_bytes(32));
$token_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

// Insert user
$stmt = $conn->prepare("
    INSERT INTO users
    (name,email,password,is_verified,verification_token,token_expiry)
    VALUES (?,?,?,?,?,?)
");

$is_verified = 0;
$stmt->bind_param(
    "sssiss",
    $name,
    $email,
    $hashed_password,
    $is_verified,
    $verification_token,
    $token_expiry
);

if (!$stmt->execute()) {
    $_SESSION['error'] = 'Database error.';
    header('Location: ../templates/auth/signup.php');
    exit();
}

$stmt->close();

// ✅ Create verification link
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$base_url = $protocol . "://" . $_SERVER['HTTP_HOST'];
$verification_link =
    $base_url . "/backend/verify.php?token=" . urlencode($verification_token);

// Send email
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'studyhelper42@gmail.com';
    $mail->Password = 'uclq yzoz cdrc niui';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('studyhelper42@gmail.com', 'AI Study Helper');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = "Confirm Login — AI Study Helper";

    // ✅ Button instead of plain link
    $mail->Body = "
        <h2>Welcome to AI Study Helper 🎉</h2>
        <p>Please confirm your email by clicking below:</p>

        <a href='$verification_link'
           style='
                background:#4f46e5;
                color:white;
                padding:14px 22px;
                text-decoration:none;
                border-radius:8px;
                font-weight:bold;
                display:inline-block;'>
            ✅ Yes, it's me
        </a>

        <p style='margin-top:15px'>
            This link expires in <b>1 hour</b>.
        </p>
    ";

    $mail->send();

    $_SESSION['pending_email'] = $email;
    $_SESSION['success'] = 'Account created! Check your email.';

    header('Location: ../templates/auth/verification_wait.php');
    exit();

} catch (Exception $e) {
    $_SESSION['error'] = 'Email sending failed: ' . $mail->ErrorInfo;
    header('Location: ../templates/auth/signup.php');
    exit();
}