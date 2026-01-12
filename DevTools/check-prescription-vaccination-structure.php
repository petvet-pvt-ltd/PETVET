<?php
require_once __DIR__ . '/../config/connect.php';

$pdo = db();

echo "<h2>Current Table Structures</h2>";

echo "<h3>Prescriptions Table:</h3>";
$cols = $pdo->query("DESCRIBE prescriptions")->fetchAll(PDO::FETCH_ASSOC);
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
foreach ($cols as $col) {
    echo "<tr>";
    echo "<td>{$col['Field']}</td>";
    echo "<td>{$col['Type']}</td>";
    echo "<td>{$col['Null']}</td>";
    echo "<td>{$col['Key']}</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h3>Sample Prescription Data:</h3>";
$sample = $pdo->query("SELECT * FROM prescriptions LIMIT 2")->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($sample);
echo "</pre>";

echo "<hr>";

echo "<h3>Vaccinations Table:</h3>";
$cols = $pdo->query("DESCRIBE vaccinations")->fetchAll(PDO::FETCH_ASSOC);
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
foreach ($cols as $col) {
    echo "<tr>";
    echo "<td>{$col['Field']}</td>";
    echo "<td>{$col['Type']}</td>";
    echo "<td>{$col['Null']}</td>";
    echo "<td>{$col['Key']}</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h3>Sample Vaccination Data:</h3>";
$sample = $pdo->query("SELECT * FROM vaccinations LIMIT 2")->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($sample);
echo "</pre>";

echo "<hr>";
echo "<h2>Recommended Table Structure Changes</h2>";

echo "<h3>Option 1: Keep Current Structure (Store as JSON)</h3>";
echo "<p><strong>Prescriptions table remains the same, but we store multiple medicines in JSON format:</strong></p>";
echo "<pre>
CREATE TABLE prescriptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    appointment_id INT NOT NULL,
    medications JSON,  -- Change from single medication/dosage to JSON array
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reports TEXT
);

Example JSON format:
{
    \"medications\": [
        {\"medication\": \"Amoxicillin\", \"dosage\": \"500mg twice daily\"},
        {\"medication\": \"Paracetamol\", \"dosage\": \"250mg as needed\"}
    ]
}
</pre>";

echo "<h3>Option 2: Create Separate Tables (Normalized)</h3>";
echo "<p><strong>Create new tables for individual prescription items and vaccination items:</strong></p>";
echo "<pre>
-- Keep prescriptions table for header info
CREATE TABLE prescriptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    appointment_id INT NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reports TEXT
);

-- New table for individual medications
CREATE TABLE prescription_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    prescription_id INT NOT NULL,
    medication VARCHAR(255) NOT NULL,
    dosage VARCHAR(255) NOT NULL,
    FOREIGN KEY (prescription_id) REFERENCES prescriptions(id) ON DELETE CASCADE
);

-- Keep vaccinations table for header info
CREATE TABLE vaccinations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    appointment_id INT NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reports TEXT
);

-- New table for individual vaccines
CREATE TABLE vaccination_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    vaccination_id INT NOT NULL,
    vaccine VARCHAR(255) NOT NULL,
    next_due DATE,
    FOREIGN KEY (vaccination_id) REFERENCES vaccinations(id) ON DELETE CASCADE
);
</pre>";

echo "<h3>Option 3: Simple Approach - Store as Delimited String (Quick Fix)</h3>";
echo "<p><strong>Modify existing columns to store multiple values separated by delimiters:</strong></p>";
echo "<pre>
ALTER TABLE prescriptions 
    MODIFY COLUMN medication TEXT,  -- Store as \"Med1||Dosage1;;Med2||Dosage2\"
    MODIFY COLUMN dosage TEXT;       -- Or keep as JSON string

ALTER TABLE vaccinations
    MODIFY COLUMN vaccine TEXT,      -- Store as \"Vaccine1||Date1;;Vaccine2||Date2\"
    MODIFY COLUMN next_due TEXT;     -- Or keep as JSON string
</pre>";

echo "<hr>";
echo "<h3>My Recommendation:</h3>";
echo "<p><strong>Option 2 (Normalized Tables)</strong> is the best practice because:</p>";
echo "<ul>";
echo "<li>✅ Clean database design</li>";
echo "<li>✅ Easy to query individual medications/vaccines</li>";
echo "<li>✅ Easy to validate and maintain</li>";
echo "<li>✅ Can add more fields per medication/vaccine later</li>";
echo "<li>✅ Proper foreign key constraints</li>";
echo "</ul>";
