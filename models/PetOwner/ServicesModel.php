<?php
require_once __DIR__ . '/../BaseModel.php';

class ServicesModel extends BaseModel {
    
    /**
     * Get all service providers based on service type and filters
     */
    public function getServiceProviders($serviceType, $filters = []) {
        // Mock data - replace with database queries when DB is implemented
        switch ($serviceType) {
            case 'trainers':
                return $this->getTrainers($filters);
            case 'sitters':
                return $this->getSitters($filters);
            case 'breeders':
                return $this->getBreeders($filters);
            case 'groomers':
                return $this->getGroomers($filters);
            default:
                return [];
        }
    }
    
    /**
     * Get trainers with filters (Mock Data)
     */
    private function getTrainers($filters) {
        // Mock trainer data
        $allTrainers = [
            [
                'id' => 1,
                'name' => 'John Anderson',
                'business_name' => 'Elite K9 Training Academy',
                'email' => 'john.anderson@petvet.com',
                'phone' => '555-0100',
                'specialization' => 'Obedience & Agility',
                'experience_years' => 8,
                'certifications' => 'Certified Dog Trainer (CDT)',
                'city' => 'Colombo',
                'avatar' => 'https://i.pravatar.cc/150?img=12',
                'base_rate' => 1500,
                'training_types' => ['Basic', 'Intermediate', 'Advanced']
            ],
            [
                'id' => 2,
                'name' => 'Sarah Mitchell',
                'business_name' => 'Pawsitive Training Center',
                'email' => 'sarah.mitchell@petvet.com',
                'phone' => '555-0101',
                'specialization' => 'Behavior Modification',
                'experience_years' => 6,
                'certifications' => 'Certified Professional Dog Trainer',
                'city' => 'Kandy',
                'avatar' => 'https://i.pravatar.cc/150?img=45',
                'base_rate' => 1800,
                'training_types' => ['Basic', 'Intermediate', 'Advanced']
            ],
            [
                'id' => 3,
                'name' => 'Michael Chen',
                'business_name' => 'Champion Dog Sports',
                'email' => 'michael.chen@petvet.com',
                'phone' => '555-0102',
                'specialization' => 'Agility & Competition',
                'experience_years' => 10,
                'certifications' => 'Master Dog Trainer, Agility Specialist',
                'city' => 'Galle',
                'avatar' => 'https://i.pravatar.cc/150?img=33',
                'base_rate' => 2000,
                'training_types' => ['Intermediate', 'Advanced']
            ],
            [
                'id' => 4,
                'name' => 'Emma Thompson',
                'business_name' => 'Happy Paws Puppy School',
                'email' => 'emma.thompson@petvet.com',
                'phone' => '555-0103',
                'specialization' => 'Puppy Training & Socialization',
                'experience_years' => 4,
                'certifications' => 'Certified Dog Behavior Consultant',
                'city' => 'Colombo',
                'avatar' => 'https://i.pravatar.cc/150?img=9',
                'base_rate' => 1200,
                'training_types' => ['Basic', 'Intermediate']
            ],
            [
                'id' => 5,
                'name' => 'David Roberts',
                'business_name' => 'Guardian K9 Training',
                'email' => 'david.roberts@petvet.com',
                'phone' => '555-0104',
                'specialization' => 'Obedience & Protection',
                'experience_years' => 12,
                'certifications' => 'Professional Dog Trainer, K9 Handler',
                'city' => 'Negombo',
                'avatar' => 'https://i.pravatar.cc/150?img=15',
                'base_rate' => 2500,
                'training_types' => ['Basic', 'Intermediate', 'Advanced']
            ]
        ];
        
        return $this->applyFilters($allTrainers, $filters);
    }
    
