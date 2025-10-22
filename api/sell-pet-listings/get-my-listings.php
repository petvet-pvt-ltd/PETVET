<?php
session_start();
require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../models/SellPetListingModel.php';

// Prevent any output before JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);
ob_start();

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $model = new SellPetListingModel();
    $userId = $_SESSION['user_id'];
    
    // Get user's listings
    $listings = $model->getUserListings($userId);
    
    // Attach images and badges to each listing
    foreach ($listings as &$listing) {
        $listing['images'] = $model->getImages($listing['id']);
        $listing['badges'] = $model->getBadges($listing['id']);
    }
    
    ob_clean();
    echo json_encode([
        'success' => true,
        'listings' => $listings
    ]);
    
} catch (Exception $e) {
    error_log("Error fetching listings: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    ob_clean();
    echo json_encode([
        'success' => false, 
        'message' => 'Failed to fetch listings',
        'error' => $e->getMessage()
    ]);
}
?>