<?php
session_start();
require __DIR__ . '/Database.php';
require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

echo "Testing SMTP...\n";

$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;  // Enable verbose debug
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'studyhelper42@gmail.com';
    $mail->Password   = 'uclq yzoz cdrc niui';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('studyhelper42@gmail.com', 'Test');
    $mail->addAddress('attha.salahudin42@gmail.com');
    $mail->Subject = 'Test from academy';
    $mail->Body    = 'If you see this, SMTP works.';

    $mail->send();
    echo "SMTP success!\n";
} catch (Exception $e) {
    echo "SMTP Error: {$mail->ErrorInfo}\n";
}