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
                cs.id,
                cs.user_id,
                cs.name,
                cs.email,
                cs.phone,
                cs.status,
                u.avatar,
                vp.specialization,
                vp.license_number,
                vp.years_experience,
                vp.consultation_fee,
                vp.rating,
                vp.bio,
                vp.available,
                (SELECT MIN(a.appointment_date) 
                 FROM appointments a 
                 WHERE a.vet_id = cs.user_id 
                 AND a.appointment_date >= CURDATE() 
                 AND a.status != 'cancelled'
                ) as next_appointment_date,
                (SELECT MIN(a.appointment_time) 
                 FROM appointments a 
                 WHERE a.vet_id = cs.user_id 
                 AND a.appointment_date >= CURDATE() 
                 AND a.status != 'cancelled'
                ) as next_appointment_time
            FROM clinic_staff cs
            LEFT JOIN users u ON cs.user_id = u.id
            LEFT JOIN vet_profiles vp ON cs.user_id = vp.user_id
            WHERE cs.clinic_id = :clinic_id 
            AND cs.role = 'vet'
            ORDER BY cs.name ASC
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
                'photo' => $vet['avatar'] ?? 'https://i.pravatar.cc/64?img=' . ($vet['id'] % 70),
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
        $sql = "
            SELECT 
                ur.id as request_id,
                u.id as user_id,
                CONCAT(u.first_name, ' ', u.last_name) as name,
                u.email,
                u.phone,
                u.avatar,
                vp.specialization,
                vp.license_number,
                vp.years_experience,
                vp.education,
                vp.bio,
                ur.applied_at,
                ur.verification_status
            FROM user_roles ur
            JOIN users u ON ur.user_id = u.id
            JOIN roles r ON ur.role_id = r.id
            LEFT JOIN vet_profiles vp ON u.id = vp.user_id
            WHERE r.role_name = 'vet'
            AND ur.verification_status = 'pending'
            AND (vp.clinic_id = :clinic_id OR vp.clinic_id IS NULL)
            ORDER BY ur.applied_at DESC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['clinic_id' => $clinicId]);
        $requests = $stmt->fetchAll();
        
        // Format for view
        $formatted = [];
        foreach ($requests as $req) {
            $formatted[] = [
                'id' => $req['request_id'],
                'user_id' => $req['user_id'],
                'name' => $req['name'],
                'email' => $req['email'],
                'phone' => $req['phone'],
                'photo' => $req['avatar'] ?? 'https://i.pravatar.cc/64?img=' . ($req['user_id'] % 70),
                'specialization' => $req['specialization'] ?? 'General',
                'license' => $req['license_number'] ?? 'N/A',
                'experience' => ($req['years_experience'] ?? 0) . ' years',
                'education' => $req['education'] ?? 'Not provided',
                'bio' => $req['bio'] ?? '',
                'applied_date' => date('M d, Y', strtotime($req['applied_at']))
            ];
        }
        
        return $formatted;
    }
}
?>