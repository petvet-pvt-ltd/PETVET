<?php
session_start();
require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../models/SellPetListingModel.php';

header('Content-Type: application/json');

// Check if user is admin
$userRole = $_SESSION['current_role'] ?? $_SESSION['role'] ?? null;
if (!isset($_SESSION['user_id']) || $userRole !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $model = new SellPetListingModel();
    
    $listingId = intval($_POST['id'] ?? 0);
    if ($listingId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid listing ID']);
        exit;
    }
    
    // Get images to delete files from server
    $images = $model->getImages($listingId);
    foreach ($images as $imageUrl) {
        $filepath = __DIR__ . '/../../public' . str_replace('/PETVET/public', '', $imageUrl);
        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }
    
    // Delete listing (this will also delete images and badges via CASCADE)
    if ($model->deleteListing($listingId)) {
        echo json_encode([
            'success' => true,
            'message' => 'Listing declined and removed successfully'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to decline listing']);
    }
    
} catch (Exception $e) {
    error_log("Error declining pet listing: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'An error occurred while declining the listing',
        'error' => $e->getMessage()
    ]);
}
?>
