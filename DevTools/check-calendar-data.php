<?php
require_once '../config/connect.php';

echo "=== HAPPY PAWS WEEKLY SCHEDULE ===\n";
$clinic = mysqli_query($conn, "SELECT * FROM clinic_weekly_schedule WHERE clinic_id = 1 ORDER BY FIELD(day_of_week, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')");
while($row = mysqli_fetch_assoc($clinic)) {
    $status = $row['is_enabled'] ? 'OPEN' : 'CLOSED';
    echo $row['day_of_week'] . ': ' . $status . "\n";
}

echo "\n=== BLOCKED DATES ===\n";
$blocked = mysqli_query($conn, "SELECT * FROM clinic_blocked_days WHERE clinic_id = 1 AND blocked_date >= CURDATE() ORDER BY blocked_date");
while($row = mysqli_fetch_assoc($blocked)) {
    echo $row['blocked_date'] . ' - ' . $row['reason'] . "\n";
}

echo "\n=== TESTING GET-AVAILABLE-DATES API ===\n";
echo "Simulating API call for clinic_id=1...\n\n";

// Simulate the API logic
$disabledDates = [];
$startDate = new DateTime();
$endDate = new DateTime();
$endDate->modify('+30 days');

$period = new DatePeriod($startDate, new DateInterval('P1D'), $endDate);

foreach ($period as $date) {
    $dateString = $date->format('Y-m-d');
    $dayOfWeek = strtolower($date->format('l'));
    
    // Check weekly schedule
    $scheduleQuery = "SELECT is_enabled FROM clinic_weekly_schedule WHERE clinic_id = 1 AND day_of_week = '$dayOfWeek'";
    $scheduleResult = mysqli_query($conn, $scheduleQuery);
    $scheduleRow = mysqli_fetch_assoc($scheduleResult);
    
    if ($scheduleRow && !$scheduleRow['is_enabled']) {
        $disabledDates[] = $dateString;
        echo "$dateString ($dayOfWeek) - DISABLED (clinic closed)\n";
    }
    
    // Check blocked days
    $blockedQuery = "SELECT * FROM clinic_blocked_days WHERE clinic_id = 1 AND blocked_date = '$dateString'";
    $blockedResult = mysqli_query($conn, $blockedQuery);
    
    if (mysqli_num_rows($blockedResult) > 0) {
        if (!in_array($dateString, $disabledDates)) {
            $disabledDates[] = $dateString;
        }
        $blockedRow = mysqli_fetch_assoc($blockedResult);
        echo "$dateString ($dayOfWeek) - BLOCKED: " . $blockedRow['reason'] . "\n";
    }
}

echo "\n=== TOTAL DISABLED DATES: " . count($disabledDates) . " ===\n";
echo json_encode($disabledDates, JSON_PRETTY_PRINT);
