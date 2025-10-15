<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Breeder/DashboardModel.php';
require_once __DIR__ . '/../models/Breeder/PetsModel.php';
require_once __DIR__ . '/../models/Breeder/SalesModel.php';
require_once __DIR__ . '/../models/Breeder/SettingsModel.php';

class BreederController extends BaseController {

    public function dashboard() {
        $model = new BreederDashboardModel();
        $breederId = 1; // Mock breeder ID
        
        $data = [
            'stats' => $model->getStats($breederId),
            'activePets' => $model->getActivePets($breederId),
            'recentSales' => $model->getRecentSales($breederId)
        ];
        
        $this->view('breeder', 'dashboard', $data);
    }

    public function pets() {
        $model = new BreederPetsModel();
        $breederId = 1; // Mock breeder ID
        
        $data = [
            'pets' => $model->getAllPets($breederId),
            'availablePets' => $model->getAvailablePets($breederId),
            'breeds' => $model->getBreeds($breederId),
            'petStats' => $model->getPetStats($breederId)
        ];
        
        $this->view('breeder', 'pets', $data);
    }

    public function sales() {
        $model = new BreederSalesModel();
        $breederId = 1; // Mock breeder ID
        
        $data = [
            'sales' => $model->getAllSales($breederId),
            'pendingSales' => $model->getPendingSales($breederId),
            'completedSales' => $model->getCompletedSales($breederId),
            'revenue' => $model->getRevenueStats($breederId)
        ];
        
        $this->view('breeder', 'sales', $data);
    }

    public function settings() {
        $model = new BreederSettingsModel();
        $breederId = 1; // Mock breeder ID
        
        $data = [
            'profile' => $model->getProfile($breederId),
            'preferences' => $model->getPreferences($breederId)
        ];
        
        $this->view('breeder', 'settings', $data);
    }
}
