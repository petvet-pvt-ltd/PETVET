<?php
/**
 * Global Configuration File
 * This file contains all the configurable settings for the PETVET application
 */

// Project Configuration
define('PROJECT_ROOT', '/PETVET');  // Change this when project folder name changes
define('PROJECT_NAME', 'PETVET');
define('PROJECT_VERSION', '1.0.0');

// Database Configuration (if needed later)
// define('DB_HOST', 'localhost');
// define('DB_NAME', 'petvet');
// define('DB_USER', 'root');
// define('DB_PASS', '');

// Helper function to get the base URL for assets and links
function getBaseUrl($path = '') {
    return PROJECT_ROOT . ($path ? '/' . ltrim($path, '/') : '');
}

// Helper function to get asset URLs
function asset($path) {
    return getBaseUrl('public/' . ltrim($path, '/'));
}

// Helper function to get view URLs  
function view($path) {
    return getBaseUrl('views/' . ltrim($path, '/'));
}

// Helper function to get route URLs
function route($module, $page = null) {
    $url = getBaseUrl('index.php?module=' . $module);
    if ($page) {
        $url .= '&page=' . $page;
    }
    return $url;
}

// Helper function to get image URLs
function img($path) {
    return getBaseUrl('views/shared/images/' . ltrim($path, '/'));
}
?>