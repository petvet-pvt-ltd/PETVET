<?php
// Direct test - bypass all caching
$_GET['module'] = 'pet-owner';
$_GET['page'] = 'payment-success';
$_GET['session_id'] = 'test_123';

// Start session
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'pet_owner';

echo "Testing payment-success routing...<br>";
echo "Module: " . $_GET['module'] . "<br>";
echo "Page: " . $_GET['page'] . "<br>";
echo "<hr>";

// Include the main index
include __DIR__ . '/index.php';
?>
