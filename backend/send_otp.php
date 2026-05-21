<?php
session_start();


require __DIR__ . '/Database.php';
require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$email = trim($_POST['email'] ?? '');

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Please enter a valid email address.";
    header("Location: ../templates/auth/forgot_password.php");
    exit();
}


$stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['otp_sent'] = true;
    header("Location: ../templates/auth/verify_otp.php");
    exit();
}



$otp = random_int(100000, 999999);
$expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));

$update = $conn->prepare(
    "UPDATE users SET otp_code=?, otp_expiry=? WHERE email=?"
);
$update->bind_param("sss", $otp, $expiry, $email);
$update->execute();



$mail = new PHPMailer(true);

try {


    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;


    $mail->Username   = 'studyhelper42@gmail.com';
    $mail->Password   = 'avhd obxy lbdg yqix';

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->setFrom('studyhelper42@gmail.com', 'AI Study Helper');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';

    $mail->Subject = 'Password Reset OTP';

    $mail->Body = "
        <h3>Password Reset Request</h3>
        <p>Your OTP is:</p>
        <h2>$otp</h2>
        <p>This code expires in 5 minutes.</p>
    ";

    $mail->AltBody = "Your OTP is: $otp (valid for 5 minutes)";

    $mail->send();

    $_SESSION['reset_email'] = $email;
    $_SESSION['otp_sent'] = true;

    header("Location: ../templates/auth/verify_otp.php");
    exit();

} catch (Exception $e) {


    $_SESSION['error'] = "Mailer Error: " . $mail->ErrorInfo;

    header("Location: ../templates/auth/forgot_password.php");
    exit();
}