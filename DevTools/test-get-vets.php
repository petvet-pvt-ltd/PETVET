<?php
require_once __DIR__ . '/../config/connect.php';

try {
    $db = db();
    
    $query = "
        SELECT 
            u.id, 
            CONCAT(u.first_name, ' ', u.last_name) as name,
            'Veterinarian' as specialization,
            u.profile_picture as avatar
        FROM users u
        JOIN user_roles ur ON u.id = ur.user_id
        JOIN roles r ON ur.role_id = r.id
        WHERE r.role_name = 'vet' 
        AND u.is_active = 1
        ORDER BY u.first_name, u.last_name
    ";
    
    echo "Executing query...\n";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Results count: " . count($results) . "\n";
    echo "Results:\n";
    print_r($results);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString();
}
