<?php
require_once __DIR__ . '/../config/connect.php';

try {
    $pdo = db();
    
    echo "Checking for new tables...\n\n";
    
    // Check prescription_items
    $stmt = $pdo->query("SHOW TABLES LIKE 'prescription_items'");
    if ($stmt->rowCount() > 0) {
        echo "✅ prescription_items table exists\n";
        $count = $pdo->query("SELECT COUNT(*) FROM prescription_items")->fetchColumn();
        echo "   Found $count medication records\n\n";
    } else {
        echo "❌ prescription_items table NOT found\n\n";
    }
    
    // Check vaccination_items
    $stmt = $pdo->query("SHOW TABLES LIKE 'vaccination_items'");
    if ($stmt->rowCount() > 0) {
        echo "✅ vaccination_items table exists\n";
        $count = $pdo->query("SELECT COUNT(*) FROM vaccination_items")->fetchColumn();
        echo "   Found $count vaccine records\n\n";
    } else {
        echo "❌ vaccination_items table NOT found\n\n";
    }
    
    echo "Sample data from prescription_items:\n";
    $stmt = $pdo->query("SELECT * FROM prescription_items LIMIT 3");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($items) {
        foreach ($items as $item) {
            echo "  ID {$item['id']}: {$item['medication']} - {$item['dosage']}\n";
        }
    } else {
        echo "  No data yet\n";
    }
    
    echo "\nSample data from vaccination_items:\n";
    $stmt = $pdo->query("SELECT * FROM vaccination_items LIMIT 3");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($items) {
        foreach ($items as $item) {
            echo "  ID {$item['id']}: {$item['vaccine']} - Next Due: " . ($item['next_due'] ?? 'N/A') . "\n";
        }
    } else {
        echo "  No data yet\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "\nYou need to run the migration!\n";
    echo "Run: php database/migrations/run-migration.php\n";
}
