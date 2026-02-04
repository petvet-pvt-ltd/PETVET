<?php
/**
 * API Endpoint: Get Clinics
 * Returns active, approved clinics for selectors (id, name, logo)
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/connect.php';

try {
    $pdo = db();

        $sql = "SELECT 
                id,
                clinic_name,
                clinic_logo,
                                clinic_address,
                city,
                district
            FROM clinics
            WHERE is_active = 1
              AND verification_status = 'approved'
                        ORDER BY clinic_name ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $clinics = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'clinics' => $clinics,
        'total_clinics' => count($clinics)
    ]);
} catch (Throwable $e) {
    error_log('Get clinics error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch clinics'
    ]);
}
