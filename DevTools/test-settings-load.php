<?php
/**
 * Test loading the settings page
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing settings page...\n\n";

// Simulate session
session_start();
$_SESSION['user_id'] = 9; // manager@gmail.com
$_SESSION['roles'] = ['clinic_manager'];

// Include the controller
require_once __DIR__ . '/../controllers/ClinicManagerController.php';

try {
    $controller = new ClinicManagerController();
    
    // Try to call settings method
    ob_start();
    $controller->settings();
    $output = ob_get_clean();
    
    echo "✅ Settings page loaded successfully!\n";
    echo "Output length: " . strlen($output) . " bytes\n";
    
} catch (Exception $e) {
    echo "❌ Error loading settings page:\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
?>
