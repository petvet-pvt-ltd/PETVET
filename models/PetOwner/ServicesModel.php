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
     * Get trainers with filters (Real Data from Database)
     */
    private function getTrainers($filters) {
        $pdo = db();
        
        // Build SQL query
        $sql = "SELECT 
                    u.id,
                    CONCAT(u.first_name, ' ', u.last_name) as name,
                    u.email,
                    u.phone,
                    u.avatar,
                    u.address,
                    spp.business_name,
                    spp.service_area,
                    spp.experience_years,
                    spp.certifications,
                    spp.specializations as specialization,
                    spp.bio,
                    spp.phone_primary,
                    spp.phone_secondary,
                    spp.training_basic_enabled,
                    spp.training_basic_charge,
                    spp.training_intermediate_enabled,
                    spp.training_intermediate_charge,
                    spp.training_advanced_enabled,
                    spp.training_advanced_charge
                FROM users u
                INNER JOIN service_provider_profiles spp ON u.id = spp.user_id
                INNER JOIN user_roles ur ON u.id = ur.user_id
                INNER JOIN roles r ON ur.role_id = r.id
                WHERE r.role_name = 'trainer' 
                AND ur.verification_status = 'approved'
                AND ur.is_active = 1
                AND spp.role_type = 'trainer'
                AND COALESCE(TRIM(spp.business_name), '') <> ''
                AND COALESCE(TRIM(spp.service_area), '') <> ''
                AND spp.experience_years IS NOT NULL
                AND COALESCE(TRIM(spp.specializations), '') <> ''
                AND COALESCE(TRIM(spp.phone_primary), '') <> ''
                AND (
                    spp.training_basic_enabled = 1
                    OR spp.training_intermediate_enabled = 1
                    OR spp.training_advanced_enabled = 1
                )";
        
        // Apply filters
        $params = [];
        
        if (!empty($filters['search'])) {
            $sql .= " AND (u.first_name LIKE ? OR u.last_name LIKE ? OR spp.business_name LIKE ? OR spp.service_area LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (!empty($filters['city'])) {
            $sql .= " AND spp.service_area LIKE ?";
            $params[] = '%' . $filters['city'] . '%';
        }
        
        if (!empty($filters['experience'])) {
            $sql .= " AND spp.experience_years >= ?";
            $params[] = (int)$filters['experience'];
        }
        
        if (!empty($filters['specialization'])) {
            $sql .= " AND spp.specializations LIKE ?";
            $params[] = '%' . $filters['specialization'] . '%';
        }
        
        if (!empty($filters['training_type'])) {
            $trainingType = $filters['training_type'];
            if ($trainingType === 'Basic') {
                $sql .= " AND spp.training_basic_enabled = 1";
            } elseif ($trainingType === 'Intermediate') {
                $sql .= " AND spp.training_intermediate_enabled = 1";
            } elseif ($trainingType === 'Advanced') {
                $sql .= " AND spp.training_advanced_enabled = 1";
            }
        }
        
        $sql .= " ORDER BY spp.experience_years DESC, u.first_name";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $trainers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Process trainers to add training_types array based on enabled flags
        foreach ($trainers as &$trainer) {
            // Normalize service areas: service_area can be JSON array string (new) or comma string (legacy)
            $trainer['service_areas'] = [];
            if (!empty($trainer['service_area'])) {
                $areaStr = trim((string)$trainer['service_area']);
                if (str_starts_with($areaStr, '[')) {
                    $decoded = json_decode($areaStr, true);
                    if (is_array($decoded)) {
                        $trainer['service_areas'] = array_values(array_filter(array_map(function($v){
                            $v = trim((string)$v);
                            return $v === '' ? null : $v;
                        }, $decoded)));
                    }
                }
                if (empty($trainer['service_areas'])) {
                    $parts = array_map('trim', explode(',', $areaStr));
                    $trainer['service_areas'] = array_values(array_filter($parts, fn($p) => $p !== ''));
                }
            }

            $training_types = [];
            if ($trainer['training_basic_enabled']) {
                $training_types[] = 'Basic';
            }
            if ($trainer['training_intermediate_enabled']) {
                $training_types[] = 'Intermediate';
            }
            if ($trainer['training_advanced_enabled']) {
                $training_types[] = 'Advanced';
            }
            $trainer['training_types'] = $training_types;
            
            // For compact UI meta, keep a single "city" value as the first working area
            $trainer['city'] = $trainer['service_areas'][0] ?? '';
            
            // Set default avatar if none exists
            if (empty($trainer['avatar'])) {
                $trainer['avatar'] = '/PETVET/public/images/emptyProfPic.png';
            }
        }
        
        return $trainers;
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
        // When show=services/packages, return items instead of providers
        if (!empty($filters['show']) && $filters['show'] === 'services') {
            return $this->getGroomerServiceItems($filters);
        }
        if (!empty($filters['show']) && $filters['show'] === 'packages') {
            return $this->getGroomerPackageItems($filters);
        }

        return $this->getGroomerProviders($filters);
    }
    
    /**
     * Providers view: only approved groomers with >= 1 service in groomer_services
     */
    private function getGroomerProviders($filters) {
        $pdo = db();

        $sql = "SELECT
                    u.id,
                    CONCAT(u.first_name, ' ', u.last_name) AS name,
                    u.avatar,
                    u.email,
                    u.phone,
                    spp.business_name,
                    spp.business_logo,
                    spp.service_area,
                    spp.experience_years,
                    spp.certifications,
                    spp.specializations,
                    spp.phone_primary,
                    spp.phone_secondary
                FROM users u
                INNER JOIN service_provider_profiles spp ON u.id = spp.user_id
                INNER JOIN user_roles ur ON u.id = ur.user_id
                INNER JOIN roles r ON ur.role_id = r.id
                WHERE r.role_name = 'groomer'
                  AND ur.verification_status = 'approved'
                  AND ur.is_active = 1
                  AND spp.role_type = 'groomer'
                  AND EXISTS (
                      SELECT 1
                      FROM groomer_services gs
                      WHERE gs.user_id = u.id
                  )";

        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (
                        u.first_name LIKE ? OR u.last_name LIKE ? OR spp.business_name LIKE ? OR spp.service_area LIKE ?
                     )";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($filters['city'])) {
            $sql .= " AND spp.service_area LIKE ?";
            $params[] = '%' . $filters['city'] . '%';
        }

        if (!empty($filters['experience'])) {
            $sql .= " AND spp.experience_years >= ?";
            $params[] = (int)$filters['experience'];
        }

        $sql .= " ORDER BY COALESCE(spp.experience_years, 0) DESC, u.first_name";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $groomers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($groomers as &$groomer) {
            $areas = $this->parseServiceAreas($groomer['service_area'] ?? '');
            $groomer['service_areas'] = $areas;
            $groomer['city'] = $areas[0] ?? '';
            if (empty($groomer['avatar'])) {
                $groomer['avatar'] = '/PETVET/public/images/emptyProfPic.png';
            }

            $logo = trim((string)($groomer['business_logo'] ?? ''));
            if ($logo === '') {
                $groomer['business_logo'] = '';
            }
        }

        return $groomers;
    }

    /**
     * Services view: single services across groomers (or one groomer via groomer_id)
     */
    private function getGroomerServiceItems($filters) {
        $pdo = db();

        $sql = "SELECT
                    gs.id,
                    gs.name AS service_name,
                    gs.description,
                    gs.price,
                    gs.duration,
                    gs.for_dogs,
                    gs.for_cats,
                    gs.available,
                    u.id AS groomer_id,
                    CONCAT(u.first_name, ' ', u.last_name) AS groomer_name,
                    spp.business_name AS groomer_business,
                    spp.service_area AS groomer_service_area,
                    spp.phone_primary AS groomer_phone,
                                        COALESCE(NULLIF(spp.business_logo, ''), u.avatar) AS groomer_avatar
                FROM groomer_services gs
                INNER JOIN users u ON gs.user_id = u.id
                INNER JOIN service_provider_profiles spp ON u.id = spp.user_id AND spp.role_type = 'groomer'
                INNER JOIN user_roles ur ON u.id = ur.user_id
                INNER JOIN roles r ON ur.role_id = r.id
                WHERE r.role_name = 'groomer'
                  AND ur.verification_status = 'approved'
                  AND ur.is_active = 1";

        $params = [];

        if (!empty($filters['groomer_id'])) {
            $sql .= " AND u.id = ?";
            $params[] = (int)$filters['groomer_id'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (
                        gs.name LIKE ? OR gs.description LIKE ? OR spp.business_name LIKE ? OR spp.service_area LIKE ?
                     )";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($filters['city'])) {
            $sql .= " AND spp.service_area LIKE ?";
            $params[] = '%' . $filters['city'] . '%';
        }

        if (!empty($filters['experience'])) {
            $sql .= " AND spp.experience_years >= ?";
            $params[] = (int)$filters['experience'];
        }

        // Optional price filtering if passed
        if (!empty($filters['min_price'])) {
            $sql .= " AND gs.price >= ?";
            $params[] = (float)$filters['min_price'];
        }
        if (!empty($filters['max_price'])) {
            $sql .= " AND gs.price <= ?";
            $params[] = (float)$filters['max_price'];
        }

        $sql .= " ORDER BY gs.price ASC, gs.created_at DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$row) {
            $row['for_dogs'] = (bool)$row['for_dogs'];
            $row['for_cats'] = (bool)$row['for_cats'];
            $row['available'] = (bool)$row['available'];
            $row['price'] = (float)$row['price'];

            $areas = $this->parseServiceAreas($row['groomer_service_area'] ?? '');
            $row['groomer_city'] = $areas[0] ?? '';

            if (empty($row['groomer_avatar'])) {
                $row['groomer_avatar'] = '/PETVET/public/images/emptyProfPic.png';
            }
        }

        return $rows;
    }

    /**
     * Packages view: packages across groomers (or one groomer via groomer_id)
     */
    private function getGroomerPackageItems($filters) {
        $pdo = db();

        $sql = "SELECT
                    gp.id,
                    gp.name AS package_name,
                    gp.description,
                    gp.original_price,
                    gp.discounted_price AS price,
                    COALESCE(
                        gp.discount_percent,
                        CASE
                            WHEN gp.original_price > 0 THEN ROUND((1 - (gp.discounted_price / gp.original_price)) * 100, 1)
                            ELSE 0
                        END
                    ) AS discount_percent,
                    gp.duration,
                    gp.for_dogs,
                    gp.for_cats,
                    gp.available,
                    u.id AS groomer_id,
                    CONCAT(u.first_name, ' ', u.last_name) AS groomer_name,
                    spp.business_name AS groomer_business,
                    spp.service_area AS groomer_service_area,
                    spp.phone_primary AS groomer_phone,
                    COALESCE(NULLIF(spp.business_logo, ''), u.avatar) AS groomer_avatar,
                    (
                        SELECT GROUP_CONCAT(gs.name ORDER BY gs.name SEPARATOR ', ')
                        FROM groomer_package_services gps
                        INNER JOIN groomer_services gs ON gps.service_id = gs.id
                        WHERE gps.package_id = gp.id
                    ) AS services_included
                FROM groomer_packages gp
                INNER JOIN users u ON gp.user_id = u.id
                INNER JOIN service_provider_profiles spp ON u.id = spp.user_id AND spp.role_type = 'groomer'
                INNER JOIN user_roles ur ON u.id = ur.user_id
                INNER JOIN roles r ON ur.role_id = r.id
                WHERE r.role_name = 'groomer'
                  AND ur.verification_status = 'approved'
                  AND ur.is_active = 1";

        $params = [];

        if (!empty($filters['groomer_id'])) {
            $sql .= " AND u.id = ?";
            $params[] = (int)$filters['groomer_id'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (
                        gp.name LIKE ? OR gp.description LIKE ? OR spp.business_name LIKE ? OR spp.service_area LIKE ?
                     )";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($filters['city'])) {
            $sql .= " AND spp.service_area LIKE ?";
            $params[] = '%' . $filters['city'] . '%';
        }

        if (!empty($filters['experience'])) {
            $sql .= " AND spp.experience_years >= ?";
            $params[] = (int)$filters['experience'];
        }

        // Optional price filtering: compare against discounted price
        if (!empty($filters['min_price'])) {
            $sql .= " AND gp.discounted_price >= ?";
            $params[] = (float)$filters['min_price'];
        }
        if (!empty($filters['max_price'])) {
            $sql .= " AND gp.discounted_price <= ?";
            $params[] = (float)$filters['max_price'];
        }

        $sql .= " ORDER BY gp.discounted_price ASC, gp.created_at DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$row) {
            $row['for_dogs'] = (bool)$row['for_dogs'];
            $row['for_cats'] = (bool)$row['for_cats'];
            $row['available'] = (bool)$row['available'];
            $row['original_price'] = (float)$row['original_price'];
            $row['price'] = (float)$row['price'];
            $row['discount_percent'] = (float)$row['discount_percent'];

            $areas = $this->parseServiceAreas($row['groomer_service_area'] ?? '');
            $row['groomer_city'] = $areas[0] ?? '';

            if (empty($row['groomer_avatar'])) {
                $row['groomer_avatar'] = '/PETVET/public/images/emptyProfPic.png';
            }
        }

        return $rows;
    }

    /**
     * Normalize service_area into array of areas (supports JSON arrays or comma strings)
     */
    private function parseServiceAreas($serviceArea) {
        $areas = [];
        $areaStr = trim((string)$serviceArea);
        if ($areaStr === '') return $areas;

        if (str_starts_with($areaStr, '[')) {
            $decoded = json_decode($areaStr, true);
            if (is_array($decoded)) {
                $areas = array_values(array_filter(array_map(function ($v) {
                    $v = trim((string)$v);
                    return $v === '' ? null : $v;
                }, $decoded)));
                if (!empty($areas)) {
                    return $areas;
                }
            }
        }

        $parts = array_map('trim', explode(',', $areaStr));
        return array_values(array_filter($parts, fn($p) => $p !== ''));
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
     * Get all unique cities for filter dropdown (Real Data from Database)
     */
    public function getCities($serviceType) {
        $pdo = db();
        
        $roleMap = [
            'trainers' => 'trainer',
            'sitters' => 'sitter',
            'breeders' => 'breeder',
            'groomers' => 'groomer'
        ];
        
        $roleType = $roleMap[$serviceType] ?? 'trainer';
        
        // Extract cities from service_area field - ONLY from providers that are publicly visible
        // Use the SAME visibility rules as the public listing
        $sql = "SELECT DISTINCT spp.service_area 
                FROM service_provider_profiles spp
                INNER JOIN users u ON spp.user_id = u.id
                INNER JOIN user_roles ur ON u.id = ur.user_id
                INNER JOIN roles r ON ur.role_id = r.id
                WHERE r.role_name = ? 
                AND ur.verification_status = 'approved'
                AND ur.is_active = 1
                AND spp.role_type = ?
                AND spp.service_area IS NOT NULL 
                AND spp.service_area != ''";
        
        // Apply service-specific visibility requirements
        if ($roleType === 'trainer') {
            $sql .= " AND COALESCE(TRIM(spp.business_name), '') <> ''
                     AND spp.experience_years IS NOT NULL
                     AND COALESCE(TRIM(spp.specializations), '') <> ''
                     AND COALESCE(TRIM(spp.phone_primary), '') <> ''
                     AND (
                         spp.training_basic_enabled = 1
                         OR spp.training_intermediate_enabled = 1
                         OR spp.training_advanced_enabled = 1
                     )";
        }

        if ($roleType === 'groomer') {
            // Only list districts for groomers who actually have at least one service
            $sql .= " AND EXISTS (SELECT 1 FROM groomer_services gs WHERE gs.user_id = spp.user_id)";
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$roleType, $roleType]);
        $results = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Extract city/district names from service_area. Supports JSON arrays or comma strings.
        $cities = [];
        foreach ($results as $area) {
            $areaStr = trim((string)$area);
            if ($areaStr === '') continue;

            if (str_starts_with($areaStr, '[')) {
                $decoded = json_decode($areaStr, true);
                if (is_array($decoded)) {
                    foreach ($decoded as $d) {
                        $d = trim((string)$d);
                        if ($d !== '' && !in_array($d, $cities)) {
                            $cities[] = $d;
                        }
                    }
                    continue;
                }
            }

            $parts = explode(',', $areaStr);
            $city = trim($parts[0]);
            if (!empty($city) && !in_array($city, $cities)) {
                $cities[] = $city;
            }
        }
        
        sort($cities);
        return $cities;
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
