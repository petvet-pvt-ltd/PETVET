<?php
/**
 * Quick Folder Name Changer
 * Usage: php change-folder-name.php NEW-FOLDER-NAME
 */

if ($argc < 2) {
    echo "Usage: php change-folder-name.php NEW-FOLDER-NAME\n";
    echo "Example: php change-folder-name.php PETVET-PRODUCTION\n";
    exit(1);
}

$newFolderName = $argv[1];

// Validate folder name
if (!preg_match('/^[a-zA-Z0-9\-_]+$/', $newFolderName)) {
    echo "Error: Folder name can only contain letters, numbers, hyphens, and underscores.\n";
    exit(1);
}

// Get current folder name from config
$configFile = dirname(__DIR__) . '/config/config.php';
if (!file_exists($configFile)) {
    echo "Error: Config file not found at {$configFile}\n";
    exit(1);
}

$configContent = file_get_contents($configFile);
if (preg_match("/define\('PROJECT_ROOT', '\/(.*?)'\);/", $configContent, $matches)) {
    $currentFolderName = $matches[1];
} else {
    echo "Error: Could not find PROJECT_ROOT in config file.\n";
    exit(1);
}

echo "Changing project folder name from: {$currentFolderName}\n";
echo "                                to: {$newFolderName}\n";
echo "Are you sure? (y/N): ";

$handle = fopen("php://stdin", "r");
$line = fgets($handle);
fclose($handle);

if (trim(strtolower($line)) !== 'y') {
    echo "Operation cancelled.\n";
    exit(0);
}

// Update config file
$newConfigContent = str_replace(
    "define('PROJECT_ROOT', '/{$currentFolderName}');",
    "define('PROJECT_ROOT', '/{$newFolderName}');",
    $configContent
);

if (file_put_contents($configFile, $newConfigContent)) {
    echo "✓ Config file updated successfully!\n";
} else {
    echo "✗ Failed to update config file!\n";
    exit(1);
}

// Update the update script for next time
$updateScriptFile = __DIR__ . '/update-folder-references.php';
if (file_exists($updateScriptFile)) {
    $updateScriptContent = file_get_contents($updateScriptFile);
    $newUpdateScriptContent = preg_replace(
        "/\$oldFolderName = '[^']*';/",
        "\$oldFolderName = '{$currentFolderName}';",
        $updateScriptContent
    );
    $newUpdateScriptContent = preg_replace(
        "/\$newFolderName = '[^']*';/",
        "\$newFolderName = '{$newFolderName}';",
        $newUpdateScriptContent
    );
    
    file_put_contents($updateScriptFile, $newUpdateScriptContent);
    echo "✓ Update script prepared for next change!\n";
}

// Run the bulk update
echo "Running bulk update script...\n";
include $updateScriptFile;

echo "\n🎉 Folder name change completed successfully!\n";
echo "Your project is now configured for folder: {$newFolderName}\n";
echo "\nNext steps:\n";
echo "1. Rename your actual project folder from '{$currentFolderName}' to '{$newFolderName}'\n";
echo "2. Update your web server configuration if needed\n";
echo "3. Update any bookmarks or documentation\n";
?>