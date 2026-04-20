<?php
require_once __DIR__ . '/../BaseModel.php';

class BreederPetsModel extends BaseModel {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getAllPets($breederId) {
        // This page previously used prototype/dummy data.
        // Until a real “pets for sale” table is wired in, return empty.
        return [];
    }

    public function getAvailablePets($breederId) {
        return [];
    }

    public function getBreeds($breederId) {
        return [];
    }

    public function getPetStats($breederId) {
        return [
            'total' => 0,
            'available' => 0,
            'reserved' => 0,
            'sold' => 0
        ];
    }

    /**
     * Get all breeding pets for a breeder
     */
    public function getBreedingPets($breederId) {
        $stmt = $this->db->prepare("
            SELECT id, name, breed, date_of_birth as dob, species,
                   photo, description, reward, is_active, age,
                   TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) as age_years
            FROM breeder_pets
            WHERE breeder_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$breederId]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $pets = [];
        foreach ($result as $row) {
            $row['is_active'] = (bool)$row['is_active'];
            $row['age'] = $row['age_years'] . ' ' . ($row['age_years'] == 1 ? 'year' : 'years');
            unset($row['age_years']);
            $pets[] = $row;
        }
        
        return $pets;
    }

    /**
     * Add a new breeding pet
     */
    public function addBreedingPet($breederId, $data) {
        $stmt = $this->db->prepare("
            INSERT INTO breeder_pets (breeder_id, name, breed, gender, date_of_birth, age, species, photo, description, reward, is_active)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $breederId,
            $data['name'],
            $data['breed'],
            $data['gender'],
            $data['dob'],
            $data['age'],
            $data['species'],
            $data['photo'],
            $data['description'],
            $data['reward'],
            $data['is_active']
        ]);
        
        if ($result) {
            return ['success' => true, 'pet_id' => $this->db->lastInsertId()];
        } else {
            return ['success' => false, 'error' => $stmt->errorInfo()[2]];
        }
    }

    /**
     * Update breeding pet with photo
     */
    public function updateBreedingPetWithPhoto($petId, $breederId, $data) {
        $stmt = $this->db->prepare("
            UPDATE breeder_pets 
            SET name = ?, breed = ?, gender = ?, date_of_birth = ?, age = ?, species = ?, photo = ?, description = ?, reward = ?, is_active = ?
            WHERE id = ? AND breeder_id = ?
        ");
        
        $result = $stmt->execute([
            $data['name'],
            $data['breed'],
            $data['gender'],
            $data['dob'],
            $data['age'],
            $data['species'],
            $data['photo'],
            $data['description'],
            $data['reward'],
            $data['is_active'],
            $petId,
            $breederId
        ]);
        
        if ($result && $stmt->rowCount() > 0) {
            return ['success' => true];
        } else {
            return ['success' => false, 'error' => $stmt->errorInfo()[2]];
        }
    }

    /**
     * Update breeding pet without photo
     */
    public function updateBreedingPet($petId, $breederId, $data) {
        $stmt = $this->db->prepare("
            UPDATE breeder_pets 
            SET name = ?, breed = ?, gender = ?, date_of_birth = ?, age = ?, species = ?, description = ?, reward = ?, is_active = ?
            WHERE id = ? AND breeder_id = ?
        ");
        
        $result = $stmt->execute([
            $data['name'],
            $data['breed'],
            $data['gender'],
            $data['dob'],
            $data['age'],
            $data['species'],
            $data['description'],
            $data['reward'],
            $data['is_active'],
            $petId,
            $breederId
        ]);
        
        if ($result && $stmt->rowCount() > 0) {
            return ['success' => true];
        } else {
            return ['success' => false, 'error' => $stmt->errorInfo()[2]];
        }
    }

    /**
     * Delete a breeding pet
     */
    public function deleteBreedingPet($petId, $breederId) {
        $stmt = $this->db->prepare("DELETE FROM breeder_pets WHERE id = ? AND breeder_id = ?");
        $result = $stmt->execute([$petId, $breederId]);
        
        if ($result && $stmt->rowCount() > 0) {
            return ['success' => true];
        } else {
            return ['success' => false, 'error' => $stmt->errorInfo()[2]];
        }
    }

    /**
     * Toggle pet status (active/inactive)
     */
    public function togglePetStatus($petId, $breederId, $isActive) {
        $stmt = $this->db->prepare("UPDATE breeder_pets SET is_active = ? WHERE id = ? AND breeder_id = ?");
        $result = $stmt->execute([$isActive, $petId, $breederId]);
        
        if ($result && $stmt->rowCount() > 0) {
            return ['success' => true];
        } else {
            return ['success' => false, 'error' => $stmt->errorInfo()[2]];
        }
    }

    /**
     * Verify if a pet belongs to a breeder
     */
    public function verifyPetOwnership($petId, $breederId) {
        $stmt = $this->db->prepare("SELECT id FROM breeder_pets WHERE id = ? AND breeder_id = ?");
        $stmt->execute([$petId, $breederId]);
        
        return $stmt->rowCount() > 0;
    }
}