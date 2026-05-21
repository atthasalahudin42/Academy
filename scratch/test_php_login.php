<?php
/** Simulate login and get magic link token for local testing */
require_once __DIR__ . '/../src/Database.php';
use Dell7420\Academy\Database;

$db = new Database();
$conn = $db->connect();
$email = 'xagent891@gmail.com';
$password = '12345678';

$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user || !password_verify($password, $user['password'])) {
    echo "LOGIN_FAIL\n";
    exit(1);
}
if (!$user['is_verified']) {
    echo "NOT_VERIFIED\n";
    exit(1);
}

$login_token = bin2hex(random_bytes(32));
$expiry = date("Y-m-d H:i:s", strtotime("+24 hours"));
$update = $conn->prepare("UPDATE users SET login_token = ?, login_token_expiry = ? WHERE id = ?");
$update->bind_param("ssi", $login_token, $expiry, $user['id']);
$update->execute();

$link = "http://127.0.0.1:8080/backend/verify_login.php?token=" . $login_token;
echo "LOGIN_OK\n";
echo "magic_link=" . $link . "\n";
echo "user=" . $user['name'] . "\n";
