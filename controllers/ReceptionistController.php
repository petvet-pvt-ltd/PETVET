<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/ClinicManager/AppointmentsModel.php';

class ReceptionistController extends BaseController {
    
    private $appointmentsModel;
    
    public function __construct() {
        parent::__construct();
        $this->appointmentsModel = new AppointmentsModel();
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
            // Get today's appointments
            $appointments = $this->appointmentsModel->fetchAppointments();
            $today = date('Y-m-d');
            $todayAppointments = $appointments[$today] ?? [];
            
            // Get quick stats
            $totalToday = count($todayAppointments);
            $upcomingWeek = 0;
            
            // Count upcoming week appointments
            for ($i = 0; $i < 7; $i++) {
                $date = date('Y-m-d', strtotime("+$i days"));
                $upcomingWeek += count($appointments[$date] ?? []);
            }
            
            $this->view('receptionist', 'dashboard', [
                'todayAppointments' => $todayAppointments,
                'totalToday' => $totalToday,
                'upcomingWeek' => $upcomingWeek
            ]);
            
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    
    public function settings() {
        try {
            $this->view('receptionist', 'settings', []);
            
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
}
?>