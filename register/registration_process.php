<?php
/**
 * DEPRECATED: Registration now uses the routing system
 * Redirecting to proper route
 */
header('Location: /PETVET/index.php?module=guest&page=register');
exit;

// Old code below - no longer executed
// ===================================

/**
 * Registration Process Router
 * Entry point for multi-role registration
 */

// Start session
session_start();

// Include required files
require_once __DIR__ . '/../controllers/RegistrationController.php';

// Instantiate controller
$controller = new RegistrationController();

// Process registration
$controller->register();
