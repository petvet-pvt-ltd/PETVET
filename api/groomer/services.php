<?php
/**
 * Groomer Services API Endpoint
 * Handles CRUD operations for groomer services
 */

// Start session
session_start();

// Set content type
header('Content-Type: application/json');

// Check if user is authenticated
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

// Include required files
require_once __DIR__ . '/../../models/Groomer/ServicesModel.php';

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get user ID from session
$userId = $_SESSION['user_id'];

// Get action from POST
$action = $_POST['action'] ?? '';

// Initialize model
$model = new GroomerServicesModel();

// Handle different actions
switch ($action) {
    case 'add':
        // Validate required fields
        if (empty($_POST['name']) || empty($_POST['price'])) {
            echo json_encode(['success' => false, 'message' => 'Name and price are required']);
            exit;
        }
        
        // Check if at least one pet type is selected
        $forDogs = isset($_POST['for_dogs']) && $_POST['for_dogs'] === 'true';
        $forCats = isset($_POST['for_cats']) && $_POST['for_cats'] === 'true';
        
        if (!$forDogs && !$forCats) {
            echo json_encode(['success' => false, 'message' => 'Please select at least one pet type']);
            exit;
        }
        
        $result = $model->addService([
            'user_id' => $userId,
            'name' => trim($_POST['name']),
            'price' => floatval($_POST['price']),
            'duration' => trim($_POST['duration'] ?? ''),
            'for_cats' => $forCats,
            'for_dogs' => $forDogs,
            'description' => trim($_POST['description'] ?? '')
        ]);
        
        echo json_encode($result);
        break;

    case 'update':
        // Validate required fields
        if (empty($_POST['service_id']) || empty($_POST['name']) || empty($_POST['price'])) {
            echo json_encode(['success' => false, 'message' => 'Service ID, name and price are required']);
            exit;
        }
        
        $serviceId = intval($_POST['service_id']);
        
        // Check if at least one pet type is selected
        $forDogs = isset($_POST['for_dogs']) && $_POST['for_dogs'] === 'true';
        $forCats = isset($_POST['for_cats']) && $_POST['for_cats'] === 'true';
        
        if (!$forDogs && !$forCats) {
            echo json_encode(['success' => false, 'message' => 'Please select at least one pet type']);
            exit;
        }
        
        $result = $model->updateService($serviceId, [
            'user_id' => $userId,
            'name' => trim($_POST['name']),
            'price' => floatval($_POST['price']),
            'duration' => trim($_POST['duration'] ?? ''),
            'for_cats' => $forCats,
            'for_dogs' => $forDogs,
            'description' => trim($_POST['description'] ?? '')
        ]);
        
        echo json_encode($result);
        break;

    case 'delete':
        // Validate required fields
        if (empty($_POST['service_id'])) {
            echo json_encode(['success' => false, 'message' => 'Service ID is required']);
            exit;
        }
        
        $serviceId = intval($_POST['service_id']);
        $result = $model->deleteService($serviceId, $userId);
        
        echo json_encode($result);
        break;

    case 'toggle_availability':
        // Validate required fields
        if (empty($_POST['service_id'])) {
            echo json_encode(['success' => false, 'message' => 'Service ID is required']);
            exit;
        }
        
        $serviceId = intval($_POST['service_id']);
        $result = $model->toggleAvailability($serviceId, $userId);
        
        echo json_encode($result);
        break;

    case 'get':
        // Get single service
        if (empty($_POST['service_id'])) {
            echo json_encode(['success' => false, 'message' => 'Service ID is required']);
            exit;
        }
        
        $serviceId = intval($_POST['service_id']);
        $service = $model->getServiceById($serviceId, $userId);
        
        if ($service) {
            echo json_encode(['success' => true, 'service' => $service]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Service not found']);
        }
        break;

    case 'list':
        // Get all services for the groomer
        $services = $model->getAllServices($userId);
        echo json_encode(['success' => true, 'services' => $services]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
