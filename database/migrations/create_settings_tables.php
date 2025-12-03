<?php
/**
 * Migration: Create Settings Tables
 * Adds tables for clinic preferences, weekly schedule, and blocked days
 */

require_once __DIR__ . '/../../config/connect.php';

try {
    $pdo = db();
    $pdo->beginTransaction();
    
    echo "=== CREATING SETTINGS TABLES ===\n\n";
    
    // 1. Add missing columns to clinics table
    echo "1. Updating clinics table...\n";
    $columns = $pdo->query("SHOW COLUMNS FROM clinics")->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('clinic_description', $columns)) {
        $pdo->exec("ALTER TABLE clinics ADD COLUMN clinic_description TEXT AFTER clinic_name");
        echo "   ✅ Added clinic_description column\n";
    }
    if (!in_array('clinic_logo', $columns)) {
        $pdo->exec("ALTER TABLE clinics ADD COLUMN clinic_logo VARCHAR(500) AFTER clinic_description");
        echo "   ✅ Added clinic_logo column\n";
    }
    if (!in_array('clinic_cover', $columns)) {
        $pdo->exec("ALTER TABLE clinics ADD COLUMN clinic_cover VARCHAR(500) AFTER clinic_logo");
        echo "   ✅ Added clinic_cover column\n";
    }
    if (!in_array('map_location', $columns)) {
        $pdo->exec("ALTER TABLE clinics ADD COLUMN map_location VARCHAR(255) AFTER clinic_address");
        echo "   ✅ Added map_location column\n";
    }
    echo "   Clinics table updated\n\n";
    
    // 2. Create clinic_preferences table
    echo "2. Creating clinic_preferences table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS clinic_preferences (
            id INT AUTO_INCREMENT PRIMARY KEY,
            clinic_id INT NOT NULL,
            email_notifications TINYINT(1) DEFAULT 1,
            slot_duration_minutes INT DEFAULT 20,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE CASCADE,
            UNIQUE KEY unique_clinic (clinic_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "   ✅ clinic_preferences table created\n\n";
    
    // 3. Create clinic_weekly_schedule table
    echo "3. Creating clinic_weekly_schedule table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS clinic_weekly_schedule (
            id INT AUTO_INCREMENT PRIMARY KEY,
            clinic_id INT NOT NULL,
            day_of_week ENUM('monday','tuesday','wednesday','thursday','friday','saturday','sunday') NOT NULL,
            is_enabled TINYINT(1) DEFAULT 1,
            start_time TIME NOT NULL,
            end_time TIME NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE CASCADE,
            UNIQUE KEY unique_clinic_day (clinic_id, day_of_week)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "   ✅ clinic_weekly_schedule table created\n\n";
    
    // 4. Create clinic_blocked_days table
    echo "4. Creating clinic_blocked_days table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS clinic_blocked_days (
            id INT AUTO_INCREMENT PRIMARY KEY,
            clinic_id INT NOT NULL,
            blocked_date DATE NOT NULL,
            reason VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE CASCADE,
            UNIQUE KEY unique_clinic_date (clinic_id, blocked_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "   ✅ clinic_blocked_days table created\n\n";
    
    // 5. Insert default preferences for existing clinics
    echo "5. Inserting default preferences for existing clinics...\n";
    $clinics = $pdo->query("SELECT id FROM clinics")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($clinics as $clinicId) {
        $pdo->exec("
            INSERT IGNORE INTO clinic_preferences (clinic_id, email_notifications, slot_duration_minutes)
            VALUES ($clinicId, 1, 20)
        ");
    }
    echo "   ✅ Default preferences inserted for " . count($clinics) . " clinics\n\n";
    
    // 6. Insert default weekly schedule for existing clinics
    echo "6. Inserting default weekly schedule for existing clinics...\n";
    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    foreach ($clinics as $clinicId) {
        foreach ($days as $day) {
            $isEnabled = ($day === 'sunday') ? 0 : 1;
            $startTime = ($day === 'saturday') ? '10:00:00' : '09:00:00';
            $endTime = ($day === 'saturday') ? '14:00:00' : (($day === 'sunday') ? '13:00:00' : '17:00:00');
            
            $pdo->exec("
                INSERT IGNORE INTO clinic_weekly_schedule 
                (clinic_id, day_of_week, is_enabled, start_time, end_time)
                VALUES ($clinicId, '$day', $isEnabled, '$startTime', '$endTime')
            ");
        }
    }
    echo "   ✅ Default weekly schedule inserted for " . count($clinics) . " clinics\n\n";
    
    $pdo->commit();
    echo "=== MIGRATION COMPLETED SUCCESSFULLY ===\n";
    
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>
