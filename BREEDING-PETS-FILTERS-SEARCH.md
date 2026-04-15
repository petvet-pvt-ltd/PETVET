# Breeding Pets - Filters & Search Implementation

**Date:** April 15, 2026  
**Branch:** test-branch-1  
**Focus:** Filter & Search Functionality Only

---

## Table of Contents
1. [Overview](#overview)
2. [Files Modified](#files-modified)
3. [Filter UI Implementation](#filter-ui-implementation)
4. [JavaScript Filter Logic](#javascript-filter-logic)
5. [CSS Styling](#css-styling)
6. [Filter Flow & Logic](#filter-flow--logic)

---

## Overview

This documentation covers the complete filter and search implementation for the Breeding Pets Management module:

### Features Implemented:
- **Breed Search**: Text input for searching by breed name (case-insensitive)
- **Age Range Filter**: Dropdown to filter by age groups (Young, Adult, Senior)
- **Status Filter**: Dropdown to show Active or Inactive pets
- **Multi-Filter Support**: Combine multiple filters with AND logic
- **Reset Button**: Clear all filters with one click
- **Real-time Filtering**: Instant results as user changes filters

---

## Files Modified

### Modified Files:
1. `/views/breeder/breeding-pets.php` - Filter UI HTML section
2. `/public/js/breeder/breeding-pets.js` - Filter logic functions
3. `/public/css/breeder/breeding-pets.css` - Filter styling

---

## Filter UI Implementation

### Filter Section HTML

**File:** `/views/breeder/breeding-pets.php`

Added before the pets table:

```html
<!-- Filters Section -->
<div class="filters-section">
    <div class="filters-header">
        <h3>Filters</h3>
        <button class="btn btn-outline btn-sm" onclick="resetFilters()">Reset All</button>
    </div>
    <div class="filters-grid">
        <div class="filter-group">
            <label for="filterBreed">Breed</label>
            <input 
                type="text" 
                id="filterBreed" 
                class="form-control" 
                placeholder="Search breed..." 
                onkeyup="applyFilters()">
        </div>
        
        <div class="filter-group">
            <label for="filterAge">Age Range</label>
            <select id="filterAge" class="form-control" onchange="applyFilters()">
                <option value="">All Ages</option>
                <option value="0-2">Young (0-2 years)</option>
                <option value="3-5">Adult (3-5 years)</option>
                <option value="6+">Senior (6+ years)</option>
            </select>
        </div>
        
        <div class="filter-group">
            <label for="filterStatus">Status</label>
            <select id="filterStatus" class="form-control" onchange="applyFilters()">
                <option value="">All Status</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>
        </div>
    </div>
</div>
```

### HTML Structure

| Element | ID | Type | Trigger | Purpose |
|---------|-----|------|---------|---------|
| Breed Input | `filterBreed` | text | `onkeyup` | Search by breed name |
| Age Dropdown | `filterAge` | select | `onchange` | Filter by age range |
| Status Dropdown | `filterStatus` | select | `onchange` | Filter by active/inactive |
| Reset Button | N/A | button | `onclick="resetFilters()"` | Clear all filters |

---

## JavaScript Filter Logic

### Apply Filters Main Function

**File:** `/public/js/breeder/breeding-pets.js`

```javascript
// Apply filters to the table
function applyFilters() {
    const breedFilter = document.getElementById('filterBreed')?.value.toLowerCase() || '';
    const ageFilter = document.getElementById('filterAge')?.value || '';
    const statusFilter = document.getElementById('filterStatus')?.value || '';
    
    const tableRows = document.querySelectorAll('.pets-table tbody tr[data-pet-id]');
    let visibleCount = 0;
    
    tableRows.forEach(row => {
        const petId = row.getAttribute('data-pet-id');
        const pet = breedingPetsData.find(p => p.id == petId);
        
        if (!pet) return;
        
        let matches = true;
        
        // Check breed filter (case-insensitive text search)
        if (breedFilter && !pet.breed.toLowerCase().includes(breedFilter)) {
            matches = false;
        }
        
        // Check age filter (using age range helper)
        if (ageFilter) {
            const ageMatch = applyAgeFilter(pet.age, ageFilter);
            if (!ageMatch) {
                matches = false;
            }
        }
        
        // Check status filter (Active/Inactive)
        if (statusFilter) {
            const petStatus = pet.is_active ? 'Active' : 'Inactive';
            if (petStatus !== statusFilter) {
                matches = false;
            }
        }
        
        // Show or hide row based on filters
        if (matches) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Show "no data" message if no pets match filters
    const noDataRow = document.querySelector('.pets-table tbody tr:not([data-pet-id])');
    if (visibleCount === 0 && noDataRow) {
        noDataRow.style.display = '';
    } else if (noDataRow) {
        noDataRow.style.display = 'none';
    }
}
```

### Function Flow

1. **Get Filter Values**: Extract current values from all filter inputs
2. **Select Table Rows**: Query all rows with `data-pet-id` attribute
3. **Loop Through Rows**: Check each row against active filters
4. **Apply AND Logic**: All active filters must match
5. **Update Visibility**: Show matching rows, hide others
6. **Handle Empty State**: Show "no data" message if no matches

### Breed Filter Logic

```javascript
// Check breed filter (case-insensitive text search)
if (breedFilter && !pet.breed.toLowerCase().includes(breedFilter)) {
    matches = false;
}
```

**Behavior:**
- Converts both filter and breed to lowercase
- Uses `includes()` for substring matching
- "lab" matches "Labrador", "Labrador mix", "Lab"
- Empty filter skipped (allows other filters to work)

### Age Filter Logic

```javascript
// Check age filter (using age range helper)
if (ageFilter) {
    const ageMatch = applyAgeFilter(pet.age, ageFilter);
    if (!ageMatch) {
        matches = false;
    }
}
```

Uses helper function to validate age ranges.

### Status Filter Logic

```javascript
// Check status filter (Active/Inactive)
if (statusFilter) {
    const petStatus = pet.is_active ? 'Active' : 'Inactive';
    if (petStatus !== statusFilter) {
        matches = false;
    }
}
```

**Behavior:**
- Converts boolean `is_active` to string ("Active" or "Inactive")
- Compares against selected filter value
- Empty filter skipped

### Age Filter Helper Function

**File:** `/public/js/breeder/breeding-pets.js`

```javascript
// Helper function to check age range
function applyAgeFilter(ageText, ageRange) {
    // Extract age number from "X year(s)" format
    const ageMatch = ageText?.match(/\d+/);
    if (!ageMatch) return false;
    
    const age = parseInt(ageMatch[0]);
    
    switch(ageRange) {
        case '0-2':
            return age >= 0 && age <= 2;
        case '3-5':
            return age >= 3 && age <= 5;
        case '6+':
            return age >= 6;
        default:
            return true;
    }
}
```

**How It Works:**

1. **Extract Number**: Uses regex `/\d+/` to find first number in age text
   - "2 years" → "2"
   - "4 years" → "4"
2. **Parse Integer**: Converts string to number
3. **Range Matching**: Compare against selected range

**Age Ranges:**
- Young: 0-2 years
- Adult: 3-5 years
- Senior: 6+ years

### Reset Filters Function

**File:** `/public/js/breeder/breeding-pets.js`

```javascript
// Reset all filters
function resetFilters() {
    document.getElementById('filterBreed').value = '';
    document.getElementById('filterAge').value = '';
    document.getElementById('filterStatus').value = '';
    applyFilters();
}
```

**Actions:**
1. Clear breed text input
2. Reset age dropdown to empty
3. Reset status dropdown to empty
4. Trigger `applyFilters()` to show all rows

---

## CSS Styling

### Filter Section Container

**File:** `/public/css/breeder/breeding-pets.css`

```css
/* Filters Section */
.filters-section {
    background: white;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    border: 1px solid #e5e7eb;
}
```

**Properties:**
- White background with rounded corners
- Padding for internal spacing
- Subtle shadow for depth
- Light border for definition

### Filter Header

```css
.filters-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f1f3;
}

.filters-header h3 {
    font-size: 18px;
    font-weight: 600;
    color: #1f2937;
}
```

**Layout:**
- Title on left, Reset button on right
- Bottom border separator
- Top margin for spacing from page header

### Filter Grid Layout

```css
.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
}
```

**Responsive:**
- Auto-fit columns (min 220px width)
- Stacks on mobile, spreads on desktop
- 20px gap between filter groups

### Filter Group

```css
.filter-group {
    display: flex;
    flex-direction: column;
}

.filter-group label {
    font-size: 14px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.filter-group input,
.filter-group select {
    padding: 10px 14px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
    transition: all 0.2s ease;
}

.filter-group input:focus,
.filter-group select:focus {
    outline: none;
    border-color: #f59e0b;
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
}
```

**Styling Features:**
- Column layout with label above input
- Uppercase labels for visual hierarchy
- Orange focus state with shadow glow
- Smooth transitions on interactions

### Small Button Style

```css
.btn-sm {
    padding: 8px 16px;
    font-size: 12px;
}
```

**Used for:** Reset All button styling

---

## Filter Flow & Logic

### User Interaction Diagram

```
USER ENTERS TEXT / SELECTS OPTION
          ↓
TRIGGERS EVENT (onkeyup/onchange)
          ↓
applyFilters() FUNCTION CALLED
          ↓
GET CURRENT FILTER VALUES
- Breed text (lowercase)
- Age range selection
- Status selection
          ↓
QUERY ALL ROWS WITH data-pet-id
          ↓
FOR EACH ROW:
  1. Get pet ID from data attribute
  2. Find pet in breedingPetsData array
  3. Check each filter condition
          ↓
BREED FILTER CHECK:
  if (breedFilter && !pet.breed.toLowerCase().includes(breedFilter))
    → FAILS: matches = false
          ↓
AGE FILTER CHECK:
  if (ageFilter && !applyAgeFilter(pet.age, ageFilter))
    → FAILS: matches = false
          ↓
STATUS FILTER CHECK:
  if (statusFilter && petStatus !== statusFilter)
    → FAILS: matches = false
          ↓
IF ALL PASS:
  row.style.display = ''     (SHOW ROW)
  visibleCount++
ELSE:
  row.style.display = 'none' (HIDE ROW)
          ↓
AFTER ALL ROWS CHECKED:
  if (visibleCount === 0 && noDataRow)
    → SHOW "NO DATA" MESSAGE
  else
    → HIDE "NO DATA" MESSAGE
```

### Filter Combination Examples

| Breed| Age | Status | Result |
|------|-----|--------|--------|
| "" | "" | "" | Show all pets |
| "Lab" | "" | "" | Show all "Labrador" breeds |
| "Lab" | "0-2" | "" | Show young Labradors only |
| "Lab" | "0-2" | "Active" | Show active young Labradors |
| "unknown" | "" | "" | Show "No data" message |

### Event Binding

```html
<!-- Breed Search: Trigger on keyup -->
<input 
    type="text" 
    id="filterBreed" 
    onkeyup="applyFilters()">

<!-- Age Select: Trigger on change -->
<select 
    id="filterAge" 
    onchange="applyFilters()">

<!-- Status Select: Trigger on change -->
<select 
    id="filterStatus" 
    onchange="applyFilters()">

<!-- Reset Button: Clear and re-filter -->
<button onclick="resetFilters()">Reset All</button>
```

---

## Data Dependencies

### Required Data Structure

The filter functions expect `breedingPetsData` global array with pet objects:

```javascript
const breedingPetsData = [
    {
        id: 1,
        name: "Max",
        breed: "Labrador",
        gender: "Male",
        dob: "2022-03-15",
        age: "2 years",
        photo: "/path/to/photo.jpg",
        description: "Excellent breeder",
        is_active: true
    },
    // ... more pets
];
```

### Required Table Structure

```html
<table class="pets-table">
    <tbody>
        <tr data-pet-id="1"><!-- row content --></tr>
        <tr data-pet-id="2"><!-- row content --></tr>
        <!-- ... more rows ... -->
        <tr><!-- no-data message --></tr>
    </tbody>
</table>
```

**Important:** Each data row must have `data-pet-id` attribute matching the pet ID.

---

## Performance Considerations

### Current Implementation

- **DOM Queries**: `querySelectorAll()` called once per filter change
- **Array Searching**: `find()` used for each row (O(n) complexity)
- **Total Time**: O(n²) where n = number of pets

### Optimization Tips (If Needed)

For large datasets (1000+ pets):
1. Cache DOM queries
2. Use indexed arrays for lookups
3. Implement debouncing on text input
4. Consider server-side filtering

---

## Testing Checklist

- [ ] Breed search works with partial text
- [ ] Breed search is case-insensitive
- [ ] Age range filter shows correct pets
- [ ] Status filter toggles Active/Inactive
- [ ] Multiple filters work together (AND)
- [ ] Reset button clears all filters
- [ ] "No data" message shows when no matches
- [ ] Reset button shows all rows again
- [ ] Filters work on page load
- [ ] Filters responsive on mobile
- [ ] Focus states visible on inputs

---

## Files Summary

| File | Changes |
|------|---------|
| `/views/breeder/breeding-pets.php` | Added filter section HTML before table |
| `/public/js/breeder/breeding-pets.js` | Added `applyFilters()`, `applyAgeFilter()`, `resetFilters()`, `initializeFilters()` |
| `/public/css/breeder/breeding-pets.css` | Added `.filters-section`, `.filters-header`, `.filters-grid`, `.filter-group`, `.btn-sm` styles |

---

**Last Updated:** April 15, 2026  
**Version:** 1.0
