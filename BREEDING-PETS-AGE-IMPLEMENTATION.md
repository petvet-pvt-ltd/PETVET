# Breeding Pets - Age Field Implementation

## Summary of Changes

This document details all changes made to implement automatic age calculation from Date of Birth (DOB) for the Breeding Pets module. The age is calculated both in real-time (JavaScript) and when saving (PHP), then stored in the database.

---

## Files Modified

### 1. `views/breeder/breeding-pets.php`
**Purpose:** Add age field to the Add/Edit Breeding Pet form

#### Changes Made:
- Added read-only age input field below the DOB field
- Field auto-calculates when user selects DOB
- Age field is NOT submitted with form (calculated backend)

#### Code Added:
```php
<div class="form-group">
    <label for="petAge">Age</label>
    <input type="text" id="petAge" class="form-control" readonly placeholder="Auto-calculated from Date of Birth">
</div>
```

#### Modified DOB Field:
```php
<div class="form-group">
    <label for="petDob">Date of Birth *</label>
    <input type="date" id="petDob" name="dob" class="form-control" required max="<?php echo date('Y-m-d'); ?>" onchange="calculateAge()">
</div>
```

**Key Points:**
- `onchange="calculateAge()"` - Triggers JavaScript calculation when DOB changes
- Age field has `readonly` attribute - Users cannot manually edit
- Placeholder shows "Auto-calculated from Date of Birth"
- Age field has NO `name` attribute - Not submitted to database (backend calculates it)

---

### 2. `public/js/breeder/breeding-pets.js`
**Purpose:** Handle real-time age calculation on frontend

#### Changes Made:

##### A. Updated `showAddPetModal()` function:
```javascript
function showAddPetModal() {
    editingPetId = null;
    document.getElementById('modalTitle').textContent = 'Add New Breeding Pet';
    document.getElementById('petForm').reset();
    document.getElementById('petId').value = '';
    document.getElementById('photoPreview').innerHTML = '<span class="photo-placeholder">📷</span>';
    document.getElementById('petAge').value = '';  // ← NEW: Clear age field
    document.getElementById('petModal').classList.add('active');
}
```

##### B. Updated `showEditPetModal()` function:
```javascript
function showEditPetModal(petId) {
    editingPetId = petId;
    document.getElementById('modalTitle').textContent = 'Edit Breeding Pet';
    
    // Find pet data
    const pet = breedingPetsData.find(p => p.id == petId);
    if (!pet) return;
    
    // Populate form
    document.getElementById('petId').value = pet.id;
    document.getElementById('petName').value = pet.name;
    document.getElementById('petBreed').value = pet.breed;
    document.getElementById('petGender').value = pet.gender;
    document.getElementById('petDob').value = pet.dob;
    document.getElementById('petDescription').value = pet.description || '';
    document.getElementById('petActive').checked = pet.is_active;
    
    // Set age from database ← NEW: Load age from database
    document.getElementById('petAge').value = pet.age || '';
    
    // Show photo if exists
    if (pet.photo) {
        document.getElementById('photoPreview').innerHTML = `<img src="${pet.photo}" alt="Pet Photo">`;
    }
    
    document.getElementById('petModal').classList.add('active');
}
```

##### C. New `calculateAge()` function:
```javascript
// Calculate Age from Date of Birth
function calculateAge() {
    const dobInput = document.getElementById('petDob').value;
    const ageInput = document.getElementById('petAge');
    
    if (!dobInput) {
        ageInput.value = '';
        return;
    }
    
    const dob = new Date(dobInput);
    const today = new Date();
    let age = today.getFullYear() - dob.getFullYear();
    
    // Adjust if birthday hasn't occurred this year
    if (today.getMonth() < dob.getMonth() || 
        (today.getMonth() === dob.getMonth() && today.getDate() < dob.getDate())) {
        age--;
    }
    
    ageInput.value = age + (age === 1 ? ' year' : ' years');
}
```

**How it works:**
1. Gets DOB value from form
2. Parses it as JavaScript Date object
3. Calculates difference in years
4. Adjusts if birthday hasn't occurred yet this year
5. Formats display as "X year(s)"

##### D. Updated DOMContentLoaded event:
```javascript
// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Breeding Pets page loaded');
    console.log('Total pets:', breedingPetsData.length);
    
    // Add event listener for DOB field ← NEW
    const dobField = document.getElementById('petDob');
    if (dobField) {
        dobField.addEventListener('change', function() {
            calculateAge();
        });
    }
});
```

