<?php /* public guest page */ ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pet Profile</title>
  <link rel="stylesheet" href="/PETVET/public/css/guest/petprofile.css"/>
</head>
<body>


  <!-- Hero Banner -->
  <section class="hero-banner">
    <div class="hero-overlay"></div>
  <img src="/PETVET/views/shared/images/pup2.png" alt="Pet Name" class="hero-image" />
    <div class="hero-content">
      <h1>Meet Rover</h1>
      <p class="status available">Quick adoption possible!</p>
    </div>
  </section>

  <div class="container">
  <a href="/PETVET/index.php?module=guest&page=adopt" class="back-link">← Back to all pets</a>

    <!-- Main Content -->
    <section class="main-content">
      <div class="gallery">
        <div class="main-image">
          <img src="/PETVET/views/shared/images/rov2.png" alt="Pet Name" />
        </div>
        <div class="thumbnails">
          <img src="/PETVET/views/shared/images/rov1.png" alt="Thumbnail" />
          <img src="/PETVET/views/shared/images/rov3.png" alt="Thumbnail" />
          <img src="/PETVET/views/shared/images/rov4.png" alt="Thumbnail" />
        </div>
      </div>

      <div class="pet-story box-section">
        <h2>Rover's story</h2>
        <p>
          Beautiful Rover is a female pup of around 1 year old. <br><br>
          She arrived at the sanctuary covered in mange. Her skin is now fully recovered. I mean.. Look at that fur!!<br><br>
          What an absolute delight and joy to be around! With her quirky character and her funky quick moves, she’ll have you doubled over as she bumps and spins around. She is a GEM! Honestly, she is beautiful both inside and out; She is a gentle girl who loves to play and cuddle. Just pure happiness is what Rover brings!<br><br>
          Rover would suit any family home. Words just don’t need to be spoken, the photos and videos say it all!<br><br>
          Rover is microchipped and has completed her vaccine course. She is now awaiting to be sterilized. She is ready to run circles around you if you are?
        </p>
        <button class="adopt-btn">Ready to Adopt Rover?</button>
      </div>
    </section>

    <!-- Pet Details -->
    <section class="pet-details box-section">
      <h2>Essential Information</h2>
      <div class="details-grid">
        <div class="detail-item">
          <div class="detail-icon">
            <img src="/PETVET/views/shared/images/paws-64.png" alt="Breed" />
          </div>
          <div class="detail-title">Type & Breed</div>
          <div class="detail-value">Mixed</div>
        </div>
        <div class="detail-item">
          <div class="detail-icon">
            <img src="/PETVET/views/shared/images/location-50.png" alt="Location" />
          </div>
          <div class="detail-title">Location</div>
          <div class="detail-value">Colombo, SL</div>
        </div>
        <div class="detail-item">
          <div class="detail-icon">
            <img src="/PETVET/views/shared/images/age-50.png" alt="Age" />
          </div>
          <div class="detail-title">Age</div>
          <div class="detail-value">1 year</div>
        </div>
        <div class="detail-item">
          <div class="detail-icon">
            <img src="/PETVET/views/shared/images/ruler-50.png" alt="Size" />
          </div>
          <div class="detail-title">Size</div>
          <div class="detail-value">Medium</div>
        </div>
        <div class="detail-item">
          <div class="detail-icon">
            <img src="/PETVET/views/shared/images/health-50.png" alt="Health" />
          </div>
          <div class="detail-title">Health</div>
          <div class="detail-value">Vaccinated, Awaiting Spay</div>
        </div>
        <div class="detail-item">
          <div class="detail-icon">
            <img src="/PETVET/views/shared/images/50.png" alt="Availability" />
          </div>
          <div class="detail-title">Available</div>
          <div class="detail-value">Available Now</div>
        </div>
      </div>
    </section>

    <!-- Contact Information -->
    <section class="contact-section box-section">
      <h2><center>Contact Information</center></h2>
      <div class="contact-content contact-flex">
        <div class="contact-person">
          <div class="contact-icon">
            <img src="/PETVET/views/shared/images/phone.png" alt="Phone Icon" />
          </div>
          <div class="contact-labels">
            
          </div>
        </div>
        <div class="contact-details">
        <h3> Piyal Perera </h3>
          <h3> 076 145 6957 </h3>

          
        </div>
      </div>
    </section>
  </div>
</body>
</html>
