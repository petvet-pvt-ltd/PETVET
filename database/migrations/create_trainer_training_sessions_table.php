<?php
/**
 * Migration: Create trainer_training_sessions table
 * Stores session history entries for trainer training programs
 */

require_once __DIR__ . '/../../config/connect.php';

try {
    $pdo = db();

    echo "Creating trainer_training_sessions table...\n\n";

    $sql = "CREATE TABLE IF NOT EXISTS trainer_training_sessions (
        id INT(11) NOT NULL AUTO_INCREMENT,
        request_id INT(11) NOT NULL,
        trainer_id INT(11) NOT NULL,
        pet_owner_id INT(11) NOT NULL,
        session_number INT(11) NOT NULL,
        notes TEXT NOT NULL,
        next_session_date DATE NULL,
        next_session_time TIME NULL,
        next_session_goals TEXT NULL,
        completed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_request (request_id),
        KEY idx_trainer (trainer_id),
        KEY idx_owner (pet_owner_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    $pdo->exec($sql);

    echo "OK: trainer_training_sessions table is ready\n";

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
