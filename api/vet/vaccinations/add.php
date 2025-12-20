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

$appointmentId = 0;
if (isset($payload['appointment_id'])) $appointmentId = (int)$payload['appointment_id'];
if (isset($payload['appointmentId'])) $appointmentId = (int)$payload['appointmentId'];

$vaccine = trim($payload['vaccine'] ?? '');

$nextDue = null;
if (!empty($payload['next_due'])) $nextDue = $payload['next_due'];
if (!empty($payload['nextDue'])) $nextDue = $payload['nextDue'];

if ($appointmentId <= 0 || $vaccine === '') {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

// Validate date (optional but recommended)
if ($nextDue !== null && $nextDue !== '') {
    $dt = DateTime::createFromFormat('Y-m-d', $nextDue);
    if (!$dt || $dt->format('Y-m-d') !== $nextDue) {
        echo json_encode(['success' => false, 'error' => 'Invalid next due date']);
        exit;
    }
} else {
    $nextDue = null;
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
        echo json_encode(['success' => false, 'error' => 'Vaccination allowed only for ongoing/completed appointments']);
        exit;
    }

    // Optional: prevent duplicates (one vaccination per appointment)
    // Remove if you want multiple vaccines in one appointment.
    $check = $pdo->prepare("SELECT id FROM vaccinations WHERE appointment_id = ? LIMIT 1");
    $check->execute([$appointmentId]);
    if ($check->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Vaccination already exists for this appointment']);
        exit;
    }

    $stmt = $pdo->prepare("
        INSERT INTO vaccinations (appointment_id, vaccine, next_due, created_at)
        VALUES (:appointment_id, :vaccine, :next_due, NOW())
    ");
    $stmt->execute([
        'appointment_id' => $appointmentId,
        'vaccine' => $vaccine,
        'next_due' => $nextDue,
    ]);

    echo json_encode(['success' => true, 'id' => (int)$pdo->lastInsertId()]);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => 'Server error']);
}
