<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'system_docman');
define('DB_USER', 'root');
define('DB_PASS', ''); // leave blank if no password
define('DB_CHARSET', 'utf8mb4');

/**
 * Create and return a PDO connection
 */
function getDBConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        return new PDO($dsn, DB_USER, DB_PASS, $options);

    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

// Optional: define role constants for easier role checking
define('ROLE_ADMIN', 1);
define('ROLE_DEPT_ADMIN', 2);
define('ROLE_USER', 3);
?>
