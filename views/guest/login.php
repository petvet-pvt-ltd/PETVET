<?php 
/* Login page with authentication */
require_once __DIR__ . '/../../config/auth_helper.php';

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
    redirectToDashboard();
}

$error = '';
$success = '';

// Check for logout success message
if (isset($_GET['logout'])) {
    $success = 'You have been logged out successfully.';
}

// Check for registration success message
if (isset($_GET['registered'])) {
    $success = 'Registration successful! You can now login.';
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login-submit'])) {
    // Clear any URL-based messages when form is submitted
    $success = '';
    
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password';
    } else {
        $result = auth()->login($email, $password);
        
        if ($result['success']) {
            // Check if there's a redirect URL saved
            $redirect = $_SESSION['redirect_after_login'] ?? $result['redirect'];
            unset($_SESSION['redirect_after_login']);
            
            header('Location: ' . $redirect);
            exit;
        } else {
            $error = $result['message'];
        }
    }
}
?>

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
  <style>
    .alert {
      padding: 12px 16px;
      margin-bottom: 20px;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 500;
    }
    .alert-error {
      background: #fee;
      color: #c33;
      border: 1px solid #fcc;
    }
    .alert-success {
      background: #efe;
      color: #363;
      border: 1px solid #cfc;
    }
    
    /* Home Button Styles */
    .home-btn {
      position: fixed;
      top: 20px;
      left: 20px;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 20px;
      background: #fff;
      color: #333;
      text-decoration: none;
      border-radius: 8px;
      font-weight: 500;
      font-size: 14px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
      z-index: 1000;
    }
    
    .home-btn:hover {
      background: #f8f9fa;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      transform: translateY(-2px);
    }
    
    .home-btn svg {
      width: 20px;
      height: 20px;
    }
    
    @media (max-width: 768px) {
      .home-btn {
        top: 15px;
        left: 15px;
        padding: 8px 16px;
        font-size: 13px;
      }
      
      .home-btn svg {
        width: 18px;
        height: 18px;
      }
    }
  </style>
</head>

<body>
  <!-- Home Button -->
  <a href="/PETVET/index.php?module=guest&page=home" class="home-btn">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
      <polyline points="9 22 9 12 15 12 15 22"></polyline>
    </svg>
    Home
  </a>
  
  <div class="auth-wrapper">
    <aside class="auth-hero">
      <div class="hero-overlay"></div>
      <div class="hero-content">
        <h2>Your pet's health, managed beautifully.</h2>
        <p>Book appointments, view records, and connect with top vets and other pet services in one place.</p>
      </div>
    </aside>
    <main class="auth-panel">
      <div class="auth-card">
        <img class="brand" src="/PETVET/views/shared/images/petvet-logo-black.png" alt="PETVET Logo">
        <h1>Login</h1>
        <p class="subtitle">Welcome back! Please login to your PETVET account.</p>
        
        <?php if ($error): ?>
          <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
          <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <form method="post" novalidate>
          <label class="field">
            <img class="icon" src="/PETVET/views/shared/images/mail.png" alt="Email Icon">
            <input type="email" name="email" placeholder="Email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
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
        <div class="signup">Don't have an account? <a href="/PETVET/index.php?module=guest&page=register">Sign up</a></div>
      </div>
    </main>
  </div>
</body>
</html>
