<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Session Debug</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        h2 { color: #333; }
        pre { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .key { color: #0066cc; font-weight: bold; }
        .value { color: #009900; }
    </style>
</head>
<body>
    <h2>🔍 Session Debug Information</h2>
    <pre><?php 
    if (empty($_SESSION)) {
        echo "❌ No session data found. User is not logged in.";
    } else {
        echo "✅ Session Active\n\n";
        foreach ($_SESSION as $key => $value) {
            echo '<span class="key">' . htmlspecialchars($key) . '</span>: ';
            if (is_array($value)) {
                echo "\n" . '<span class="value">' . print_r($value, true) . '</span>';
            } else {
                echo '<span class="value">' . htmlspecialchars($value) . '</span>';
            }
            echo "\n";
        }
    }
    ?></pre>
    
    <p><a href="/PETVET/">← Back to Home</a> | <a href="/PETVET/logout.php">Logout</a></p>
</body>
</html>
