<?php

define('DB_HOST', 'localhost');
define('DB_PORT', '3306'); 
define('DB_NAME', 'system_docman'); 
define('DB_USER', 'root');
define('DB_PASS', '55555');  
define('DB_CHARSET', 'utf8mb4');

// Create PDO connection
function getDBConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database Connection Error: " . $e->getMessage());
        throw new Exception("Database connection failed. Please contact the administrator.");
    }
}

// Role constants
define('ROLE_SUPER_ADMIN', 1);
define('ROLE_DEPT_ADMIN', 2);
define('ROLE_USER', 3);

// Check user role
function hasRole($userId, $roleId) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT role_id FROM users WHERE user_id = ? AND is_active = 1");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if ($user && $user['role_id'] == $roleId) {
            return true;
        }
        return false;
    } catch (Exception $e) {
        error_log("Role Check Error: " . $e->getMessage());
        return false;
    }
}

// Check if user is super admin
function isSuperAdmin($userId) {
    return hasRole($userId, ROLE_SUPER_ADMIN);
}

// Check if user is department admin
function isDeptAdmin($userId) {
    return hasRole($userId, ROLE_DEPT_ADMIN);
}

// Check if user is regular user
function isUser($userId) {
    return hasRole($userId, ROLE_USER);
}

// Get user role name
function getUserRole($userId) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            SELECT r.role_name 
            FROM users u 
            JOIN roles r ON u.role_id = r.role_id 
            WHERE u.user_id = ? AND u.is_active = 1
        ");
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        
        return $result ? $result['role_name'] : null;
    } catch (Exception $e) {
        error_log("Get User Role Error: " . $e->getMessage());
        return null;
    }
}
?>