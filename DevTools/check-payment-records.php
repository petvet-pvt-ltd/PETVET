<?php
require_once __DIR__ . '/../config/connect.php';

$pdo = db();

echo "=== Checking Payment Records ===\n\n";

// Check payments table
$stmt = $pdo->query("SELECT COUNT(*) as count FROM payments");
$count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
echo "Total payments in database: {$count}\n\n";

// Get all payment records
$stmt = $pdo->query("
    SELECT 
        p.id,
        p.invoice_number,
        p.appointment_id,
        p.payment_date,
        p.total_amount
    FROM payments p
    ORDER BY p.id
");
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Payment Records:\n";
foreach ($payments as $payment) {
    echo "  ID: {$payment['id']}, Invoice: {$payment['invoice_number']}, Appt: {$payment['appointment_id']}, Date: {$payment['payment_date']}, Amount: {$payment['total_amount']}\n";
}

echo "\n=== Checking Appointments with 'paid' status ===\n\n";

$stmt = $pdo->query("
    SELECT id, pet_owner_id, pet_id, status, appointment_date 
    FROM appointments 
    WHERE status = 'paid'
    ORDER BY id
");
$paidAppts = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Total 'paid' appointments: " . count($paidAppts) . "\n";
foreach ($paidAppts as $appt) {
    echo "  Appt ID: {$appt['id']}, Status: {$appt['status']}, Date: {$appt['appointment_date']}\n";
}

echo "\n=== Query Result Preview ===\n\n";

// Run the same query as the controller
$clinicId = 1; // Assume clinic 1
$stmt = $pdo->prepare("
    SELECT 
        p.invoice_number,
        p.payment_date as date,
        p.total_amount as amount,
        u.first_name AS owner_first_name,
        u.last_name AS owner_last_name,
        pet.name AS pet_name,
        v.first_name AS vet_first_name,
        v.last_name AS vet_last_name,
        a.appointment_type
    FROM payments p
    JOIN appointments a ON p.appointment_id = a.id
    JOIN users u ON a.pet_owner_id = u.id
    JOIN pets pet ON a.pet_id = pet.id
    JOIN users v ON a.vet_id = v.id
    WHERE a.clinic_id = :clinic_id
      AND a.status = 'paid'
    ORDER BY p.payment_date DESC, p.created_at DESC
");
$stmt->execute(['clinic_id' => $clinicId]);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Records that will show on page (Clinic ID: {$clinicId}):\n";
echo "Total: " . count($records) . "\n\n";
foreach ($records as $rec) {
    echo "  Invoice: {$rec['invoice_number']}, Client: {$rec['owner_first_name']} {$rec['owner_last_name']}, Pet: {$rec['pet_name']}, Amount: {$rec['amount']}\n";
}
