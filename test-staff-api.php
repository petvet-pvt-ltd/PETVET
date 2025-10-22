<?php
/**
 * Test script for Staff API
 * Access: http://localhost/PETVET/test-staff-api.php
 */

session_start();

// Simulate logged-in clinic manager for testing
$_SESSION['user_id'] = 1; // Assuming clinic manager has user_id 1
$_SESSION['role'] = 'clinic_manager';
$_SESSION['logged_in'] = true;

require_once __DIR__ . '/models/ClinicManager/StaffModel.php';

echo "<h1>Staff Management API Test</h1>";
echo "<style>
body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
.section { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
h2 { color: #2563eb; }
table { width: 100%; border-collapse: collapse; margin: 10px 0; }
th, td { padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb; }
th { background: #f3f4f6; font-weight: 600; }
.success { color: #10b981; }
.error { color: #ef4444; }
button { background: #2563eb; color: white; padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; margin: 5px; }
button:hover { background: #1d4ed8; }
</style>";

// Test 1: Get all staff
echo "<div class='section'>";
echo "<h2>Test 1: Get All Staff Members</h2>";
try {
    $model = new StaffModel();
    $staff = $model->all();
    
    echo "<p class='success'>✓ Successfully retrieved " . count($staff) . " staff members</p>";
    echo "<table>";
    echo "<tr><th>ID</th><th>Name</th><th>Role</th><th>Email</th><th>Phone</th><th>Status</th></tr>";
    foreach ($staff as $s) {
        echo "<tr>";
        echo "<td>{$s['id']}</td>";
        echo "<td>{$s['name']}</td>";
        echo "<td>{$s['role']}</td>";
        echo "<td>{$s['email']}</td>";
        echo "<td>{$s['phone']}</td>";
        echo "<td>{$s['status']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// Test 2: Add new staff member
echo "<div class='section'>";
echo "<h2>Test 2: Add New Staff Member</h2>";
try {
    $model = new StaffModel();
    $testStaff = [
        'name' => 'Test Employee ' . date('His'),
        'role' => 'Support Staff',
        'email' => 'test' . date('His') . '@petvet.lk',
        'phone' => '+94 71 ' . rand(1000000, 9999999)
    ];
    
    $newId = $model->add($testStaff);
    
    if ($newId) {
        echo "<p class='success'>✓ Successfully added staff member with ID: {$newId}</p>";
        $newStaff = $model->findById($newId);
        echo "<pre>" . print_r($newStaff, true) . "</pre>";
    } else {
        echo "<p class='error'>✗ Failed to add staff member</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// Test 3: Update staff member
echo "<div class='section'>";
echo "<h2>Test 3: Update Staff Member</h2>";
try {
    $model = new StaffModel();
    // Get the last staff member
    $staff = $model->all();
    if (!empty($staff)) {
        $lastStaff = end($staff);
        $updateData = [
            'name' => $lastStaff['name'] . ' (Updated)',
            'role' => $lastStaff['role'],
            'email' => $lastStaff['email'],
            'phone' => $lastStaff['phone'],
            'status' => 'Active'
        ];
        
        $success = $model->update($lastStaff['id'], $updateData);
        
        if ($success) {
            echo "<p class='success'>✓ Successfully updated staff member ID: {$lastStaff['id']}</p>";
            $updated = $model->findById($lastStaff['id']);
            echo "<pre>" . print_r($updated, true) . "</pre>";
        } else {
            echo "<p class='error'>✗ Failed to update staff member</p>";
        }
    } else {
        echo "<p class='error'>✗ No staff members found</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// Test 4: Delete staff member
echo "<div class='section'>";
echo "<h2>Test 4: Delete Staff Member</h2>";
try {
    $model = new StaffModel();
    // Get the last staff member
    $staff = $model->all();
    if (!empty($staff)) {
        $lastStaff = end($staff);
        echo "<p>Attempting to delete: {$lastStaff['name']} (ID: {$lastStaff['id']})</p>";
        
        $success = $model->delete($lastStaff['id']);
        
        if ($success) {
            echo "<p class='success'>✓ Successfully deleted staff member</p>";
        } else {
            echo "<p class='error'>✗ Failed to delete staff member</p>";
        }
    } else {
        echo "<p class='error'>✗ No staff members found</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
}
echo "</div>";

echo "<div class='section'>";
echo "<h2>Access Staff Management Page</h2>";
echo "<p><a href='/PETVET/index.php?module=clinic-manager&page=staff'><button>Go to Staff Management Page</button></a></p>";
echo "</div>";
?>
