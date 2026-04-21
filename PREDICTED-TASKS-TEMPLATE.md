# Predicted Tasks for Lost & Found Section - Template Solutions

Based on analysis of 20+ completed tasks, here are the likely upcoming tasks with implementation guides.

---

## **PATTERN 1: Add Input Field → Save to DB → Display in Frontend**

### Common Upcoming Tasks:
- Add estimated value field for found items
- Add location/coordinates field for item discovery
- Add color/description field for identifying items
- Add category field for item type

### Quick Implementation Guide

**Files to Modify:**

1. **HTML Form** - `api/guest/lost-found.php`
   ```php
   // In Report Modal
   <input type="text" id="itemColor" placeholder="Item Color" required>
   ```

2. **Frontend JS** - `scripts/lost-found.js`
   ```js
   formData.append('itemColor', qs('#itemColor').value);
   ```

3. **Backend API** - `api/guest/submit-report.php`
   ```php
   $color = $_POST['itemColor'] ?? null;
   ```

4. **Model** - `models/LostFoundModel.php`
   ```php
   public function addReport($reportData) {
       $reportData['item_color'] = $reportData['item_color'] ?? null;
   }
   ```

5. **Database** - Run migration to add column:
   ```sql
   ALTER TABLE LostFoundReport ADD COLUMN item_color VARCHAR(100);
   ```

6. **Display** - Show in table/cards in frontend

---

## **PATTERN 2: Form Field Validation (Frontend + Backend)**

### Common Upcoming Tasks:
- Validate phone number (10 digits, starts with 07)
- Validate NIC format
- Validate date not in future
- Validate email format
- Validate text length limits

### Quick Implementation Guide

**Frontend Validation** - `scripts/lost-found.js`
```js
function validatePhoneNumber(phone) {
    const regex = /^07\d{8}$/; // Starts with 07, total 10 digits
    return regex.test(phone);
}

// On form submission
if (!validatePhoneNumber(phone)) {
    showError('Phone must be 10 digits starting with 07');
    return false;
}
```

**Backend Validation** - `models/LostFoundModel.php`
```php
public function validatePhoneNumber($phone) {
    if (!preg_match('/^07\d{8}$/', $phone)) {
        return ['valid' => false, 'error' => 'Invalid phone format'];
    }
    return ['valid' => true];
}
```

---

## **PATTERN 3: Date/Time Field with Constraints**

### Common Upcoming Tasks:
- Registration date cannot be future date
- Report date must be within last 30 days
- Follow-up date must be after report date

### Quick Implementation Guide

**Frontend** - `lost-found.php`
```html
<input type="date" id="reportDate" max="" required>
```
```js
// Set max to today
document.getElementById('reportDate').max = new Date().toISOString().split('T')[0];
```

**Backend** - `models/LostFoundModel.php`
```php
public function validateDateNotFuture($date) {
    $inputDate = new DateTime($date);
    $today = new DateTime('today');
    
    if ($inputDate > $today) {
        return ['valid' => false, 'error' => 'Date cannot be in future'];
    }
    return ['valid' => true];
}
```

---

## **PATTERN 4: Dropdown Field (Static or Dynamic)**

### Common Upcoming Tasks:
- Add item category dropdown (Jewelry, Electronics, Documents, etc.)
- Add priority level dropdown
- Add status dropdown (Lost, Found, Recovered)

### Quick Implementation Guide

**Frontend** - `lost-found.php`
```html
<select id="itemCategory" required>
    <option value="">Select Category</option>
    <option value="jewelry">Jewelry</option>
    <option value="electronics">Electronics</option>
    <option value="documents">Documents</option>
</select>
```

**Backend** - `api/guest/submit-report.php`
```php
$category = $_POST['itemCategory'];
if (!in_array($category, ['jewelry', 'electronics', 'documents'])) {
    return json_encode(['status' => 'error', 'message' => 'Invalid category']);
}
```

---

## **PATTERN 5: Conditional Dropdown (Show Options Based on Selection)**

### Common Upcoming Tasks:
- If category is "Electronics", show subcategories (Phone, Laptop, etc.)
- If type is "Lost", show urgency level options

### Quick Implementation Guide

**Frontend JS** - `scripts/lost-found.js`
```js
qs('#itemCategory').addEventListener('change', function() {
    const category = this.value;
    const subcategoryDropdown = qs('#itemSubcategory');
    
    if (category === 'electronics') {
        subcategoryDropdown.innerHTML = `
            <option value="phone">Phone</option>
            <option value="laptop">Laptop</option>
        `;
        subcategoryDropdown.style.display = 'block';
    } else {
        subcategoryDropdown.style.display = 'none';
    }
});
```

---

## **PATTERN 6: File Upload with Type Validation**

### Common Upcoming Tasks:
- Upload photo proof of item
- Validate file type (only JPG, PNG, PDF)
- Set file size limit (max 5MB)

### Quick Implementation Guide

**Frontend** - `lost-found.php`
```html
<input type="file" id="itemPhoto" accept=".jpg,.jpeg,.png" required>
```

