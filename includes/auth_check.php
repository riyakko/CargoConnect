<?php
/**
 * CargoConnect — Session Helper
 * 
 * Include this at the top of every protected page.
 * It starts the session, checks if the user is logged in,
 * and provides $current_user with their DB data.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
$current_user = null;

if ($is_logged_in && $conn) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $r = $stmt->get_result();
    if ($r->num_rows > 0) {
        $current_user = $r->fetch_assoc();
    } else {
        // User not found in DB — destroy session
        session_destroy();
        header('Location: auth.php');
        exit;
    }
    $stmt->close();
}

// Redirect to login if not authenticated
if (!$is_logged_in || !$current_user) {
    header('Location: auth.php');
    exit;
}

// Shorthand helpers
$user_id = $current_user['user_id'];
$user_name = $current_user['first_name'] . ' ' . $current_user['last_name'];
$user_initials = strtoupper(substr($current_user['first_name'], 0, 1) . substr($current_user['last_name'], 0, 1));
$user_role = $current_user['role']; // 'admin' or 'customer'
?>
