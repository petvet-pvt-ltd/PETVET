<?php
/**
 * Medical Records File Upload Handler
 * Handles file uploads for medical reports and documents
 */

class MedicalFileUploader {
    private $uploadDir;
    private $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'];
    private $maxFileSize = 10485760; // 10MB
    
    public function __construct() {
        // Create upload directory structure
        $baseDir = __DIR__ . '/../uploads/medical-reports';
        if (!is_dir($baseDir)) {
            mkdir($baseDir, 0755, true);
        }
        $this->uploadDir = $baseDir;
    }
    
    /**
     * Upload multiple files
     * @param array $files - Files from $_FILES
     * @return array - Array of uploaded file paths or error
     */
    public function uploadFiles($files) {
        $uploadedFiles = [];
        $errors = [];
        
        if (empty($files['name'][0])) {
            return ['success' => true, 'files' => []]; // No files uploaded
        }
        
        $fileCount = count($files['name']);
        
        for ($i = 0; $i < $fileCount; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_NO_FILE) {
                continue; // Skip empty file inputs
            }
            
            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                $errors[] = "File upload error for {$files['name'][$i]}";
                continue;
            }
            
            // Validate file size
            if ($files['size'][$i] > $this->maxFileSize) {
                $errors[] = "File {$files['name'][$i]} exceeds maximum size of 10MB";
                continue;
            }
            
            // Validate file type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $files['tmp_name'][$i]);
            finfo_close($finfo);
            
            if (!in_array($mimeType, $this->allowedTypes)) {
                $errors[] = "File type not allowed for {$files['name'][$i]}";
                continue;
            }
            
            // Generate unique filename
            $extension = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
            $filename = uniqid('report_', true) . '_' . time() . '.' . $extension;
            $filepath = $this->uploadDir . '/' . $filename;
            
            // Move uploaded file
            if (move_uploaded_file($files['tmp_name'][$i], $filepath)) {
                $uploadedFiles[] = 'uploads/medical-reports/' . $filename;
            } else {
                $errors[] = "Failed to move file {$files['name'][$i]}";
            }
        }
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors, 'files' => $uploadedFiles];
        }
        
        return ['success' => true, 'files' => $uploadedFiles];
    }
    
    /**
     * Delete a file
     * @param string $filepath - Relative path to file
     * @return bool
     */
    public function deleteFile($filepath) {
        $fullPath = __DIR__ . '/../' . $filepath;
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }
    
    /**
     * Delete multiple files
     * @param array $filepaths - Array of relative file paths
     */
    public function deleteFiles($filepaths) {
        foreach ($filepaths as $filepath) {
            $this->deleteFile($filepath);
        }
    }
}
