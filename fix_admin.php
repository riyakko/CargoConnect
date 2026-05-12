<?php
// One-time admin password fixer
require_once 'includes/db.php';
if ($conn) {
    $hash = password_hash('Admin@1234', PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password=? WHERE email='admin@cargoconnect.com'");
    $stmt->bind_param('s', $hash);
    if ($stmt->execute()) {
        echo "✅ Admin password updated successfully!<br>";
        echo "Email: admin@cargoconnect.com<br>";
        echo "Password: Admin@1234<br>";
        echo "<a href='auth.php'>Go to Login →</a>";
    } else {
        echo "❌ Update failed: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "❌ No DB connection.";
}
?>
