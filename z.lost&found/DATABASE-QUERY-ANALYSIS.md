# Lost & Found Reports - Database Query Analysis

## Summary
The Lost & Found system fetches reports from the `LostFoundReport` table using multiple API endpoints and a Model layer. The fields `breed`, `age`, `color`, and `location` are stored both in individual columns AND in JSON within a description field for backward compatibility.

---

## 1. DATABASE QUERIES - WHERE REPORTS ARE FETCHED

### A. Main Model Layer
**File:** `models/PetOwner/LostFoundModel.php`

#### Query 1: `getAllReports()` - Lines 145-160
```php
// Fetches ALL reports (lost and found)
public function getAllReports() {
    $stmt = $this->db->prepare("
        SELECT * FROM LostFoundReport 
        ORDER BY date_reported DESC
    ");
    $stmt->execute();
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $this->formatReports($reports);
}
```
**Fields Selected:** `SELECT *` (all columns including breed, age, color, etc.)

#### Query 2: `getLostReports()` - Lines 250-266
```php
// Fetches ONLY lost pet reports
public function getLostReports() {
    $stmt = $this->db->prepare("
        SELECT * FROM LostFoundReport 
        WHERE type = 'lost'
        ORDER BY date_reported DESC
    ");
    $stmt->execute();
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $this->formatReports($reports);
}
```

#### Query 3: `getFoundReports()` - Lines 269-284
```php
// Fetches ONLY found pet reports
public function getFoundReports() {
    $stmt = $this->db->prepare("
        SELECT * FROM LostFoundReport 
        WHERE type = 'found'
        ORDER BY date_reported DESC
    ");
    $stmt->execute();
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $this->formatReports($reports);
}
```

#### Query 4: `searchReports()` - Lines 480-510
```php
// Search reports with optional type and query filters
public function searchReports($query, $species = null, $type = null) {
    $sql = "SELECT * FROM LostFoundReport WHERE 1=1";
    $params = [];
    
    if ($type && in_array($type, ['lost', 'found'])) {
        $sql .= " AND type = :type";
        $params[':type'] = $type;
    }
    
    if ($query) {
        $sql .= " AND (location LIKE :query OR description LIKE :query2)";
        $params[':query'] = '%' . $query . '%';
        $params[':query2'] = '%' . $query . '%';
    }
    
    $sql .= " ORDER BY date_reported DESC";
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    return $this->formatReports($stmt->fetchAll(PDO::FETCH_ASSOC));
}
```

---

### B. API Endpoints

#### 1. `api/pet-owner/get-reports.php` - Lines 1-65
```php
// Fetches reports with optional type and species filtering
// GET Parameters: ?type=lost|found&species=Dog|Cat&sort=new|old|days_missing

$sql = "SELECT * FROM LostFoundReport";
$params = [];
$where = [];

if ($type && in_array($type, ['lost', 'found'])) {
    $where[] = "type = :type";
    $params[':type'] = $type;
}

if ($species) {
    $where[] = "description LIKE :species";
    $params[':species'] = '%"species":"' . $species . '"%';  // Searches JSON field
}

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY date_reported DESC";  // or ASC for old
```

**Key Issue:** Species filter searches JSON field with string matching, NOT individual column

**Formatting in get-reports.php (Lines 58-75):**
```php
foreach ($reports as $report) {
    $description = json_decode($report['description'], true);
    
    $formattedReports[] = [
        'id' => $report['report_id'],
        'type' => $report['type'],
        'location' => $report['location'],
        'date' => $report['date_reported'],
        'species' => $description['species'] ?? '',
        'name' => $description['name'] ?? null,
        'color' => $description['color'] ?? '',      // Reads from JSON
        'notes' => $description['notes'] ?? '',
        'photos' => $description['photos'] ?? [],
        'days_missing' => $daysMissing,
        'contact' => $description['contact'] ?? [...]
        // NOTE: No breed or age fields returned!
    ];
}
```

