<?php
require_once __DIR__ . '/../config/connect.php';

class SellPetListingModel {
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    // Get all approved listings
    public function getAllListings() {
        $query = "SELECT * FROM sell_pet_listings WHERE status = 'approved' ORDER BY created_at DESC";
        $result = mysqli_query($this->conn, $query);
        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    }
    
    // Get listings by user ID
    public function getUserListings($userId) {
        $query = "SELECT * FROM sell_pet_listings WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    }
    
    // Get single listing by ID
    public function getListingById($id) {
        $query = "SELECT * FROM sell_pet_listings WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return $result ? mysqli_fetch_assoc($result) : null;
    }
    
    // Create new listing
    public function createListing($data) {
        // Handle NULL values for latitude/longitude
        $lat = $data['latitude'];
        $lng = $data['longitude'];
        
        $query = "INSERT INTO sell_pet_listings (
            user_id, name, species, breed, age, gender, price, location, 
            description, phone, phone2, email, latitude, longitude, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
        
        $stmt = mysqli_prepare($this->conn, $query);
        
        if (!$stmt) {
            error_log("Prepare failed: " . mysqli_error($this->conn));
            return false;
        }
        
        mysqli_stmt_bind_param(
            $stmt, 
            "isssssdsssssdd",
            $data['user_id'],
            $data['name'],
            $data['species'],
            $data['breed'],
            $data['age'],
            $data['gender'],
            $data['price'],
            $data['location'],
            $data['description'],
            $data['phone'],
            $data['phone2'],
            $data['email'],
            $lat,
            $lng
        );
        
        $success = mysqli_stmt_execute($stmt);
        
        if (!$success) {
            error_log("Execute failed: " . mysqli_stmt_error($stmt));
            return false;
        }
        
        return mysqli_insert_id($this->conn);
    }
    
    // Update listing
    public function updateListing($id, $data) {
        $query = "UPDATE sell_pet_listings SET 
            name = ?, species = ?, breed = ?, age = ?, gender = ?, 
            price = ?, location = ?, description = ?, phone = ?, phone2 = ?, email = ?
            WHERE id = ?";
        
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param(
            $stmt,
            "sssssdsssssi",
            $data['name'],
            $data['species'],
            $data['breed'],
            $data['age'],
            $data['gender'],
            $data['price'],
            $data['location'],
            $data['description'],
            $data['phone'],
            $data['phone2'],
            $data['email'],
            $id
        );
        
        return mysqli_stmt_execute($stmt);
    }
    
    // Delete listing
    public function deleteListing($id) {
        $query = "DELETE FROM sell_pet_listings WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        return mysqli_stmt_execute($stmt);
    }
    
    // Update listing status
    public function updateStatus($id, $status) {
        $query = "UPDATE sell_pet_listings SET status = ? WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "si", $status, $id);
        return mysqli_stmt_execute($stmt);
    }
    
    // Image management
    public function addImage($listingId, $imageUrl, $displayOrder = 0) {
        $query = "INSERT INTO sell_pet_listing_images (listing_id, image_url, display_order) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "isi", $listingId, $imageUrl, $displayOrder);
        return mysqli_stmt_execute($stmt);
    }
    
    public function getImages($listingId) {
        $query = "SELECT image_url FROM sell_pet_listing_images WHERE listing_id = ? ORDER BY display_order ASC";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $listingId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $images = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $images[] = $row['image_url'];
        }
        return $images;
    }
    
    public function deleteImages($listingId) {
        $query = "DELETE FROM sell_pet_listing_images WHERE listing_id = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $listingId);
        return mysqli_stmt_execute($stmt);
    }
    
    // Badge management
    public function addBadge($listingId, $badge) {
        $query = "INSERT INTO sell_pet_listing_badges (listing_id, badge) VALUES (?, ?)";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "is", $listingId, $badge);
        return mysqli_stmt_execute($stmt);
    }
    
    public function getBadges($listingId) {
        $query = "SELECT badge FROM sell_pet_listing_badges WHERE listing_id = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $listingId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $badges = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $badges[] = $row['badge'];
        }
        return $badges;
    }
    
    public function deleteBadges($listingId) {
        $query = "DELETE FROM sell_pet_listing_badges WHERE listing_id = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $listingId);
        return mysqli_stmt_execute($stmt);
    }
}
?>
