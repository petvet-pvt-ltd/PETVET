<?php
// controllers
require_once __DIR__ . '/BaseController.php';

// Models
require_once __DIR__ . '/../models/PetOwner/MyPetsModel.php';
require_once __DIR__ . '/../models/PetOwner/MedicalRecordsModel.php';
require_once __DIR__ . '/../models/PetOwner/AppointmentsModel.php';
require_once __DIR__ . '/../models/PetOwner/LostFoundModel.php';
require_once __DIR__ . '/../models/PetOwner/ExplorePetsModel.php';
require_once __DIR__ . '/../models/PetOwner/SellPetsModel.php';
require_once __DIR__ . '/../models/PetOwner/SettingsModel.php';

class PetOwnerController extends BaseController {

    public function myPets() {
        $model = new MyPetsModel();
        $data = ['pets' => $model->fetchPets()];
        $this->view('pet-owner', 'my-pets', $data);
    }

    // /PETVET/?module=pet-owner&page=medical-records&pet=1
    public function medicalRecords() {
        $petId = isset($_GET['pet']) ? (int)$_GET['pet'] : 0;
        if ($petId <= 0) {
            header("Location: /PETVET/?module=pet-owner&page=my-pets");
            exit;
        }

        $model = new MedicalRecordsModel();
        $full  = $model->getFullMedicalRecordByPetId($petId);
        if (!$full) {
            http_response_code(404);
            $this->view('errors', '404', ['message' => 'Pet not found']);
            return;
        }

        // Match variables used by the view exactly
        $data = [
            'pet'            => $full['pet'],
            'clinic_visits'  => $full['clinic_visits'],
            'vaccinations'   => $full['vaccinations'],
            'reports'        => $full['reports'],
        ];

        $this->view('pet-owner', 'medical-records', $data);
    }

    // UI-only pages migrated from standalone prototypes (#now working on)
    public function appointments() {
        $appointmentsModel = new PetOwnerAppointmentsModel();
        
        // Fetch upcoming appointments for the current pet owner
        // In a real app, you'd get the owner ID from session/auth
        $ownerId = 1; // Mock owner ID for testing
        
        $data = $appointmentsModel->getUpcomingAppointments($ownerId);
        
        $this->view('pet-owner', 'appointments', $data);
    }

    public function lostFound() {
        $lostFoundModel = new LostFoundModel();
        
        $data = [
            'reports' => $lostFoundModel->getAllReports(),
            'lostReports' => $lostFoundModel->getLostReports(),
            'foundReports' => $lostFoundModel->getFoundReports()
        ];
        
        $this->view('pet-owner', 'lost-found', $data);
    }

    public function explorePets() {
        $explorePetsModel = new ExplorePetsModel();
        
        // Get current user ID (mock for now)
        $currentUserId = 1;
        
        $data = [
            'currentUser' => ['id' => $currentUserId, 'name' => 'You'],
            'sellers' => $explorePetsModel->getAllSellers(),
            'pets' => $explorePetsModel->getAllPets(),
            'myListings' => $explorePetsModel->getPetsByUserId($currentUserId),
            'availableSpecies' => $explorePetsModel->getAvailableSpecies()
        ];
        
        $this->view('pet-owner', 'explore-pets', $data);
    }

    public function sellPets() {
        $sellPetsModel = new SellPetsModel();
        
        // Get current user ID (mock for now)
        $currentUserId = 1;
        
        $data = [
            'formData' => $sellPetsModel->getFormData(),
            'userListings' => $sellPetsModel->getUserListings($currentUserId),
            'listingStats' => $sellPetsModel->getListingStats($currentUserId)
        ];
        
        $this->view('pet-owner', 'sell-pets', $data);
    }

    public function settings() {
        $settingsModel = new SettingsModel();
        
        // Get current user ID (mock for now)
        $currentUserId = 1;
        
        $data = [
            'profile' => $settingsModel->getUserProfile($currentUserId),
            'prefs' => $settingsModel->getUserPreferences($currentUserId),
            'accountStats' => $settingsModel->getAccountStats($currentUserId)
        ];
        
        $this->view('pet-owner', 'settings', $data);
    }
}
