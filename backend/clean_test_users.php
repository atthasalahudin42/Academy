<?php
/**
 * Run ONCE to delete test users: http://academy.test/backend/clean_test_users.php
 */
require_once __DIR__ . '/../src/Database.php';
use Dell7420\Academy\Database;

$db = new Database();
$conn = $db->connect();

 // Delete unverified test users older than 1 day
$stmt = $conn->prepare("DELETE FROM users WHERE is_verified = 0 AND created_at < DATE_SUB(NOW(), INTERVAL 1 DAY)");
$stmt->execute();
$deleted = $stmt->affected_rows;

 // Show remaining users
$result = $conn->query("SELECT id, email, is_verified FROM users ORDER BY id DESC LIMIT 10");
echo "<h2>Cleaned $deleted test users</h2><table border='1'>";
while ($row = $result->fetch_assoc()) {
    echo "<tr><td>{$row['id']}</td><td>" . htmlspecialchars($row['email']) . "</td><td>" . ($row['is_verified'] ? 'Yes' : 'No') . "</td></tr>";
}
echo "</table>";

echo "<p><a href='../templates/auth/signup.php'>Test Signup Now</a></p>";
?>
