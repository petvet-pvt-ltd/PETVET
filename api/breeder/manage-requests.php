<?php
header('Content-Type: application/json');
require_once dirname(__DIR__, 2) . '/config/connect.php';

session_start();

// Check if user is logged in and is a breeder
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'get_all':
            getAllRequests($conn, $userId);
            break;
        
        case 'accept':
            acceptRequest($conn, $userId);
            break;
        
        case 'decline':
            declineRequest($conn, $userId);
            break;
        
        case 'complete':
            completeRequest($conn, $userId);
            break;
        
        case 'get_active_pets':
            getActivePets($conn, $userId);
            break;
        
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

function getAllRequests($conn, $userId) {
    $stmt = $conn->prepare("
        SELECT 
            br.*,
            u.first_name as owner_fname,
            u.last_name as owner_lname,
            u.phone as phone,
            '' as phone_2,
            u.email,
            bp.name as breeder_pet_name,
            bp.breed as breeder_pet_breed
        FROM breeding_requests br
        JOIN users u ON br.owner_id = u.id
        LEFT JOIN breeder_pets bp ON br.breeder_pet_id = bp.id
        WHERE br.breeder_id = ?
        ORDER BY br.requested_date DESC
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $pending = [];
    $approved = [];
    $completed = [];
    $declined = [];
    
    while ($row = $result->fetch_assoc()) {
        $row['owner_name'] = trim($row['owner_fname'] . ' ' . $row['owner_lname']);
        $row['pet_name'] = $row['owner_pet_name'];
        $row['breed'] = $row['owner_pet_breed'];
        $row['pet_breed'] = $row['owner_pet_breed'];
        $row['gender'] = $row['owner_pet_gender'];
        $row['requested_date'] = $row['requested_date'];
        
        switch ($row['status']) {
            case 'pending':
                $pending[] = $row;
                break;
            case 'approved':
                $approved[] = $row;
                break;
            case 'completed':
                $row['completion_date'] = $row['completed_date'];
                $completed[] = $row;
                break;
            case 'declined':
                $declined[] = $row;
                break;
        }
    }
    
    echo json_encode([
        'success' => true,
        'pending' => $pending,
        'approved' => $approved,
        'completed' => $completed,
        'declined' => $declined
    ]);
}

function acceptRequest($conn, $userId) {
    $requestId = $_POST['request_id'] ?? 0;
    $breederPetId = $_POST['breeder_pet_id'] ?? 0;
    $breedingDate = $_POST['breeding_date'] ?? null;
    $notes = $_POST['notes'] ?? '';
    
    if (empty($requestId) || empty($breederPetId)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        return;
    }
    
    // Check if request belongs to this breeder
    $checkStmt = $conn->prepare("SELECT id, preferred_date FROM breeding_requests WHERE id = ? AND breeder_id = ? AND status = 'pending'");
    $checkStmt->bind_param("ii", $requestId, $userId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized or request not found']);
        return;
    }
    
    $request = $result->fetch_assoc();
    
    // Use preferred date if no breeding date provided
    if (empty($breedingDate)) {
        $breedingDate = $request['preferred_date'];
    }
    
    // Verify breeder pet belongs to this breeder
    $petCheckStmt = $conn->prepare("SELECT id FROM breeder_pets WHERE id = ? AND breeder_id = ?");
    $petCheckStmt->bind_param("ii", $breederPetId, $userId);
    $petCheckStmt->execute();
    
    if ($petCheckStmt->get_result()->num_rows === 0) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Invalid breeding pet']);
        return;
    }
    
    $stmt = $conn->prepare("
        UPDATE breeding_requests 
        SET status = 'approved', 
            breeder_pet_id = ?, 
            breeding_date = ?,
            notes = ?,
            approved_date = NOW()
        WHERE id = ? AND breeder_id = ?
    ");
    $stmt->bind_param("issii", $breederPetId, $breedingDate, $notes, $requestId, $userId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Request accepted successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to accept request']);
    }
}

function declineRequest($conn, $userId) {
    $requestId = $_POST['request_id'] ?? 0;
    $reason = $_POST['reason'] ?? '';
    
    if (empty($requestId)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Request ID is required']);
        return;
    }
    
    // Check if request belongs to this breeder
    $checkStmt = $conn->prepare("SELECT id FROM breeding_requests WHERE id = ? AND breeder_id = ? AND status = 'pending'");
    $checkStmt->bind_param("ii", $requestId, $userId);
    $checkStmt->execute();
    
    if ($checkStmt->get_result()->num_rows === 0) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized or request not found']);
        return;
    }
    
    $stmt = $conn->prepare("
        UPDATE breeding_requests 
        SET status = 'declined', 
            decline_reason = ?,
            declined_date = NOW()
        WHERE id = ? AND breeder_id = ?
    ");
    $stmt->bind_param("sii", $reason, $requestId, $userId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Request declined successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to decline request']);
    }
}

function completeRequest($conn, $userId) {
    $requestId = $_POST['request_id'] ?? 0;
    $finalNotes = $_POST['final_notes'] ?? '';
    
    if (empty($requestId)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Request ID is required']);
        return;
    }
    
    // Check if request belongs to this breeder
    $checkStmt = $conn->prepare("SELECT id FROM breeding_requests WHERE id = ? AND breeder_id = ? AND status = 'approved'");
    $checkStmt->bind_param("ii", $requestId, $userId);
    $checkStmt->execute();
    
    if ($checkStmt->get_result()->num_rows === 0) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized or request not found']);
        return;
    }
    
    $stmt = $conn->prepare("
        UPDATE breeding_requests 
        SET status = 'completed', 
            final_notes = ?,
            completed_date = NOW()
        WHERE id = ? AND breeder_id = ?
    ");
    $stmt->bind_param("sii", $finalNotes, $requestId, $userId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Breeding marked as completed successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to complete request']);
    }
}

function getActivePets($conn, $userId) {
    $stmt = $conn->prepare("
        SELECT id, name, breed, gender
        FROM breeder_pets
        WHERE breeder_id = ? AND is_active = 1
        ORDER BY name ASC
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $pets = [];
    while ($row = $result->fetch_assoc()) {
        $pets[] = $row;
    }
    
    echo json_encode(['success' => true, 'pets' => $pets]);
}
