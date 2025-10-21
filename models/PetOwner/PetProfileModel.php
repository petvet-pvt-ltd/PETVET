<?php
require_once __DIR__ . '/../BaseModel.php';
require_once __DIR__ . '/../../config/connect.php';

class PetProfileModel extends BaseModel {
    
    private $db;
    
    public function __construct() {
        parent::__construct();
        $this->db = db();
    }
    
    /**
     * Get all pets for a specific user (only active pets by default)
     */
    public function getUserPets(int $userId, bool $includeInactive = false): array {
        try {
            $sql = "
                SELECT id, user_id, name, species, breed, sex, 
                       date_of_birth, weight, color, allergies, notes, 
                       photo_url, is_active, created_at, updated_at
                FROM pets
                WHERE user_id = ?
            ";
            
            if (!$includeInactive) {
                $sql .= " AND is_active = TRUE";
            }
            
            $sql .= " ORDER BY created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get user pets error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get pet by ID (with user verification)
     */
    public function getPetById(int $id, int $userId = null): ?array {
        try {
            $sql = "
                SELECT id, user_id, name, species, breed, sex, 
                       date_of_birth, weight, color, allergies, notes, 
                       photo_url, is_active, created_at, updated_at
                FROM pets 
                WHERE id = ?
            ";
            
            $params = [$id];
            
            // Optional: verify ownership
            if ($userId !== null) {
                $sql .= " AND user_id = ?";
                $params[] = $userId;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $pet = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $pet ?: null;
        } catch (PDOException $e) {
            error_log("Get pet by ID error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create a new pet profile
     */
    public function createPet(array $data): bool {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO pets 
                (user_id, name, species, breed, sex, date_of_birth, weight, 
                 color, allergies, notes, photo_url, is_active)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            return $stmt->execute([
                $data['user_id'],
                $data['name'],
                $data['species'],
                $data['breed'] ?? null,
                $data['sex'] ?? null,
                $data['date_of_birth'] ?? null,
                $data['weight'] ?? null,
                $data['color'] ?? null,
                $data['allergies'] ?? null,
                $data['notes'] ?? null,
                $data['photo_url'] ?? null,
                $data['is_active'] ?? true
            ]);
        } catch (PDOException $e) {
            error_log("Create pet error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update an existing pet profile
     */
    public function updatePet(int $id, array $data, int $userId = null): bool {
        try {
            $sql = "
                UPDATE pets 
                SET name = ?, 
                    species = ?, 
                    breed = ?, 
                    sex = ?, 
                    date_of_birth = ?, 
                    weight = ?, 
                    color = ?,
                    allergies = ?,
                    notes = ?,
                    photo_url = ?,
                    is_active = ?,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ";
            
            $params = [
                $data['name'],
                $data['species'],
                $data['breed'] ?? null,
                $data['sex'] ?? null,
                $data['date_of_birth'] ?? null,
                $data['weight'] ?? null,
                $data['color'] ?? null,
                $data['allergies'] ?? null,
                $data['notes'] ?? null,
                $data['photo_url'] ?? null,
                $data['is_active'] ?? true,
                $id
            ];
            
            // Optional: verify ownership
            if ($userId !== null) {
                $sql .= " AND user_id = ?";
                $params[] = $userId;
            }
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Update pet error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Soft delete a pet (set is_active = false)
     */
    public function deletePet(int $id, int $userId = null): bool {
        try {
            $sql = "
                UPDATE pets 
                SET is_active = FALSE, updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ";
            
            $params = [$id];
            
            // Optional: verify ownership
            if ($userId !== null) {
                $sql .= " AND user_id = ?";
                $params[] = $userId;
            }
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Delete pet error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Permanently delete a pet from database
     */
    public function permanentlyDeletePet(int $id, int $userId = null): bool {
        try {
            $sql = "DELETE FROM pets WHERE id = ?";
            $params = [$id];
            
            // Optional: verify ownership
            if ($userId !== null) {
                $sql .= " AND user_id = ?";
                $params[] = $userId;
            }
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Permanent delete pet error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Restore a soft-deleted pet
     */
    public function restorePet(int $id, int $userId = null): bool {
        try {
            $sql = "
                UPDATE pets 
                SET is_active = TRUE, updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ";
            
            $params = [$id];
            
            // Optional: verify ownership
            if ($userId !== null) {
                $sql .= " AND user_id = ?";
                $params[] = $userId;
            }
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Restore pet error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get last inserted pet ID
     */
    public function getLastInsertId(): int {
        return (int)$this->db->lastInsertId();
    }
    
    /**
     * Get pet species options
     */
    public function getSpeciesOptions(): array {
        return [
            'Dog',
            'Cat',
            'Bird',
            'Rabbit',
            'Hamster',
            'Guinea Pig',
            'Fish',
            'Turtle',
            'Other'
        ];
    }
    
    /**
     * Get sex options
     */
    public function getSexOptions(): array {
        return [
            'Male',
            'Female',
            'Unknown'
        ];
    }
}
?>