**Backend** - `api/guest/submit-report.php`
```php
$allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
$fileType = $_FILES['itemPhoto']['type'];

if (!in_array($fileType, $allowedTypes)) {
    return json_encode(['status' => 'error', 'message' => 'Only JPG, PNG, PDF allowed']);
}

if ($_FILES['itemPhoto']['size'] > 5 * 1024 * 1024) { // 5MB
    return json_encode(['status' => 'error', 'message' => 'File too large']);
}
```

---

## **PATTERN 7: Display Calculated Value (with Threshold Highlight)**

### Common Upcoming Tasks:
- Show "High Value" tag if reward > 100,000
- Show "Urgent" tag if report age > 7 days
- Show status badge based on conditions

### Quick Implementation Guide

**Frontend Display** - `lost-found.php`
```html
<?php
    $reward = $listing['reward'];
    if ($reward > 100000) {
        echo '<span class="badge badge-danger">💎 High Value</span>';
    }
?>
```

---

## **PATTERN 8: Search/Filter Feature**

### Common Upcoming Tasks:
- Search lost items by category
- Filter items by date range
- Search by location/district

### Quick Implementation Guide

**Backend API** - Create `api/guest/search-reports.php`
```php
$category = $_GET['category'] ?? null;
$searchQuery = "SELECT * FROM LostFoundReport WHERE 1=1";

if ($category) {
    $searchQuery .= " AND category = '" . mysqli_real_escape_string($conn, $category) . "'";
}

$result = mysqli_query($conn, $searchQuery);
echo json_encode(['data' => mysqli_fetch_all($result, MYSQLI_ASSOC)]);
```

**Frontend JS** - `scripts/lost-found.js`
```js
qs('#filterCategory').addEventListener('change', function() {
    const category = this.value;
    fetch(`api/guest/search-reports.php?category=${category}`)
        .then(r => r.json())
        .then(data => displayReports(data.data));
});
```

---

## **PATTERN 9: Update CRUD for Specific Fields**

### Common Upcoming Tasks:
- Allow edit of status/urgency field
- Update reward amount
- Modify description/notes

### Implementation Files:
1. **Edit Form** - `api/guest/lost-found.php` (add field in edit modal)
2. **Update API** - `api/guest/update-report.php` (handle update)
3. **Backend Logic** - `models/LostFoundModel.php` (validation)
4. **Frontend JS** - `scripts/lost-found.js` (form population)

---

## **PATTERN 10: Multi-Field Input (e.g., Phone with Country Code)**

### Common Upcoming Tasks:
- Phone field split into country code + number
- Date range with start and end dates
- Address split into multiple parts

### Quick Implementation Guide

**Frontend** - `lost-found.php`
```html
<select id="countryCode" required>
    <option value="+94">Sri Lanka (+94)</option>
    <option value="+1">USA (+1)</option>
</select>
<input type="text" id="phoneNumber" placeholder="9 digits" pattern="\d{9}" required>
```

**Backend** - `api/guest/submit-report.php`
```php
$fullPhone = $_POST['countryCode'] . $_POST['phoneNumber'];
// Validate both parts separately
```

---

## **File Structure Reference**

### Files You'll Modify 90% of the Time:

1. **`api/guest/lost-found.php`** - HTML form fields
2. **`api/guest/submit-report.php`** - Receive & process POST data
3. **`api/guest/update-report.php`** - Handle updates
4. **`models/LostFoundModel.php`** - Validation & DB logic
5. **`scripts/lost-found.js`** - Frontend form handling & display
6. **`database/migrations/`** - Add new DB columns

### Typical File Change Checklist:
- [ ] Add input field to form (HTML)
- [ ] Add append to FormData (JS)
- [ ] Add extraction from POST (API)
- [ ] Add validation method (Model)
- [ ] Add column to DB (Migration)
- [ ] Add display logic to frontend (PHP/JS)

---

## **Most Likely Next Tasks (Ranked by Probability)**

1. **Estimated Value Field** - Add numeric field, validate > 0, display with "High Value" badge if > 100,000
2. **Item Category Dropdown** - Add dropdown, validate selection, filter by category
3. **Phone Number Split** - Country code dropdown + 9-digit number field
4. **Search/Filter Feature** - Filter reports by category/date/location
5. **Status Update CRUD** - Allow owner to update lost item status to "Found/Recovered"
6. **Photo Upload** - Add photo proof field with JPG/PNG validation (5MB limit)
7. **Date Range Filter** - Show reports within last 30 days by default
8. **Priority/Urgency Field** - Add dropdown (Low/Medium/High) with color coding
9. **Bulk Update** - Checkbox selection for marking multiple items as recovered
10. **Report Notes** - Text area for additional description with 500 char limit

---

## **Quick Copy-Paste Template**

**For ANY "Add Field" Task:**

1. Form HTML → `<input type="text" id="fieldName">`
2. Frontend JS → `formData.append('fieldName', qs('#fieldName').value)`
3. Backend API → `$fieldName = $_POST['fieldName'] ?? null`
4. Model Method → Add validation function
5. Database → `ALTER TABLE LostFoundReport ADD COLUMN field_name VARCHAR(255)`
6. Display → Echo `$report['field_name']` in PHP or JS

---
