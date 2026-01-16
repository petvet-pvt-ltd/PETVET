<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/ClinicManager/AppointmentsModel.php';
require_once __DIR__ . '/../config/connect.php';

class ReceptionistController extends BaseController {
    
    private $appointmentsModel;
    
    public function __construct() {
        parent::__construct();
        $this->appointmentsModel = new AppointmentsModel();
    }
    
    public function payments() {
        try {
            if (empty($_SESSION['clinic_id'])) {
                echo "Clinic not set in session";
                return;
            }

            $clinicId = (int)$_SESSION['clinic_id'];
            
            // Get completed appointments pending payment from database
            $pdo = db();
            $stmt = $pdo->prepare("
                SELECT 
                    a.id,
                    a.appointment_date,
                    a.appointment_time,
                    a.appointment_type,
                    u.first_name AS owner_first_name,
                    u.last_name AS owner_last_name,
                    p.name AS pet_name,
                    p.species AS animal_type,
                    v.first_name AS vet_first_name,
                    v.last_name AS vet_last_name
                FROM appointments a
                JOIN users u ON a.pet_owner_id = u.id
                JOIN pets p ON a.pet_id = p.id
                JOIN users v ON a.vet_id = v.id
                WHERE a.clinic_id = :clinic_id
                  AND a.status = 'completed'
                ORDER BY a.appointment_date DESC, a.appointment_time DESC
            ");
            $stmt->execute(['clinic_id' => $clinicId]);
            $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $pendingPayments = [];
            foreach ($appointments as $appt) {
                $pendingPayments[] = [
                    'id' => $appt['id'],  // Real appointment ID from database
                    'client' => trim($appt['owner_first_name'] . ' ' . $appt['owner_last_name']),
                    'pet' => $appt['pet_name'],
                    'animal' => ucfirst($appt['animal_type']),
                    'vet' => 'Dr. ' . trim($appt['vet_first_name'] . ' ' . $appt['vet_last_name']),
                    'type' => ucfirst($appt['appointment_type']),
                    'date' => $appt['appointment_date'],
                    'time' => date('h:i A', strtotime($appt['appointment_time'])),
                    'status' => 'Pending Payment'
                ];
            }
            
            $this->view('receptionist', 'payments', [
                'pendingPayments' => $pendingPayments
            ]);
            
        } catch (Exception $e) {
            error_log("ReceptionistController::payments error: " . $e->getMessage());
            echo "Error: " . $e->getMessage();
        }
    }
    
    public function paymentRecords() {
        try {
            if (empty($_SESSION['clinic_id'])) {
                echo "Clinic not set in session";
                return;
            }

            $clinicId = (int)$_SESSION['clinic_id'];
            
            // Get paid appointments from database
            $pdo = db();
            
            // Check if payments table exists
            $stmt = $pdo->query("SHOW TABLES LIKE 'payments'");
            $paymentsTableExists = $stmt->rowCount() > 0;
            
            $paymentRecords = [];
            
            if ($paymentsTableExists) {
                $stmt = $pdo->prepare("
                    SELECT 
                        p.invoice_number,
                        p.payment_date as date,
                        p.total_amount as amount,
                        u.first_name AS owner_first_name,
                        u.last_name AS owner_last_name,
                        pet.name AS pet_name,
                        v.first_name AS vet_first_name,
                        v.last_name AS vet_last_name,
                        a.appointment_type
                    FROM appointments a
                    JOIN payments p ON p.appointment_id = a.id
                    JOIN users u ON a.pet_owner_id = u.id
                    JOIN pets pet ON a.pet_id = pet.id
                    JOIN users v ON a.vet_id = v.id
                    WHERE a.clinic_id = :clinic_id
                      AND a.status = 'paid'
                    ORDER BY p.payment_date DESC, p.created_at DESC
                ");
                $stmt->execute(['clinic_id' => $clinicId]);
                $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($records as $rec) {
                    $paymentRecords[] = [
                        'invoice_number' => $rec['invoice_number'],
                        'date' => $rec['date'],
                        'client' => trim($rec['owner_first_name'] . ' ' . $rec['owner_last_name']),
                        'pet' => $rec['pet_name'],
                        'vet' => 'Dr. ' . trim($rec['vet_first_name'] . ' ' . $rec['vet_last_name']),
                        'amount' => (float)$rec['amount'],
                        'status' => 'Paid'
                    ];
                }
            }
            
            $this->view('receptionist', 'payment-records', [
                'paymentRecords' => $paymentRecords
            ]);
            
        } catch (Exception $e) {
            error_log("ReceptionistController::paymentRecords error: " . $e->getMessage());
            echo "Error: " . $e->getMessage();
        }
    }
    
    public function appointments() {
        try {
            // Get appointment data (same as clinic manager)
            $appointments = $this->appointmentsModel->fetchAppointments();
            
            // Get view type (week or month)
            $view = $_GET['view'] ?? 'week';
            if (!in_array($view, ['week', 'month'])) {
                $view = 'week';
            }
            
            // Get vet names for dropdowns (same data as clinic manager)
            $vetNames = [
                1 => 'Dr. Sarah Johnson',
                2 => 'Dr. Michael Chen', 
                3 => 'Dr. Emily Davis',
                4 => 'Dr. Robert Wilson'
            ];
            
            // Pass data to view
            $this->view('receptionist', 'appointments', [
                'appointments' => $appointments,
                'view' => $view,
                'vetNames' => $vetNames
            ]);
            
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    
    public function dashboard() {
        try {
            require_once __DIR__ . '/../models/SharedAppointmentsModel.php';
            $sharedModel = new SharedAppointmentsModel();
            
            $today = date('Y-m-d');
            
            // Get pending appointments count
            $pendingAppointments = $sharedModel->getPendingAppointments();
            $pendingCount = count($pendingAppointments);
            
            // Get user info
            $userId = currentUserId();
            $userName = '';
            $clinicName = '';
            
            if ($userId) {
                $pdo = db();
                
                // Get user name
                $userStmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
                $userStmt->execute([$userId]);
                $user = $userStmt->fetch(PDO::FETCH_ASSOC);
                if ($user) {
                    $userName = $user['first_name'] . ' ' . $user['last_name'];
                }
                
                // Get clinic name
                $clinicStmt = $pdo->prepare("
                    SELECT c.clinic_name 
                    FROM clinic_staff cs
                    JOIN clinics c ON cs.clinic_id = c.id
                    WHERE cs.user_id = ?
                ");
                $clinicStmt->execute([$userId]);
                $clinic = $clinicStmt->fetch(PDO::FETCH_ASSOC);
                if ($clinic) {
                    $clinicName = $clinic['clinic_name'];
                }
            }
            
            // Get ongoing appointments (appointments happening right now)
            $currentTime = time();
            $currentHour = (int)date('H', $currentTime);
            $currentMinute = (int)date('i', $currentTime);
            $currentTimeMinutes = $currentHour * 60 + $currentMinute;
            
            $ongoingAppointments = [];
            
            // Get receptionist's clinic ID
            $clinicFilter = "";
            $params = [$today];
            
            if ($userId) {
                $pdo = db();
                $checkClinic = $pdo->prepare("SELECT clinic_id FROM clinic_staff WHERE user_id = ?");
                $checkClinic->execute([$userId]);
                $clinicId = $checkClinic->fetchColumn();
                
                if ($clinicId) {
                    $clinicFilter = " AND a.clinic_id = ?";
                    $params[] = $clinicId;
                }
            }
            
            // Get today's approved appointments
            $query = "
                SELECT 
                    a.id,
                    a.appointment_time,
                    a.duration_minutes,
                    a.appointment_type,
                    p.name as pet,
                    p.species as animal,
                    CONCAT(u.first_name, ' ', u.last_name) as client,
                    COALESCE(CONCAT(v.first_name, ' ', v.last_name), 'Any Available Vet') as vet
                FROM appointments a
                JOIN pets p ON a.pet_id = p.id
                JOIN users u ON a.pet_owner_id = u.id
                LEFT JOIN users v ON a.vet_id = v.id
                WHERE a.appointment_date = ? 
                AND a.status = 'approved' $clinicFilter
                ORDER BY a.appointment_time
            ";
            
            $pdo = db();
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $todayAppointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Find ongoing appointments
            foreach ($todayAppointments as $appt) {
                $apptTime = strtotime($appt['appointment_time']);
                $apptHour = (int)date('H', $apptTime);
                $apptMinute = (int)date('i', $apptTime);
                $duration = $appt['duration_minutes'] ?? 20;
                
                $apptStart = $apptHour * 60 + $apptMinute;
                $apptEnd = $apptStart + $duration;
                
                if ($currentTimeMinutes >= $apptStart && $currentTimeMinutes <= $apptEnd) {
                    $endHour = floor($apptEnd / 60);
                    $endMinute = $apptEnd % 60;
                    
                    $ongoingAppointments[] = [
                        'hasAppointment' => true,
                        'vet' => $appt['vet'],
                        'animal' => $appt['animal'],
                        'client' => $appt['client'],
                        'type' => $appt['appointment_type'],
                        'time_range' => sprintf('%02d:%02d - %02d:%02d', $apptHour, $apptMinute, $endHour, $endMinute),
                        'pet' => $appt['pet']
                    ];
                }
            }
            
            $ongoingCount = count($ongoingAppointments);
            
            // Get upcoming appointments for TODAY only (after current time)
            $upcomingAppointments = [];
            
            foreach ($todayAppointments as $appt) {
                $apptTime = strtotime($today . ' ' . $appt['appointment_time']);
                
                // Only include future appointments
                if ($apptTime > $currentTime) {
                    $upcomingAppointments[] = [
                        'date' => $today,
                        'time' => $appt['appointment_time'],
                        'pet' => $appt['pet'],
                        'client' => $appt['client'],
                        'vet' => $appt['vet'],
                        'type' => $appt['appointment_type'],
                        'timestamp' => $apptTime
                    ];
                }
            }
            
            // Sort by timestamp
            usort($upcomingAppointments, function($a, $b) {
                return $a['timestamp'] - $b['timestamp'];
            });
            
            $this->view('receptionist', 'dashboard', [
                'pendingCount' => $pendingCount,
                'ongoingCount' => $ongoingCount,
                'ongoingAppointments' => $ongoingAppointments,
                'upcomingAppointments' => $upcomingAppointments,
                'userName' => $userName,
                'clinicName' => $clinicName
            ]);
            
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    
    // Handle appointment actions (add, edit, delete)
    public function handleAppointmentAction() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }
        
        try {
            $action = $_POST['action'] ?? '';
            
            switch ($action) {
                case 'add':
                    $this->addAppointment();
                    break;
                case 'edit':
                    $this->editAppointment();
                    break;
                case 'cancel':
                    $this->cancelAppointment();
                    break;
                default:
                    throw new Exception('Invalid action');
            }
            
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    
    private function addAppointment() {
        // Validate required fields
        $required = ['clientName', 'petName', 'appointmentDate', 'appointmentTime', 'vetSelect', 'appointmentReason'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Field '$field' is required");
            }
        }
        
        // Here you would typically save to database
        // For now, just return success
        echo json_encode([
            'success' => true,
            'message' => 'Appointment scheduled successfully'
        ]);
    }
    
    private function editAppointment() {
        // Similar validation and processing as addAppointment
        $required = ['editClientName', 'editPetName', 'editAppointmentDate', 'editAppointmentTime', 'editVetSelect', 'editAppointmentReason'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Field '$field' is required");
            }
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Appointment updated successfully'
        ]);
    }
    
    private function cancelAppointment() {
        // Here you would typically update database to mark as cancelled
        echo json_encode([
            'success' => true,
            'message' => 'Appointment cancelled successfully'
        ]);
    }
    
    public function settings() {
        require_once __DIR__ . '/../models/PetOwner/SettingsModel.php';
        $settingsModel = new SettingsModel();
        
        // Get current user ID from session
        $currentUserId = $_SESSION['user_id'] ?? null;
        
        if (!$currentUserId) {
            header('Location: /PETVET/index.php?module=guest&page=login');
            exit;
        }
        
        $data = [
            'profile' => $settingsModel->getUserProfile($currentUserId),
            'prefs' => $settingsModel->getUserPreferences($currentUserId),
            'accountStats' => $settingsModel->getAccountStats($currentUserId)
        ];
        
        $this->view('receptionist', 'settings', $data);
    }
}
?>