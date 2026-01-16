# Medical Data Deletion Scripts

This directory contains scripts to safely delete medical data from the PetVet database.

## ⚠️ CRITICAL WARNING
**These scripts permanently delete data. ALWAYS create a backup before running any deletion script!**

## 📁 Available Scripts

### 1. `analyze-database-structure.php`
**Purpose:** Analyzes the database structure to understand table relationships and dependencies.

**Usage:**
```bash
php DevTools/analyze-database-structure.php
```

**What it does:**
- Shows table structures for appointments, medical_records, prescriptions, and vaccinations
- Displays foreign key constraints
- Shows current record counts
- Provides recommended deletion order

**When to use:** Run this FIRST before any deletion to understand what will be deleted.

---

### 2. `delete-medical-records.php`
**Purpose:** Deletes all medical records (medical_records table)

**Usage:**
```bash
php DevTools/delete-medical-records.php
```

**What it deletes:**
- All records from `medical_records` table

**Dependencies:** None (medical_records has no child tables)

---

### 3. `delete-prescriptions.php`
**Purpose:** Deletes all prescription records including child items

**Usage:**
```bash
php DevTools/delete-prescriptions.php
```

**What it deletes:**
- All records from `prescription_items` table (if exists)
- All records from `prescriptions` table

**Dependencies:** None (prescriptions has no dependent parent tables to worry about)

---

### 4. `delete-vaccinations.php`
**Purpose:** Deletes all vaccination records including child items

**Usage:**
```bash
php DevTools/delete-vaccinations.php
```

**What it deletes:**
- All records from `vaccination_items` table (if exists)
- All records from `vaccinations` table

**Dependencies:** None (vaccinations has no dependent parent tables to worry about)

---

### 5. `delete-appointments.php`
**Purpose:** Deletes all appointment records

**Usage:**
```bash
php DevTools/delete-appointments.php
```

**What it deletes:**
- All records from `appointments` table

**Dependencies:** 
⚠️ **IMPORTANT:** This script will FAIL if there are any medical_records, prescriptions, or vaccinations still in the database, as they reference appointments via foreign keys.

**Requirements:**
- Must run scripts 2, 3, and 4 FIRST (or use the master script)

---

### 6. `delete-all-medical-data.php` ⭐ (RECOMMENDED)
**Purpose:** Master script that deletes ALL medical data in the correct order

**Usage:**
```bash
php DevTools/delete-all-medical-data.php
```

**What it deletes (in order):**
1. Medical Records (`medical_records`)
2. Prescriptions (`prescriptions` + `prescription_items`)
3. Vaccinations (`vaccinations` + `vaccination_items`)
4. Appointments (`appointments`)

**Why use this:**
- ✅ Automatically handles correct deletion order
- ✅ Single transaction (all-or-nothing)
- ✅ Comprehensive error handling
- ✅ Detailed progress reporting
- ✅ Automatic verification
- ✅ No foreign key constraint issues

**This is the SAFEST and EASIEST way to delete all medical data.**

---

## 🔄 Recommended Workflow

### Option A: Use Master Script (Recommended)
```bash
# Step 1: Analyze database
php DevTools/analyze-database-structure.php

# Step 2: Create backup
php create-db-backup.php "Before deleting all medical data"

# Step 3: Run master deletion script
php DevTools/delete-all-medical-data.php
```

### Option B: Manual Step-by-Step
```bash
# Step 1: Analyze database
php DevTools/analyze-database-structure.php

# Step 2: Create backup
php create-db-backup.php "Before deleting medical data"

# Step 3: Delete in correct order
php DevTools/delete-medical-records.php
php DevTools/delete-prescriptions.php
php DevTools/delete-vaccinations.php
php DevTools/delete-appointments.php
```

---

## 🛡️ Safety Features

All scripts include:

✅ **Transaction Support**
- All deletions occur within a database transaction
- If ANY error occurs, ALL changes are rolled back
- Database remains unchanged on failure

✅ **Comprehensive Error Handling**
- PDO exceptions caught and handled
- Detailed error messages
- Error logging to log files

✅ **Pre-Deletion Checks**
- Shows what will be deleted before deletion
- Checks for dependent records
- Prevents deletion if dependencies exist (for individual scripts)

✅ **Post-Deletion Verification**
- Confirms deletion was successful
- Shows remaining record counts
- Detailed operation logs

✅ **Foreign Key Awareness**
- Respects foreign key constraints
- Deletes in correct order
- Handles child tables automatically

---

## 📊 Database Relationships

```
appointments (parent)
    ├── medical_records (child - references appointment_id)
    ├── prescriptions (child - references appointment_id)
    │   └── prescription_items (grandchild - references prescription_id)
    └── vaccinations (child - references appointment_id)
        └── vaccination_items (grandchild - references vaccination_id)
```

**Deletion Order:** Always delete children before parents!

---

## 📝 Log Files

All scripts log to:
- `logs/delete-appointments-errors.log`
- `logs/delete-vaccinations-errors.log`
- `logs/delete-medical-records-errors.log`
- `logs/delete-prescriptions-errors.log`
- `logs/delete-all-medical-data-errors.log`

---

## ⚡ Quick Reference

| Script | Deletes | Child Tables | Safe Alone? |
|--------|---------|--------------|-------------|
| delete-medical-records.php | medical_records | None | ✅ Yes |
| delete-prescriptions.php | prescriptions | prescription_items | ✅ Yes |
| delete-vaccinations.php | vaccinations | vaccination_items | ✅ Yes |
| delete-appointments.php | appointments | None | ❌ No - must delete children first |
| delete-all-medical-data.php | ALL | ALL | ✅ Yes - handles everything |

---

## 🚨 Common Issues

### Issue: "Cannot delete appointments" error
**Cause:** There are still medical_records, prescriptions, or vaccinations in the database.

**Solution:** Either:
1. Use the master script `delete-all-medical-data.php` (recommended)
2. Delete child records first manually

### Issue: Transaction timeout
**Cause:** Too many records to delete at once.

**Solution:** This shouldn't happen with current data volumes, but if it does, the scripts use prepared statements which are efficient.

---

## 📞 Support

If you encounter any issues:
1. Check the log files in `logs/` directory
2. Run `analyze-database-structure.php` to check current state
3. Ensure you have proper database permissions
4. Verify database connection in `config/connect.php`

---

## 🎯 Best Practices

1. **Always backup first** - Run `create-db-backup.php` before ANY deletion
2. **Test in development first** - Don't run on production without testing
3. **Use the master script** - It's safer and easier than individual scripts
4. **Check the analysis** - Run `analyze-database-structure.php` first
5. **Review the output** - Each script shows what it's doing
6. **Keep logs** - The log files track all operations

---

**Created:** 2026-01-16  
**Author:** GitHub Copilot  
**Database:** TiDB (petvetDB)
