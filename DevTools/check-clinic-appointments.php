<?php
require_once '../config/connect.php';
$pdo = db();

echo "=== CHECKING APPOINTMENTS DATA ===\n\n";

// Check total appointments in clinic 1
$stmt = $pdo->query("SELECT COUNT(*) as total FROM appointments WHERE clinic_id = 1");
$total = $stmt->fetchColumn();
echo "Total appointments in clinic 1: $total\n\n";

// Check appointments by status
echo "--- Appointments by Status ---\n";
$stmt = $pdo->query("SELECT status, COUNT(*) as count FROM appointments WHERE clinic_id = 1 GROUP BY status ORDER BY count DESC");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "  {$row['status']}: {$row['count']}\n";
}

// Check date range of appointments
echo "\n--- Date Range of Appointments ---\n";
$stmt = $pdo->query("SELECT MIN(appointment_date) as earliest, MAX(appointment_date) as latest FROM appointments WHERE clinic_id = 1");
$range = $stmt->fetch(PDO::FETCH_ASSOC);
echo "  Earliest: {$range['earliest']}\n";
echo "  Latest: {$range['latest']}\n";

// Check recent appointments (last 30 days)
echo "\n--- Recent Appointments (Last 30 days) ---\n";
$stmt = $pdo->query("
    SELECT 
        a.appointment_date,
        a.status,
        CONCAT(u.first_name, ' ', u.last_name) as vet_name,
        p.total_amount
    FROM appointments a
    LEFT JOIN users u ON a.vet_id = u.id
    LEFT JOIN payments p ON a.id = p.appointment_id
    WHERE a.clinic_id = 1 
    AND a.appointment_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    ORDER BY a.appointment_date DESC
    LIMIT 10
");

$count = 0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $amount = $row['total_amount'] ? 'LKR ' . number_format($row['total_amount'], 2) : 'No payment';
    echo "  {$row['appointment_date']} | {$row['vet_name']} | {$row['status']} | $amount\n";
    $count++;
}

if ($count === 0) {
    echo "  No appointments in the last 30 days\n";
}
