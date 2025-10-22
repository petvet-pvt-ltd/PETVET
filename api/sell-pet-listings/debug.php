<?php
// Simple debug script
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing connection...\n";

require_once __DIR__ . '/../../config/connect.php';
echo "Config loaded\n";

require_once __DIR__ . '/../../models/SellPetListingModel.php';
echo "Model loaded\n";

try {
    $model = new SellPetListingModel();
    echo "Model instantiated\n";
    
    $listings = $model->getUserListings(1); // Test with user ID 1
    echo "Listings fetched: " . count($listings) . "\n";
    
    print_r($listings);
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
?>
