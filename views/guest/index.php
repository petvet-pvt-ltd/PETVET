<?php
require_once 'redirectorToLoggedUser.php';
?>

<html lang="en">
<head>
    <title>Home | PetVet</title>
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/home.css">
    
</head>
<body>
<script src="redirectorToLoggedUser.js"></script>       <!-- redirecting javascript file -->

    <?php require_once 'navbar.php' ?>

    <video autoplay muted loop class="background-video">
        <source src="videos/petvet-home-video.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    <section class="pet-owner-section">
  <div class="container">
    <div class="content-wrapper">
      <!-- Left side - Image -->
      <div class="image-section">
        <img
          src="images/petowner.jpg"
          alt="Puppy with stethoscope"
          class="pet-image"
        />
      </div>
      <!-- Right side - Content -->
      <div class="text-section">
        <h2 class="title">Do you have a pet?</h2>
        <p class="paragraph">
          At PetVet, we believe our customers view their animal friends as
          extensions of their family. This is why our suite of services,
          including wellness programmes and disease diagnosis and treatment,
          are designed to connect, care and cure your companions.
        </p>
        <p class="paragraph">
          We offer the following services for companion (including equines)
          and exotic animals. These include wellness programs as well as
          disease diagnosis and treatment, including surgery, dentistry and
          diagnostic imaging (X-Ray/Scan).
        </p>
        <div class="button-wrapper">
          <button class="register-btn" onclick="window.location.href='register/client-reg.php'">Register as an owner</button>
        </div>
      </div>
    </div>
  </div>
</section>


   <section class="veterinarian-section">
  <div class="container">
    <div class="content-wrapper reverse">
      <!-- Right side - Image -->
      <div class="image-section">
        <img
          src="images/vet.jpg"
          alt="Veterinarian examining a pet"
          class="vet-image"
        />
      </div>
      <!-- Left side - Content -->
      <div class="text-section">
        <h2 class="title">Are you a veterinarian?</h2>
        <p class="paragraph">
          Join our network of dedicated veterinary professionals who are
          revolutionizing pet healthcare through digital innovation. PetVet
          provides you with the tools to streamline your practice, connect
          with pet owners, and deliver exceptional care.
        </p>
        <p class="paragraph">
          Our platform offers digital patient records, telemedicine
          capabilities, appointment scheduling, and a professional network
          to consult with specialists. Focus more on treating animals and
          less on administrative tasks.
        </p>
        <div class="button-wrapper">
          <button class="join-btn" onclick="window.location.href='register/vet-reg.php'">Join as a veterinarian</button>
        </div>
      </div>
    </div>
  </div>
</section>


   <section class="service-provider-section">
  <div class="container">
    <div class="content-wrapper">
      <!-- Left side - Image -->
      <div class="image-section">
        <img
          src="images/petser.jpg"
          alt="Pet groomer working with a dog"
          class="service-image"
        />
      </div>
      <!-- Right side - Content -->
      <div class="text-section">
        <h2 class="title">Do you provide pet services?</h2>
        <p class="paragraph">
          Whether you're a groomer, trainer, pet sitter, or dog walker,
          PetVet helps you grow your business by connecting you directly
          with pet owners looking for quality services for their beloved
          companions.
        </p>
        <p class="paragraph">
          Our platform streamlines booking, payments, and client
          communication so you can focus on what you do best. Build your
          reputation with verified reviews and showcase your expertise to a
          community of dedicated pet owners.
        </p>
        <div class="button-wrapper">
          <button class="register-btn">Register your service</button>
        </div>
      </div>
    </div>
  </div>
</section>


  <section class="vet-clinic-section">
  <div class="container">
    <div class="content-wrapper reverse">
      <!-- Right side - Image -->
      <div class="image-section">
        <img
          src="images/vetclinic2.jpg"
          alt="Modern veterinary clinic"
          class="clinic-image"
        />
      </div>
      <!-- Left side - Content -->
      <div class="text-section">
        <h2 class="title">Do you manage a vet clinic?</h2>
        <p class="paragraph">
          Transform your veterinary practice with PetVet's comprehensive
          clinic management system. Digitize operations from appointment
          scheduling to medical records, inventory management, and client
          communications.
        </p>
        <p class="paragraph">
          Our platform helps you increase efficiency, improve patient care,
          and grow your practice. Access analytics to track performance,
          manage staff schedules, and integrate with laboratory services—all
          in one secure, HIPAA-compliant solution.
        </p>
        <div class="button-wrapper">
          <button class="partner-btn" onclick="window.location.href='register/clinic-manager-reg.php'">Partner with us</button>
        </div>
      </div>
    </div>
  </div>
</section>
</body>
</html>