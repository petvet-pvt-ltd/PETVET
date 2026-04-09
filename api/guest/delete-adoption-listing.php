<?php
session_start();
require_once __DIR__ . '/../../config/connect.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        exit;
    }

    $listingId = $_POST['listing_id'] ?? null;
    $email = $_POST['email'] ?? null;
    $userId = $_SESSION['user_id'] ?? null;

    if (!$listingId) {
        echo json_encode(['success' => false, 'message' => 'Listing ID is required']);
        exit;
    }

    // Get the listing first
    $getQuery = "SELECT id, user_id, email FROM sell_pet_listings WHERE id = ? AND listing_type = 'adoption'";
    $getStmt = mysqli_prepare($conn, $getQuery);
    if (!$getStmt) {
        throw new Exception("Database error: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($getStmt, "i", $listingId);
    mysqli_stmt_execute($getStmt);
    $getResult = mysqli_stmt_get_result($getStmt);
    $listing = mysqli_fetch_assoc($getResult);

    if (!$listing) {
        echo json_encode(['success' => false, 'message' => 'Listing not found']);
        exit;
    }

    // Verify ownership
    // Either the user_id matches (logged in user) OR email matches (guest user)
    if ($userId && $listing['user_id'] === (int)$userId) {
        // Logged-in user
        $authorized = true;
    } elseif (!$userId && $email && $listing['email'] === $email && $listing['user_id'] === null) {
        // Guest user with matching email
        $authorized = true;
    } else {
        echo json_encode(['success' => false, 'message' => 'You do not have permission to delete this listing']);
        exit;
    }

    // Delete images first
    $deleteImagesQuery = "DELETE FROM sell_pet_listing_images WHERE listing_id = ?";
    $deleteImagesStmt = mysqli_prepare($conn, $deleteImagesQuery);
    if ($deleteImagesStmt) {
        mysqli_stmt_bind_param($deleteImagesStmt, "i", $listingId);
        mysqli_stmt_execute($deleteImagesStmt);
        mysqli_stmt_close($deleteImagesStmt);
    }

    // Delete badges
    $deleteBadgesQuery = "DELETE FROM sell_pet_listing_badges WHERE listing_id = ?";
    $deleteBadgesStmt = mysqli_prepare($conn, $deleteBadgesQuery);
    if ($deleteBadgesStmt) {
        mysqli_stmt_bind_param($deleteBadgesStmt, "i", $listingId);
        mysqli_stmt_execute($deleteBadgesStmt);
        mysqli_stmt_close($deleteBadgesStmt);
    }

    // Delete the listing
    $deleteListingQuery = "DELETE FROM sell_pet_listings WHERE id = ?";
    $deleteListingStmt = mysqli_prepare($conn, $deleteListingQuery);
    if (!$deleteListingStmt) {
        throw new Exception("Database error: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($deleteListingStmt, "i", $listingId);
    if (!mysqli_stmt_execute($deleteListingStmt)) {
        throw new Exception("Failed to delete listing");
    }

    echo json_encode([
        'success' => true,
        'message' => 'Listing deleted successfully'
    ]);

} catch (Exception $e) {
    error_log("Error deleting listing: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to delete listing',
        'error' => $e->getMessage()
    ]);
}
?>