#### 2. `api/pet-owner/get-reports-by-distance.php` - Lines 1-150
```php
// Fetches reports sorted by distance from user location
// GET Parameters: ?latitude=X&longitude=Y&type=lost|found

$sql = "SELECT * FROM LostFoundReport";
if ($type && in_array($type, ['lost', 'found'])) {
    $sql .= " WHERE type = :type";
}
$sql .= " ORDER BY date_reported DESC";
```

**Formatting (Lines 100-135):**
```php
foreach ($reportsWithDistance as $report) {
    $description = json_decode($report['description'], true);
    
    $formattedReports[] = [
        'id' => $report['report_id'],
        'type' => $report['type'],
        'name' => $description['name'] ?? null,
        'species' => $description['species'] ?? 'Unknown',
        'breed' => $description['breed'] ?? 'Unknown',      // ← DEFAULT: 'Unknown'
        'age' => $description['age'] ?? 'Unknown',          // ← DEFAULT: 'Unknown'
        'color' => $description['color'] ?? '',
        'photo' => $photos,
        'last_seen' => $report['location'],
        'date' => $report['date_reported'],
        'time' => $description['time'] ?? null,
        'notes' => $description['notes'] ?? '',
        'contact' => $description['contact'] ?? [
            'name' => 'Anonymous',
            'email' => '',
            'phone' => '',
            'phone2' => ''
        ],
        'latitude' => $description['latitude'] ?? null,
        'longitude' => $description['longitude'] ?? null,
        'distance_km' => $report['distance_km'],
        'distance_formatted' => $report['distance_formatted']
    ];
}
```

#### 3. `api/pet-owner/get-my-reports.php` - Lines 1-75
```php
// Fetches reports submitted by current logged-in user
// Uses JSON_UNQUOTE to query JSON field

$stmt = $db->prepare("
    SELECT * FROM LostFoundReport 
    WHERE CAST(JSON_UNQUOTE(JSON_EXTRACT(description, '$.user_id')) AS UNSIGNED) = :user_id
    ORDER BY date_reported DESC
");
$stmt->execute([':user_id' => (int)$userId]);
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
```

**Formatting (Lines 35-70):**
```php
foreach ($reports as $report) {
    $description = json_decode($report['description'], true);
    
    $formattedReports[] = [
        'id' => $report['report_id'],
        'type' => $report['type'],
        'location' => $report['location'],
        'date' => $report['date_reported'],
        'species' => $description['species'] ?? '',
        'name' => $description['name'] ?? null,
        'color' => $description['color'] ?? '',
        'notes' => $description['notes'] ?? '',
        'reward' => $report['reward'] ?? $description['reward'] ?? null,
        'price' => $report['price'] ?? $description['price'] ?? null,
        'risk' => $report['risk'] ?? $description['risk'] ?? null,
        'photos' => $description['photos'] ?? [],
        'latitude' => $description['latitude'] ?? null,
        'longitude' => $description['longitude'] ?? null,
        'time' => $description['time'] ?? null,
        'contact' => $description['contact'] ?? [...]
        'pet_id' => $description['pet_id'] ?? null,
        'listing_source' => !empty($description['pet_id']) ? 'my-pet-missing' : 'manual-report'
        // NOTE: No breed or age fields returned!
    ];
}
```

---

## 2. HOW BREED, AGE, COLOR, LOCATION ARE POPULATED

### A. During Insertion (Data Creation)

#### From Manual Report - `api/pet-owner/submit-report.php` (Lines 1-150)
```php
// These fields come from POST form data
$type = $_POST['type'] ?? '';
$species = $_POST['species'] ?? '';
$name = $_POST['name'] ?? '';
$color = $_POST['color'] ?? '';          // ← From form
$location = $_POST['location'] ?? '';    // ← From form (geocoded location)
$latitude = $_POST['latitude'] ?? null;  // ← From location picker
$longitude = $_POST['longitude'] ?? null;
$date = $_POST['date'] ?? '';
$time = $_POST['time'] ?? '';
$notes = $_POST['notes'] ?? '';
$reward = $_POST['reward'] ?? '';

// Build data array - breed and age are NOT in form!
$reportData = [
    'species' => $species,
    'name' => $name,
    'color' => $color,
    'breed' => null,          // ← NOT PROVIDED BY USER
    'age' => null,            // ← NOT PROVIDED BY USER
    'notes' => $notes,
    'time' => $time,
    'reward' => $reward,
    'contact' => [...],
    'photos' => $photoPaths,
    'latitude' => $latitude,
    'longitude' => $longitude,
    'user_id' => $_SESSION['user_id'],
    'submitted_at' => date('Y-m-d H:i:s')
];

// Insert via Model (saves to individual columns AND JSON description)
$reportId = $lostFoundModel->insertReport($type, $location, $date, $reportData);
```

