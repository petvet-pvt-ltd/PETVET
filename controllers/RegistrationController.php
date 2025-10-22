<?php
/**
 * Registration Controller
 * Handles multi-role user registration
 */

class RegistrationController {
    
    private $model;
    private $errors = [];
    
    public function __construct() {
        require_once __DIR__ . '/../models/RegistrationModel.php';
        $this->model = new RegistrationModel();
    }
    
    /**
     * Main registration handler
     */
    public function register() {
        // Add debug logging
        error_log("=== Registration Started ===");
        error_log("POST data: " . print_r($_POST, true));
        error_log("FILES data: " . print_r($_FILES, true));
        
        try {
            // Validate input
            if (!$this->validateInput()) {
                error_log("Validation failed: " . print_r($this->errors, true));
                $this->redirectWithErrors();
                return;
            }
            
            // Extract and sanitize data
            $userData = $this->extractUserData();
            error_log("User data extracted: " . print_r($userData, true));
            
            $roles = $this->extractRoles();
            error_log("Roles extracted: " . print_r($roles, true));
            
            $roleData = $this->extractRoleSpecificData();
            error_log("Role data extracted: " . print_r($roleData, true));
            
            // Validate at least one role selected
            if (empty($roles)) {
                $this->errors[] = "Please select at least one role";
                error_log("No roles selected");
                $this->redirectWithErrors();
                return;
            }
            
            // Handle file uploads
            $uploadedFiles = $this->handleFileUploads($roles);
            error_log("Files uploaded: " . print_r($uploadedFiles, true));
            
            // Create user in database
            $userId = $this->model->createUser($userData);
            error_log("User created with ID: " . $userId);
            
            if (!$userId) {
                $this->errors[] = "Failed to create user account";
                error_log("Failed to create user");
                $this->redirectWithErrors();
                return;
            }
            
            // Assign roles (all approved, no admin verification needed for now)
            $primaryRole = $roles[0]; // First selected role is primary
            $rolesAssigned = $this->model->assignRoles($userId, $roles, $primaryRole);
            error_log("Roles assigned: " . ($rolesAssigned ? 'success' : 'failed'));
            
            if (!$rolesAssigned) {
                $this->errors[] = "Failed to assign roles";
                $this->redirectWithErrors();
                return;
            }
            
            // Create role-specific profiles
            foreach ($roles as $role) {
                $roleInfo = $roleData[$role] ?? [];
                $files = $uploadedFiles[$role] ?? [];
                error_log("Creating profile for role: $role");
                $this->model->createRoleProfile($userId, $role, $roleInfo, $files);
            }
            
            // Success - redirect to login with success message
            error_log("=== Registration Successful ===");
            $_SESSION['registration_success'] = true;
            $_SESSION['registered_email'] = $userData['email'];
            header('Location: /PETVET/index.php?module=guest&page=login&registered=1');
            exit;
            
        } catch (Exception $e) {
            error_log("Registration exception: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $this->errors[] = "Registration failed. Please try again.";
            $this->redirectWithErrors();
        }
    }
    
    /**
     * Validate form input
     */
    private function validateInput() {
        // Base required fields
        $required = ['fname', 'lname', 'email', 'password', 'confirm_password'];
        
        // Add role-specific required fields
        $roles = $_POST['roles'] ?? [];
        
        // For regular registration, require address
        if (!in_array('vet', $roles) && !in_array('clinic_manager', $roles)) {
            $required[] = 'address';
        }
        
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                $this->errors[] = "Please fill in all required fields ($field missing)";
                return false;
            }
        }
        
