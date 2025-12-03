<?php
/**
 * Database Backup Script using mysqldump
 * Creates a backup of the Clever Cloud MySQL database
 */

// Database credentials
$host = 'btfrleeonbksuwewbmxg-mysql.services.clever-cloud.com';
$port = '3306';
$database = 'btfrleeonbksuwewbmxg';
$username = 'uzwzdfmfehmmdzpv';
$password = 'F3aS56NJLiEo2CTUkzxZ';

// Backup settings
$backupDir = __DIR__;
$backupFile = 'backup_' . date('Y-m-d_His') . '.sql';
$backupPath = $backupDir . '/' . $backupFile;

// mysqldump command (using XAMPP's mysqldump)
$mysqldump = 'C:/xampp/mysql/bin/mysqldump.exe';
$command = sprintf(
    '"%s" --host=%s --port=%s --user=%s --password=%s --databases %s --result-file=%s --single-transaction --quick --lock-tables=false --connect-timeout=60 --max_allowed_packet=512M',
    $mysqldump,
    escapeshellarg($host),
    escapeshellarg($port),
    escapeshellarg($username),
    escapeshellarg($password),
    escapeshellarg($database),
    escapeshellarg($backupPath)
);

echo "Creating database backup...\n";
echo "Database: $database\n";
echo "Backup file: $backupFile\n\n";

// Execute mysqldump
exec($command . ' 2>&1', $output, $returnCode);

if ($returnCode === 0) {
    if (file_exists($backupPath)) {
        $fileSize = filesize($backupPath);
        $fileSizeMB = round($fileSize / 1024 / 1024, 2);
        echo "✅ Backup created successfully!\n";
        echo "File: $backupPath\n";
        echo "Size: $fileSizeMB MB\n";
        
        // Create a compressed version
        $gzipPath = $backupPath . '.gz';
        if (function_exists('gzencode')) {
            $sqlContent = file_get_contents($backupPath);
            file_put_contents($gzipPath, gzencode($sqlContent, 9));
            $gzipSize = filesize($gzipPath);
            $gzipSizeMB = round($gzipSize / 1024 / 1024, 2);
            echo "✅ Compressed backup created: $gzipSizeMB MB\n";
        }
    } else {
        echo "❌ Error: Backup file was not created\n";
    }
} else {
    echo "❌ Backup failed with error code: $returnCode\n";
    if (!empty($output)) {
        echo "Error details:\n";
        foreach ($output as $line) {
            echo "  $line\n";
        }
    }
}

echo "\nBackup process completed.\n";
