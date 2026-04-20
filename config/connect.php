<?php
/**
 * Database Connection Configuration
 * PETVET Authentication System
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_USER', 'root');
define('DB_PASS', '123456');  // Change if you set a password
define('DB_NAME', 'petvetDB');
define('DB_CHARSET', 'utf8mb4');

// TiDB Database credentials
// define('DB_HOST', 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com');
// define('DB_PORT', '4000');
// define('DB_USER', '2iYmekB7i4tHWm7.root');
// define('DB_PASS', 'Po3TdFdOuAqvbtCn');
// define('DB_NAME', 'petvetDB');
// define('DB_CHARSET', 'utf8mb4');
// define('DB_CA_PATH', __DIR__ . '/../database/CA/isrgrootx1.pem');

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
        ];

        // Only enable SSL options when a CA cert is configured (TiDB Cloud)
        if (defined('DB_CA_PATH') && is_string(DB_CA_PATH) && file_exists(DB_CA_PATH)) {
            $options[PDO::MYSQL_ATTR_SSL_CA] = DB_CA_PATH;
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = true;
        }
        
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Local-dev convenience: auto-create the database if it's missing
            $message = $e->getMessage();
            $isUnknownDb = (stripos($message, 'Unknown database') !== false);
            $isLocal = (DB_HOST === 'localhost' || DB_HOST === '127.0.0.1');

            if ($isLocal && $isUnknownDb) {
                try {
                    $dsnNoDb = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=" . DB_CHARSET;
                    $pdoNoDb = new PDO($dsnNoDb, DB_USER, DB_PASS, $options);
                    $dbNameEscaped = str_replace('`', '``', DB_NAME);
                    $pdoNoDb->exec("CREATE DATABASE IF NOT EXISTS `{$dbNameEscaped}` CHARACTER SET " . DB_CHARSET);
                    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
                } catch (PDOException $inner) {
                    error_log("Database connection failed (auto-create DB failed): " . $inner->getMessage());
                    die("Database connection failed. Please check your configuration.");
                }
            } else {
                error_log("Database connection failed: " . $message);
                die("Database connection failed. Please check your configuration.");
            }
        }
    }
    
    return $pdo;
}

// MySQLi connection for legacy compatibility and models
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = mysqli_init();

// Set SSL options
// mysqli_ssl_set($conn, NULL, NULL, DB_CA_PATH, NULL, NULL);

// Use SSL only when a CA cert is configured (TiDB Cloud)
$mysqliFlags = 0;
if (defined('DB_CA_PATH') && is_string(DB_CA_PATH) && file_exists(DB_CA_PATH)) {
    $mysqliFlags = MYSQLI_CLIENT_SSL;
}

try {
    mysqli_real_connect($conn, DB_HOST, DB_USER, DB_PASS, DB_NAME, (int)DB_PORT, NULL, $mysqliFlags);
} catch (mysqli_sql_exception $e) {
    // Local-dev convenience: auto-create the database if it's missing
    $isLocal = (DB_HOST === 'localhost' || DB_HOST === '127.0.0.1');
    $isUnknownDb = ($e->getCode() === 1049); // ER_BAD_DB_ERROR

    if ($isLocal && $isUnknownDb) {
        // Connect without selecting a DB, create it, then select it.
        mysqli_real_connect($conn, DB_HOST, DB_USER, DB_PASS, null, (int)DB_PORT, NULL, $mysqliFlags);
        $dbNameEscaped = str_replace('`', '``', DB_NAME);
        mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS `{$dbNameEscaped}` CHARACTER SET " . DB_CHARSET);
        mysqli_select_db($conn, DB_NAME);
    } else {
        error_log('MySQLi connection failed: ' . $e->getMessage());
        die('Database connection failed. Please check your configuration.');
    }
}

mysqli_set_charset($conn, DB_CHARSET);

// Legacy compatibility (for old code that might use these)
$server_name = DB_HOST;
$server_user = DB_USER;
$server_pass = DB_PASS;
$server_db = DB_NAME;
?>