        // Validate email format
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "Invalid email format";
            return false;
        }
        
        // Check if email already exists
        if ($this->model->emailExists($_POST['email'])) {
            $this->errors[] = "Email already registered";
            return false;
        }
        
        // Validate password match
        if ($_POST['password'] !== $_POST['confirm_password']) {
            $this->errors[] = "Passwords do not match";
            return false;
        }
        
        // Validate password length
        if (strlen($_POST['password']) < 6) {
            $this->errors[] = "Password must be at least 6 characters";
            return false;
        }
        
        return true;
    }
    
    /**
     * Extract user data from POST
     */
    private function extractUserData() {
        return [
            'email' => filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
            'password' => password_hash($_POST['password'], PASSWORD_BCRYPT),
            'first_name' => htmlspecialchars(trim($_POST['fname'])),
            'last_name' => htmlspecialchars(trim($_POST['lname'])),
            'phone' => htmlspecialchars(trim($_POST['phone'] ?? '')),
            'address' => htmlspecialchars(trim($_POST['address'] ?? '')) // Make optional for vet/clinic_manager
        ];
    }
    
    /**
     * Extract selected roles
     */
    private function extractRoles() {
        return $_POST['roles'] ?? [];
    }
    
    /**
     * Extract role-specific data
     */
    private function extractRoleSpecificData() {
        $roleData = [];
        
        // Pet Owner
        if (in_array('pet_owner', $_POST['roles'] ?? [])) {
            $roleData['pet_owner'] = [
                'info' => $_POST['pet_owner_info'] ?? ''
            ];
        }
        
        // Trainer
        if (in_array('trainer', $_POST['roles'] ?? [])) {
            $roleData['trainer'] = [
                'specialization' => $_POST['trainer_specialization'] ?? '',
                'experience' => $_POST['trainer_experience'] ?? 0,
                'hourly_rate' => $_POST['trainer_hourly_rate'] ?? null,
                'service_area' => $_POST['trainer_service_area'] ?? '',
                'certifications' => $_POST['trainer_certifications'] ?? ''
            ];
        }
        
        // Groomer
        if (in_array('groomer', $_POST['roles'] ?? [])) {
            $roleData['groomer'] = [
                'experience' => $_POST['groomer_experience'] ?? 0,
                'business_name' => $_POST['groomer_business_name'] ?? '',
                'services' => $_POST['groomer_services'] ?? '',
                'pricing' => $_POST['groomer_pricing'] ?? ''
            ];
        }
        
        // Sitter
        if (in_array('sitter', $_POST['roles'] ?? [])) {
            $roleData['sitter'] = [
                'home_type' => $_POST['sitter_home_type'] ?? '',
                'daily_rate' => $_POST['sitter_daily_rate'] ?? null,
                'max_pets' => $_POST['sitter_max_pets'] ?? 1,
                'experience' => $_POST['sitter_experience'] ?? 0,
                'pet_types' => $_POST['sitter_pet_types'] ?? '',
                'overnight' => $_POST['sitter_overnight'] ?? '0'
            ];
        }
        
        // Breeder
        if (in_array('breeder', $_POST['roles'] ?? [])) {
            $roleData['breeder'] = [
                'breeds' => $_POST['breeder_breeds'] ?? '',
                'experience' => $_POST['breeder_experience'] ?? 0,
                'kennel_registration' => $_POST['breeder_kennel_registration'] ?? '',
                'philosophy' => $_POST['breeder_philosophy'] ?? ''
            ];
        }
        
        // Veterinarian
        if (in_array('vet', $_POST['roles'] ?? [])) {
            $roleData['vet'] = [
                'specialization' => $_POST['vet_specialization'] ?? '',
                'experience' => $_POST['vet_experience'] ?? 0,
                'clinic_id' => $_POST['vet_clinic_id'] ?? null,
                'license_number' => $_POST['vet_license_number'] ?? ''
            ];
        }
        
        // Clinic Manager
        if (in_array('clinic_manager', $_POST['roles'] ?? [])) {
            $roleData['clinic_manager'] = [
                'clinic_name' => $_POST['clinic_name'] ?? '',
                'clinic_address' => $_POST['clinic_address'] ?? '',
                'district' => $_POST['district'] ?? '',
                'clinic_phone' => $_POST['clinic_phone'] ?? '',
                'clinic_email' => $_POST['clinic_email'] ?? ''
            ];
        }
        
        return $roleData;
    }
    
    /**
     * Handle file uploads for role documents
     */
    private function handleFileUploads($roles) {
        $uploadedFiles = [];
        $uploadDir = __DIR__ . '/../uploads/verification_documents/';
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        foreach ($roles as $role) {
            $fileInputName = $role . '_license';
            
            if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES[$fileInputName];
                
                // Validate file type
                if ($file['type'] !== 'application/pdf') {
                    continue; // Skip non-PDF files
                }
                
                // Validate file size (5MB max)
                if ($file['size'] > 5 * 1024 * 1024) {
                    continue; // Skip files over 5MB
                }
                
                // Generate unique filename
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '_' . time() . '.' . $extension;
                $destination = $uploadDir . $filename;
                
                // Move uploaded file
                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    $uploadedFiles[$role] = [
                        'filename' => $filename,
                        'original_name' => $file['name'],
                        'path' => 'uploads/verification_documents/' . $filename,
                        'size' => $file['size']
                    ];
                }
            }
        }
        
        return $uploadedFiles;
    }
    
    /**
     * Redirect back with errors
     */
    private function redirectWithErrors() {
        $_SESSION['registration_errors'] = $this->errors;
        $_SESSION['registration_data'] = $_POST;
        
        // Determine which registration page to redirect to based on the role
        $roles = $_POST['roles'] ?? [];
        
        if (in_array('vet', $roles) && count($roles) === 1) {
            // Single vet registration
            header('Location: /PETVET/index.php?module=guest&page=vet-register&error=1');
        } elseif (in_array('clinic_manager', $roles) && count($roles) === 1) {
            // Single clinic manager registration
            header('Location: /PETVET/index.php?module=guest&page=clinic-manager-register&error=1');
        } else {
            // Multi-role registration or default
            header('Location: /PETVET/index.php?module=guest&page=register&error=1');
        }
        
        exit;
    }
}