    /**
     * Get sitters with filters (Mock Data)
     */
    private function getSitters($filters) {
        // Mock sitter data
        $allSitters = [
            [
                'id' => 6,
                'name' => 'Sophie Williams',
                'business_name' => 'Paws & Stay Pet Care',
                'email' => 'sophie.williams@petvet.com',
                'phone' => '555-0200',
                'experience_years' => 5,
                'pet_types' => 'Dogs, Cats',
                'home_type' => 'House with Yard',
                'city' => 'Colombo',
                'avatar' => 'https://i.pravatar.cc/150?img=47',
                'description' => 'Offering professional pet sitting, daily walks, and overnight care. Spacious backyard perfect for active dogs. Starting from LKR 1,500/day.',
                'base_rate' => 1500
            ],
            [
                'id' => 7,
                'name' => 'Lucas Martinez',
                'business_name' => 'Happy Tails Pet Sitting',
                'email' => 'lucas.martinez@petvet.com',
                'phone' => '555-0201',
                'experience_years' => 3,
                'pet_types' => 'Dogs, Cats, Birds',
                'home_type' => 'Apartment',
                'city' => 'Kandy',
                'avatar' => 'https://i.pravatar.cc/150?img=52',
                'description' => 'Specialized in small pets and birds. Daily visits, feeding, playtime. Flexible scheduling available. From LKR 1,200/visit.',
                'base_rate' => 1200
            ],
            [
                'id' => 8,
                'name' => 'Olivia Brown',
                'business_name' => 'Canine Companions Care',
                'email' => 'olivia.brown@petvet.com',
                'phone' => '555-0202',
                'experience_years' => 7,
                'pet_types' => 'Dogs',
                'home_type' => 'House with Yard',
                'city' => 'Galle',
                'avatar' => 'https://i.pravatar.cc/150?img=10',
                'description' => 'Dog walking specialist with large fenced yard. Active play sessions, overnight boarding, and exercise programs. LKR 2,000/day.',
                'base_rate' => 2000
            ],
            [
                'id' => 9,
                'name' => 'James Wilson',
                'business_name' => 'Pet Paradise Sitters',
                'email' => 'james.wilson@petvet.com',
                'phone' => '555-0203',
                'experience_years' => 4,
                'pet_types' => 'Cats, Birds',
                'home_type' => 'Apartment',
                'city' => 'Colombo',
                'avatar' => 'https://i.pravatar.cc/150?img=56',
                'description' => 'Cat and bird care expert. Quiet environment, medication administration available. Daily updates with photos. From LKR 1,300/day.',
                'base_rate' => 1300
            ],
            [
                'id' => 10,
                'name' => 'Ava Garcia',
                'business_name' => 'Furry Friends Haven',
                'email' => 'ava.garcia@petvet.com',
                'phone' => '555-0204',
                'experience_years' => 6,
                'pet_types' => 'Dogs, Cats',
                'home_type' => 'House with Yard',
                'city' => 'Mount Lavinia',
                'avatar' => 'https://i.pravatar.cc/150?img=16',
                'description' => 'Full-service pet care including grooming, walks, and training reinforcement. Beach walks available nearby. LKR 1,800/day.',
                'base_rate' => 1800
            ]
        ];
        
        return $this->applyFilters($allSitters, $filters);
    }
    
