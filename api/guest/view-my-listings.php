<?php
session_start();
require_once __DIR__ . '/../../config/connect.php';

header('Content-Type: application/json');

try {
    // Check if user provided email for guest submissions or if they're logged in
    $email = $_POST['email'] ?? null;
    $userId = $_SESSION['user_id'] ?? null;

    if (!$email && !$userId) {
        echo json_encode(['success' => false, 'message' => 'Please provide an email address or log in to view your listings']);
        exit;
    }

    // Get listings - either by user_id (logged in) or by email (guest)
    if ($userId) {
        $query = "SELECT * FROM sell_pet_listings 
                  WHERE user_id = ? AND listing_type = 'adoption' 
                  ORDER BY created_at DESC";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $userId);
    } else {
        $query = "SELECT * FROM sell_pet_listings 
                  WHERE email = ? AND listing_type = 'adoption' 
                  ORDER BY created_at DESC";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
    }

    if (!$stmt) {
        throw new Exception("Database error: " . mysqli_error($conn));
    }

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
    error_log("Error fetching listings: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch your listings',
        'error' => $e->getMessage()
    ]);
}
?>
