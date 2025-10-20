<?php
require_once __DIR__ . '/config/config.php';  // Include global configuration
require_once __DIR__ . '/config/connect.php';

// Function to display 404 error page
function show404($message = null) {
    http_response_code(404);
    include __DIR__ . '/views/errors/404.php';
    exit;
}

/************************************************************/
$module = $_GET['module'] ?? 'guest'; // default module
/************************************************************/

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
} elseif ($module === 'guest') {
  // Default guest landing page
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
      // other pages-----
      default: show404("This clinic manager page doesn't exist."); break;
    }
    break;

  case 'pet-owner':
    require_once __DIR__ . '/controllers/PetOwnerController.php';
    $c = new PetOwnerController();
    switch ($page) {
      case 'my-pets': $c->myPets(); break;
      case 'medical-records': $c->medicalRecords(); break; // <-- Controller renders the view
  case 'appointments': $c->appointments(); break;
  case 'services': $c->services(); break;
  case 'lost-found': $c->lostFound(); break;
  case 'explore-pets': $c->explorePets(); break;
  case 'sell-pets': $c->sellPets(); break;
  case 'settings': $c->settings(); break;
  case 'shop': $c->shop(); break;
  case 'shop-product': $c->shopProduct(); break;
      // other pages-----
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
    
    // Handle AJAX actions
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
    
    // Handle AJAX actions
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
    
    // Handle AJAX actions
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
    // Handle shop pages through GuestController, others directly
    if ($page === 'shop' || $page === 'shop-product') {
      require_once __DIR__ . '/controllers/GuestController.php';
      $c = new GuestController();
      switch ($page) {
        case 'shop': $c->shop(); break;
        case 'shop-product': $c->shopProduct(); break;
        default: show404("This guest page doesn't exist."); break;
      }
    } else {
      // Render other guest views directly from views/guest/{page}.php
      $safePage = basename($page); // prevent directory traversal
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
