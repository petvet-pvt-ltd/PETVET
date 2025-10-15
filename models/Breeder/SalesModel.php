<?php
require_once __DIR__ . '/../BaseModel.php';

class BreederSalesModel extends BaseModel {
    
    public function getAllSales($breederId) {
        return array_merge($this->getPendingSales($breederId), $this->getCompletedSales($breederId));
    }

    public function getPendingSales($breederId) {
        return [
            [
                'id' => 3,
                'pet_name' => 'Rocky',
                'breed' => 'Bulldog',
                'buyer_name' => 'James Wilson',
                'buyer_email' => 'james.w@email.com',
                'buyer_phone' => '555-0301',
                'sale_date' => '2025-10-20',
                'amount' => 2000,
                'deposit' => 500,
                'status' => 'pending',
                'payment_method' => 'Bank Transfer'
            ]
        ];
    }

    public function getCompletedSales($breederId) {
        return [
            [
                'id' => 1,
                'pet_name' => 'Max',
                'breed' => 'Labrador',
                'buyer_name' => 'Robert Smith',
                'buyer_email' => 'robert.s@email.com',
                'buyer_phone' => '555-0302',
                'sale_date' => '2025-10-10',
                'amount' => 1800,
                'deposit' => 1800,
                'status' => 'completed',
                'payment_method' => 'Cash'
            ],
            [
                'id' => 2,
                'pet_name' => 'Luna',
                'breed' => 'Beagle',
                'buyer_name' => 'Emily Davis',
                'buyer_email' => 'emily.d@email.com',
                'buyer_phone' => '555-0303',
                'sale_date' => '2025-10-08',
                'amount' => 1200,
                'deposit' => 1200,
                'status' => 'completed',
                'payment_method' => 'Card'
            ]
        ];
    }

    public function getRevenueStats($breederId) {
        return [
            'this_month' => 15600,
            'last_month' => 12400,
            'this_year' => 142000,
            'pending' => 3500
        ];
    }
}
