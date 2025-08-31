<!-- <ul class="sidebar-nav-top">
  <li class="nav-item"><img src="../../images/dashboard/pets.png" class="icon"> My Pets</li>
  <li class="nav-item"><img src="../../images/dashboard/calendar.png" class="icon"> Appointments</li>
  <li class="nav-item"><img src="../../images/dashboard/folder.png" class="icon"> Medical Records</li>
  <li class="nav-item"><img src="../../images/dashboard/shopping-cart.png" class="icon"> Shop</li>
  <li class="nav-item"><img src="../../images/dashboard/package-box.png" class="icon"> My Orders</li>
  <li class="nav-item"><img src="../../images/dashboard/compass.png" class="icon"> Lost & Found</li>
  <li class="nav-item"><img src="../../images/dashboard/sell-pets.png" class="icon"> Sell Pets</li>
</ul> -->

<ul class="sidebar-nav-top">
  <a href="index.php" class="<?= $currentPage == 'index.php' ? 'active' : '' ?>">
    <li class="nav-item"><img src="../../images/dashboard/pets.png" class="icon"> My Pets</li>
  </a>

  <a href="appointments.php" class="<?= $currentPage == 'appointments.php' ? 'active' : '' ?>">
    <li class="nav-item"><img src="../../images/dashboard/calendar.png" class="icon"> Appointments</li>
  </a>

  <a href="shop.php" class="<?= $currentPage == 'shop.php' ? 'active' : '' ?>">
    <li class="nav-item"><img src="../../images/dashboard/shopping-cart.png" class="icon"> Shop</li>
  </a>

  <a href="my-orders.php" class="<?= $currentPage == 'my-orders.php' ? 'active' : '' ?>">
    <li class="nav-item"><img src="../../images/dashboard/package-box.png" class="icon"> My Orders</li>
  </a>

  <a href="lost-found.php" class="<?= $currentPage == 'lost-found.php' ? 'active' : '' ?>">
    <li class="nav-item"><img src="../../images/dashboard/compass.png" class="icon"> Lost & Found</li>
  </a>

  <a href="sell-pets.php" class="<?= $currentPage == 'sell-pets.php' ? 'active' : '' ?>">
    <li class="nav-item"><img src="../../images/dashboard/sell-pets.png" class="icon"> Sell Pets</li>
  </a>
</ul>