    /**
     * Get breeders with filters (Mock Data)
     */
    private function getBreeders($filters) {
        // Mock breeder data
        $allBreeders = [
            [
                'id' => 11,
                'name' => 'William Taylor',
                'email' => 'william.taylor@petvet.com',
                'phone' => '555-0300',
                'business_name' => 'Royal Canine Breeding',
                'experience_years' => 12,
                'city' => 'Colombo',
                'avatar' => 'https://i.pravatar.cc/150?img=11',
                'specialization' => 'German Shepherd & Golden Retriever Specialist',
                'breeding_pets' => [
                    [
                        'id' => 1,
                        'name' => 'Max',
                        'breed' => 'Golden Retriever',
                        'gender' => 'Male',
                        'is_active' => true,
                    ],
                    [
                        'id' => 2,
                        'name' => 'Luna',
                        'breed' => 'Golden Retriever',
                        'gender' => 'Female',
                        'is_active' => true,
                    ],
                    [
                        'id' => 3,
                        'name' => 'Charlie',
                        'breed' => 'German Shepherd',
                        'gender' => 'Male',
                        'is_active' => false,
                    ]
                ],
            ],
            [
                'id' => 12,
                'name' => 'Patricia Moore',
                'email' => 'patricia.moore@petvet.com',
                'phone' => '555-0301',
                'business_name' => 'Persian Paradise Cattery',
                'experience_years' => 8,
                'city' => 'Galle',
                'avatar' => 'https://i.pravatar.cc/150?img=21',
                'specialization' => 'Persian & Maine Coon Specialist',
                'breeding_pets' => [
                    [
                        'id' => 4,
                        'name' => 'Simba',
                        'breed' => 'Persian Cat',
                        'gender' => 'Male',
                        'is_active' => true,
                    ],
                    [
                        'id' => 5,
                        'name' => 'Nala',
                        'breed' => 'Persian Cat',
                        'gender' => 'Female',
                        'is_active' => true,
                    ],
                    [
                        'id' => 6,
                        'name' => 'Leo',
                        'breed' => 'Maine Coon',
                        'gender' => 'Male',
                        'is_active' => true,
                    ],
                    [
                        'id' => 7,
                        'name' => 'Luna',
                        'breed' => 'Maine Coon',
                        'gender' => 'Female',
                        'is_active' => false,
                    ]
                ],
            ],
            [
                'id' => 13,
                'name' => 'Jennifer Lee',
                'email' => 'jennifer.lee@petvet.com',
                'phone' => '555-0302',
                'business_name' => 'Elite Dog Breeders',
                'experience_years' => 15,
                'city' => 'Kandy',
                'avatar' => 'https://i.pravatar.cc/150?img=31',
                'specialization' => 'Labrador & Rottweiler Expert',
                'breeding_pets' => [
                    [
                        'id' => 8,
                        'name' => 'Duke',
                        'breed' => 'Labrador Retriever',
                        'gender' => 'Male',
                        'is_active' => true,
                    ],
                    [
                        'id' => 9,
                        'name' => 'Bella',
                        'breed' => 'Labrador Retriever',
                        'gender' => 'Female',
                        'is_active' => true,
                    ],
                    [
                        'id' => 10,
                        'name' => 'Rocky',
                        'breed' => 'Rottweiler',
                        'gender' => 'Male',
                        'is_active' => true,
                    ],
                    [
                        'id' => 11,
                        'name' => 'Rose',
                        'breed' => 'Rottweiler',
                        'gender' => 'Female',
                        'is_active' => true,
                    ]
                ],
            ],
            [
                'id' => 14,
                'name' => 'Emily Watson',
                'email' => 'emily.watson@petvet.com',
                'phone' => '555-0303',
                'business_name' => 'Pedigree Pups',
                'experience_years' => 6,
                'city' => 'Colombo',
                'avatar' => 'https://i.pravatar.cc/150?img=41',
                'specialization' => 'Poodle & Yorkshire Terrier Specialist',
                'breeding_pets' => [
                    [
                        'id' => 12,
                        'name' => 'Charlie',
                        'breed' => 'Poodle',
                        'gender' => 'Male',
                        'is_active' => true,
                    ],
                    [
                        'id' => 13,
                        'name' => 'Sophie',
                        'breed' => 'Poodle',
                        'gender' => 'Female',
                        'is_active' => true,
                    ],
                    [
                        'id' => 14,
                        'name' => 'Buddy',
                        'breed' => 'Yorkshire Terrier',
                        'gender' => 'Male',
                        'is_active' => true,
                    ]
                ],
            ],
            [
                'id' => 15,
                'name' => 'Daniel White',
                'email' => 'daniel.white@petvet.com',
                'phone' => '555-0304',
                'business_name' => 'Exotic Bird Haven',
                'experience_years' => 10,
                'city' => 'Negombo',
                'avatar' => 'https://i.pravatar.cc/150?img=51',
                'specialization' => 'Exotic Birds Breeding Specialist',
                'breeding_pets' => [
                    [
                        'id' => 15,
                        'name' => 'Rio',
                        'breed' => 'African Grey Parrot',
                        'gender' => 'Male',
                        'is_active' => true,
                    ],
                    [
                        'id' => 16,
                        'name' => 'Pearl',
                        'breed' => 'African Grey Parrot',
                        'gender' => 'Female',
                        'is_active' => true,
                    ],
                    [
                        'id' => 17,
                        'name' => 'Sky',
                        'breed' => 'Cockatiel',
                        'gender' => 'Male',
                        'is_active' => true,
                    ],
                    [
                        'id' => 18,
                        'name' => 'Dawn',
                        'breed' => 'Cockatiel',
                        'gender' => 'Female',
                        'is_active' => true,
                    ]
                ],
            ]
        ];
        
        return $this->applyFilters($allBreeders, $filters);
    }
    
