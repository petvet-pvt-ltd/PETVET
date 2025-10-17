<?php
require_once __DIR__ . '/../BaseModel.php';

class GroomerPackagesModel extends BaseModel {
    
    /**
     * Get all packages for a groomer
     */
    public function getAllPackages($groomerId) {
        // Mock data - replace with actual database queries
        return [
            [
                'id' => 1,
                'name' => 'Complete Care Package',
                'description' => 'Full grooming experience with all essentials',
                'included_services' => 'Bath & Brush, Full Grooming, Nail Trim, Ear Cleaning',
                'original_price' => 36000.00,
                'discounted_price' => 29700.00,
                'discount_percent' => 17.5,
                'for_cats' => false,
                'for_dogs' => true,
                'duration' => '2 hours',
                'available' => true
            ],
            [
                'id' => 2,
                'name' => 'Premium Pamper Package',
                'description' => 'Ultimate pampering for your pet',
                'included_services' => 'Full Grooming, De-shedding Treatment, Teeth Cleaning, Nail Trim',
                'original_price' => 48000.00,
                'discounted_price' => 40500.00,
                'discount_percent' => 15.6,
                'for_cats' => false,
                'for_dogs' => true,
                'duration' => '2.5 hours',
                'available' => true
            ],
            [
                'id' => 3,
                'name' => 'Basic Essentials',
                'description' => 'Perfect for maintaining your pet between full grooming',
                'included_services' => 'Bath & Brush, Nail Trim',
                'original_price' => 15000.00,
                'discounted_price' => 12600.00,
                'discount_percent' => 16.0,
                'for_cats' => true,
                'for_dogs' => true,
                'duration' => '1 hour',
                'available' => true
            ],
            [
                'id' => 4,
                'name' => 'Cat Special',
                'description' => 'Gentle grooming designed for cats',
                'included_services' => 'Bath & Brush, Nail Trim, Ear Cleaning',
                'original_price' => 21000.00,
                'discounted_price' => 17700.00,
                'discount_percent' => 15.7,
                'for_cats' => true,
                'for_dogs' => false,
                'duration' => '1 hour',
                'available' => true
            ],
            [
                'id' => 5,
                'name' => 'Quick Refresh',
                'description' => 'Fast touch-up for busy pet parents',
                'included_services' => 'Bath & Brush, Ear Cleaning',
                'original_price' => 16500.00,
                'discounted_price' => 14100.00,
                'discount_percent' => 14.5,
                'for_cats' => true,
                'for_dogs' => true,
                'duration' => '45 min',
                'available' => false
            ]
        ];
    }
    
    /**
     * Add a new package
     */
    public function addPackage($data) {
        // Mock response - replace with actual database insert
        return [
            'success' => true,
            'message' => 'Package added successfully',
            'package_id' => rand(100, 999)
        ];
    }
    
    /**
     * Update an existing package
     */
    public function updatePackage($packageId, $data) {
        // Mock response - replace with actual database update
        return [
            'success' => true,
            'message' => 'Package updated successfully'
        ];
    }
    
    /**
     * Delete a package
     */
    public function deletePackage($packageId, $groomerId) {
        // Mock response - replace with actual database delete
        return [
            'success' => true,
            'message' => 'Package deleted successfully'
        ];
    }
    
    /**
     * Toggle package availability
     */
    public function toggleAvailability($packageId, $groomerId) {
        // Mock response - replace with actual database update
        return [
            'success' => true,
            'message' => 'Package availability updated'
        ];
    }
}
