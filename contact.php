<?php
require_once 'redirectorToLoggedUser.php';
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/contact.css">
</head>
<body>
  <?php require_once 'navbar.php' ?>

</body>
<script src="redirectorToLoggedUser.js"></script>       <!-- redirecting javascript file -->

 <section class="hero">
    <h1>Contact Us</h1>
    <p>We'd love to hear from you! Reach out with any questions, feedback, or pet emergencies.</p>
  </section>

  <main class="contact-wrapper">
    <section class="contact-info">
      <h2> Contact Details</h2>
      <div class="info-block">
        <h4> Address</h4>
        <p>98/4, Havelock Road,<br>Colombo 05, Sri Lanka<br><a href="#">(see location)</a></p>
      </div>
      <div class="info-block">
        <h4> Phone</h4>
        <p>+94 11 259 9799 / +94 11 259 9800 (8.30AM–9PM)<br>Emergency: <strong>+94 777 738 838</strong></p>
      </div>
      <div class="info-block">
        <h4> Email</h4>
        <p><a href="mailto:info@petvet.lk">info@petvet.lk</a></p>
      </div>
      <div class="info-block">
        <h4> Office Hours</h4>
        <p>Mon – Sun: 8:30 AM – 9:00 PM</p>
      </div>
    </section>

    <section class="contact-form">
      <h2> Send Us a Message</h2>
      <form>
        <div class="form-grid">
          <input type="text" placeholder="Your name" required />
          <input type="email" placeholder="Your email" required />
        </div>
        <textarea placeholder="Your message" required></textarea>
        <label class="checkbox">
          <input type="checkbox" required /> I agree to the data handling terms outlined in our <a href="#">Privacy Policy</a>.
        </label>
        <button type="submit">Send Message</button>
      </form>
    </section>
  </main>

</body>

</html>