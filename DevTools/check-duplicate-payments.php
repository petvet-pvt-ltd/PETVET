<?php
/**
 * Check for duplicate payment records
 */

require_once __DIR__ . '/../config/connect.php';

try {
    $pdo = db();
    
    echo "=== Checking Payment Records ===\n\n";
    
    // Check payments table
    echo "PAYMENTS TABLE:\n";
    echo str_repeat("-", 80) . "\n";
    $stmt = $pdo->query("
        SELECT id, appointment_id, amount, payment_method, payment_date
        FROM payments
        ORDER BY appointment_id, id
    ");
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($payments)) {
        echo "No payment records found.\n\n";
    } else {
        foreach ($payments as $payment) {
            echo "Payment ID: {$payment['id']} | ";
            echo "Appointment ID: {$payment['appointment_id']} | ";
            echo "Amount: {$payment['amount']} | ";
            echo "Method: {$payment['payment_method']} | ";
            echo "Date: {$payment['payment_date']}\n";
        }
        echo "\nTotal payment records: " . count($payments) . "\n\n";
    }
    
    // Check for duplicates
    echo "DUPLICATE CHECK:\n";
    echo str_repeat("-", 80) . "\n";
    $stmt = $pdo->query("
        SELECT appointment_id, COUNT(*) as count
        FROM payments
        GROUP BY appointment_id
        HAVING count > 1
    ");
    $duplicates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($duplicates)) {
        echo "✅ No duplicate payments found.\n\n";
    } else {
        echo "❌ Found duplicate payments:\n";
        foreach ($duplicates as $dup) {
            echo "  Appointment ID {$dup['appointment_id']}: {$dup['count']} payment records\n";
        }
        echo "\n";
    }
    
    // Check appointments with status='paid'
    echo "APPOINTMENTS WITH STATUS='paid':\n";
    echo str_repeat("-", 80) . "\n";
    $stmt = $pdo->query("
        SELECT id, patient_name, appointment_date, service_type, status, total_fee
        FROM appointments
        WHERE status = 'paid'
        ORDER BY id
    ");
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($appointments)) {
        echo "No paid appointments found.\n\n";
    } else {
        foreach ($appointments as $apt) {
            echo "Appointment ID: {$apt['id']} | ";
            echo "Patient: {$apt['patient_name']} | ";
            echo "Status: {$apt['status']} | ";
            echo "Fee: {$apt['total_fee']}\n";
        }
        echo "\nTotal paid appointments: " . count($appointments) . "\n\n";
    }
    
    echo "=== EXPLANATION ===\n";
    echo "If you see 3 payment records for 1 appointment:\n";
    echo "→ The 'Confirm Payment' button was clicked 3 times\n";
    echo "→ Each click created a new payment record\n";
    echo "→ The appointment table has 1 record (correct)\n";
    echo "→ The payments table has 3 records (incorrect - should be 1)\n\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
