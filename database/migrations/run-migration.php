<?php
// Run this file to execute the migration
require_once __DIR__ . '/../../config/connect.php';

$pdo = db();

echo "<h2>Running Database Migration</h2>";

try {
    // Step 1: Create prescription_items table
    echo "<p><strong>Step 1:</strong> Creating prescription_items table...</p>";
    $sql = "CREATE TABLE IF NOT EXISTS prescription_items (
        id INT(11) NOT NULL AUTO_INCREMENT,
        prescription_id INT(11) NOT NULL,
        medication VARCHAR(255) NOT NULL,
        dosage VARCHAR(255) NOT NULL,
        PRIMARY KEY (id),
        KEY prescription_id (prescription_id),
        FOREIGN KEY (prescription_id) REFERENCES prescriptions(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $pdo->exec($sql);
    echo "<p style='color:green;'>✓ prescription_items table created</p>";
    
    // Step 2: Create vaccination_items table
    echo "<p><strong>Step 2:</strong> Creating vaccination_items table...</p>";
    $sql = "CREATE TABLE IF NOT EXISTS vaccination_items (
        id INT(11) NOT NULL AUTO_INCREMENT,
        vaccination_id INT(11) NOT NULL,
        vaccine VARCHAR(255) NOT NULL,
        next_due DATE DEFAULT NULL,
        PRIMARY KEY (id),
        KEY vaccination_id (vaccination_id),
        FOREIGN KEY (vaccination_id) REFERENCES vaccinations(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $pdo->exec($sql);
    echo "<p style='color:green;'>✓ vaccination_items table created</p>";
    
    // Step 3: Migrate existing prescriptions data
    echo "<p><strong>Step 3:</strong> Migrating existing prescriptions data...</p>";
    $sql = "INSERT INTO prescription_items (prescription_id, medication, dosage)
            SELECT id, medication, dosage
            FROM prescriptions
            WHERE medication IS NOT NULL AND medication != ''";
    $result = $pdo->exec($sql);
    echo "<p style='color:green;'>✓ Migrated $result prescription records</p>";
    
    // Step 4: Migrate existing vaccinations data
    echo "<p><strong>Step 4:</strong> Migrating existing vaccinations data...</p>";
    $sql = "INSERT INTO vaccination_items (vaccination_id, vaccine, next_due)
            SELECT id, vaccine, next_due
            FROM vaccinations
            WHERE vaccine IS NOT NULL AND vaccine != ''";
    $result = $pdo->exec($sql);
    echo "<p style='color:green;'>✓ Migrated $result vaccination records</p>";
    
    echo "<hr>";
    echo "<h3 style='color:green;'>✅ Migration Complete!</h3>";
    
    // Verify the data
    echo "<h3>Prescription Items:</h3>";
    $items = $pdo->query("SELECT pi.*, p.appointment_id 
                          FROM prescription_items pi 
                          JOIN prescriptions p ON p.id = pi.prescription_id")->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($items);
    echo "</pre>";
    
    echo "<h3>Vaccination Items:</h3>";
    $items = $pdo->query("SELECT vi.*, v.appointment_id 
                          FROM vaccination_items vi 
                          JOIN vaccinations v ON v.id = vi.vaccination_id")->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($items);
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<p style='color:red;'><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p><pre>" . $e->getTraceAsString() . "</pre>";
}
