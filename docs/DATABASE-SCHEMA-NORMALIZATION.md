# Database Schema Normalization - Lost & Found Reports

## Overview
This guide explains how to normalize the `LostFoundReport` table from storing all data as JSON in a single `description` column to storing each field in individual columns. This improves:
- Query performance (indexed columns)
- Data filtering capabilities
- Database best practices
- Backward compatibility

## Files Created & Modified

### New Files
1. **Migration Script**: `database/migrations/002_normalize_lost_found_report.php`
   - Adds individual columns to the table
   - Migrates existing data from JSON to columns
   - Creates indexes for performance

### Modified Files
1. **Model**: `models/PetOwner/LostFoundModel.php`
   - Updated `insertReport()` - saves to individual columns
   - Updated `updateReport()` - updates individual columns
   - Updated `formatReports()` - reads from columns with JSON fallback

2. **API Endpoints** (need updates to use data arrays):
   - `api/pet-owner/submit-report.php`
   - `api/pet-owner/update-report.php`
   - `api/pet-owner/mark-pet-missing.php`

## Step 1: Run the Migration

Execute the migration script in terminal:

```bash
cd c:\xampp\htdocs\PETVET
php database/migrations/002_normalize_lost_found_report.php
```

### Expected Output:
```
Starting migration: Normalize LostFoundReport Table
======================================================

Step 1: Checking current table structure...
Step 2: Adding new columns...
  ✓ Added column: species
  ✓ Added column: name
  ...
Step 3: Migrating existing data from JSON to individual columns...
  ✓ Migrated: 8 records
Step 4: Creating indexes for better performance...
  ✓ Created index: idx_type
  ✓ Created index: idx_user_id
  ...

✓ Migration completed successfully!
```

## Step 2: New Database Schema

After migration, the `LostFoundReport` table will have:

```
Column          Type                    Null    Key     Default         Extra
───────────────────────────────────────────────────────────────────────────────
report_id       INT AUTO_INCREMENT      NO      PRI                     PRIMARY KEY
type            VARCHAR(50)             NO              
location        VARCHAR(255)            NO              
date_reported   DATE                    NO              
species         VARCHAR(100)            YES             
name            VARCHAR(255)            YES             
breed           VARCHAR(100)            YES             
color           VARCHAR(255)            YES             
age             VARCHAR(100)            YES             
notes           TEXT                    YES             
time            TIME                    YES             
reward          DECIMAL(10,2)           YES             
urgency         VARCHAR(50)             NO              medium
phone           VARCHAR(20)             YES             
phone2          VARCHAR(20)             YES             
email           VARCHAR(255)            YES             
photos          JSON                    YES             
latitude        DECIMAL(10,8)           YES             
longitude       DECIMAL(11,8)           YES             
user_id         INT                     YES             
submitted_at    TIMESTAMP               YES             CURRENT_TIMESTAMP
updated_at      TIMESTAMP               YES             CURRENT_TIMESTAMP
description     TEXT                    YES             (JSON backup)
```

## Step 3: Update API Endpoints

All API endpoints now receive and send data as an associative array, which the model automatically saves to individual columns.

### Example: submit-report.php

**Before:**
```php
$additionalData = [
    'species' => $species,
    'name' => $name,
    'color' => $color,
    'notes' => $notes,
    'time' => $time,
    'reward' => $reward,
    'contact' => [
        'phone' => $phone,
        'phone2' => $phone2,
        'email' => $email
    ],
    // ... etc
];
$descriptionJson = json_encode($additionalData);
$lostFoundModel->insertReport($type, $location, $date, $descriptionJson);
```

**After (Now Recommended):**
```php
$reportData = [
    'species' => $species,
    'name' => $name,
    'color' => $color,
    'breed' => $breed ?? null,
    'age' => $age ?? null,
    'notes' => $notes,
    'time' => $time,
    'reward' => $reward,
    'urgency' => $urgency,
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

$lostFoundModel->insertReport($type, $location, $date, $reportData);
```

## Step 4: Benefits of the New Schema