#### From Pet Listing - `api/pet-owner/mark-pet-missing.php` (Lines 1-150)
```php
// Fetches pet from pets table
$petStmt = $db->prepare("
    SELECT id, user_id, name, species, breed, color, photo_url
    FROM pets
    WHERE id = ? AND user_id = ?
");
$pet = $petStmt->fetch(PDO::FETCH_ASSOC);

// Build report data from pet record
$reportData = [
    'species' => $pet['species'] ?? '',
    'name' => $pet['name'] ?? null,
    'breed' => $pet['breed'] ?? '',      // ← FROM PET TABLE
    'color' => $pet['color'] ?? '',      // ← FROM PET TABLE
    'age' => null,                       // ← NOT AVAILABLE FROM PET
    'notes' => $circumstances,           // User input
    'reward' => ($reward !== null && $reward !== '') ? (float)$reward : null,
    'urgency' => 'medium',
    'time' => $time,
    'photos' => !empty($pet['photo_url']) ? [$pet['photo_url']] : [],
    'contact' => [
        'phone' => $userContact['phone'] ?? '',
        'phone2' => '',
        'email' => $userContact['email'] ?? ''
    ],
    'latitude' => null,
    'longitude' => null,
    'user_id' => (int)$userId,
    'pet_id' => (int)$petId,
    'submitted_at' => date('Y-m-d H:i:s')
];

$reportId = $lostFoundModel->insertReport('lost', $location, $date, $reportData);
```

### B. Model Insertion - `models/PetOwner/LostFoundModel.php::insertReport()` (Lines 310-370)
```php
public function insertReport($type, $location, $date, $data) {
    // Convert array to JSON for description field
    $description = is_array($data) ? json_encode($data) : $data;
    
    // Extract individual fields from array
    $species = isset($data['species']) ? $data['species'] : null;
    $name = isset($data['name']) ? $data['name'] : null;
    $color = isset($data['color']) ? $data['color'] : null;
    $breed = isset($data['breed']) ? $data['breed'] : null;    // ← Can be null!
    $age = isset($data['age']) ? $data['age'] : null;          // ← Can be null!
    $notes = isset($data['notes']) ? $data['notes'] : null;
    $time = isset($data['time']) ? $data['time'] : null;
    $reward = isset($data['reward']) ? (float)$data['reward'] : null;
    $price = isset($data['price']) ? (float)$data['price'] : null;
    $risk = isset($data['risk']) ? $data['risk'] : 'medium';
    $phone = isset($data['contact']['phone']) ? $data['contact']['phone'] : null;
    $phone2 = isset($data['contact']['phone2']) ? $data['contact']['phone2'] : null;
    $email = isset($data['contact']['email']) ? $data['contact']['email'] : null;
    $photos = isset($data['photos']) ? json_encode($data['photos']) : null;
    $latitude = isset($data['latitude']) ? $data['latitude'] : null;
    $longitude = isset($data['longitude']) ? $data['longitude'] : null;
    $user_id = isset($data['user_id']) ? $data['user_id'] : null;
    $submitted_at = isset($data['submitted_at']) ? $data['submitted_at'] : date('Y-m-d H:i:s');
    
    $stmt = $this->db->prepare("
        INSERT INTO LostFoundReport (
            type, location, date_reported, species, name, color, breed, age, notes, time,
            reward, price, risk, phone, phone2, email, photos, latitude, longitude, user_id, 
            submitted_at, description
        ) VALUES (
            :type, :location, :date_reported, :species, :name, :color, :breed, :age, :notes, :time,
            :reward, :price, :risk, :phone, :phone2, :email, :photos, :latitude, :longitude, 
            :user_id, :submitted_at, :description
        )
    ");
    
    // Saves to BOTH individual columns AND JSON description
    $stmt->execute([
        ':type' => $type,
        ':location' => $location,
        ':date_reported' => $date,
        ':species' => $species,
        ':name' => $name,
        ':color' => $color,
        ':breed' => $breed,           // ← Saved to column
        ':age' => $age,               // ← Saved to column
        ':notes' => $notes,
        ':time' => $time,
        ':reward' => $reward,
        ':price' => $price,
        ':risk' => $risk,
        ':phone' => $phone,
        ':phone2' => $phone2,
        ':email' => $email,
        ':photos' => $photos,
        ':latitude' => $latitude,
        ':longitude' => $longitude,
        ':user_id' => $user_id,
        ':submitted_at' => $submitted_at,
        ':description' => $description  // ← Also saved as JSON
    ]);
    
    return $this->db->lastInsertId();
}
```

