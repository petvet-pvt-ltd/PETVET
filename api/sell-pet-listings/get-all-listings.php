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
    
    // Get all listings (not just approved ones)
    $query = "SELECT l.*, 
              CONCAT(u.first_name, ' ', u.last_name) as username,
              u.email as user_email 
              FROM sell_pet_listings l 
              LEFT JOIN users u ON l.user_id = u.id 
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