    /**
     * Get groomers with filters (Mock Data)
     */
    private function getGroomers($filters) {
        // Check if user wants to see services/packages instead of providers
        if (!empty($filters['show']) && ($filters['show'] === 'services' || $filters['show'] === 'packages')) {
            return $this->getGroomingServices($filters);
        }
        
        // Default: Show groomer providers
        // Mock groomer data
        $allGroomers = [
            [
                'id' => 16,
                'name' => 'Isabella Martinez',
                'email' => 'isabella.martinez@petvet.com',
                'phone' => '555-0400',
                'business_name' => 'Paws & Perfection Grooming',
                'experience_years' => 7,
                'specializations' => 'Dogs, Cats, Show Grooming',
                'certifications' => 'Certified Professional Groomer',
                'city' => 'Colombo',
                'avatar' => 'https://i.pravatar.cc/150?img=32'
            ],
            [
                'id' => 17,
                'name' => 'Alexander Johnson',
                'email' => 'alexander.johnson@petvet.com',
                'phone' => '555-0401',
                'business_name' => 'Elite Breed Grooming Studio',
                'experience_years' => 5,
                'specializations' => 'Dogs, Breed-Specific Cuts',
                'certifications' => 'Master Groomer Certified',
                'city' => 'Kandy',
                'avatar' => 'https://i.pravatar.cc/150?img=70'
            ],
            [
                'id' => 18,
                'name' => 'Sophia Rodriguez',
                'email' => 'sophia.rodriguez@petvet.com',
                'phone' => '555-0402',
                'business_name' => 'Whiskers & Wags Spa',
                'experience_years' => 9,
                'specializations' => 'Cats, Show Grooming',
                'certifications' => 'International Cat Groomer',
                'city' => 'Galle',
                'avatar' => 'https://i.pravatar.cc/150?img=38'
            ],
            [
                'id' => 19,
                'name' => 'Benjamin Clark',
                'email' => 'benjamin.clark@petvet.com',
                'phone' => '555-0403',
                'business_name' => 'Quick Clips Pet Salon',
                'experience_years' => 6,
                'specializations' => 'Dogs, Cats, Small Pets',
                'certifications' => 'Certified Pet Stylist',
                'city' => 'Colombo',
                'avatar' => 'https://i.pravatar.cc/150?img=11'
            ],
            [
                'id' => 20,
                'name' => 'Charlotte Anderson',
                'email' => 'charlotte.anderson@petvet.com',
                'phone' => '555-0404',
                'business_name' => 'Glamour Paws Boutique',
                'experience_years' => 8,
                'specializations' => 'Dogs, Show Grooming, Creative Styling',
                'certifications' => 'Award-Winning Groomer',
                'city' => 'Mount Lavinia',
                'avatar' => 'https://i.pravatar.cc/150?img=43'
            ]
        ];
        
        return $this->applyFilters($allGroomers, $filters);
    }
    
    /**
     * Get grooming services/packages instead of providers (Mock Data)
     */
    private function getGroomingServices($filters) {
        // Check if user specifically wants packages
        if (!empty($filters['show']) && $filters['show'] === 'packages') {
            return $this->getGroomingPackages($filters);
        }
        
        // Otherwise check service_type filter
        $serviceType = $filters['service_type'] ?? 'single';
        
        if ($serviceType === 'package') {
            return $this->getGroomingPackages($filters);
        } else {
            return $this->getGroomingSingles($filters);
        }
    }
    
