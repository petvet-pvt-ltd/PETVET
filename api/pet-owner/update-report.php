<?php
/**
 * Update Lost & Found Report API
 * Allows users to update their own reports
 */

session_start();
header('Content-Type: application/json');

// Enable CORS if needed
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, PUT');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../models/PetOwner/LostFoundModel.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Please log in.']);
    exit;
}

// Only accept POST/PUT requests
if (!in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT'])) {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $lostFoundModel = new LostFoundModel();
    $userId = $_SESSION['user_id'];
    
    // Get report ID
    $reportId = $_POST['report_id'] ?? $_GET['report_id'] ?? null;
    
    if (!$reportId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Report ID is required']);
        exit;
    }
    
    // Verify ownership - fetch existing report using model
    $existingReport = $lostFoundModel->getReportById($reportId);
    
    if (!$existingReport) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Report not found']);
        exit;
    }
    
    // Check ownership - read from user_id column if available, fallback to JSON
    $ownerUserId = $existingReport['user_id'] ?? null;
    $existingDescription = [];
    if (!empty($existingReport['description'])) {
        $existingDescription = json_decode($existingReport['description'], true);
        if (!is_array($existingDescription)) {
            $existingDescription = [];
        }
    }
    if (!$ownerUserId && !empty($existingDescription)) {
        $ownerUserId = $existingDescription['user_id'] ?? null;
    }
    if ($ownerUserId != $userId) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'You do not have permission to edit this report']);
        exit;
    }
    
    // Get updated data - prefer individual columns, fallback to JSON
    $type = $_POST['type'] ?? $existingReport['type'];
    $species = $_POST['species'] ?? ($existingReport['species'] ?? $existingDescription['species'] ?? '');
    $name = $_POST['name'] ?? ($existingReport['name'] ?? $existingDescription['name'] ?? '');
    $color = $_POST['color'] ?? ($existingReport['color'] ?? $existingDescription['color'] ?? '');
    $location = $_POST['location'] ?? $existingReport['location'];
    $latitude = $_POST['latitude'] ?? ($existingReport['latitude'] ?? $existingDescription['latitude'] ?? null);
    $longitude = $_POST['longitude'] ?? ($existingReport['longitude'] ?? $existingDescription['longitude'] ?? null);
    $date = $_POST['date'] ?? $existingReport['date_reported'];
    $time = $_POST['time'] ?? ($existingReport['time'] ?? $existingDescription['time'] ?? '');
    $notes = $_POST['notes'] ?? ($existingReport['notes'] ?? $existingDescription['notes'] ?? '');
    $phone = $_POST['phone'] ?? ($existingReport['phone'] ?? (isset($existingDescription['contact']['phone']) ? $existingDescription['contact']['phone'] : ''));
    $phone2 = $_POST['phone2'] ?? ($existingReport['phone2'] ?? (isset($existingDescription['contact']['phone2']) ? $existingDescription['contact']['phone2'] : ''));
    $email = $_POST['email'] ?? ($existingReport['email'] ?? (isset($existingDescription['contact']['email']) ? $existingDescription['contact']['email'] : ''));
    
    // Validate all fields using model
    $validation = $lostFoundModel->validateReportFields($type, $species, $name, $color, $location, $date, $time, $phone, $phone2, $email, $notes);
    
    if (!$validation['valid']) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validation['errors']
        ]);
        exit;
    }
    
    // Sanitize time - ensure it's in valid HH:MM format or empty
    if (!empty($time) && !preg_match('/^([0-1][0-9]|2[0-3]):([0-5][0-9])(?::([0-5][0-9]))?$/', $time)) {
        $time = null;
    }
    
    // Handle photo upload if new photos provided
    $photoPaths = $existingDescription['photos'] ?? [];
    
    if (isset($_FILES['photos']) && !empty($_FILES['photos']['name'][0])) {
        $uploadDir = __DIR__ . '/../../uploads/lost-found/';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Handle multiple photos
        $fileCount = count($_FILES['photos']['name']);
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        $newPhotos = [];
        
        for ($i = 0; $i < $fileCount; $i++) {
            if ($_FILES['photos']['error'][$i] === UPLOAD_ERR_OK) {
                $tmpName = $_FILES['photos']['tmp_name'][$i];
                $fileName = $_FILES['photos']['name'][$i];
                $fileSize = $_FILES['photos']['size'][$i];
                $fileType = $_FILES['photos']['type'][$i];
                
                // Validate file type
                if (!in_array($fileType, $allowedTypes)) {
                    continue; // Skip invalid files
                }
                
                // Validate file size
                if ($fileSize > $maxSize) {
                    continue; // Skip files that are too large
                }
                
                // Generate unique filename
                $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                $newFileName = uniqid('pet_' . $type . '_') . '.' . $ext;
                $destination = $uploadDir . $newFileName;
                
                // Move uploaded file
                if (move_uploaded_file($tmpName, $destination)) {
                    $newPhotos[] = '/PETVET/uploads/lost-found/' . $newFileName;
                }
            }
        }
        
        // If new photos uploaded, replace old ones
        if (!empty($newPhotos)) {
            $photoPaths = $newPhotos;
        }
    }
    
    // Prepare updated data array for individual column storage
    $updatedData = [
        'species' => $species,
        'name' => $name,
        'color' => $color,
        'breed' => null,
        'age' => null,
        'notes' => $notes,
        'time' => $time,
        'reward' => null,
        'urgency' => 'medium',
        'contact' => [
            'phone' => $phone,
            'phone2' => $phone2,
            'email' => $email
        ],
        'photos' => $photoPaths,
        'latitude' => $latitude,
        'longitude' => $longitude,
        'user_id' => $userId,
        'submitted_at' => $existingReport['submitted_at'] ?? date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    // Update database using model
    $lostFoundModel->updateReport($reportId, $type, $location, $date, $updatedData);
    
    // Return success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Report updated successfully',
        'report_id' => $reportId,
        'data' => [
            'type' => $type,
            'location' => $location,
            'date' => $date,
            'photos' => $photoPaths
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in update-report.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred'
    ]);
} catch (Exception $e) {
    error_log("Error in update-report.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred'
    ]);
}
