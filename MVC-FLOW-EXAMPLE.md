# MVC Flow: Reporting a Lost or Found Pet (PETVET)

## Overview
This document shows the complete flow from **VIEW → CONTROLLER → MODEL → DATABASE** for the "Report Lost/Found Pet" feature.

---

## 1️⃣ VIEW LAYER (Frontend - What User Sees)

### File: `/views/pet-owner/lost-found.php` or `/views/guest/lost-found.php`

**What happens here:**
- User sees the Lost & Found page with a list of reports
- User clicks "+ Report Pet" button
- A modal form pops up with these fields:
  - Type (Lost/Found)
  - Species (Dog/Cat/Bird)
  - Name (optional)
  - Color
  - Location (map picker)
  - Date & Time last seen
  - Contact info (phone, email)
  - Photos (up to 3)
  - Notes/Reward amount

**HTML Form Structure:**
```html
<form id="reportForm" autocomplete="off">
  <input type="hidden" id="rLatitude">
  <input type="hidden" id="rLongitude">
  <select id="rType" required> <!-- Lost/Found -->
  <select id="rSpecies" required>
  <input type="text" id="rName">
  <input type="text" id="rColor" required>
  <input type="text" id="rLocation" readonly>
  <input type="date" id="rDate" required>
  <input type="time" id="rTime">
  <input type="tel" id="rPhone" required>
  <input type="tel" id="rPhone2">
  <input type="email" id="rEmail">
  <input type="file" id="rPhoto" multiple>
  <textarea id="rNotes"></textarea>
  <input type="number" id="rReward">
  <button type="submit">Submit</button>
</form>
```

---

## 2️⃣ CLIENT-SIDE JAVASCRIPT (Frontend Logic)

### File: `/public/js/pet-owner/lost-found.js`

**What happens here:**
- JavaScript listens for form submission (event listener on `#reportForm`)
- When user clicks "Submit", JavaScript:
  1. **Validates form data** - checks if all required fields are filled
  2. **Gets map coordinates** - retrieves latitude/longitude from hidden inputs
  3. **Prepares FormData object** with:
     - Type, species, name, color, location
     - Date, time, coordinates
     - Contact info (phone, email)
     - Multiple photos as files
     - Notes and reward amount

**Code Example:**
```javascript
reportForm.addEventListener("submit", async (e) => {
  e.preventDefault();
  
  // Validation
  if (!locationEl.value || !dateEl.value) {
    alert("Please fill required fields");
    return;
  }
  
  // Prepare data
  const formData = new FormData();
  formData.append("type", qs('#rType').value);
  formData.append("species", qs('#rSpecies').value);
  formData.append("name", qs('#rName').value);
  formData.append("color", qs('#rColor').value);
  formData.append("location", qs('#rLocation').value);
  formData.append("latitude", missingLatInput.value);
  formData.append("longitude", missingLngInput.value);
  formData.append("date", qs('#rDate').value);
  formData.append("time", qs('#rTime').value);
  formData.append("phone", qs('#rPhone').value);
  formData.append("phone2", qs('#rPhone2').value);
  formData.append("email", qs('#rEmail').value);
  formData.append("notes", qs('#rNotes').value);
  formData.append("reward", qs('#rReward').value);
  
  // Add multiple photos
  Array.from(photoInput.files).forEach((file) => {
    formData.append('photos[]', file);
  });
  
  // SEND TO API/CONTROLLER
  const response = await fetch('/PETVET/api/pet-owner/submit-report.php', {
    method: 'POST',
    body: formData
  });
  
  const result = await response.json();
  if (result.success) {
    alert('Report submitted successfully!');
    window.location.reload();
  }
});
```

---

## 3️⃣ CONTROLLER / API LAYER (Server Entry Point)

### File: `/api/pet-owner/submit-report.php`

**What happens here:**
- Acts as the **API endpoint** that receives the form data from JavaScript
- **Checks authentication** - verifies user is logged in
- **Validates input** - calls model's validation method
- **Handles file uploads** - processes multiple photos
- **Prepares data** - organizes all data into array format
- **Calls MODEL** to insert into database
- **Returns JSON response** to frontend

**Flow:**
```
POST /api/pet-owner/submit-report.php
  ↓
Session check → User logged in?
  ↓
Method check → POST request?
  ↓
Extract POST data:
  - type, species, name, color, location, date, time
  - phone, phone2, email, notes, reward, latitude, longitude
  ↓
VALIDATION (calls model):
  $lostFoundModel->validateReportFields(...)
  ↓
FILE UPLOAD HANDLING:
  - Check each photo for type/size
  - Generate unique filenames
  - Save to /uploads/lost-found/
  - Collect paths into array
  ↓
PREPARE DATA ARRAY:
  $reportData = [
    'species' => $species,
    'name' => $name,
    'color' => $color,
    'notes' => $notes,
    'contact' => ['phone' => $phone, 'email' => $email],
    'photos' => $photoPaths,
    'latitude' => $latitude,
    'longitude' => $longitude,
    'user_id' => $_SESSION['user_id'],
    'submitted_at' => date('Y-m-d H:i:s')
  ];
  ↓
CALL MODEL:
  $reportId = $lostFoundModel->insertReport($type, $location, $date, $reportData);
  ↓
RETURN JSON RESPONSE:
  {
    "success": true,
    "message": "Report submitted successfully",
    "report_id": 123
  }
```