**Purpose:**
- Ensures DOB field has event listener when page loads
- Triggers `calculateAge()` whenever DOB changes
- Provides real-time age preview to user

---

### 3. `api/breeder/manage-breeding-pets.php`
**Purpose:** Calculate and store age in database

#### Changes Made:

##### A. Updated `addBreedingPet()` function:

**NEW: Age calculation with error handling:**
```php
// Validate required fields
if (empty($name) || empty($breed) || empty($gender) || empty($dob)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    return;
}

// Calculate age from date of birth
$age = 0;
try {
    $dobDate = new DateTime($dob);
    $today = new DateTime();
    $age = $today->diff($dobDate)->y;
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid date format: ' . $e->getMessage()]);
    return;
}
```

**UPDATED: INSERT statement includes age:**
```php
$stmt = $conn->prepare("
    INSERT INTO breeder_pets (breeder_id, name, breed, gender, date_of_birth, age, photo, description, is_active)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param("issssissi", $userId, $name, $breed, $gender, $dob, $age, $photoPath, $description, $isActive);
```

**Type string:** `"issssissi"`
- i = integer (userId)
- s = string (name)
- s = string (breed)
- s = string (gender)
- s = string (dob)
- i = integer (age) ← NEW
- s = string (photoPath)
- s = string (description)
- i = integer (isActive)

---

##### B. Updated `updateBreedingPet()` function:

**NEW: Age calculation with error handling:**
```php
// Calculate age from date of birth
$age = 0;
try {
    $dobDate = new DateTime($dob);
    $today = new DateTime();
    $age = $today->diff($dobDate)->y;
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid date format: ' . $e->getMessage()]);
    return;
}
```

**UPDATED: UPDATE statement with photo:**
```php
if ($updatePhoto) {
    $stmt = $conn->prepare("
        UPDATE breeder_pets 
        SET name = ?, breed = ?, gender = ?, date_of_birth = ?, age = ?, photo = ?, description = ?, is_active = ?
        WHERE id = ? AND breeder_id = ?
    ");
    $stmt->bind_param("ssssissiii", $name, $breed, $gender, $dob, $age, $photoPath, $description, $isActive, $petId, $userId);
}
```

**UPDATED: UPDATE statement without photo:**
```php
else {
    $stmt = $conn->prepare("
        UPDATE breeder_pets 
        SET name = ?, breed = ?, gender = ?, date_of_birth = ?, age = ?, description = ?, is_active = ?
        WHERE id = ? AND breeder_id = ?
    ");
    $stmt->bind_param("ssssisiii", $name, $breed, $gender, $dob, $age, $description, $isActive, $petId, $userId);
}
```

**Type strings:**
- With photo: `"ssssissiii"` (10 parameters)
- Without photo: `"ssssisiii"` (9 parameters)

---

##### C. `getAllBreedingPets()` function - Updated SELECT:

```php
function getAllBreedingPets($conn, $userId) {
    $stmt = $conn->prepare("
        SELECT id, name, breed, gender, date_of_birth as dob, 
               photo, description, is_active, age
        FROM breeder_pets
        WHERE breeder_id = ?
        ORDER BY created_at DESC
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $pets = [];
    while ($row = $result->fetch_assoc()) {
        $row['is_active'] = (bool)$row['is_active'];
        $row['age'] = $row['age'] . ' ' . ($row['age'] == 1 ? 'year' : 'years');
        $pets[] = $row;
    }
    
    echo json_encode(['success' => true, 'pets' => $pets]);
}
```

**Changes:**
- Fetches `age` column from database
- Formats as "X year(s)" for display
- Example: age=3 → "3 years", age=1 → "1 year"

---

## Database Schema

### Added Column:
```sql
ALTER TABLE breeder_pets 
ADD COLUMN age INT DEFAULT 0 AFTER date_of_birth;
```

### Updated breeder_pets Table Structure:
```
id              INT           PRIMARY KEY AUTO_INCREMENT
breeder_id      INT           FOREIGN KEY (users.id)
name            VARCHAR(100)  NOT NULL
breed           VARCHAR(100)  NOT NULL
gender          ENUM          NOT NULL (Male/Female)
date_of_birth   DATE          NOT NULL
age             INT           DEFAULT 0        ← NEW COLUMN
photo           VARCHAR(255)  NULLABLE
description     TEXT          NULLABLE
is_active       TINYINT       DEFAULT 1
created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
updated_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
```

