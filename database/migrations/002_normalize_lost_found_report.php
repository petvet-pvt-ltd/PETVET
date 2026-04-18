<?php
/**
 * Migration: Normalize LostFoundReport Table
 * Converts from JSON storage to individual columns for better querying and indexing
 * 
 * Run: php 002_normalize_lost_found_report.php
 */

require_once __DIR__ . '/../../config/connect.php';

try {
    $db = db();
    
    echo "Starting migration: Normalize LostFoundReport Table\n";
    echo "======================================================\n\n";
    
    // Step 1: Check if new columns already exist
    echo "Step 1: Checking current table structure...\n";
    $checkStmt = $db->query("DESCRIBE LostFoundReport");
    $columns = $checkStmt->fetchAll(PDO::FETCH_ASSOC);
    $columnNames = array_column($columns, 'Field');
    
    $newColumnsNeeded = ['species', 'name', 'color', 'breed', 'age', 'notes', 'time', 'reward', 'urgency', 'phone', 'phone2', 'email', 'photos', 'latitude', 'longitude', 'user_id', 'submitted_at', 'updated_at'];
    $columnsToAdd = array_diff($newColumnsNeeded, $columnNames);
    
    if (empty($columnsToAdd)) {
        echo "✓ All new columns already exist. Skipping column creation.\n\n";
    } else {
        // Step 2: Add new columns
        echo "Step 2: Adding new columns...\n";
        $alterStatements = [
            "ALTER TABLE LostFoundReport ADD COLUMN species VARCHAR(100) AFTER location",
            "ALTER TABLE LostFoundReport ADD COLUMN name VARCHAR(255) AFTER species",
            "ALTER TABLE LostFoundReport ADD COLUMN breed VARCHAR(100) AFTER name",
            "ALTER TABLE LostFoundReport ADD COLUMN color VARCHAR(255) AFTER breed",
            "ALTER TABLE LostFoundReport ADD COLUMN age VARCHAR(100) AFTER color",
            "ALTER TABLE LostFoundReport ADD COLUMN notes TEXT AFTER age",
            "ALTER TABLE LostFoundReport ADD COLUMN time TIME AFTER notes",
            "ALTER TABLE LostFoundReport ADD COLUMN reward DECIMAL(10, 2) AFTER time",
            "ALTER TABLE LostFoundReport ADD COLUMN urgency VARCHAR(50) DEFAULT 'medium' AFTER reward",
            "ALTER TABLE LostFoundReport ADD COLUMN phone VARCHAR(20) AFTER urgency",
            "ALTER TABLE LostFoundReport ADD COLUMN phone2 VARCHAR(20) AFTER phone",
            "ALTER TABLE LostFoundReport ADD COLUMN email VARCHAR(255) AFTER phone2",
            "ALTER TABLE LostFoundReport ADD COLUMN photos JSON AFTER email",
            "ALTER TABLE LostFoundReport ADD COLUMN latitude DECIMAL(10, 8) AFTER photos",
            "ALTER TABLE LostFoundReport ADD COLUMN longitude DECIMAL(11, 8) AFTER latitude",
            "ALTER TABLE LostFoundReport ADD COLUMN user_id INT AFTER longitude",
            "ALTER TABLE LostFoundReport ADD COLUMN submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER user_id",
            "ALTER TABLE LostFoundReport ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER submitted_at"
        ];
        
        foreach ($alterStatements as $sql) {
            if (strpos(strtolower($sql), 'add column') !== false) {
                $columnName = preg_match('/ADD COLUMN (\w+)/', $sql, $matches) ? $matches[1] : 'unknown';
                if (in_array($columnName, $columnsToAdd)) {
                    $db->exec($sql);
                    echo "  ✓ Added column: $columnName\n";
                }
            }
        }
        echo "\n";
    }
    
    // Step 3: Migrate data from JSON to individual columns
    echo "Step 3: Migrating existing data from JSON to individual columns...\n";
    
    $selectStmt = $db->query("SELECT report_id, description FROM LostFoundReport WHERE description IS NOT NULL AND description != ''");
    $reports = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
    
    $migratedCount = 0;
    $skippedCount = 0;
    
    foreach ($reports as $report) {
        $description = json_decode($report['description'], true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "  ⚠ Skipping report {$report['report_id']}: Invalid JSON\n";
            $skippedCount++;
            continue;
        }
        
        // Extract fields from JSON with proper validation
        $species = isset($description['species']) ? $description['species'] : null;
        $name = isset($description['name']) ? $description['name'] : null;
        $breed = isset($description['breed']) ? $description['breed'] : null;
        $color = isset($description['color']) ? $description['color'] : null;
        $age = isset($description['age']) ? $description['age'] : null;
        $notes = isset($description['notes']) ? $description['notes'] : null;
        
        // Handle time field - validate HH:MM:SS format
        $time = null;
        if (isset($description['time']) && !empty($description['time'])) {
            $timeStr = $description['time'];
            // Validate it's in TIME format (HH:MM or HH:MM:SS)
            if (preg_match('/^([0-1][0-9]|2[0-3]):([0-5][0-9])(?::([0-5][0-9]))?$/', $timeStr)) {
                $time = $timeStr;
            }
        }
        
        $reward = isset($description['reward']) && !empty($description['reward']) ? (float)$description['reward'] : null;
        $urgency = isset($description['urgency']) ? $description['urgency'] : 'medium';
        $phone = isset($description['contact']['phone']) ? $description['contact']['phone'] : null;
        $phone2 = isset($description['contact']['phone2']) ? $description['contact']['phone2'] : null;
        $email = isset($description['contact']['email']) ? $description['contact']['email'] : null;
        $photos = !empty($description['photos']) ? json_encode($description['photos']) : null;
        $latitude = isset($description['latitude']) && !empty($description['latitude']) ? (float)$description['latitude'] : null;
        $longitude = isset($description['longitude']) && !empty($description['longitude']) ? (float)$description['longitude'] : null;
        $user_id = isset($description['user_id']) ? (int)$description['user_id'] : null;
        
        // Handle submitted_at datetime - validate format
        $submitted_at = date('Y-m-d H:i:s');
        if (isset($description['submitted_at']) && !empty($description['submitted_at'])) {
            $timestamp = strtotime($description['submitted_at']);
            if ($timestamp !== false) {
                $submitted_at = date('Y-m-d H:i:s', $timestamp);
            }
        }
        
        // Update the row with extracted data
        $updateStmt = $db->prepare("
            UPDATE LostFoundReport 
            SET species = :species,
                name = :name,
                breed = :breed,
                color = :color,
                age = :age,
                notes = :notes,
                time = :time,
                reward = :reward,
                urgency = :urgency,
                phone = :phone,
                phone2 = :phone2,
                email = :email,
                photos = :photos,
                latitude = :latitude,
                longitude = :longitude,
                user_id = :user_id,
                submitted_at = :submitted_at
            WHERE report_id = :report_id
        ");
        
        $updateStmt->execute([
            ':species' => $species,
            ':name' => $name,
            ':breed' => $breed,
            ':color' => $color,
            ':age' => $age,
            ':notes' => $notes,
            ':time' => $time,
            ':reward' => $reward,
            ':urgency' => $urgency,
            ':phone' => $phone,
            ':phone2' => $phone2,
            ':email' => $email,
            ':photos' => $photos,
            ':latitude' => $latitude,
            ':longitude' => $longitude,
            ':user_id' => $user_id,
            ':submitted_at' => $submitted_at,
            ':report_id' => $report['report_id']
        ]);
        
        $migratedCount++;
    }
    
    echo "  ✓ Migrated: $migratedCount records\n";
    if ($skippedCount > 0) {
        echo "  ⚠ Skipped: $skippedCount records (invalid data)\n";
    }
    echo "\n";
    
    // Step 4: Create indexes for better performance
    echo "Step 4: Creating indexes for better performance...\n";
    $indexStatements = [
        "CREATE INDEX idx_type ON LostFoundReport(type)",
        "CREATE INDEX idx_user_id ON LostFoundReport(user_id)",
        "CREATE INDEX idx_species ON LostFoundReport(species)",
        "CREATE INDEX idx_date_reported ON LostFoundReport(date_reported)",
        "CREATE INDEX idx_urgency ON LostFoundReport(urgency)"
    ];
    
    foreach ($indexStatements as $sql) {
        try {
            $db->exec($sql);
            $indexName = preg_match('/INDEX (\w+)/', $sql, $matches) ? $matches[1] : 'unknown';
            echo "  ✓ Created index: $indexName\n";
        } catch (PDOException $e) {
            // Index might already exist, that's okay
            if (strpos($e->getMessage(), 'Duplicate') !== false) {
                $indexName = preg_match('/INDEX (\w+)/', $sql, $matches) ? $matches[1] : 'unknown';
                echo "  ℹ Index already exists: $indexName\n";
            } else {
                throw $e;
            }
        }
    }
    echo "\n";
    
    echo "✓ Migration completed successfully!\n";
    echo "\nNew table structure:\n";
    $describeStmt = $db->query("DESCRIBE LostFoundReport");
    $structure = $describeStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "┌─────────────────────┬──────────────────────────┬──────┬─────┬─────────┬────────────┐\n";
    echo "│ Field               │ Type                     │ Null │ Key │ Default │ Extra      │\n";
    echo "├─────────────────────┼──────────────────────────┼──────┼─────┼─────────┼────────────┤\n";
    foreach ($structure as $col) {
        $field = str_pad($col['Field'], 19);
        $type = str_pad($col['Type'], 26);
        $null = str_pad($col['Null'], 4);
        $key = str_pad($col['Key'], 3);
        $default = str_pad($col['Default'] ?? 'NULL', 7);
        $extra = $col['Extra'] ?? '';
        echo "│ $field │ $type │ $null │ $key │ $default │ $extra\n";
    }
    echo "└─────────────────────┴──────────────────────────┴──────┴─────┴─────────┴────────────┘\n";
    
} catch (PDOException $e) {
    echo "✗ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
