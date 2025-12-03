<?php
/**
 * Update existing clinic data with sample descriptions and images
 */

require_once __DIR__ . '/../config/connect.php';

try {
    $pdo = db();
    
    echo "=== UPDATING CLINIC DATA ===\n\n";
    
    // Update first clinic (Happy Paws)
    $pdo->exec("
        UPDATE clinics 
        SET clinic_description = 'Trusted pet healthcare and wellness. Experienced vets, modern facilities, and friendly service.',
            clinic_logo = 'https://static.vecteezy.com/system/resources/previews/005/601/780/non_2x/veterinary-clinic-logo-vector.jpg',
            clinic_cover = 'https://img.freepik.com/free-vector/veterinary-clinic-social-media-cover-template_23-2149716789.jpg',
            map_location = '6.9271, 79.8612'
        WHERE id = 1
    ");
    echo "✅ Updated Happy Paws Veterinary Clinic\n";
    
    // Update second clinic
    $pdo->exec("
        UPDATE clinics 
        SET clinic_description = 'Professional veterinary care for your beloved pets.',
            clinic_logo = 'https://static.vecteezy.com/system/resources/previews/005/601/780/non_2x/veterinary-clinic-logo-vector.jpg',
            clinic_cover = 'https://img.freepik.com/free-vector/veterinary-clinic-social-media-cover-template_23-2149716789.jpg'
        WHERE id = 2
    ");
    echo "✅ Updated second clinic\n";
    
    echo "\n=== UPDATE COMPLETED ===\n";
    
} catch (Exception $e) {
    echo "❌ Update failed: " . $e->getMessage() . "\n";
}
?>
