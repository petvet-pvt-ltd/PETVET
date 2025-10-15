<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Sitter/DashboardModel.php';
require_once __DIR__ . '/../models/Sitter/BookingsModel.php';
require_once __DIR__ . '/../models/Sitter/PetsModel.php';
require_once __DIR__ . '/../models/Sitter/SettingsModel.php';

class SitterController extends BaseController {

    public function dashboard() {
        $model = new SitterDashboardModel();
        $sitterId = 1; // Mock sitter ID
        
        $data = [
            'stats' => $model->getStats($sitterId),
            'upcomingBookings' => $model->getUpcomingBookings($sitterId, 5)
        ];
        
        $this->view('sitter', 'dashboard', $data);
    }

    public function bookings() {
        $model = new SitterBookingsModel();
        $sitterId = $_SESSION['user_id'] ?? 1; // Get from session or mock
        
        $data = [
            'bookings' => $model->getAllBookings($sitterId),
            'pendingBookings' => $model->getPendingBookings($sitterId),
            'activeBookings' => $model->getActiveBookings($sitterId),
            'completedBookings' => $model->getCompletedBookings($sitterId)
        ];
        
        $this->view('sitter', 'bookings', $data);
    }

    public function handleBookingAction() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        $action = $_POST['action'] ?? '';
        $bookingId = $_POST['booking_id'] ?? 0;
        $sitterId = $_SESSION['user_id'] ?? 1;

        $model = new SitterBookingsModel();
        
        switch ($action) {
            case 'accept':
                $result = $model->acceptBooking($bookingId, $sitterId);
                break;
            case 'decline':
                $result = $model->declineBooking($bookingId, $sitterId);
                break;
            case 'complete':
                $result = $model->completeBooking($bookingId, $sitterId);
                break;
            default:
                $result = ['success' => false, 'message' => 'Invalid action'];
        }

        header('Content-Type: application/json');
        echo json_encode($result);
    }

    public function availability() {
        $this->view('sitter', 'availability');
    }

    public function pets() {
        $model = new SitterPetsModel();
        $sitterId = 1; // Mock sitter ID
        
        $data = [
            'currentPets' => $model->getCurrentPets($sitterId),
            'petHistory' => $model->getPetHistory($sitterId),
            'petStats' => $model->getPetStats($sitterId)
        ];
        
        $this->view('sitter', 'pets', $data);
    }

    public function settings() {
        $model = new SitterSettingsModel();
        $sitterId = 1; // Mock sitter ID
        
        $data = [
            'profile' => $model->getProfile($sitterId),
            'preferences' => $model->getPreferences($sitterId)
        ];
        
        $this->view('sitter', 'settings', $data);
    }
}