    /**
     * Get single grooming services from all groomers
     */
    private function getGroomingSingles($filters) {
        // Mock single services from all groomers
        $allServices = [
            // Isabella Martinez's services
            [
                'id' => 101,
                'service_name' => 'Basic Bath & Brush',
                'description' => 'Includes bath, blow dry, brushing, nail trim',
                'price' => 3500,
                'duration' => '1 hour',
                'pet_size' => 'Small',
                'for_dogs' => true,
                'for_cats' => false,
                'available' => true,
                'groomer_id' => 16,
                'groomer_name' => 'Isabella Martinez',
                'groomer_business' => 'Paws & Perfection Grooming',
                'groomer_city' => 'Colombo',
                'groomer_phone' => '555-0400',
                'groomer_avatar' => 'https://i.pravatar.cc/150?img=32'
            ],
            [
                'id' => 102,
                'service_name' => 'Full Grooming Package',
                'description' => 'Bath, haircut, styling, nail trim, ear cleaning',
                'price' => 6500,
                'duration' => '2 hours',
                'pet_size' => 'Medium',
                'for_dogs' => true,
                'for_cats' => false,
                'available' => true,
                'groomer_id' => 16,
                'groomer_name' => 'Isabella Martinez',
                'groomer_business' => 'Paws & Perfection Grooming',
                'groomer_city' => 'Colombo',
                'groomer_phone' => '555-0400',
                'groomer_avatar' => 'https://i.pravatar.cc/150?img=32'
            ],
            // Alexander Johnson's services
            [
                'id' => 103,
                'service_name' => 'Breed-Specific Cut',
                'description' => 'Professional breed standard grooming',
                'price' => 7500,
                'duration' => '2.5 hours',
                'pet_size' => 'All sizes',
                'for_dogs' => true,
                'for_cats' => false,
                'available' => true,
                'groomer_id' => 17,
                'groomer_name' => 'Alexander Johnson',
                'groomer_business' => 'Elite Breed Grooming Studio',
                'groomer_city' => 'Kandy',
                'groomer_phone' => '555-0401',
                'groomer_avatar' => 'https://i.pravatar.cc/150?img=70'
            ],
            [
                'id' => 104,
                'service_name' => 'Puppy First Groom',
                'description' => 'Gentle introduction to grooming for puppies',
                'price' => 3000,
                'duration' => '45 mins',
                'pet_size' => 'Small',
                'for_dogs' => true,
                'for_cats' => false,
                'available' => true,
                'groomer_id' => 17,
                'groomer_name' => 'Alexander Johnson',
                'groomer_business' => 'Elite Breed Grooming Studio',
                'groomer_city' => 'Kandy',
                'groomer_phone' => '555-0401',
                'groomer_avatar' => 'https://i.pravatar.cc/150?img=70'
            ],
            // Sophia Rodriguez's services
            [
                'id' => 105,
                'service_name' => 'Cat Grooming Special',
                'description' => 'Specialized cat grooming with gentle handling',
                'price' => 4500,
                'duration' => '1 hour',
                'pet_size' => 'Cats',
                'for_dogs' => false,
                'for_cats' => true,
                'available' => true,
                'groomer_id' => 18,
                'groomer_name' => 'Sophia Rodriguez',
                'groomer_business' => 'Whiskers & Wags Spa',
                'groomer_city' => 'Galle',
                'groomer_phone' => '555-0402',
                'groomer_avatar' => 'https://i.pravatar.cc/150?img=38'
            ],
            [
                'id' => 106,
                'service_name' => 'Show Prep Grooming',
                'description' => 'Competition-ready grooming for show cats',
                'price' => 9000,
                'duration' => '3 hours',
                'pet_size' => 'Cats',
                'for_dogs' => false,
                'for_cats' => true,
                'available' => true,
                'groomer_id' => 18,
                'groomer_name' => 'Sophia Rodriguez',
                'groomer_business' => 'Whiskers & Wags Spa',
                'groomer_city' => 'Galle',
                'groomer_phone' => '555-0402',
                'groomer_avatar' => 'https://i.pravatar.cc/150?img=38'
            ],
            // Benjamin Clark's services
            [
                'id' => 107,
                'service_name' => 'Express Grooming',
                'description' => 'Quick bath and brush for busy pet owners',
                'price' => 2500,
                'duration' => '30 mins',
                'pet_size' => 'Small',
                'for_dogs' => true,
                'for_cats' => true,
                'available' => true,
                'groomer_id' => 19,
                'groomer_name' => 'Benjamin Clark',
                'groomer_business' => 'Quick Clips Pet Salon',
                'groomer_city' => 'Colombo',
                'groomer_phone' => '555-0403',
                'groomer_avatar' => 'https://i.pravatar.cc/150?img=11'
            ],
            [
                'id' => 108,
                'service_name' => 'De-Shedding Treatment',
                'description' => 'Special treatment to reduce shedding',
                'price' => 5000,
                'duration' => '1.5 hours',
                'pet_size' => 'All sizes',
                'for_dogs' => true,
                'for_cats' => true,
                'available' => true,
                'groomer_id' => 19,
                'groomer_name' => 'Benjamin Clark',
                'groomer_business' => 'Quick Clips Pet Salon',
                'groomer_city' => 'Colombo',
                'groomer_phone' => '555-0403',
                'groomer_avatar' => 'https://i.pravatar.cc/150?img=11'
            ],
            // Charlotte Anderson's services
            [
                'id' => 109,
                'service_name' => 'Creative Styling',
                'description' => 'Fun and creative pet styling with colors',
                'price' => 8000,
                'duration' => '2 hours',
                'pet_size' => 'Small/Medium',
                'for_dogs' => true,
                'for_cats' => false,
                'available' => true,
                'groomer_id' => 20,
                'groomer_name' => 'Charlotte Anderson',
                'groomer_business' => 'Glamour Paws Boutique',
                'groomer_city' => 'Mount Lavinia',
                'groomer_phone' => '555-0404',
                'groomer_avatar' => 'https://i.pravatar.cc/150?img=43'
            ],
            [
                'id' => 110,
                'service_name' => 'Senior Pet Care',
                'description' => 'Gentle grooming for older pets with special needs',
                'price' => 5500,
                'duration' => '1.5 hours',
                'pet_size' => 'All sizes',
                'for_dogs' => true,
                'for_cats' => true,
                'available' => true,
                'groomer_id' => 20,
                'groomer_name' => 'Charlotte Anderson',
                'groomer_business' => 'Glamour Paws Boutique',
                'groomer_city' => 'Mount Lavinia',
                'groomer_phone' => '555-0404',
                'groomer_avatar' => 'https://i.pravatar.cc/150?img=43'
            ]
        ];
        
        return $this->applyServiceFilters($allServices, $filters);
    }
    
