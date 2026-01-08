# Medical Reports Upload - Installation Checklist

## ✅ Step-by-Step Installation

### 1. Database Migration (REQUIRED)
- [ ] Open phpMyAdmin
- [ ] Select `petvetDB` database
- [ ] Go to SQL tab
- [ ] Copy and paste contents from `database/RUN_THIS_MIGRATION.sql`
- [ ] Click "Go" to execute
- [ ] Verify success message appears

**Alternative using MySQL CLI:**
```bash
mysql -u root -p petvetDB < database/RUN_THIS_MIGRATION.sql
```

### 2. File System Setup (AUTO-CREATED)
- [ ] The `uploads/medical-reports/` directory will be created automatically on first upload
- [ ] If you want to create it manually: `mkdir uploads/medical-reports`
- [ ] Ensure directory has write permissions (755 or 777)

**On Windows (XAMPP):**
- Directory will be created automatically
- Default permissions should work

**On Linux:**
```bash
mkdir -p uploads/medical-reports
chmod 755 uploads/medical-reports
chown www-data:www-data uploads/medical-reports  # or apache:apache
```

### 3. PHP Configuration (CHECK)
- [ ] Verify PHP upload settings in `php.ini`:
  - `upload_max_filesize = 10M` (or higher)
  - `post_max_size = 20M` (should be 2x upload_max_filesize)
  - `file_uploads = On`
  - `max_file_uploads = 20`

**Location of php.ini:**
- XAMPP Windows: `C:\xampp\php\php.ini`
- Linux: `/etc/php/[version]/apache2/php.ini`

**After editing php.ini:**
- Restart Apache server

### 4. Testing
- [ ] Log in as a vet
- [ ] Go to Medical Records page
- [ ] Click on an ongoing appointment
- [ ] Try uploading a test image or PDF
- [ ] Verify file appears in table with icon
- [ ] Click icon to view/download file

## 📋 Quick Verification

Run this checklist after installation:

1. **Database Check:**
   ```sql
   DESCRIBE medical_records;
   -- Should show 'reports' column (TEXT, NULL, NULL)
   ```

2. **File Upload Check:**
   - Navigate to: `Medical Records` page
   - Look for "Reports & Documents" file input
   - Should accept multiple files

3. **Directory Check:**
   - After first upload, verify: `uploads/medical-reports/` exists
   - Check uploaded files are present

4. **Display Check:**
   - Uploaded records should show icons in "Reports" column
   - Click icon should open file in new tab

## 🔧 Troubleshooting

### Problem: Database error when submitting form
**Solution:** 
- Run the migration SQL again
- Check if `reports` column exists: `SHOW COLUMNS FROM medical_records LIKE 'reports';`

### Problem: "Failed to move file" error
**Solution:**
- Check directory permissions
- On Windows: Right-click `uploads` folder → Properties → Security → Give "Users" write permission
- On Linux: `chmod 755 uploads/medical-reports`

### Problem: "File type not allowed"
**Solution:**
- Only these types are allowed: JPG, PNG, GIF, WebP, PDF, DOC, DOCX, TXT
- Check actual MIME type, not just extension

### Problem: Files not displaying in table
**Solution:**
- Open browser console (F12) and check for JavaScript errors
- Verify file paths in database are correct
- Check files exist in `uploads/medical-reports/`

### Problem: Large files failing to upload
**Solution:**
- Increase `upload_max_filesize` and `post_max_size` in php.ini
- Restart Apache after changes
- Check nginx configuration if using nginx

## 📁 What Was Modified

### New Files:
1. `config/MedicalFileUploader.php` - File upload handler
2. `database/migrations/add_reports_columns.sql` - Migration script
3. `database/RUN_THIS_MIGRATION.sql` - Quick migration runner
4. `api/download-file.php` - Secure file download endpoint
5. `docs/MEDICAL_REPORTS_UPLOAD_GUIDE.md` - Full documentation

### Modified Files:
1. `views/vet/medical-records.php` - Added file input
2. `views/vet/prescriptions.php` - Added file input
3. `views/vet/vaccinations.php` - Added file input
4. `public/js/vet/medical-records.js` - FormData handling
5. `public/js/vet/prescriptions.js` - FormData handling
6. `public/js/vet/vaccinations.js` - FormData handling
7. `public/css/vet/enhanced-vet.css` - File upload styles
8. `api/vet/medical-records/add.php` - File upload logic
9. `api/vet/prescriptions/add.php` - File upload logic
10. `api/vet/vaccinations/add.php` - File upload logic

## ✅ Installation Complete!

Once you've completed all steps above, the feature is ready to use!

**Need Help?**
- Check: `docs/MEDICAL_REPORTS_UPLOAD_GUIDE.md` for detailed documentation
- Review: `config/MedicalFileUploader.php` for file handling logic
- Inspect: Browser console (F12) for JavaScript errors

---
**Installation Date:** _____________
**Installed By:** _____________
**Status:** [ ] Pending [ ] In Progress [ ] Complete [ ] Verified