---

## 4️⃣ MODEL LAYER (Business Logic & Database)

### File: `/models/PetOwner/LostFoundModel.php`

### A) VALIDATION METHOD
```php
public function validateReportFields($type, $species, $name, $color, ...) {
  $errors = [];
  
  // Validate type
  if (!in_array($type, ['lost', 'found'])) {
    $errors[] = 'Invalid type';
  }
  
  // Validate species (only letters)
  if (!preg_match('/^[a-zA-Z\s]+$/', $species)) {
    $errors[] = 'Species must contain only letters';
  }
  
  // Validate phone format
  if (!$this->isValidPhoneNumber($phone)) {
    $errors[] = 'Invalid phone format';
  }
  
  // Validate date (not in future)
  $dateObj = DateTime::createFromFormat('Y-m-d', $date);
  if ($dateObj > new DateTime()) {
    $errors[] = 'Date cannot be in future';
  }
  
  return [
    'valid' => empty($errors),
    'errors' => $errors
  ];
}
```

### B) INSERT REPORT METHOD (Saves to Database)
```php
public function insertReport($type, $location, $date, $data) {
  // Extract individual fields from array
  $species = $data['species'];
  $name = $data['name'];
  $color = $data['color'];
  $notes = $data['notes'];
  $reward = $data['reward'];
  $photos = json_encode($data['photos']); // Convert array to JSON
  $latitude = $data['latitude'];
  $longitude = $data['longitude'];
  $user_id = $data['user_id'];
  $submitted_at = $data['submitted_at'];
  
  // Prepare SQL INSERT statement
  $stmt = $this->db->prepare("
    INSERT INTO LostFoundReport (
      type, location, date_reported, species, name, color, 
      breed, age, notes, time, reward, price, risk, 
      phone, phone2, email, photos, latitude, longitude, 
      user_id, submitted_at, description
    ) VALUES (
      :type, :location, :date_reported, :species, :name, :color, 
      :breed, :age, :notes, :time, :reward, :price, :risk, 
      :phone, :phone2, :email, :photos, :latitude, :longitude, 
      :user_id, :submitted_at, :description
    )
  ");
  
  // Execute with bound parameters (prevents SQL injection)
  $stmt->execute([
    ':type' => $type,
    ':location' => $location,
    ':date_reported' => $date,
    ':species' => $species,
    ':name' => $name,
    ':color' => $color,
    ':notes' => $notes,
    ':reward' => $reward,
    ':photos' => $photos,
    ':latitude' => $latitude,
    ':longitude' => $longitude,
    ':user_id' => $user_id,
    ':submitted_at' => $submitted_at,
    // ... other fields
  ]);
  
  // Return the new record's ID
  return $this->db->lastInsertId();
}
```

---

## 5️⃣ DATABASE LAYER (Data Storage)

### Table: `LostFoundReport`

```sql
CREATE TABLE LostFoundReport (
  id INT PRIMARY KEY AUTO_INCREMENT,
  type ENUM('lost', 'found'),
  location VARCHAR(255),
  date_reported DATE,
  species VARCHAR(50),
  name VARCHAR(50),
  color VARCHAR(100),
  breed VARCHAR(50),
  age VARCHAR(50),
  notes TEXT,
  time TIME,
  reward DECIMAL(10, 2),
  price DECIMAL(10, 2),
  risk ENUM('Low', 'Medium', 'High'),
  phone VARCHAR(20),
  phone2 VARCHAR(20),
  email VARCHAR(100),
  photos JSON,           -- Stores array of photo paths
  latitude DECIMAL(10, 8),
  longitude DECIMAL(11, 8),
  user_id INT,
  submitted_at TIMESTAMP,
  description JSON,     -- Stores full data as backup
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);
```

**What gets stored:**
- All pet details (species, name, color, age)
- Report location and map coordinates
- Contact information
- Photo file paths (as JSON array)
- User who submitted the report
- Date/time submitted

---

## 📊 COMPLETE FLOW DIAGRAM

