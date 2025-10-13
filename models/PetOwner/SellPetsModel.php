<?php
require_once __DIR__ . '/../BaseModel.php';

class SellPetsModel extends BaseModel {
    
    public function getFormData() {
        // Return data needed for the sell pets form
        return [
            'species_options' => [
                'Dog' => 'Dog',
                'Cat' => 'Cat', 
                'Bird' => 'Bird',
                'Rabbit' => 'Rabbit',
                'Fish' => 'Fish',
                'Other' => 'Other'
            ],
            'gender_options' => [
                'Male' => 'Male',
                'Female' => 'Female'
            ],
            'available_badges' => [
                'Vaccinated' => 'Vaccinated',
                'Microchipped' => 'Microchipped',
                'Spayed/Neutered' => 'Spayed/Neutered',
                'Health Checked' => 'Health Checked',
                'Trained' => 'Trained'
            ],
            'popular_breeds' => [
                'Dog' => [
                    'Golden Retriever', 'Labrador', 'German Shepherd', 'Beagle', 
                    'Poodle', 'Rottweiler', 'Bulldog', 'Mixed Breed'
                ],
                'Cat' => [
                    'Siamese', 'Persian', 'Maine Coon', 'British Shorthair',
                    'Ragdoll', 'Domestic Shorthair', 'Mixed Breed'
                ],
                'Bird' => [
                    'Canary', 'Cockatiel', 'Parrot', 'Budgie', 'Finch', 'Lovebird'
                ],
                'Rabbit' => [
                    'Holland Lop', 'Netherland Dwarf', 'Lionhead', 'Mini Rex'
                ]
            ]
        ];
    }
    
    public function getUserListings($userId) {
        // This would fetch the user's current pet listings from database
        // For now, return mock data
        return [
            [
                'id' => 109,
                'name' => 'Charlie',
                'species' => 'Dog',
                'breed' => 'Boxer',
                'age' => '2y',
                'gender' => 'Male',
                'price' => 75000,
                'status' => 'active',
                'views' => 23,
                'date_posted' => '2025-10-08',
                'image' => 'https://images.unsplash.com/photo-1583337130417-3346a1be7dee?q=80&w=300&auto=format&fit=crop'
            ],
            [
                'id' => 110,
                'name' => 'Mittens',
                'species' => 'Cat',
                'breed' => 'Tabby',
                'age' => '1y',
                'gender' => 'Female',
                'price' => 35000,
                'status' => 'sold',
                'views' => 45,
                'date_posted' => '2025-10-02',
                'image' => 'https://images.unsplash.com/photo-1574158622682-e40e69881006?q=80&w=300&auto=format&fit=crop'
            ]
        ];
    }
    
    public function createListing($data) {
        // This would insert a new pet listing into the database
        // For now, just validate and return success/error
        
        $errors = [];
        
        // Basic validation
        if (empty($data['name'])) {
            $errors[] = 'Pet name is required';
        }
        if (empty($data['species'])) {
            $errors[] = 'Species is required';
        }
        if (empty($data['breed'])) {
            $errors[] = 'Breed is required';
        }
        if (empty($data['age'])) {
            $errors[] = 'Age is required';
        }
        if (empty($data['price']) || !is_numeric($data['price']) || $data['price'] <= 0) {
            $errors[] = 'Valid price is required';
        }
        if (empty($data['desc'])) {
            $errors[] = 'Description is required';
        }
        if (empty($data['image']) || !filter_var($data['image'], FILTER_VALIDATE_URL)) {
            $errors[] = 'Valid image URL is required';
        }
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        // In a real app, this would insert into database
        // For now, return success with mock ID
        $newListingId = rand(200, 999);
        
        return [
            'success' => true,
            'listing_id' => $newListingId,
            'message' => 'Pet listing created successfully!'
        ];
    }
    
    public function updateListing($listingId, $data, $userId) {
        // This would update an existing listing in the database
        // Validate ownership and update
        
        // Mock validation - in real app, check if listing belongs to user
        $userListings = $this->getUserListings($userId);
        $exists = false;
        foreach ($userListings as $listing) {
            if ($listing['id'] == $listingId) {
                $exists = true;
                break;
            }
        }
        
        if (!$exists) {
            return ['success' => false, 'error' => 'Listing not found or not owned by user'];
        }
        
        // Perform same validation as create
        $result = $this->createListing($data);
        if (!$result['success']) {
            return $result;
        }
        
        return [
            'success' => true,
            'message' => 'Pet listing updated successfully!'
        ];
    }
    
    public function deleteListing($listingId, $userId) {
        // This would delete a listing from the database
        // Validate ownership first
        
        $userListings = $this->getUserListings($userId);
        $exists = false;
        foreach ($userListings as $listing) {
            if ($listing['id'] == $listingId) {
                $exists = true;
                break;
            }
        }
        
        if (!$exists) {
            return ['success' => false, 'error' => 'Listing not found or not owned by user'];
        }
        
        // In real app, soft delete or hard delete from database
        return [
            'success' => true,
            'message' => 'Pet listing deleted successfully!'
        ];
    }
    
    public function getListingStats($userId) {
        $listings = $this->getUserListings($userId);
        
        $totalListings = count($listings);
        $activeListings = count(array_filter($listings, fn($l) => $l['status'] === 'active'));
        $soldListings = count(array_filter($listings, fn($l) => $l['status'] === 'sold'));
        $totalViews = array_sum(array_column($listings, 'views'));
        
        return [
            'total_listings' => $totalListings,
            'active_listings' => $activeListings,
            'sold_listings' => $soldListings,
            'total_views' => $totalViews
        ];
    }
}
?>