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
require_once __DIR__ . '/../models/PetOwner/ServicesModel.php';
require_once __DIR__ . '/../models/PetOwner/ShopModel.php';

class PetOwnerController extends BaseController {

    public function myPets() {
        $model = new MyPetsModel();
        $data = [
            'pets' => $model->fetchPets(),
            'clinics' => $model->getClinics()
        ];
        $this->view('pet-owner', 'my-pets', $data);
    }

    // /PETVET/?module=pet-owner&page=medical-records&pet=1
    public function medicalRecords() {
        $petId = isset($_GET['pet']) ? (int)$_GET['pet'] : 0;
        $ownerId = $_SESSION['user_id'] ?? 0;
        
        if ($petId <= 0 || $ownerId <= 0) {
            header("Location: /PETVET/?module=pet-owner&page=my-pets");
            exit;
        }

        $model = new MedicalRecordsModel();
        $full  = $model->getFullMedicalRecordByPetId($petId, $ownerId);
        if (!$full) {
            http_response_code(404);
            $this->view('errors', '404', ['message' => 'Pet not found or access denied']);
            return;
        }

        // Pass all data to view
        $data = [
            'pet'               => $full['pet'],
            'clinic_visits'     => $full['clinic_visits'],
            'medical_records'   => $full['medical_records'],
            'vaccinations'      => $full['vaccinations'],
            'prescriptions'     => $full['prescriptions'],
        ];

        $this->view('pet-owner', 'medical-records', $data);
    }

    // UI-only pages migrated from standalone prototypes (#now working on)
    public function appointments() {
        $appointmentsModel = new PetOwnerAppointmentsModel();
        
        // Fetch upcoming appointments for the current pet owner
        $ownerId = $_SESSION['user_id'] ?? null;
        
        if (!$ownerId) {
            // Redirect to login if not authenticated
            header('Location: /PETVET/index.php?module=guest&page=login');
            exit;
        }
        
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
        
        // Get current user ID from session
        $currentUserId = $_SESSION['user_id'] ?? null;
        
        if (!$currentUserId) {
            header('Location: /PETVET/index.php?module=guest&page=login');
            exit;
        }
        
        $data = [
            'profile' => $settingsModel->getUserProfile($currentUserId),
            'prefs' => $settingsModel->getUserPreferences($currentUserId),
            'accountStats' => $settingsModel->getAccountStats($currentUserId)
        ];
        
        $this->view('pet-owner', 'settings', $data);
    }

    public function services() {
        $servicesModel = new ServicesModel();
        
        // Get service type and filters from query params
        $serviceType = isset($_GET['type']) ? $_GET['type'] : 'trainers';
        
        // Build filters array from query params
        $filters = [];
        if (isset($_GET['search'])) $filters['search'] = $_GET['search'];
        if (isset($_GET['city'])) $filters['city'] = $_GET['city'];
        if (isset($_GET['experience'])) $filters['experience'] = $_GET['experience'];
        
        // Service-specific filters
        if ($serviceType === 'trainers' && isset($_GET['specialization'])) {
            $filters['specialization'] = $_GET['specialization'];
        }
        
        if ($serviceType === 'trainers' && isset($_GET['training_type'])) {
            $filters['training_type'] = $_GET['training_type'];
        }
        
        if ($serviceType === 'sitters') {
            if (isset($_GET['pet_type'])) $filters['pet_type'] = $_GET['pet_type'];
            if (isset($_GET['home_type'])) $filters['home_type'] = $_GET['home_type'];
        }
        
        if ($serviceType === 'breeders') {
            if (isset($_GET['breed'])) $filters['breed'] = $_GET['breed'];
            if (isset($_GET['gender'])) $filters['gender'] = $_GET['gender'];
        }
        
        if ($serviceType === 'groomers') {
            if (isset($_GET['show'])) $filters['show'] = $_GET['show'];
            if (isset($_GET['service_type'])) $filters['service_type'] = $_GET['service_type'];
            if (isset($_GET['specialization'])) $filters['specialization'] = $_GET['specialization'];
            if (isset($_GET['min_price'])) $filters['min_price'] = $_GET['min_price'];
            if (isset($_GET['max_price'])) $filters['max_price'] = $_GET['max_price'];
            if (isset($_GET['groomer_id'])) $filters['groomer_id'] = $_GET['groomer_id'];
        }
        
        // Fetch providers and cities for dropdown
        $providers = $servicesModel->getServiceProviders($serviceType, $filters);
        $cities = $servicesModel->getCities($serviceType);
        
        $data = [
            'serviceType' => $serviceType,
            'providers' => $providers,
            'cities' => $cities,
            'filters' => $filters
        ];
        
                
        $this->view('pet-owner', 'services', $data);
    }

