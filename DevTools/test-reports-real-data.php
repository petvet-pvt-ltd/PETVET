<?php
session_start();
require_once '../config/connect.php';
require_once '../models/ClinicManager/ReportsModel.php';

// Simulate logged-in clinic manager
$_SESSION['user_id'] = 17; // Akila (clinic manager)
$_SESSION['clinic_id'] = 1;

$model = new ReportsModel();

echo "=== TESTING REPORTS MODEL WITH REAL DATA ===\n\n";

// Test with current week
$from = date('Y-m-d', strtotime('monday this week'));
$to = date('Y-m-d', strtotime('sunday this week'));

echo "Date Range: $from to $to\n";
echo "Mode: week\n";
echo "Clinic ID: 1\n\n";

$report = $model->getReport($from, $to, 'week', 1);

echo "--- APPOINTMENT STATUS ---\n";
foreach ($report['apptStatus'] as $status => $count) {
    echo "  $status: $count\n";
}

echo "\n--- VET WORKLOAD ---\n";
$workloadCount = 0;
foreach ($report['workload'] as $vet => $count) {
    if ($count > 0) {
        echo "  $vet: $count appointments\n";
        $workloadCount++;
    }
}
if ($workloadCount === 0) {
    echo "  No appointments this week\n";
}

echo "\n--- REVENUE ---\n";
echo "  Appointments Revenue: LKR " . number_format($report['appointmentsRevenue'], 2) . "\n";
echo "  Shop Revenue: LKR " . number_format($report['shopRevenue'], 2) . " (mock data)\n";
echo "  Gross Revenue: LKR " . number_format($report['grossRevenue'], 2) . "\n";
echo "  Net Income: LKR " . number_format($report['netIncome'], 2) . "\n";

echo "\n--- CHART DATA ---\n";
echo "  Labels: " . implode(', ', $report['labels']) . "\n";
echo "  Bars (revenue per bucket): " . implode(', ', array_map(fn($v) => number_format($v, 0), $report['bars'])) . "\n";

echo "\n=== NOW TESTING WITH BROADER DATE RANGE (THIS MONTH) ===\n\n";

$from2 = date('Y-m-01');
$to2 = date('Y-m-t');

echo "Date Range: $from2 to $to2\n";
echo "Mode: month\n\n";

$report2 = $model->getReport($from2, $to2, 'month', 1);

echo "--- APPOINTMENT STATUS ---\n";
foreach ($report2['apptStatus'] as $status => $count) {
    echo "  $status: $count\n";
}

echo "\n--- VET WORKLOAD ---\n";
$workloadCount2 = 0;
foreach ($report2['workload'] as $vet => $count) {
    if ($count > 0) {
        echo "  $vet: $count appointments\n";
        $workloadCount2++;
    }
}
if ($workloadCount2 === 0) {
    echo "  No appointments this month\n";
}

echo "\n--- REVENUE ---\n";
echo "  Appointments Revenue: LKR " . number_format($report2['appointmentsRevenue'], 2) . "\n";
echo "  Gross Revenue: LKR " . number_format($report2['grossRevenue'], 2) . "\n";
