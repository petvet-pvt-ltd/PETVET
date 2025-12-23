<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Guest/GuestShopModel.php';
require_once __DIR__ . '/../models/Guest/GuestExplorePetsModel.php';
require_once __DIR__ . '/../models/Guest/GuestLostFoundModel.php';
require_once __DIR__ . '/../models/RegistrationModel.php';

class GuestController extends BaseController {
    private $shopModel;
    private $explorePetsModel;
    private $lostFoundModel;
    private $registrationModel;

    public function __construct() {
        $this->shopModel = new GuestShopModel();
        $this->explorePetsModel = new GuestExplorePetsModel();
        $this->lostFoundModel = new GuestLostFoundModel();
        $this->registrationModel = new RegistrationModel();
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
        
        $this->guestView('shop', [
            'clinics' => $clinics
        ]);
    }

    public function shopClinic() {
        $clinicId = isset($_GET['clinic_id']) ? (int)$_GET['clinic_id'] : 0;
        
        if ($clinicId <= 0) {
            header("Location: /PETVET/?module=guest&page=shop");
            exit;
        }
        
        $pdo = db();
        
        // Fetch clinic details
        $stmt = $pdo->prepare("SELECT * FROM clinics WHERE id = ? AND is_active = 1 AND verification_status = 'approved'");
        $stmt->execute([$clinicId]);
        $clinic = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$clinic) {
            header("Location: /PETVET/?module=guest&page=shop");
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
        
        $this->guestView('shop-clinic', [
            'clinic' => $clinic,
            'products' => $products
        ]);
    }

    public function shopProduct() {
        $productId = $_GET['id'] ?? 1;
        $product = $this->shopModel->getProductById($productId);
        $relatedProducts = $this->shopModel->getRelatedProducts($productId, $product['category'] ?? 'food');
        
        if (!$product) {
            // Redirect to shop if product not found
            $this->redirect('/PETVET/index.php?module=guest&page=shop');
            return;
        }
        
        // Add multiple images to product and related products
        $images = $this->shopModel->getProductImages($productId);
        $product['images'] = !empty($images) ? $images : [$product['image']];
        
        foreach ($relatedProducts as &$related) {
            $relImages = $this->shopModel->getProductImages($related['id']);
            $related['images'] = !empty($relImages) ? $relImages : [$related['image']];
        }

        $this->guestView('shop-product', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'productId' => $productId
        ]);
    }

    public function explorePets() {
        $sellers = $this->explorePetsModel->getAllSellers();
        $pets = $this->explorePetsModel->getAllPets();
        
        $this->guestView('explore-pets', [
            'sellers' => $sellers,
            'pets' => $pets
        ]);
    }

    public function lostFound() {
        $reports = $this->lostFoundModel->getAllReports();
        $lostReports = $this->lostFoundModel->getLostReports();
        $foundReports = $this->lostFoundModel->getFoundReports();
        
        $this->guestView('lost-found', [
            'reports' => $reports,
            'lostReports' => $lostReports,
            'foundReports' => $foundReports
        ]);
    }

    public function register() {
        // If it's a POST request, handle registration
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../controllers/RegistrationController.php';
            $registrationController = new RegistrationController();
            $registrationController->register();
        } else {
            // Otherwise, just show the registration form
            // The form file has its own auth check and will handle the display
            include __DIR__ . '/../views/guest/register.php';
        }
    }

    public function vetRegister() {
        // Debug logging to custom file
        $debug = "=== VET REGISTER CALLED ===\n";
        $debug .= "Time: " . date('Y-m-d H:i:s') . "\n";
        $debug .= "Method: " . $_SERVER['REQUEST_METHOD'] . "\n";
        $debug .= "POST data: " . print_r($_POST, true) . "\n";
        file_put_contents(__DIR__ . '/../vet-debug.log', $debug, FILE_APPEND);
        
        error_log("=== VET REGISTER METHOD CALLED ===");
        error_log("Request method: " . $_SERVER['REQUEST_METHOD']);
        error_log("POST data: " . print_r($_POST, true));
        
        // If it's a POST request, handle veterinarian registration
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            file_put_contents(__DIR__ . '/../vet-debug.log', "Processing POST request\n", FILE_APPEND);
            error_log("Processing vet registration POST request");
            require_once __DIR__ . '/../controllers/RegistrationController.php';
            $registrationController = new RegistrationController();
            $registrationController->register();
        } else {
            file_put_contents(__DIR__ . '/../vet-debug.log', "Showing form (GET)\n", FILE_APPEND);
            error_log("Showing vet registration form (GET request)");
            // Otherwise, just show the vet registration form
            // The form file has its own auth check and will handle the display
            include __DIR__ . '/../views/guest/vet-register.php';
        }
    }

    public function clinicManagerRegister() {
        // If it's a POST request, handle clinic manager registration
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../controllers/RegistrationController.php';
            $registrationController = new RegistrationController();
            $registrationController->register();
        } else {
            // Otherwise, just show the clinic manager registration form
            include __DIR__ . '/../views/guest/clinic-manager-register.php';
        }
    }

    // Modified view method for guest pages (no sidebar)
    protected function guestView(string $name, array $data = []) {
        // Make data available globally to ensure it persists through includes
        foreach ($data as $key => $value) {
            $GLOBALS[$key] = $value;
        }
        extract($data);
        $page = $name;
        include __DIR__ . '/../views/guest/' . $name . '.php';
    }
}
?>