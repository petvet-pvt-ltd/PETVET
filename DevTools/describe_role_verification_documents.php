<?php
// DevTools: Describe role_verification_documents schema
// Usage: php DevTools/describe_role_verification_documents.php

require_once __DIR__ . '/../config/connect.php';

try {
    $pdo = db();
    echo "DB OK\n\n";

    $rows = $pdo->query('DESCRIBE role_verification_documents')->fetchAll(PDO::FETCH_ASSOC);
    if (!$rows) {
        echo "No rows returned from DESCRIBE role_verification_documents\n";
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
