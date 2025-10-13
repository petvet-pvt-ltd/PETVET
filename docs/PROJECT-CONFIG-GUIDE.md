# PETVET Project Configuration Guide

## Overview
This project now includes a centralized configuration system that makes it easy to change the project folder name and other settings without manually editing hundreds of files.

## Quick Folder Name Change

### Method 1: Using the Quick Change Script (Recommended)
1. Run the one-command script:
   ```bash
   php scripts/change-folder-name.php YOUR-NEW-NAME
   ```

### Method 2: Using the Update Script Manually
1. Edit `scripts/update-folder-references.php`
2. Change these lines:
   ```php
   $oldFolderName = 'PETVET-MVC';        // Current folder name
   $newFolderName = 'YOUR-NEW-NAME';     // New folder name
   ```
3. Run the script:
   ```bash
   php scripts/update-folder-references.php
   ```

### Method 2: Manual Configuration Update
1. Edit `config/config.php`
2. Change this line:
   ```php
   define('PROJECT_ROOT', '/YOUR-NEW-FOLDER-NAME');
   ```
3. Then update your code to use the helper functions (see below)

## Helper Functions Available

### Core Functions
```php
// Get base URL with optional path
getBaseUrl()              // Returns: /PETVET-MVC
getBaseUrl('index.php')   // Returns: /PETVET-MVC/index.php

// Get asset URLs (CSS, JS, images in public folder)
asset('css/style.css')    // Returns: /PETVET-MVC/public/css/style.css
asset('js/script.js')     // Returns: /PETVET-MVC/public/js/script.js

// Get view URLs
view('guest/home.php')    // Returns: /PETVET-MVC/views/guest/home.php

// Get route URLs
route('guest')            // Returns: /PETVET-MVC/index.php?module=guest
route('guest', 'home')    // Returns: /PETVET-MVC/index.php?module=guest&page=home

// Get image URLs (from views/shared/images)
img('logo.png')           // Returns: /PETVET-MVC/views/shared/images/logo.png
```

## Usage Examples

### Old Way (Hardcoded - Don't do this)
```php
<link rel="stylesheet" href="/PETVET-MVC/public/css/style.css">
<img src="/PETVET-MVC/views/shared/images/logo.png" alt="Logo">
<a href="/PETVET-MVC/index.php?module=guest&page=home">Home</a>
```

### New Way (Using Helper Functions)
```php
<link rel="stylesheet" href="<?= asset('css/style.css') ?>">
<img src="<?= img('logo.png') ?>" alt="Logo">
<a href="<?= route('guest', 'home') ?>">Home</a>
```

### For JavaScript Files
```javascript
// Old way
window.location.href = '/PETVET-MVC/index.php?module=guest&page=home';

// New way - pass the base URL from PHP
window.location.href = '<?= route("guest", "home") ?>';
```

## Migration Strategy

### For New Files
Always use the helper functions from the start:
```php
<?php require_once '../config/config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>
    <img src="<?= img('logo.png') ?>" alt="Logo">
    <a href="<?= route('guest', 'home') ?>">Home</a>
</body>
</html>
```

### For Existing Files
1. Include the config file at the top
2. Replace hardcoded paths with helper functions gradually
3. Or use the update script for bulk changes

## Configuration Options

Edit `config/config.php` to customize:

```php
// Project Configuration
define('PROJECT_ROOT', '/YOUR-FOLDER-NAME');  // Main setting to change
define('PROJECT_NAME', 'PETVET');
define('PROJECT_VERSION', '1.0.0');

// Add your own configurations
define('SITE_URL', 'http://localhost');
define('ADMIN_EMAIL', 'admin@petvet.com');
```

## Benefits

1. **One-line folder name changes**: Just update PROJECT_ROOT constant
2. **No more broken links**: When you change folder name, everything updates automatically
3. **Easier maintenance**: All URLs generated from one place
4. **Environment flexibility**: Easy to switch between development/production
5. **Future-proof**: Add new configuration options easily

## Files Updated by the Script

The update script automatically processes:
- All `.php` files
- All `.html` files  
- All `.js` files
- All `.css` files

It excludes:
- `.git` directory
- `node_modules` directory
- `vendor` directory
- `#old-project` directory

## Troubleshooting

### Script doesn't find files
- Make sure you're running it from the project root directory
- Check that PHP is in your PATH or use full path to php.exe

### Some references not updated
- Check if they use backslashes `\` instead of forward slashes `/`
- Look for references without the leading slash

### Links still broken
- Ensure the config.php is included in your PHP files
- Check that helper functions are being used correctly
- Verify the PROJECT_ROOT constant is correct

## Future Enhancements

You can extend this system to handle:
- Database configuration
- API endpoints
- Environment-specific settings
- Debug mode settings
- Cache settings