<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../models/PetOwner/MyPetsModel.php';

$clinicId = isset($_GET['clinic_id']) ? (int)$_GET['clinic_id'] : 0;

if ($clinicId <= 0) {
    echo json_encode(['error' => 'Invalid clinic ID']);
    exit;
}

$model = new MyPetsModel();
$vets = $model->getVetsByClinic($clinicId);

echo json_encode(['success' => true, 'vets' => $vets]);
