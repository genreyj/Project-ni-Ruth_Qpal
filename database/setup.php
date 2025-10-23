<?php
/**
 * Database Setup Script
 * Run this file once to create the database and tables
 * Access via: http://localhost/system_docman/database/setup.php
 */

// Database connection parameters
$host = 'localhost';
$dbname = 'system_docman';
$username = 'root';
$password = '';

// Read the SQL file
$sqlFile = __DIR__ . '/schema.sql';
if (!file_exists($sqlFile)) {
    die("Error: schema.sql file not found!");
}

$sql = file_get_contents($sqlFile);

try {
    // First, connect without selecting a database to create it
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Database Setup</h2>";
    echo "<p>Starting database creation...</p>";
    
    // Split SQL file into individual statements
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            return !empty($stmt) && 
                   !preg_match('/^\s*--/', $stmt) && 
                   !preg_match('/^\s*$/', $stmt);
        }
    );
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($statements as $statement) {
        try {
            $pdo->exec($statement);
            $successCount++;
        } catch (PDOException $e) {
            // Skip if database or table already exists
            if (strpos($e->getMessage(), 'already exists') !== false) {
                echo "<p style='color: orange;'>⚠️ " . htmlspecialchars($e->getMessage()) . "</p>";
            } else {
                echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
                $errorCount++;
            }
        }
    }
    
    echo "<hr>";
    echo "<h3>Setup Complete!</h3>";
    echo "<p>✅ Successfully executed: <strong>$successCount</strong> statements</p>";
    
    if ($errorCount > 0) {
        echo "<p>❌ Errors encountered: <strong>$errorCount</strong></p>";
    }
    
    // Verify the setup
    $pdo->exec("USE $dbname");
    
    echo "<hr>";
    echo "<h3>Verification</h3>";
    
    // Check roles
    $stmt = $pdo->query("SELECT * FROM roles");
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h4>Roles Created:</h4>";
    echo "<ul>";
    foreach ($roles as $role) {
        echo "<li><strong>" . htmlspecialchars($role['role_name']) . "</strong> - " . 
             htmlspecialchars($role['role_description']) . "</li>";
    }
    echo "</ul>";
    
    // Check tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h4>Tables Created (" . count($tables) . "):</h4>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>" . htmlspecialchars($table) . "</li>";
    }
    echo "</ul>";
    
    // Check default admin user
    $stmt = $pdo->query("SELECT username, email, role_id FROM users WHERE role_id = 1");
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "<hr>";
        echo "<h3>Default Admin Credentials</h3>";
        echo "<div style='background: #f0f0f0; padding: 15px; border-radius: 5px;'>";
        echo "<p><strong>Username:</strong> " . htmlspecialchars($admin['username']) . "</p>";
        echo "<p><strong>Email:</strong> " . htmlspecialchars($admin['email']) . "</p>";
        echo "<p><strong>Password:</strong> Admin@123</p>";
        echo "<p style='color: red;'><strong>⚠️ IMPORTANT:</strong> Change this password after first login!</p>";
        echo "</div>";
    }
    
    echo "<hr>";
    echo "<h3>Next Steps</h3>";
    echo "<ol>";
    echo "<li>Delete or secure this setup.php file</li>";
    echo "<li>Login to the system with the default credentials</li>";
    echo "<li>Change the default admin password</li>";
    echo "<li>Start creating departments and users</li>";
    echo "</ol>";
    
    echo "<p><a href='../index.html' style='display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Go to Homepage</a></p>";
    
} catch (PDOException $e) {
    echo "<h2>Setup Failed</h2>";
    echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Please check your database connection settings and try again.</p>";
}

// Add some styling
echo "<style>
    body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
    h2 { color: #333; }
    h3 { color: #555; margin-top: 20px; }
    ul { line-height: 1.8; }
    hr { margin: 30px 0; border: none; border-top: 1px solid #ddd; }
</style>";
?>
