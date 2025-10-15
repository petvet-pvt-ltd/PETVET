
<?php
// Sidebar stylesheet for inclusion in main page head
echo '<link rel="stylesheet" href="/PETVET/public/css/sidebar/sidebar.css">';
$activeModule = isset($module) ? $module : (isset($GLOBALS['module']) ? $GLOBALS['module'] : null);
echo "<!-- activeModule: $activeModule -->";
?>

<!-- Floating toggle button for mobile -->
<button class="sidebar-toggle" onclick="toggleSidebar()">
  <span class="toggle-icon">&#9776;</span>
</button>

<div class="dashboard-sidebar" id="sidebar">
  <div class="sidebar-heading">
    <h2 class="sidebar-title">
  <img src="/PETVET/views/shared/images/sidebar/petvet-logo-web.png" class="petvet-logo">PETVET
    </h2>
    <!-- Close button for mobile -->
    <button class="sidebar-close" onclick="toggleSidebar()">&times;</button>
  </div>

  <?php
  $activeModule = isset($module) ? $module : (isset($GLOBALS['module']) ? $GLOBALS['module'] : null);
  if ($activeModule == 'clinic_manager') {
    // Clinic Manager Sidebar
    ?>
    <ul class="sidebar-nav-top">
      <a href="/PETVET/index.php?module=clinic-manager&page=overview" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'overview.php') ? 'active' : '' ?>">
        <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/meter.png" class="icon"> Overview</li>
      </a>
      <a href="/PETVET/index.php?module=clinic-manager&page=appointments" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'appointments.php') ? 'active' : '' ?>">
        <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/calendar.png" class="icon"> Appointments</li>
      </a>
      <a href="/PETVET/index.php?module=clinic-manager&page=vets" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'vets.php') ? 'active' : '' ?>">
        <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/vets.png" class="icon"> Vets</li>
      </a>
      <a href="/PETVET/index.php?module=clinic-manager&page=staff" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'staff.php') ? 'active' : '' ?>">
        <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/staff.png" class="icon"> Staff</li>
      </a>
      <a href="/PETVET/index.php?module=clinic-manager&page=shop" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'shop.php') ? 'active' : '' ?>">
        <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/shopping-cart.png" class="icon"> Shop</li>
      </a>
      <a href="/PETVET/index.php?module=clinic-manager&page=reports" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'reports.php') ? 'active' : '' ?>">
        <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/reports.png" class="icon"> Reports</li>
      </a>
    </ul>
    <?php
  } elseif ($activeModule == 'pet-owner') {
    // Pet Owner Sidebar
    ?>
    
    <ul class="sidebar-nav-top">
      <a href="/PETVET/index.php?module=pet-owner&page=my-pets" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'my-pets.php') ? 'active' : '' ?>">
        <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/pets.png" class="icon"> My Pets</li>
      </a>
      <a href="/PETVET/index.php?module=pet-owner&page=appointments" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'appointments.php') ? 'active' : '' ?>">
        <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/calendar.png" class="icon"> Appointments</li>
      </a>
      <a href="/PETVET/index.php?module=pet-owner&page=shop" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'shop.php') ? 'active' : '' ?>">
        <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/shopping-cart.png" class="icon"> Shop</li>
      </a>
      <a href="/PETVET/index.php?module=pet-owner&page=lost-found" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'lost-found.php') ? 'active' : '' ?>">
        <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/compass.png" class="icon"> Lost & Found</li>
      </a>
      <a href="/PETVET/index.php?module=pet-owner&page=explore-pets" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'explore-pets.php') ? 'active' : '' ?>">
        <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/sell-pets.png" class="icon"> Explore Pets</li>
      </a>
    </ul>
    <?php
  } elseif ($activeModule == 'vet') {
    ?>
    <ul class="sidebar-nav-top">
      <a href="/PETVET/index.php?module=vet&page=dashboard" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'dashboard.php') ? 'active' : '' ?>">
        <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/meter.png" class="icon"> Dashboard</li>
      </a>
      <a href="/PETVET/index.php?module=vet&page=appointments" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'appointments.php') ? 'active' : '' ?>">
        <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/calendar.png" class="icon"> Appointments</li>
      </a>
      <a href="/PETVET/index.php?module=vet&page=medical-records" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'medical-records.php') ? 'active' : '' ?>">
        <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/folder.png" class="icon"> Medical Records</li>
      </a>
      <a href="/PETVET/index.php?module=vet&page=prescriptions" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'prescriptions.php') ? 'active' : '' ?>">
        <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/package-box.png" class="icon"> Prescriptions</li>
      </a>
      <a href="/PETVET/index.php?module=vet&page=vaccinations" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'vaccinations.php') ? 'active' : '' ?>">
        <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/pets.png" class="icon"> Vaccinations</li>
      </a>
    </ul>
    <?php
  } elseif ($activeModule == 'admin') {
    ?>
    <ul class="sidebar-nav-top">
      <a href="/PETVET/index.php?module=admin&page=dashboard" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'dashboard.php') ? 'active' : '' ?>">
        <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/meter.png" class="icon"> Overview</li>
      </a>
      <a href="/PETVET/index.php?module=admin&page=manage-users" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'manage-users.php') ? 'active' : '' ?>">
        <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/staff.png" class="icon"> Manage Users</li>
      </a>
      <a href="/PETVET/index.php?module=admin&page=appointments" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'appointments.php') ? 'active' : '' ?>">
        <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/calendar.png" class="icon"> Appointments</li>
      </a>
      <a href="/PETVET/index.php?module=admin&page=medical-records" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'medical-records.php') ? 'active' : '' ?>">
        <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/folder.png" class="icon"> Medical Records</li>
      </a>
      <a href="/PETVET/index.php?module=admin&page=pet-listings" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'pet-listings.php') ? 'active' : '' ?>">
        <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/pets.png" class="icon"> Pet Listings</li>
      </a>
      <a href="/PETVET/index.php?module=admin&page=lost-found" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'lost-found.php') ? 'active' : '' ?>">
        <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/compass.png" class="icon"> Lost & Found</li>
      </a>
      <a href="/PETVET/index.php?module=admin&page=reports" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'reports.php') ? 'active' : '' ?>">
        <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/reports.png" class="icon"> Reports</li>
      </a>
      <a href="/PETVET/index.php?module=admin&page=finance-panel" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'finance-panel.php') ? 'active' : '' ?>">
        <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/package-box.png" class="icon"> Finance Panel</li>
      </a>
    </ul>
    <?php
  } elseif ($activeModule == 'receptionist') {
    ?>
    <ul class="sidebar-nav-top">
      <a href="/PETVET/index.php?module=receptionist&page=dashboard" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'dashboard.php') ? 'active' : '' ?>">
        <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/meter.png" class="icon"> Overview</li>
      </a>
      <a href="/PETVET/index.php?module=receptionist&page=appointments" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'appointments.php') ? 'active' : '' ?>">
        <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/calendar.png" class="icon"> Appointments</li>
      </a>
    </ul>
    <?php
  } elseif ($activeModule == 'trainer') {
    // Trainer Sidebar
    ?>
    <ul class="sidebar-nav-top">
      <a href="/PETVET/index.php?module=trainer&page=dashboard" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'dashboard.php') ? 'active' : '' ?>">
        <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/meter.png" class="icon"> Dashboard</li>
      </a>
      <a href="/PETVET/index.php?module=trainer&page=appointments" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'appointments.php') ? 'active' : '' ?>">
        <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/calendar.png" class="icon"> Appointments</li>
      </a>
      <a href="/PETVET/index.php?module=trainer&page=clients" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'clients.php') ? 'active' : '' ?>">
        <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/staff.png" class="icon"> Clients</li>
      </a>
    </ul>
    <?php
  } elseif ($activeModule == 'sitter') {
    // Sitter Sidebar
    ?>
    <ul class="sidebar-nav-top">
      <a href="/PETVET/index.php?module=sitter&page=dashboard" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'dashboard.php') ? 'active' : '' ?>">
        <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/meter.png" class="icon"> Dashboard</li>
      </a>
      <a href="/PETVET/index.php?module=sitter&page=bookings" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'bookings.php') ? 'active' : '' ?>">
        <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/calendar.png" class="icon"> Bookings</li>
      </a>
      <a href="/PETVET/index.php?module=sitter&page=pets" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'pets.php') ? 'active' : '' ?>">
        <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/pets.png" class="icon"> Pets</li>
      </a>
    </ul>
    <?php
  } elseif ($activeModule == 'breeder') {
    // Breeder Sidebar
    ?>
    <ul class="sidebar-nav-top">
      <a href="/PETVET/index.php?module=breeder&page=dashboard" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'dashboard.php') ? 'active' : '' ?>">
        <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/meter.png" class="icon"> Dashboard</li>
      </a>
      <a href="/PETVET/index.php?module=breeder&page=pets" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'pets.php') ? 'active' : '' ?>">
        <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/pets.png" class="icon"> My Pets</li>
      </a>
      <a href="/PETVET/index.php?module=breeder&page=sales" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'sales.php') ? 'active' : '' ?>">
        <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/package-box.png" class="icon"> Sales</li>
      </a>
    </ul>
    <?php
  }
  ?>

  <ul class="sidebar-nav-bottom">
  <?php if ($activeModule == 'clinic_manager'): ?>
    <a href="/PETVET/index.php?module=clinic-manager&page=settings" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'settings.php') ? 'active' : '' ?>">
      <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/account-settings.png" class="icon"> Settings</li>
    </a>
  <?php elseif ($activeModule == 'pet-owner'): ?>
    <a href="/PETVET/index.php?module=pet-owner&page=settings" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'settings.php') ? 'active' : '' ?>">
      <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/account-settings.png" class="icon"> Settings</li>
    </a>
  <?php elseif ($activeModule == 'vet'): ?>
    <a href="/PETVET/index.php?module=vet&page=settings" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'settings.php') ? 'active' : '' ?>">
      <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/account-settings.png" class="icon"> Settings</li>
    </a>
  <?php elseif ($activeModule == 'admin'): ?>
    <a href="/PETVET/index.php?module=admin&page=settings" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'settings.php') ? 'active' : '' ?>">
      <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/account-settings.png" class="icon"> Settings</li>
    </a>
  <?php elseif ($activeModule == 'receptionist'): ?>
    <a href="/PETVET/index.php?module=receptionist&page=settings" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'settings.php') ? 'active' : '' ?>">
      <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/account-settings.png" class="icon"> Settings</li>
    </a>
  <?php elseif ($activeModule == 'trainer'): ?>
    <a href="/PETVET/index.php?module=trainer&page=settings" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'settings.php') ? 'active' : '' ?>">
      <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/account-settings.png" class="icon"> Settings</li>
    </a>
  <?php elseif ($activeModule == 'sitter'): ?>
    <a href="/PETVET/index.php?module=sitter&page=settings" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'settings.php') ? 'active' : '' ?>">
      <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/account-settings.png" class="icon"> Settings</li>
    </a>
  <?php elseif ($activeModule == 'breeder'): ?>
    <a href="/PETVET/index.php?module=breeder&page=settings" class="<?= (isset($GLOBALS['currentPage']) && $GLOBALS['currentPage'] == 'settings.php') ? 'active' : '' ?>">
      <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/account-settings.png" class="icon"> Settings</li>
    </a>
  <?php else: ?>
    <li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/account-settings.png" class="icon"> Settings</li>
  <?php endif; ?>
  <a href="/PETVET/db/logout.php"><li class="nav-item"><img src="/PETVET/views/shared/images/sidebar/logout.png" class="icon"> Logout</li></a>
  </ul>
</div>

<script>
  function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const body = document.body;
    sidebar.classList.toggle('active-sidebar');
    body.classList.toggle('active-sidebar');
  }
</script>