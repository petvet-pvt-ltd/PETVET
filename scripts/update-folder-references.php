<?php
/**
 * Project Folder Name Update Script
 * This script updates all hardcoded project folder references
 * Usage: Run this script whenever you change the project folder name
 */

// Configuration
$oldFolderName = 'PETVET-MVC';
$newFolderName = 'PETVET';

// File types to search and replace in
$fileExtensions = ['php', 'html', 'js', 'css'];

// Directories to exclude from search
$excludeDirectories = ['.git', 'node_modules', 'vendor', '#old-project'];

function updateFolderReferences($directory, $oldName, $newName, $extensions, $excludeDirs) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveCallbackFilterIterator(
            new RecursiveDirectoryIterator($directory),
            function ($file, $key, $iterator) use ($excludeDirs) {
                // Skip excluded directories
                if ($iterator->hasChildren() && in_array($file->getFilename(), $excludeDirs)) {
                    return false;
                }
                return true;
            }
        )
    );

    $filesUpdated = 0;
    $totalReplacements = 0;

    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $extension = strtolower($file->getExtension());
            if (in_array($extension, $extensions)) {
                $filePath = $file->getPathname();
                $content = file_get_contents($filePath);
                
                if ($content !== false) {
                    $originalContent = $content;
                    
                    // Replace occurrences
                    $updatedContent = str_replace('/' . $oldName, '/' . $newName, $content);
                    
                    // Count replacements
                    $replacements = substr_count($content, '/' . $oldName);
                    
                    if ($updatedContent !== $originalContent && $replacements > 0) {
                        if (file_put_contents($filePath, $updatedContent)) {
                            $filesUpdated++;
                            $totalReplacements += $replacements;
                            echo "Updated: " . $filePath . " ({$replacements} replacements)\n";
                        } else {
                            echo "Error updating: " . $filePath . "\n";
                        }
                    }
                }
            }
        }
    }

    return [$filesUpdated, $totalReplacements];
}

// Run the update
echo "Starting project folder reference update...\n";
echo "Changing from: /{$oldFolderName} to: /{$newFolderName}\n";
echo "----------------------------------------\n";

$startTime = microtime(true);
[$filesUpdated, $totalReplacements] = updateFolderReferences(dirname(__DIR__), $oldFolderName, $newFolderName, $fileExtensions, $excludeDirectories);
$endTime = microtime(true);

echo "----------------------------------------\n";
echo "Update completed!\n";
echo "Files updated: {$filesUpdated}\n";
echo "Total replacements: {$totalReplacements}\n";
echo "Time taken: " . round($endTime - $startTime, 2) . " seconds\n";

// Update the config file as well
$configFile = dirname(__DIR__) . '/config/config.php';
if (file_exists($configFile)) {
    $configContent = file_get_contents($configFile);
    $updatedConfigContent = str_replace("define('PROJECT_ROOT', '/{$oldFolderName}');", "define('PROJECT_ROOT', '/{$newFolderName}');", $configContent);
    
    if ($configContent !== $updatedConfigContent) {
        if (file_put_contents($configFile, $updatedConfigContent)) {
            echo "Config file updated successfully!\n";
        } else {
            echo "Error updating config file!\n";
        }
    } else {
        echo "Config file already up to date.\n";
    }
}

echo "\nIMPORTANT: For future changes, just update the PROJECT_ROOT constant in config/config.php\n";
echo "and use the helper functions like getBaseUrl(), asset(), route(), etc. in your code.\n";
?>