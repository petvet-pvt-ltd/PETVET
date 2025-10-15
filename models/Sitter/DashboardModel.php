<?php
require_once __DIR__ . '/../BaseModel.php';

class SitterDashboardModel extends BaseModel {
    
    public function getStats($sitterId) {
        return [
            'active_bookings' => 3,
            'total_pets_cared' => 87,
            'completed_bookings' => 2,
            'pending_requests' => 3
        ];
    }

    public function getActiveBookings($sitterId) {
        return [
            [
                'id' => 4,
                'pet_name' => 'Luna & Shadow',
                'pet_type' => 'Dog',
                'owner_name' => 'Maria Garcia',
                'start_date' => '2025-10-20',
                'end_date' => '2025-10-20',
                'status' => 'confirmed',
                'special_notes' => 'Two friendly Golden Retrievers need their daily walk.'
            ],
            [
                'id' => 5,
                'pet_name' => 'Whiskers',
                'pet_type' => 'Cat',
                'owner_name' => 'Tom Wilson',
                'start_date' => '2025-10-20',
                'end_date' => '2025-10-22',
                'status' => 'confirmed',
                'special_notes' => 'Indoor cat needs daily feeding, litter cleaning, and companionship.'
            ],
            [
                'id' => 6,
                'pet_name' => 'Rocky',
                'pet_type' => 'Dog',
                'owner_name' => 'James Miller',
                'start_date' => '2025-10-19',
                'end_date' => '2025-10-21',
                'status' => 'confirmed',
                'special_notes' => 'Active dog needs plenty of exercise. Has special diet.'
            ]
        ];
    }

    public function getUpcomingBookings($sitterId) {
        return [
            [
                'id' => 1,
                'pet_name' => 'Buddy',
                'pet_type' => 'Dog',
                'owner_name' => 'Lisa Chen',
                'start_date' => '2025-10-20',
                'end_date' => '2025-10-20',
                'status' => 'pending'
            ],
            [
                'id' => 2,
                'pet_name' => 'Charlie',
                'pet_type' => 'Dog',
                'owner_name' => 'David Smith',
                'start_date' => '2025-10-22',
                'end_date' => '2025-10-24',
                'status' => 'pending'
            ],
            [
                'id' => 3,
                'pet_name' => 'Mittens',
                'pet_type' => 'Cat',
                'owner_name' => 'Sarah Johnson',
                'start_date' => '2025-10-25',
                'end_date' => '2025-10-27',
                'status' => 'pending'
            ]
        ];
    }
}
