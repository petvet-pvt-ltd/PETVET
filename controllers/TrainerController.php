<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Trainer/DashboardModel.php';
require_once __DIR__ . '/../models/Trainer/AppointmentsModel.php';
require_once __DIR__ . '/../models/Trainer/ClientsModel.php';
require_once __DIR__ . '/../models/Trainer/SettingsModel.php';

class TrainerController extends BaseController {

    public function dashboard() {
        $model = new TrainerDashboardModel();
        $trainerId = 1; // Mock trainer ID
        
        $data = [
            'stats' => $model->getStats($trainerId),
            'upcomingAppointments' => $model->getUpcomingAppointments($trainerId, 5)
        ];
        
        $this->view('trainer', 'dashboard', $data);
    }

    public function appointments() {
        $model = new TrainerAppointmentsModel();
        $trainerId = 1; // Mock trainer ID - replace with actual session user ID
        
        $data = [
            'pendingRequests' => $model->getPendingRequests($trainerId),
            'confirmedSessions' => $model->getConfirmedSessions($trainerId),
            'completedSessions' => $model->getCompletedSessions($trainerId)
        ];
        
        $this->view('trainer', 'appointments', $data);
    }

    /**
     * Handle AJAX requests for training actions (accept, decline, complete, etc.)
     */
    public function handleTrainingAction() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $action = $_POST['action'] ?? '';
        $trainerId = 1; // Mock trainer ID - replace with actual session user ID
        $model = new TrainerAppointmentsModel();

        switch ($action) {
            case 'accept':
                $requestId = $_POST['request_id'] ?? 0;
                $result = $model->acceptRequest($requestId, $trainerId);
                echo json_encode($result);
                break;

            case 'decline':
                $requestId = $_POST['request_id'] ?? 0;
                $reason = $_POST['reason'] ?? '';
                $result = $model->declineRequest($requestId, $trainerId, $reason);
                echo json_encode($result);
                break;

            case 'complete':
                $sessionId = $_POST['session_id'] ?? 0;
                $notes = $_POST['notes'] ?? '';
                $nextSessionDate = $_POST['next_session_date'] ?? null;
                $nextSessionTime = $_POST['next_session_time'] ?? null;
                $nextSessionGoals = $_POST['next_session_goals'] ?? '';
                $result = $model->completeSession($sessionId, $trainerId, $notes, $nextSessionDate, $nextSessionTime, $nextSessionGoals);
                echo json_encode($result);
                break;

            case 'mark_program_complete':
                $sessionId = $_POST['session_id'] ?? 0;
                $finalNotes = $_POST['final_notes'] ?? '';
                $result = $model->markProgramComplete($sessionId, $trainerId, $finalNotes);
                echo json_encode($result);
                break;

            case 'get_session':
                $sessionId = $_POST['session_id'] ?? 0;
                $session = $model->getSessionById($sessionId);
                if ($session) {
                    echo json_encode(['success' => true, 'session' => $session]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Session not found']);
                }
                break;

            case 'get_session_history':
                $sessionId = $_POST['session_id'] ?? 0;
                $history = $model->getSessionHistory($sessionId);
                echo json_encode(['success' => true, 'history' => $history]);
                break;

            case 'get_request':
                $requestId = $_POST['request_id'] ?? 0;
                $request = $model->getRequestById($requestId);
                if ($request) {
                    echo json_encode(['success' => true, 'request' => $request]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Request not found']);
                }
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
    }

    public function clients() {
        $model = new TrainerClientsModel();
        $trainerId = 1; // Mock trainer ID
        
        $data = [
            'clients' => $model->getAllClients($trainerId),
            'activeClients' => $model->getActiveClients($trainerId),
            'clientStats' => $model->getClientStats($trainerId)
        ];
        
        $this->view('trainer', 'clients', $data);
    }

    public function availability() {
        // Simple view - no model needed for now, just display the form
        $this->view('trainer', 'availability', []);
    }

    public function settings() {
        $model = new TrainerSettingsModel();
        $trainerId = 1; // Mock trainer ID
        
        $data = [
            'profile' => $model->getProfile($trainerId),
            'preferences' => $model->getPreferences($trainerId)
        ];
        
        $this->view('trainer', 'settings', $data);
    }
}
