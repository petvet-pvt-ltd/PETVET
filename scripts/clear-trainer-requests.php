<?php
/**
 * DevTool: Clear all trainer training requests
 * Usage: php DevTools/clear-trainer-requests.php
 */

require_once __DIR__ . '/../config/connect.php';

try {
    $pdo = db();

    $stmt = $pdo->prepare("DELETE FROM trainer_training_requests");
    $stmt->execute();

    $deleted = $stmt->rowCount();

    echo "OK: Deleted {$deleted} trainer_training_requests rows" . PHP_EOL;
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
