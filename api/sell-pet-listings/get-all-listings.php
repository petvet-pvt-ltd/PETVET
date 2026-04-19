<?php
session_start();
require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../models/SellPetListingModel.php';

// Prevent any output before JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);
ob_start();

header('Content-Type: application/json');

// Check if user is admin
$userRole = $_SESSION['current_role'] ?? $_SESSION['role'] ?? null;
if (!isset($_SESSION['user_id']) || $userRole !== 'admin') {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Unauthorized access', 'role' => $userRole, 'user_id' => $_SESSION['user_id'] ?? null]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    global $conn;
    
    if (!$conn) {
        throw new Exception("Database connection failed");
    }
    
    $model = new SellPetListingModel();
    
    // Get all listings with price between 500 and 5000
    $query = "SELECT l.id, l.user_id, l.name, l.species, l.breed, l.age, l.gender, CAST(l.weight AS DECIMAL(10,2)) as weight, 
              CAST(l.height AS DECIMAL(10,2)) as height,
              l.price, l.listing_type, l.location, l.description, l.phone, l.phone2, l.email,
              l.latitude, l.longitude, l.status, l.created_at, l.updated_at,
              CONCAT(u.first_name, ' ', u.last_name) as username,
              u.email as user_email 
              FROM sell_pet_listings l 
              LEFT JOIN users u ON l.user_id = u.id 
              WHERE l.price BETWEEN 500 AND 500000
              ORDER BY l.created_at DESC";
    
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        throw new Exception("Query failed: " . mysqli_error($conn));
    }
    
    $listings = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
    // Attach images and badges to each listing
    foreach ($listings as &$listing) {
        $images = $model->getImages($listing['id']);
        $badges = $model->getBadges($listing['id']);
        
        // Convert weight to float for proper JSON serialization
        if ($listing['weight'] !== null) {
            $listing['weight'] = floatval($listing['weight']);
        }
        
        // Convert height to float for proper JSON serialization
        if ($listing['height'] !== null) {
            $listing['height'] = floatval($listing['height']);
        }
        
        // Format images as objects with image_url property
        $listing['images'] = array_map(function($url) {
            return ['image_url' => $url];
        }, $images);
        
        // Format badges as objects with badge property
        $listing['badges'] = array_map(function($badge) {
            return ['badge' => $badge];
        }, $badges);
    }
    
    ob_clean();
    echo json_encode([
        'success' => true,
        'data' => $listings,
        'count' => count($listings)
    ]);
    
} catch (Exception $e) {
    error_log("Error fetching admin listings: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    ob_clean();
    echo json_encode([
        'success' => false, 
        'message' => 'Failed to fetch listings',
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>
