<?php
require_once __DIR__ . '/BaseModel.php';

class SellPetListingModel extends BaseModel {
    
    // Get all approved listings
    public function getAllListings() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM sell_pet_listings 
                WHERE status = 'approved' 
                ORDER BY created_at DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get all listings error: " . $e->getMessage());
            return [];
        }
    }
    
    // Get listings by user ID
    public function getUserListings($userId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM sell_pet_listings 
                WHERE user_id = ? 
                ORDER BY created_at DESC
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get user listings error: " . $e->getMessage());
            return [];
        }
    }
    
    // Get single listing by ID
    public function getListingById($id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM sell_pet_listings 
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get listing by ID error: " . $e->getMessage());
            return null;
        }
    }
    
    // Create new listing
    public function createListing($data) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO sell_pet_listings (
                    user_id, name, species, breed, age, gender, weight, price, listing_type, 
                    location, description, phone, phone2, email, latitude, longitude, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
            ");
            
            $success = $stmt->execute([
                $data['user_id'],
                $data['name'],
                $data['species'],
                $data['breed'],
                $data['age'],
                $data['gender'],
                $data['weight'] ?? null,
                $data['price'],
                $data['listing_type'],
                $data['location'],
                $data['description'],
                $data['phone'],
                $data['phone2'] ?? '',
                $data['email'] ?? '',
                $data['latitude'] ?? null,
                $data['longitude'] ?? null
            ]);
            
            if ($success) {
                return $this->pdo->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Create listing error: " . $e->getMessage());
            return false;
        }
    }
    
    // Update listing
    public function updateListing($id, $data) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE sell_pet_listings SET 
                    name = ?, species = ?, breed = ?, age = ?, gender = ?, weight = ?,
                    price = ?, listing_type = ?, location = ?, description = ?, 
                    phone = ?, phone2 = ?, email = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ");
            
            return $stmt->execute([
                $data['name'],
                $data['species'],
                $data['breed'],
                $data['age'],
                $data['gender'],
                $data['weight'] ?? null,
                $data['price'],
                $data['listing_type'],
                $data['location'],
                $data['description'],
                $data['phone'],
                $data['phone2'] ?? '',
                $data['email'] ?? '',
                $id
            ]);
        } catch (PDOException $e) {
            error_log("Update listing error: " . $e->getMessage());
            return false;
        }
    }
    
    // Delete listing
    public function deleteListing($id) {
        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM sell_pet_listings WHERE id = ?
            ");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Delete listing error: " . $e->getMessage());
            return false;
        }
    }
    
    // Update listing status
    public function updateStatus($id, $status) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE sell_pet_listings 
                SET status = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?
            ");
            return $stmt->execute([$status, $id]);
        } catch (PDOException $e) {
            error_log("Update status error: " . $e->getMessage());
            return false;
        }
    }
    
    // Image management
    public function addImage($listingId, $imageUrl, $displayOrder = 0) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO sell_pet_listing_images (listing_id, image_url, display_order) 
                VALUES (?, ?, ?)
            ");
            return $stmt->execute([$listingId, $imageUrl, $displayOrder]);
        } catch (PDOException $e) {
            error_log("Add image error: " . $e->getMessage());
            return false;
        }
    }
    
    public function getImages($listingId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT image_url FROM sell_pet_listing_images 
                WHERE listing_id = ? 
                ORDER BY display_order ASC
            ");
            $stmt->execute([$listingId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map(function($row) { return $row['image_url']; }, $rows);
        } catch (PDOException $e) {
            error_log("Get images error: " . $e->getMessage());
            return [];
        }
    }
    
    public function deleteImages($listingId) {
        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM sell_pet_listing_images WHERE listing_id = ?
            ");
            return $stmt->execute([$listingId]);
        } catch (PDOException $e) {
            error_log("Delete images error: " . $e->getMessage());
            return false;
        }
    }
    
    // Badge management
    public function addBadge($listingId, $badge) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO sell_pet_listing_badges (listing_id, badge) 
                VALUES (?, ?)
            ");
            return $stmt->execute([$listingId, $badge]);
        } catch (PDOException $e) {
            error_log("Add badge error: " . $e->getMessage());
            return false;
        }
    }
    
    public function getBadges($listingId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT badge FROM sell_pet_listing_badges 
                WHERE listing_id = ? 
                ORDER BY id ASC
            ");
            $stmt->execute([$listingId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map(function($row) { return $row['badge']; }, $rows);
        } catch (PDOException $e) {
            error_log("Get badges error: " . $e->getMessage());
            return [];
        }
    }
    
    public function deleteBadges($listingId) {
        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM sell_pet_listing_badges WHERE listing_id = ?
            ");
            return $stmt->execute([$listingId]);
        } catch (PDOException $e) {
            error_log("Delete badges error: " . $e->getMessage());
            return false;
        }
    }
}
?>
