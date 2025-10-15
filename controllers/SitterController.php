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
            'activeBookings' => $model->getActiveBookings($sitterId),
            'upcomingBookings' => $model->getUpcomingBookings($sitterId)
        ];
        
        $this->view('sitter', 'dashboard', $data);
    }

    public function bookings() {
        $model = new SitterBookingsModel();
        $sitterId = 1; // Mock sitter ID
        
        $data = [
            'bookings' => $model->getAllBookings($sitterId),
            'pendingBookings' => $model->getPendingBookings($sitterId),
            'activeBookings' => $model->getActiveBookings($sitterId),
            'completedBookings' => $model->getCompletedBookings($sitterId)
        ];
        
        $this->view('sitter', 'bookings', $data);
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
