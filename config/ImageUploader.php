<?php
/**
 * Image Upload Utility for Product Images
 * Handles image validation, upload, and deletion
 */

class ImageUploader {
    
    private $uploadDir;
    private $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    private $maxFileSize = 5242880; // 5MB in bytes
    
    public function __construct($uploadDir = null) {
        $this->uploadDir = $uploadDir ?? __DIR__ . '/../public/images/products/';
        
        // Create directory if it doesn't exist
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }
    
    /**
     * Upload an image file
     * 
     * @param array $file The $_FILES['fieldname'] array
     * @param string $prefix Optional filename prefix (default: 'product_')
     * @return array ['success' => bool, 'path' => string|null, 'message' => string]
     */
    public function upload($file, $prefix = 'product_') {
        // Check if file was uploaded
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'path' => null,
                'message' => 'No file uploaded or upload error occurred'
            ];
        }
        
        // Validate file type
        if (!in_array($file['type'], $this->allowedTypes)) {
            return [
                'success' => false,
                'path' => null,
                'message' => 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed'
            ];
        }
        
        // Validate file size
        if ($file['size'] > $this->maxFileSize) {
            return [
                'success' => false,
                'path' => null,
                'message' => 'File size exceeds 5MB limit'
            ];
        }
        
        // Validate actual image
        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            return [
                'success' => false,
                'path' => null,
                'message' => 'File is not a valid image'
            ];
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $prefix . time() . '_' . uniqid() . '.' . strtolower($extension);
        $uploadPath = $this->uploadDir . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            // Return relative path for database storage
            $relativePath = '/PETVET/public/images/products/' . $filename;
            
            return [
                'success' => true,
                'path' => $relativePath,
                'message' => 'Image uploaded successfully'
            ];
        } else {
            return [
                'success' => false,
                'path' => null,
                'message' => 'Failed to move uploaded file'
            ];
        }
    }
    
    /**
     * Delete an image file from server
     * 
     * @param string $imagePath The database path (e.g., /PETVET/public/images/products/abc.jpg)
     * @return bool True if deleted or doesn't exist, false on error
     */
    public function delete($imagePath) {
        // Only delete if it's a local file (not external URL)
        if (empty($imagePath) || strpos($imagePath, '/PETVET/public/images/products/') !== 0) {
            return true; // Not our file, consider it "deleted"
        }
        
        // Convert database path to filesystem path
        $fullPath = __DIR__ . '/..' . str_replace('/PETVET/', '/', $imagePath);
        
        // Delete if exists
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        
        return true; // Already doesn't exist
    }
    
    /**
     * Validate if a file is an image
     * 
     * @param array $file The $_FILES['fieldname'] array
     * @return array ['valid' => bool, 'message' => string]
     */
    public function validate($file) {
        if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return ['valid' => true, 'message' => 'No file to validate'];
        }
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['valid' => false, 'message' => 'Upload error occurred'];
        }
        
        if (!in_array($file['type'], $this->allowedTypes)) {
            return ['valid' => false, 'message' => 'Invalid file type'];
        }
        
        if ($file['size'] > $this->maxFileSize) {
            return ['valid' => false, 'message' => 'File too large (max 5MB)'];
        }
        
        return ['valid' => true, 'message' => 'Valid image file'];
    }
    
    /**
     * Get allowed file types
     */
    public function getAllowedTypes() {
        return $this->allowedTypes;
    }
    
    /**
     * Get max file size in bytes
     */
    public function getMaxFileSize() {
        return $this->maxFileSize;
    }
    
    /**
     * Get max file size in human-readable format
     */
    public function getMaxFileSizeFormatted() {
        return round($this->maxFileSize / 1048576, 2) . 'MB';
    }
}
?>