---

## 3. HOW FIELDS ARE FORMATTED FOR DISPLAY

### Model Formatting - `models/PetOwner/LostFoundModel.php::formatReports()` (Lines 177-240)
```php
private function formatReports($dbReports) {
    $formatted = [];
    
    foreach ($dbReports as $report) {
        // Try individual columns first, fallback to JSON
        $photos = [];
        if (!empty($report['photos'])) {
            $decoded = json_decode($report['photos'], true);
            $photos = is_array($decoded) ? $decoded : [$report['photos']];
        }
        
        // Fallback to description JSON if columns are empty
        if (empty($photos) && !empty($report['description'])) {
            $description = json_decode($report['description'], true);
            $photos = $description['photos'] ?? [];
        }
        
        if (empty($photos)) {
            $photos = ['/PETVET/public/img/default-pet.jpg']; // DEFAULT IMAGE
        }
        
        // Get contact from individual columns
        $contact = [
            'phone' => $report['phone'] ?? '',
            'phone2' => $report['phone2'] ?? '',
            'email' => $report['email'] ?? ''
        ];
        
        // Fallback to description JSON if columns empty
        if (empty($contact['phone']) && empty($contact['email']) && !empty($report['description'])) {
            $description = json_decode($report['description'], true);
            $contact = $description['contact'] ?? $contact;
        }
        
        $formatted[] = [
            'id' => $report['report_id'],
            'type' => $report['type'],
            'name' => $report['name'] ?? null,
            'species' => $report['species'] ?? 'Unknown',       // ← DEFAULT: 'Unknown'
            'breed' => $report['breed'] ?? 'Unknown',           // ← DEFAULT: 'Unknown'
            'age' => $report['age'] ?? 'Unknown',               // ← DEFAULT: 'Unknown'
            'color' => $report['color'] ?? '',
            'reward' => $report['reward'] ? (float)$report['reward'] : 0,
            'price' => $report['price'] ? (float)$report['price'] : 0,
            'risk' => $report['risk'] ?? 'medium',
            'time' => $report['time'] ?? null,
            'photo' => $photos,                                 // Array of URLs
            'last_seen' => $report['location'],
            'date' => $report['date_reported'],
            'days_missing' => $daysMissing,
            'notes' => $report['notes'] ?? '',
            'contact' => $contact,
            'latitude' => $report['latitude'] ?? null,
            'longitude' => $report['longitude'] ?? null,
            'user_id' => $report['user_id'] ?? null
        ];
    }
    
    return $formatted;
}
```

**Key Finding:** All defaults applied here - breed, age, species default to "Unknown"

---

## 4. WHERE "UNKNOWN" DEFAULTS ARE SET

### Primary Location: Model Formatting (Line 213-214)
```php
'breed' => $report['breed'] ?? 'Unknown',
'age' => $report['age'] ?? 'Unknown',
```

### Secondary Location: API get-reports-by-distance.php (Lines 113-114)
```php
'breed' => $description['breed'] ?? 'Unknown',
'age' => $description['age'] ?? 'Unknown',
```

