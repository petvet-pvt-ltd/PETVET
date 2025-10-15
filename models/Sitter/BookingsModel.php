<?php
require_once __DIR__ . '/../BaseModel.php';

class SitterBookingsModel extends BaseModel {
    
    public function getAllBookings($sitterId) {
        return array_merge(
            $this->getPendingBookings($sitterId),
            $this->getActiveBookings($sitterId),
            $this->getCompletedBookings($sitterId)
        );
    }

    public function getPendingBookings($sitterId) {
        return [
            [
                'id' => 4,
                'pet_name' => 'Buddy',
                'pet_type' => 'Dog',
                'pet_breed' => 'Beagle',
                'owner_name' => 'Chris Wilson',
                'owner_phone' => '555-0201',
                'start_date' => '2025-10-28',
                'end_date' => '2025-10-30',
                'status' => 'pending',
                'daily_rate' => 45,
                'special_notes' => 'First time boarding'
            ]
        ];
    }

    public function getActiveBookings($sitterId) {
        return [
            [
                'id' => 1,
                'pet_name' => 'Luna',
                'pet_type' => 'Cat',
                'pet_breed' => 'Persian',
                'owner_name' => 'Jessica Brown',
                'owner_phone' => '555-0202',
                'start_date' => '2025-10-14',
                'end_date' => '2025-10-18',
                'status' => 'active',
                'daily_rate' => 35,
                'special_notes' => 'Needs medication twice daily'
            ]
        ];
    }

    public function getCompletedBookings($sitterId) {
        return [
            [
                'id' => 5,
                'pet_name' => 'Max',
                'pet_type' => 'Dog',
                'pet_breed' => 'Poodle',
                'owner_name' => 'Linda Green',
                'owner_phone' => '555-0203',
                'start_date' => '2025-10-05',
                'end_date' => '2025-10-10',
                'status' => 'completed',
                'daily_rate' => 50,
                'special_notes' => 'Very friendly, loves toys'
            ]
        ];
    }
}
