<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Trainer/DashboardModel.php';
require_once __DIR__ . '/../models/Trainer/AppointmentsModel.php';
require_once __DIR__ . '/../models/Trainer/ClientsModel.php';
require_once __DIR__ . '/../models/Trainer/SettingsModel.php';
require_once __DIR__ . '/../helpers/NotificationHelper.php';

class TrainerController extends BaseController {

    public function dashboard() {
        $model = new TrainerDashboardModel();
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $trainerId = (int)($_SESSION['user_id'] ?? 0);
        
        $data = [
            'stats' => $model->getStats($trainerId),
            'upcomingAppointments' => $model->getUpcomingAppointments($trainerId, 5)
        ];
        
        $this->view('trainer', 'dashboard', $data);
    }

    public function appointments() {
        $model = new TrainerAppointmentsModel();
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $trainerId = (int)($_SESSION['user_id'] ?? 0);
        
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

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $action = $_POST['action'] ?? '';
        $trainerId = (int)($_SESSION['user_id'] ?? 0);
        $model = new TrainerAppointmentsModel();

        switch ($action) {
            case 'accept':
                $requestId = (int)($_POST['request_id'] ?? 0);
                $result = $model->acceptRequest($requestId, $trainerId);
                if (!empty($result['success'])) {
                    $request = $model->getRequestById($requestId);
                    if ($request) {
                        $petOwnerId = (int)($request['pet_owner_id'] ?? 0);
                        $trainerName = (string)($request['trainer_name'] ?? '');
                        $petName = (string)($request['pet_name'] ?? '');
                        $sessionDate = !empty($request['preferred_date']) ? date('F j, Y', strtotime($request['preferred_date'])) : null;
                        $sessionTime = !empty($request['preferred_time']) ? date('g:i A', strtotime($request['preferred_time'])) : null;
                        $locationType = (string)($request['location_type'] ?? '');
                        $locationLabel = null;
                        if ($locationType === 'home') {
                            $locationLabel = 'At my location';
                        } elseif ($locationType === 'trainer') {
                            $locationLabel = "At trainer's location";
                        } else {
                            $locationLabel = (string)($request['location_address'] ?? '');
                            if ($locationLabel === '') {
                                $locationLabel = 'Selected location';
                            }
                        }
                        $trainingType = (string)($request['training_type'] ?? '');
                        if ($petOwnerId > 0 && $trainerName !== '' && $petName !== '') {
                            NotificationHelper::createTrainerNotification(
                                $petOwnerId,
                                $trainerId,
                                $trainerName,
                                $petName,
                                'accepted',
                                null,
                                $sessionDate,
                                $sessionTime,
                                $locationLabel,
                                $trainingType
                            );
                        }
                    }
                }
                echo json_encode($result);
                break;

            case 'decline':
                $requestId = (int)($_POST['request_id'] ?? 0);
                $reason = $_POST['reason'] ?? '';
                $result = $model->declineRequest($requestId, $trainerId, $reason);
                if (!empty($result['success'])) {
                    $request = $model->getRequestById($requestId);
                    if ($request) {
                        $petOwnerId = (int)($request['pet_owner_id'] ?? 0);
                        $trainerName = (string)($request['trainer_name'] ?? '');
                        $petName = (string)($request['pet_name'] ?? '');
                        $trainingType = (string)($request['training_type'] ?? '');
                        if ($petOwnerId > 0 && $trainerName !== '' && $petName !== '') {
                            NotificationHelper::createTrainerNotification(
                                $petOwnerId,
                                $trainerId,
                                $trainerName,
                                $petName,
                                'declined',
                                $reason,
                                null,
                                null,
                                null,
                                $trainingType
                            );
                        }
                    }
                }
                echo json_encode($result);
                break;

            case 'complete':
                $sessionId = $_POST['session_id'] ?? 0;
                $notes = $_POST['notes'] ?? '';
                $nextSessionDate = $_POST['next_session_date'] ?? null;
                $nextSessionTime = $_POST['next_session_time'] ?? null;
                $nextSessionGoals = $_POST['next_session_goals'] ?? '';
                $result = $model->completeSession($sessionId, $trainerId, $notes, $nextSessionDate, $nextSessionTime, $nextSessionGoals);
                if (!empty($result['success'])) {
                    $request = $model->getRequestById($sessionId);
                    if ($request) {
                        $petOwnerId = (int)($request['pet_owner_id'] ?? 0);
                        $trainerName = (string)($request['trainer_name'] ?? '');
                        $petName = (string)($request['pet_name'] ?? '');
                        $trainingType = (string)($request['training_type'] ?? '');
                        $sessionNumber = (int)($result['session_number'] ?? 0);
                        $nextDateFormatted = !empty($nextSessionDate) ? date('F j, Y', strtotime($nextSessionDate)) : null;
                        $nextTimeFormatted = !empty($nextSessionTime) ? date('g:i A', strtotime($nextSessionTime)) : null;

                        if ($petOwnerId > 0 && $trainerName !== '' && $petName !== '') {
                            NotificationHelper::createTrainerNotification(
                                $petOwnerId,
                                $trainerId,
                                $trainerName,
                                $petName,
                                'session_completed',
                                null,
                                null,
                                null,
                                null,
                                $trainingType,
                                $sessionNumber,
                                $nextDateFormatted,
                                $nextTimeFormatted
                            );
                        }
                    }
                }
                echo json_encode($result);
                break;

            case 'mark_program_complete':
                $sessionId = $_POST['session_id'] ?? 0;
                $finalNotes = $_POST['final_notes'] ?? '';
                $result = $model->markProgramComplete($sessionId, $trainerId, $finalNotes);
                if (!empty($result['success'])) {
                    $request = $model->getRequestById($sessionId);
                    if ($request) {
                        $petOwnerId = (int)($request['pet_owner_id'] ?? 0);
                        $trainerName = (string)($request['trainer_name'] ?? '');
                        $petName = (string)($request['pet_name'] ?? '');
                        $trainingType = (string)($request['training_type'] ?? '');
                        if ($petOwnerId > 0 && $trainerName !== '' && $petName !== '') {
                            NotificationHelper::createTrainerNotification(
                                $petOwnerId,
                                $trainerId,
                                $trainerName,
                                $petName,
                                'program_completed',
                                null,
                                null,
                                null,
                                null,
                                $trainingType
                            );
                        }
                    }
                }
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
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $trainerId = (int)($_SESSION['user_id'] ?? 0);
        
        $data = [
            'profile' => $model->getProfile($trainerId),
            'preferences' => $model->getPreferences($trainerId)
        ];
        
        $this->view('trainer', 'settings', $data);
    }
}
