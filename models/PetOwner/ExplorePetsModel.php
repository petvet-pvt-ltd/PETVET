<?php
require_once __DIR__ . '/../BaseModel.php';

class ExplorePetsModel extends BaseModel {
    
    public function getAllSellers() {
        return [
            1 => ['id' => 1, 'name' => 'You', 'location' => 'Colombo'],
            2 => ['id' => 2, 'name' => 'Kasun Perera', 'location' => 'Kandy'],
            3 => ['id' => 3, 'name' => 'Nirmala Silva', 'location' => 'Galle'],
            4 => ['id' => 4, 'name' => 'Ravi Fernando', 'location' => 'Negombo'],
            5 => ['id' => 5, 'name' => 'Priya Rajapaksha', 'location' => 'Matara'],
            6 => ['id' => 6, 'name' => 'Chaminda Wickrama', 'location' => 'Kurunegala']
        ];
    }
    
    public function getAllPets() {
        return [
            [
                'id' => 101,
                'name' => 'Rocky',
                'species' => 'Dog',
                'breed' => 'Golden Retriever',
                'age' => '3y',
                'gender' => 'Male',
                'badges' => ['Vaccinated', 'Microchipped'],
                'price' => 95000,
                'desc' => 'Friendly and well-trained. Great with kids and other pets.',
                'images' => ['https://images.unsplash.com/photo-1552053831-71594a27632d?q=80&w=1400&auto=format&fit=crop'],
                'seller_id' => 2,
                'date_posted' => '2025-10-05'
            ],
            [
                'id' => 102,
                'name' => 'Whiskers',
                'species' => 'Cat',
                'breed' => 'Siamese',
                'age' => '2y',
                'gender' => 'Female',
                'badges' => ['Vaccinated'],
                'price' => 45000,
                'desc' => 'Playful, litter-trained, prefers quiet home environment.',
                'images' => ['https://images.unsplash.com/photo-1543852786-1cf6624b9987?q=80&w=1400&auto=format&fit=crop'],
                'seller_id' => 3,
                'date_posted' => '2025-10-07'
            ],
            [
                'id' => 103,
                'name' => 'Tweety',
                'species' => 'Bird',
                'breed' => 'Canary',
                'age' => '1y',
                'gender' => 'Female',
                'badges' => ['Microchipped'],
                'price' => 12000,
                'desc' => 'Sings every morning. Healthy and very active.',
                'images' => ['https://images.unsplash.com/photo-1452570053594-1b985d6ea890?q=80&w=1400&auto=format&fit=crop'],
                'seller_id' => 2,
                'date_posted' => '2025-10-08'
            ],
            [
                'id' => 104,
                'name' => 'Bruno',
                'species' => 'Dog',
                'breed' => 'Beagle',
                'age' => '1y',
                'gender' => 'Male',
                'badges' => ['Vaccinated'],
                'price' => 80000,
                'desc' => 'Curious and energetic. Loves walks and playing fetch.',
                'images' => ['https://images.unsplash.com/photo-1543466835-00a7907e9de1?q=80&w=1400&auto=format&fit=crop'],
                'seller_id' => 1,
                'date_posted' => '2025-10-09'
            ],
            [
                'id' => 105,
                'name' => 'Luna',
                'species' => 'Cat',
                'breed' => 'Persian',
                'age' => '1.5y',
                'gender' => 'Female',
                'badges' => ['Vaccinated', 'Microchipped'],
                'price' => 65000,
                'desc' => 'Beautiful Persian cat with long fluffy fur. Very gentle.',
                'images' => ['https://images.unsplash.com/photo-1513245543132-31f507417b26?q=80&w=1400&auto=format&fit=crop'],
                'seller_id' => 4,
                'date_posted' => '2025-10-06'
            ],
            [
                'id' => 106,
                'name' => 'Max',
                'species' => 'Dog',
                'breed' => 'Labrador',
                'age' => '2y',
                'gender' => 'Male',
                'badges' => ['Vaccinated', 'Microchipped'],
                'price' => 85000,
                'desc' => 'Loyal Labrador, perfect family dog. Loves swimming.',
                'images' => ['https://images.unsplash.com/photo-1601758228041-f3b2795255f1?q=80&w=1400&auto=format&fit=crop'],
                'seller_id' => 5,
                'date_posted' => '2025-10-04'
            ],
            [
                'id' => 107,
                'name' => 'Charlie',
                'species' => 'Bird',
                'breed' => 'Cockatiel',
                'age' => '6m',
                'gender' => 'Male',
                'badges' => ['Vaccinated'],
                'price' => 18000,
                'desc' => 'Young cockatiel with beautiful crest. Can whistle tunes.',
                'images' => ['https://images.unsplash.com/photo-1583337130417-3346a1be7dee?q=80&w=1400&auto=format&fit=crop'],
                'seller_id' => 6,
                'date_posted' => '2025-10-10'
            ],
            [
                'id' => 108,
                'name' => 'Bella',
                'species' => 'Dog',
                'breed' => 'Poodle',
                'age' => '4y',
                'gender' => 'Female',
                'badges' => ['Vaccinated', 'Microchipped'],
                'price' => 70000,
                'desc' => 'Smart and hypoallergenic. Great for families with allergies.',
                'images' => ['https://images.unsplash.com/photo-1616190264687-b7ebf7aa3fa7?q=80&w=1400&auto=format&fit=crop'],
                'seller_id' => 3,
                'date_posted' => '2025-10-03'
            ]
        ];
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