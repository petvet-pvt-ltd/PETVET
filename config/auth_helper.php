<?php
/**
 * Authentication Helper Functions
 * Quick access to authentication functionality
 */

require_once __DIR__ . '/Auth.php';
require_once __DIR__ . '/User.php';

// Initialize global auth instance
function auth(): Auth {
    static $auth = null;
    if ($auth === null) {
        $auth = new Auth();
    }
    return $auth;
}

// Initialize global user model instance
function userModel(): User {
    static $user = null;
    if ($user === null) {
        $user = new User();
    }
    return $user;
}

// Quick check if user is logged in
function isLoggedIn(): bool {
    return auth()->isLoggedIn();
}

// Get current user ID
function currentUserId(): ?int {
    return auth()->getUserId();
}

// Get current role
function currentRole(): ?string {
    return auth()->getCurrentRole();
}

// Alias for currentRole() - for backward compatibility
function getUserRole(): ?string {
    return currentRole();
}

// Check if user has role
function hasRole(string $roleName): bool {
    return auth()->hasRole($roleName);
}

// Get current user data
function currentUser(): ?array {
    return auth()->getUser();
}

// Require login (redirect if not logged in)
function requireLogin(string $redirectUrl = '/PETVET/index.php?module=guest&page=login'): void {
    auth()->requireLogin($redirectUrl);
}

// Require specific role
function requireRole(string $roleName, string $redirectUrl = '/PETVET/index.php'): void {
    auth()->requireRole($roleName, $redirectUrl);
}

// Check if current user can access module
function canAccessModule(string $module): bool {
    if (!isLoggedIn()) {
        return $module === 'guest';
    }
    
    // Map modules to required roles
    $moduleRoleMap = [
        'guest' => true, // Everyone can access guest pages
        'admin' => 'admin',
        'vet' => 'vet',
        'clinic-manager' => 'clinic_manager',
        'receptionist' => 'receptionist',
        'trainer' => 'trainer',
        'sitter' => 'sitter',
        'breeder' => 'breeder',
        'groomer' => 'groomer',
        'pet-owner' => 'pet_owner',
    ];
    
    if (!isset($moduleRoleMap[$module])) {
        return false;
    }
    
    if ($moduleRoleMap[$module] === true) {
        return true;
    }
    
    return hasRole($moduleRoleMap[$module]);
}

// Redirect to login if not authenticated
function redirectToLogin(): void {
    header('Location: /PETVET/index.php?module=guest&page=login');
    exit;
}

// Redirect to appropriate dashboard based on role
function redirectToDashboard(): void {
    $role = currentRole();
    
    $dashboards = [
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
    
    $redirect = $dashboards[$role] ?? '/PETVET/index.php?module=pet-owner&page=my-pets';
    header('Location: ' . $redirect);
    exit;
}

// Format user display name
function userDisplayName(?array $user = null): string {
    $user = $user ?? currentUser();
    if (!$user) return 'Guest';
    return ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '');
}

// Get user avatar or default
function userAvatar(?array $user = null): string {
    $user = $user ?? currentUser();
    if (!$user || empty($user['avatar'])) {
        return '/PETVET/views/shared/images/placeholder-avatar.png';
    }
    return $user['avatar'];
}

// CSRF Token Generation and Validation
function generateCsrfToken(): string {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

function validateCsrfToken(string $token): bool {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Output CSRF token input field
function csrfField(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(generateCsrfToken()) . '">';
}
