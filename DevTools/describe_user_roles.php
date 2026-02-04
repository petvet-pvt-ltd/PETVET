<?php
// DevTools: Describe user_roles schema
// Usage: php DevTools/describe_user_roles.php

require_once __DIR__ . '/../config/connect.php';

try {
    $pdo = db();
    echo "DB OK\n\n";

    $rows = $pdo->query('DESCRIBE user_roles')->fetchAll(PDO::FETCH_ASSOC);
    if (!$rows) {
        echo "No rows returned from DESCRIBE user_roles\n";
        exit(0);
    }

    foreach ($rows as $r) {
        $field = $r['Field'] ?? '';
        $type = $r['Type'] ?? '';
        $null = $r['Null'] ?? '';
        $default = array_key_exists('Default', $r) ? ($r['Default'] === null ? 'NULL' : (string)$r['Default']) : 'NULL';
        $key = $r['Key'] ?? '';
        echo $field . "\t" . $type . "\t" . $null . "\t" . $default . "\t" . $key . "\n";
    }
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
