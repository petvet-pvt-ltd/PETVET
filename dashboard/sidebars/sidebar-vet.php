
<div class="dashboard-sidebar">
  <div class="sidebar-heading">
    <h2 class="sidebar-title">
      <img src="../../images/petvet-logo-web.png" class="petvet-logo" alt="PetVet">
      PETVET
    </h2>
  </div>

  <ul class="sidebar-nav-top">
    <a href="index.php" class="<?= basename($_SERVER['PHP_SELF'])==='index.php' ? 'active' : '' ?>">
      <li class="nav-item"><img src="../../images/dashboard/meter.png" class="icon" alt=""> Dashboard</li>
    </a>
    <a href="appointments.php" class="<?= basename($_SERVER['PHP_SELF'])==='appointments.php' ? 'active' : '' ?>">
      <li class="nav-item"><img src="../../images/dashboard/calendar.png" class="icon" alt=""> Appointments</li>
    </a>
    <a href="medical-records.php" class="<?= basename($_SERVER['PHP_SELF'])==='medical-records.php' ? 'active' : '' ?>">
      <li class="nav-item"><img src="../../images/dashboard/folder.png" class="icon" alt=""> Medical Records</li>
    </a>
    <a href="prescriptions.php" class="<?= basename($_SERVER['PHP_SELF'])==='prescriptions.php' ? 'active' : '' ?>">
      <li class="nav-item"><img src="../../images/dashboard/package-box.png" class="icon" alt=""> Prescriptions</li>
    </a>
    <a href="vaccinations.php" class="<?= basename($_SERVER['PHP_SELF'])==='vaccinations.php' ? 'active' : '' ?>">
      <li class="nav-item"><img src="../../images/dashboard/pets.png" class="icon" alt=""> Vaccinations</li>
    </a>
  </ul>

  <ul class="sidebar-nav-bottom">
    <li class="nav-item"><img src="../../images/dashboard/account-settings.png" class="icon" alt=""> Settings</li>
    <a href="../../db/logout.php"><li class="nav-item"><img src="../../images/dashboard/logout.png" class="icon" alt=""> Logout</li></a>
  </ul>
</div>
