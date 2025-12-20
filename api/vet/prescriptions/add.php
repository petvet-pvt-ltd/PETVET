<?php
require_once __DIR__ . '/../../../config/connect.php';
require_once __DIR__ . '/../../../config/auth_helper.php';

header('Content-Type: application/json; charset=utf-8');

requireLogin('/PETVET/index.php?module=guest&page=login');
requireRole('vet', '/PETVET/index.php');

if (empty($_SESSION['clinic_id']) || empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Clinic or user not set']);
    exit;
}

$payload = json_decode(file_get_contents('php://input'), true);

$appointmentId = isset($payload['appointment_id']) ? (int)$payload['appointment_id'] : 0;
$medication = trim($payload['medication'] ?? '');
$dosage = trim($payload['dosage'] ?? '');
$notes = trim($payload['notes'] ?? '');

if ($appointmentId <= 0 || $medication === '' || $dosage === '') {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

$vetId = (int)$_SESSION['user_id'];
$clinicId = (int)$_SESSION['clinic_id'];

try {
    $pdo = db();

    // Verify appointment ownership + status
    $stmt = $pdo->prepare("
        SELECT id, status
        FROM appointments
        WHERE id = :id AND vet_id = :vet_id AND clinic_id = :clinic_id
        LIMIT 1
    ");
    $stmt->execute(['id' => $appointmentId, 'vet_id' => $vetId, 'clinic_id' => $clinicId]);
    $appt = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$appt) {
        echo json_encode(['success' => false, 'error' => 'Appointment not found']);
        exit;
    }

    if (!in_array($appt['status'], ['ongoing','completed'], true)) {
        echo json_encode(['success' => false, 'error' => 'Prescription allowed only for ongoing/completed appointments']);
        exit;
    }

    // Optional: prevent duplicates (one prescription per appointment)
    // Remove this block if you want multiple prescriptions per appointment.
    $check = $pdo->prepare("SELECT id FROM prescriptions WHERE appointment_id = ? LIMIT 1");
    $check->execute([$appointmentId]);
    if ($check->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Prescription already exists for this appointment']);
        exit;
    }

    $stmt = $pdo->prepare("
        INSERT INTO prescriptions (appointment_id, medication, dosage, notes, created_at)
        VALUES (:appointment_id, :medication, :dosage, :notes, NOW())
    ");
    $stmt->execute([
        'appointment_id' => $appointmentId,
        'medication' => $medication,
        'dosage' => $dosage,
        'notes' => ($notes === '' ? null : $notes),
    ]);

    echo json_encode(['success' => true, 'id' => (int)$pdo->lastInsertId()]);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => 'Server error']);
}
