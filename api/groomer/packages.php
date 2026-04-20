<?php
/**
 * Groomer Packages API Endpoint - Handles CRUD operations for groomer packages
 */

session_start();
header('Content-Type: application/json');

// Verify user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

// Verify POST request method is used
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../../models/Groomer/PackagesModel.php';

$userId = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';
$model = new GroomerPackagesModel();

// Helper function to convert comma-separated or array service IDs to integers
function parseServiceIds($input) {
    if (is_array($input)) {
        return array_map('intval', array_filter($input));
    }
    
    if (is_string($input) && !empty($input)) {
        $ids = explode(',', $input);
        return array_map('intval', array_filter($ids));
    }
    
    return [];
}

// Route to create new package with validation
switch ($action) {
    case 'add':
        // Validate required fields
        if (empty($_POST['name']) || empty($_POST['discounted_price'])) {
            echo json_encode(['success' => false, 'message' => 'Name and discounted price are required']);
            exit;
        }
        
        // Check if at least one pet type is selected
        $forDogs = isset($_POST['for_dogs']) && $_POST['for_dogs'] === 'true';
        $forCats = isset($_POST['for_cats']) && $_POST['for_cats'] === 'true';
        
        if (!$forDogs && !$forCats) {
            echo json_encode(['success' => false, 'message' => 'Please select at least one pet type']);
            exit;
        }
        
        // Parse service IDs
        $serviceIds = parseServiceIds($_POST['service_ids'] ?? '');
        
        if (empty($serviceIds)) {
            echo json_encode(['success' => false, 'message' => 'Please select at least one service']);
            exit;
        }
        
        $result = $model->addPackage([
            'user_id' => $userId,
            'name' => trim($_POST['name']),
            'original_price' => floatval($_POST['original_price']),
            'discounted_price' => floatval($_POST['discounted_price']),
            'duration' => trim($_POST['duration'] ?? ''), // '?? Medium'
            'for_cats' => $forCats,
            'for_dogs' => $forDogs,
            'description' => trim($_POST['description'] ?? ''),
            'included_services' => trim($_POST['included_services'] ?? ''),
            'service_ids' => $serviceIds
        ]);
        
        echo json_encode($result);
        break;

    case 'update':
        // Route to update existing package with validation
        if (empty($_POST['package_id']) || empty($_POST['name']) || empty($_POST['discounted_price'])) {
            echo json_encode(['success' => false, 'message' => 'Package ID, name and discounted price are required']);
            exit;
        }
        
        $packageId = intval($_POST['package_id']);
        
        // Check if at least one pet type is selected
        $forDogs = isset($_POST['for_dogs']) && $_POST['for_dogs'] === 'true';
        $forCats = isset($_POST['for_cats']) && $_POST['for_cats'] === 'true';
        
        if (!$forDogs && !$forCats) {
            echo json_encode(['success' => false, 'message' => 'Please select at least one pet type']);
            exit;
        }
        
        // Parse service IDs
        $serviceIds = parseServiceIds($_POST['service_ids'] ?? '');
        
        if (empty($serviceIds)) {
            echo json_encode(['success' => false, 'message' => 'Please select at least one service']);
            exit;
        }
        
        $result = $model->updatePackage($packageId, [
            'user_id' => $userId,
            'name' => trim($_POST['name']),
            'original_price' => floatval($_POST['original_price']),
            'discounted_price' => floatval($_POST['discounted_price']),
            'duration' => trim($_POST['duration'] ?? ''), // '?? Medium'
            'for_cats' => $forCats,
            'for_dogs' => $forDogs,
            'description' => trim($_POST['description'] ?? ''),
            'included_services' => trim($_POST['included_services'] ?? ''),
            'service_ids' => $serviceIds
        ]);
        
        echo json_encode($result);
        break;

    case 'delete':
        // Route to delete package permanently
        if (empty($_POST['package_id'])) {
            echo json_encode(['success' => false, 'message' => 'Package ID is required']);
            exit;
        }
        
        $packageId = intval($_POST['package_id']);
        $result = $model->deletePackage($packageId, $userId);
        
        echo json_encode($result);
        break;

    case 'toggle_availability':
        // Route to toggle package availability status
        if (empty($_POST['package_id'])) {
            echo json_encode(['success' => false, 'message' => 'Package ID is required']);
            exit;
        }
        
        $packageId = intval($_POST['package_id']);
        $result = $model->toggleAvailability($packageId, $userId);
        
        echo json_encode($result);
        break;

    case 'get':
        // Route to retrieve single package by ID
        if (empty($_POST['package_id'])) {
            echo json_encode(['success' => false, 'message' => 'Package ID is required']);
            exit;
        }
        
        $packageId = intval($_POST['package_id']);
        $package = $model->getPackageById($packageId, $userId);
        
        if ($package) {
            echo json_encode(['success' => true, 'package' => $package]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Package not found']);
        }
        break;

    case 'list':
        // Route to retrieve all packages for logged-in groomer
        $packages = $model->getAllPackages($userId);
        echo json_encode(['success' => true, 'packages' => $packages]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
