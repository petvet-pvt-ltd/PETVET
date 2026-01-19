<?php
$dbPath = '["uploads\/medical-reports\/report_695e7090527765.45961073_1767796880.pdf"]';
$files = json_decode($dbPath, true);

echo "Original from DB:\n";
echo $dbPath . "\n\n";

echo "After json_decode:\n";
print_r($files);
echo "\n";

$filesWithPath = array_map(function($file) {
    return '/PETVET/' . $file;
}, $files);

echo "After adding /PETVET/:\n";
print_r($filesWithPath);
echo "\n";

$filesJson = json_encode($filesWithPath);
echo "After json_encode:\n";
echo $filesJson . "\n\n";

echo "In onclick attribute:\n";
echo "<button onclick='openFilesGallery($filesJson)'>Click</button>\n\n";

echo "Escaped for onclick:\n";
$filesJsonEscaped = htmlspecialchars($filesJson, ENT_QUOTES);
echo "<button onclick=\"openFilesGallery($filesJsonEscaped)\">Click</button>\n\n";

echo "Better approach - use data attribute:\n";
echo "<button class='btn-view-files' data-files='" . htmlspecialchars($filesJson, ENT_QUOTES) . "'>Click</button>\n";
