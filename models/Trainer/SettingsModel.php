<?php
require_once __DIR__ . '/../BaseModel.php';

class TrainerSettingsModel extends BaseModel {
    
    public function getProfile($trainerId) {
        return [
            'id' => $trainerId,
            'name' => 'John Trainer',
            'email' => 'john.trainer@petvet.com',
            'phone' => '555-0100',
            'specialization' => 'Obedience & Agility',
            'experience_years' => 8,
            'certifications' => 'Certified Dog Trainer (CDT)',
            'address' => '123 Training Street',
            'city' => 'Pet City',
            'postal_code' => '12345',
            'avatar' => '/PETVET/public/images/default-avatar.png'
        ];
    }

    public function getPreferences($trainerId) {
        return [
            'notifications_email' => true,
            'notifications_sms' => false,
            'session_reminders' => '24h',
            'availability' => 'Mon-Fri 9AM-6PM'
        ];
    }
}
