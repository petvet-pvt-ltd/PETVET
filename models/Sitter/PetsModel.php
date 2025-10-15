<?php
require_once __DIR__ . '/../BaseModel.php';

class SitterPetsModel extends BaseModel {
    
    public function getCurrentPets($sitterId) {
        return [
            [
                'id' => 1,
                'name' => 'Luna',
                'type' => 'Cat',
                'breed' => 'Persian',
                'age' => 3,
                'owner' => 'Jessica Brown',
                'check_in' => '2025-10-14',
                'check_out' => '2025-10-18',
                'special_needs' => 'Medication schedule',
                'feeding_instructions' => 'Twice daily, morning and evening'
            ],
            [
                'id' => 2,
                'name' => 'Rocky',
                'type' => 'Dog',
                'breed' => 'Labrador',
                'age' => 2,
                'owner' => 'Tom Anderson',
                'check_in' => '2025-10-15',
                'check_out' => '2025-10-20',
                'special_needs' => 'Daily exercise',
                'feeding_instructions' => 'Three times daily'
            ]
        ];
    }

    public function getPetHistory($sitterId) {
        return [
            ['name' => 'Max', 'type' => 'Dog', 'visits' => 5, 'last_visit' => '2025-10-10'],
            ['name' => 'Whiskers', 'type' => 'Cat', 'visits' => 3, 'last_visit' => '2025-09-28'],
            ['name' => 'Buddy', 'type' => 'Dog', 'visits' => 2, 'last_visit' => '2025-09-15']
        ];
    }

    public function getPetStats($sitterId) {
        return [
            'total_pets' => 87,
            'dogs' => 52,
            'cats' => 35,
            'current' => 2
        ];
    }
}
