<?php /* public guest page */ ?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About | PetVet</title>
  <link rel="stylesheet" href="/PETVET/public/css/guest/navbar.css">
  <link rel="stylesheet" href="/PETVET/public/css/guest/about.css">
</head>
<body>


  <?php require_once 'navbar.php' ?>
  <section class="about-hero" aria-label="About PetVet">
    <div class="overlay" aria-hidden="true"></div>
    <div class="hero-inner container">
      <h1 class="hero-title">About PetVet</h1>
      <p class="hero-subtitle">We connect pet owners, veterinarians, and clinics with modern tools that make pet healthcare simple, trusted, and accessible.</p>
      <div class="cta-row">
        <button class="btn primary" onclick="window.location.href='/PETVET/register/client-reg.php'">Get Started</button>
        <button class="btn outline" onclick="window.location.href='/PETVET/index.php?module=guest&page=contact'">Contact Us</button>
      </div>
    </div>
  </section>
  
  <div class="container">

    <!-- Quick Stats -->
    <section class="stats">
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat">10k+</div>
          <div class="label">Pet Owners</div>
        </div>
        <div class="stat-card">
          <div class="stat">500+</div>
          <div class="label">Vets & Clinics</div>
        </div>
        <div class="stat-card">
          <div class="stat">1M+</div>
          <div class="label">Health Records</div>
        </div>
        <div class="stat-card">
          <div class="stat">24/7</div>
          <div class="label">Secure Access</div>
        </div>
      </div>
    </section>

    <!-- Who We Are -->
    <section class="info-box">
      <h2>Who We Are</h2>
      <p>
        We are a passionate team committed to providing innovative pet care solutions. 
        Our platform connects pet owners with trusted veterinary, grooming, and training services — 
        all in one place. We believe in simplifying pet care and enhancing the lives of animals and their owners.
      </p>
    </section>

    <!-- Why PetVet (Features) -->
    <section class="info-box features">
      <h2>Why PetVet</h2>
      <div class="features-grid">
        <div class="feature-card">
          <div class="icon">💙</div>
          <h3>Owner-first Experience</h3>
          <p>Easy appointment booking, reminders, and complete medical history for every pet.</p>
        </div>
        <div class="feature-card">
          <div class="icon">🩺</div>
          <h3>Tools for Vets</h3>
          <p>Digital records, case timelines, and practice-friendly scheduling to reduce admin time.</p>
        </div>
        <div class="feature-card">
          <div class="icon">🧪</div>
          <h3>Integrated Labs</h3>
          <p>Attach lab results to visits and keep everything in one secure place.</p>
        </div>
        <div class="feature-card">
          <div class="icon">🔐</div>
          <h3>Privacy & Security</h3>
          <p>Strong safeguards to protect sensitive data and only share with consent.</p>
        </div>
        <div class="feature-card">
          <div class="icon">⚡</div>
          <h3>Fast & Reliable</h3>
          <p>Responsive, mobile-friendly, and designed for real-world clinic workloads.</p>
        </div>
        <div class="feature-card">
          <div class="icon">🤝</div>
          <h3>Community Support</h3>
          <p>Work together with trusted professionals to give pets the best possible care.</p>
        </div>
      </div>
    </section>

    <!-- Vision & Mission -->
    <section class="info-box">
      <h2>Our Vision & Mission</h2>
      <p><strong>Vision:</strong> To become the most trusted and comprehensive digital platform for pet care services.</p>
      <p><strong>Mission:</strong> Empower pet owners through technology by offering reliable, convenient, and high-quality services that ensure the well-being of pets everywhere.</p>
    </section>

    <!-- Core Values -->
    <section class="info-box">
      <h2>Core Values</h2>
      <div class="table-wrapper">
        <table class="values-table">
          <thead>
            <tr>
              <th>Value</th>
              <th>Description</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Compassion</td>
              <td>Treat every animal and pet owner with care and empathy.</td>
            </tr>
            <tr>
              <td>Trust</td>
              <td>Ensure safety and reliability in every service we offer.</td>
            </tr>
            <tr>
              <td>Innovation</td>
              <td>Leverage technology to simplify and enhance pet care.</td>
            </tr>
            <tr>
              <td>Accessibility</td>
              <td>Provide a user-friendly experience accessible to all pet lovers.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <!-- Our Team -->
    <section class="info-box">
      <h2>Meet Our Team</h2>
      <div class="team">
        <div class="team-member">
          <img loading="lazy" src="https://images.unsplash.com/photo-1556157382-97eda2d62296?q=80&w=320&auto=format&fit=crop" alt="Team member portrait 1">
          <h3>Aminda Perera</h3>
          <p>Project Lead</p>
        </div>
        <div class="team-member">
          <img loading="lazy" src="https://images.unsplash.com/photo-1547425260-76bcadfb4f2c?q=80&w=320&auto=format&fit=crop" alt="Team member portrait 2">
          <h3>Ravindu Silva</h3>
          <p>Backend Developer</p>
        </div>
        <div class="team-member">
          <img loading="lazy" src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?q=80&w=320&auto=format&fit=crop" alt="Team member portrait 3">
          <h3>Sachini Fernando</h3>
          <p>UI/UX Designer</p>
        </div>
        <div class="team-member">
          <img loading="lazy" src="https://images.unsplash.com/photo-1547425260-76bcadfb4f2c?q=80&w=320&auto=format&fit=crop" alt="Team member portrait 4">
          <h3>Nipun Jayasuriya</h3>
          <p>Frontend Developer</p>
        </div>
      </div>
    </section>

    <!-- Closing CTA -->
    <section class="info-box cta-box">
      <h2>Ready to get started?</h2>
      <p>Join PetVet today and keep all your pet’s care in one place.</p>
      <div class="cta-row">
        <button class="btn primary" onclick="window.location.href='/PETVET/register/client-reg.php'">Create an account</button>
      </div>
    </section>

  </div>

  <!-- Footer 
  <footer>
    <div class="footer-container">
      <p>&copy; 2025 PetVet. All rights reserved.</p>
      <div class="footer-links">
        <a href="#">Privacy Policy</a>
        <a href="#">Terms</a>
        <a href="#">Contact Us</a>
      </div>
      <div class="social-icons">
        <a href="#"><img src="facebook-icon.png" alt="Facebook"></a>
        <a href="#"><img src="twitter-icon.png" alt="Twitter"></a>
        <a href="#"><img src="instagram-icon.png" alt="Instagram"></a>
      </div>
    </div>
  </footer>-->

</body>
</html>