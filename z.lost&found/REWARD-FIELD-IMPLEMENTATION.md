# Lost & Found - Add "Reward" Field Implementation Guide

## Overview
This document covers all changes needed to add a "Reward" field to Lost & Found pet reports.

---

## 1. DATABASE & MODELS

### File: `models/PetOwner/LostFoundModel.php`

**Location:** Line 43 in `formatReports()` method

**Change:** Update reward extraction to ensure it's always a number

```php
// OLD:
'reward' => $description['reward'] ?? 0,

// NEW:
'reward' => !empty($description['reward']) ? (float)$description['reward'] : 0,
```

**Why:** Prevents `number_format()` error when reward is empty string

---

## 2. VIEWS

### File: `views/pet-owner/lost-found.php`

#### Update 1: Add Reward Input - Report Form Modal
**Location:** Around line 230-240 (after Color field)

```php
<div class="row">
    <label class="field flex-2">Name (optional)
        <input type="text" id="rName" placeholder="Rocky / Unknown">
    </label>
    <label class="field">Color
        <input type="text" id="rColor" placeholder="Golden / Black">
    </label>
</div>
<!-- ADD THIS: -->
<div class="row">
    <label class="field">Reward
        <input type="number" id="rReward" placeholder="0.00" min="0">
    </label>
</div>
<!-- END ADD -->
<label class="field">Select location on map
```

#### Update 2: Add Reward Input - Edit Form Modal
**Location:** Around line 340-350 (after Color field in edit form)

```php
<div class="row">
    <label class="field flex-2">Name (optional)
        <input type="text" id="editName" placeholder="Rocky / Unknown">
    </label>
    <label class="field">Color
        <input type="text" id="editColor" placeholder="Golden / Black">
    </label>
</div>
<!-- ADD THIS: -->
<div class="row">
    <label class="field">Reward
        <input type="number" id="editReward" placeholder="0.00">
    </label>
</div>
<!-- END ADD -->
<label class="field">Select location on map
```

#### Update 3: Display Reward on Card - LOST Section
**Location:** Around line 108 (in lost reports loop, after time-ago element)

```php
<p class="time-ago" data-time="<?php echo lf_esc($r['time'] ?? ''); ?>" data-date="<?php echo lf_esc($r['date']); ?>" style="color: var(--primary); font-weight: 500; font-size: 0.9em; margin-top: 4px;"></p>
<!-- ADD THIS: -->
<?php if(!empty($r['reward']) && $r['reward'] > 0): ?>
    <p>Reward: $<?php echo number_format($r['reward'], 2); ?></p>
<?php endif; ?>
<!-- END ADD -->
<p class="report-distance" data-report-id="<?php echo lf_esc($r['id']); ?>">
```

#### Update 4: Display Reward on Card - FOUND Section
**Location:** Around line 155 (in found reports loop, after time-ago element)

```php
<p class="time-ago" data-time="<?php echo lf_esc($r['time'] ?? ''); ?>" data-date="<?php echo lf_esc($r['date']); ?>" style="color: var(--primary); font-weight: 500; font-size: 0.9em; margin-top: 4px;"></p>
<!-- ADD THIS: -->
<?php if(!empty($r['reward']) && $r['reward'] > 0): ?>
    <p>Reward: $<?php echo number_format($r['reward'], 2); ?></p>
<?php endif; ?>
<!-- END ADD -->
<p class="report-distance" data-report-id="<?php echo lf_esc($r['id']); ?>">
```

---

## 3. APIs

### File: `api/pet-owner/submit-report.php`

#### Update 1: Get Reward from Form
**Location:** Around line 46 (with other form data extraction)

```php
$email = $_POST['email'] ?? '';
// ADD THIS:
$reward = $_POST['reward'] ?? '';
```

#### Update 2: Add Phone Validation (Optional but Recommended)
**Location:** Around line 61 (after type validation)

```php
// Validate type
if (!in_array($type, ['lost', 'found'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid type. Must be "lost" or "found".']);
    exit;
}

// ADD THIS: Phone validation
if (empty($phone)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Phone number is required']);
    exit;
}

if (!preg_match('/^(\d{10}|\+94\d{9})$/', $phone)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Phone must be 10 digits or +94 with 9 digits']);
    exit;
}

if (!empty($phone2) && !preg_match('/^(\d{10}|\+94\d{9})$/', $phone2)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Secondary phone must be 10 digits or +94 with 9 digits']);
    exit;
}
// END PHONE VALIDATION
```

#### Update 3: Add Reward to JSON Data
**Location:** Around line 127 (in `$additionalData` array)

