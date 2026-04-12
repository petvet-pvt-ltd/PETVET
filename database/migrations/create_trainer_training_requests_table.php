<?php
/**
 * Migration: Create trainer_training_requests table
 * Stores pet-owner booking requests shown on trainer appointments page (Pending)
 */

require_once __DIR__ . '/../../config/connect.php';

try {
    $pdo = db();

    echo "Creating trainer_training_requests table...\n\n";

    // Create table if not exists
    $sql = "CREATE TABLE IF NOT EXISTS trainer_training_requests (
        id INT(11) NOT NULL AUTO_INCREMENT,
        trainer_id INT(11) NOT NULL,
        pet_owner_id INT(11) NOT NULL,

        training_type VARCHAR(32) NOT NULL,
        pet_name VARCHAR(255) NOT NULL,
        pet_breed VARCHAR(255) NOT NULL,

        preferred_date DATE NOT NULL,
        preferred_time TIME NOT NULL,

        location_type ENUM('trainer','home','park','other') NOT NULL,
        location_address VARCHAR(512) NULL,
        location_lat DECIMAL(10,6) NULL,
        location_lng DECIMAL(10,6) NULL,
        location_district VARCHAR(64) NULL,

        additional_notes TEXT NULL,

        status ENUM('pending','accepted','declined','completed') NOT NULL DEFAULT 'pending',
        trainer_response_at DATETIME NULL,
        decline_reason VARCHAR(255) NULL,

        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

        PRIMARY KEY (id),
        KEY idx_trainer_status_date (trainer_id, status, preferred_date),
        KEY idx_owner (pet_owner_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    $pdo->exec($sql);

    echo "✓ trainer_training_requests table is ready\n";

} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
