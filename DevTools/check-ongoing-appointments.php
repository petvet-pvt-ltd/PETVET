<?php
require_once __DIR__ . '/../config/connect.php';

try {
    $pdo = db();
    
    echo "=== Checking Ongoing Appointments ===\n\n";
    
    // Check for ongoing appointments
    $stmt = $pdo->query("
        SELECT 
            a.id,
            a.appointment_date,
            a.appointment_time,
            a.status,
            a.vet_id,
            CONCAT(vet.first_name, ' ', vet.last_name) as vet_name,
            CONCAT(owner.first_name, ' ', owner.last_name) as owner_name,
            p.name as pet_name
        FROM appointments a
        LEFT JOIN users vet ON a.vet_id = vet.id
        LEFT JOIN users owner ON a.pet_owner_id = owner.id
        LEFT JOIN pets p ON a.pet_id = p.id
        WHERE a.status = 'ongoing'
        ORDER BY a.appointment_date DESC, a.appointment_time DESC
        LIMIT 10
    ");
    
    $ongoing = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($ongoing)) {
        echo "No appointments with status='ongoing' found in database.\n\n";
    } else {
        echo "Found " . count($ongoing) . " ongoing appointment(s):\n\n";
        foreach ($ongoing as $appt) {
            echo "ID: {$appt['id']}\n";
            echo "Date: {$appt['appointment_date']} {$appt['appointment_time']}\n";
            echo "Vet: {$appt['vet_name']} (ID: {$appt['vet_id']})\n";
            echo "Owner: {$appt['owner_name']}\n";
            echo "Pet: {$appt['pet_name']}\n";
            echo "Status: {$appt['status']}\n";
            echo "---\n";
        }
    }
    
    // Check today's approved appointments
    echo "\n=== Today's Approved Appointments ===\n\n";
    $stmt = $pdo->query("
        SELECT 
            a.id,
            a.appointment_time,
            a.status,
            CONCAT(vet.first_name, ' ', vet.last_name) as vet_name,
            p.name as pet_name
        FROM appointments a
        LEFT JOIN users vet ON a.vet_id = vet.id
        LEFT JOIN pets p ON a.pet_id = p.id
        WHERE a.appointment_date = CURDATE()
        AND a.status = 'approved'
        ORDER BY a.appointment_time
        LIMIT 10
    ");
    
    $approved = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($approved)) {
        echo "No approved appointments for today.\n";
    } else {
        echo "Found " . count($approved) . " approved appointment(s) for today:\n\n";
        foreach ($approved as $appt) {
            echo "ID: {$appt['id']} | {$appt['appointment_time']} | Vet: {$appt['vet_name']} | Pet: {$appt['pet_name']}\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
