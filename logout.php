<?php
/**
 * Logout Script - Document Management System
 * Pamantasan ng Lungsod ng Pasig
 */

// Start session
session_start();

// Include database configuration
require_once 'config/database.php';

// Log logout in audit trail if user is logged in
if (isset($_SESSION['user_id'])) {
    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("
            INSERT INTO audit_trails (user_id, action_type, table_name, ip_address, user_agent) 
            VALUES (?, 'LOGOUT', 'users', ?, ?)
        ");
        $stmt->execute([
            $_SESSION['user_id'],
            $_SERVER['REMOTE_ADDR'],
            $_SERVER['HTTP_USER_AGENT']
        ]);
    } catch (Exception $e) {
        // Audit trail failed, but continue with logout
        error_log("Logout audit trail error: " . $e->getMessage());
    }
}

// Destroy all session data
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

// Redirect to login page with success message
header('Location: login.php?logout=success');
exit();
?>
