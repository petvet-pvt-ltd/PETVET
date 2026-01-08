# Medical Reports Upload Feature - Implementation Guide

## Overview
This implementation adds file upload functionality (reports, documents, images) to the vet dashboard for:
- Medical Records
- Prescriptions
- Vaccinations

## Database Setup

### Step 1: Run the Migration
Execute the SQL migration to add the `reports` column to the database tables:

```sql
-- Location: database/migrations/add_reports_columns.sql
-- Run this in your phpMyAdmin or MySQL client

ALTER TABLE medical_records 
ADD COLUMN reports TEXT DEFAULT NULL COMMENT 'JSON array of file paths for reports/attachments';

ALTER TABLE prescriptions 
ADD COLUMN reports TEXT DEFAULT NULL COMMENT 'JSON array of file paths for reports/attachments';

ALTER TABLE vaccinations 
ADD COLUMN reports TEXT DEFAULT NULL COMMENT 'JSON array of file paths for reports/attachments';
```

### Step 2: Verify Database Changes
After running the migration, verify the columns were added:

```sql
DESCRIBE medical_records;
DESCRIBE prescriptions;
DESCRIBE vaccinations;
```

## File Structure

### New Files Created:
```
PETVET/
├── config/
│   └── MedicalFileUploader.php          # File upload handler class
├── database/
│   └── migrations/
│       └── add_reports_columns.sql      # Database migration
├── uploads/
│   └── medical-reports/                 # Upload directory (auto-created)
└── api/
    └── download-file.php                # Secure file download endpoint
```

### Modified Files:
```
views/vet/
├── medical-records.php                  # Added file upload input
├── prescriptions.php                    # Added file upload input
└── vaccinations.php                     # Added file upload input

public/js/vet/
├── medical-records.js                   # Updated to handle FormData
├── prescriptions.js                     # Updated to handle FormData
└── vaccinations.js                      # Updated to handle FormData

public/css/vet/
└── enhanced-vet.css                     # Added file upload styles

api/vet/
├── medical-records/add.php              # Updated to handle file uploads
├── prescriptions/add.php                # Updated to handle file uploads
└── vaccinations/add.php                 # Updated to handle file uploads
```

## Features Implemented

### 1. File Upload Functionality
- **Multiple file upload** support in all three forms
- **Accepted file types**: 
  - Images: JPG, PNG, GIF, WebP
  - Documents: PDF, DOC, DOCX, TXT
- **Maximum file size**: 10MB per file
- **Real-time file preview** showing selected files before upload

### 2. Security Features
- File type validation (MIME type checking)
- File size validation
- Unique filename generation to prevent conflicts
- Secure file storage in dedicated directory
- Authentication required for file access
- Directory traversal attack prevention

### 3. User Interface
- Clean file upload interface matching existing design
- File preview section showing selected files with sizes
- Visual icons for different file types (🖼️ for images, 📄 for documents)
- Clickable links in tables to view/download files
- Hover effects on file links

### 4. Database Storage
- Files stored as JSON array in `reports` column
- Example data structure:
```json
["uploads/medical-reports/report_123456.pdf", "uploads/medical-reports/xray_789012.jpg"]
```

## How to Use

### For Vets:

#### 1. Adding a Medical Record with Reports
1. Navigate to Medical Records page
2. Select an ongoing appointment
3. Fill in symptoms, diagnosis, and treatment
4. Click "Reports & Documents" file input
5. Select one or more files (images, PDFs, etc.)
6. Review the file preview
7. Click "💾 Save Record"

#### 2. Adding a Prescription with Documents
1. Navigate to Prescriptions page
2. Select an ongoing appointment
3. Fill in medication, dosage, and notes
4. Upload prescription images or pharmacy documents
5. Click "💊 Save Prescription"

#### 3. Adding a Vaccination with Certificates
1. Navigate to Vaccinations page
2. Select an ongoing appointment
3. Fill in vaccine name and next due date
4. Upload vaccination certificates or batch info
5. Click "💉 Save Vaccination"

### Viewing Uploaded Files
- Files appear in the "Reports" column of each table
- Click the icon (🖼️ or 📄) to view/download the file
- Images and PDFs open in new browser tab
- Other files trigger download

## File Upload Limits

- **Max file size**: 10MB per file
- **Max number of files**: Unlimited (browser dependent)
- **Allowed extensions**: .jpg, .jpeg, .png, .gif, .webp, .pdf, .doc, .docx, .txt

## Troubleshooting

### Files not uploading?
1. Check `uploads/medical-reports/` directory exists and is writable
2. Verify PHP upload settings in `php.ini`:
   ```ini
   upload_max_filesize = 10M
   post_max_size = 20M
   ```
3. Check browser console for JavaScript errors

### Database errors?
1. Ensure migration script was run successfully
2. Verify `reports` column exists in all three tables
3. Check column type is TEXT

### Files not displaying?
1. Check file paths in database are correct
2. Verify files exist in `uploads/medical-reports/` directory
3. Ensure user is authenticated

## Technical Details

### File Upload Process:
1. User selects files in form
2. JavaScript shows preview of selected files
3. On submit, FormData object created with all form fields + files
4. Files sent to API endpoint via POST
5. `MedicalFileUploader` class validates and processes files
6. Files moved to `uploads/medical-reports/` with unique names
7. File paths stored as JSON array in database
8. Success response returned to client

### File Naming Convention:
```
report_[uniqueid]_[timestamp].[extension]
Example: report_678abc123def_1736265432.pdf
```

### Security Measures:
- MIME type validation (not just extension)
- File size limits enforced
- Unique filenames prevent overwrites
- Authentication required for access
- Path sanitization prevents directory traversal
- Secure file storage outside web root (recommended further hardening)

## Future Enhancements (Optional)

1. **File deletion**: Add ability to remove individual files
2. **Thumbnail previews**: Show image thumbnails in tables
3. **File categories**: Tag files as "Lab Result", "X-Ray", etc.
4. **Cloud storage**: Integrate AWS S3 or similar for scalability
5. **Watermarking**: Add clinic watermark to uploaded images
6. **OCR**: Extract text from uploaded PDFs for searchability
7. **Compression**: Automatically compress large images
8. **Virus scanning**: Integrate antivirus checking
9. **Access logs**: Track who viewed which files

## Support

For issues or questions, refer to:
- Database migration: `database/migrations/add_reports_columns.sql`
- File handler class: `config/MedicalFileUploader.php`
- API endpoints: `api/vet/[module]/add.php`

---

**Implementation Date**: January 7, 2026
**Version**: 1.0
**Status**: Production Ready
