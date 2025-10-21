# Product Image Upload System

## Overview
The product management system now supports **file uploads** for product images. Images are stored on the web server, and only the file path is saved in the database.

## Architecture

### Storage Structure
```
public/
  images/
    products/
      product_1729532400_abc123.jpg
      product_1729532401_def456.png
      product_1729532402_ghi789.webp
```

### Database Storage
The `products` table stores the **relative path** in the `image_url` column:
```
/PETVET/public/images/products/product_1729532400_abc123.jpg
```

This allows:
- Easy file access via web server
- Portability (works on different domains)
- Backward compatibility with external URLs

## File Upload Flow

### 1. Add Product with Image

**Frontend Form:**
```html
<form id="addProductForm" enctype="multipart/form-data">
  <input type="text" name="name" required>
  <input type="number" name="price" required>
  <select name="category" required>
    <option value="food">Food</option>
    <option value="toys">Toys</option>
    <!-- etc -->
  </select>
  <input type="file" name="product_image" accept="image/*">
  <textarea name="description"></textarea>
  <input type="number" name="stock">
  <button type="submit">Add Product</button>
</form>
```

**JavaScript (AJAX):**
```javascript
const form = document.getElementById('addProductForm');
form.addEventListener('submit', async (e) => {
  e.preventDefault();
  
  const formData = new FormData(form);
  
  const response = await fetch('/PETVET/api/products/add.php', {
    method: 'POST',
    body: formData // Don't set Content-Type, browser handles it
  });
  
  const result = await response.json();
  console.log(result);
});
```

**Backend Processing (`api/products/add.php`):**
1. Validates user is `clinic_manager`
2. Checks if `$_FILES['product_image']` exists
3. Validates file type (JPG, PNG, GIF, WebP)
4. Validates file size (max 5MB)
5. Generates unique filename: `product_{timestamp}_{uniqid}.{ext}`
6. Moves file to `public/images/products/`
7. Saves path `/PETVET/public/images/products/{filename}` to database

### 2. Update Product with New Image

