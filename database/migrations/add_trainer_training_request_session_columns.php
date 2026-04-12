<?php
/**
 * Migration: Add session tracking columns to trainer_training_requests
 */

require_once __DIR__ . '/../../config/connect.php';

try {
    $pdo = db();

    echo "Adding session tracking columns to trainer_training_requests...\n\n";

    $columns = [
        "ALTER TABLE trainer_training_requests ADD COLUMN sessions_completed INT NOT NULL DEFAULT 0",
        "ALTER TABLE trainer_training_requests ADD COLUMN next_session_date DATE NULL",
        "ALTER TABLE trainer_training_requests ADD COLUMN next_session_time TIME NULL",
        "ALTER TABLE trainer_training_requests ADD COLUMN next_session_goals TEXT NULL",
        "ALTER TABLE trainer_training_requests ADD COLUMN completed_at DATETIME NULL",
        "ALTER TABLE trainer_training_requests ADD COLUMN final_notes TEXT NULL"
    ];

    foreach ($columns as $sql) {
        try {
            $pdo->exec($sql);
        } catch (PDOException $e) {
            if (($e->errorInfo[0] ?? null) !== '42S21') {
                throw $e;
            }
        }
    }

    echo "OK: trainer_training_requests updated\n";

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
