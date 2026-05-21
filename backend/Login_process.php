<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use Dell7420\Academy\Database;
use PHPMailer\PHPMailer\PHPMailer;

// ✅ Create DB connection
$db = new Database();
$conn = $db->connect();

// Get inputs
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validate
if (empty($email) || empty($password)) {
    $_SESSION['error'] = 'All fields required.';
    header('Location: ../templates/auth/login.php');
    exit();
}

// Find user
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = 'Invalid credentials.';
    header('Location: ../templates/auth/login.php');
    exit();
}

$user = $result->fetch_assoc();

// Check password
if (!password_verify($password, $user['password'])) {
    $_SESSION['error'] = 'Invalid credentials.';
    header('Location: ../templates/auth/login.php');
    exit();
}

// Check email verified
if ($user['is_verified'] == 0) {
    $_SESSION['error'] = 'Please verify your email first.';
    header('Location: ../templates/auth/login.php');
    exit();
}

// Generate login token
$login_token = bin2hex(random_bytes(32));
$expiry = date("Y-m-d H:i:s", strtotime("+24 hours"));

// Save token
$update = $conn->prepare("
    UPDATE users 
    SET login_token = ?, login_token_expiry = ? 
    WHERE id = ?
");

$update->bind_param("ssi", $login_token, $expiry, $user['id']);
$update->execute();

// Create link
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$base_url = $protocol . "://" . $_SERVER['HTTP_HOST'];
$login_link = $base_url . "/backend/verify_login.php?token=" . $login_token;

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
    $mail->Subject = 'Confirm Login';

    $mail->Body = "
        <h2>Login Attempt</h2>
        <p>Click below to confirm your login:</p>
        <a href='$login_link'>Confirm Login</a>
        <p>This link expires in 24 hours.</p>
    ";


    $mail->send();

    $_SESSION['success'] = 'Check your email to confirm login.';
    header('Location: ../templates/auth/verification_wait.php');
    exit();

} catch (Exception $e) {
    $_SESSION['error'] = 'Email failed: ' . $mail->ErrorInfo;
    header('Location: ../templates/auth/login.php');
    exit();
}