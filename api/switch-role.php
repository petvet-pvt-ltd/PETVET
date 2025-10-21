<?php
/**
 * API Endpoint: Switch User Role
 * Handles role switching requests
 */

require_once __DIR__ . '/../config/auth_helper.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $newRole = $input['role'] ?? '';
    
    if (empty($newRole)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Role not specified']);
        exit;
    }
    
    // Attempt to switch role
    $switched = auth()->switchRole($newRole);
    
    if ($switched) {
        // Get redirect URL for the new role
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
        
        $redirect = $redirects[$newRole] ?? '/PETVET/index.php';
        
        echo json_encode([
            'success' => true,
            'message' => 'Role switched successfully',
            'redirect' => $redirect,
            'new_role' => $_SESSION['current_role_display'] ?? ''
        ]);
    } else {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'Unable to switch to this role. You may not have permission.'
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
