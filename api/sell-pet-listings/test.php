<?php
session_start();
require_once __DIR__ . '/../../config/connect.php';

header('Content-Type: application/json');

try {
    // Check session
    $sessionInfo = [
        'user_id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? null,
        'role' => $_SESSION['role'] ?? null,
        'current_role' => $_SESSION['current_role'] ?? null,
    ];
    
    // Check database connection
    $dbConnected = false;
    if (isset($conn) && $conn) {
        $dbConnected = true;
        $result = mysqli_query($conn, "SELECT 1");
        $dbWorking = $result ? true : false;
    } else {
        $dbWorking = false;
    }
    
    // Check if table exists
    $tableExists = false;
    if ($dbConnected && $dbWorking) {
        $result = mysqli_query($conn, "SHOW TABLES LIKE 'sell_pet_listings'");
        $tableExists = mysqli_num_rows($result) > 0;
    }
    
    echo json_encode([
        'success' => true,
        'session' => $sessionInfo,
        'database' => [
            'connected' => $dbConnected,
            'working' => $dbWorking,
            'table_exists' => $tableExists
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
