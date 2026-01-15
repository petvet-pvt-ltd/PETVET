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

$appointmentId = isset($_GET['appointment_id']) ? (int)$_GET['appointment_id'] : 0;

if ($appointmentId <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid appointment ID']);
    exit;
}

$clinicId = (int)$_SESSION['clinic_id'];

try {
    $pdo = db();

    // Verify appointment belongs to this clinic
    $stmt = $pdo->prepare("
        SELECT id, status 
        FROM appointments 
        WHERE id = ? AND clinic_id = ?
    ");
    $stmt->execute([$appointmentId, $clinicId]);
    $appt = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$appt) {
        echo json_encode(['success' => false, 'error' => 'Appointment not found']);
        exit;
    }

    // Get clinic data
    $stmt = $pdo->prepare("
        SELECT 
            clinic_name,
            clinic_address,
            clinic_phone,
            clinic_email,
            clinic_logo
        FROM clinics 
        WHERE id = ?
    ");
    $stmt->execute([$clinicId]);
    $clinic = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$clinic) {
        echo json_encode(['success' => false, 'error' => 'Clinic not found']);
        exit;
    }

    // Get prescriptions (support both legacy single-row + new per-item tables)
    $stmt = $pdo->prepare("
        SELECT id, medication, dosage, notes
        FROM prescriptions
        WHERE appointment_id = ?
        ORDER BY created_at
    ");
    $stmt->execute([$appointmentId]);
    $prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $prescriptionIds = array_values(array_filter(array_map(fn($r) => (int)($r['id'] ?? 0), $prescriptions)));
    $prescriptionItemsById = [];
    if (!empty($prescriptionIds)) {
        $placeholders = implode(',', array_fill(0, count($prescriptionIds), '?'));
        $stmt = $pdo->prepare("
            SELECT prescription_id, medication, dosage
            FROM prescription_items
            WHERE prescription_id IN ($placeholders)
            ORDER BY prescription_id, id
        ");
        $stmt->execute($prescriptionIds);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $pid = (int)$row['prescription_id'];
            if (!isset($prescriptionItemsById[$pid])) $prescriptionItemsById[$pid] = [];
            $prescriptionItemsById[$pid][] = [
                'medication' => $row['medication'] ?? '',
                'dosage' => $row['dosage'] ?? ''
            ];
        }
    }

    $medications = [];
    foreach ($prescriptions as $pr) {
        $pid = (int)($pr['id'] ?? 0);
        $med = trim((string)($pr['medication'] ?? ''));
        $dos = trim((string)($pr['dosage'] ?? ''));
        $isPlaceholder = ($med === '' || strcasecmp($med, 'See prescription items') === 0 || strcasecmp($dos, 'Multiple medications') === 0);

        if ($isPlaceholder && $pid > 0 && !empty($prescriptionItemsById[$pid])) {
            foreach ($prescriptionItemsById[$pid] as $it) {
                $medications[] = [
                    'id' => $pid,
                    'medication' => $it['medication'],
                    'dosage' => $it['dosage'],
                    'notes' => $pr['notes'] ?? null
                ];
            }
        } elseif ($med !== '') {
            // Legacy single-row medication stored directly on prescriptions
            $medications[] = [
                'id' => $pid,
                'medication' => $med,
                'dosage' => $dos,
                'notes' => $pr['notes'] ?? null
            ];
        }
    }

    // Get vaccinations (support both legacy single-row + new per-item tables)
    $stmt = $pdo->prepare("
        SELECT id, vaccine, next_due
        FROM vaccinations
        WHERE appointment_id = ?
        ORDER BY created_at
    ");
    $stmt->execute([$appointmentId]);
    $vaccinationHeaders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $vaccinationIds = array_values(array_filter(array_map(fn($r) => (int)($r['id'] ?? 0), $vaccinationHeaders)));
    $vaccinationItemsById = [];
    if (!empty($vaccinationIds)) {
        $placeholders = implode(',', array_fill(0, count($vaccinationIds), '?'));
        $stmt = $pdo->prepare("
            SELECT vaccination_id, vaccine, next_due
            FROM vaccination_items
            WHERE vaccination_id IN ($placeholders)
            ORDER BY vaccination_id, id
        ");
        $stmt->execute($vaccinationIds);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $vid = (int)$row['vaccination_id'];
            if (!isset($vaccinationItemsById[$vid])) $vaccinationItemsById[$vid] = [];
            $vaccinationItemsById[$vid][] = [
                'vaccine' => $row['vaccine'] ?? '',
                'next_due' => $row['next_due'] ?? null
            ];
        }
    }

    $vaccinations = [];
    foreach ($vaccinationHeaders as $vh) {
        $vid = (int)($vh['id'] ?? 0);
        $vac = trim((string)($vh['vaccine'] ?? ''));
        $isPlaceholder = ($vac === '' || strcasecmp($vac, 'See vaccination items') === 0);

        if ($isPlaceholder && $vid > 0 && !empty($vaccinationItemsById[$vid])) {
            foreach ($vaccinationItemsById[$vid] as $it) {
                $vaccinations[] = [
                    'id' => $vid,
                    'vaccine' => $it['vaccine'],
                    'next_due' => $it['next_due']
                ];
            }
        } elseif ($vac !== '') {
            $vaccinations[] = [
                'id' => $vid,
                'vaccine' => $vac,
                'next_due' => $vh['next_due'] ?? null
            ];
        }
    }

    echo json_encode([
        'success' => true,
        'clinic' => [
            'name' => $clinic['clinic_name'] ?? 'PetVet Clinic',
            'address' => $clinic['clinic_address'] ?? '',
            'phone' => $clinic['clinic_phone'] ?? '',
            'email' => $clinic['clinic_email'] ?? '',
            'logo' => $clinic['clinic_logo'] ?? '/PETVET/views/shared/images/sidebar/petvet-logo-web.png'
        ],
        'medications' => $medications,
        'vaccinations' => $vaccinations
    ]);

} catch (Exception $e) {
    error_log("get-invoice-data.php error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
