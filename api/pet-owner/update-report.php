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
    $db = db();
    $userId = $_SESSION['user_id'];
    
    // Get report ID
    $reportId = $_POST['report_id'] ?? $_GET['report_id'] ?? null;
    
    if (!$reportId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Report ID is required']);
        exit;
    }
    
    // Verify ownership - fetch existing report
    $stmt = $db->prepare("SELECT * FROM LostFoundReport WHERE report_id = :report_id");
    $stmt->execute([':report_id' => $reportId]);
    $existingReport = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$existingReport) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Report not found']);
        exit;
    }
    
    // Check ownership
    $existingDescription = json_decode($existingReport['description'], true);
    if (($existingDescription['user_id'] ?? null) != $userId) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'You do not have permission to edit this report']);
        exit;
    }
    
    // Get updated data
    $type = $_POST['type'] ?? $existingReport['type'];
    $species = $_POST['species'] ?? $existingDescription['species'];
    $name = $_POST['name'] ?? $existingDescription['name'];
    $color = $_POST['color'] ?? $existingDescription['color'];
    $location = $_POST['location'] ?? $existingReport['location'];
    $date = $_POST['date'] ?? $existingReport['date_reported'];
    $notes = $_POST['notes'] ?? $existingDescription['notes'];
    $phone = $_POST['phone'] ?? $existingDescription['contact']['phone'];
    $phone2 = $_POST['phone2'] ?? $existingDescription['contact']['phone2'];
    $email = $_POST['email'] ?? $existingDescription['contact']['email'];
    
    // Validate required fields
    if (empty($type) || empty($species) || empty($location) || empty($date)) {
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'message' => 'Required fields: type, species, location, date'
        ]);
        exit;
    }
    
    // Validate type
    if (!in_array($type, ['lost', 'found'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid type. Must be "lost" or "found".']);
        exit;
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
    
    // Prepare updated description as JSON
    $updatedDescription = [
        'species' => $species,
        'name' => $name,
        'color' => $color,
        'notes' => $notes,
        'contact' => [
            'phone' => $phone,
            'phone2' => $phone2,
            'email' => $email
        ],
        'photos' => $photoPaths,
        'user_id' => $userId,
        'submitted_at' => $existingDescription['submitted_at'] ?? date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    $descriptionJson = json_encode($updatedDescription, JSON_UNESCAPED_UNICODE);
    
    // Update database
    $stmt = $db->prepare("
        UPDATE LostFoundReport 
        SET type = :type, 
            location = :location, 
            date_reported = :date_reported, 
            description = :description
        WHERE report_id = :report_id
    ");
    
    $stmt->execute([
        ':type' => $type,
        ':location' => $location,
        ':date_reported' => $date,
        ':description' => $descriptionJson,
        ':report_id' => $reportId
    ]);
    
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
        'message' => 'Database error occurred',
        'error' => $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("Error in update-report.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred',
        'error' => $e->getMessage()
    ]);
}
