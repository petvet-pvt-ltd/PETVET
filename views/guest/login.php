<?php /* public guest page */ ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login | PETVET</title>
  <link rel="stylesheet" href="/PETVET/public/css/guest/login.css">
  <link rel="preload" href="/PETVET/views/shared/images/login-wallpaper.jpg" as="image">
  <link rel="preload" href="/PETVET/views/shared/images/petvet-logo-black.png" as="image">
  <link rel="preload" href="/PETVET/views/shared/images/mail.png" as="image">
  <link rel="preload" href="/PETVET/views/shared/images/lock.png" as="image">
  
</head>

<body>
  <div class="auth-wrapper">
    <aside class="auth-hero">
      <div class="hero-overlay"></div>
      <div class="hero-content">
        <h2>Your pet's health, managed beautifully.</h2>
        <p>Book appointments, view records, and connect with top vets in one place.</p>
      </div>
    </aside>
    <main class="auth-panel">
      <div class="auth-card">
        <img class="brand" src="/PETVET/views/shared/images/petvet-logo-black.png" alt="PETVET Logo">
        <h1>Login</h1>
        <p class="subtitle">Welcome back! Please login to your PETVET account.</p>
        <form action="/PETVET/db/login-process.php" method="post" novalidate>
          <label class="field">
            <img class="icon" src="/PETVET/views/shared/images/mail.png" alt="Email Icon">
            <input type="email" name="email" placeholder="Email" required>
          </label>
          <label class="field">
            <img class="icon" src="/PETVET/views/shared/images/lock.png" alt="Password Icon">
            <input type="password" name="password" placeholder="Password" required>
          </label>
          <div class="row between">
            <div></div>
            <a class="muted" href="#">Forgot password?</a>
          </div>
          <button name="login-submit" type="submit" class="primary-btn">Login</button>
        </form>
        <div class="signup">Don't have an account? <a href="/PETVET/register/client-reg.php">Sign up</a></div>
      </div>
    </main>
  </div>
</body>
</html>
