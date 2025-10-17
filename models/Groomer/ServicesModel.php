<?php
require_once __DIR__ . '/../BaseModel.php';

class GroomerServicesModel extends BaseModel {
    
    /**
     * Get all services for a groomer
     */
    public function getAllServices($groomerId) {
        // Mock data - replace with actual database queries
        return [
            [
                'id' => 1,
                'name' => 'Bath & Brush',
                'description' => 'Basic bath with shampoo and brushing',
                'price' => 10500.00,
                'for_cats' => true,
                'for_dogs' => true,
                'duration' => '45 min',
                'available' => true
            ],
            [
                'id' => 2,
                'name' => 'Full Grooming',
                'description' => 'Complete grooming including bath, haircut, and nail trim',
                'price' => 19500.00,
                'for_cats' => false,
                'for_dogs' => true,
                'duration' => '90 min',
                'available' => true
            ],
            [
                'id' => 3,
                'name' => 'Nail Trim',
                'description' => 'Quick nail trimming service',
                'price' => 4500.00,
                'for_cats' => true,
                'for_dogs' => true,
                'duration' => '15 min',
                'available' => true
            ],
            [
                'id' => 4,
                'name' => 'Teeth Cleaning',
                'description' => 'Professional dental cleaning',
                'price' => 13500.00,
                'for_cats' => true,
                'for_dogs' => true,
                'duration' => '30 min',
                'available' => true
            ],
            [
                'id' => 5,
                'name' => 'De-shedding Treatment',
                'description' => 'Special treatment to reduce shedding',
                'price' => 15000.00,
                'for_cats' => true,
                'for_dogs' => true,
                'duration' => '60 min',
                'available' => true
            ],
            [
                'id' => 6,
                'name' => 'Ear Cleaning',
                'description' => 'Gentle ear cleaning and inspection',
                'price' => 6000.00,
                'for_cats' => true,
                'for_dogs' => true,
                'duration' => '20 min',
                'available' => false
            ]
        ];
    }
    
    /**
     * Add a new service
     */
    public function addService($data) {
        // Mock response - replace with actual database insert
        return [
            'success' => true,
            'message' => 'Service added successfully',
            'service_id' => rand(100, 999)
        ];
    }
    
    /**
     * Update an existing service
     */
    public function updateService($serviceId, $data) {
        // Mock response - replace with actual database update
        return [
            'success' => true,
            'message' => 'Service updated successfully'
        ];
    }
    
    /**
     * Delete a service
     */
    public function deleteService($serviceId, $groomerId) {
        // Mock response - replace with actual database delete
        return [
            'success' => true,
            'message' => 'Service deleted successfully'
        ];
    }
    
    /**
     * Toggle service availability
     */
    public function toggleAvailability($serviceId, $groomerId) {
        // Mock response - replace with actual database update
        return [
            'success' => true,
            'message' => 'Service availability updated'
        ];
    }
}
