<?php
/**
 * Authentication Class
 * Handles user login, logout, registration, session management, and authorization
 */

require_once __DIR__ . '/connect.php';

class Auth {
    private PDO $db;
    private int $maxLoginAttempts = 5;
    private int $lockoutTime = 900; // 15 minutes in seconds
    
    public function __construct() {
        $this->db = db();
        $this->startSession();
    }
    
    /**
     * Start session if not already started
     */
    private function startSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Register a new user
     * @param array $data User registration data
     * @return array Success/error response
     */
    public function register(array $data): array {
        try {
            // Validate required fields
            $required = ['email', 'password', 'first_name', 'last_name'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return ['success' => false, 'message' => ucfirst($field) . ' is required'];
                }
            }
            
            // Validate email format
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                return ['success' => false, 'message' => 'Invalid email format'];
            }
            
            // Check if email already exists
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$data['email']]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Email already registered'];
            }
            
            // Validate password strength
            if (strlen($data['password']) < 6) {
                return ['success' => false, 'message' => 'Password must be at least 6 characters'];
            }
            
            // Hash password
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            
            // Insert user
            $stmt = $this->db->prepare("
                INSERT INTO users (email, password, first_name, last_name, phone, address, email_verified) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['email'],
                $hashedPassword,
                $data['first_name'],
                $data['last_name'],
                $data['phone'] ?? null,
                $data['address'] ?? null,
                false // Email not verified by default
            ]);
            
            $userId = $this->db->lastInsertId();
            
            // Assign default role (pet_owner) if no role specified
            $roleId = $data['role_id'] ?? $this->getRoleIdByName('pet_owner');
            $requiresVerification = $this->roleRequiresVerification($roleId);
            
            $stmt = $this->db->prepare("
                INSERT INTO user_roles (user_id, role_id, is_primary, is_active, verification_status) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $userId,
                $roleId,
                true, // First role is primary
                true,
                $requiresVerification ? 'pending' : 'approved'
            ]);
            
            // Create profile based on role
            $this->createUserProfile($userId, $roleId, $data);
            
            // Log the registration
            $this->logAudit($userId, 'register', ['email' => $data['email']]);
            
            return [
                'success' => true, 
                'message' => $requiresVerification 
                    ? 'Registration successful! Your account is pending verification.' 
                    : 'Registration successful! You can now login.',
                'user_id' => $userId,
                'requires_verification' => $requiresVerification
            ];
            
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Registration failed. Please try again.'];
        }
    }
    
    /**
     * User login
     * @param string $email
     * @param string $password
     * @return array Success/error response
     */
    public function login(string $email, string $password): array {
        try {
            // Check login attempts
            if ($this->isAccountLocked($email)) {
                return [
                    'success' => false, 
                    'message' => 'Too many failed attempts. Account locked for 15 minutes.'
                ];
            }
            
            // Get user
            $stmt = $this->db->prepare("
                SELECT id, email, password, first_name, last_name, avatar, 
                       is_active, is_blocked, email_verified 
                FROM users 
                WHERE email = ?
            ");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if (!$user) {
                $this->recordLoginAttempt($email, false);
                return ['success' => false, 'message' => 'Invalid email or password'];
            }
            
            // Verify password
            if (!password_verify($password, $user['password'])) {
                $this->recordLoginAttempt($email, false);
                return ['success' => false, 'message' => 'Invalid email or password'];
            }
            
            // Check if account is active
            if (!$user['is_active'] || $user['is_blocked']) {
                return ['success' => false, 'message' => 'Your account has been deactivated'];
            }
            
            // Get user's approved roles
            $stmt = $this->db->prepare("
                SELECT ur.id, ur.role_id, ur.is_primary, r.role_name, r.role_display_name 
                FROM user_roles ur
                JOIN roles r ON ur.role_id = r.id
                WHERE ur.user_id = ? AND ur.is_active = 1 AND ur.verification_status = 'approved'
                ORDER BY ur.is_primary DESC
            ");
            $stmt->execute([$user['id']]);
            $roles = $stmt->fetchAll();
            
            if (empty($roles)) {
                return [
                    'success' => false, 
                    'message' => 'Your account is pending verification. Please wait for admin approval.'
                ];
            }
            
            // Successful login - create session
            $this->recordLoginAttempt($email, true);
            $this->createSession($user, $roles);
            
            // If vet logged in but clinic_id missing -> block access (data integrity)
            if ($_SESSION['current_role'] === 'vet' && empty($_SESSION['clinic_id'])) {
             $this->logout();
             return ['success' => false, 'message' => 'Vet profile not linked to a clinic. Contact admin.'];
}


            // Update last login
            $stmt = $this->db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $stmt->execute([$user['id']]);
            
            // Log the login
            $this->logAudit($user['id'], 'login');
            
            return [
                'success' => true,
                'message' => 'Login successful',
                'redirect' => $this->getDefaultRedirect($roles[0]['role_name'])
            ];
            
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Login failed. Please try again.'];
        }
    }
    
    /**
     * Create user session
     */
    private function createSession(array $user, array $roles): void {
    $primaryRole = $roles[0];

    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['name'] = $user['first_name'] . ' ' . $user['last_name'];
    $_SESSION['avatar'] = $user['avatar'] ?? null;

    $_SESSION['current_role'] = $primaryRole['role_name'];
    $_SESSION['current_role_id'] = (int)$primaryRole['role_id'];
    $_SESSION['current_role_display'] = $primaryRole['role_display_name'];
    $_SESSION['roles'] = array_map(fn($r) => $r['role_name'], $roles);

    $_SESSION['logged_in'] = true;
    $_SESSION['login_time'] = time();

    // ✅ Always create the key so it shows in session dump
    $_SESSION['clinic_id'] = null;

    // ✅ If vet, pull clinic_id from vets table
    if ($primaryRole['role_name'] === 'vet') {
        $stmt = $this->db->prepare("SELECT clinic_id FROM vets WHERE user_id = ? LIMIT 1");
        $stmt->execute([$_SESSION['user_id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && isset($row['clinic_id']) && $row['clinic_id'] !== null) {
            $_SESSION['clinic_id'] = (int)$row['clinic_id'];
        }
    }
    
    // ✅ If receptionist or clinic_manager, pull clinic_id from clinic_staff table
    if (in_array($primaryRole['role_name'], ['receptionist', 'clinic_manager'])) {
        $stmt = $this->db->prepare("SELECT clinic_id FROM clinic_staff WHERE user_id = ? LIMIT 1");
        $stmt->execute([$_SESSION['user_id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && isset($row['clinic_id']) && $row['clinic_id'] !== null) {
            $_SESSION['clinic_id'] = (int)$row['clinic_id'];
        }
    }
}

    
    /**
     * Logout user
     */
    public function logout(): void {
        if (isset($_SESSION['user_id'])) {
            $this->logAudit($_SESSION['user_id'], 'logout');
        }
        
        session_unset();
        session_destroy();
        
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
    }
    
    /**
     * Check if user is logged in
     */
    public function isLoggedIn(): bool {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    /**
     * Get current user ID
     */
    public function getUserId(): ?int {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Get current user role
     */
    public function getCurrentRole(): ?string {
        return $_SESSION['current_role'] ?? null;
    }
    
    /**
     * Check if user has a specific role
     */
    public function hasRole(string $roleName): bool {
        return in_array($roleName, $_SESSION['roles'] ?? []);
    }
    
    /**
     * Switch user's active role
     */
    public function switchRole(string $roleName): bool {
        if (!$this->isLoggedIn() || !$this->hasRole($roleName)) {
            return false;
        }
        
        // Get role details
        $stmt = $this->db->prepare("
            SELECT ur.role_id, r.role_display_name 
            FROM user_roles ur
            JOIN roles r ON ur.role_id = r.id
            WHERE ur.user_id = ? AND r.role_name = ? 
            AND ur.is_active = 1 AND ur.verification_status = 'approved'
        ");
        $stmt->execute([$this->getUserId(), $roleName]);
        $role = $stmt->fetch();
        
        if ($role) {
            $_SESSION['current_role'] = $roleName;
            $_SESSION['current_role_id'] = $role['role_id'];
            $_SESSION['current_role_display'] = $role['role_display_name'];
            
            $this->logAudit($this->getUserId(), 'role_switch', ['new_role' => $roleName]);
            return true;
        }
        
        return false;
    }
    
    /**
     * Require login (redirect if not logged in)
     */
    public function requireLogin(string $redirectUrl = '/PETVET/index.php?module=guest&page=login'): void {
        if (!$this->isLoggedIn()) {
            header('Location: ' . $redirectUrl);
            exit;
        }
    }
    
    /**
     * Require specific role
     */
    public function requireRole(string $roleName, string $redirectUrl = '/PETVET/index.php'): void {
        if (!$this->hasRole($roleName)) {
            header('Location: ' . $redirectUrl);
            exit;
        }
    }
    
    /**
     * Check if account is locked due to failed attempts
     */
    private function isAccountLocked(string $email): bool {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as attempts 
            FROM login_attempts 
            WHERE email = ? 
            AND success = 0 
            AND attempted_at > DATE_SUB(NOW(), INTERVAL ? SECOND)
        ");
        $stmt->execute([$email, $this->lockoutTime]);
        $result = $stmt->fetch();
        
        return $result['attempts'] >= $this->maxLoginAttempts;
    }
    
    /**
     * Record login attempt
     */
    private function recordLoginAttempt(string $email, bool $success): void {
        $stmt = $this->db->prepare("
            INSERT INTO login_attempts (email, ip_address, success) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$email, $_SERVER['REMOTE_ADDR'] ?? 'unknown', $success ? 1 : 0]);
        
        // Clean old attempts (older than 24 hours)
        $this->db->exec("DELETE FROM login_attempts WHERE attempted_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)");
    }
    
    /**
     * Get role ID by role name
     */
    private function getRoleIdByName(string $roleName): int {
        $stmt = $this->db->prepare("SELECT id FROM roles WHERE role_name = ?");
        $stmt->execute([$roleName]);
        $result = $stmt->fetch();
        return $result ? $result['id'] : 1; // Default to first role
    }
    
    /**
     * Check if role requires verification
     */
    private function roleRequiresVerification(int $roleId): bool {
        $stmt = $this->db->prepare("SELECT requires_verification FROM roles WHERE id = ?");
        $stmt->execute([$roleId]);
        $result = $stmt->fetch();
        return $result ? (bool)$result['requires_verification'] : false;
    }
    
    /**
     * Create user profile based on role
     */
    private function createUserProfile(int $userId, int $roleId, array $data): void {
        // Get role name
        $stmt = $this->db->prepare("SELECT role_name FROM roles WHERE id = ?");
        $stmt->execute([$roleId]);
        $role = $stmt->fetch();
        
        if (!$role) return;
        
        try {
            switch ($role['role_name']) {
                case 'pet_owner':
                    $stmt = $this->db->prepare("
                        INSERT INTO pet_owner_profiles (user_id) VALUES (?)
                    ");
                    $stmt->execute([$userId]);
                    break;
                    
                case 'vet':
                // If user registers as vet, store vet profile in `vets` table
                     if (!empty($data['license_number']) && !empty($data['clinic_id'])) {
                     $stmt = $this->db->prepare("
                         INSERT INTO vets (
                         user_id, clinic_id, license_number,
                         specialization, years_experience,
                         created_at, updated_at
                      )
                         VALUES (?, ?, ?, ?, ?, NOW(), NOW())
                  ");
                      $stmt->execute([
                      $userId,
                      (int)$data['clinic_id'],
                      $data['license_number'],
                      $data['specialization'] ?? null,
                      (int)($data['years_experience'] ?? 0)
                   ]);}
                      break;

                    
                case 'clinic_manager':
                    if (!empty($data['clinic_id'])) {
                        $stmt = $this->db->prepare("
                            INSERT INTO clinic_manager_profiles (user_id, clinic_id) 
                            VALUES (?, ?)
                        ");
                        $stmt->execute([$userId, $data['clinic_id']]);
                    }
                    break;
                    
                case 'trainer':
                case 'sitter':
                case 'breeder':
                case 'groomer':
                    $stmt = $this->db->prepare("
                        INSERT INTO service_provider_profiles (user_id, role_type, service_area, experience_years) 
                        VALUES (?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $userId,
                        $role['role_name'],
                        $data['service_area'] ?? null,
                        $data['experience_years'] ?? 0
                    ]);
                    break;
            }
        } catch (Exception $e) {
            error_log("Profile creation error: " . $e->getMessage());
        }
    }
    
    /**
     * Get default redirect URL based on role
     */
    private function getDefaultRedirect(string $roleName): string {
        $redirects = [
            'admin' => '/PETVET/index.php?module=admin&page=dashboard',
            'vet' => '/PETVET/index.php?module=vet&page=dashboard',
            'clinic_manager' => '/PETVET/index.php?module=clinic-manager&page=overview',
            'receptionist' => '/PETVET/index.php?module=receptionist&page=dashboard',
            'trainer' => '/PETVET/index.php?module=trainer&page=dashboard',
            'sitter' => '/PETVET/index.php?module=sitter&page=dashboard',
            'breeder' => '/PETVET/index.php?module=breeder&page=dashboard',
            'groomer' => '/PETVET/index.php?module=groomer&page=services',
            'pet_owner' => '/PETVET/index.php?module=pet-owner&page=my-pets',
        ];
        
        return $redirects[$roleName] ?? '/PETVET/index.php?module=pet-owner&page=my-pets';
    }
    
    /**
     * Log audit trail
     */
    private function logAudit(?int $userId, string $action, array $details = []): void {
        // Audit logging disabled for now - will enable later
        return;
        
        /* DISABLED - Uncomment to re-enable audit logging
        try {
            $stmt = $this->db->prepare("
                INSERT INTO audit_logs (user_id, action, ip_address, user_agent, details) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $userId,
                $action,
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                json_encode($details)
            ]);
        } catch (Exception $e) {
            error_log("Audit log error: " . $e->getMessage());
        }
        */
    }
    
    /**
     * Get user data
     */
    public function getUser(?int $userId = null): ?array {
        $userId = $userId ?? $this->getUserId();
        if (!$userId) return null;
        
        $stmt = $this->db->prepare("
            SELECT id, email, first_name, last_name, phone, address, avatar, 
                   email_verified, last_login, created_at 
            FROM users 
            WHERE id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: null;
    }
}
