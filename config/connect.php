<?php
/**
 * Database Connection Configuration
 * PETVET Authentication System
 */

// Database credentials
// define('DB_HOST', 'localhost');
// define('DB_USER', 'root');
// define('DB_PASS', '');  // Change if you set a password
// define('DB_NAME', 'petvet');
// define('DB_CHARSET', 'utf8mb4');

// TiDB Database credentials
define('DB_HOST', 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com');
define('DB_PORT', '4000');
define('DB_USER', '2iYmekB7i4tHWm7.root');
define('DB_PASS', 'Po3TdFdOuAqvbtCn');
define('DB_NAME', 'petvetDB');
define('DB_CHARSET', 'utf8mb4');
define('DB_CA_PATH', __DIR__ . '/../database/CA/isrgrootx1.pem');

/**
 * Get PDO database connection
 * @return PDO
 */
function db(): PDO {
    static $pdo = null;
    
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_SSL_CA => DB_CA_PATH,
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => true,
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
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = mysqli_init();

// Set SSL options
mysqli_ssl_set($conn, NULL, NULL, DB_CA_PATH, NULL, NULL);

// Connect with SSL
if (!mysqli_real_connect($conn, DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT, NULL, MYSQLI_CLIENT_SSL)) {
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