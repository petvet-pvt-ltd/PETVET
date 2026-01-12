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
$notes = trim($_POST['notes'] ?? '');

// Get medications array from JSON
$medications = [];
if (isset($_POST['medications'])) {
    $medicationsData = json_decode($_POST['medications'], true);
    if (is_array($medicationsData) && count($medicationsData) > 0) {
        $medications = $medicationsData;
    }
}

if ($appointmentId <= 0 || empty($medications)) {
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

    // Create prescription header (without medication/dosage - those go in prescription_items)
    $stmt = $pdo->prepare("
        INSERT INTO prescriptions (appointment_id, notes, reports, created_at)
        VALUES (:appointment_id, :notes, :reports, NOW())
    ");
    $stmt->execute([
        'appointment_id' => $appointmentId,
        'notes' => ($notes === '' ? null : $notes),
        'reports' => $reports
    ]);

    $prescriptionId = (int)$pdo->lastInsertId();

    // Insert medications into prescription_items
    $itemStmt = $pdo->prepare("
        INSERT INTO prescription_items (prescription_id, medication, dosage)
        VALUES (:prescription_id, :medication, :dosage)
    ");

    foreach ($medications as $med) {
        $medication = trim($med['medication'] ?? '');
        $dosage = trim($med['dosage'] ?? '');
        
        if ($medication !== '' && $dosage !== '') {
            $itemStmt->execute([
                'prescription_id' => $prescriptionId,
                'medication' => $medication,
                'dosage' => $dosage
            ]);
        }
    }

    echo json_encode(['success' => true, 'id' => $prescriptionId]);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}
