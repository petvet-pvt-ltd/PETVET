<ul class="sidebar-nav-top">
  <a href="index.php" class="<?= $currentPage == 'index.php' ? 'active' : '' ?>">
    <li class="nav-item"><img src="../../images/dashboard/meter.png" class="icon"> Overview</li>
  </a>

  <a href="appointments.php" class="<?= $currentPage == 'appointments.php' ? 'active' : '' ?>">
    <li class="nav-item"><img src="../../images/dashboard/calendar.png" class="icon"> Appointments</li>
  </a>
  
  <a href="vets.php" class="<?= $currentPage == 'vets.php' ? 'active' : '' ?>">
    <li class="nav-item"><img src="../../images/dashboard/vets.png" class="icon"> Vets</li>
  </a>

  <a href="shop.php" class="<?= $currentPage == 'shop.php' ? 'active' : '' ?>">
    <li class="nav-item"><img src="../../images/dashboard/shopping-cart.png" class="icon"> Shop</li>
  </a>

  <a href="reports.php" class="<?= $currentPage == 'reports.php' ? 'active' : '' ?>">
    <li class="nav-item"><img src="../../images/dashboard/reports.png" class="icon"> Reports</li>
  </a>
</ul>