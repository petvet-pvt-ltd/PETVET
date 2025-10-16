<?php
require_once __DIR__ . '/../BaseModel.php';

class BreederPetsModel extends BaseModel {
    
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
        return [
            [
                'id' => 1,
                'name' => 'Max',
                'breed' => 'Golden Retriever',
                'gender' => 'Male',
                'dob' => '2021-05-15',
                'age' => '4 years',
                'photo' => 'https://images.unsplash.com/photo-1633722715463-d30f4f325e24?w=300&h=300&fit=crop',
                'is_active' => true,
                'description' => 'Champion bloodline Golden Retriever with excellent temperament'
            ],
            [
                'id' => 2,
                'name' => 'Daisy',
                'breed' => 'Golden Retriever',
                'gender' => 'Female',
                'dob' => '2020-08-20',
                'age' => '5 years',
                'photo' => 'https://images.unsplash.com/photo-1552053831-71594a27632d?w=300&h=300&fit=crop',
                'is_active' => true,
                'description' => 'Award-winning female with multiple championships'
            ],
            [
                'id' => 3,
                'name' => 'Duke',
                'breed' => 'German Shepherd',
                'gender' => 'Male',
                'dob' => '2021-03-10',
                'age' => '4 years',
                'photo' => 'https://images.unsplash.com/photo-1568572933382-74d440642117?w=300&h=300&fit=crop',
                'is_active' => true,
                'description' => 'Working line German Shepherd with strong protective instincts'
            ],
            [
                'id' => 4,
                'name' => 'Bella',
                'breed' => 'Labrador Retriever',
                'gender' => 'Female',
                'dob' => '2022-01-25',
                'age' => '2 years',
                'photo' => 'https://images.unsplash.com/photo-1510771463146-e89e6e86560e?w=300&h=300&fit=crop',
                'is_active' => true,
                'description' => 'Friendly and energetic Labrador, excellent with families'
            ],
            [
                'id' => 5,
                'name' => 'Rex',
                'breed' => 'Golden Retriever',
                'gender' => 'Male',
                'dob' => '2020-11-05',
                'age' => '4 years',
                'photo' => 'https://images.unsplash.com/photo-1601758228041-f3b2795255f1?w=300&h=300&fit=crop',
                'is_active' => true,
                'description' => 'Show quality Golden Retriever with perfect conformation'
            ],
            [
                'id' => 6,
                'name' => 'Molly',
                'breed' => 'Labrador Retriever',
                'gender' => 'Female',
                'dob' => '2021-07-12',
                'age' => '4 years',
                'photo' => 'https://images.unsplash.com/photo-1587300003388-59208cc962cb?w=300&h=300&fit=crop',
                'is_active' => false,
                'description' => 'Currently unavailable for breeding - health checkup'
            ]
        ];
    }
}
