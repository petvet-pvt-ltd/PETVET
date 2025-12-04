<?php
require_once __DIR__ . '/../config/connect.php';

$pdo = db();

echo "=== UNDERSTANDING CURRENT AVAILABILITY LOGIC ===\n\n";

// 1. Check how appointments track time conflicts
echo "1. APPOINTMENTS WITH OVERLAPPING TIMES:\n";
$query = "
    SELECT 
        a.id,
        a.appointment_date,
        a.appointment_time,
        a.duration_minutes,
        a.vet_id,
        a.clinic_id,
        a.status,
        TIME_FORMAT(a.appointment_time, '%H:%i') as start_time,
        TIME_FORMAT(ADDTIME(a.appointment_time, SEC_TO_TIME(a.duration_minutes * 60)), '%H:%i') as end_time
    FROM appointments a
    WHERE a.status IN ('pending', 'approved')
    ORDER BY a.appointment_date, a.appointment_time
    LIMIT 10
";
$appointments = $pdo->query($query)->fetchAll();
foreach($appointments as $apt) {
    echo "   Date: {$apt['appointment_date']}, Vet: {$apt['vet_id']}, Time: {$apt['start_time']} - {$apt['end_time']} ({$apt['duration_minutes']}min), Status: {$apt['status']}\n";
}

// 2. Check clinic preferences for slot duration
echo "\n2. CLINIC SLOT DURATIONS:\n";
$prefs = $pdo->query("SELECT clinic_id, slot_duration_minutes FROM clinic_preferences")->fetchAll();
foreach($prefs as $pref) {
    echo "   Clinic {$pref['clinic_id']}: {$pref['slot_duration_minutes']} minute slots\n";
}

// 3. Check clinic weekly schedules
echo "\n3. CLINIC WEEKLY SCHEDULES (Sample - Clinic 1):\n";
$schedule = $pdo->query("
    SELECT day_of_week, is_enabled, start_time, end_time 
    FROM clinic_weekly_schedule 
    WHERE clinic_id = 1
    ORDER BY FIELD(day_of_week, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')
")->fetchAll();
foreach($schedule as $day) {
    $status = $day['is_enabled'] ? '✓' : '✗';
    echo "   $status {$day['day_of_week']}: {$day['start_time']} - {$day['end_time']}\n";
}

// 4. Check blocked days
echo "\n4. BLOCKED DAYS:\n";
$blocked = $pdo->query("SELECT clinic_id, blocked_date, reason FROM clinic_blocked_days ORDER BY blocked_date")->fetchAll();
if (empty($blocked)) {
    echo "   No blocked days configured\n";
} else {
    foreach($blocked as $b) {
        echo "   Clinic {$b['clinic_id']}: {$b['blocked_date']} - {$b['reason']}\n";
    }
}

// 5. Check vets in system
echo "\n5. VETS IN SYSTEM:\n";
$vets = $pdo->query("
    SELECT u.id, u.first_name, u.last_name, cs.clinic_id
    FROM users u
    JOIN user_roles ur ON u.id = ur.user_id
    JOIN roles r ON ur.role_id = r.id
    LEFT JOIN clinic_staff cs ON u.id = cs.user_id
    WHERE r.role_name = 'vet'
    AND ur.is_active = 1
")->fetchAll();
foreach($vets as $vet) {
    echo "   ID: {$vet['id']}, Name: {$vet['first_name']} {$vet['last_name']}, Clinic: {$vet['clinic_id']}\n";
}

echo "\n=== SUMMARY ===\n";
echo "✅ Appointments table has: date, time, duration, vet_id, clinic_id\n";
echo "✅ Clinic schedules configured (weekly + blocked days)\n";
echo "✅ Slot durations stored per clinic\n";
echo "✅ Vet availability = Check conflicting appointments\n";
echo "\n💡 For vet availability: Query appointments where vet_id matches and time overlaps\n";
?>
