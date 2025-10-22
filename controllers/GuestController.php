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
        $products = $this->shopModel->getAllProducts();
        $categories = $this->shopModel->getCategories();
        
        // Add multiple images to each product
        foreach ($products as &$product) {
            $images = $this->shopModel->getProductImages($product['id']);
            $product['images'] = !empty($images) ? $images : [$product['image']];
        }
        
        $this->guestView('shop', [
            'products' => $products,
            'categories' => $categories
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
        extract($data);
        $page = $name;
        include __DIR__ . '/../views/guest/' . $name . '.php';
    }
}
?>