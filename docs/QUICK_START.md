# 🚀 Quick Start Guide - Medical Reports Upload

## ⚡ 30-Second Setup

### 1️⃣ Run Database Migration (REQUIRED)
Open phpMyAdmin → Select `petvetDB` → SQL tab → Paste this:

```sql
USE petvetDB;

ALTER TABLE medical_records ADD COLUMN reports TEXT DEFAULT NULL;
ALTER TABLE prescriptions ADD COLUMN reports TEXT DEFAULT NULL;
ALTER TABLE vaccinations ADD COLUMN reports TEXT DEFAULT NULL;
```

Click **Go**. Done! ✅

### 2️⃣ Test It (Optional but Recommended)
Visit: `http://localhost/PETVET/DevTools/test-file-upload.html`

Click all 4 test buttons. All should be ✅ green.

### 3️⃣ Use It!
1. Log in as vet
2. Go to **Medical Records** (or Prescriptions or Vaccinations)
3. Click on any **ongoing appointment**
4. Fill the form
5. Click **"Reports & Documents"** → Select files
6. Click **Save**
7. See files appear in table with icons 🖼️ 📄

---

## 📌 Quick Reference

### Accepted File Types
✅ Images: JPG, PNG, GIF, WebP
✅ Documents: PDF, DOC, DOCX, TXT
❌ Videos, executables, zip files

### File Size Limits
- Max per file: **10MB**
- Max total: **200MB** (20 files × 10MB)

### Where Files Are Stored
`uploads/medical-reports/report_[uniqueid]_[timestamp].[ext]`

Example: `uploads/medical-reports/report_678abc_1736265432.pdf`

### Database Storage
JSON array in `reports` column:
```json
["uploads/medical-reports/file1.pdf", "uploads/medical-reports/file2.jpg"]
```

---

## 🔧 Troubleshooting

| Problem | Quick Fix |
|---------|-----------|
| **Files won't upload** | Check `uploads/medical-reports/` exists and is writable (755 permissions) |
| **"Missing reports column" error** | Run the migration SQL again |
| **Files too large** | Edit `php.ini`: `upload_max_filesize = 10M`, `post_max_size = 20M`, then restart Apache |
| **Wrong file type error** | Only images and documents allowed. Check file extension. |
| **Icons not showing** | Press Ctrl+F5 to hard refresh. Check browser console (F12) for errors. |

---

## 📖 Where to Find Help

| Need | Document |
|------|----------|
| **Quick setup** | `INSTALLATION_CHECKLIST.md` |
| **Full details** | `docs/MEDICAL_REPORTS_UPLOAD_GUIDE.md` |
| **What changed** | `IMPLEMENTATION_SUMMARY.md` |
| **Visual examples** | `VISUAL_GUIDE.md` |
| **Testing** | `DevTools/test-file-upload.html` |

---

## ✅ Checklist

- [ ] Ran database migration
- [ ] Ran test page (all green)
- [ ] Logged in as vet
- [ ] Uploaded test file
- [ ] File appears in table
- [ ] Clicked icon to view file
- [ ] Tested on all 3 pages (Medical Records, Prescriptions, Vaccinations)

---

## 🎯 Common Questions

**Q: Can I upload multiple files at once?**
A: Yes! Select multiple files in the file picker.

**Q: What happens to old records without files?**
A: They just show "-" in the Reports column. No impact.

**Q: Can I delete uploaded files?**
A: Not from the UI yet. Can manually delete from `uploads/medical-reports/` folder.

**Q: Are files backed up?**
A: Include `uploads/medical-reports/` in your backup routine.

**Q: Can pet owners see these files?**
A: Not currently. Only vets can upload and view.

**Q: What if I upload the wrong file?**
A: Currently no delete feature. Add a new record or manually remove from database.

---

## 🔐 Security Notes

✅ Files are validated (type & size)
✅ Unique filenames prevent conflicts
✅ Authentication required
✅ Path sanitization enabled
✅ MIME type checking (not just extension)

---

## 📞 Emergency Rollback

If something goes wrong and you need to undo:

```sql
-- Remove reports columns
ALTER TABLE medical_records DROP COLUMN reports;
ALTER TABLE prescriptions DROP COLUMN reports;
ALTER TABLE vaccinations DROP COLUMN reports;

-- Delete uploaded files
-- Manually delete: uploads/medical-reports/
```

Then restore old API files from backup.

---

**Quick Start Guide v1.0** | January 7, 2026
