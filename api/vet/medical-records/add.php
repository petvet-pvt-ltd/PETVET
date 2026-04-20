<?php
// API endpoint for creating medical records with file uploads
require_once __DIR__ . '/../../../config/connect.php';
require_once __DIR__ . '/../../../config/auth_helper.php';
require_once __DIR__ . '/../../../config/MedicalFileUploader.php';

header('Content-Type: application/json; charset=utf-8');

// Verify user is authenticated and has vet role
requireLogin('/PETVET/index.php?module=guest&page=login');
requireRole('vet', '/PETVET/index.php');

// Check if vet account is not suspended
enforceVetNotSuspendedApi();

if (empty($_SESSION['clinic_id']) || empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Clinic or user not set']);
    exit;
}

// Extract and validate medical record data from form
$appointmentId = isset($_POST['appointment_id']) ? (int)$_POST['appointment_id'] : 0;
$symptoms = trim($_POST['symptoms'] ?? '');
$diagnosis = trim($_POST['diagnosis'] ?? '');
$treatment = trim($_POST['treatment'] ?? '');

// Ensure all required fields are provided
if ($appointmentId <= 0 || $symptoms === '' || $diagnosis === '' || $treatment === '') {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

$vetId = (int)$_SESSION['user_id'];
$clinicId = (int)$_SESSION['clinic_id'];

try {
    $pdo = db();

    // Verify appointment exists and belongs to current vet and clinic
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

    // Check that appointment status allows medical record creation
    if (!in_array($appt['status'], ['ongoing','completed'], true)) {
        echo json_encode(['success' => false, 'error' => 'Medical record allowed only for ongoing/completed appointments']);
        exit;
    }

    // Prevent duplicate medical records for same appointment
    $check = $pdo->prepare("SELECT id FROM medical_records WHERE appointment_id = ? LIMIT 1");
    $check->execute([$appointmentId]);
    if ($check->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Medical record already exists for this appointment']);
        exit;
    }

    // Upload medical documents/reports if provided
    $reports = null;
    if (isset($_FILES['reports']) && !empty($_FILES['reports']['name'][0])) {
        $uploader = new MedicalFileUploader();
        $uploadResult = $uploader->uploadFiles($_FILES['reports']);
        
        if ($uploadResult['success'] && !empty($uploadResult['files'])) {
            // Store file references as JSON
            $reports = json_encode($uploadResult['files']);
        } elseif (!$uploadResult['success']) {
            // Return file upload errors
            echo json_encode(['success' => false, 'error' => 'File upload error: ' . implode(', ', $uploadResult['errors'])]);
            exit;
        }
    }

    $stmt = $pdo->prepare("
        INSERT INTO medical_records (appointment_id, symptoms, diagnosis, treatment, reports, created_at)
        VALUES (:appointment_id, :symptoms, :diagnosis, :treatment, :reports, NOW())
    ");
    $stmt->execute([
        'appointment_id' => $appointmentId,
        'symptoms' => $symptoms,
        'diagnosis' => $diagnosis,
        'treatment' => $treatment,
        'reports' => $reports
    ]);

    echo json_encode(['success' => true, 'id' => (int)$pdo->lastInsertId()]);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}
