<?php
require_once __DIR__ . '/../BaseModel.php';

class BreederDashboardModel extends BaseModel {
    
    public function getStats($breederId) {
        return [
            'total_pets' => 32,
            'available_pets' => 12,
            'sold_this_month' => 8,
            'monthly_revenue' => 15600
        ];
    }

    public function getActivePets($breederId) {
        return [
            [
                'id' => 1,
                'name' => 'Bella',
                'breed' => 'Golden Retriever',
                'age' => '3 months',
                'gender' => 'Female',
                'price' => 1200,
                'status' => 'available',
                'health_status' => 'Excellent'
            ],
            [
                'id' => 2,
                'name' => 'Duke',
                'breed' => 'German Shepherd',
                'age' => '2 months',
                'gender' => 'Male',
                'price' => 1500,
                'status' => 'available',
                'health_status' => 'Excellent'
            ]
        ];
    }

    public function getRecentSales($breederId) {
        return [
            [
                'id' => 1,
                'pet_name' => 'Max',
                'breed' => 'Labrador',
                'buyer_name' => 'Robert Smith',
                'sale_date' => '2025-10-10',
                'amount' => 1800,
                'status' => 'completed'
            ],
            [
                'id' => 2,
                'pet_name' => 'Luna',
                'breed' => 'Beagle',
                'buyer_name' => 'Emily Davis',
                'sale_date' => '2025-10-08',
                'amount' => 1200,
                'status' => 'completed'
            ]
        ];
    }
}
