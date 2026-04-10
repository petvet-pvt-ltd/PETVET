<?php
/**
 * Database Migration: Allow guest adoption pet listings (user_id nullable)
 * Run this once to update the database schema
 */

require_once __DIR__ . '/../config/connect.php';

header('Content-Type: application/json');

try {
    echo json_encode(['status' => 'Running migration...'], JSON_PRETTY_PRINT);
    
    // Migrate: Make user_id nullable for guest listings
    $sql = "ALTER TABLE sell_pet_listings 
            MODIFY COLUMN user_id INT NULL COMMENT 'NULL for guest submissions, references users(id) for logged-in users'";
    
    if (mysqli_query($conn, $sql)) {
        echo json_encode([
            'status' => 'SUCCESS',
            'message' => 'Database migration completed successfully!',
            'details' => 'sell_pet_listings.user_id is now nullable. Guest users can now submit adoption listings without logging in.'
        ], JSON_PRETTY_PRINT);
    } else {
        $error = mysqli_error($conn);
        
        // Check if column is already nullable
        if (strpos($error, 'Syntax error') !== false || strpos($error, 'Duplicate') !== false) {
            echo json_encode([
                'status' => 'INFO',
                'message' => 'Migration may already be applied',
                'error' => $error
            ], JSON_PRETTY_PRINT);
        } else {
            throw new Exception($error);
        }
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'ERROR',
        'message' => 'Migration failed',
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
?>