    /**
     * Get grooming packages from all groomers
     */
    private function getGroomingPackages($filters) {
        // Mock packages from all groomers
        $allPackages = [
            // Isabella Martinez's packages
            [
                'id' => 201,
                'package_name' => 'Monthly Maintenance',
                'description' => '4 grooming sessions per month',
                'price' => 20000,
                'original_price' => 24000,
                'discount_percent' => 16.7,
                'services_included' => 'Bath, Brush, Nail Trim, Ear Cleaning',
                'duration' => '4 sessions',
                'validity' => '1 month',
                'for_dogs' => true,
                'for_cats' => false,
                'available' => true,
                'groomer_id' => 16,
                'groomer_name' => 'Isabella Martinez',
                'groomer_business' => 'Paws & Perfection Grooming',
                'groomer_city' => 'Colombo',
                'groomer_phone' => '555-0400',
                'groomer_avatar' => 'https://i.pravatar.cc/150?img=32'
            ],
            [
                'id' => 202,
                'package_name' => 'Show Dog Premium',
                'description' => 'Complete show preparation package',
                'price' => 35000,
                'original_price' => 42000,
                'discount_percent' => 16.7,
                'services_included' => 'Show cuts, styling, conditioning treatments',
                'duration' => '3 months',
                'validity' => '3 months',
                'for_dogs' => true,
                'for_cats' => false,
                'available' => true,
                'groomer_id' => 16,
                'groomer_name' => 'Isabella Martinez',
                'groomer_business' => 'Paws & Perfection Grooming',
                'groomer_city' => 'Colombo',
                'groomer_phone' => '555-0400',
                'groomer_avatar' => 'https://i.pravatar.cc/150?img=32'
            ],
            // Alexander Johnson's packages
            [
                'id' => 203,
                'package_name' => 'Puppy Starter Pack',
                'description' => '6 grooming sessions for puppies under 1 year',
                'price' => 15000,
                'original_price' => 18000,
                'discount_percent' => 16.7,
                'services_included' => 'Gentle grooming, nail trim, training',
                'duration' => '6 sessions',
                'validity' => '6 months',
                'for_dogs' => true,
                'for_cats' => false,
                'available' => true,
                'groomer_id' => 17,
                'groomer_name' => 'Alexander Johnson',
                'groomer_business' => 'Elite Breed Grooming Studio',
                'groomer_city' => 'Kandy',
                'groomer_phone' => '555-0401',
                'groomer_avatar' => 'https://i.pravatar.cc/150?img=70'
            ],
            [
                'id' => 204,
                'package_name' => 'Breed Standard Package',
                'description' => 'Year-round breed-specific grooming',
                'price' => 75000,
                'original_price' => 90000,
                'discount_percent' => 16.7,
                'services_included' => '12 breed-standard cuts, maintenance',
                'duration' => '1 year',
                'validity' => '1 year',
                'for_dogs' => true,
                'for_cats' => false,
                'available' => true,
                'groomer_id' => 17,
                'groomer_name' => 'Alexander Johnson',
                'groomer_business' => 'Elite Breed Grooming Studio',
                'groomer_city' => 'Kandy',
                'groomer_phone' => '555-0401',
                'groomer_avatar' => 'https://i.pravatar.cc/150?img=70'
            ],
            // Sophia Rodriguez's packages
            [
                'id' => 205,
                'package_name' => 'Cat Care Bundle',
                'description' => '3 months of complete cat grooming',
                'price' => 12000,
                'original_price' => 14400,
                'discount_percent' => 16.7,
                'services_included' => 'Bath, brush, nail trim, ear cleaning',
                'duration' => '3 months',
                'validity' => '3 months',
                'for_dogs' => false,
                'for_cats' => true,
                'available' => true,
                'groomer_id' => 18,
                'groomer_name' => 'Sophia Rodriguez',
                'groomer_business' => 'Whiskers & Wags Spa',
                'groomer_city' => 'Galle',
                'groomer_phone' => '555-0402',
                'groomer_avatar' => 'https://i.pravatar.cc/150?img=38'
            ],
            // Benjamin Clark's packages
            [
                'id' => 206,
                'package_name' => 'Quick Clean Package',
                'description' => '10 express grooming sessions',
                'price' => 22000,
                'original_price' => 25000,
                'discount_percent' => 12.0,
                'services_included' => 'Bath, brush, nail trim',
                'duration' => '10 sessions',
                'validity' => '3 months',
                'for_dogs' => true,
                'for_cats' => true,
                'available' => true,
                'groomer_id' => 19,
                'groomer_name' => 'Benjamin Clark',
                'groomer_business' => 'Quick Clips Pet Salon',
                'groomer_city' => 'Colombo',
                'groomer_phone' => '555-0403',
                'groomer_avatar' => 'https://i.pravatar.cc/150?img=11'
            ],
            // Charlotte Anderson's packages
            [
                'id' => 207,
                'package_name' => 'VIP Styling Package',
                'description' => 'Premium grooming with creative styling',
                'price' => 45000,
                'original_price' => 54000,
                'discount_percent' => 16.7,
                'services_included' => 'Creative cuts, coloring, spa treatments',
                'duration' => '6 months',
                'validity' => '6 months',
                'for_dogs' => true,
                'for_cats' => false,
                'available' => true,
                'groomer_id' => 20,
                'groomer_name' => 'Charlotte Anderson',
                'groomer_business' => 'Glamour Paws Boutique',
                'groomer_city' => 'Mount Lavinia',
                'groomer_phone' => '555-0404',
                'groomer_avatar' => 'https://i.pravatar.cc/150?img=43'
            ]
        ];
        
        return $this->applyServiceFilters($allPackages, $filters);
    }
    
