<?php
require_once __DIR__ . '/../BaseModel.php';
require_once __DIR__ . '/../../config/connect.php';

class BreederPetsModel extends BaseModel {
    protected $conn;
    
    public function __construct() {
        parent::__construct();
        global $conn;
        $this->conn = $conn;
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

    public function getBreedingPets($breederId) {
        $stmt = $this->conn->prepare("
            SELECT id, name, breed, gender, date_of_birth as dob,species,
                   photo, description, is_active,
                   TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) as age_years
            FROM breeder_pets
            WHERE breeder_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->bind_param("i", $breederId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $pets = [];
        while ($row = $result->fetch_assoc()) {
            $row['is_active'] = (bool)$row['is_active'];
            $row['age'] = $row['age_years'] . ' ' . ($row['age_years'] == 1 ? 'year' : 'years');
            unset($row['age_years']);
            $pets[] = $row;
        }
        
        return $pets;
    }
}
