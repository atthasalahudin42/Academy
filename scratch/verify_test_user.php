<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Dell7420\Academy\Database;

$db = new Database();
$conn = $db->connect();

$email = 'testuser@example.com';
$stmt = $conn->prepare("UPDATE users SET is_verified = 1 WHERE email = ?");
$stmt->bind_param("s", $email);

if ($stmt->execute()) {
    echo "Successfully verified $email\n";
} else {
    echo "Error verifying $email: " . $conn->error . "\n";
}
$stmt->close();
$conn->close();
