<?php
/**
 * Clinic Manager Settings Model
 * Handles all settings data retrieval and updates
 */

require_once __DIR__ . '/../../config/connect.php';

class ClinicManagerSettingsModel {
    private $pdo;
    
    public function __construct() {
        $this->pdo = db();
    }
    
    /**
     * Get manager profile data
     */
    public function getManagerProfile($userId) {
        $stmt = $this->pdo->prepare("
            SELECT id, email, first_name, last_name, phone, avatar, 
                   email_verified, created_at
            FROM users 
            WHERE id = ?
        ");
        $stmt->execute([$userId]);
        $profile = $stmt->fetch();
        
        if (!$profile['avatar']) {
            $profile['avatar'] = '/PETVET/public/images/emptyProfPic.png';
        }
        
        return $profile;
    }
    
    /**
     * Get clinic data for the manager
     */
    public function getClinicData($userId) {
        $stmt = $this->pdo->prepare("
            SELECT c.* 
            FROM clinics c
            JOIN clinic_manager_profiles cm ON c.id = cm.clinic_id
            WHERE cm.user_id = ?
        ");
        $stmt->execute([$userId]);
        $clinic = $stmt->fetch();
        
        if (!$clinic) {
            return null;
        }
        
        // Set defaults for images if not set
        if (empty($clinic['clinic_logo'])) {
            $clinic['clinic_logo'] = 'https://static.vecteezy.com/system/resources/previews/005/601/780/non_2x/veterinary-clinic-logo-vector.jpg';
        }
        if (empty($clinic['clinic_cover'])) {
            $clinic['clinic_cover'] = 'https://img.freepik.com/free-vector/veterinary-clinic-social-media-cover-template_23-2149716789.jpg';
        }
        
        return [
            'id' => $clinic['id'],
            'name' => $clinic['clinic_name'],
            'description' => $clinic['clinic_description'] ?? 'Trusted pet healthcare and wellness.',
            'address' => $clinic['clinic_address'],
            'map_pin' => $clinic['map_location'] ?? '',
            'phone' => $clinic['clinic_phone'],
            'email' => $clinic['clinic_email'],
            'logo' => $clinic['clinic_logo'],
            'cover' => $clinic['clinic_cover']
        ];
    }
    
    /**
     * Get clinic preferences
     */
    public function getPreferences($userId) {
        $clinicId = $this->getClinicId($userId);
        
        if (!$clinicId) {
            return [
                'email_notifications' => true,
                'slot_duration_minutes' => 20
            ];
        }
        
        $stmt = $this->pdo->prepare("
            SELECT email_notifications, slot_duration_minutes
            FROM clinic_preferences
            WHERE clinic_id = ?
        ");
        $stmt->execute([$clinicId]);
        $prefs = $stmt->fetch();
        
        if (!$prefs) {
            return [
                'email_notifications' => true,
                'slot_duration_minutes' => 20
            ];
        }
        
        return [
            'email_notifications' => (bool)$prefs['email_notifications'],
            'slot_duration_minutes' => (int)$prefs['slot_duration_minutes']
        ];
    }
    
    /**
     * Get weekly schedule
     */
    public function getWeeklySchedule($userId) {
        $clinicId = $this->getClinicId($userId);
        
        if (!$clinicId) {
            return $this->getDefaultSchedule();
        }
        
        $stmt = $this->pdo->prepare("
            SELECT day_of_week, is_enabled, start_time, end_time
            FROM clinic_weekly_schedule
            WHERE clinic_id = ?
            ORDER BY FIELD(day_of_week, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')
        ");
        $stmt->execute([$clinicId]);
        $schedule = $stmt->fetchAll();
        
        if (empty($schedule)) {
            return $this->getDefaultSchedule();
        }
        
        $result = [];
        $dayLabels = [
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday'
        ];
        
        foreach ($schedule as $day) {
            $result[] = [
                'id' => $day['day_of_week'],
                'label' => $dayLabels[$day['day_of_week']],
                'start' => substr($day['start_time'], 0, 5), // HH:MM format
                'end' => substr($day['end_time'], 0, 5),
                'active' => (bool)$day['is_enabled']
            ];
        }
        
        return $result;
    }
    
    /**
     * Get blocked days
     */
    public function getBlockedDays($userId) {
        $clinicId = $this->getClinicId($userId);
        
        if (!$clinicId) {
            return [];
        }
        
        $stmt = $this->pdo->prepare("
            SELECT blocked_date, reason
            FROM clinic_blocked_days
            WHERE clinic_id = ?
            ORDER BY blocked_date ASC
        ");
        $stmt->execute([$clinicId]);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get clinic ID for user
     */
    private function getClinicId($userId) {
        $stmt = $this->pdo->prepare("
            SELECT clinic_id 
            FROM clinic_manager_profiles 
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }
    
    /**
     * Get default weekly schedule
     */
    private function getDefaultSchedule() {
        return [
            ['id' => 'monday', 'label' => 'Monday', 'start' => '09:00', 'end' => '17:00', 'active' => true],
            ['id' => 'tuesday', 'label' => 'Tuesday', 'start' => '09:00', 'end' => '17:00', 'active' => true],
            ['id' => 'wednesday', 'label' => 'Wednesday', 'start' => '09:00', 'end' => '17:00', 'active' => true],
            ['id' => 'thursday', 'label' => 'Thursday', 'start' => '09:00', 'end' => '17:00', 'active' => true],
            ['id' => 'friday', 'label' => 'Friday', 'start' => '09:00', 'end' => '17:00', 'active' => true],
            ['id' => 'saturday', 'label' => 'Saturday', 'start' => '10:00', 'end' => '14:00', 'active' => true],
            ['id' => 'sunday', 'label' => 'Sunday', 'start' => '09:00', 'end' => '13:00', 'active' => false]
        ];
    }
}
?>
