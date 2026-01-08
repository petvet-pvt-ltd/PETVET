<?php
require_once __DIR__ . '/../../../config/connect.php';
require_once __DIR__ . '/../../../config/auth_helper.php';
require_once __DIR__ . '/../../../config/MedicalFileUploader.php';

header('Content-Type: application/json; charset=utf-8');

requireLogin('/PETVET/index.php?module=guest&page=login');
requireRole('vet', '/PETVET/index.php');

if (empty($_SESSION['clinic_id']) || empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Clinic or user not set']);
    exit;
}

// Handle both multipart/form-data and JSON
$appointmentId = isset($_POST['appointment_id']) ? (int)$_POST['appointment_id'] : 0;
$medication = trim($_POST['medication'] ?? '');
$dosage = trim($_POST['dosage'] ?? '');
$notes = trim($_POST['notes'] ?? '');

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

    // Handle file uploads
    $reports = null;
    if (isset($_FILES['reports']) && !empty($_FILES['reports']['name'][0])) {
        $uploader = new MedicalFileUploader();
        $uploadResult = $uploader->uploadFiles($_FILES['reports']);
        
        if ($uploadResult['success'] && !empty($uploadResult['files'])) {
            $reports = json_encode($uploadResult['files']);
        } elseif (!$uploadResult['success']) {
            echo json_encode(['success' => false, 'error' => 'File upload error: ' . implode(', ', $uploadResult['errors'])]);
            exit;
        }
    }

    $stmt = $pdo->prepare("
        INSERT INTO prescriptions (appointment_id, medication, dosage, notes, reports, created_at)
        VALUES (:appointment_id, :medication, :dosage, :notes, :reports, NOW())
    ");
    $stmt->execute([
        'appointment_id' => $appointmentId,
        'medication' => $medication,
        'dosage' => $dosage,
        'notes' => ($notes === '' ? null : $notes),
        'reports' => $reports
    ]);

    echo json_encode(['success' => true, 'id' => (int)$pdo->lastInsertId()]);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}
