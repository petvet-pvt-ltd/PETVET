<?php
require_once __DIR__ . '/../config/connect.php';

$pdo = db();

$sql = "SELECT 
            u.id,
            CONCAT(u.first_name, ' ', u.last_name) as name,
            spp.business_name,
            spp.service_area,
            spp.experience_years,
            spp.specializations,
            CASE WHEN COALESCE(TRIM(spp.bio), '') = '' THEN 'EMPTY' ELSE 'HAS BIO' END as bio_status,
            LENGTH(spp.bio) as bio_length,
            spp.phone_primary,
            spp.training_basic_enabled,
            spp.training_intermediate_enabled,
            spp.training_advanced_enabled
        FROM users u
        INNER JOIN service_provider_profiles spp ON u.id = spp.user_id
        INNER JOIN user_roles ur ON u.id = ur.user_id
        INNER JOIN roles r ON ur.role_id = r.id
        WHERE r.role_name = 'trainer' 
        AND ur.verification_status = 'approved'
        AND ur.is_active = 1
        AND spp.role_type = 'trainer'";

$stmt = $pdo->query($sql);
$trainers = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Total trainers found: " . count($trainers) . "\n\n";

foreach ($trainers as $trainer) {
    echo "Trainer ID: {$trainer['id']}\n";
    echo "Name: {$trainer['name']}\n";
    echo "Business Name: " . ($trainer['business_name'] ?: 'EMPTY') . "\n";
    echo "Service Area: " . ($trainer['service_area'] ?: 'EMPTY') . "\n";
    echo "Experience Years: " . ($trainer['experience_years'] ?: 'EMPTY') . "\n";
    echo "Specializations: " . ($trainer['specializations'] ?: 'EMPTY') . "\n";
    echo "Bio Status: {$trainer['bio_status']} (Length: {$trainer['bio_length']})\n";
    echo "Phone Primary: " . ($trainer['phone_primary'] ?: 'EMPTY') . "\n";
    echo "Training Types: ";
    $types = [];
    if ($trainer['training_basic_enabled']) $types[] = 'Basic';
    if ($trainer['training_intermediate_enabled']) $types[] = 'Intermediate';
    if ($trainer['training_advanced_enabled']) $types[] = 'Advanced';
    echo implode(', ', $types) ?: 'NONE';
    echo "\n";
    echo str_repeat('-', 80) . "\n\n";
}