```
┌─────────────────────────────────────────────────────────────────┐
│  1. USER INTERACTION (VIEW LAYER)                               │
│     - Fills out form in modal                                   │
│     - Clicks submit button                                      │
└──────────────────┬──────────────────────────────────────────────┘
                   ↓
┌─────────────────────────────────────────────────────────────────┐
│  2. CLIENT JAVASCRIPT (Frontend Logic)                          │
│     - Validates form fields                                     │
│     - Collects coordinates from map                             │
│     - Creates FormData with all inputs + files                  │
│     - Sends POST request to API                                 │
└──────────────────┬──────────────────────────────────────────────┘
                   ↓
         POST /api/pet-owner/submit-report.php
                   ↓
┌─────────────────────────────────────────────────────────────────┐
│  3. CONTROLLER / API (Entry Point)                              │
│     /api/pet-owner/submit-report.php                            │
│                                                                 │
│     1. Check: User logged in? ✓                                │
│     2. Check: Is POST request? ✓                               │
│     3. Extract form data from $_POST                            │
│     4. Call Model: validateReportFields()                       │
│        - If validation fails → Return error JSON               │
│     5. Handle file uploads (photos)                             │
│        - Move files to /uploads/lost-found/                     │
│        - Generate unique filenames                              │
│     6. Prepare data array                                       │
│     7. Call Model: insertReport()                               │
│     8. Return JSON response                                     │
└──────────────────┬──────────────────────────────────────────────┘
                   ↓
┌─────────────────────────────────────────────────────────────────┐
│  4. MODEL LAYER (Business Logic)                                │
│     LostFoundModel class                                        │
│                                                                 │
│     A) validateReportFields()                                   │
│        - Check type (lost/found)                                │
│        - Check species format                                   │
│        - Check phone number format                              │
│        - Check date (not in future)                             │
│        - Return: {valid: true/false, errors: [...]}            │
│                                                                 │
│     B) insertReport()                                           │
│        - Prepare PDO statement                                  │
│        - Bind parameters (prevent SQL injection)                │
│        - Execute INSERT                                        │
│        - Return: new record ID                                  │
└──────────────────┬──────────────────────────────────────────────┘
                   ↓
┌─────────────────────────────────────────────────────────────────┐
│  5. DATABASE (Data Storage)                                     │
│     Table: LostFoundReport                                      │
│                                                                 │
│     INSERT INTO LostFoundReport (                               │
│       type, location, date_reported, species, name,             │
│       color, notes, reward, photos, latitude, longitude,        │
│       user_id, submitted_at, phone, email                       │
│     ) VALUES (...)                                              │
│                                                                 │
│     ✓ Record saved with ID: 123                                │
└──────────────────┬──────────────────────────────────────────────┘
                   ↓
┌─────────────────────────────────────────────────────────────────┐
│  6. RESPONSE BACK (JSON to Frontend)                            │
│                                                                 │
│     {                                                           │
│       "success": true,                                          │
│       "message": "Report submitted successfully",               │
│       "report_id": 123                                          │
│     }                                                           │
└──────────────────┬──────────────────────────────────────────────┘
                   ↓
┌─────────────────────────────────────────────────────────────────┐
│  7. FRONTEND HANDLES RESPONSE                                   │
│     JavaScript code in lost-found.js                            │
│     - If success: Show success message                          │
│     - Reload page to show new report in list                    │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🔄 DATA FLOW SUMMARY

```
User Form Data
    ↓
JavaScript FormData (with validation)
    ↓
POST to API: /api/pet-owner/submit-report.php
    ↓
Controller: Extract, validate, upload files
    ↓
Model->validateReportFields(): Check for errors
    ↓
Controller: Prepare data array
    ↓
Model->insertReport(): Execute INSERT query
    ↓
Database: Row added to LostFoundReport table
    ↓
Model: Return new record ID
    ↓
API: Return JSON response {success: true, report_id: 123}
    ↓
JavaScript: Handle response, reload page
    ↓
View: Shows new report in Lost/Found list
```

---

## 📁 Files Involved (Quick Reference)

| Layer | File | Purpose |
|-------|------|---------|
| **VIEW** | `/views/pet-owner/lost-found.php` | HTML form UI |
| **JavaScript** | `/public/js/pet-owner/lost-found.js` | Form validation & submission |
| **Controller** | `/api/pet-owner/submit-report.php` | Entry point, file handling |
| **Model** | `/models/PetOwner/LostFoundModel.php` | Validation & DB insert |
| **Database** | `LostFoundReport` table | Data storage |
| **Upload** | `/uploads/lost-found/` | Photo storage |

---

## ✅ Key Points

1. **VIEW** → User sees form and clicks submit
2. **JAVASCRIPT** → Validates, collects data, sends to API
3. **CONTROLLER** → Receives request, validates, handles files, calls model
4. **MODEL** → Contains business logic (validation, database operations)
5. **DATABASE** → Stores the report permanently

**MVC Benefits:**
- **Separation of concerns** - each layer has one job
- **Reusability** - model can be used by multiple controllers
- **Testability** - can test validation logic independently
- **Maintainability** - changes in one layer don't break others
