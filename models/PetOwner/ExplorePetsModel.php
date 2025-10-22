<?php
require_once __DIR__ . '/../BaseModel.php';

class ExplorePetsModel extends BaseModel {
    
    public function getAllSellers() {
        global $conn;
        
        // Fetch sellers (users) who have approved listings
        $sql = "SELECT DISTINCT 
                    u.id, 
                    CONCAT(u.first_name, ' ', u.last_name) as name,
                    l.location,
                    l.phone,
                    l.phone2,
                    l.email
                FROM users u
                INNER JOIN sell_pet_listings l ON u.id = l.user_id
                WHERE l.status = 'approved'";
        
        $result = mysqli_query($conn, $sql);
        
        if (!$result) {
            error_log("ExplorePetsModel getAllSellers error: " . mysqli_error($conn));
            return [];
        }
        
        $sellers = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $sellers[$row['id']] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'location' => $row['location'] ?? '',
                'phone' => $row['phone'] ?? '',
                'phone2' => $row['phone2'] ?? '',
                'email' => $row['email'] ?? ''
            ];
        }
        
        return $sellers;
    }
    
    public function getAllPets() {
        global $conn;
        
        // Fetch only approved listings from database
        $sql = "SELECT 
                    l.id, 
                    l.name, 
                    l.species, 
                    l.breed, 
                    l.age, 
                    l.gender, 
                    l.price, 
                    l.description as `desc`, 
                    l.location,
                    l.user_id as seller_id,
                    l.created_at as date_posted,
                    CONCAT(u.first_name, ' ', u.last_name) as seller_name,
                    u.email as seller_email
                FROM sell_pet_listings l
                LEFT JOIN users u ON l.user_id = u.id
                WHERE l.status = 'approved'
                ORDER BY l.created_at DESC";
        
        $result = mysqli_query($conn, $sql);
        
        if (!$result) {
            error_log("ExplorePetsModel getAllPets error: " . mysqli_error($conn));
            return [];
        }
        
        $pets = [];
        while ($row = mysqli_fetch_assoc($result)) {
            // Get images for this listing
            $imgSql = "SELECT image_url FROM sell_pet_listing_images 
                       WHERE listing_id = ? ORDER BY display_order ASC";
            $imgStmt = mysqli_prepare($conn, $imgSql);
            mysqli_stmt_bind_param($imgStmt, 'i', $row['id']);
            mysqli_stmt_execute($imgStmt);
            $imgResult = mysqli_stmt_get_result($imgStmt);
            
            $images = [];
            while ($imgRow = mysqli_fetch_assoc($imgResult)) {
                $images[] = $imgRow['image_url'];
            }
            
            // Get badges for this listing
            $badgeSql = "SELECT badge FROM sell_pet_listing_badges 
                         WHERE listing_id = ?";
            $badgeStmt = mysqli_prepare($conn, $badgeSql);
            mysqli_stmt_bind_param($badgeStmt, 'i', $row['id']);
            mysqli_stmt_execute($badgeStmt);
            $badgeResult = mysqli_stmt_get_result($badgeStmt);
            
            $badges = [];
            while ($badgeRow = mysqli_fetch_assoc($badgeResult)) {
                $badges[] = $badgeRow['badge'];
            }
            
            // Format data to match the expected structure
            $pets[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'species' => $row['species'],
                'breed' => $row['breed'],
                'age' => $row['age'] . 'y',
                'gender' => $row['gender'],
                'badges' => $badges,
                'price' => $row['price'],
                'desc' => $row['desc'] ?? '',
                'images' => $images,
                'seller_id' => $row['seller_id'],
                'date_posted' => $row['date_posted']
            ];
        }
        
        return $pets;
    }
    
    public function getPetsByUserId($userId) {
        $pets = $this->getAllPets();
        return array_values(array_filter($pets, fn($p) => $p['seller_id'] === $userId));
    }
    
    public function searchPets($query = null, $species = null, $minPrice = null, $maxPrice = null, $sortBy = 'newest') {
        $pets = $this->getAllPets();
        
        // Filter by species if specified
        if ($species && $species !== 'all') {
            $pets = array_filter($pets, fn($p) => 
                strcasecmp($p['species'], $species) === 0
            );
        }
        
        // Filter by price range
        if ($minPrice !== null) {
            $pets = array_filter($pets, fn($p) => $p['price'] >= $minPrice);
        }
        if ($maxPrice !== null) {
            $pets = array_filter($pets, fn($p) => $p['price'] <= $maxPrice);
        }
        
        // Search by query if specified
        if ($query) {
            $query = strtolower($query);
            $pets = array_filter($pets, function($p) use ($query) {
                return (
                    stripos($p['name'], $query) !== false ||
                    stripos($p['breed'], $query) !== false ||
                    stripos($p['desc'], $query) !== false ||
                    stripos($p['species'], $query) !== false
                );
            });
        }
        
        // Sort results
        switch ($sortBy) {
            case 'price_low':
                usort($pets, fn($a, $b) => $a['price'] <=> $b['price']);
                break;
            case 'price_high':
                usort($pets, fn($a, $b) => $b['price'] <=> $a['price']);
                break;
            case 'oldest':
                usort($pets, fn($a, $b) => strtotime($a['date_posted']) <=> strtotime($b['date_posted']));
                break;
            case 'newest':
            default:
                usort($pets, fn($a, $b) => strtotime($b['date_posted']) <=> strtotime($a['date_posted']));
                break;
        }
        
        return array_values($pets);
    }
    
    public function getPetById($petId) {
        $pets = $this->getAllPets();
        foreach ($pets as $pet) {
            if ($pet['id'] === $petId) {
                return $pet;
            }
        }
        return null;
    }
    
    public function getAvailableSpecies() {
        $pets = $this->getAllPets();
        $species = array_unique(array_column($pets, 'species'));
        sort($species);
        return $species;
    }
}
?>