<?php
require_once __DIR__ . '/../BaseModel.php';
require_once __DIR__ . '/SitterData.php';

class SitterDashboardModel extends BaseModel {
    
    public function getStats($sitterId) {
        return SitterData::getStats();
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

    public function getUpcomingBookings($sitterId, $limit = 5) {
        return SitterData::getUpcomingBookings($limit);
    }
}