    public function shop() {
        // Fetch all active clinics
        $pdo = db();
        $sql = "SELECT 
                    id,
                    clinic_name,
                    clinic_description,
                    clinic_logo,
                    clinic_address,
                    map_location,
                    city,
                    district
                FROM clinics 
                WHERE is_active = 1 
                AND verification_status = 'approved'
                ORDER BY clinic_name";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $clinics = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $this->view('pet-owner', 'shop', [
            'clinics' => $clinics
        ]);
    }

    public function shopClinic() {
        $clinicId = isset($_GET['clinic_id']) ? (int)$_GET['clinic_id'] : 0;
        
        if ($clinicId <= 0) {
            header("Location: /PETVET/?module=pet-owner&page=shop");
            exit;
        }
        
        $pdo = db();
        
        // Fetch clinic details
        $stmt = $pdo->prepare("SELECT * FROM clinics WHERE id = ? AND is_active = 1 AND verification_status = 'approved'");
        $stmt->execute([$clinicId]);
        $clinic = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$clinic) {
            header("Location: /PETVET/?module=pet-owner&page=shop");
            exit;
        }
        
        // Fetch products for this clinic
        $stmt = $pdo->prepare("SELECT * FROM products WHERE clinic_id = ? AND is_active = 1 ORDER BY created_at DESC");
        $stmt->execute([$clinicId]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Fetch images for each product
        foreach ($products as &$product) {
            $imgStmt = $pdo->prepare("SELECT image_url FROM product_images WHERE product_id = ? ORDER BY display_order");
            $imgStmt->execute([$product['id']]);
            $images = $imgStmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Use product_images if available, otherwise use main image_url
            if (!empty($images)) {
                $product['images'] = $images;
            } else if (!empty($product['image_url'])) {
                $product['images'] = [$product['image_url']];
            } else {
                $product['images'] = ['public/images/product-placeholder.png'];
            }
        }
        
        $this->view('pet-owner', 'shop-clinic', [
            'clinic' => $clinic,
            'products' => $products
        ]);
    }

    public function shopProduct() {
        $productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($productId <= 0) {
            header("Location: /PETVET/?module=pet-owner&page=shop");
            exit;
        }
        
        $shopModel = new PetOwnerShopModel();
        $product = $shopModel->getProductById($productId);
        
        if (!$product) {
            header("Location: /PETVET/?module=pet-owner&page=shop");
            exit;
        }
        
        // Add multiple images to product
        $images = $shopModel->getProductImages($productId);
        $product['images'] = !empty($images) ? $images : [$product['image']];
        
        $relatedProducts = $shopModel->getRelatedProducts($productId, $product['category'], 4);
        
        // Add multiple images to related products
        foreach ($relatedProducts as &$related) {
            $relImages = $shopModel->getProductImages($related['id']);
            $related['images'] = !empty($relImages) ? $relImages : [$related['image']];
        }
        
        $data = [
            'product' => $product,
            'relatedProducts' => $relatedProducts
        ];
        
        $this->view('pet-owner', 'shop-product', $data);
    }

    public function paymentSuccess() {
        // Simply render the payment success page
        // The page itself handles cart clearing and order saving via JavaScript
        $this->viewStandalone('pet-owner', 'payment-success', []);
    }

    public function paymentCancel() {
        // Simply render the payment cancel page
        $this->viewStandalone('pet-owner', 'payment-cancel', []);
    }

    public function orders() {
        // Render the orders page
        // Orders are loaded from localStorage via JavaScript
        $this->view('pet-owner', 'orders', []);
    }
}
