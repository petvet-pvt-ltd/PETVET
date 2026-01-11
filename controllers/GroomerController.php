<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Groomer/ServicesModel.php';
require_once __DIR__ . '/../models/Groomer/PackagesModel.php';
require_once __DIR__ . '/../models/Groomer/SettingsModel.php';

class GroomerController extends BaseController {

    /**
     * Get the current logged-in user's ID
     */
    private function getUserId() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'User not authenticated']);
            exit;
        }
        return $_SESSION['user_id'];
    }

    public function services() {
        $model = new GroomerServicesModel();
        $userId = $this->getUserId();
        
        $data = [
            'services' => $model->getAllServices($userId)
        ];
        
        $this->view('groomer', 'services', $data);
    }

    public function handleServiceAction() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $userId = $this->getUserId();
        $action = $_POST['action'] ?? '';
        $model = new GroomerServicesModel();

        switch ($action) {
            case 'add':
                $result = $model->addService([
                    'user_id' => $userId,
                    'name' => $_POST['name'] ?? '',
                    'price' => $_POST['price'] ?? 0,
                    'duration' => $_POST['duration'] ?? null,
                    'for_cats' => isset($_POST['for_cats']) && $_POST['for_cats'] === 'true',
                    'for_dogs' => isset($_POST['for_dogs']) && $_POST['for_dogs'] === 'true',
                    'description' => $_POST['description'] ?? ''
                ]);
                echo json_encode($result);
                break;

            case 'update':
                $serviceId = $_POST['service_id'] ?? 0;
                $result = $model->updateService($serviceId, [
                    'user_id' => $userId,
                    'name' => $_POST['name'] ?? '',
                    'price' => $_POST['price'] ?? 0,
                    'duration' => $_POST['duration'] ?? null,
                    'for_cats' => isset($_POST['for_cats']) && $_POST['for_cats'] === 'true',
                    'for_dogs' => isset($_POST['for_dogs']) && $_POST['for_dogs'] === 'true',
                    'description' => $_POST['description'] ?? ''
                ]);
                echo json_encode($result);
                break;

            case 'delete':
                $serviceId = $_POST['service_id'] ?? 0;
                $result = $model->deleteService($serviceId, $userId);
                echo json_encode($result);
                break;

            case 'toggle_availability':
                $serviceId = $_POST['service_id'] ?? 0;
                $result = $model->toggleAvailability($serviceId, $userId);
                echo json_encode($result);
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
    }

    public function packages() {
        $model = new GroomerPackagesModel();
        $userId = $this->getUserId();
        
        $data = [
            'packages' => $model->getAllPackages($userId)
        ];
        
        $this->view('groomer', 'packages', $data);
    }

    public function handlePackageAction() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $userId = $this->getUserId();
        $action = $_POST['action'] ?? '';
        $model = new GroomerPackagesModel();

        switch ($action) {
            case 'add':
                // Parse service IDs from comma-separated string or array
                $serviceIds = $this->parseServiceIds($_POST['service_ids'] ?? '');
                
                $result = $model->addPackage([
                    'user_id' => $userId,
                    'name' => $_POST['name'] ?? '',
                    'original_price' => $_POST['original_price'] ?? 0,
                    'discounted_price' => $_POST['discounted_price'] ?? 0,
                    'duration' => $_POST['duration'] ?? null,
                    'for_cats' => isset($_POST['for_cats']) && $_POST['for_cats'] === 'true',
                    'for_dogs' => isset($_POST['for_dogs']) && $_POST['for_dogs'] === 'true',
                    'description' => $_POST['description'] ?? '',
                    'included_services' => $_POST['included_services'] ?? '',
                    'service_ids' => $serviceIds
                ]);
                echo json_encode($result);
                break;

            case 'update':
                $packageId = $_POST['package_id'] ?? 0;
                $serviceIds = $this->parseServiceIds($_POST['service_ids'] ?? '');
                
                $result = $model->updatePackage($packageId, [
                    'user_id' => $userId,
                    'name' => $_POST['name'] ?? '',
                    'original_price' => $_POST['original_price'] ?? 0,
                    'discounted_price' => $_POST['discounted_price'] ?? 0,
                    'duration' => $_POST['duration'] ?? null,
                    'for_cats' => isset($_POST['for_cats']) && $_POST['for_cats'] === 'true',
                    'for_dogs' => isset($_POST['for_dogs']) && $_POST['for_dogs'] === 'true',
                    'description' => $_POST['description'] ?? '',
                    'included_services' => $_POST['included_services'] ?? '',
                    'service_ids' => $serviceIds
                ]);
                echo json_encode($result);
                break;

            case 'delete':
                $packageId = $_POST['package_id'] ?? 0;
                $result = $model->deletePackage($packageId, $userId);
                echo json_encode($result);
                break;

            case 'toggle_availability':
                $packageId = $_POST['package_id'] ?? 0;
                $result = $model->toggleAvailability($packageId, $userId);
                echo json_encode($result);
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
    }
    
    /**
     * Parse service IDs from various formats
     */
    private function parseServiceIds($input) {
        if (is_array($input)) {
            return array_map('intval', $input);
        }
        
        if (is_string($input) && !empty($input)) {
            return array_map('intval', explode(',', $input));
        }
        
        return [];
    }

    public function availability() {
        $this->view('groomer', 'availability');
    }

    public function settings() {
        $model = new GroomerSettingsModel();
        $groomerId = 1; // Mock groomer ID
        
        $data = [
            'profile' => $model->getProfile($groomerId),
            'preferences' => $model->getPreferences($groomerId)
        ];
        
        $this->view('groomer', 'settings', $data);
    }
}
