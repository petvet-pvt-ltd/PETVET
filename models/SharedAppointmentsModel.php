<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * Shared Appointments Model
 * Used by both Clinic Manager and Receptionist
 * Handles all appointment data operations following MVC architecture
 */
class SharedAppointmentsModel extends BaseModel {
    
    /**
     * Get all appointments with optional filtering
     * @param string $vetFilter Filter by specific vet ('all' for no filter)
     * @return array Appointments data organized by date
     */
    public function getAppointments($vetFilter = 'all') {
        try {
            // Get clinic_id if user is receptionist
            $clinicFilter = "";
            $params = [];
            
            if (isset($_SESSION['user_id'])) {
                $checkClinic = $this->db->prepare("SELECT clinic_id FROM clinic_staff WHERE user_id = ?");
                $checkClinic->execute([$_SESSION['user_id']]);
                $clinicId = $checkClinic->fetchColumn();
                
                if ($clinicId) {
                    $clinicFilter = " AND a.clinic_id = ?";
                    $params[] = $clinicId;
                }
            }
            
            $query = "
                SELECT 
                    a.id,
                    a.appointment_date,
                    a.appointment_time as time,
                    a.appointment_type as type,
                    a.status,
                    p.name as pet,
                    p.species as animal,
                    CONCAT(u.first_name, ' ', u.last_name) as client,
                    u.phone as client_phone,
                    CONCAT(v.first_name, ' ', v.last_name) as vet,
                    a.vet_id
                FROM appointments a
                JOIN pets p ON a.pet_id = p.id
                JOIN users u ON a.pet_owner_id = u.id
                LEFT JOIN users v ON a.vet_id = v.id
                WHERE a.status IN ('approved', 'completed') $clinicFilter
                ORDER BY a.appointment_date, a.appointment_time
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Organize by date
            $appointments = [];
            
            foreach ($results as $row) {
                $date = $row['appointment_date'];
                if (!isset($appointments[$date])) {
                    $appointments[$date] = [];
                }
                
                // Format vet name or use "Any Available Vet"
                $vetName = $row['vet'] ?: 'Any Available Vet';
                
                $appointments[$date][] = [
                    'id' => $row['id'],
                    'pet' => $row['pet'],
                    'animal' => $row['animal'],
                    'client' => $row['client'],
                    'client_phone' => $row['client_phone'],
                    'vet' => $vetName,
                    'vet_id' => $row['vet_id'],
                    'time' => $row['time'],
                    'type' => $row['type'],
                    'status' => $row['status']
                ];
            }
            
            // Filter by vet if specified
            if ($vetFilter !== 'all') {
                $filteredAppointments = [];
                foreach ($appointments as $date => $appts) {
                    $filteredDay = [];
                    foreach ($appts as $appt) {
                        if ($appt['vet'] === $vetFilter) {
                            $filteredDay[] = $appt;
                        }
                    }
                    if (!empty($filteredDay)) {
                        $filteredAppointments[$date] = $filteredDay;
                    }
                }
                return $filteredAppointments;
            }
            
            return $appointments;
            
        } catch (Exception $e) {
            error_log("Error fetching appointments: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get list of veterinarians for the receptionist's clinic
     * @return array List of vet names
     */
    public function getVetNames() {
        try {
            // Get clinic_id for the current receptionist
            $clinicFilter = "";
            $params = [];
            
            if (isset($_SESSION['user_id'])) {
                $checkClinic = $this->db->prepare("SELECT clinic_id FROM clinic_staff WHERE user_id = ?");
                $checkClinic->execute([$_SESSION['user_id']]);
                $clinicId = $checkClinic->fetchColumn();
                
                if ($clinicId) {
                    $clinicFilter = " AND cs.clinic_id = ?";
                    $params[] = $clinicId;
                }
            }
            
            $query = "
                SELECT DISTINCT u.id, CONCAT(u.first_name, ' ', u.last_name) as vet_name
                FROM clinic_staff cs
                JOIN users u ON cs.user_id = u.id
                WHERE cs.role = 'vet' 
                AND cs.status = 'Active'
                AND u.is_active = 1
                $clinicFilter
                ORDER BY vet_name
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $results;
            
        } catch (Exception $e) {
            error_log("Error fetching vet names: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Generate week dates starting from today
     * @param int $numDays Number of days to generate (default 7)
     * @return array Array of DateTime objects
     */
    public function getWeekDates($numDays = 7) {
        $weekDays = [];
        $today = new DateTime();
        
        for ($i = 0; $i < $numDays; $i++) {
            $date = clone $today;
            $date->modify("+{$i} day");
            $weekDays[] = $date;
        }
        
        return $weekDays;
    }
    
    /**
     * Generate month dates (5 weeks worth)
     * @param int $numWeeks Number of weeks to generate (default 5)
     * @return array Array of weeks, each containing DateTime objects
     */
    public function getMonthDates($numWeeks = 5) {
        $monthDays = [];
        $start = new DateTime();
        
        for ($row = 0; $row < $numWeeks; $row++) {
            $week = [];
            for ($col = 0; $col < 7; $col++) {
                $date = clone $start;
                $date->modify("+" . ($row * 7 + $col) . " day");
                $week[] = $date;
            }
            $monthDays[] = $week;
        }
        
        return $monthDays;
    }
    
    /**
     * Get module name based on user role for routing
     * @param string $userRole The user's role
     * @return string Module name for URLs
     */
    public function getModuleName($userRole) {
        return $userRole === 'receptionist' ? 'receptionist' : 'clinic-manager';
    }
    
    /**
     * Add a new appointment
     * @param array $appointmentData Appointment details
     * @return bool Success status
     */
    public function addAppointment($appointmentData) {
        // TODO: Implement database insertion
        // This would insert into appointments table
        return true;
    }
    
    /**
     * Update an existing appointment
     * @param int $appointmentId Appointment ID
     * @param array $appointmentData Updated appointment details
     * @return bool Success status
     */
    public function updateAppointment($appointmentId, $appointmentData) {
        // TODO: Implement database update
        // This would update the appointments table
        return true;
    }
    
    /**
     * Cancel/delete an appointment
     * @param int $appointmentId Appointment ID
     * @return bool Success status
     */
    public function cancelAppointment($appointmentId) {
        // TODO: Implement database deletion
        // This would soft delete or remove the appointment
        return true;
    }
    
    /**
     * Get pending appointments awaiting approval
     * @return array Pending appointments list
     */
    public function getPendingAppointments() {
        try {
            // Get clinic_id if user is receptionist
            $clinicFilter = "";
            $params = [];
            
            if (isset($_SESSION['user_id'])) {
                $checkClinic = $this->db->prepare("SELECT clinic_id FROM clinic_staff WHERE user_id = ?");
                $checkClinic->execute([$_SESSION['user_id']]);
                $clinicId = $checkClinic->fetchColumn();
                
                if ($clinicId) {
                    $clinicFilter = " AND a.clinic_id = ?";
                    $params[] = $clinicId;
                }
            }
            
            $query = "
                SELECT 
                    a.id,
                    a.clinic_id,
                    a.appointment_date as requested_date,
                    a.appointment_time as requested_time,
                    a.appointment_type,
                    a.symptoms,
                    a.created_at as requested_at,
                    a.status,
                    p.name as pet,
                    p.species as pet_type,
                    CONCAT(owner.first_name, ' ', owner.last_name) as owner,
                    owner.phone,
                    c.clinic_name as clinic,
                    COALESCE(CONCAT(vet.first_name, ' ', vet.last_name), 'Any Available Vet') as requested_vet
                FROM appointments a
                JOIN pets p ON a.pet_id = p.id
                JOIN users owner ON a.pet_owner_id = owner.id
                JOIN clinics c ON a.clinic_id = c.id
                LEFT JOIN users vet ON a.vet_id = vet.id
                WHERE a.status = 'pending' $clinicFilter
                ORDER BY a.created_at ASC
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error fetching pending appointments: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Approve a pending appointment
     * @param int $appointmentId Appointment ID
     * @return bool Success status
     */
    public function approveAppointment($appointmentId, $vetName = null, $vetIdParam = null) {
        try {
            $userId = $_SESSION['user_id'] ?? null;
            
            // Use provided vet_id if available, otherwise look up by name
            $vetId = $vetIdParam;
            
            if ($vetId === null && $vetName && $vetName !== 'Any Available Vet') {
                $vetStmt = $this->db->prepare("
                    SELECT u.id 
                    FROM users u
                    JOIN user_roles ur ON u.id = ur.user_id
                    WHERE CONCAT(u.first_name, ' ', u.last_name) = ? 
                    AND ur.role_id = 2
                    LIMIT 1
                ");
                $vetStmt->execute([$vetName]);
                $vet = $vetStmt->fetch(PDO::FETCH_ASSOC);
                if ($vet) {
                    $vetId = $vet['id'];
                }
            }
            
            // Update appointment with optional vet assignment
            if ($vetId !== null) {
                $stmt = $this->db->prepare("
                    UPDATE appointments 
                    SET status = 'approved', 
                        approved_by = ?, 
                        approved_at = NOW(),
                        vet_id = ?
                    WHERE id = ? AND status = 'pending'
                ");
                $stmt->execute([$userId, $vetId, $appointmentId]);
            } else {
                $stmt = $this->db->prepare("
                    UPDATE appointments 
                    SET status = 'approved', 
                        approved_by = ?, 
                        approved_at = NOW() 
                    WHERE id = ? AND status = 'pending'
                ");
                $stmt->execute([$userId, $appointmentId]);
            }
            
            return $stmt->rowCount() > 0;
            
        } catch (Exception $e) {
            error_log("Error approving appointment: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Decline a pending appointment
     * @param int $appointmentId Appointment ID
     * @param string $reason Reason for decline
     * @return bool Success status
     */
    public function declineAppointment($appointmentId, $reason = '') {
        try {
            $stmt = $this->db->prepare("
                UPDATE appointments 
                SET status = 'declined', 
                    decline_reason = ? 
                WHERE id = ? AND status = 'pending'
            ");
            
            $stmt->execute([$reason, $appointmentId]);
            
            return $stmt->rowCount() > 0;
            
        } catch (Exception $e) {
            error_log("Error declining appointment: " . $e->getMessage());
            return false;
        }
    }
}
?>