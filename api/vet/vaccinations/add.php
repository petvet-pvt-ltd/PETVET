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
$appointmentId = 0;
if (isset($_POST['appointment_id'])) $appointmentId = (int)$_POST['appointment_id'];
if (isset($_POST['appointmentId'])) $appointmentId = (int)$_POST['appointmentId'];

// Get vaccines array from JSON
$vaccines = [];
if (isset($_POST['vaccines'])) {
    $vaccinesData = json_decode($_POST['vaccines'], true);
    if (is_array($vaccinesData) && count($vaccinesData) > 0) {
        $vaccines = $vaccinesData;
    }
}

if ($appointmentId <= 0 || empty($vaccines)) {
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

    // Create vaccination header (with placeholder for old columns)
    $stmt = $pdo->prepare("
        INSERT INTO vaccinations (appointment_id, vaccine, reports, created_at)
        VALUES (:appointment_id, :vaccine, :reports, NOW())
    ");
    $stmt->execute([
        'appointment_id' => $appointmentId,
        'vaccine' => 'See vaccination items',
        'reports' => $reports
    ]);

    $vaccinationId = (int)$pdo->lastInsertId();

    // Insert vaccines into vaccination_items
    $itemStmt = $pdo->prepare("
        INSERT INTO vaccination_items (vaccination_id, vaccine, next_due)
        VALUES (:vaccination_id, :vaccine, :next_due)
    ");

    foreach ($vaccines as $vac) {
        $vaccine = trim($vac['vaccine'] ?? '');
        $nextDue = trim($vac['nextDue'] ?? '');
        
        if ($vaccine !== '') {
            // Validate date
            $nextDueValue = null;
            if ($nextDue !== '') {
                $dt = DateTime::createFromFormat('Y-m-d', $nextDue);
                if ($dt && $dt->format('Y-m-d') === $nextDue) {
                    $nextDueValue = $nextDue;
                }
            }

            $itemStmt->execute([
                'vaccination_id' => $vaccinationId,
                'vaccine' => $vaccine,
                'next_due' => $nextDueValue
            ]);
        }
    }

    echo json_encode(['success' => true, 'id' => $vaccinationId]);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}
