<?php
require_once __DIR__ . '/../BaseModel.php';

class SitterDashboardModel extends BaseModel {
    
    public function getStats($sitterId) {
        return [
            'active_bookings' => 5,
            'total_pets_cared' => 87,
            'completed_bookings' => 142,
            'monthly_earnings' => 2800
        ];
    }

    public function getActiveBookings($sitterId) {
        return [
            [
                'id' => 1,
                'pet_name' => 'Luna',
                'pet_type' => 'Cat',
                'owner_name' => 'Jessica Brown',
                'start_date' => '2025-10-14',
                'end_date' => '2025-10-18',
                'status' => 'active',
                'special_notes' => 'Needs medication twice daily'
            ],
            [
                'id' => 2,
                'pet_name' => 'Rocky',
                'pet_type' => 'Dog',
                'owner_name' => 'Tom Anderson',
                'start_date' => '2025-10-15',
                'end_date' => '2025-10-20',
                'status' => 'active',
                'special_notes' => 'Loves walks in the park'
            ]
        ];
    }

    public function getUpcomingBookings($sitterId) {
        return [
            [
                'id' => 3,
                'pet_name' => 'Whiskers',
                'pet_type' => 'Cat',
                'owner_name' => 'Mary Smith',
                'start_date' => '2025-10-22',
                'end_date' => '2025-10-25',
                'status' => 'confirmed'
            ]
        ];
    }
}
