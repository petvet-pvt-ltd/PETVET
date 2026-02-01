<?php
/**
 * Create Notifications Tables
 * Run this script to set up the notification system
 */

require_once __DIR__ . '/../config/connect.php';

$pdo = db();

echo "Creating notifications tables...\n\n";

// 1. Notifications table
$sql1 = "CREATE TABLE IF NOT EXISTS notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pet_owner_id INT NOT NULL,
    type ENUM('appointment', 'sitter', 'trainer', 'breeder') NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    clinic_id INT,
    clinic_name VARCHAR(255),
    entity_id INT,
    entity_type VARCHAR(50),
    action_data JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (pet_owner_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_pet_owner (pet_owner_id),
    INDEX idx_created_at (created_at),
    INDEX idx_type (type)
)";

// 2. Notification reads table (tracks which notifications have been read)
$sql2 = "CREATE TABLE IF NOT EXISTS notification_reads (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pet_owner_id INT NOT NULL,
    notification_id INT NOT NULL,
    read_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_read (pet_owner_id, notification_id),
    FOREIGN KEY (pet_owner_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (notification_id) REFERENCES notifications(id) ON DELETE CASCADE,
    INDEX idx_pet_owner (pet_owner_id)
)";

try {
    $pdo->exec($sql1);
    echo "✓ notifications table created\n";
    
    $pdo->exec($sql2);
    echo "✓ notification_reads table created\n";
    
    echo "\nNotification tables created successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
