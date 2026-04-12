<?php
require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../config/auth_helper.php';

header('Content-Type: application/json; charset=utf-8');

requireLogin('/PETVET/index.php?module=guest&page=login');
requireRole('vet', '/PETVET/index.php');

// Suspended vets should not access dashboard data (dashboard shows suspended screen)
enforceVetNotSuspendedApi();

if (empty($_SESSION['clinic_id']) || empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Clinic or user not set']);
    exit;
}

$vetId = (int)$_SESSION['user_id'];
$clinicId = (int)$_SESSION['clinic_id'];

try {
    $pdo = db();

    // Return appointments with pet + owner names
    $stmt = $pdo->prepare("
        SELECT 
            a.id,
            a.appointment_date,
            a.appointment_time,
            a.status,
            a.appointment_type,
            COALESCE(p.name, a.guest_pet_name) AS pet_name,
            COALESCE(CONCAT(u.first_name, ' ', u.last_name), a.guest_client_name) AS owner_name,
            a.guest_phone AS guest_phone
        FROM appointments a
        LEFT JOIN pets p ON p.id = a.pet_id
        LEFT JOIN users u ON u.id = a.pet_owner_id
        WHERE a.vet_id = :vet_id
          AND a.clinic_id = :clinic_id
          AND a.status IN ('approved','ongoing','completed','cancelled','no_show')
        ORDER BY a.appointment_date ASC, a.appointment_time ASC
    ");
    $stmt->execute(['vet_id' => $vetId, 'clinic_id' => $clinicId]);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'appointments' => $appointments
    ]);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => 'Server error']);
}