**Flow:**
1. Retrieves existing product from database
2. If new image uploaded:
   - Uploads new image
   - **Deletes old image** from server (if it's a local file)
   - Updates database with new path
3. If no new image, keeps existing image path

### 3. Delete Product

**Soft Delete (`api/products/delete.php`):**
- Sets `is_active = FALSE`
- **Keeps image file** (for potential restoration)

**Permanent Delete (`api/products/permanent-delete.php`):**
- Deletes product from database
- **Deletes image file** from server

## Image Validation

### Allowed File Types
- `image/jpeg` (JPG/JPEG)
- `image/png` (PNG)
- `image/gif` (GIF)
- `image/webp` (WebP)

### File Size Limit
- Maximum: **5MB**

### Security Features
- MIME type validation
- File extension validation
- `getimagesize()` verification (ensures it's a real image)
- Unique filenames prevent overwrites
- Files stored outside `/views/` and `/controllers/` (no execution risk)

## API Endpoints

### POST `/api/products/add.php`
**Accepts:**
- `multipart/form-data`
- Field: `product_image` (file upload)
- Field: `name` (text, required)
- Field: `price` (number, required)
- Field: `category` (text, required)
- Field: `description` (text)
- Field: `stock` (number)
- Field: `seller` (text)
- Field: `image_url` (text, fallback if no file)

**Response:**
```json
{
  "success": true,
  "message": "Product added successfully"
}
```

### POST `/api/products/update.php`
**Accepts:**
- `multipart/form-data`
- Field: `id` (number, required)
- Field: `product_image` (file upload, optional)
- All other product fields

**Behavior:**
- If new image uploaded: replaces old image
- If no new image: keeps existing image
- Deletes old local images automatically

**Response:**
```json
{
  "success": true,
  "message": "Product updated successfully"
}
```

### POST `/api/products/delete.php`
**Soft Delete** - Keeps image file

**Accepts:**
- Field: `id` (number, required)

**Response:**
```json
{
  "success": true,
  "message": "Product deleted successfully"
}
```

### POST `/api/products/permanent-delete.php`
**Hard Delete** - Removes image file

**Accepts:**
- Field: `id` (number, required)

**Response:**
```json
{
  "success": true,
  "message": "Product permanently deleted"
}
```

## ImageUploader Utility Class

**Location:** `config/ImageUploader.php`

**Usage:**
```php
require_once __DIR__ . '/../../config/ImageUploader.php';

$uploader = new ImageUploader();

// Upload image
$result = $uploader->upload($_FILES['product_image'], 'product_');
if ($result['success']) {
    $imagePath = $result['path']; // /PETVET/public/images/products/abc.jpg
    // Save to database
}

// Delete image
$uploader->delete('/PETVET/public/images/products/old_image.jpg');

// Validate before upload
$validation = $uploader->validate($_FILES['product_image']);
if ($validation['valid']) {
    // Proceed with upload
}
```

**Methods:**
- `upload($file, $prefix)` - Upload and return path
- `delete($imagePath)` - Delete file from server
- `validate($file)` - Check if file is valid
- `getAllowedTypes()` - Get allowed MIME types
- `getMaxFileSize()` - Get max size in bytes
- `getMaxFileSizeFormatted()` - Get max size as "5MB"

## Backward Compatibility

### External URLs Still Supported
If you want to use an external image URL instead of uploading:
```html
<input type="text" name="image_url" placeholder="Or paste image URL">
```

The system will:
1. Check if file uploaded → use uploaded file
2. If no file → use `image_url` field value
3. Store external URLs as-is in database

**Example:**
```
https://images.unsplash.com/photo-xyz.jpg
```

## Error Handling

### Common Errors

**"Invalid image type"**
- Solution: Use JPG, PNG, GIF, or WebP

**"Image size must be less than 5MB"**
- Solution: Compress image before upload

**"Failed to upload image"**
- Check directory permissions: `chmod 755 public/images/products/`
- Check PHP upload settings: `upload_max_filesize`, `post_max_size`

**"Unauthorized access"**
- Only `clinic_manager` role can upload images
- Ensure user is logged in with correct role

## PHP Configuration

### Required Settings (php.ini)
```ini
file_uploads = On
upload_max_filesize = 10M
post_max_size = 10M
max_file_uploads = 20
```

### Check Current Settings
```php
<?php
echo "Upload max filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "Post max size: " . ini_get('post_max_size') . "<br>";
echo "File uploads enabled: " . (ini_get('file_uploads') ? 'Yes' : 'No');
?>
```

## Directory Permissions

### Linux/Unix
```bash
chmod 755 public/images
chmod 755 public/images/products
```

### Windows
Ensure `IUSR` and `IIS_IUSRS` have write permissions on:
- `C:\xampp\htdocs\PETVET\public\images\products\`

## Testing

### Test Image Upload
1. Login as `clinic_manager`
2. Navigate to Shop Management
3. Click "Add Product"
4. Fill form and upload image (JPG/PNG under 5MB)
5. Submit form
6. Check:
   - Image appears in product card
   - File exists in `public/images/products/`
   - Database has correct path

### Test Image Update
1. Edit existing product
2. Upload new image
3. Verify:
   - Old image deleted from server
   - New image appears
   - Database updated

### Test Image Delete
1. Soft delete product → image remains
2. Permanent delete → image removed from server

## Security Considerations

### ✅ Implemented
- File type validation (MIME + extension)
- File size limits
- `getimagesize()` check (prevents fake images)
- Unique filenames (prevents overwrites)
- Role-based access control
- Files stored in public directory (no PHP execution)

### 🔒 Additional Recommendations
1. Add CSRF token to forms
2. Implement rate limiting on uploads
3. Scan uploaded files for malware (optional)
4. Use CDN for production (offload storage)

## Production Considerations

### CDN Integration (Optional)
For high-traffic sites, consider:
- Upload to AWS S3 / Cloudflare R2
- Store CDN URL in database
- Benefits: faster delivery, reduced server load

### Example with AWS S3:
```php
// config/ImageUploader.php - add S3 support
public function uploadToS3($file) {
    // Use AWS SDK
    $s3Client = new Aws\S3\S3Client([...]);
    $result = $s3Client->putObject([
        'Bucket' => 'petvet-products',
        'Key' => 'products/' . $filename,
        'SourceFile' => $file['tmp_name']
    ]);
    
    return $result['ObjectURL'];
}
```

## Summary

### Before (Old System)
- ❌ Only external URLs stored
- ❌ No file upload capability
- ❌ Reliant on external hosting

### After (New System)
- ✅ File uploads to server
- ✅ Automatic image management
- ✅ Old image cleanup on update/delete
- ✅ 5MB file size limit
- ✅ Type validation (JPG, PNG, GIF, WebP)
- ✅ Unique filename generation
- ✅ Backward compatible with URLs
- ✅ Reusable `ImageUploader` utility class

---

**Status:** ✅ **IMAGE UPLOAD SYSTEM COMPLETE**

**Files Modified:**
- `api/products/add.php` - Added file upload handling
- `api/products/update.php` - Added image replacement + old file deletion
- `api/products/permanent-delete.php` - Added image cleanup

**Files Created:**
- `config/ImageUploader.php` - Reusable image utility class
- `public/images/products/` - Image storage directory

**Next Steps:**
- Update frontend forms to include file input
- Add image preview before upload
- Test complete upload workflow
