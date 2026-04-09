<?php
require_once __DIR__ . '/../BaseModel.php';

class SitterPetsModel extends BaseModel {
    
    public function getCurrentPets($sitterId) {
        return [];
    }

    public function getPetHistory($sitterId) {
        return [];
    }

    public function getPetStats($sitterId) {
        return [
            'total_pets' => 0,
            'dogs' => 0,
            'cats' => 0,
            'current' => 0
        ];
    }
}
