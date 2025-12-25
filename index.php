<?php
// Start session at the very beginning
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/connect.php';
require_once __DIR__ . '/config/auth_helper.php';

// Function to display 404 error page
function show404($message = null) {
    http_response_code(404);
    include __DIR__ . '/views/errors/404.php';
    exit;
}

$module = $_GET['module'] ?? 'guest'; // default module

// Define which modules require authentication and their required roles
$moduleRoleMap = [
    'admin' => 'admin',
    'vet' => 'vet',
    'clinic-manager' => 'clinic_manager',
    'receptionist' => 'receptionist',
    'trainer' => 'trainer',
    'sitter' => 'sitter',
    'breeder' => 'breeder',
    'groomer' => 'groomer',
    'pet-owner' => 'pet_owner',
    'guest' => null,
];

// Check if module exists
if (!array_key_exists($module, $moduleRoleMap)) {
    show404("The requested module doesn't exist.");
}

$requiredRole = $moduleRoleMap[$module];

// Check authentication and authorization
if ($requiredRole !== null) {

    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '/PETVET/index.php';
        header('Location: /PETVET/index.php?module=guest&page=login');
        exit;
    }

    if (!hasRole($requiredRole)) {
        $userRole = currentRole();

        $redirects = [
            'admin' => '/PETVET/index.php?module=admin&page=dashboard',
            'vet' => '/PETVET/index.php?module=vet&page=dashboard',
            'clinic_manager' => '/PETVET/index.php?module=clinic-manager&page=overview',
            'receptionist' => '/PETVET/index.php?module=receptionist&page=dashboard',
            'trainer' => '/PETVET/index.php?module=trainer&page=dashboard',
            'sitter' => '/PETVET/index.php?module=sitter&page=dashboard',
            'breeder' => '/PETVET/index.php?module=breeder&page=dashboard',
            'groomer' => '/PETVET/index.php?module=groomer&page=services',
            'pet_owner' => '/PETVET/index.php?module=pet-owner&page=my-pets',
        ];

        $redirect = $redirects[$userRole] ?? '/PETVET/index.php?module=guest&page=home';
        header('Location: ' . $redirect);
        exit;
    }

    // ✅ Extra safety: clinic-based enforcement for vets
    if ($module === 'vet') {
        if (empty($_SESSION['clinic_id'])) {
            try {
                $stmt = db()->prepare("SELECT clinic_id FROM vets WHERE user_id = ? LIMIT 1");
                $stmt->execute([$_SESSION['user_id']]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row && !empty($row['clinic_id'])) {
                    $_SESSION['clinic_id'] = (int)$row['clinic_id'];
                } else {
                    // Vet has no clinic profile: block access cleanly
                    header('Location: /PETVET/index.php?module=guest&page=home');
                    exit;
                }
            } catch (Exception $e) {
                header('Location: /PETVET/index.php?module=guest&page=home');
                exit;
            }
        }
    }
}

// Landing pages of each user-role
if ($module === 'clinic-manager') {
    $page = $_GET['page'] ?? 'overview';
} elseif ($module === 'pet-owner') {
    $page = $_GET['page'] ?? 'my-pets';
} elseif ($module === 'vet') {
    $page = $_GET['page'] ?? 'dashboard';
} elseif ($module === 'admin') {
    $page = $_GET['page'] ?? 'dashboard';
} elseif ($module === 'receptionist') {
    $page = $_GET['page'] ?? 'dashboard';
} elseif ($module === 'trainer') {
    $page = $_GET['page'] ?? 'dashboard';
} elseif ($module === 'sitter') {
    $page = $_GET['page'] ?? 'dashboard';
} elseif ($module === 'breeder') {
    $page = $_GET['page'] ?? 'dashboard';
} elseif ($module === 'groomer') {
    $page = $_GET['page'] ?? 'services';
} else { // guest
    $page = $_GET['page'] ?? 'home';
    if ($page === 'index') { $page = 'home'; }
}