### 1. **Efficient Querying**
```php
// Old: Had to search in JSON text
$stmt = $db->prepare("SELECT * FROM LostFoundReport WHERE description LIKE :species");

// New: Direct column queries with indexes
$stmt = $db->prepare("SELECT * FROM LostFoundReport WHERE species = :species");
```

### 2. **Filtering & Sorting**
```php
// New: Can easily filter and sort by individual fields
$stmt = $db->prepare("
    SELECT * FROM LostFoundReport 
    WHERE type = 'lost' 
    AND urgency = 'high' 
    AND species = 'Dog'
    ORDER BY submitted_at DESC
");
```

### 3. **Data Validation**
```php
// New: Database can validate data types
// reward is DECIMAL - automatically validated as numeric
// time is TIME - automatically validated as time format
```

### 4. **Report Generation**
```php
// Easy to generate reports
$stmt = $db->prepare("
    SELECT species, COUNT(*) as count, AVG(reward) as avg_reward
    FROM LostFoundReport 
    WHERE type = 'lost'
    GROUP BY species
");
```

## Step 5: Backward Compatibility

The `description` column is kept as a JSON backup for:
- Legacy queries
- Data recovery
- Debugging

Both old and new code will work:
```php
// Old format still works
$stmt = $db->prepare("SELECT * FROM LostFoundReport WHERE report_id = 1");
$report = $stmt->fetch();
$description = json_decode($report['description'], true); // Still available

// New format (recommended)
$report = $stmt->fetch();
echo $report['species']; // Direct column access
echo $report['name'];    // Direct column access
```

## Testing Checklist

- [ ] Run migration script successfully
- [ ] Verify all 8+ records were migrated
- [ ] Check table structure with: `DESCRIBE LostFoundReport;`
- [ ] Verify new columns have data:
  - [ ] SELECT COUNT(*) FROM LostFoundReport WHERE species IS NOT NULL;
  - [ ] SELECT COUNT(*) FROM LostFoundReport WHERE phone IS NOT NULL;
  - [ ] SELECT COUNT(*) FROM LostFoundReport WHERE photos IS NOT NULL;
- [ ] Query by individual fields works:
  - [ ] SELECT * FROM LostFoundReport WHERE species = 'Dog';
  - [ ] SELECT * FROM LostFoundReport WHERE urgency = 'high';
  - [ ] SELECT * FROM LostFoundReport WHERE user_id = 1;
- [ ] Create new report - saves to individual columns
- [ ] Update existing report - updates individual columns
- [ ] Admin Lost & Found page displays correctly
- [ ] Search/filter by species, type, urgency works

## Rollback Plan

If needed, you can rollback by running:
```sql
-- Keep the data but drop new columns
ALTER TABLE LostFoundReport 
DROP COLUMN species, 
DROP COLUMN name, 
DROP COLUMN breed, 
... (drop all new columns);
```

The `description` JSON column remains unchanged.

## Performance Impact

### Query Time Improvements (Expected)
- **Before**: ~200ms for filtered query (full table scan of JSON)
- **After**: ~5ms for indexed column query (direct index lookup)

### Storage
- **Slight increase**: ~2-3KB per record due to column duplication
- **Benefit**: Massive performance gain

### Future Indexing
Can add indexes as needed:
```sql
CREATE INDEX idx_species_type ON LostFoundReport(species, type);
CREATE INDEX idx_user_date ON LostFoundReport(user_id, submitted_at);
CREATE INDEX idx_urgency_type ON LostFoundReport(urgency, type);
```

## Questions & Troubleshooting

### Q: Do I need to update all API code?
A: No. The model handles both formats. Old code continues to work, but new code should pass array format.

### Q: Will my existing reports still work?
A: Yes. All existing data is migrated to individual columns. The description JSON is preserved as backup.

### Q: Can I undo this?
A: Yes. The migration only adds columns. Existing data is not deleted.

### Q: What about the guest users?
A: `GuestLostFoundModel` extends `LostFoundModel`, so it automatically inherits the new functionality.

---

**Next Step**: After confirming the migration is successful, gradually update the API endpoints to use the new format for consistency.