    /**
     * Apply filters specifically for services/packages
     */
    private function applyServiceFilters($items, $filters) {
        $filtered = $items;
        
        // Apply groomer_id filter (show only this groomer's services)
        if (!empty($filters['groomer_id'])) {
            $filtered = array_filter($filtered, function($item) use ($filters) {
                return ($item['groomer_id'] ?? 0) == $filters['groomer_id'];
            });
        }
        
        // Apply search filter
        if (!empty($filters['search'])) {
            $search = strtolower($filters['search']);
            $filtered = array_filter($filtered, function($item) use ($search) {
                return stripos($item['service_name'] ?? $item['package_name'] ?? '', $search) !== false ||
                       stripos($item['description'] ?? '', $search) !== false ||
                       stripos($item['groomer_name'] ?? '', $search) !== false ||
                       stripos($item['groomer_business'] ?? '', $search) !== false ||
                       stripos($item['groomer_city'] ?? '', $search) !== false;
            });
        }
        
        // Apply city filter
        if (!empty($filters['city'])) {
            $filtered = array_filter($filtered, function($item) use ($filters) {
                return ($item['groomer_city'] ?? '') === $filters['city'];
            });
        }
        
        // Sort by price (low to high)
        usort($filtered, function($a, $b) {
            return $a['price'] - $b['price'];
        });
        
        return array_values($filtered);
    }
    
