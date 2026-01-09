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
        return array_merge($this->getAvailablePets($breederId), [
            [
                'id' => 3,
                'name' => 'Charlie',
                'breed' => 'Poodle',
                'age' => '4 months',
                'gender' => 'Male',
                'price' => 1400,
                'status' => 'sold',
                'health_status' => 'Excellent',
                'vaccinations' => 'Up to date',
                'parents' => 'Champion bloodline'
            ]
        ]);
    }

    public function getAvailablePets($breederId) {
        return [
            [
                'id' => 1,
                'name' => 'Bella',
                'breed' => 'Golden Retriever',
                'age' => '3 months',
                'gender' => 'Female',
                'price' => 1200,
                'status' => 'available',
                'health_status' => 'Excellent',
                'vaccinations' => 'Up to date',
                'parents' => 'Champion bloodline'
            ],
            [
                'id' => 2,
                'name' => 'Duke',
                'breed' => 'German Shepherd',
                'age' => '2 months',
                'gender' => 'Male',
                'price' => 1500,
                'status' => 'available',
                'health_status' => 'Excellent',
                'vaccinations' => 'Up to date',
                'parents' => 'Working line'
            ]
        ];
    }

    public function getBreeds($breederId) {
        return [
            ['name' => 'Golden Retriever', 'count' => 8],
            ['name' => 'German Shepherd', 'count' => 6],
            ['name' => 'Labrador', 'count' => 10],
            ['name' => 'Beagle', 'count' => 5],
            ['name' => 'Poodle', 'count' => 3]
        ];
    }

    public function getPetStats($breederId) {
        return [
            'total' => 32,
            'available' => 12,
            'reserved' => 5,
            'sold' => 15
        ];
    }

    public function getBreedingPets($breederId) {
        $stmt = $this->conn->prepare("
            SELECT id, name, breed, gender, date_of_birth as dob, 
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
