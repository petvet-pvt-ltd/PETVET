<?php
require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../config/auth_helper.php';

header('Content-Type: application/json; charset=utf-8');

requireLogin('/PETVET/index.php?module=guest&page=login');
requireRole('vet', '/PETVET/index.php');

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
            p.name AS pet_name,
            CONCAT(u.first_name, ' ', u.last_name) AS owner_name
        FROM appointments a
        JOIN pets p ON p.id = a.pet_id
        JOIN users u ON u.id = a.pet_owner_id
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