switch ($module) {

    case 'clinic-manager':
        require_once __DIR__ . '/controllers/ClinicManagerController.php';
        $c = new ClinicManagerController();
        switch ($page) {
            case 'appointments': $c->appointments(); break;
            case 'overview': $c->overview(); break;
            case 'reports': $c->reports(); break;
            case 'shop': $c->shop(); break;
            case 'vets': $c->vets(); break;
            case 'staff': $c->staff(); break;
            case 'settings': $c->settings(); break;
            default: show404("This clinic manager page doesn't exist."); break;
        }
        break;

    case 'pet-owner':
        require_once __DIR__ . '/controllers/PetOwnerController.php';
        $c = new PetOwnerController();
        switch ($page) {
            case 'my-pets': $c->myPets(); break;
            case 'medical-records': $c->medicalRecords(); break;
            case 'appointments': $c->appointments(); break;
            case 'services': $c->services(); break;
            case 'lost-found': $c->lostFound(); break;
            case 'explore-pets': $c->explorePets(); break;
            case 'sell-pets': $c->sellPets(); break;
            case 'settings': $c->settings(); break;
            case 'shop': $c->shop(); break;
            case 'shop-clinic': $c->shopClinic(); break;
            case 'shop-product': $c->shopProduct(); break;
            default: show404("This pet owner page doesn't exist."); break;
        }
        break;

    case 'vet':
        require_once __DIR__ . '/controllers/VetController.php';
        $c = new VetController();
        switch ($page) {
            case 'dashboard': $c->dashboard(); break;
            case 'appointments': $c->appointments(); break;
            case 'medical-records': $c->medicalRecords(); break;
            case 'prescriptions': $c->prescriptions(); break;
            case 'vaccinations': $c->vaccinations(); break;
            case 'settings': $c->settings(); break;
            default: show404("This veterinarian page doesn't exist."); break;
        }
        break;

    case 'receptionist':
        require_once __DIR__ . '/controllers/ReceptionistController.php';
        $c = new ReceptionistController();
        switch ($page) {
            case 'dashboard': $c->dashboard(); break;
            case 'appointments': $c->appointments(); break;
            case 'payments': $c->payments(); break;
            case 'payment-records': $c->paymentRecords(); break;
            case 'settings': $c->settings(); break;
            default: show404("This receptionist page doesn't exist."); break;
        }
        break;

    case 'admin':
        require_once __DIR__ . '/controllers/AdminController.php';
        $c = new AdminController();
        switch ($page) {
            case 'dashboard': $c->dashboard(); break;
            case 'manage-users': $c->manageUsers(); break;
            case 'manage-users-by-role': $c->manageUsersByRole(); break;
            case 'manage-clinics': $c->manageClinics(); break;
            case 'appointments': $c->appointments(); break;
            case 'medical-records': $c->medicalRecords(); break;
            case 'pet-listings': $c->petListings(); break;
            case 'finance-panel': $c->financePanel(); break;
            case 'reports': $c->reports(); break;
            case 'lost-found': $c->lostFound(); break;
            case 'settings': $c->settings(); break;
            default: show404("This admin page doesn't exist."); break;
        }
        break;

    case 'trainer':
        require_once __DIR__ . '/controllers/TrainerController.php';
        $c = new TrainerController();

        $action = $_GET['action'] ?? null;
        if ($action === 'handleTrainingAction') {
            $c->handleTrainingAction();
            exit;
        }

        switch ($page) {
            case 'dashboard': $c->dashboard(); break;
            case 'appointments': $c->appointments(); break;
            case 'availability': $c->availability(); break;
            case 'clients': $c->clients(); break;
            case 'settings': $c->settings(); break;
            default: show404("This trainer page doesn't exist."); break;
        }
        break;

    case 'sitter':
        require_once __DIR__ . '/controllers/SitterController.php';
        $c = new SitterController();

        $action = $_GET['action'] ?? null;
        if ($action === 'handleBookingAction') {
            $c->handleBookingAction();
            exit;
        }

        switch ($page) {
            case 'dashboard': $c->dashboard(); break;
            case 'bookings': $c->bookings(); break;
            case 'pets': $c->pets(); break;
            case 'availability': $c->availability(); break;
            case 'settings': $c->settings(); break;
            default: show404("This sitter page doesn't exist."); break;
        }
        break;

    case 'breeder':
        require_once __DIR__ . '/controllers/BreederController.php';
        $c = new BreederController();
        switch ($page) {
            case 'dashboard': $c->dashboard(); break;
            case 'requests': $c->requests(); break;
            case 'breeding-pets': $c->breedingPets(); break;
            case 'availability': $c->availability(); break;
            case 'pets': $c->pets(); break;
            case 'settings': $c->settings(); break;
            default: show404("This breeder page doesn't exist."); break;
        }
        break;

    case 'groomer':
        require_once __DIR__ . '/controllers/GroomerController.php';
        $c = new GroomerController();

        $action = $_GET['action'] ?? null;
        if ($action === 'handleServiceAction') {
            $c->handleServiceAction();
            exit;
        }
        if ($action === 'handlePackageAction') {
            $c->handlePackageAction();
            exit;
        }

        switch ($page) {
            case '':
            case 'services': $c->services(); break;
            case 'packages': $c->packages(); break;
            case 'availability': $c->availability(); break;
            case 'settings': $c->settings(); break;
            default: show404("This groomer page doesn't exist."); break;
        }
        break;

    case 'guest':
        // Redirect if already logged in (except logout)
        if (isLoggedIn() && $page !== 'logout') {
            redirectToDashboard();
        }

        // Pages that use GuestController
        if (in_array($page, ['shop','shop-clinic','shop-product','explore-pets','lost-found','register','vet-register','clinic-manager-register'], true)) {
            require_once __DIR__ . '/controllers/GuestController.php';
            $c = new GuestController();
            switch ($page) {
                case 'shop': $c->shop(); break;
                case 'shop-clinic': $c->shopClinic(); break;
                case 'shop-product': $c->shopProduct(); break;
                case 'explore-pets': $c->explorePets(); break;
                case 'lost-found': $c->lostFound(); break;
                case 'register': $c->register(); break;
                case 'vet-register': $c->vetRegister(); break;
                case 'clinic-manager-register': $c->clinicManagerRegister(); break;
                default: show404("This guest page doesn't exist."); break;
            }
        } else {
            $safePage = basename($page);
            $guestFile = __DIR__ . '/views/guest/' . $safePage . '.php';
            if (is_file($guestFile)) {
                require $guestFile;
            } else {
                show404("The requested page could not be found.");
            }
        }
        break;

    default:
        show404("The requested module doesn't exist.");
        break;
}
