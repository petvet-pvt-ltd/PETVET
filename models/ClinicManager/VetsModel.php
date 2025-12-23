<?php
require_once __DIR__ . '/../BaseModel.php';

class VetsModel extends BaseModel {
    
    /**
     * Fetch all vets for a specific clinic from the database
     * @param int $clinicId - The clinic ID of the logged-in clinic manager
     * @return array - Array of vet data
     */
    public function fetchVetsData(int $clinicId): array {
        $sql = "
            SELECT 
                v.user_id as id,
                v.user_id,
                CONCAT(u.first_name, ' ', u.last_name) as name,
                u.email,
                u.phone,
                CASE WHEN v.available = 1 THEN 'Active' ELSE 'Inactive' END as status,
                u.avatar,
                v.specialization,
                v.license_number,
                v.years_experience,
                v.consultation_fee,
                v.rating,
                v.bio,
                v.available,
                (SELECT MIN(a.appointment_date) 
                 FROM appointments a 
                 WHERE a.vet_id = v.user_id 
                 AND a.appointment_date >= CURDATE() 
                 AND a.status != 'cancelled'
                ) as next_appointment_date,
                (SELECT MIN(a.appointment_time) 
                 FROM appointments a 
                 WHERE a.vet_id = v.user_id 
                 AND a.appointment_date >= CURDATE() 
                 AND a.status != 'cancelled'
                ) as next_appointment_time
            FROM vets v
            JOIN users u ON v.user_id = u.id
            WHERE v.clinic_id = :clinic_id
            ORDER BY u.first_name, u.last_name ASC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['clinic_id' => $clinicId]);
        $vets = $stmt->fetchAll();
        
        // Format the data for the view
        $formatted = [];
        foreach ($vets as $vet) {
            $nextSlot = null;
            if ($vet['next_appointment_date'] && $vet['next_appointment_time']) {
                $nextSlot = $vet['next_appointment_date'] . ' ' . $vet['next_appointment_time'];
            }
            
            $formatted[] = [
                'id' => $vet['id'],
                'user_id' => $vet['user_id'],
                'name' => $vet['name'],
                'email' => $vet['email'],
                'phone' => $vet['phone'],
                'photo' => !empty($vet['avatar']) ? $vet['avatar'] : '/PETVET/public/images/emptyProfPic.png',
                'specialization' => $vet['specialization'] ?? 'General',
                'license_number' => $vet['license_number'] ?? 'N/A',
                'years_experience' => $vet['years_experience'] ?? 0,
                'consultation_fee' => $vet['consultation_fee'] ?? 0.00,
                'rating' => $vet['rating'] ?? 0.00,
                'bio' => $vet['bio'] ?? '',
                'status' => $vet['status'] ?? 'Active',
                'available' => $vet['available'] ?? 1,
                'next_slot' => $nextSlot,
                'on_duty_dates' => [] // This would need clinic_weekly_schedule integration
            ];
        }
        
        return $formatted;
    }
    
    /**
     * Fetch pending vet requests for a clinic
     * @param int $clinicId - The clinic ID
     * @return array - Array of pending requests
     */
    public function fetchPendingRequests(int $clinicId): array {
        // Note: With the new system, vets are created directly in the vets table
        // Pending requests would need to come from a different workflow
        // For now, return empty array as pending vet requests are handled differently
        
        // If you need pending vet applications, you would query user_roles 
        // where role='vet' and verification_status='pending'
        // But those vets won't have entries in the vets table until approved
        
        $sql = "
            SELECT 
                ur.id as request_id,
                u.id as user_id,
                CONCAT(u.first_name, ' ', u.last_name) as name,
                u.email,
                u.phone,
                u.avatar,
                ur.applied_at,
                ur.verification_status
            FROM user_roles ur
            JOIN users u ON ur.user_id = u.id
            JOIN roles r ON ur.role_id = r.id
            WHERE r.role_name = 'vet'
            AND ur.verification_status = 'pending'
            ORDER BY ur.applied_at DESC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $requests = $stmt->fetchAll();
        
        // Format for view
        $formatted = [];
        foreach ($requests as $req) {
            $formatted[] = [
                'id' => $req['request_id'],
                'user_id' => $req['user_id'],
                'name' => $req['name'],
                'email' => $req['email'],
                'phone' => $req['phone'] ?? 'N/A',
                'photo' => $req['avatar'] ?? 'https://i.pravatar.cc/64?img=' . ($req['user_id'] % 70),
                'specialization' => 'General',
                'license' => 'Pending',
                'experience' => 'N/A',
                'education' => 'Not provided',
                'bio' => '',
                'applied_date' => date('M d, Y', strtotime($req['applied_at']))
            ];
        }
        
        return $formatted;
    }
}
?>