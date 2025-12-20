# Lost & Found Pet Reporting - Database Integration

## Summary

Successfully implemented database integration for the Lost & Found pet reporting system. Reports are now stored in the `LostFoundReport` table on TiDB Cloud and displayed in real-time across all user types.

## Changes Made

### 1. API Endpoints Created

#### `/api/pet-owner/submit-report.php`

- Handles POST requests from the Report Pet form
- Validates required fields (type, species, location, date)
- Handles multiple photo uploads (up to 3 images, 5MB each)
- Stores photos in `/uploads/lost-found/` directory
- Saves additional data as JSON in the `description` field
- Returns success response with report ID

**Request Format:**

- Method: POST (FormData)
- Fields: type, species, name, color, location, date, notes, phone, phone2, email, photos[]
- Response: JSON with success status and report_id

#### `/api/pet-owner/get-reports.php`

- Fetches reports from database with optional filtering
- GET parameter: `type` (optional: 'lost', 'found', or null for all)
- Parses JSON description field for display
- Returns formatted array of reports

**Response Format:**

```json
{
  "success": true,
  "count": 5,
  "reports": [
    {
      "id": 1,
      "type": "lost",
      "location": "Central Park",
      "date": "2025-01-15",
      "species": "Dog",
      "name": "Max",
      "color": "Golden",
      "notes": "Friendly dog...",
      "photos": ["/PETVET/uploads/lost-found/pet_lost_123.jpg"],
      "contact": {
        "phone": "+94 77 123 4567",
        "phone2": "",
        "email": "owner@example.com"
      }
    }
  ]
}
```

### 2. Model Updates

#### `models/PetOwner/LostFoundModel.php`

**Before:** Returned hardcoded mock data array
**After:** Queries `LostFoundReport` table from TiDB database

**Methods Updated:**

- `getAllReports()` - Fetches all reports ordered by date
- `getLostReports()` - Filters by type='lost'
- `getFoundReports()` - Filters by type='found'
- `searchReports()` - Searches with filters (query, species, type)
- `formatReports()` - New helper method to parse JSON description

#### `models/Guest/GuestLostFoundModel.php`

**Before:** Contained duplicate mock data
**After:** Extends `LostFoundModel` to inherit all database methods

Guest users now see real-time data from the database (read-only access).

### 3. JavaScript Updates

#### `public/js/pet-owner/lost-found.js`

**Form Submission Handler Updated:**

- Changed from localStorage to API POST request
- Creates FormData with all form fields
- Handles multiple photo uploads
- Shows loading state during submission
- Displays success/error messages
- Reloads page to show new report

**Before:**

```javascript
// Stored to localStorage
myListings.push(newListing);
saveMyListings();
alert("Report submitted successfully (demo mode)");
```

**After:**

```javascript
// Posts to API
const response = await fetch("/PETVET/api/pet-owner/submit-report.php", {
  method: "POST",
  body: formData,
});
const result = await response.json();
if (result.success) {
  alert("Report submitted successfully!");
  window.location.reload();
}
```

## Database Schema

**Table:** `LostFoundReport`

```sql
CREATE TABLE LostFoundReport (
    report_id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(50) NOT NULL,           -- 'lost' or 'found'
    location VARCHAR(255) NOT NULL,      -- Where pet was lost/found
    date_reported DATE NOT NULL,         -- When it happened
    description TEXT                      -- JSON with additional data
);
```

**Description Field JSON Structure:**

```json
{
  "species": "Dog",
  "name": "Max",
  "color": "Golden",
  "notes": "Friendly dog wearing red collar",
  "contact": {
    "phone": "+94 77 123 4567",
    "phone2": "+94 77 123 4568",
    "email": "owner@example.com"
  },
  "photos": [
    "/PETVET/uploads/lost-found/pet_lost_abc123.jpg",
    "/PETVET/uploads/lost-found/pet_lost_def456.jpg"
  ],
  "user_id": 1,
  "submitted_at": "2025-01-15 14:30:00"
}
```

## File Upload Configuration

**Upload Directory:** `c:\xampp\htdocs\PETVET\uploads\lost-found\`
**Allowed Types:** JPEG, JPG, PNG, GIF
**Max File Size:** 5MB per image
**Max Images:** 3 per report (handled in form)
**Filename Format:** `pet_{type}_{unique_id}.{ext}`

## Security Features

1. **Session Authentication:** Checks `$_SESSION['user_id']` before accepting submissions
2. **Type Validation:** Only accepts 'lost' or 'found'
3. **File Type Validation:** Checks MIME types against allowed list
4. **File Size Limits:** 5MB per image
5. **SQL Injection Prevention:** Uses PDO prepared statements
6. **XSS Prevention:** JSON encoding escapes special characters

## Testing Checklist

### Pet Owner Testing

- [ ] Navigate to Lost & Found page
- [ ] Click "Report Pet" button
- [ ] Fill form with all fields
- [ ] Upload 1-3 photos
- [ ] Submit form - should save to database
- [ ] Verify report appears in correct section (Lost/Found)
- [ ] Check photos display correctly
- [ ] Test contact modal shows all info
- [ ] Test search and filters work

### Guest Testing

- [ ] Navigate to Lost & Found as guest
- [ ] Verify same reports display as pet owner
- [ ] Confirm "Report Pet" button works (if enabled for guests)
- [ ] Test contact functionality
- [ ] Verify search and filters work

### Admin Testing

- [ ] Check database table has new entries
- [ ] Verify JSON description field is valid
- [ ] Check uploaded photos exist in `/uploads/lost-found/`
- [ ] Test with missing optional fields
- [ ] Test with maximum photos (3)

## Error Handling

### API Error Responses

- **401 Unauthorized:** User not logged in
- **405 Method Not Allowed:** Non-POST request
- **400 Bad Request:** Missing required fields or invalid type
- **500 Internal Server Error:** Database or server issues

### JavaScript Error Handling

- Displays user-friendly error messages
- Re-enables submit button on error
- Console logs technical errors for debugging
- Falls back gracefully on network failures

## Future Enhancements

1. **Image Optimization:** Resize/compress uploaded photos
2. **Email Notifications:** Notify users when matching pet is reported
3. **Geolocation:** Add map integration for location
4. **Status Updates:** Add "resolved" status when pet is found
5. **My Listings:** Update to fetch user's own reports from database
6. **Edit/Delete:** Add functionality to modify submitted reports
7. **Advanced Search:** Full-text search in description JSON
8. **Pagination:** For large numbers of reports

## Database Connection

- **Host:** gateway01.ap-southeast-1.prod.aws.tidbcloud.com:4000
- **Database:** petvetDB
- **Connection:** PDO with SSL (uses `/database/CA/isrgrootx1.pem`)
- **Error Logging:** PHP error_log for debugging

## Notes

- Reports are now persistent across sessions
- All mock data has been replaced with database queries
- Guest users can view all reports (read-only)
- Photo uploads create files in the uploads directory
- Page reload is required to see new reports (can be optimized with AJAX)
