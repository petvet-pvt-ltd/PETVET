<?php
require_once __DIR__ . '/../BaseModel.php';

class SettingsModel extends BaseModel {
    
    public function getUserProfile($userId) {
        // Mock user profile data - in real app this would come from database
        $profiles = [
            1 => [
                'id' => 1,
                'name' => 'Janith Perera',
                'email' => 'janith@example.com',
                'phone' => '+94 77 123 4567',
                'address' => '123 Galle Road, Colombo 03',
                'city' => 'Colombo',
                'postal_code' => '00300',
                'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?q=80&w=256&auto=format&fit=crop',
                'date_joined' => '2024-06-15',
                'verified_email' => true,
                'verified_phone' => true
            ],
            2 => [
                'id' => 2,
                'name' => 'Kasun Perera',
                'email' => 'kasun@example.com',
                'phone' => '+94 71 987 6543',
                'address' => '456 Temple Street, Kandy',
                'city' => 'Kandy',
                'postal_code' => '20000',
                'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?q=80&w=256&auto=format&fit=crop',
                'date_joined' => '2024-08-20',
                'verified_email' => true,
                'verified_phone' => false
            ]
        ];
        
        return $profiles[$userId] ?? null;
    }
    
    public function getUserPreferences($userId) {
        // Mock user preferences - in real app this would come from database
        $preferences = [
            1 => [
                'email_notifications' => true,
                'sms_notifications' => false,
                'reminder_appointments' => 24, // hours before
                'reminder_vaccinations' => 168, // 1 week before
                'newsletter_subscription' => true,
                'marketing_emails' => false,
                'language' => 'en',
                'timezone' => 'Asia/Colombo',
                'theme' => 'light',
                'privacy_profile' => 'public'
            ],
            2 => [
                'email_notifications' => true,
                'sms_notifications' => true,
                'reminder_appointments' => 48, // 2 days before
                'reminder_vaccinations' => 336, // 2 weeks before
                'newsletter_subscription' => false,
                'marketing_emails' => false,
                'language' => 'en',
                'timezone' => 'Asia/Colombo',
                'theme' => 'dark',
                'privacy_profile' => 'private'
            ]
        ];
        
        return $preferences[$userId] ?? $this->getDefaultPreferences();
    }
    
    public function getDefaultPreferences() {
        return [
            'email_notifications' => true,
            'sms_notifications' => false,
            'reminder_appointments' => 24,
            'reminder_vaccinations' => 168,
            'newsletter_subscription' => true,
            'marketing_emails' => false,
            'language' => 'en',
            'timezone' => 'Asia/Colombo',
            'theme' => 'light',
            'privacy_profile' => 'public'
        ];
    }
    
    public function updateUserProfile($userId, $data) {
        // Validate profile data
        $errors = [];
        
        if (empty($data['name'])) {
            $errors[] = 'Name is required';
        }
        
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email address is required';
        }
        
        if (!empty($data['phone']) && !preg_match('/^\+94\s?[0-9]{2}\s?[0-9]{3}\s?[0-9]{4}$/', $data['phone'])) {
            $errors[] = 'Phone number must be in format +94 XX XXX XXXX';
        }
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        // In real app, this would update the database
        return [
            'success' => true,
            'message' => 'Profile updated successfully!'
        ];
    }
    
    public function updateUserPreferences($userId, $preferences) {
        // Validate preferences
        $validPreferences = $this->getDefaultPreferences();
        $filteredPreferences = [];
        
        foreach ($preferences as $key => $value) {
            if (array_key_exists($key, $validPreferences)) {
                // Type casting and validation
                switch ($key) {
                    case 'email_notifications':
                    case 'sms_notifications':
                    case 'newsletter_subscription':
                    case 'marketing_emails':
                        $filteredPreferences[$key] = (bool) $value;
                        break;
                    case 'reminder_appointments':
                    case 'reminder_vaccinations':
                        $filteredPreferences[$key] = max(0, (int) $value);
                        break;
                    case 'language':
                        $filteredPreferences[$key] = in_array($value, ['en', 'si', 'ta']) ? $value : 'en';
                        break;
                    case 'timezone':
                        $filteredPreferences[$key] = $value === 'Asia/Colombo' ? $value : 'Asia/Colombo';
                        break;
                    case 'theme':
                        $filteredPreferences[$key] = in_array($value, ['light', 'dark']) ? $value : 'light';
                        break;
                    case 'privacy_profile':
                        $filteredPreferences[$key] = in_array($value, ['public', 'private']) ? $value : 'public';
                        break;
                    default:
                        $filteredPreferences[$key] = $value;
                }
            }
        }
        
        // In real app, this would update the database
        return [
            'success' => true,
            'message' => 'Preferences updated successfully!'
        ];
    }
    
    public function changePassword($userId, $currentPassword, $newPassword, $confirmPassword) {
        // Validate password change
        $errors = [];
        
        if (empty($currentPassword)) {
            $errors[] = 'Current password is required';
        }
        
        if (empty($newPassword)) {
            $errors[] = 'New password is required';
        } elseif (strlen($newPassword) < 8) {
            $errors[] = 'New password must be at least 8 characters long';
        }
        
        if ($newPassword !== $confirmPassword) {
            $errors[] = 'Password confirmation does not match';
        }
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        // In real app, verify current password against database hash
        // For demo, assume validation passes
        
        return [
            'success' => true,
            'message' => 'Password changed successfully!'
        ];
    }
    
    public function getAccountStats($userId) {
        // Mock account statistics
        return [
            'total_pets' => 3,
            'active_appointments' => 2,
            'total_medical_records' => 8,
            'account_age_days' => (time() - strtotime('2024-06-15')) / (60 * 60 * 24),
            'last_login' => date('Y-m-d H:i:s', strtotime('-2 hours')),
            'profile_completion' => 85 // percentage
        ];
    }
}
?>