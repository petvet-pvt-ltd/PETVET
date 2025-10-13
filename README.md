# PETVET MVC - Pet Veterinary Management System

## Quick Setup

### Folder Name Configuration
If you need to change the project folder name:

```bash
# One command solution
php scripts/change-folder-name.php YOUR-NEW-FOLDER-NAME

# Example
php scripts/change-folder-name.php PETVET-PRODUCTION
```

## Documentation & Demos

- **📖 Complete Guide**: [`docs/PROJECT-CONFIG-GUIDE.md`](docs/PROJECT-CONFIG-GUIDE.md)
- **🎯 Live Demo**: [`docs/config-demo.php`](docs/config-demo.php) - Visit this page in your browser
- **⚙️ Configuration**: [`config/config.php`](config/config.php) - Main settings file

## Scripts

- **🔄 Quick Change**: [`scripts/change-folder-name.php`](scripts/change-folder-name.php) - One-command folder name changer
- **🔧 Manual Update**: [`scripts/update-folder-references.php`](scripts/update-folder-references.php) - Bulk file updater

## Project Structure

```
PETVET-MVC/
├── config/                 # Configuration files
│   ├── config.php         # Main configuration & helper functions
│   └── connect.php        # Database connection
├── scripts/               # Utility scripts
│   ├── change-folder-name.php
│   └── update-folder-references.php
├── docs/                  # Documentation & demos
│   ├── PROJECT-CONFIG-GUIDE.md
│   └── config-demo.php
├── controllers/           # MVC Controllers
├── models/               # MVC Models  
├── views/                # MVC Views
├── public/               # Assets (CSS, JS, images)
└── index.php             # Main entry point
```

## Quick Start

1. **Access the application**: `http://localhost/PETVET-MVC/`
2. **View configuration demo**: `http://localhost/PETVET-MVC/docs/config-demo.php`
3. **Read the full guide**: Open `docs/PROJECT-CONFIG-GUIDE.md`

## Configuration Helper Functions

```php
// Include configuration
require_once 'config/config.php';

// Generate URLs dynamically
getBaseUrl()                    // /PETVET-MVC
asset('css/style.css')          // /PETVET-MVC/public/css/style.css
route('guest', 'home')          // /PETVET-MVC/index.php?module=guest&page=home
img('logo.png')                 // /PETVET-MVC/views/shared/images/logo.png
```

## Need Help?

- Check the **complete documentation** in `docs/PROJECT-CONFIG-GUIDE.md`
- Run the **live demo** at `docs/config-demo.php`
- All scripts include built-in help and validation

---

*This project uses a custom MVC architecture with no external frameworks.*