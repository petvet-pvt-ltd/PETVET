<?php
require_once __DIR__ . '/../BaseModel.php';
require_once __DIR__ . '/../../config/connect.php';

class SettingsModel extends BaseModel {
    private $conn;
    
    public function __construct() {
        parent::__construct();
        global $conn;
        $this->conn = $conn;
    }
    
    public function getUserProfile($userId) {
        $sql = "SELECT 
                    id,
                    CONCAT(first_name, ' ', last_name) as name,
                    first_name,
                    last_name,
                    email,
                    phone,
                    address,
                    avatar,
                    email_verified,
                    created_at as date_joined
                FROM users 
                WHERE id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return null;
        }
        
        $profile = $result->fetch_assoc();
        
        // Set default avatar if none exists
        if (empty($profile['avatar'])) {
            $profile['avatar'] = '/PETVET/public/images/emptyProfPic.png';
        }
        
        return $profile;
    }
    
    public function getUserPreferences($userId) {
        // Mock user preferences - will implement with database table later
        $preferences = [
            1 => [
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
            ],
            2 => [
                'email_notifications' => true,
                'sms_notifications' => true,
                'reminder_appointments' => 48,
                'reminder_vaccinations' => 336,
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
        
        if (empty($data['first_name'])) {
            $errors[] = 'First name is required';
        }
        
        if (empty($data['last_name'])) {
            $errors[] = 'Last name is required';
        }
        
        // Validate Sri Lankan phone number if provided
        if (!empty($data['phone'])) {
            $phone = trim($data['phone']);
            if (!preg_match('/^07\d{8}$/', $phone)) {
                $errors[] = 'Phone number must be 10 digits starting with 07';
            }
        }
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        // Don't allow email changes for security - it's the login username
        // Build SQL dynamically based on whether avatar is being updated
        if (isset($data['avatar'])) {
            $sql = "UPDATE users SET 
                        first_name = ?,
                        last_name = ?,
                        phone = ?,
                        address = ?,
                        avatar = ?,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE id = ?";
            
            $stmt = $this->conn->prepare($sql);
            $phone = $data['phone'] ?? null;
            $address = $data['address'] ?? null;
            $stmt->bind_param('sssssi', $data['first_name'], $data['last_name'], $phone, $address, $data['avatar'], $userId);
        } else {
            $sql = "UPDATE users SET 
                        first_name = ?,
                        last_name = ?,
                        phone = ?,
                        address = ?,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE id = ?";
            
            $stmt = $this->conn->prepare($sql);
            $phone = $data['phone'] ?? null;
            $address = $data['address'] ?? null;
            $stmt->bind_param('ssssi', $data['first_name'], $data['last_name'], $phone, $address, $userId);
        }
        
        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Profile updated successfully!'
            ];
        } else {
            return [
                'success' => false,
                'errors' => ['Failed to update profile']
            ];
        }
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
        
        // Verify current password
        $sql = "SELECT password FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return ['success' => false, 'errors' => ['User not found']];
        }
        
        $user = $result->fetch_assoc();
        
        if (!password_verify($currentPassword, $user['password'])) {
            return ['success' => false, 'errors' => ['Current password is incorrect']];
        }
        
        // Update password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateSql = "UPDATE users SET password = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $updateStmt = $this->conn->prepare($updateSql);
        $updateStmt->bind_param('si', $hashedPassword, $userId);
        
        if ($updateStmt->execute()) {
            return [
                'success' => true,
                'message' => 'Password changed successfully!'
            ];
        } else {
            return [
                'success' => false,
                'errors' => ['Failed to update password']
            ];
        }
    }
    
    public function getAccountStats($userId) {
        // Get total pets
        $petsResult = $this->conn->query("SELECT COUNT(*) as count FROM pets WHERE user_id = $userId AND is_active = 1");
        $totalPets = $petsResult->fetch_assoc()['count'];
        
        // Get active appointments (upcoming appointments with status 'pending' or 'approved')
        $appointmentsResult = $this->conn->query(
            "SELECT COUNT(*) as count FROM appointments 
             WHERE pet_owner_id = $userId 
             AND status IN ('pending', 'approved') 
             AND appointment_date >= CURDATE()"
        );
        $activeAppointments = $appointmentsResult->fetch_assoc()['count'];
        
        // Get user info for account age and last login
        $userResult = $this->conn->query(
            "SELECT created_at, last_login FROM users WHERE id = $userId"
        );
        $userInfo = $userResult->fetch_assoc();
        
        $accountAgeDays = 0;
        if ($userInfo && $userInfo['created_at']) {
            $accountAgeDays = (time() - strtotime($userInfo['created_at'])) / (60 * 60 * 24);
        }
        
        // Calculate profile completion
        $profileResult = $this->conn->query(
            "SELECT 
                (CASE WHEN first_name IS NOT NULL AND first_name != '' THEN 20 ELSE 0 END) +
                (CASE WHEN last_name IS NOT NULL AND last_name != '' THEN 20 ELSE 0 END) +
                (CASE WHEN phone IS NOT NULL AND phone != '' THEN 20 ELSE 0 END) +
                (CASE WHEN address IS NOT NULL AND address != '' THEN 20 ELSE 0 END) +
                (CASE WHEN avatar IS NOT NULL AND avatar != '' THEN 20 ELSE 0 END) as completion
             FROM users WHERE id = $userId"
        );
        $profileCompletion = $profileResult->fetch_assoc()['completion'];
        
        return [
            'total_pets' => $totalPets,
            'active_appointments' => $activeAppointments,
            'total_medical_records' => 0, // TODO: implement when medical records table exists
            'account_age_days' => (int)$accountAgeDays,
            'last_login' => $userInfo['last_login'] ?? date('Y-m-d H:i:s'),
            'profile_completion' => $profileCompletion
        ];
    }
}
?>
