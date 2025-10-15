<?php
require_once __DIR__ . '/../BaseModel.php';

class SitterSettingsModel extends BaseModel {
    
    public function getProfile($sitterId) {
        return [
            'id' => $sitterId,
            'name' => 'Sarah Sitter',
            'email' => 'sarah.sitter@petvet.com',
            'phone' => '555-0200',
            'experience_years' => 5,
            'pet_types' => 'Dogs, Cats',
            'home_type' => 'House with Yard',
            'address' => '456 Sitting Lane',
            'city' => 'Pet Town',
            'postal_code' => '54321',
            'avatar' => '/PETVET/public/images/default-avatar.png'
        ];
    }

    public function getPreferences($sitterId) {
        return [
            'notifications_email' => true,
            'notifications_sms' => true,
            'booking_reminders' => '48h',
            'max_pets' => 3,
            'availability' => 'Flexible'
        ];
    }
}
