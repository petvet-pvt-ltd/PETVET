<?php
/**
 * Migration Script: Set all adoption listings to user_id = 2
 * This ensures all adoption pets are associated with a specific pet owner
 */

require_once __DIR__ . '/../../config/connect.php';

// Get the connection
global $conn;

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Connected to database successfully.\n\n";

// Update all adoption listings to user_id = 2
echo "Updating adoption listings to user_id = 2...\n";
$sql = "UPDATE `sell_pet_listings` SET `user_id` = 2 WHERE `listing_type` = 'adoption'";

if (mysqli_query($conn, $sql)) {
    $affected = mysqli_affected_rows($conn);
    echo "✓ Updated $affected adoption listings to user_id = 2.\n";
} else {
    die("Error updating records: " . mysqli_error($conn) . "\n");
}

// Verify the update
echo "\nVerifying update...\n";
$verifySql = "SELECT COUNT(*) as count FROM `sell_pet_listings` WHERE `listing_type` = 'adoption' AND `user_id` = 2";
$result = mysqli_query($conn, $verifySql);
$row = mysqli_fetch_assoc($result);
echo "Total adoption listings with user_id = 2: " . $row['count'] . "\n";

// Check if user_id = 2 exists
echo "\nChecking user information for user_id = 2...\n";
$userSql = "SELECT id, first_name, last_name, email FROM users WHERE id = 2";
$userResult = mysqli_query($conn, $userSql);

if ($userRow = mysqli_fetch_assoc($userResult)) {
    echo "✓ User found:\n";
    echo "  ID: " . $userRow['id'] . "\n";
    echo "  Name: " . $userRow['first_name'] . " " . $userRow['last_name'] . "\n";
    echo "  Email: " . $userRow['email'] . "\n";
} else {
    echo "⚠ Warning: User with ID = 2 not found in database!\n";
}

echo "\n✓✓✓ Migration completed successfully! ✓✓✓\n";

mysqli_close($conn);
?>
