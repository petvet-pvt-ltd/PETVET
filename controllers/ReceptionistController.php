<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/ClinicManager/AppointmentsModel.php';

class ReceptionistController extends BaseController {
    
    private $appointmentsModel;
    
    public function __construct() {
        parent::__construct();
        $this->appointmentsModel = new AppointmentsModel();
    }
    
    public function payments() {
        try {
            // Get completed appointments pending payment
            $appointments = $this->appointmentsModel->fetchAppointments();
            $pendingPayments = [];
            
            $vets = [
                1 => 'Dr. Robert Fox',
                2 => 'Theresa Webb',
                3 => 'Marvin McKinney',
                4 => 'Dr. Kathryn Murphy'
            ];
            
            // Filter completed appointments (mock data - in production filter by payment_status)
            foreach ($appointments as $date => $dayAppointments) {
                foreach ($dayAppointments as $appt) {
                    if (isset($appt['status']) && $appt['status'] === 'Completed') {
                        $pendingPayments[] = [
                            'id' => uniqid('appt_'),
                            'client' => $appt['client'],
                            'pet' => $appt['pet'],
                            'animal' => $appt['animal'],
                            'vet' => $vets[$appt['vet_id']] ?? 'Unknown',
                            'type' => $appt['type'],
                            'date' => $date,
                            'time' => $appt['time'],
                            'status' => 'Pending Payment'
                        ];
                    }
                }
            }
            
            $this->view('receptionist', 'payments', [
                'pendingPayments' => $pendingPayments
            ]);
            
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    
    public function paymentRecords() {
        try {
            // Mock payment records (in production, fetch from database)
            $paymentRecords = [
                [
                    'invoice_number' => 'INV-001234',
                    'date' => date('Y-m-d', strtotime('-2 days')),
                    'client' => 'John Silva',
                    'pet' => 'Max',
                    'vet' => 'Dr. Robert Fox',
                    'amount' => 3500.00,
                    'status' => 'Paid'
                ],
                [
                    'invoice_number' => 'INV-001235',
                    'date' => date('Y-m-d', strtotime('-1 day')),
                    'client' => 'Sarah Fernando',
                    'pet' => 'Bella',
                    'vet' => 'Marvin McKinney',
                    'amount' => 5200.00,
                    'status' => 'Paid'
                ],
                [
                    'invoice_number' => 'INV-001236',
                    'date' => date('Y-m-d'),
                    'client' => 'David Perera',
                    'pet' => 'Rocky',
                    'vet' => 'Dr. Robert Fox',
                    'amount' => 2800.00,
                    'status' => 'Paid'
                ],
            ];
            
            $this->view('receptionist', 'payment-records', [
                'paymentRecords' => $paymentRecords
            ]);
            
        } catch (Exception $e) {
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
            // Get all appointments
            $appointments = $this->appointmentsModel->fetchAppointments();
            $today = date('Y-m-d');
            $todayAppointments = $appointments[$today] ?? [];
            
            // Count pending appointments (appointments with status != Completed)
            $pendingCount = 0;
            foreach ($appointments as $date => $dayAppointments) {
                foreach ($dayAppointments as $appt) {
                    if (isset($appt['status']) && $appt['status'] !== 'Completed') {
                        $pendingCount++;
                    }
                }
            }
            
            // Get ongoing appointments (appointments happening right now)
            $vets = [
                1 => 'Dr. Robert Fox',
                2 => 'Theresa Webb',
                3 => 'Marvin McKinney',
                4 => 'Dr. Kathryn Murphy'
            ];
            
            $currentTime = time();
            $currentHour = (int)date('H', $currentTime);
            $currentMinute = (int)date('i', $currentTime);
            
            $ongoingAppointments = [];
            
            // Check each vet for current appointments
            foreach ($vets as $vetId => $vetName) {
                $foundAppointment = false;
                
                foreach ($todayAppointments as $appt) {
                    if ($appt['vet_id'] == $vetId && $appt['status'] === 'Confirmed') {
                        // Parse appointment time
                        list($apptHour, $apptMinute) = explode(':', $appt['time']);
                        $apptHour = (int)$apptHour;
                        $apptMinute = (int)$apptMinute;
                        
                        // Calculate if appointment is ongoing (within 20 minutes window)
                        $apptStart = $apptHour * 60 + $apptMinute;
                        $apptEnd = $apptStart + 20; // 20 minute slots
                        $currentTimeMinutes = $currentHour * 60 + $currentMinute;
                        
                        if ($currentTimeMinutes >= $apptStart && $currentTimeMinutes <= $apptEnd) {
                            $endHour = floor($apptEnd / 60);
                            $endMinute = $apptEnd % 60;
                            
                            $ongoingAppointments[] = [
                                'hasAppointment' => true,
                                'vet' => $vetName,
                                'animal' => $appt['animal'],
                                'client' => $appt['client'],
                                'type' => $appt['type'],
                                'time_range' => sprintf('%02d:%02d - %02d:%02d', $apptHour, $apptMinute, $endHour, $endMinute),
                                'pet' => $appt['pet']
                            ];
                            $foundAppointment = true;
                            break;
                        }
                    }
                }
                
                // If no ongoing appointment found, add "No current appointment"
                if (!$foundAppointment) {
                    $ongoingAppointments[] = [
                        'hasAppointment' => false,
                        'vet' => $vetName,
                        'animal' => '',
                        'client' => '',
                        'type' => '',
                        'time_range' => '',
                        'pet' => ''
                    ];
                }
            }
            
            // Count ongoing appointments
            $ongoingCount = 0;
            foreach ($ongoingAppointments as $appt) {
                if ($appt['hasAppointment']) {
                    $ongoingCount++;
                }
            }
            
            // Get upcoming appointments for TODAY only (after current time)
            $upcomingAppointments = [];
            $currentTimeTimestamp = $currentTime;
            
            // Only check today's appointments
            foreach ($todayAppointments as $appt) {
                $apptDateTime = strtotime($today . ' ' . $appt['time']);
                
                // Only include future appointments with Confirmed status
                if ($apptDateTime > $currentTimeTimestamp && $appt['status'] === 'Confirmed') {
                    $upcomingAppointments[] = [
                        'date' => $today,
                        'time' => $appt['time'],
                        'pet' => $appt['pet'],
                        'client' => $appt['client'],
                        'vet' => $appt['vet'],
                        'type' => $appt['type'],
                        'timestamp' => $apptDateTime
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
                'upcomingAppointments' => $upcomingAppointments
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