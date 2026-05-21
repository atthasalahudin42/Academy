<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: ../templates/dashboard/main.php");
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../templates/auth/signup.php");
    exit();
}

require 'Database.php';
require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Get form input
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validate input
if (empty($name) || empty($email) || empty($password)) {
    $_SESSION['error'] = "All fields are required.";
    header("Location: ../templates/auth/signup.php");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Invalid email format.";
    header("Location: ../templates/auth/signup.php");
    exit();
}

// Check if email exists
$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check_result = $check->get_result();

if ($check_result->num_rows > 0) {
    $_SESSION['error'] = "Email already exists!";
    header("Location: ../templates/auth/signup.php");
    exit();
}

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert new user
$stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $hashed_password);

if ($stmt->execute()) {
    $user_id = $stmt->insert_id;

    // Generate verification token
    $verification_token = bin2hex(random_bytes(32));

    // Update user with verification token and is_verified = 0
    $update_stmt = $conn->prepare("UPDATE users SET verification_token = ?, is_verified = 0 WHERE id = ?");
    $update_stmt->bind_param("si", $verification_token, $user_id);
    $update_stmt->execute();

    // Send verification email
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'studyhelper42@gmail.com';
        $mail->Password   = 'uclq yzoz cdrc niui';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('studyhelper42@gmail.com', 'AI Study Helper');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        $mail->Subject = 'Verify Your Email - AI Study Helper';
        $mail->Body = "
            <h3>Welcome to AI Study Helper!</h3>
            <p>Please verify your email by clicking the link below:</p>
            <a href='http://localhost/academy/templates/auth/verify.php?token=$verification_token' style='background: #4F46E5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;'>Verify Email</a>
            <p>Or copy this link: http://localhost/academy/templates/auth/verify.php?token=$verification_token</p>
            <p>This link expires in 24 hours. If you didn't create an account, ignore this email.</p>
        ";

        $mail->send();
        $_SESSION['success'] = "Account created! Check your email for the verification link.";

    } catch (Exception $e) {
        $_SESSION['error'] = "Account created but verification email failed to send (" . $mail->ErrorInfo . "). Please contact support or try resending.";
    }

    $_SESSION['pending_email'] = $email;
    header("Location: ../templates/auth/verification_wait.php");
    exit();

} else {
    $_SESSION['error'] = "Database Error. Please try again.";
    header("Location: ../templates/auth/signup.php");
    exit();
}

// Close connections
$stmt->close();
$check->close();
$conn->close();
?>