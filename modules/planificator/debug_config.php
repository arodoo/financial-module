<?php
// Show all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Configuration Debugger</h1>";

// Check if config.php exists
echo "<h2>Checking config.php</h2>";
if (file_exists('config/config.php')) {
    echo "config/config.php exists.<br>";
    
    // Include the file and display defined constants
    include_once 'config/config.php';
    
    echo "<h3>Database Constants from config.php:</h3>";
    echo "DB_HOST: " . (defined('DB_HOST') ? DB_HOST : 'Not defined') . "<br>";
    echo "DB_NAME: " . (defined('DB_NAME') ? DB_NAME : 'Not defined') . "<br>";
    echo "DB_USER: " . (defined('DB_USER') ? DB_USER : 'Not defined') . "<br>";
    echo "DB_PASS: " . (defined('DB_PASS') ? (DB_PASS ? '[Password set]' : '[Empty password]') : 'Not defined') . "<br>";
} else {
    echo "config/config.php does not exist.<br>";
}

// Check MySQL connection with current constants
echo "<h2>Testing Database Connection</h2>";
if (defined('DB_HOST') && defined('DB_NAME') && defined('DB_USER')) {
    echo "Attempting to connect to MySQL with:<br>";
    echo "Host: " . DB_HOST . "<br>";
    echo "Database: " . DB_NAME . "<br>";
    echo "User: " . DB_USER . "<br>";
    
    try {
        $conn = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
            DB_USER,
            DB_PASS,
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );
        echo "<strong style='color:green'>Connection successful!</strong><br>";
        $conn = null;
    } catch (PDOException $e) {
        echo "<strong style='color:red'>Connection failed:</strong> " . $e->getMessage() . "<br>";
    }
} else {
    echo "Database constants are not properly defined.<br>";
}

// Provide a solution
echo "<h2>Suggested Solution</h2>";
echo "<p>Based on the error message, you need to update your database credentials in config/config.php to match your local database setup.</p>";
echo "<p>It appears you're trying to connect with username 'zenfamili' which doesn't have proper access.</p>";
echo "<p>For a local XAMPP installation, try using:</p>";
echo "<pre>
define('DB_HOST', 'localhost');
define('DB_NAME', 'financial_db');
define('DB_USER', 'root');
define('DB_PASS', '');
</pre>";
