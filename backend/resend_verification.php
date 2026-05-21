<?php
session_start();

// Check if user came from verification flow
if (!isset($_SESSION['pending_email'])) {
    header("Location: ../templates/auth/login.php");
    exit();
}

require __DIR__ . '/../vendor/autoload.php';
use Dell7420\Academy\Database;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$db = new Database();
$conn = $db->connect();

$email = $_SESSION['pending_email'];

// Safety check
if (empty($email)) {
    unset($_SESSION['pending_email']);
    $_SESSION['error'] = "No pending verification found.";
    header("Location: ../templates/auth/login.php");
    exit();
}

// Check if user exists and is NOT verified
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND is_verified = 0");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    unset($_SESSION['pending_email']);
    $_SESSION['error'] = "Account already verified or not found.";
    header("Location: ../templates/auth/login.php");
    exit();
}

$user = $result->fetch_assoc();
$user_id = $user['id'];

$stmt->close();

$verification_token = bin2hex(random_bytes(32));

$update_stmt = $conn->prepare("UPDATE users SET verification_token = ? WHERE id = ?");
$update_stmt->bind_param("si", $verification_token, $user_id);
$update_stmt->execute();
$update_stmt->close();

$base_url = "http://academy.test";
$verification_link = $base_url . "/backend/verify.php?token=" . $verification_token;

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'studyhelper42@gmail.com';

    $mail->Password   = 'uclq yzoz cdrc niui';

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->SMTPDebug  = 0;

    $mail->setFrom('studyhelper42@gmail.com', 'AI Study Helper');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';

    $mail->Subject = 'Verify Your Email - AI Study Helper (Resend)';

    $mail->Body = "
        <h2>Email Verification (Resend) 🔁</h2>
        <p>Click below to verify your email:</p>

        <a href='$verification_link' 
           style='background:#4F46E5;color:white;padding:12px 20px;text-decoration:none;border-radius:6px;display:inline-block;'>
           Verify Email ✅
        </a>

        <p>If button doesn't work:</p>
        <p>$verification_link</p>

        <p>This link expires in 24 hours.</p>
    ";

    $mail->send();

    $_SESSION['success'] = "Verification email resent! Check your inbox.";

} catch (Exception $e) {
    $_SESSION['error'] = "Email failed: " . $mail->ErrorInfo;
}

header("Location: ../templates/auth/verification_wait.php");
exit();

$conn->close();
?>
