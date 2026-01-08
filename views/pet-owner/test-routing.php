<?php
// Quick test to verify routing is working
echo "<!DOCTYPE html><html><head><title>Test</title></head><body>";
echo "<h1>Routing Test</h1>";
echo "<p>If you can see this, the file system is working.</p>";

// Check if controller method exists
require_once __DIR__ . '/../../controllers/PetOwnerController.php';
if (method_exists('PetOwnerController', 'paymentSuccess')) {
    echo "<p style='color: green;'>✅ paymentSuccess() method EXISTS in PetOwnerController</p>";
} else {
    echo "<p style='color: red;'>❌ paymentSuccess() method MISSING from PetOwnerController</p>";
}

// Check index.php routing
$indexContent = file_get_contents(__DIR__ . '/../../index.php');
if (strpos($indexContent, "case 'payment-success'") !== false) {
    echo "<p style='color: green;'>✅ 'payment-success' case EXISTS in index.php</p>";
} else {
    echo "<p style='color: red;'>❌ 'payment-success' case MISSING from index.php</p>";
}

echo "<hr><p><a href='/PETVET/index.php?module=pet-owner&page=payment-success&session_id=test123'>Click here to test payment-success page</a></p>";
echo "</body></html>";
?>
