<?php
require_once __DIR__ . '/../src/Database.php';
use Dell7420\Academy\Database;

$db = new Database();
$conn = $db->connect();
$email = 'xagent891@gmail.com';
$stmt = $conn->prepare("SELECT id, name, email, is_verified FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$r = $stmt->get_result();
if ($r->num_rows === 0) {
    echo "USER_NOT_FOUND\n";
    exit(1);
}
$user = $r->fetch_assoc();
echo json_encode($user) . "\n";

// verify password
$stmt2 = $conn->prepare("SELECT password FROM users WHERE email = ?");
$stmt2->bind_param("s", $email);
$stmt2->execute();
$row = $stmt2->get_result()->fetch_assoc();
echo "password_ok=" . (password_verify('12345678', $row['password']) ? 'yes' : 'no') . "\n";
