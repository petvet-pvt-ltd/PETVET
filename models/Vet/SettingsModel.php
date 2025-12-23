<?php
require_once __DIR__ . '/../BaseModel.php';

class VetSettingsModel extends BaseModel {
    
    /**
     * Get vet profile data
     */
    public function getVetProfile($userId) {
        $stmt = $this->db->prepare("
            SELECT 
                u.first_name,
                u.last_name,
                u.email,
                u.phone,
                u.avatar,
                u.created_at as date_joined,
                u.email_verified,
                v.specialization,
                v.license_number,
                v.years_experience,
                v.consultation_fee,
                v.bio
            FROM users u
            JOIN vets v ON u.id = v.user_id
            WHERE u.id = ?
        ");
        $stmt->execute([$userId]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Set default avatar if empty
        if ($profile && empty($profile['avatar'])) {
            $profile['avatar'] = '/PETVET/public/images/emptyProfPic.png';
        }
        
        if (!$profile) {
            // Fallback if vet not found
            return [
                'first_name' => '',
                'last_name' => '',
                'email' => '',
                'phone' => '',
                'avatar' => '/PETVET/public/images/emptyProfPic.png',
                'specialization' => '',
                'license_number' => '',
                'years_experience' => 0,
                'consultation_fee' => 0.00,
                'bio' => '',
                'date_joined' => date('Y-m-d'),
                'email_verified' => false
            ];
        }
        
        return $profile;
    }
    
    /**
     * Get user preferences
     */
    public function getPreferences($userId) {
        // Note: user_preferences table may not exist
        // Return default preferences for now
        // TODO: Create user_preferences table if needed for future functionality
        
        return [
            'email_notifications' => true,
            'sms_notifications' => false,
            'reminder_appointments' => 24,
            'language' => 'en',
            'timezone' => 'Asia/Colombo',
            'theme' => 'light'
        ];
    }
    
    /**
     * Get account statistics
     */
    public function getAccountStats($userId) {
        // Get total appointments
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total FROM appointments WHERE vet_id = ?
        ");
        $stmt->execute([$userId]);
        $totalAppointments = $stmt->fetchColumn();
        
        // Get completed appointments
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as completed FROM appointments 
            WHERE vet_id = ? AND status = 'completed'
        ");
        $stmt->execute([$userId]);
        $completedAppointments = $stmt->fetchColumn();
        
        // Get total unique patients
        $stmt = $this->db->prepare("
            SELECT COUNT(DISTINCT pet_id) as patients FROM appointments 
            WHERE vet_id = ?
        ");
        $stmt->execute([$userId]);
        $totalPatients = $stmt->fetchColumn();
        
        // Get account age
        $stmt = $this->db->prepare("
            SELECT DATEDIFF(NOW(), created_at) as age_days FROM users WHERE id = ?
        ");
        $stmt->execute([$userId]);
        $accountAgeDays = $stmt->fetchColumn();
        
        // Get last login
        $stmt = $this->db->prepare("
            SELECT last_login FROM users WHERE id = ?
        ");
        $stmt->execute([$userId]);
        $lastLogin = $stmt->fetchColumn();
        
        return [
            'total_appointments' => $totalAppointments ?? 0,
            'completed_appointments' => $completedAppointments ?? 0,
            'total_patients' => $totalPatients ?? 0,
            'account_age_days' => $accountAgeDays ?? 0,
            'last_login' => $lastLogin ?? date('Y-m-d H:i:s'),
            'profile_completion' => 100 // Calculate based on filled fields if needed
        ];
    }
}
