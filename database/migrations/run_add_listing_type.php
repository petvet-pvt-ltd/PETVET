<?php
/**
 * Migration Script: Add listing_type column to sell_pet_listings
 * Run this file once to add the listing_type field
 */

require_once __DIR__ . '/../../config/connect.php';

// Get the connection (now properly set up with SSL in connect.php)
global $conn;

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Connected to database successfully.\n\n";

// Check if column already exists
$checkSql = "SHOW COLUMNS FROM `sell_pet_listings` LIKE 'listing_type'";
$result = mysqli_query($conn, $checkSql);

if (mysqli_num_rows($result) > 0) {
    echo "✓ Column 'listing_type' already exists. No migration needed.\n";
    mysqli_close($conn);
    exit(0);
}

echo "Adding 'listing_type' column to sell_pet_listings table...\n";

// Add the listing_type column
$sql1 = "ALTER TABLE `sell_pet_listings` 
         ADD COLUMN `listing_type` ENUM('selling', 'adopting') NOT NULL DEFAULT 'selling' AFTER `price`";

if (mysqli_query($conn, $sql1)) {
    echo "✓ Column 'listing_type' added successfully.\n";
} else {
    die("Error adding column: " . mysqli_error($conn) . "\n");
}

// Add index for better performance
echo "Adding index on 'listing_type' column...\n";
$sql2 = "ALTER TABLE `sell_pet_listings` ADD INDEX `idx_listing_type` (`listing_type`)";

if (mysqli_query($conn, $sql2)) {
    echo "✓ Index 'idx_listing_type' added successfully.\n";
} else {
    echo "Note: Index might already exist: " . mysqli_error($conn) . "\n";
}

// Update existing records
echo "Updating existing records to default to 'selling'...\n";
$sql3 = "UPDATE `sell_pet_listings` SET `listing_type` = 'selling' WHERE `listing_type` IS NULL OR `listing_type` = ''";

if (mysqli_query($conn, $sql3)) {
    $affected = mysqli_affected_rows($conn);
    echo "✓ Updated $affected records.\n";
} else {
    echo "Warning: Could not update records: " . mysqli_error($conn) . "\n";
}

echo "\n✓✓✓ Migration completed successfully! ✓✓✓\n";

mysqli_close($conn);
?>
