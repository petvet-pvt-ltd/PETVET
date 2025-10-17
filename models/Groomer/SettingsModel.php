<?php
require_once __DIR__ . '/../BaseModel.php';

class GroomerSettingsModel extends BaseModel {
    
    /**
     * Get groomer profile information
     */
    public function getProfile($groomerId) {
        // Mock data - replace with actual database queries
        return [
            'name' => 'Grooming Pro',
            'email' => 'groomer@petvet.com',
            'phone' => '+1 234 567 8900',
            'address' => '123 Pet Street',
            'city' => 'Pet City',
            'postal_code' => '12345',
            'avatar' => 'https://placehold.co/200x200?text=Groomer',
            'cover_photo' => 'https://placehold.co/1200x300?text=Grooming+Salon',
            'experience_years' => 8,
            'specializations' => 'Dogs, Cats, Show Grooming',
            'certifications' => 'Certified Professional Groomer',
            'bio' => 'Passionate pet groomer with over 8 years of experience. Specialized in breed-specific cuts and gentle handling.',
            'google_maps_link' => 'https://maps.google.com',
            'date_joined' => '2020-05-15',
            'verified_email' => true,
            'verified_phone' => true
        ];
    }
    
    /**
     * Get groomer preferences
     */
    public function getPreferences($groomerId) {
        // Mock data - replace with actual database queries
        return [
            'email_notifications' => true,
            'sms_notifications' => true,
            'booking_reminders' => 24,
            'newsletter_subscription' => true,
            'marketing_emails' => false,
            'language' => 'en',
            'timezone' => 'Asia/Colombo',
            'theme' => 'light',
            'auto_accept_bookings' => false,
            'max_bookings_per_day' => 6
        ];
    }
    
    /**
     * Update profile information
     */
    public function updateProfile($groomerId, $data) {
        // Mock response - replace with actual database update
        return [
            'success' => true,
            'message' => 'Profile updated successfully'
        ];
    }
    
    /**
     * Update preferences
     */
    public function updatePreferences($groomerId, $data) {
        // Mock response - replace with actual database update
        return [
            'success' => true,
            'message' => 'Preferences updated successfully'
        ];
    }
}