---

## How It Works - Complete Flow

### **Adding a New Pet:**

```
1. User opens "Add New Breeding Pet" form
   └─ Age field is cleared
   └─ Age field shows placeholder: "Auto-calculated from Date of Birth"

2. User selects Date of Birth (e.g., 06/06/2022)
   └─ onchange event fires
   └─ JavaScript calculateAge() runs
   └─ Age field displays: "3 years"

3. User clicks "Save Pet"
   └─ Form data sent to backend

4. Backend (PHP) processes:
   └─ Receives DOB: "2022-06-06"
   └─ Validates all required fields
   └─ Calculates age using DateTime: 2026 - 2022 = 3 years
   └─ Inserts: name, breed, gender, DOB, AGE (3), photo, description, status

5. Page reloads
   └─ Table displays with age from database: "3 years"
```

### **Editing a Pet:**

```
1. User clicks Edit button on existing pet
   └─ Form pre-fills with existing data
   └─ Age field shows from database: "3 years"

2. User changes DOB (e.g., 06/06/2020)
   └─ JavaScript calculateAge() automatically recalculates
   └─ Age field updates to: "5 years"

3. User clicks "Save"
   └─ Form data sent to backend

4. Backend (PHP) processes:
   └─ Validates fields
   └─ Recalculates age from new DOB: 2026 - 2020 = 5 years
   └─ Updates: name, breed, gender, DOB, AGE (5), description, status

5. Page reloads
   └─ Table shows updated age: "5 years"
```

### **Viewing Table:**

```
Table displays:
Photo | Name | Breed | Gender | DOB | Age | Status | Actions

Example row:
[dog] | Max  | Labrador | Male | Jun 06, 2022 | 3 years | Active | Edit/Delete
```

---

## Key Features

✅ **Real-time calculation** - Age updates as user types DOB (JavaScript)  
✅ **Accurate storage** - Age recalculated and stored each time (PHP)  
✅ **No manual entry** - Users cannot manually enter age  
✅ **Error handling** - Invalid dates caught with try-catch blocks  
✅ **Proper formatting** - "1 year" or "2 years" for display  
✅ **Database persistence** - Age stored for quick retrieval  
✅ **Edit support** - Age updates when DOB changes during edit  

---

## Error Handling

### JavaScript Errors:
- If DOB field is empty: Age field cleared
- Invalid date formats: Handled by Date object

### PHP Errors:
```php
try {
    $dobDate = new DateTime($dob);
    $today = new DateTime();
    $age = $today->diff($dobDate)->y;
} catch (Exception $e) {
    // Returns JSON error instead of crashing
    echo json_encode(['success' => false, 'message' => 'Invalid date format']);
}
```

---

## Testing Checklist

- [ ] Age field appears in form (read-only)
- [ ] Age calculates when DOB is selected
- [ ] Age displays as "X year(s)"
- [ ] Age saves to database (can verify in phpMyAdmin)
- [ ] Age displays in breeding pets table
- [ ] Age updates when editing pet and changing DOB
- [ ] Error shows if invalid date is entered
- [ ] Page works without errors after all changes

---

## Technical Details

| Aspect | Frontend (JavaScript) | Backend (PHP) |
|--------|----------------------|---------------|
| **Language** | JavaScript | PHP 7.0+ |
| **Calculation Method** | `Date.getFullYear()` difference | `DateTime->diff()->y` |
| **Format** | "3 years" or "1 year" | Integer (3 or 1) stored in DB |
| **Timing** | Real-time (as user types) | When form is saved |
| **Purpose** | User preview/feedback | Official calculation & storage |

---

## Files Summary

| File | Changes | Lines Modified |
|------|---------|-----------------|
| views/breeder/breeding-pets.php | Added age input field | ~5 lines |
| public/js/breeder/breeding-pets.js | Added calculateAge() function, updated modals, added event listener | ~40 lines |
| api/breeder/manage-breeding-pets.php | Added age calculation in add/update functions, updated SQL queries | ~30 lines |

---

## Notes

- Age is **calculated** not stored manually
- Age is stored as **integer** (years only)
- Age is **recalculated** on each save (ensures accuracy)
- Age field is **read-only** in form (prevents manual entry)
- Both **JavaScript and PHP** calculate age (frontend preview + backend validation)

---
