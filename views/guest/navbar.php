<?php
  // Determine active guest page robustly when routed via index.php
  $active = $_GET['page'] ?? null;
  if (!$active) {
    $basename = basename($_SERVER['PHP_SELF'] ?? '');
    $active = pathinfo($basename, PATHINFO_FILENAME);
  }
  if ($active === 'index') { $active = 'home'; }
?>

<nav class="navbar">
  <div class="logo">
    <img src="/PETVET/views/shared/images/petvet-logo-web.png" alt="Pet Vet Logo">
  </div>
  <input type="checkbox" id="nav-toggle" class="nav-toggle" aria-label="Toggle navigation" />
  <label for="nav-toggle" class="hamburger" aria-hidden="true">
    <span></span><span></span><span></span>
  </label>
  <ul class="nav-links">
  <li><a href="/PETVET/index.php?module=guest&page=home" class="<?= $active === 'home' ? 'active' : '' ?>">Home</a></li>
  <li><a href="/PETVET/index.php?module=guest&page=shop" class="<?= $active === 'shop' ? 'active' : '' ?>">Pet Shop</a></li>
  <li><a href="/PETVET/index.php?module=guest&page=adopt" class="<?= $active === 'adopt' ? 'active' : '' ?>">Pet Adoption</a></li>
  <li><a href="/PETVET/index.php?module=guest&page=about" class="<?= $active === 'about' ? 'active' : '' ?>">About</a></li>
  <li><a href="/PETVET/index.php?module=guest&page=contact" class="<?= $active === 'contact' ? 'active' : '' ?>">Contact</a></li>
  <li class="login-item"><a href="/PETVET/index.php?module=guest&page=login" class="<?= $active === 'login' ? 'active' : '' ?>">Login</a></li>
  </ul>
</nav>

<!--Navigation bar content only-->