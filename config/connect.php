<?php
/**
 * Database Connection Configuration
 * PETVET Authentication System
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Change if you set a password
define('DB_NAME', 'petvet');
define('DB_CHARSET', 'utf8mb4');

/**
 * Get PDO database connection
 * @return PDO
 */
function db(): PDO {
    static $pdo = null;
    
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            die("Database connection failed. Please check your configuration.");
        }
    }
    
    return $pdo;
}

// MySQLi connection for legacy compatibility and models
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    error_log("MySQLi connection failed: " . mysqli_connect_error());
    die("Database connection failed. Please check your configuration.");
}

mysqli_set_charset($conn, DB_CHARSET);

// Legacy compatibility (for old code that might use these)
$server_name = DB_HOST;
$server_user = DB_USER;
$server_pass = DB_PASS;
$server_db = DB_NAME;
?>