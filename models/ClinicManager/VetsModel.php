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
                CASE
                    WHEN v.is_suspended = 1 THEN 'Suspended'
                    WHEN v.is_on_leave = 1 THEN 'On Leave'
                    WHEN v.available = 1 THEN 'Active'
                    ELSE 'Inactive'
                END as status,
                u.avatar,
                v.specialization,
                v.license_number,
                v.years_experience,
                v.consultation_fee,
                v.rating,
                v.bio,
                v.available,
                v.is_suspended,
                v.is_on_leave,
                (SELECT MIN(a.appointment_date) 
                 FROM appointments a 
                 WHERE a.vet_id = v.user_id 
                 AND a.appointment_date >= CURDATE() 
                 AND a.status NOT IN ('cancelled', 'completed', 'paid')
                ) as next_appointment_date,
                (SELECT MIN(a.appointment_time) 
                 FROM appointments a 
                 WHERE a.vet_id = v.user_id 
                 AND a.appointment_date >= CURDATE() 
                 AND a.status NOT IN ('cancelled', 'completed', 'paid')
                ) as next_appointment_time
                        FROM vets v
                        JOIN users u ON v.user_id = u.id
                        JOIN user_roles ur ON ur.user_id = u.id AND ur.is_active = 1
                        JOIN roles r ON r.id = ur.role_id AND r.role_name = 'vet'
                        WHERE v.clinic_id = :clinic_id
                            AND ur.verification_status = 'approved'
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
        $sql = "
            SELECT 
                ur.id as user_role_id,
                u.id as user_id,
                CONCAT(u.first_name, ' ', u.last_name) as name,
                u.email,
                u.phone,
                u.avatar,
                ur.applied_at,
                v.specialization,
                v.license_number,
                v.years_experience
            FROM user_roles ur
            JOIN roles r ON ur.role_id = r.id
            JOIN users u ON ur.user_id = u.id
            JOIN vets v ON v.user_id = u.id
            WHERE r.role_name = 'vet'
              AND ur.is_active = 1
              AND ur.verification_status = 'pending'
              AND v.clinic_id = :clinic_id
            ORDER BY ur.applied_at DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['clinic_id' => $clinicId]);
        $requests = $stmt->fetchAll();
        
        // Format for view
        $formatted = [];
        $docStmt = $this->db->prepare("
            SELECT id, document_type, document_name, file_path
            FROM role_verification_documents
            WHERE user_role_id = ?
            ORDER BY uploaded_at DESC
        ");

        foreach ($requests as $req) {
            $docStmt->execute([$req['user_role_id']]);
            $docsRows = $docStmt->fetchAll();
            $docs = [];
            foreach ($docsRows as $d) {
                $label = $d['document_type'] === 'other'
                    ? 'Proof / CV (PDF)'
                    : (ucfirst($d['document_type']) . ' (PDF)');
                $docs[] = [
                    'label' => $label,
                    'url' => '/PETVET/api/download-file.php?doc_id=' . (int)$d['id']
                ];
            }

            $formatted[] = [
                'id' => $req['user_role_id'],
                'user_id' => $req['user_id'],
                'name' => $req['name'],
                'email' => $req['email'],
                'phone' => $req['phone'] ?? 'N/A',
                'photo' => !empty($req['avatar']) ? $req['avatar'] : '/PETVET/public/images/emptyProfPic.png',
                'specialization' => $req['specialization'] ?? 'General',
                'license' => $req['license_number'] ?? 'Pending',
                'experience' => ((int)($req['years_experience'] ?? 0)) . ' years experience',
                'education' => 'Not provided',
                'bio' => '',
                'docs' => $docs,
                'applied_date' => !empty($req['applied_at']) ? date('M d, Y', strtotime($req['applied_at'])) : ''
            ];
        }
        
        return $formatted;
    }
}
?>
