<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About | PetVet</title>
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/about.css">
</head>
<body>
  <?php require_once 'navbar.php' ?>
  <header>
    <h1>About Us</h1>
    <p>Learn more about our identity, purpose, and the principles that guide us.</p>
  </header>

  <div class="coverphoto">
    <img src="images/about-pets.png" alt="">
  </div>
  
  <div class="container">

    <!-- Who We Are -->
    <section class="info-box">
      <h2>Who We Are</h2>
      <p>
        We are a passionate team committed to providing innovative pet care solutions. 
        Our platform connects pet owners with trusted veterinary, grooming, and training services — 
        all in one place. We believe in simplifying pet care and enhancing the lives of animals and their owners.
      </p>
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
          <img src="team1.jpg" alt="Team Member 1">
          <h3>Aminda Perera</h3>
          <p>Project Lead</p>
        </div>
        <div class="team-member">
          <img src="team2.jpg" alt="Team Member 2">
          <h3>Ravindu Silva</h3>
          <p>Backend Developer</p>
        </div>
        <div class="team-member">
          <img src="team3.jpg" alt="Team Member 3">
          <h3>Sachini Fernando</h3>
          <p>UI/UX Designer</p>
        </div>
        <div class="team-member">
          <img src="team4.jpg" alt="Team Member 4">
          <h3>Nipun Jayasuriya</h3>
          <p>Frontend Developer</p>
        </div>
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