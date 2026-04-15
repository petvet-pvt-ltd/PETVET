# Feature 3: Display "Days Missing" on Cards

## Overview
Show how many days each report has been missing on the card.
- Display: "Missing for X days"
- Highlight with **yellow badge** if missing more than 7 days

---

## Files to Modify

### 1. **`models/PetOwner/LostFoundModel.php`** - Calculate Days Missing
**What**: Add days missing calculation to formatted report data

**Location**: In `formatReports()` method where other fields are added

**Simple code needed**:
```php
// After fetching each report
$daysMissing = $this->calculateDaysMissing($row['date_reported']);

// Add to return array
'days_missing' => $daysMissing,
```

**Helper function** (add to LostFoundModel class):
```php
private function calculateDaysMissing($dateReported) {
    $today = new DateTime();
    $reported = new DateTime($dateReported);
    $interval = $today->diff($reported);
    return $interval->days;
}
```

---

### 2. **`views/pet-owner/lost-found.php`** - Display Days Missing on Card
**What**: Show "Missing for X days" and highlight if > 7 days

**Location**: On each report card (in the loop)

**Simple code needed**:
```php
<?php
    $daysMissing = $r['days_missing'];
    $highlight = $daysMissing > 7 ? 'style="background-color: #fff3cd; padding: 5px; border: 1px solid #ffc107; border-radius: 4px;"' : '';
?>

<p <?php echo $highlight; ?>>
    Missing for <?php echo $daysMissing; ?> days
</p>
```

---

### 3. **Optional: Add CSS for Badge Style** (If desired)
**Location**: `public/css/pet-owner/lost-found.css`

**Simple CSS** (only if you want):
```css
.days-missing-badge {
    background-color: #fff3cd;
    border: 1px solid #ffc107;
    padding: 5px 10px;
    border-radius: 4px;
    color: #856404;
}
```

Then use in HTML:
```php
<p class="days-missing-badge">
    Missing for <?php echo $daysMissing; ?> days
</p>
```

---

## Data Flow
```
Report date stored in database
        ↓
Model calculates: TODAY - date_reported = days_missing
        ↓
Add to formatted report array
        ↓
View displays: "Missing for X days"
        ↓
If X > 7, apply yellow highlight
```

---

## Display Examples
- "Missing for 2 days" (normal)
- "Missing for 10 days" (highlighted yellow)
- "Missing for 15 days" (highlighted yellow)

---

## Ready?
Say **"Next: Days Missing Implementation"** to start coding!
