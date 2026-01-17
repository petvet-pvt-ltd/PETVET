<?php
/**
 * Registration Model
 * Handles database operations for user registration
 */

class RegistrationModel {
    
    private $db;
    
    public function __construct() {
        require_once __DIR__ . '/../config/connect.php';
        $this->db = db();
    }
    
    /**
     * Check if email already exists
     */
    public function emailExists($email) {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() !== false;
    }
    
    /**
     * Create new user
     */
    public function createUser($userData) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO users (email, password, first_name, last_name, phone, address, is_active, email_verified, created_at)
                VALUES (?, ?, ?, ?, ?, ?, 1, 1, NOW())
            ");
            
            $stmt->execute([
                $userData['email'],
                $userData['password'],
                $userData['first_name'],
                $userData['last_name'],
                $userData['phone'],
                $userData['address']
            ]);
            
            return $this->db->lastInsertId();
            
        } catch (PDOException $e) {
            error_log("Create user error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Assign roles to user
     */
    public function assignRoles($userId, $roles, $primaryRole) {
        try {
            $this->db->beginTransaction();
            
            foreach ($roles as $role) {
                // Get role ID
                $stmt = $this->db->prepare("SELECT id FROM roles WHERE role_name = ?");
                $stmt->execute([$role]);
                $roleData = $stmt->fetch();
                
                if (!$roleData) {
                    continue; // Skip if role doesn't exist
                }
                
                $roleId = $roleData['id'];
                $isPrimary = ($role === $primaryRole) ? 1 : 0;
                
                // Insert user_role with approved status (no admin verification needed)
                $stmt = $this->db->prepare("
                    INSERT INTO user_roles (user_id, role_id, is_primary, is_active, verification_status, applied_at)
                    VALUES (?, ?, ?, 1, 'approved', NOW())
                ");
                
                $stmt->execute([$userId, $roleId, $isPrimary]);
            }
            
            $this->db->commit();
            return true;
            
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Assign roles error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create role-specific profile
     */
    public function createRoleProfile($userId, $role, $roleData, $files = []) {
        switch ($role) {
            case 'pet_owner':
                return $this->createPetOwnerProfile($userId, $roleData);
            
            case 'trainer':
                return $this->createTrainerProfile($userId, $roleData, $files);
            
            case 'groomer':
                return $this->createGroomerProfile($userId, $roleData, $files);
            
            case 'sitter':
                return $this->createSitterProfile($userId, $roleData, $files);
            
            case 'breeder':
                return $this->createBreederProfile($userId, $roleData, $files);
            
            case 'vet':
                return $this->createVetProfile($userId, $roleData, $files);
            
            case 'clinic_manager':
                return $this->createClinicManagerProfile($userId, $roleData, $files);
            
            default:
                return true;
        }
    }
    
    /**
     * Create pet owner profile
     */
    private function createPetOwnerProfile($userId, $data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO pet_owner_profiles (user_id, created_at)
                VALUES (?, NOW())
            ");
            
            return $stmt->execute([$userId]);
            
        } catch (PDOException $e) {
            error_log("Create pet owner profile error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create trainer profile (service provider)
     */
    private function createTrainerProfile($userId, $data, $files) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO service_provider_profiles 
                (user_id, role_type, business_name, service_area, experience_years, certifications, bio, available, created_at)
                VALUES (?, 'trainer', ?, ?, ?, ?, ?, 1, NOW())
            ");
            
            $businessName = $data['specialization'] ?? 'Pet Training Services';
            $serviceArea = $data['service_area'] ?? '';
            $experience = $data['experience'] ?? 0;
            $certifications = $data['certifications'] ?? '';
            $bio = "Specialization: " . ($data['specialization'] ?? 'General Training');
            
            $stmt->execute([
                $userId,
                $businessName,
                $serviceArea,
                $experience,
                $certifications,
                $bio
            ]);
            
            // Save file document if provided
            if (!empty($files)) {
                $this->saveVerificationDocument($userId, 'trainer', $files);
            }
            
            return true;
            
        } catch (PDOException $e) {
            error_log("Create trainer profile error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create groomer profile (service provider)
     */
    private function createGroomerProfile($userId, $data, $files) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO service_provider_profiles 
                (user_id, role_type, business_name, service_area, experience_years, bio, available, created_at)
                VALUES (?, 'groomer', ?, '', ?, ?, 1, NOW())
            ");
            
            $businessName = $data['business_name'] ?? 'Pet Grooming Services';
            $experience = $data['experience'] ?? 0;
            $bio = "Services: " . ($data['services'] ?? '') . "\nPricing: " . ($data['pricing'] ?? '');
            
            $stmt->execute([
                $userId,
                $businessName,
                $experience,
                $bio
            ]);
            
            // Save file document if provided
            if (!empty($files)) {
                $this->saveVerificationDocument($userId, 'groomer', $files);
            }
            
            return true;
            
        } catch (PDOException $e) {
            error_log("Create groomer profile error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create sitter profile (service provider)
     */
    private function createSitterProfile($userId, $data, $files) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO service_provider_profiles 
                (user_id, role_type, service_area, experience_years, bio, available, created_at)
                VALUES (?, 'sitter', '', ?, ?, 1, NOW())
            ");
            
            $experience = $data['experience'] ?? 0;
            $bio = "Home Type: " . ($data['home_type'] ?? '') . 
                   "\nPet Types: " . ($data['pet_types'] ?? '') . 
                   "\nMax Pets: " . ($data['max_pets'] ?? '1') .
                   "\nOvernight: " . ($data['overnight'] === '1' ? 'Yes' : 'No');
            
            $stmt->execute([
                $userId,
                $experience,
                $bio
            ]);
            
            return true;
            
        } catch (PDOException $e) {
            error_log("Create sitter profile error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create breeder profile (service provider)
     */
    private function createBreederProfile($userId, $data, $files) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO service_provider_profiles 
                (user_id, role_type, business_name, experience_years, certifications, bio, available, created_at)
                VALUES (?, 'breeder', ?, ?, ?, ?, 1, NOW())
            ");
            
            $businessName = 'Breeding: ' . ($data['breeds'] ?? 'Various Breeds');
            $experience = $data['experience'] ?? 0;
            $certifications = $data['kennel_registration'] ?? '';
            $bio = "Breeds: " . ($data['breeds'] ?? '') . 
                   "\nPhilosophy: " . ($data['philosophy'] ?? '');
            
            $stmt->execute([
                $userId,
                $businessName,
                $experience,
                $certifications,
                $bio
            ]);
            
            // Save file document if provided
            if (!empty($files)) {
                $this->saveVerificationDocument($userId, 'breeder', $files);
            }
            
            return true;
            
        } catch (PDOException $e) {
            error_log("Create breeder profile error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create veterinarian profile
     */
    private function createVetProfile($userId, $data, $files) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO vet_profiles 
                (user_id, clinic_id, license_number, specialization, years_experience, available, created_at)
                VALUES (?, ?, ?, ?, ?, 1, NOW())
            ");
            
            $clinicId = !empty($data['clinic_id']) ? $data['clinic_id'] : null;
            $licenseNumber = $data['license_number'] ?? 'PENDING';
            $specialization = $data['specialization'] ?? '';
            $experience = $data['experience'] ?? 0;
            
            $stmt->execute([
                $userId,
                $clinicId,
                $licenseNumber,
                $specialization,
                $experience
            ]);
            
            // Save medical license document if provided
            if (!empty($files)) {
                $this->saveVerificationDocument($userId, 'vet', $files);
            }
            
            return true;
            
        } catch (PDOException $e) {
            error_log("Create vet profile error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create clinic manager profile
     */
    private function createClinicManagerProfile($userId, $data, $files) {
        try {
            $this->db->beginTransaction();
            
            // First, create the clinic
            $stmt = $this->db->prepare("
                INSERT INTO clinics 
                (clinic_name, clinic_address, district, clinic_phone, clinic_email, license_document, map_location, verification_status, is_active, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', 0, NOW())
            ");
            
            $licenseDocument = null;
            if (!empty($files)) {
                $licenseDocument = $this->saveVerificationDocument($userId, 'clinic_manager', $files);
            }
            
            // Prepare map_location as "latitude, longitude"
            $mapLocation = null;
            if (!empty($data['latitude']) && !empty($data['longitude'])) {
                $mapLocation = $data['latitude'] . ', ' . $data['longitude'];
            }
            
            $stmt->execute([
                $data['clinic_name'] ?? '',
                $data['clinic_address'] ?? '',
                $data['district'] ?? '',
                $data['clinic_phone'] ?? '',
                $data['clinic_email'] ?? '',
                $licenseDocument,
                $mapLocation
            ]);
            
            $clinicId = $this->db->lastInsertId();
            
            // Then, create the clinic manager profile
            $stmt = $this->db->prepare("
                INSERT INTO clinic_manager_profiles 
                (user_id, clinic_id, position, created_at)
                VALUES (?, ?, 'Manager', NOW())
            ");
            
            $stmt->execute([
                $userId,
                $clinicId
            ]);
            
            $this->db->commit();
            return true;
            
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Create clinic manager profile error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Save verification document
     */
    private function saveVerificationDocument($userId, $role, $fileData) {
        try {
            // First, get the user_role id
            $stmt = $this->db->prepare("
                SELECT ur.id 
                FROM user_roles ur
                JOIN roles r ON ur.role_id = r.id
                WHERE ur.user_id = ? AND r.role_name = ?
            ");
            $stmt->execute([$userId, $role]);
            $userRoleData = $stmt->fetch();
            
            if (!$userRoleData) {
                return false;
            }
            
            $userRoleId = $userRoleData['id'];
            
            // Insert document record
            $stmt = $this->db->prepare("
                INSERT INTO role_verification_documents 
                (user_role_id, document_type, document_name, file_path, file_size, mime_type, uploaded_at)
                VALUES (?, 'license', ?, ?, ?, 'application/pdf', NOW())
            ");
            
            return $stmt->execute([
                $userRoleId,
                $fileData['original_name'],
                $fileData['path'],
                $fileData['size']
            ]);
            
        } catch (PDOException $e) {
            error_log("Save verification document error: " . $e->getMessage());
            return false;
        }
    }
}
