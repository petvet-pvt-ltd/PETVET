<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Breeder/DashboardModel.php';
require_once __DIR__ . '/../models/Breeder/PetsModel.php';
require_once __DIR__ . '/../models/Breeder/SettingsModel.php';

class BreederController extends BaseController {

    public function dashboard() {
        $model = new BreederDashboardModel();
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $breederId = (int)($_SESSION['user_id'] ?? 0);
        
        $data = [
            'stats' => $model->getStats($breederId),
            'upcomingBreedingDates' => $model->getUpcomingBreedingDates($breederId, 5)
        ];
        
        $this->view('breeder', 'dashboard', $data);
    }

    public function requests() {
        $model = new BreederDashboardModel();
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $breederId = (int)($_SESSION['user_id'] ?? 0);
        
        $data = [
            'pendingRequests' => $model->getPendingRequests($breederId),
            'approvedRequests' => $model->getApprovedRequests($breederId),
            'completedRequests' => $model->getCompletedRequests($breederId)
        ];
        
        $this->view('breeder', 'requests', $data);
    }

    public function breedingPets() {
        $model = new BreederPetsModel();
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        $data = [
            'breedingPets' => $model->getBreedingPetsByBreed('Golden Retriever')
        ];
        
        $this->view('breeder', 'breeding-pets', $data);
    }

    public function availability() {
        $this->view('breeder', 'availability', []);
    }

    public function pets() {
        $model = new BreederPetsModel();
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $breederId = (int)($_SESSION['user_id'] ?? 0);
        
        $data = [
            'pets' => $model->getAllPets($breederId),
            'availablePets' => $model->getAvailablePets($breederId),
            'breeds' => $model->getBreeds($breederId),
            'petStats' => $model->getPetStats($breederId)
        ];
        
        $this->view('breeder', 'pets', $data);
    }

    public function settings() {
        $model = new BreederSettingsModel();
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $breederId = (int)($_SESSION['user_id'] ?? 0);
        
        $data = [
            'profile' => $model->getProfile($breederId),
            'preferences' => $model->getPreferences($breederId)
        ];
        
        $this->view('breeder', 'settings', $data);
    }
}
