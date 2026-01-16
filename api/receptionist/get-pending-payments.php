<?php
require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../config/auth_helper.php';

header('Content-Type: application/json; charset=utf-8');

requireLogin('/PETVET/index.php?module=guest&page=login');
requireRole('receptionist', '/PETVET/index.php');

if (empty($_SESSION['clinic_id'])) {
    echo json_encode(['success' => false, 'error' => 'Clinic not set']);
    exit;
}

$clinicId = (int)$_SESSION['clinic_id'];

try {
    $pdo = db();
    
    $stmt = $pdo->prepare("
        SELECT 
            a.id,
            a.appointment_date,
            a.appointment_time,
            a.appointment_type,
            a.status,
            u.first_name AS owner_first_name,
            u.last_name AS owner_last_name,
            p.name AS pet_name,
            p.species AS animal_type,
            v.first_name AS vet_first_name,
            v.last_name AS vet_last_name
        FROM appointments a
        JOIN users u ON a.pet_owner_id = u.id
        JOIN pets p ON a.pet_id = p.id
        JOIN users v ON a.vet_id = v.id
        WHERE a.clinic_id = :clinic_id
          AND a.status = 'completed'
        ORDER BY a.appointment_date DESC, a.appointment_time DESC
    ");
    $stmt->execute(['clinic_id' => $clinicId]);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $pendingPayments = [];
    foreach ($appointments as $appt) {
        $pendingPayments[] = [
            'id' => $appt['id'],
            'client' => trim($appt['owner_first_name'] . ' ' . $appt['owner_last_name']),
            'pet' => $appt['pet_name'],
            'animal' => ucfirst($appt['animal_type']),
            'vet' => 'Dr. ' . trim($appt['vet_first_name'] . ' ' . $appt['vet_last_name']),
            'type' => ucfirst($appt['appointment_type']),
            'date' => $appt['appointment_date'],
            'time' => date('h:i A', strtotime($appt['appointment_time'])),
            'status' => 'Pending Payment'
        ];
    }
    
    echo json_encode([
        'success' => true,
        'payments' => $pendingPayments,
        'count' => count($pendingPayments)
    ]);
    
} catch (Exception $e) {
    error_log("get-pending-payments error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
