<?php
require_once __DIR__ . '/../BaseModel.php';

class BreederDashboardModel extends BaseModel {
    
    public function getStats($breederId) {
        return [
            'pending_requests' => 5,
            'approved_requests' => 8,
            'total_breedings' => 32,
            'active_pets' => 6
        ];
    }

    public function getPendingRequests($breederId, $limit = null) {
        $requests = [
            [
                'id' => 1,
                'pet_name' => 'Bella',
                'breed' => 'Golden Retriever',
                'pet_breed' => 'Golden Retriever',
                'gender' => 'Female',
                'owner_name' => 'Sarah Johnson',
                'phone' => '+1 (555) 123-4567',
                'phone_2' => '+1 (555) 123-4568',
                'email' => 'sarah.j@email.com',
                'requested_date' => '2025-10-15',
                'preferred_date' => '2025-11-01',
                'message' => 'Looking for a healthy breeding partner for my Golden Retriever. She is 3 years old and has all health clearances.'
            ],
            [
                'id' => 2,
                'pet_name' => 'Max',
                'breed' => 'German Shepherd',
                'pet_breed' => 'German Shepherd',
                'gender' => 'Male',
                'owner_name' => 'Michael Brown',
                'phone' => '+1 (555) 234-5678',
                'phone_2' => '',
                'email' => 'mbrown@email.com',
                'requested_date' => '2025-10-14',
                'preferred_date' => '2025-10-28',
                'message' => 'Interested in breeding services. My dog is AKC registered with excellent temperament.'
            ],
            [
                'id' => 3,
                'pet_name' => 'Luna',
                'breed' => 'Labrador',
                'pet_breed' => 'Labrador Retriever',
                'gender' => 'Female',
                'owner_name' => 'Emma Wilson',
                'phone' => '+1 (555) 345-6789',
                'phone_2' => '+1 (555) 345-6790',
                'email' => '',
                'requested_date' => '2025-10-13',
                'preferred_date' => '2025-11-15',
                'message' => 'First time breeding. Would appreciate guidance throughout the process.'
            ],
            [
                'id' => 4,
                'pet_name' => 'Rocky',
                'breed' => 'Beagle',
                'pet_breed' => 'Beagle',
                'gender' => 'Male',
                'owner_name' => 'David Miller',
                'phone' => '+1 (555) 456-7890',
                'phone_2' => '',
                'email' => 'd.miller@email.com',
                'requested_date' => '2025-10-12',
                'preferred_date' => '2025-11-05',
                'message' => 'Looking for quality breeding services for my champion bloodline Beagle.'
            ]
        ];
        
        return $limit ? array_slice($requests, 0, $limit) : $requests;
    }

    public function getApprovedRequests($breederId, $limit = null) {
        $requests = [
            [
                'id' => 5,
                'pet_name' => 'Charlie',
                'breed' => 'Golden Retriever',
                'pet_breed' => 'Golden Retriever',
                'gender' => 'Male',
                'owner_name' => 'Jessica Davis',
                'phone' => '+1 (555) 567-8901',
                'phone_2' => '',
                'email' => 'jdavis@email.com',
                'breeding_date' => '2025-10-25',
                'breeder_pet_name' => 'Daisy',
                'notes' => 'Approved for breeding with Daisy. All health checks completed.'
            ],
            [
                'id' => 6,
                'pet_name' => 'Buddy',
                'breed' => 'Labrador',
                'pet_breed' => 'Labrador Retriever',
                'gender' => 'Male',
                'owner_name' => 'James Anderson',
                'phone' => '+1 (555) 678-9012',
                'phone_2' => '+1 (555) 678-9013',
                'email' => 'j.anderson@email.com',
                'breeding_date' => '2025-10-22',
                'breeder_pet_name' => 'Molly',
                'notes' => 'Scheduled breeding session confirmed.'
            ],
            [
                'id' => 7,
                'pet_name' => 'Sadie',
                'breed' => 'German Shepherd',
                'pet_breed' => 'German Shepherd',
                'gender' => 'Female',
                'owner_name' => 'Robert Taylor',
                'phone' => '+1 (555) 789-0123',
                'phone_2' => '',
                'email' => '',
                'breeding_date' => '2025-11-02',
                'breeder_pet_name' => 'Duke',
                'notes' => 'Owner has experience with breeding. Second breeding attempt.'
            ]
        ];
        
        return $limit ? array_slice($requests, 0, $limit) : $requests;
    }

    public function getCompletedRequests($breederId) {
        return [
            [
                'id' => 8,
                'pet_name' => 'Molly',
                'breed' => 'Golden Retriever',
                'pet_breed' => 'Golden Retriever',
                'gender' => 'Female',
                'owner_name' => 'Lisa White',
                'phone' => '+1 (555) 890-1234',
                'phone_2' => '',
                'email' => 'lwhite@email.com',
                'completion_date' => '2025-09-15',
                'breeder_pet_name' => 'Max',
                'final_notes' => 'Breeding completed successfully. Owner very satisfied with the service.'
            ],
            [
                'id' => 9,
                'pet_name' => 'Cooper',
                'breed' => 'Labrador',
                'pet_breed' => 'Labrador Retriever',
                'gender' => 'Male',
                'owner_name' => 'William Harris',
                'phone' => '+1 (555) 901-2345',
                'phone_2' => '+1 (555) 901-2346',
                'email' => 'wharris@email.com',
                'completion_date' => '2025-09-10',
                'breeder_pet_name' => 'Bella',
                'final_notes' => 'Excellent breeding pair. All paperwork completed.'
            ]
        ];
    }

    public function getUpcomingBreedingDates($breederId, $limit = 5) {
        $dates = [
            [
                'id' => 1,
                'breeding_date' => '2025-10-22',
                'breeder_pet_name' => 'Molly',
                'customer_pet_name' => 'Buddy',
                'breed' => 'Labrador Retriever',
                'owner_name' => 'James Anderson'
            ],
            [
                'id' => 2,
                'breeding_date' => '2025-10-25',
                'breeder_pet_name' => 'Daisy',
                'customer_pet_name' => 'Charlie',
                'breed' => 'Golden Retriever',
                'owner_name' => 'Jessica Davis'
            ],
            [
                'id' => 3,
                'breeding_date' => '2025-11-02',
                'breeder_pet_name' => 'Duke',
                'customer_pet_name' => 'Sadie',
                'breed' => 'German Shepherd',
                'owner_name' => 'Robert Taylor'
            ],
            [
                'id' => 4,
                'breeding_date' => '2025-11-08',
                'breeder_pet_name' => 'Rex',
                'customer_pet_name' => 'Princess',
                'breed' => 'Golden Retriever',
                'owner_name' => 'Amanda Clark'
            ],
            [
                'id' => 5,
                'breeding_date' => '2025-11-15',
                'breeder_pet_name' => 'Bella',
                'customer_pet_name' => 'Rocky',
                'breed' => 'Beagle',
                'owner_name' => 'Christopher Lee'
            ],
            [
                'id' => 6,
                'breeding_date' => '2025-11-20',
                'breeder_pet_name' => 'Max',
                'customer_pet_name' => 'Lucy',
                'breed' => 'Golden Retriever',
                'owner_name' => 'Patricia Martinez'
            ]
        ];
        
        return array_slice($dates, 0, $limit);
    }
}
