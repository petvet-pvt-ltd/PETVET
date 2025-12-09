<?php
/**
 * Manage Favorite Clinics API
 * Add, remove, and get favorite clinics for pet owners
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    $pdo = db();
    
    switch ($action) {
        case 'get':
            // Get all favorite clinics for this user
            $sql = "SELECT 
                        c.id,
                        c.clinic_name,
                        c.clinic_description,
                        c.clinic_logo,
                        c.clinic_address,
                        c.map_location,
                        c.city,
                        c.district,
                        c.clinic_phone,
                        c.clinic_email,
                        fc.created_at as favorited_at
                    FROM favorite_clinics fc
                    JOIN clinics c ON fc.clinic_id = c.id
                    WHERE fc.user_id = ?
                    AND c.is_active = 1
                    AND c.verification_status = 'approved'
                    ORDER BY fc.created_at DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$userId]);
            $favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'favorites' => $favorites,
                'total' => count($favorites)
            ]);
            break;
            
        case 'add':
            // Add clinic to favorites
            $clinicId = $_POST['clinic_id'] ?? null;
            
            if (!$clinicId) {
                echo json_encode(['success' => false, 'error' => 'Clinic ID required']);
                exit;
            }
            
            // Check if clinic exists and is active
            $stmt = $pdo->prepare("SELECT id FROM clinics WHERE id = ? AND is_active = 1 AND verification_status = 'approved'");
            $stmt->execute([$clinicId]);
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'error' => 'Clinic not found']);
                exit;
            }
            
            // Add to favorites (ignore if already exists due to UNIQUE constraint)
            $sql = "INSERT INTO favorite_clinics (user_id, clinic_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE created_at = created_at";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$userId, $clinicId]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Clinic added to favorites'
            ]);
            break;
            
        case 'remove':
            // Remove clinic from favorites
            $clinicId = $_POST['clinic_id'] ?? null;
            
            if (!$clinicId) {
                echo json_encode(['success' => false, 'error' => 'Clinic ID required']);
                exit;
            }
            
            $sql = "DELETE FROM favorite_clinics WHERE user_id = ? AND clinic_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$userId, $clinicId]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Clinic removed from favorites'
            ]);
            break;
            
        case 'check':
            // Check if clinic is favorited
            $clinicId = $_GET['clinic_id'] ?? null;
            
            if (!$clinicId) {
                echo json_encode(['success' => false, 'error' => 'Clinic ID required']);
                exit;
            }
            
            $sql = "SELECT COUNT(*) as is_favorite FROM favorite_clinics WHERE user_id = ? AND clinic_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$userId, $clinicId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'is_favorite' => $result['is_favorite'] > 0
            ]);
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    error_log("Favorites API error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
