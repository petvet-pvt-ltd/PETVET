<?php
require_once __DIR__ . '/../BaseModel.php';

class OverviewModel extends BaseModel {
    
    /**
     * Get clinic_id for the logged-in clinic manager
     */
    private function getClinicId() {
        $userId = $_SESSION['user_id'] ?? 0;
        if (!$userId) return 0;

        $stmt = $this->db->prepare("SELECT clinic_id FROM clinic_manager_profiles WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn() ?: 0;
    }
    
    public function fetchOverviewData(): array {
        // Load configuration
        $cfgPath = __DIR__ . '/../../config/clinic_manager.php';
        $cfg = file_exists($cfgPath) ? require $cfgPath : [
            'timezone' => 'Asia/Colombo',
            'slot_duration_minutes' => 60
        ];
        date_default_timezone_set($cfg['timezone'] ?? 'Asia/Colombo');
        
        $clinicId = $this->getClinicId();
        if (!$clinicId) {
            return [
                'kpis' => [],
                'appointments' => [],
                'ongoingAppointments' => [],
                'pendingVetRequests' => [],
                'badgeClasses' => []
            ];
        }
        
        $today = date('Y-m-d');
        $nowTs = time();
        $slotMinutes = (int)($cfg['slot_duration_minutes'] ?? 60);
        
        // Optional debug override: pass ?debugNow=HH:MM or YYYY-MM-DD HH:MM to preview ongoing logic
        if (!empty($_GET['debugNow'])) {
            $debug = trim($_GET['debugNow']);
            if (preg_match('/^\d{2}:\d{2}$/', $debug)) {
                $ts = strtotime($today . ' ' . $debug);
                if ($ts !== false) { $nowTs = $ts; }
            } elseif (preg_match('/^\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}$/', $debug)) {
                $ts = strtotime($debug);
                if ($ts !== false) { $nowTs = $ts; }
            }
        }
        
        // Get today's appointments count
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM appointments
            WHERE clinic_id = ? AND appointment_date = ?
        ");
        $stmt->execute([$clinicId, $today]);
        $todayAppointmentsCount = $stmt->fetch()['count'];
        
        // Get active vets count
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM vets v
            JOIN users u ON v.user_id = u.id
            WHERE v.clinic_id = ? AND v.available = 1 AND u.is_active = 1 AND u.is_blocked = 0
        ");
        $stmt->execute([$clinicId]);
        $activeVetsCount = $stmt->fetch()['count'];
        
        // Get pending shop orders count (if shop exists for this clinic)
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM orders
            WHERE clinic_id = ? AND status = 'pending'
        ");
        $stmt->execute([$clinicId]);
        $pendingOrdersCount = $stmt->fetch()['count'];
        
        // Get pending vet requests (vets with is_active = 0 or email_verified = 0)
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM vets v
            JOIN users u ON v.user_id = u.id
            WHERE v.clinic_id = ? AND (u.is_active = 0 OR u.email_verified = 0)
        ");
        $stmt->execute([$clinicId]);
        $pendingVetRequestsCount = $stmt->fetch()['count'];
        
        // Build KPIs
        $kpis = [
            ['label'=>'Appointments Today','value'=>$todayAppointmentsCount],
            ['label'=>'Active Vets','value'=>$activeVetsCount],
            ['label'=>'Pending Shop Orders','value'=>$pendingOrdersCount],
            ['label'=>'Pending Vet Requests','value'=>$pendingVetRequestsCount],
        ];
        
        // Get today's appointments with details
        $stmt = $this->db->prepare("
            SELECT 
                a.id,
                a.appointment_time,
                a.appointment_type,
                a.status,
                a.duration_minutes,
                p.name as pet_name,
                p.species,
                CONCAT(u_owner.first_name, ' ', u_owner.last_name) as owner_name,
                CONCAT(u_vet.first_name, ' ', u_vet.last_name) as vet_name,
                a.vet_id
            FROM appointments a
            JOIN pets p ON a.pet_id = p.id
            JOIN users u_owner ON a.pet_owner_id = u_owner.id
            LEFT JOIN users u_vet ON a.vet_id = u_vet.id
            WHERE a.clinic_id = ? AND a.appointment_date = ?
            ORDER BY a.appointment_time ASC
        ");
        $stmt->execute([$clinicId, $today]);
        $appointments = $stmt->fetchAll();
        
        // Get ongoing appointments (appointments where current time is within the appointment window)
        $stmt = $this->db->prepare("
            SELECT 
                a.id,
                a.appointment_time,
                a.appointment_type,
                a.duration_minutes,
                p.name as pet_name,
                p.species,
                CONCAT(u_owner.first_name, ' ', u_owner.last_name) as owner_name,
                CONCAT(u_vet.first_name, ' ', u_vet.last_name) as vet_name,
                a.vet_id
            FROM appointments a
            JOIN pets p ON a.pet_id = p.id
            JOIN users u_owner ON a.pet_owner_id = u_owner.id
            JOIN users u_vet ON a.vet_id = u_vet.id
            WHERE a.clinic_id = ? 
            AND a.appointment_date = ?
            AND a.status IN ('approved', 'ongoing')
            ORDER BY a.appointment_time ASC
        ");
        $stmt->execute([$clinicId, $today]);
        $appointmentsData = $stmt->fetchAll();
        
        // Build ongoing appointments list
        $ongoing = [];
        $currentTime = date('H:i:s', $nowTs);
        
        foreach ($appointmentsData as $appt) {
            $startTime = $appt['appointment_time'];
            $duration = $appt['duration_minutes'] ?? $slotMinutes;
            $endTime = date('H:i:s', strtotime($startTime) + ($duration * 60));
            
            // Check if current time is within appointment window
            $isOngoing = ($currentTime >= $startTime && $currentTime < $endTime);
            
            if ($isOngoing) {
                $ongoing[] = [
                    'vet' => $appt['vet_name'] ?? 'Unknown Vet',
                    'hasAppointment' => true,
                    'animal' => $appt['species'] ?? 'Pet',
                    'client' => $appt['owner_name'] ?? 'Unknown',
                    'type' => $appt['appointment_type'] ?? 'Checkup',
                    'time_range' => date('H:i', strtotime($startTime)) . ' – ' . date('H:i', strtotime($endTime))
                ];
            }
        }
        
        // If no ongoing appointments, show vets with no current appointments
        if (empty($ongoing)) {
            $stmt = $this->db->prepare("
                SELECT CONCAT(u.first_name, ' ', u.last_name) as vet_name
                FROM vets v
                JOIN users u ON v.user_id = u.id
                WHERE v.clinic_id = ? AND v.available = 1 AND u.is_active = 1 AND u.is_blocked = 0
                LIMIT 3
            ");
            $stmt->execute([$clinicId]);
            $availableVets = $stmt->fetchAll();
            
            foreach ($availableVets as $vet) {
                $ongoing[] = [
                    'vet' => $vet['vet_name'],
                    'hasAppointment' => false
                ];
            }
        }
        
        // Get staff on duty for today from staff_duty_schedule table
        $stmt = $this->db->prepare("
            SELECT 
                sds.staff_id,
                sds.shift_time,
                COALESCE(CONCAT(u.first_name, ' ', u.last_name), cs.name) as name,
                cs.role
            FROM staff_duty_schedule sds
            JOIN clinic_staff cs ON sds.staff_id = cs.id
            LEFT JOIN users u ON cs.user_id = u.id
            WHERE sds.clinic_id = ? 
            AND sds.duty_date = ?
            AND cs.status = 'Active'
            ORDER BY cs.role, name
        ");
        $stmt->execute([$clinicId, $today]);
        $staffMembers = $stmt->fetchAll();
        
        // Group staff by role
        $staff = [];
        foreach ($staffMembers as $member) {
            $roleKey = $member['role'];
            if (!isset($staff[$roleKey])) {
                $staff[$roleKey] = [];
            }
            $staff[$roleKey][] = [
                'name' => $member['name'],
                'time' => $member['shift_time'],
                'status' => 'online'
            ];
        }
        
        $badgeClasses = [
            'pending' => 'badge-pending',
            'approved' => 'badge-confirmed',
            'completed' => 'badge-completed',
            'cancelled' => 'badge-cancelled',
            'ongoing' => 'badge-ongoing',
        ];
        
        return [
            'kpis' => $kpis,
            'appointments' => $appointments,
            'ongoingAppointments' => $ongoing,
            'staff' => $staff,
            'badgeClasses' => $badgeClasses,
        ];
    }
}
?>