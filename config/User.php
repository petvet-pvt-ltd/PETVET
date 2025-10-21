<?php
/**
 * User Model
 * Handles user data operations, profile management, and role assignments
 */

require_once __DIR__ . '/connect.php';

class User {
    private PDO $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    /**
     * Get user by ID
     */
    public function getUserById(int $userId): ?array {
        $stmt = $this->db->prepare("
            SELECT id, email, first_name, last_name, phone, address, avatar,
                   email_verified, is_active, is_blocked, last_login, created_at, updated_at
            FROM users 
            WHERE id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: null;
    }
    
    /**
     * Get user by email
     */
    public function getUserByEmail(string $email): ?array {
        $stmt = $this->db->prepare("
            SELECT id, email, first_name, last_name, phone, address, avatar,
                   email_verified, is_active, is_blocked, last_login, created_at
            FROM users 
            WHERE email = ?
        ");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }
    
    /**
     * Get user's roles
     */
    public function getUserRoles(int $userId): array {
        $stmt = $this->db->prepare("
            SELECT ur.id, ur.role_id, ur.is_primary, ur.is_active, 
                   ur.verification_status, ur.verified_at, ur.applied_at,
                   r.role_name, r.role_display_name, r.description
            FROM user_roles ur
            JOIN roles r ON ur.role_id = r.id
            WHERE ur.user_id = ?
            ORDER BY ur.is_primary DESC, ur.applied_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Update user profile
     */
    public function updateProfile(int $userId, array $data): array {
        try {
            $fields = [];
            $values = [];
            
            $allowedFields = ['first_name', 'last_name', 'phone', 'address', 'avatar'];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $fields[] = "$field = ?";
                    $values[] = $data[$field];
                }
            }
            
            if (empty($fields)) {
                return ['success' => false, 'message' => 'No data to update'];
            }
            
            $values[] = $userId;
            $sql = "UPDATE users SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($values);
            
            return ['success' => true, 'message' => 'Profile updated successfully'];
        } catch (Exception $e) {
            error_log("Profile update error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to update profile'];
        }
    }
    
    /**
     * Change user password
     */
    public function changePassword(int $userId, string $currentPassword, string $newPassword): array {
        try {
            // Validate new password
            if (strlen($newPassword) < 6) {
                return ['success' => false, 'message' => 'Password must be at least 6 characters'];
            }
            
            // Get current password hash
            $stmt = $this->db->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            
            if (!$user) {
                return ['success' => false, 'message' => 'User not found'];
            }
            
            // Verify current password
            if (!password_verify($currentPassword, $user['password'])) {
                return ['success' => false, 'message' => 'Current password is incorrect'];
            }
            
            // Update password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$hashedPassword, $userId]);
            
            return ['success' => true, 'message' => 'Password changed successfully'];
        } catch (Exception $e) {
            error_log("Password change error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to change password'];
        }
    }
    
    /**
     * Add role to user
     */
    public function addRole(int $userId, int $roleId, array $profileData = []): array {
        try {
            // Check if user already has this role
            $stmt = $this->db->prepare("SELECT id FROM user_roles WHERE user_id = ? AND role_id = ?");
            $stmt->execute([$userId, $roleId]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'User already has this role'];
            }
            
            // Check if role requires verification
            $stmt = $this->db->prepare("SELECT requires_verification FROM roles WHERE id = ?");
            $stmt->execute([$roleId]);
            $role = $stmt->fetch();
            $requiresVerification = $role ? (bool)$role['requires_verification'] : false;
            
            // Add role
            $stmt = $this->db->prepare("
                INSERT INTO user_roles (user_id, role_id, is_primary, is_active, verification_status) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $userId,
                $roleId,
                false, // Not primary by default
                true,
                $requiresVerification ? 'pending' : 'approved'
            ]);
            
            return [
                'success' => true, 
                'message' => $requiresVerification 
                    ? 'Role added successfully. Pending verification.' 
                    : 'Role added successfully',
                'requires_verification' => $requiresVerification
            ];
        } catch (Exception $e) {
            error_log("Add role error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to add role'];
        }
    }
    
    /**
     * Remove role from user
     */
    public function removeRole(int $userId, int $roleId): array {
        try {
            // Check if it's the primary role
            $stmt = $this->db->prepare("
                SELECT is_primary FROM user_roles WHERE user_id = ? AND role_id = ?
            ");
            $stmt->execute([$userId, $roleId]);
            $userRole = $stmt->fetch();
            
            if (!$userRole) {
                return ['success' => false, 'message' => 'User does not have this role'];
            }
            
            if ($userRole['is_primary']) {
                return ['success' => false, 'message' => 'Cannot remove primary role'];
            }
            
            // Remove role
            $stmt = $this->db->prepare("DELETE FROM user_roles WHERE user_id = ? AND role_id = ?");
            $stmt->execute([$userId, $roleId]);
            
            return ['success' => true, 'message' => 'Role removed successfully'];
        } catch (Exception $e) {
            error_log("Remove role error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to remove role'];
        }
    }
    
    /**
     * Set primary role
     */
    public function setPrimaryRole(int $userId, int $roleId): array {
        try {
            // Check if user has this role
            $stmt = $this->db->prepare("
                SELECT id FROM user_roles 
                WHERE user_id = ? AND role_id = ? AND verification_status = 'approved'
            ");
            $stmt->execute([$userId, $roleId]);
            if (!$stmt->fetch()) {
                return ['success' => false, 'message' => 'User does not have this approved role'];
            }
            
            // Remove primary from all roles
            $stmt = $this->db->prepare("UPDATE user_roles SET is_primary = 0 WHERE user_id = ?");
            $stmt->execute([$userId]);
            
            // Set new primary
            $stmt = $this->db->prepare("UPDATE user_roles SET is_primary = 1 WHERE user_id = ? AND role_id = ?");
            $stmt->execute([$userId, $roleId]);
            
            return ['success' => true, 'message' => 'Primary role updated'];
        } catch (Exception $e) {
            error_log("Set primary role error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to update primary role'];
        }
    }
    
    /**
     * Get pet owner profile
     */
    public function getPetOwnerProfile(int $userId): ?array {
        $stmt = $this->db->prepare("
            SELECT * FROM pet_owner_profiles WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: null;
    }
    
    /**
     * Get vet profile
     */
    public function getVetProfile(int $userId): ?array {
        $stmt = $this->db->prepare("
            SELECT vp.*, c.clinic_name, c.clinic_address 
            FROM vet_profiles vp
            LEFT JOIN clinics c ON vp.clinic_id = c.id
            WHERE vp.user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: null;
    }
    
    /**
     * Get service provider profile
     */
    public function getServiceProviderProfile(int $userId, string $roleType): ?array {
        $stmt = $this->db->prepare("
            SELECT * FROM service_provider_profiles 
            WHERE user_id = ? AND role_type = ?
        ");
        $stmt->execute([$userId, $roleType]);
        return $stmt->fetch() ?: null;
    }
    
    /**
     * Update pet owner profile
     */
    public function updatePetOwnerProfile(int $userId, array $data): array {
        try {
            $stmt = $this->db->prepare("
                UPDATE pet_owner_profiles 
                SET emergency_contact_name = ?, 
                    emergency_contact_phone = ?,
                    notification_preferences = ?,
                    updated_at = NOW()
                WHERE user_id = ?
            ");
            $stmt->execute([
                $data['emergency_contact_name'] ?? null,
                $data['emergency_contact_phone'] ?? null,
                json_encode($data['notification_preferences'] ?? []),
                $userId
            ]);
            
            return ['success' => true, 'message' => 'Profile updated'];
        } catch (Exception $e) {
            error_log("Update profile error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to update profile'];
        }
    }
    
    /**
     * Update vet profile
     */
    public function updateVetProfile(int $userId, array $data): array {
        try {
            $fields = [];
            $values = [];
            
            $allowedFields = ['specialization', 'years_experience', 'education', 
                            'consultation_fee', 'bio', 'available'];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $fields[] = "$field = ?";
                    $values[] = $data[$field];
                }
            }
            
            if (empty($fields)) {
                return ['success' => false, 'message' => 'No data to update'];
            }
            
            $values[] = $userId;
            $sql = "UPDATE vet_profiles SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE user_id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($values);
            
            return ['success' => true, 'message' => 'Profile updated'];
        } catch (Exception $e) {
            error_log("Update vet profile error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to update profile'];
        }
    }
    
    /**
     * Update service provider profile
     */
    public function updateServiceProviderProfile(int $userId, string $roleType, array $data): array {
        try {
            $fields = [];
            $values = [];
            
            $allowedFields = ['business_name', 'service_area', 'experience_years', 
                            'certifications', 'price_range_min', 'price_range_max', 
                            'bio', 'available'];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $fields[] = "$field = ?";
                    $values[] = $field === 'specializations' ? json_encode($data[$field]) : $data[$field];
                }
            }
            
            if (empty($fields)) {
                return ['success' => false, 'message' => 'No data to update'];
            }
            
            $values[] = $userId;
            $values[] = $roleType;
            $sql = "UPDATE service_provider_profiles SET " . implode(', ', $fields) . 
                   ", updated_at = NOW() WHERE user_id = ? AND role_type = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($values);
            
            return ['success' => true, 'message' => 'Profile updated'];
        } catch (Exception $e) {
            error_log("Update service provider profile error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to update profile'];
        }
    }
    
    /**
     * Upload avatar
     */
    public function uploadAvatar(int $userId, array $file): array {
        try {
            // Validate file
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            
            if (!in_array($file['type'], $allowedTypes)) {
                return ['success' => false, 'message' => 'Invalid file type. Only JPG and PNG allowed'];
            }
            
            if ($file['size'] > $maxSize) {
                return ['success' => false, 'message' => 'File too large. Maximum 5MB'];
            }
            
            // Create upload directory if not exists
            $uploadDir = __DIR__ . '/../uploads/avatars/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'avatar_' . $userId . '_' . time() . '.' . $extension;
            $filepath = $uploadDir . $filename;
            
            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                // Update database
                $relativePath = '/PETVET/uploads/avatars/' . $filename;
                $stmt = $this->db->prepare("UPDATE users SET avatar = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$relativePath, $userId]);
                
                return [
                    'success' => true, 
                    'message' => 'Avatar uploaded successfully',
                    'avatar_url' => $relativePath
                ];
            }
            
            return ['success' => false, 'message' => 'Failed to upload file'];
        } catch (Exception $e) {
            error_log("Avatar upload error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to upload avatar'];
        }
    }
    
    /**
     * Get all users (admin function)
     */
    public function getAllUsers(int $limit = 100, int $offset = 0): array {
        $stmt = $this->db->prepare("
            SELECT u.id, u.email, u.first_name, u.last_name, u.phone, 
                   u.is_active, u.is_blocked, u.last_login, u.created_at,
                   GROUP_CONCAT(r.role_display_name SEPARATOR ', ') as roles
            FROM users u
            LEFT JOIN user_roles ur ON u.id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.id
            GROUP BY u.id
            ORDER BY u.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }
    
    /**
     * Block/Unblock user (admin function)
     */
    public function toggleUserBlock(int $userId, bool $block): array {
        try {
            $stmt = $this->db->prepare("UPDATE users SET is_blocked = ? WHERE id = ?");
            $stmt->execute([$block ? 1 : 0, $userId]);
            
            return [
                'success' => true, 
                'message' => $block ? 'User blocked' : 'User unblocked'
            ];
        } catch (Exception $e) {
            error_log("Toggle block error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to update user status'];
        }
    }
}