```php
$additionalData = [
    'species' => $species,
    'name' => $name,
    'color' => $color,
    'notes' => $notes,
    'time' => $time,
    // ADD THIS:
    'reward' => $reward,
    // END ADD
    'contact' => [
        'phone' => $phone,
        'phone2' => $phone2,
        'email' => $email
    ],
    'photos' => $photoPaths,
    'latitude' => $latitude,
    'longitude' => $longitude,
    'user_id' => $_SESSION['user_id'],
    'submitted_at' => date('Y-m-d H:i:s')
];
```

---

### File: `api/pet-owner/update-report.php`

#### Update 1: Get Reward from Form
**Location:** Around line 68 (with other form data extraction)

```php
$email = $_POST['email'] ?? $existingDescription['contact']['email'];
// ADD THIS:
$reward = isset($_POST['reward']) && $_POST['reward'] !== '' ? $_POST['reward'] : ($existingDescription['reward'] ?? null);
```

#### Update 2: Add Reward to JSON Data
**Location:** Around line 165-180 (in updated `$additionalData` array)

```php
$additionalData = [
    'species' => $species,
    'name' => $name,
    'color' => $color,
    'notes' => $notes,
    'time' => $time,
    // ADD THIS:
    'reward' => $reward,
    // END ADD
    'contact' => [
        'phone' => $phone,
        'phone2' => $phone2,
        'email' => $email
    ],
    // ... rest of array
];
```

---

## 4. JAVASCRIPT

### File: `public/js/pet-owner/lost-found.js`

#### Update 1: Get Reward on Form Submit
**Location:** In the `reportForm.addEventListener('submit')` handler (around line 600-650)

Find where form data is appended:
```javascript
reportForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(reportForm);
    // ... other form fields
    formData.append('color', qs('#rColor').value);
    // ADD THIS:
    formData.append('reward', qs('#rReward').value || '0');
    // END ADD
    formData.append('location', qs('#rLocation').value);
```

#### Update 2: Get Reward on Edit Form Submit
**Location:** In the `editListingForm.addEventListener('submit')` handler (around line 750-800)

Find where edit form data is appended:
```javascript
editListingForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const editFormData = new FormData(editListingForm);
    // ... other form fields
    editFormData.append('color', qs('#editColor').value);
    // ADD THIS:
    editFormData.append('reward', qs('#editReward').value || '0');
    // END ADD
    editFormData.append('location', qs('#editLocation').value);
```

#### Update 3: Populate Reward on Edit Modal Open
**Location:** In the function that loads edit modal data (around line 700-730)

Find where you populate edit fields:
```javascript
// Inside the edit modal population function
qs('#editColor').value = listing.color;
// ADD THIS:
qs('#editReward').value = listing.reward > 0 ? listing.reward : '';
```

---

## 5. SUMMARY TABLE

| File | Location | Change | Type |
|------|----------|--------|------|
| `LostFoundModel.php` | Line 43 | Cast reward to float | Bug Fix |
| `lost-found.php` | Line 230 | Add reward input (report form) | Form Field |
| `lost-found.php` | Line 340 | Add reward input (edit form) | Form Field |
| `lost-found.php` | Line 108 | Display reward (lost cards) | Display |
| `lost-found.php` | Line 155 | Display reward (found cards) | Display |
| `submit-report.php` | Line 46 | Get reward from POST | Data Extract |
| `submit-report.php` | Line 61 | Phone validation | Validation |
| `submit-report.php` | Line 127 | Add reward to JSON | Data Storage |
| `update-report.php` | Line 68 | Get reward from POST | Data Extract |
| `update-report.php` | Line 170 | Add reward to JSON | Data Storage |
| `lost-found.js` | Line 600+ | Append reward to form | Form Submit |
| `lost-found.js` | Line 750+ | Append reward to edit form | Form Submit |
| `lost-found.js` | Line 700+ | Populate reward on edit | Form Population |

---

## 6. TESTING CHECKLIST

- [ ] Form displays reward input field
- [ ] Can submit report with reward
- [ ] Can submit report without reward (optional)
- [ ] Reward displays on card (only if > 0)
- [ ] Reward persists after edit
- [ ] Phone validation rejects invalid formats
- [ ] Phone validation accepts `0777123456` format
- [ ] Phone validation accepts `+94771234567` format
- [ ] Database stores reward in JSON description field
- [ ] No `number_format()` errors on display

---

## 7. FILE LOCATIONS QUICK REFERENCE

```
PETVET/
├── models/PetOwner/LostFoundModel.php
├── views/pet-owner/lost-found.php
├── api/pet-owner/
│   ├── submit-report.php
│   └── update-report.php
└── public/js/pet-owner/lost-found.js
```

---

## NOTES

- **No database migration needed** - Reward is stored in JSON `description` field
- **Optional field** - Users can leave blank, defaults to 0
- **Validation location** - Backend validation in PHP protects data integrity
- **Phone format** - Accepts 10-digit (0777123456) or +94 format (+94771234567 with 9 digits)
