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

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

$appointmentId = isset($data['appointmentId']) ? (int)$data['appointmentId'] : 0;
$newStatus = $data['status'] ?? '';

$allowed = ['ongoing','completed','cancelled'];
if ($appointmentId <= 0 || !in_array($newStatus, $allowed, true)) {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

$vetId = (int)$_SESSION['user_id'];
$clinicId = (int)$_SESSION['clinic_id'];

try {
    $pdo = db();

    // Load current status and verify ownership
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

    $current = $appt['status'];

    // Transition rules (simple + safe)
    $validTransition = false;

    if ($newStatus === 'ongoing' && $current === 'approved') $validTransition = true;
    if ($newStatus === 'completed' && $current === 'ongoing') $validTransition = true;
    if ($newStatus === 'cancelled' && in_array($current, ['approved','ongoing'], true)) $validTransition = true;

    if (!$validTransition) {
        echo json_encode(['success' => false, 'error' => "Invalid status change ($current → $newStatus)"]);
        exit;
    }

    $stmt = $pdo->prepare("
        UPDATE appointments
        SET status = :status, updated_at = NOW()
        WHERE id = :id
    ");
    $stmt->execute(['status' => $newStatus, 'id' => $appointmentId]);

    echo json_encode(['success' => true]);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => 'Server error']);
}
