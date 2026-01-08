<?php
/**
 * Test Script for Medical Reports Upload Feature
 * Tests database, directory, file upload, and PHP configuration
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/connect.php';

$test = $_GET['test'] ?? '';
$results = [];

switch ($test) {
    case 'database':
        // Test if reports columns exist
        try {
            $pdo = db();
            
            $tables = ['medical_records', 'prescriptions', 'vaccinations'];
            $allPass = true;
            
            foreach ($tables as $table) {
                $stmt = $pdo->query("SHOW COLUMNS FROM $table LIKE 'reports'");
                $column = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $results[$table] = [
                    'exists' => !empty($column),
                    'type' => $column['Type'] ?? 'N/A',
                    'null' => $column['Null'] ?? 'N/A',
                    'default' => $column['Default'] ?? 'N/A'
                ];
                
                if (empty($column)) {
                    $allPass = false;
                }
            }
            
            echo json_encode([
                'success' => $allPass,
                'results' => $results,
                'message' => $allPass ? 'All tables have reports column' : 'Some tables missing reports column'
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        break;
        
    case 'directory':
        // Test upload directory
        $uploadDir = __DIR__ . '/../uploads/medical-reports';
        
        $exists = is_dir($uploadDir);
        $writable = $exists ? is_writable($uploadDir) : false;
        
        if (!$exists) {
            // Try to create it
            $created = mkdir($uploadDir, 0755, true);
            echo json_encode([
                'success' => $created,
                'results' => [
                    'path' => $uploadDir,
                    'existed' => false,
                    'created' => $created,
                    'writable' => $created ? is_writable($uploadDir) : false
                ],
                'message' => $created ? 'Directory created successfully' : 'Could not create directory',
                'suggestion' => !$created ? 'Please create the directory manually and set permissions to 755' : null
            ]);
        } else {
            echo json_encode([
                'success' => $writable,
                'results' => [
                    'path' => $uploadDir,
                    'exists' => true,
                    'writable' => $writable,
                    'permissions' => substr(sprintf('%o', fileperms($uploadDir)), -4)
                ],
                'message' => $writable ? 'Directory exists and is writable' : 'Directory exists but is not writable',
                'suggestion' => !$writable ? 'Please set directory permissions to 755 or 777' : null
            ]);
        }
        break;
        
    case 'upload':
        // Test actual file upload
        if (!isset($_FILES['testFile'])) {
            echo json_encode([
                'success' => false,
                'error' => 'No file uploaded'
            ]);
            break;
        }
        
        require_once __DIR__ . '/../config/MedicalFileUploader.php';
        
        try {
            $uploader = new MedicalFileUploader();
            
            // Convert single file to array format
            $filesArray = [
                'name' => [$_FILES['testFile']['name']],
                'type' => [$_FILES['testFile']['type']],
                'tmp_name' => [$_FILES['testFile']['tmp_name']],
                'error' => [$_FILES['testFile']['error']],
                'size' => [$_FILES['testFile']['size']]
            ];
            
            $result = $uploader->uploadFiles($filesArray);
            
            echo json_encode([
                'success' => $result['success'],
                'results' => $result,
                'message' => $result['success'] ? 'File uploaded successfully' : 'Upload failed'
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        break;
        
    case 'php':
        // Check PHP configuration
        $config = [
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'max_file_uploads' => ini_get('max_file_uploads'),
            'file_uploads' => ini_get('file_uploads') ? 'On' : 'Off',
            'memory_limit' => ini_get('memory_limit')
        ];
        
        echo json_encode([
            'success' => true,
            'results' => $config
        ]);
        break;
        
    default:
        echo json_encode([
            'success' => false,
            'error' => 'Invalid test type. Use: database, directory, upload, or php'
        ]);
}
