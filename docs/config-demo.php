<?php
/**
 * Configuration System Demo
 * This file demonstrates how to use the new configuration system
 */

// Include the configuration (always do this first)
require_once '../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration Demo - <?= PROJECT_NAME ?></title>
    
    <!-- Using asset() helper for CSS -->
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .demo-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
        .code { background: #f5f5f5; padding: 10px; margin: 10px 0; }
        .old-way { border-left: 4px solid #ff4444; }
        .new-way { border-left: 4px solid #44ff44; }
    </style>
</head>
<body>
    <h1>PETVET Configuration System Demo</h1>
    
    <div class="demo-section">
        <h2>Current Configuration</h2>
        <ul>
            <li><strong>Project Root:</strong> <?= PROJECT_ROOT ?></li>
            <li><strong>Project Name:</strong> <?= PROJECT_NAME ?></li>
            <li><strong>Project Version:</strong> <?= PROJECT_VERSION ?></li>
        </ul>
    </div>

    <div class="demo-section">
        <h2>Helper Functions Examples</h2>
        
        <h3>1. Base URL Generation</h3>
        <div class="code old-way">
            <strong>❌ Old Way (Hardcoded):</strong><br>
            &lt;a href="/PETVET/index.php?module=guest&amp;page=home"&gt;Home&lt;/a&gt;
        </div>
        <div class="code new-way">
            <strong>✅ New Way (Dynamic):</strong><br>
            &lt;a href="&lt;?= route('guest', 'home') ?&gt;"&gt;Home&lt;/a&gt;<br>
            <strong>Result:</strong> <a href="<?= route('guest', 'home') ?>">Home</a>
        </div>

        <h3>2. Asset Links (CSS, JS)</h3>
        <div class="code old-way">
            <strong>❌ Old Way:</strong><br>
            &lt;link rel="stylesheet" href="/PETVET/public/css/style.css"&gt;
        </div>
        <div class="code new-way">
            <strong>✅ New Way:</strong><br>
            &lt;link rel="stylesheet" href="&lt;?= asset('css/style.css') ?&gt;"&gt;<br>
            <strong>Result:</strong> <?= asset('css/style.css') ?>
        </div>

        <h3>3. Image Sources</h3>
        <div class="code old-way">
            <strong>❌ Old Way:</strong><br>
            &lt;img src="/PETVET/views/shared/images/logo.png" alt="Logo"&gt;
        </div>
        <div class="code new-way">
            <strong>✅ New Way:</strong><br>
            &lt;img src="&lt;?= img('logo.png') ?&gt;" alt="Logo"&gt;<br>
            <strong>Result:</strong> <?= img('logo.png') ?>
        </div>

        <h3>4. Navigation Links</h3>
        <div class="code new-way">
            <strong>✅ Example Navigation:</strong><br>
            <ul>
                <li><a href="<?= route('guest', 'home') ?>">Home</a></li>
                <li><a href="<?= route('guest', 'shop') ?>">Pet Shop</a></li>
                <li><a href="<?= route('guest', 'adopt') ?>">Pet Adoption</a></li>
                <li><a href="<?= route('guest', 'about') ?>">About</a></li>
                <li><a href="<?= route('guest', 'contact') ?>">Contact</a></li>
            </ul>
        </div>
    </div>

    <div class="demo-section">
        <h2>All Available Helper Functions</h2>
        <div class="code">
            <strong>getBaseUrl():</strong> <?= getBaseUrl() ?><br>
            <strong>getBaseUrl('index.php'):</strong> <?= getBaseUrl('index.php') ?><br>
            <strong>asset('css/style.css'):</strong> <?= asset('css/style.css') ?><br>
            <strong>view('guest/home.php'):</strong> <?= view('guest/home.php') ?><br>
            <strong>route('guest'):</strong> <?= route('guest') ?><br>
            <strong>route('guest', 'home'):</strong> <?= route('guest', 'home') ?><br>
            <strong>img('logo.png'):</strong> <?= img('logo.png') ?>
        </div>
    </div>

    <div class="demo-section">
        <h2>How to Change Folder Name</h2>
        <div class="code">
            <strong>Method 1 - One Command (Recommended):</strong><br>
            php scripts/change-folder-name.php NEW-FOLDER-NAME<br><br>
            
            <strong>Method 2 - Manual:</strong><br>
            1. Edit config/config.php<br>
            2. Change PROJECT_ROOT to '/NEW-FOLDER-NAME'<br>
            3. Run: php scripts/update-folder-references.php
        </div>
    </div>

    <script>
        // Example of using base URL in JavaScript
        console.log('Base URL from PHP: <?= PROJECT_ROOT ?>');
        
        function navigateToHome() {
            window.location.href = '<?= route("guest", "home") ?>';
        }
    </script>
</body>
</html>