    /**
     * Apply filters to provider array
     */
    private function applyFilters($providers, $filters) {
        $filtered = $providers;
        
        // Apply search filter
        if (!empty($filters['search'])) {
            $search = strtolower($filters['search']);
            $filtered = array_filter($filtered, function($provider) use ($search) {
                return stripos($provider['name'], $search) !== false ||
                       stripos($provider['city'] ?? '', $search) !== false ||
                       stripos($provider['specialization'] ?? '', $search) !== false ||
                       stripos($provider['specializations'] ?? '', $search) !== false ||
                       stripos($provider['business_name'] ?? '', $search) !== false;
            });
        }
        
        // Apply city filter
        if (!empty($filters['city'])) {
            $filtered = array_filter($filtered, function($provider) use ($filters) {
                return ($provider['city'] ?? '') === $filters['city'];
            });
        }
        
        // Apply experience filter
        if (!empty($filters['experience'])) {
            $filtered = array_filter($filtered, function($provider) use ($filters) {
                return ($provider['experience_years'] ?? 0) >= (int)$filters['experience'];
            });
        }
        
        // Apply specialization filter (for trainers/groomers)
        if (!empty($filters['specialization'])) {
            $filtered = array_filter($filtered, function($provider) use ($filters) {
                $spec = $provider['specialization'] ?? $provider['specializations'] ?? '';
                return stripos($spec, $filters['specialization']) !== false;
            });
        }
        
        // Apply training type filter (for trainers)
        if (!empty($filters['training_type'])) {
            $filtered = array_filter($filtered, function($provider) use ($filters) {
                $trainingTypes = $provider['training_types'] ?? [];
                // Debug: uncomment to see what's being checked
                // error_log("Checking provider: " . $provider['name'] . " | Training types: " . implode(', ', $trainingTypes) . " | Looking for: " . $filters['training_type']);
                $result = in_array($filters['training_type'], $trainingTypes, true);
                return $result;
            });
        }
        
        // Apply pet type filter (for sitters)
        if (!empty($filters['pet_type'])) {
            $filtered = array_filter($filtered, function($provider) use ($filters) {
                return stripos($provider['pet_types'] ?? '', $filters['pet_type']) !== false;
            });
        }
        
        // Apply home type filter (for sitters)
        if (!empty($filters['home_type'])) {
            $filtered = array_filter($filtered, function($provider) use ($filters) {
                return ($provider['home_type'] ?? '') === $filters['home_type'];
            });
        }
        
        // Apply breed filter (for breeders)
        if (!empty($filters['breed'])) {
            $filtered = array_filter($filtered, function($provider) use ($filters) {
                return stripos($provider['specialization'] ?? '', $filters['breed']) !== false;
            });
        }
        
        // Apply gender filter (for breeders)
        if (!empty($filters['gender'])) {
            // This would filter by breeding pet gender in real implementation
            // For now, just return all since mock data doesn't have pet details
            $filtered = $filtered;
        }
        
        // Apply service type filter (for groomers)
        if (!empty($filters['service_type'])) {
            // In real implementation, would filter by single services vs packages
            // For now, return all
            $filtered = $filtered;
        }
        
        // Sort by experience (most experienced first)
        usort($filtered, function($a, $b) {
            return $b['experience_years'] - $a['experience_years'];
        });
        
        return array_values($filtered);
    }
    
    /**
     * Get all unique cities for filter dropdown (Mock Data)
     */
    public function getCities($serviceType) {
        // Mock cities data
        $allCities = ['Colombo', 'Kandy', 'Galle', 'Negombo', 'Mount Lavinia'];
        return $allCities;
    }
    
    /**
     * Get service provider details by ID (Mock Data)
     */
    public function getProviderDetails($providerId, $serviceType) {
        // Get all providers of this type
        $providers = $this->getServiceProviders($serviceType, []);
        
        // Find the provider by ID
        foreach ($providers as $provider) {
            if ($provider['id'] == $providerId) {
                return $provider;
            }
        }
        
        return null;
    }
}
