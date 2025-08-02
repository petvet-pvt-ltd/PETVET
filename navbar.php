<?php
  $currentPage = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar">
  <div class="logo">
    <img src="images/petvet-logo-web.png" alt="Pet Vet Logo">
  </div>
  <ul class="nav-links">
    <li><a href="index.php" class="<?= $currentPage == 'index.php' ? 'active' : '' ?>">Home</a></li>
    <li><a href="shop.php" class="<?= $currentPage == 'shop.php' ? 'active' : '' ?>">Pet Shop</a></li>
    <li><a href="adopt.php" class="<?= $currentPage == 'adopt.php' ? 'active' : '' ?>">Pet Adoption</a></li>
    <li><a href="about.php" class="<?= $currentPage == 'about.php' ? 'active' : '' ?>">About</a></li>
    <li><a href="contact.php" class="<?= $currentPage == 'contact.php' ? 'active' : '' ?>">Contact</a></li>
  </ul>
  <a href="login.php" class="login-btn <?= $currentPage == 'login.php' ? 'active' : '' ?>">Login</a>
</nav>

<!--Navigation bar content only-->