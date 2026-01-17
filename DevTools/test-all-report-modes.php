<?php
session_start();
require_once '../config/connect.php';
require_once '../models/ClinicManager/ReportsModel.php';

// Simulate logged-in clinic manager
$_SESSION['user_id'] = 17;
$_SESSION['clinic_id'] = 1;

$model = new ReportsModel();

echo "=== COMPREHENSIVE REPORTS TEST ===\n\n";

// Test all time range modes
$tests = [
    ['mode' => 'week', 'from' => date('Y-m-d', strtotime('monday this week')), 'to' => date('Y-m-d', strtotime('sunday this week'))],
    ['mode' => 'month', 'from' => date('Y-m-01'), 'to' => date('Y-m-t')],
    ['mode' => 'year', 'from' => date('Y-01-01'), 'to' => date('Y-12-31')],
    ['mode' => 'custom', 'from' => '2026-01-01', 'to' => '2026-01-31'],
];

foreach ($tests as $test) {
    echo "=== Testing Mode: {$test['mode']} ===\n";
    echo "Date Range: {$test['from']} to {$test['to']}\n\n";
    
    try {
        $report = $model->getReport($test['from'], $test['to'], $test['mode'], 1);
        
        $totalAppts = array_sum($report['apptStatus']);
        $totalVets = count(array_filter($report['workload'], fn($c) => $c > 0));
        
        echo "✓ Report generated successfully\n";
        echo "  - Total appointments: $totalAppts\n";
        echo "  - Active vets: $totalVets\n";
        echo "  - Revenue: LKR " . number_format($report['appointmentsRevenue'], 2) . "\n";
        echo "  - Chart labels: " . count($report['labels']) . " buckets\n";
        echo "  - Status breakdown: ";
        $statusParts = [];
        foreach ($report['apptStatus'] as $status => $count) {
            if ($count > 0) {
                $statusParts[] = "$status($count)";
            }
        }
        echo implode(', ', $statusParts) ?: 'None';
        echo "\n\n";
        
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n\n";
    }
}

echo "=== Testing with no clinic_id (should use session) ===\n";
try {
    $report = $model->getReport('2026-01-01', '2026-12-31', 'year', null);
    echo "✓ Report generated with session clinic_id\n";
    echo "  - Total appointments: " . array_sum($report['apptStatus']) . "\n";
    echo "  - Revenue: LKR " . number_format($report['appointmentsRevenue'], 2) . "\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n=== ALL TESTS COMPLETED ===\n";
