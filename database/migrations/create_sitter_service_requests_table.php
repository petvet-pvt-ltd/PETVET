<?php
/**
 * Migration: Create sitter_service_requests table
 * Stores pet-owner booking requests shown on sitter bookings page (Pending)
 */

require_once __DIR__ . '/../../config/connect.php';

try {
    $pdo = db();

    echo "Creating sitter_service_requests table...\n\n";

    $sql = "CREATE TABLE IF NOT EXISTS sitter_service_requests (
        id INT(11) NOT NULL AUTO_INCREMENT,
        sitter_id INT(11) NOT NULL,
        pet_owner_id INT(11) NOT NULL,

        service_type VARCHAR(64) NOT NULL,

        pet_name VARCHAR(255) NOT NULL,
        pet_type VARCHAR(32) NOT NULL,
        pet_breed VARCHAR(255) NOT NULL,

        duration_type ENUM('single','multiple') NOT NULL DEFAULT 'single',
        number_of_days INT(11) NULL,

        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        start_time TIME NOT NULL,
        end_time TIME NOT NULL,

        location_type ENUM('sitter','home','park','other') NOT NULL,
        location_address VARCHAR(512) NULL,
        location_lat DECIMAL(10,6) NULL,
        location_lng DECIMAL(10,6) NULL,
        location_district VARCHAR(64) NULL,

        special_notes TEXT NULL,

        status ENUM('pending','accepted','declined') NOT NULL DEFAULT 'pending',
        sitter_response_at DATETIME NULL,
        decline_reason VARCHAR(255) NULL,

        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

        PRIMARY KEY (id),
        KEY idx_sitter_status_date (sitter_id, status, start_date),
        KEY idx_owner (pet_owner_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    $pdo->exec($sql);

    echo "✓ sitter_service_requests table is ready\n";
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
