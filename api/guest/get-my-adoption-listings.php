<?php
session_start();
require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../models/SellPetListingModel.php';

header('Content-Type: application/json');

try {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Please log in to view your listings']);
        exit;
    }

    $model = new SellPetListingModel();
    $userId = $_SESSION['user_id'];

    // Get all adoption listings for this user (pending, approved, rejected)
    $query = "SELECT * FROM sell_pet_listings 
              WHERE user_id = ? AND listing_type = 'adoption' 
              ORDER BY created_at DESC";
    
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        throw new Exception("Database error: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $listings = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Fetch images and badges for each listing
    foreach ($listings as &$listing) {
        $listingId = $listing['id'];
        
        // Get images
        $imgQuery = "SELECT image_url FROM sell_pet_listing_images WHERE listing_id = ? ORDER BY display_order ASC";
        $imgStmt = mysqli_prepare($conn, $imgQuery);
        mysqli_stmt_bind_param($imgStmt, "i", $listingId);
        mysqli_stmt_execute($imgStmt);
        $imgResult = mysqli_stmt_get_result($imgStmt);
        $listing['images'] = mysqli_fetch_all($imgResult, MYSQLI_ASSOC);
        $listing['images'] = array_map(fn($img) => $img['image_url'], $listing['images']);

        // Get badges
        $badgeQuery = "SELECT badge FROM sell_pet_listing_badges WHERE listing_id = ?";
        $badgeStmt = mysqli_prepare($conn, $badgeQuery);
        mysqli_stmt_bind_param($badgeStmt, "i", $listingId);
        mysqli_stmt_execute($badgeStmt);
        $badgeResult = mysqli_stmt_get_result($badgeStmt);
        $listing['badges'] = mysqli_fetch_all($badgeResult, MYSQLI_ASSOC);
        $listing['badges'] = array_map(fn($badge) => $badge['badge'], $listing['badges']);
    }

    echo json_encode([
        'success' => true,
        'listings' => $listings
    ]);

} catch (Exception $e) {
    error_log("Error fetching user listings: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch your listings',
        'error' => $e->getMessage()
    ]);
}
?>
