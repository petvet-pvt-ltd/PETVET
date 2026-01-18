<?php
echo "<h2>Testing Clinic Registration - Latitude/Longitude Fix</h2>";
echo "<style>
    body { font-family: Arial; padding: 20px; background: #f5f5f5; }
    .success { color: #10b981; font-weight: bold; }
    .error { color: #ef4444; font-weight: bold; }
    .info { background: #dbeafe; padding: 15px; border-radius: 6px; margin: 15px 0; }
    code { background: #1f2937; color: #10b981; padding: 2px 6px; border-radius: 3px; }
    pre { background: #1f2937; color: #10b981; padding: 15px; border-radius: 6px; overflow-x: auto; }
</style>";

echo "<div class='info'>";
echo "<h3>✅ Fix Applied</h3>";
echo "<p>Updated <code>RegistrationController.php</code> to extract latitude and longitude from POST data.</p>";
echo "</div>";

echo "<h3>What Was Changed:</h3>";
echo "<pre>";
echo "// BEFORE (missing latitude and longitude)\n";
echo "\$roleData['clinic_manager'] = [\n";
echo "    'clinic_name' => \$_POST['clinic_name'] ?? '',\n";
echo "    'clinic_address' => \$_POST['clinic_address'] ?? '',\n";
echo "    'district' => \$_POST['district'] ?? '',\n";
echo "    'clinic_phone' => \$_POST['clinic_phone'] ?? '',\n";
echo "    'clinic_email' => \$_POST['clinic_email'] ?? ''\n";
echo "];\n\n";

echo "// AFTER (now includes latitude and longitude)\n";
echo "\$roleData['clinic_manager'] = [\n";
echo "    'clinic_name' => \$_POST['clinic_name'] ?? '',\n";
echo "    'clinic_address' => \$_POST['clinic_address'] ?? '',\n";
echo "    'district' => \$_POST['district'] ?? '',\n";
echo "    'clinic_phone' => \$_POST['clinic_phone'] ?? '',\n";
echo "    'clinic_email' => \$_POST['clinic_email'] ?? '',\n";
echo "    <span style='color: #fbbf24;'>'latitude' => \$_POST['latitude'] ?? null,</span>\n";
echo "    <span style='color: #fbbf24;'>'longitude' => \$_POST['longitude'] ?? null</span>\n";
echo "];\n";
echo "</pre>";

echo "<h3>Testing Instructions:</h3>";
echo "<ol>";
echo "<li>Clear your browser cache or use incognito mode</li>";
echo "<li>Go to <a href='/PETVET/index.php?module=guest&page=clinic-manager-register' target='_blank'>/PETVET/index.php?module=guest&page=clinic-manager-register</a></li>";
echo "<li>Fill out the registration form</li>";
echo "<li>Click on the map to select a location</li>";
echo "<li>Verify you see coordinates displayed under the map</li>";
echo "<li>Submit the form</li>";
echo "<li>Check the database - <code>map_location</code> should now be populated!</li>";
echo "</ol>";

echo "<div class='info'>";
echo "<h3>🔍 Verify Database After Test:</h3>";
echo "<p>Run this query to check the latest clinic:</p>";
echo "<pre>SELECT id, clinic_name, map_location, verification_status, is_active FROM clinics ORDER BY id DESC LIMIT 1;</pre>";
echo "<p>The <code>map_location</code> column should show something like: <code>6.927079, 79.861244</code></p>";
echo "</div>";

echo "<h3>Why This Happened:</h3>";
echo "<p>The registration form was correctly sending <code>latitude</code> and <code>longitude</code> in the POST data, ";
echo "but the <code>RegistrationController::extractRoleSpecificData()</code> method wasn't extracting these fields ";
echo "from the POST array. So when the data was passed to <code>RegistrationModel::createClinicManagerProfile()</code>, ";
echo "the latitude and longitude values were missing.</p>";

echo "<div style='background: #d1fae5; padding: 15px; border-radius: 6px; margin: 20px 0;'>";
echo "<h3 style='color: #065f46; margin-top: 0;'>✅ Fix Complete!</h3>";
echo "<p style='color: #065f46;'>The latitude and longitude should now be saved correctly to the database.</p>";
echo "</div>";
?>
