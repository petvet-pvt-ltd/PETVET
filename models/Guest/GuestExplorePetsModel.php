<?php
require_once __DIR__ . '/../BaseModel.php';

class GuestExplorePetsModel extends BaseModel {
    
    public function getAllSellers() {
        // Same data as PetOwner version - 100% identical
        return [
            1 => ['id' => 1, 'name' => 'You', 'location' => 'Colombo', 'phone' => '+94 77 123 4567', 'phone2' => '+94 77 123 4568', 'email' => 'you@example.com'],
            2 => ['id' => 2, 'name' => 'Kasun Perera', 'location' => 'Kandy', 'phone' => '+94 77 987 6543', 'phone2' => '', 'email' => 'kasun.perera@petvet.lk'],
            3 => ['id' => 3, 'name' => 'Nirmala Silva', 'location' => 'Galle', 'phone' => '+94 76 555 1212', 'phone2' => '+94 76 555 1213', 'email' => 'nirmala@example.com'],
            4 => ['id' => 4, 'name' => 'Ravi Fernando', 'location' => 'Negombo', 'phone' => '+94 75 888 9999', 'phone2' => '', 'email' => 'ravi@example.com'],
            5 => ['id' => 5, 'name' => 'Priya Rajapaksha', 'location' => 'Matara', 'phone' => '+94 78 222 3333', 'phone2' => '', 'email' => 'priya@example.com'],
            6 => ['id' => 6, 'name' => 'Chaminda Wickrama', 'location' => 'Kurunegala', 'phone' => '+94 71 444 5555', 'phone2' => '', 'email' => 'chaminda@example.com']
        ];
    }
    
    public function getAllPets() {
        // Same data as PetOwner version - 100% identical
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
                'images' => [
                    'https://images.unsplash.com/photo-1552053831-71594a27632d?q=80&w=1400&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1633722715463-d30f4f325e24?q=80&w=1400&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1601758228041-f3b2795255f1?q=80&w=1400&auto=format&fit=crop'
                ],
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
                'images' => [
                    'https://images.unsplash.com/photo-1543852786-1cf6624b9987?q=80&w=1400&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1574158622682-e40e69881006?q=80&w=1400&auto=format&fit=crop'
                ],
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
                'images' => [
                    'https://images.unsplash.com/photo-1543466835-00a7907e9de1?q=80&w=1400&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1505628346881-b72b27e84530?q=80&w=1400&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1517849845537-4d257902454a?q=80&w=1400&auto=format&fit=crop'
                ],
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
                'badges' => ['Vaccinated'],
                'price' => 85000,
                'desc' => 'Obedient, house-trained. Loves swimming.',
                'images' => [
                    'https://images.unsplash.com/photo-1534351450181-ea9f78427fe8?q=80&w=1400&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1529472119196-cb724127a98e?q=80&w=1400&auto=format&fit=crop'
                ],
                'seller_id' => 5,
                'date_posted' => '2025-10-04'
            ],
            [
                'id' => 107,
                'name' => 'Mittens',
                'species' => 'Cat',
                'breed' => 'British Shorthair',
                'age' => '1y',
                'gender' => 'Male',
                'badges' => ['Vaccinated', 'Microchipped'],
                'price' => 55000,
                'desc' => 'Calm temperament. Loves to nap in sunny spots.',
                'images' => ['https://images.unsplash.com/photo-1529778873920-4da4926a72c2?q=80&w=1400&auto=format&fit=crop'],
                'seller_id' => 6,
                'date_posted' => '2025-10-03'
            ],
            [
                'id' => 108,
                'name' => 'Charlie',
                'species' => 'Bird',
                'breed' => 'Budgie',
                'age' => '6m',
                'gender' => 'Male',
                'badges' => [],
                'price' => 8000,
                'desc' => 'Playful budgie. Can mimic words.',
                'images' => ['https://images.unsplash.com/photo-1535083783855-76ae62b2914e?q=80&w=1400&auto=format&fit=crop'],
                'seller_id' => 2,
                'date_posted' => '2025-10-10'
            ]
        ];
    }
    
    public function getAvailableSpecies() {
        return ['Dog', 'Cat', 'Bird', 'Other'];
    }
}
?>
