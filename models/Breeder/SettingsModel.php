<?php
require_once __DIR__ . '/../BaseModel.php';

class BreederSettingsModel extends BaseModel {
    
    public function getProfile($breederId) {
        return [
            'id' => $breederId,
            'name' => 'David Breeder',
            'email' => 'david.breeder@petvet.com',
            'phone' => '555-0300',
            'business_name' => 'Premium Paws Breeding',
            'license_number' => 'BR-2025-1234',
            'experience_years' => 12,
            'specialization' => 'Large Breeds',
            'address' => '789 Breeding Road',
            'city' => 'Pet Village',
            'postal_code' => '67890',
            'avatar' => '/PETVET/public/images/default-avatar.png'
        ];
    }

    public function getPreferences($breederId) {
        return [
            'notifications_email' => true,
            'notifications_sms' => true,
            'inquiry_alerts' => true,
            'public_profile' => true,
            'show_pricing' => true
        ];
    }
}
