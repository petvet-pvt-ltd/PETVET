<?php
/**
 * API Endpoint: Clinic Manager KPIs
 * Returns overview KPI data as JSON for AJAX refresh.
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['current_role']) || $_SESSION['current_role'] !== 'clinic_manager') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit;
}

require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../models/ClinicManager/OverviewModel.php';

try {
    $model = new OverviewModel();
    $data = $model->fetchOverviewData();

    echo json_encode([
        'success' => true,
        'kpis' => $data['kpis'] ?? [],
        'generated_at' => date('c')
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to load KPIs'
    ]);
}
