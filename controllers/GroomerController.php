<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Groomer/ServicesModel.php';
require_once __DIR__ . '/../models/Groomer/PackagesModel.php';
require_once __DIR__ . '/../models/Groomer/SettingsModel.php';

class GroomerController extends BaseController {

    public function services() {
        $model = new GroomerServicesModel();
        $groomerId = 1; // Mock groomer ID
        
        $data = [
            'services' => $model->getAllServices($groomerId)
        ];
        
        $this->view('groomer', 'services', $data);
    }

    public function handleServiceAction() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $action = $_POST['action'] ?? '';
        $groomerId = 1; // Mock groomer ID
        $model = new GroomerServicesModel();

        switch ($action) {
            case 'add':
                $result = $model->addService([
                    'groomer_id' => $groomerId,
                    'name' => $_POST['name'] ?? '',
                    'price' => $_POST['price'] ?? 0,
                    'for_cats' => isset($_POST['for_cats']) && $_POST['for_cats'] === 'true',
                    'for_dogs' => isset($_POST['for_dogs']) && $_POST['for_dogs'] === 'true',
                    'description' => $_POST['description'] ?? ''
                ]);
                echo json_encode($result);
                break;

            case 'update':
                $serviceId = $_POST['service_id'] ?? 0;
                $result = $model->updateService($serviceId, [
                    'name' => $_POST['name'] ?? '',
                    'price' => $_POST['price'] ?? 0,
                    'for_cats' => isset($_POST['for_cats']) && $_POST['for_cats'] === 'true',
                    'for_dogs' => isset($_POST['for_dogs']) && $_POST['for_dogs'] === 'true',
                    'description' => $_POST['description'] ?? ''
                ]);
                echo json_encode($result);
                break;

            case 'delete':
                $serviceId = $_POST['service_id'] ?? 0;
                $result = $model->deleteService($serviceId, $groomerId);
                echo json_encode($result);
                break;

            case 'toggle_availability':
                $serviceId = $_POST['service_id'] ?? 0;
                $result = $model->toggleAvailability($serviceId, $groomerId);
                echo json_encode($result);
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
    }

    public function packages() {
        $model = new GroomerPackagesModel();
        $groomerId = 1; // Mock groomer ID
        
        $data = [
            'packages' => $model->getAllPackages($groomerId)
        ];
        
        $this->view('groomer', 'packages', $data);
    }

    public function handlePackageAction() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $action = $_POST['action'] ?? '';
        $groomerId = 1; // Mock groomer ID
        $model = new GroomerPackagesModel();

        switch ($action) {
            case 'add':
                $result = $model->addPackage([
                    'groomer_id' => $groomerId,
                    'name' => $_POST['name'] ?? '',
                    'original_price' => $_POST['original_price'] ?? 0,
                    'discounted_price' => $_POST['discounted_price'] ?? 0,
                    'for_cats' => isset($_POST['for_cats']) && $_POST['for_cats'] === 'true',
                    'for_dogs' => isset($_POST['for_dogs']) && $_POST['for_dogs'] === 'true',
                    'description' => $_POST['description'] ?? '',
                    'included_services' => $_POST['included_services'] ?? ''
                ]);
                echo json_encode($result);
                break;

            case 'update':
                $packageId = $_POST['package_id'] ?? 0;
                $result = $model->updatePackage($packageId, [
                    'name' => $_POST['name'] ?? '',
                    'original_price' => $_POST['original_price'] ?? 0,
                    'discounted_price' => $_POST['discounted_price'] ?? 0,
                    'for_cats' => isset($_POST['for_cats']) && $_POST['for_cats'] === 'true',
                    'for_dogs' => isset($_POST['for_dogs']) && $_POST['for_dogs'] === 'true',
                    'description' => $_POST['description'] ?? '',
                    'included_services' => $_POST['included_services'] ?? ''
                ]);
                echo json_encode($result);
                break;

            case 'delete':
                $packageId = $_POST['package_id'] ?? 0;
                $result = $model->deletePackage($packageId, $groomerId);
                echo json_encode($result);
                break;

            case 'toggle_availability':
                $packageId = $_POST['package_id'] ?? 0;
                $result = $model->toggleAvailability($packageId, $groomerId);
                echo json_encode($result);
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
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
