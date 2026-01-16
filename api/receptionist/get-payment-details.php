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

if (!isset($_GET['invoice'])) {
    echo json_encode(['success' => false, 'error' => 'Invoice number required']);
    exit;
}

$invoiceNumber = $_GET['invoice'];
$clinicId = (int)$_SESSION['clinic_id'];

try {
    $pdo = db();
    
    // Fetch payment details with appointment and client info
    $stmt = $pdo->prepare("
        SELECT 
            p.*,
            a.pet_id,
            a.appointment_date,
            CONCAT(u.first_name, ' ', u.last_name) as vet_name,
            CONCAT(po.first_name, ' ', po.last_name) as client_name,
            po.phone as client_phone_number,
            pet.name as pet_name
        FROM payments p
        INNER JOIN appointments a ON p.appointment_id = a.id
        LEFT JOIN users u ON a.vet_id = u.id
        LEFT JOIN users po ON a.pet_owner_id = po.id
        LEFT JOIN pets pet ON a.pet_id = pet.id
        WHERE p.invoice_number = ?
        AND a.clinic_id = ?
        AND a.status = 'paid'
    ");
    
    $stmt->execute([$invoiceNumber, $clinicId]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$payment) {
        echo json_encode(['success' => false, 'error' => 'Payment not found']);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'payment' => $payment
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
