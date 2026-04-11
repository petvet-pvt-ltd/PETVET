<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Sitter/DashboardModel.php';
require_once __DIR__ . '/../models/Sitter/BookingsModel.php';
require_once __DIR__ . '/../models/Sitter/PetsModel.php';
require_once __DIR__ . '/../models/Sitter/SettingsModel.php';
require_once __DIR__ . '/../helpers/NotificationHelper.php';

class SitterController extends BaseController {

    public function dashboard() {
        $model = new SitterDashboardModel();
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $sitterId = (int)($_SESSION['user_id'] ?? 0);
        
        $data = [
            'stats' => $model->getStats($sitterId),
            'upcomingBookings' => $model->getUpcomingBookings($sitterId, 5)
        ];
        
        $this->view('sitter', 'dashboard', $data);
    }

    public function bookings() {
        $model = new SitterBookingsModel();
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $sitterId = (int)($_SESSION['user_id'] ?? 0);
        
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
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $sitterId = (int)($_SESSION['user_id'] ?? 0);

        $model = new SitterBookingsModel();
        
        switch ($action) {
            case 'accept':
                $result = $model->acceptBooking($bookingId, $sitterId);
                if (!empty($result['success'])) {
                    $booking = $model->getBookingById($bookingId, $sitterId);
                    if ($booking) {
                        $petOwnerId = (int)($booking['pet_owner_id'] ?? 0);
                        $sitterName = (string)($booking['sitter_name'] ?? '');
                        $petName = (string)($booking['pet_name'] ?? '');
                        if ($petOwnerId > 0 && $sitterName !== '' && $petName !== '') {
                            NotificationHelper::createSitterNotification(
                                $petOwnerId,
                                $sitterId,
                                $sitterName,
                                $petName,
                                'accepted',
                                null
                            );
                        }
                    }
                }
                break;
            case 'decline':
                $reason = $_POST['reason'] ?? '';
                if (is_string($reason)) {
                    $reason = substr(trim($reason), 0, 255);
                }
                $result = $model->declineBooking($bookingId, $sitterId, $reason);
                if (!empty($result['success'])) {
                    $booking = $model->getBookingById($bookingId, $sitterId);
                    if ($booking) {
                        $petOwnerId = (int)($booking['pet_owner_id'] ?? 0);
                        $sitterName = (string)($booking['sitter_name'] ?? '');
                        $petName = (string)($booking['pet_name'] ?? '');
                        if ($petOwnerId > 0 && $sitterName !== '' && $petName !== '') {
                            NotificationHelper::createSitterNotification(
                                $petOwnerId,
                                $sitterId,
                                $sitterName,
                                $petName,
                                'declined',
                                is_string($reason) ? $reason : ''
                            );
                        }
                    }
                }
                break;
            case 'complete':
                $result = $model->completeBooking($bookingId, $sitterId);
                if (!empty($result['success'])) {
                    $booking = $model->getBookingById($bookingId, $sitterId);
                    if ($booking) {
                        $petOwnerId = (int)($booking['pet_owner_id'] ?? 0);
                        $sitterName = (string)($booking['sitter_name'] ?? '');
                        $petName = (string)($booking['pet_name'] ?? '');
                        if ($petOwnerId > 0 && $sitterName !== '' && $petName !== '') {
                            NotificationHelper::createSitterNotification(
                                $petOwnerId,
                                $sitterId,
                                $sitterName,
                                $petName,
                                'completed',
                                null
                            );
                        }
                    }
                }
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
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $sitterId = (int)($_SESSION['user_id'] ?? 0);
        
        $data = [
            'currentPets' => $model->getCurrentPets($sitterId),
            'petHistory' => $model->getPetHistory($sitterId),
            'petStats' => $model->getPetStats($sitterId)
        ];
        
        $this->view('sitter', 'pets', $data);
    }

    public function settings() {
        $model = new SitterSettingsModel();
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $sitterId = (int)($_SESSION['user_id'] ?? 0);
        
        $data = [
            'profile' => $model->getProfile($sitterId),
            'preferences' => $model->getPreferences($sitterId)
        ];
        
        $this->view('sitter', 'settings', $data);
    }
}
