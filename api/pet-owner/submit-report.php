<?php
/**
 * Lost & Found Pet Report Submission API
 * Handles form submissions from the Report Pet modal
 */

session_start();
header('Content-Type: application/json');

// Enable CORS if needed
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../models/PetOwner/LostFoundModel.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Please log in.']);
    exit;
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Get form data
    $type = $_POST['type'] ?? '';
    $species = $_POST['species'] ?? '';
    $name = $_POST['name'] ?? '';
    $color = $_POST['color'] ?? '';
    $location = $_POST['location'] ?? '';
    $latitude = $_POST['latitude'] ?? null;
    $longitude = $_POST['longitude'] ?? null;
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '';
    $notes = $_POST['notes'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $phone2 = $_POST['phone2'] ?? '';
    $email = $_POST['email'] ?? '';
    
    // Validate all fields using model
    $lostFoundModel = new LostFoundModel();
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
    
    // Sanitize time - ensure it's in valid HH:MM format or empty/null
    if (!empty($time) && !preg_match('/^([0-1][0-9]|2[0-3]):([0-5][0-9])(?::([0-5][0-9]))?$/', $time)) {
        $time = null;
    }
    
    // Handle photo upload
    $photoPath = null;
    $photoPaths = [];
    
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
                    $photoPaths[] = '/PETVET/uploads/lost-found/' . $newFileName;
                }
            }
        }
        
        // Use first photo as primary
        $photoPath = !empty($photoPaths) ? $photoPaths[0] : null;
    }
    
    // Prepare data array for individual column storage
    $reportData = [
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
        'user_id' => $_SESSION['user_id'],
        'submitted_at' => date('Y-m-d H:i:s')
    ];
    
    $lostFoundModel = new LostFoundModel();
    $reportId = $lostFoundModel->insertReport($type, $location, $date, $reportData);
    
    // Return success response
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Report submitted successfully',
        'report_id' => $reportId,
        'data' => [
            'type' => $type,
            'location' => $location,
            'date' => $date,
            'photos' => $photoPaths
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in submit-report.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred'
    ]);
} catch (Exception $e) {
    error_log("Error in submit-report.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred'
    ]);
}
