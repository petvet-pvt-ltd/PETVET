<?php
/**
 * Clinic Manager Delivery Settings API
 * Handle saving and loading shop delivery settings
 */

session_start();
require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../config/Auth.php';

header('Content-Type: application/json');

// Check authentication
$auth = new Auth();
if (!$auth->isLoggedIn() || !$auth->hasRole('clinic_manager')) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userId = $auth->getUserId();
$pdo = db();

// Get clinic ID for the current manager
function getClinicId($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT clinic_id FROM clinic_manager_profiles WHERE user_id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetch();
    return $result ? $result['clinic_id'] : null;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'get_settings':
        $clinicId = getClinicId($pdo, $userId);
        
        if (!$clinicId) {
            echo json_encode(['success' => false, 'message' => 'Clinic not found']);
            exit;
        }
        
        $stmt = $pdo->prepare("
            SELECT base_delivery_charge, max_delivery_distance, max_items_per_order, delivery_rules, is_active
            FROM clinic_shop_settings
            WHERE clinic_id = ?
        ");
        $stmt->execute([$clinicId]);
        $settings = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($settings) {
            // Decode JSON rules
            $settings['delivery_rules'] = json_decode($settings['delivery_rules'], true) ?? [];
            $settings['base_delivery_charge'] = (float)$settings['base_delivery_charge'];
            $settings['max_delivery_distance'] = (float)$settings['max_delivery_distance'];
            $settings['max_items_per_order'] = (int)$settings['max_items_per_order'];
            $settings['is_active'] = (bool)$settings['is_active'];
        } else {
            // Return default settings
            $settings = [
                'base_delivery_charge' => 0,
                'max_delivery_distance' => 0,
                'max_items_per_order' => 10,
                'delivery_rules' => [],
                'is_active' => true
            ];
        }
        
        echo json_encode(['success' => true, 'settings' => $settings]);
        break;
        
    case 'save_settings':
        $clinicId = getClinicId($pdo, $userId);
        
        if (!$clinicId) {
            echo json_encode(['success' => false, 'message' => 'Clinic not found']);
            exit;
        }
        
        $baseCharge = floatval($_POST['base_delivery_charge'] ?? 0);
        $maxDistance = floatval($_POST['max_delivery_distance'] ?? 0);
        $maxItems = intval($_POST['max_items_per_order'] ?? 10);
        $rulesJson = $_POST['delivery_rules'] ?? '[]';
        
        // Validate max items range
        if ($maxItems < 5 || $maxItems > 10) {
            echo json_encode(['success' => false, 'message' => 'Maximum items per order must be between 5 and 10']);
            exit;
        }
        
        // Validate JSON
        $rules = json_decode($rulesJson, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(['success' => false, 'message' => 'Invalid delivery rules format']);
            exit;
        }
        
        // Validate rules structure
        foreach ($rules as $rule) {
            if (!isset($rule['distance']) || !isset($rule['charge_per_km'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid rule structure']);
                exit;
            }
        }
        
        // Convert rules to proper format for storage
        $formattedRules = array_map(function($rule) {
            return [
                'distance' => (float)$rule['distance'],
                'charge_per_km' => (float)($rule['charge_per_km'] ?? $rule['charge'])
            ];
        }, $rules);
        
        try {
            // Check if settings exist
            $stmt = $pdo->prepare("SELECT id FROM clinic_shop_settings WHERE clinic_id = ?");
            $stmt->execute([$clinicId]);
            $existing = $stmt->fetch();
            
            if ($existing) {
                // Update existing settings
                $stmt = $pdo->prepare("
                    UPDATE clinic_shop_settings 
                    SET base_delivery_charge = ?, 
                        max_delivery_distance = ?, 
                        max_items_per_order = ?,
                        delivery_rules = ?,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE clinic_id = ?
                ");
                $stmt->execute([
                    $baseCharge,
                    $maxDistance,
                    $maxItems,
                    json_encode($formattedRules),
                    $clinicId
                ]);
            } else {
                // Insert new settings
                $stmt = $pdo->prepare("
                    INSERT INTO clinic_shop_settings 
                    (clinic_id, base_delivery_charge, max_delivery_distance, max_items_per_order, delivery_rules) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $clinicId,
                    $baseCharge,
                    $maxDistance,
                    $maxItems,
                    json_encode($formattedRules)
                ]);
            }
            
            echo json_encode(['success' => true, 'message' => 'Delivery settings saved successfully']);
        } catch (PDOException $e) {
            error_log("Delivery settings save error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database error occurred']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>
