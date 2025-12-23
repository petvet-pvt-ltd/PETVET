<?php
require_once 'config/connect.php';
try {
    $pdo = db();
    $stmt = $pdo->query('DESCRIBE products');
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo $col['Field'] . " (" . $col['Type'] . ")\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
