# Feature 1: Filter Reports by Species

## Overview
Add a dropdown to filter reports by species: **Dog, Cat, Bird, Other**

---

## Files to Modify

### 1. **`views/pet-owner/lost-found.php`** - Add Filter Dropdown
**What**: Add HTML dropdown for species filter in the report list section

**Location**: Above the reports list (before the loop that displays cards)

**Simple code needed**:
```php
<label>Filter by Species:
    <select id="speciesFilter">
        <option value="">-- All Species --</option>
        <option value="Dog">Dog</option>
        <option value="Cat">Cat</option>
        <option value="Bird">Bird</option>
        <option value="Other">Other</option>
    </select>
</label>
```

**Then add JavaScript listener**:
```javascript
document.getElementById('speciesFilter').addEventListener('change', function() {
    const species = this.value;
    // Load reports with this species (will add to get-reports.php)
    loadReports(species);
});
```

---

### 2. **`public/js/pet-owner/lost-found.js`** - Handle Filter Change
**What**: When dropdown changes, reload reports with species filter

**Location**: In the lost-found.js file

**Simple function needed**:
```javascript
function loadReports(species = '') {
    let url = 'api/pet-owner/get-reports.php';
    if (species) {
        url += '?species=' + encodeURIComponent(species);
    }
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            // Load reports data
            displayReports(data);
        });
}
```

---

### 3. **`api/pet-owner/get-reports.php`** - Add Species Filter
**What**: Accept species query parameter and filter results

**Location**: In the SQL query or after fetching

**Simple code needed**:
```php
// Get species parameter from URL
$species = $_GET['species'] ?? '';

// If species selected, filter results
if ($species) {
    $reports = array_filter($reports, function($report) use ($species) {
        return $report['species'] === $species;
    });
}
```

---

### 4. **Display "No Matches Found"**
**Location**: In lost-found.php where reports display

**Simple code needed**:
```php
<?php if (empty($reports)): ?>
    <p>No reports match your filter. Try another species!</p>
<?php else: ?>
    <!-- Display reports as usual -->
<?php endif; ?>
```

---

## Data Flow
```
User selects species in dropdown
        ↓
JavaScript calls loadReports(species)
        ↓
Makes fetch request to get-reports.php?species=Dog
        ↓
PHP filters by species
        ↓
Returns filtered results
        ↓
Display reports or "No matches found"
```

---

## Ready?
Say **"Next: Species Filter Implementation"** to start coding!
