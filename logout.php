<?php
/**
 * Logout Handler
 * Logs out the user and redirects to login page
 */

require_once __DIR__ . '/config/auth_helper.php';

// Logout the user
auth()->logout();

// Redirect to login page with success message
header('Location: /PETVET/index.php?module=guest&page=login&logout=1');
exit;