### Tertiary Location: JavaScript my-pets.js (Lines 541, 659)
```javascript
${currentPet.breed && currentPet.breed !== 'Unknown' ? ' (' + currentPet.breed + ')' : ''}
```

### Views Handling Unknown (views/**/lost-found.php, Line 78-79)
```php
<img src="<?php echo lf_esc($photos[0]); ?>" 
     alt="<?php echo lf_esc($r['name'] ?: ($r['species'].' (unknown name)')); ?>" 
     class="carousel-image" data-index="0">
```

---

## 5. SUMMARY TABLE

| Field | Manual Form | From Pet | Stored Columns | Stored JSON | Fetch Sources | Default Display |
|-------|------------|----------|----------------|-------------|----------------|-----------------|
| **breed** | ❌ Not in form | ✅ From pet record | ✅ `breed` column | ✅ In description | formatReports() | **'Unknown'** |
| **age** | ❌ Not in form | ❌ Not in pet data | ✅ `age` column | ✅ In description | formatReports() | **'Unknown'** |
| **color** | ✅ In form | ✅ From pet record | ✅ `color` column | ✅ In description | get-reports.php returns no breed/age; get-reports-by-distance returns with defaults | (empty string) |
| **location** | ✅ In form (geocoded) | ❌ Manual input only | ✅ `location` column | ✅ In description | All APIs fetch | (value or null) |

---

## 6. DATA FLOW DIAGRAM

```
USER SUBMITS MANUAL REPORT
│
├─→ api/pet-owner/submit-report.php
│   ├─ breed = NULL (not in form)
│   ├─ age = NULL (not in form)
│   ├─ color = form input
│   └─ location = geocoded address
│
└─→ LostFoundModel::insertReport()
    ├─ Saves to individual columns
    │  (breed=null, age=null, color=X, location=Y)
    └─ Saves to description JSON
       (all fields encoded)


USER "MARKS PET MISSING" 
│
├─→ api/pet-owner/mark-pet-missing.php
│   ├─ breed = FROM pet.breed
│   ├─ age = NULL (not in pet record)
│   ├─ color = FROM pet.color
│   └─ location = form input
│
└─→ LostFoundModel::insertReport()
    ├─ Saves to individual columns
    │  (breed=X, age=null, color=Y, location=Z)
    └─ Saves to description JSON


FRONTEND FETCHES REPORTS
│
├─→ api/pet-owner/get-reports.php
│   └─ Returns species, name, color only
│      (NO breed, NO age in response)
│
├─→ api/pet-owner/get-reports-by-distance.php
│   └─ Calls LostFoundModel::formatReports()
│       ├─ breed: column ?? 'Unknown'
│       ├─ age: column ?? 'Unknown'
│       ├─ color: column ?? ''
│       └─ location: column
│
└─→ Views display formatted data
    └─ Shows 'Unknown' for null breed/age
```

---

## 7. KEY ISSUES IDENTIFIED

1. **Breed & Age are NOT available in manual report form** - Users cannot specify these when reporting lost/found pets manually

2. **Age is NEVER populated** - Even when marking a pet missing from My Pets, the pet table doesn't have age field, so it stays NULL

3. **Inconsistent field presence** - get-reports.php doesn't return breed/age at all, while get-reports-by-distance.php includes them with defaults

4. **"Unknown" is a UI fallback** - It's added at model layer when fields are NULL, not stored as actual value in database

5. **Dual storage overhead** - Data stored in BOTH individual columns AND JSON description field for backward compatibility

6. **Species field inconsistency** - Sometimes defaults to 'Unknown' (formatReports), sometimes to empty string (get-reports.php)

---

## 8. DATABASE SCHEMA
**Table:** `LostFoundReport`

Relevant columns for pet details:
```
species         VARCHAR(100)    - Pet type (Dog, Cat, etc)
name            VARCHAR(255)    - Pet name
breed           VARCHAR(100)    - Pet breed (often NULL)
color           VARCHAR(255)    - Pet color
age             VARCHAR(100)    - Pet age (often NULL)
location        VARCHAR(255)    - Where pet was lost/found
description     TEXT            - JSON with all details (legacy)
photos          JSON            - Photo URLs array
```
