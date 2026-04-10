<?php
require_once __DIR__ . '/../../config/connect.php';

class GuestAdoptModel {
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    /**
     * Get all approved adoption pets grouped by species
     * @return array Array with keys: dogs, cats, birds, other
     */
    public function getAdoptionPetsBySpecies() {
        $query = "
            SELECT 
                l.id,
                l.name,
                l.species,
                l.breed,
                l.age,
                l.gender,
                l.price,
                l.listing_type,
                l.description as `desc`,
                l.location,
                l.latitude,
                l.longitude,
                l.user_id as seller_id,
                l.phone,
                l.phone2,
                l.email,
                l.created_at,
                CONCAT(u.first_name, ' ', u.last_name) as owner_name,
                u.phone as owner_phone,
                u.email as owner_email
            FROM sell_pet_listings l
            LEFT JOIN users u ON l.user_id = u.id
            WHERE l.listing_type = 'adoption' 
            AND l.status = 'approved'
            ORDER BY l.created_at DESC
        ";
        
        $result = mysqli_query($this->conn, $query);
        
        if (!$result) {
            error_log("Error fetching adoption pets: " . mysqli_error($this->conn));
            return ['dogs' => [], 'cats' => [], 'birds' => [], 'other' => []];
        }
        
        $pets = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
        // Get images and badges for each pet
        foreach ($pets as &$pet) {
            $pet['images'] = $this->getPetImages($pet['id']);
            $pet['badges'] = $this->getPetBadges($pet['id']);
        }
        
        // Group by species
        $grouped = [
            'dogs' => [],
            'cats' => [],
            'birds' => [],
            'other' => []
        ];
        
        foreach ($pets as $pet) {
            $species = strtolower($pet['species']);
            
            if ($species === 'dog' || $species === 'dogs') {
                $grouped['dogs'][] = $pet;
            } elseif ($species === 'cat' || $species === 'cats') {
                $grouped['cats'][] = $pet;
            } elseif ($species === 'bird' || $species === 'birds') {
                $grouped['birds'][] = $pet;
            } else {
                $grouped['other'][] = $pet;
            }
        }
        
        return $grouped;
    }
    
    /**
     * Get images for a specific pet listing
     * @param int $listingId
     * @return array Array of image URLs
     */
    private function getPetImages($listingId) {
        $query = "SELECT image_url FROM sell_pet_listing_images WHERE listing_id = ? ORDER BY id ASC";
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
    
    /**
     * Get badges for a specific pet listing
     * @param int $listingId
     * @return array Array of badge names
     */
    private function getPetBadges($listingId) {
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
    
    /**
     * Get a single pet listing by ID
     * @param int $id
     * @return array|null
     */
    public function getPetById($id) {
        $query = "
            SELECT 
                l.id,
                l.name,
                l.species,
                l.breed,
                l.age,
                l.gender,
                l.price,
                l.listing_type,
                l.description as `desc`,
                l.location,
                l.latitude,
                l.longitude,
                l.user_id as seller_id,
                l.phone,
                l.phone2,
                l.email,
                l.created_at,
                CONCAT(u.first_name, ' ', u.last_name) as owner_name,
                u.phone as owner_phone,
                u.email as owner_email
            FROM sell_pet_listings l
            LEFT JOIN users u ON l.user_id = u.id
            WHERE l.id = ? 
            AND l.listing_type = 'adoption'
            AND l.status = 'approved'
        ";
        
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $pet = mysqli_fetch_assoc($result);
        
        if ($pet) {
            $pet['images'] = $this->getPetImages($pet['id']);
            $pet['badges'] = $this->getPetBadges($pet['id']);
        }
        
        return $pet;
    }
}
?>
