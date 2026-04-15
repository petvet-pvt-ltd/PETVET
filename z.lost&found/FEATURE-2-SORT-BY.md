# Feature 2: Sort Reports (Newest, Oldest, Days Missing)

## Overview
Add a "Sort By" dropdown with three options:
- **Newest** (date_reported DESC)
- **Oldest** (date_reported ASC)  
- **Days Missing** (sort by days missing, longest first)

---

## Files to Modify

### 1. **`views/pet-owner/lost-found.php`** - Add Sort Dropdown
**What**: Add HTML dropdown for sorting

**Location**: Next to the Species filter dropdown

**Simple code needed**:
```php
<label>Sort By:
    <select id="sortBy">
        <option value="newest">Newest First</option>
        <option value="oldest">Oldest First</option>
        <option value="days_missing">Days Missing (Most)</option>
    </select>
</label>
```

**Then add JavaScript listener**:
```javascript
document.getElementById('sortBy').addEventListener('change', function() {
    const sortType = this.value;
    sortReports(sortType);
});
```

---

### 2. **`public/js/pet-owner/lost-found.js`** - Handle Sort Change
**What**: When sort dropdown changes, reorder reports

**Location**: Add new function in lost-found.js

**Simple function needed**:
```javascript
function sortReports(sortType) {
    let url = 'api/pet-owner/get-reports.php?sort=' + sortType;
    
    // Include current species filter if set
    const species = document.getElementById('speciesFilter').value;
    if (species) {
        url += '&species=' + encodeURIComponent(species);
    }
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            displayReports(data);
        });
}
```

---

### 3. **`api/pet-owner/get-reports.php`** - Add Sort Logic
**What**: Accept sort parameter and sort results accordingly

**Location**: After filtering by species

**Simple code needed**:
```php
$sort = $_GET['sort'] ?? 'newest';

if ($sort === 'oldest') {
    usort($reports, function($a, $b) {
        return strtotime($a['date_reported']) - strtotime($b['date_reported']);
    });
} elseif ($sort === 'days_missing') {
    usort($reports, function($a, $b) {
        $daysA = calculateDaysMissing($a['date_reported']);
        $daysB = calculateDaysMissing($b['date_reported']);
        return $daysB - $daysA; // Longest first
    });
} else { // 'newest' (default)
    usort($reports, function($a, $b) {
        return strtotime($b['date_reported']) - strtotime($a['date_reported']);
    });
}
```

**Helper function** (add at top of get-reports.php):
```php
function calculateDaysMissing($dateReported) {
    $today = new DateTime();
    $reported = new DateTime($dateReported);
    $interval = $today->diff($reported);
    return $interval->days;
}
```

---

## Data Flow
```
User selects sort option
        ↓
JavaScript calls sortReports(sortType)
        ↓
Fetch to get-reports.php?sort=newest (+ species filter if set)
        ↓
PHP calculates days missing and sorts
        ↓
Returns sorted results
        ↓
Display in new order
```

---

## Ready?
Say **"Next: Sort Implementation"** to start coding!
