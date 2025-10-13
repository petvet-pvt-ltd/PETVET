<?php /* public guest page */ ?>

<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pet Adoption | PetVet</title>
  <link rel="stylesheet" href="/PETVET/public/css/guest/navbar.css">
  <link rel="stylesheet" href="/PETVET/public/css/guest/adoption.css">
</head>

<body>


  <?php require_once 'navbar.php' ?>
  
  <div class="hero-banner">
    <div class="overlay"></div>
    <img src="https://images.unsplash.com/photo-1450778869180-41d0601e046e?auto=format&fit=crop&w=1950&q=80" alt="Dogs and cats together" class="background-image" />
    <div class="hero-content">
      <h1>Welcome to our Adoptions Page!</h1>
      <p>Find your perfect furry companion and give them a forever home</p>
  <button type="button" id="openAdoptionFormBtn" class="cta-primary">Add a pet for adoption</button>
    </div>
  </div>

  <h1 style="text-align: center;">About our Dogs and Cats</h1>

  <section class="intro">
    <div class="intro-container">
      <p>
        Our adoption process is designed to match you with the perfect companion for your lifestyle. All our pets are thoroughly examined by veterinarians, vaccinated, microchipped, and spayed/neutered before adoption.
      </p>
      <div class="features">
        <div class="feature">
          <div class="icon">❤️</div>
          <h3>With Love</h3>
          <p>Every pet is cared for with love and attention while they wait for their forever home.</p>
        </div>
        <div class="feature">
          <div class="icon">🛡️</div>
          <h3>Health Guaranteed</h3>
          <p>All pets are vaccinated, microchipped, and receive a thorough health check.</p>
        </div>
        <div class="feature">
          <div class="icon">🏠</div>
          <h3>Perfect Match</h3>
          <p>We help you find the perfect companion that fits your lifestyle and home.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="pets-section">
    <h2>Meet Our Pets</h2>

    <div class="pet-category">
      <h3><span class="tag2">Puppies</span> Find Your Perfect Puppy</h3>
      <div class="pet-cards">
        <div class="pet-card">
          <img src="/PETVET/views/shared/images/dog2.png" />
          <h4>Sheeba</h4>
          <p>1 year | Male</p>
          <p>📍 Kaduwela</p>
          <button onclick="window.location.href='/PETVET/index.php?module=guest&page=petprofile'">View Profile</button>
        </div>
        <div class="pet-card">
          <img src="/PETVET/views/shared/images/pup2.png"/>
          <h4>Rover</h4>
          <p>6 mo | Male</p>
          <p>📍 Piliyandala</p>
          <button onclick="window.location.href='/PETVET/index.php?module=guest&page=petprofile'">View Profile</button>
        </div>
        <div class="pet-card">
          <img src="/PETVET/views/shared/images/pup1.png"/>
          <h4>Bruno</h4>
          <p>6 mo | Female</p>
          <p>📍 Kelaniya</p>
          <button onclick="window.location.href='/PETVET/index.php?module=guest&page=petprofile'">View Profile</button>
        </div>
        <div class="pet-card">
          <img src="/PETVET/views/shared/images/pup4.png"/>
          <h4>Ruby</h4>
          <p>3 mo | Male</p>
          <p>📍 Kandy</p>
          <button onclick="window.location.href='/PETVET/index.php?module=guest&page=petprofile'">View Profile</button>
        </div>
        <div class="pet-card">
          <img src="/PETVET/views/shared/images/pupn.png"/>
          <h4>Charlie</h4>
          <p>8 mo | Male</p>
          <p>📍 Nugegoda</p>
          <button onclick="window.location.href='/PETVET/index.php?module=guest&page=petprofile'">View Profile</button>
        </div>
      </div>
    </div>

    <div class="pet-category">
      <h3><span class="tag1">Adult Dogs</span> Loyal Companions</h3>
      <div class="pet-cards">
        <div class="pet-card">
          <img src="/PETVET/views/shared/images/ad1.png"/>
          <h4>Luna</h4>
          <p>3 years | Female</p>
          <p>📍 Rajagiriye</p>
          <button onclick="window.location.href='/PETVET/index.php?module=guest&page=petprofile'">View Profile</button>
        </div>
        <div class="pet-card">
          <img src="/PETVET/views/shared/images/ad2.png"/>
          <h4>Tina</h4>
          <p>6 years | Female</p>
          <p>📍 Kottawa</p>
          <button onclick="window.location.href='/PETVET/index.php?module=guest&page=petprofile'">View Profile</button>
        </div>
        <div class="pet-card">
          <img src="/PETVET/views/shared/images/ad3.png"/>
          <h4>Scoob</h4>
          <p>5 years | Male</p>
          <p>📍 Kolonnawa</p>
          <button onclick="window.location.href='/PETVET/index.php?module=guest&page=petprofile'">View Profile</button>
        </div>
        <div class="pet-card">
          <img src="/PETVET/views/shared/images/ad4.png"/>
          <h4>Maddy</h4>
          <p>2 years | Male</p>
          <p>📍 Delgoda</p>
          <button onclick="window.location.href='/PETVET/index.php?module=guest&page=petprofile'">View Profile</button>
        </div>
        <div class="pet-card">
          <img src="/PETVET/views/shared/images/ad5.png"/>
          <h4>Tommy</h4>
          <p>8 years | Male</p>
          <p>📍 Kaluthara</p>
          <button onclick="window.location.href='/PETVET/index.php?module=guest&page=petprofile'">View Profile</button>
        </div>
      </div>
    </div>

    <div class="pet-category">
      <h3><span class="tag3">Kittens</span> Playful Furballs</h3>
      <div class="pet-cards">
        <div class="pet-card">
          <img src="/PETVET/views/shared/images/kit1.png"/>
          <h4>Jim</h4>
          <p>1 year | Female</p>
          <p>📍 Colombo</p>
          <button onclick="window.location.href='/PETVET/index.php?module=guest&page=petprofile'">View Profile</button>
        </div>
        <div class="pet-card">
          <img src="/PETVET/views/shared/images/kit2.png"/>
          <h4>Gary</h4>
          <p>2 years | Male</p>
          <p>📍 Kotahena</p>
          <button onclick="window.location.href='/PETVET/index.php?module=guest&page=petprofile'">View Profile</button>
        </div>
        <div class="pet-card">
          <img src="/PETVET/views/shared/images/kit3.png"/>
          <h4>Sisi</h4>
          <p>2 years | Female</p>
          <p>📍 Kadawatha</p>
          <button onclick="window.location.href='/PETVET/index.php?module=guest&page=petprofile'">View Profile</button>
        </div>
      </div>
    </div>

    <div class="pet-category">
      <h3><span class="tag4">Cats</span> Elegant Feline Friends</h3>
      <div class="pet-cards">
        <div class="pet-card">
          <img src="/PETVET/views/shared/images/cat1.png"/>
          <h4>Shadow</h4>
          <p>3 years | Female</p>
          <p>📍 Rathnapura</p>
          <button onclick="window.location.href='/PETVET/index.php?module=guest&page=petprofile'">View Profile</button>
        </div>
        <div class="pet-card">
          <img src="/PETVET/views/shared/images/cat2.png"/>
          <h4>Simba</h4>
          <p>4 years | Female</p>
          <p>📍 Kosgoda</p>
          <button onclick="window.location.href='/PETVET/index.php?module=guest&page=petprofile'">View Profile</button>
        </div>
        <div class="pet-card">
          <img src="/PETVET/views/shared/images/cat3.png"/>
          <h4>Cleo</h4>
          <p>3 years | Male</p>
          <p>📍 Kaduwela</p>
          <button onclick="window.location.href='petprofile.php'">View Profile</button>
        </div>
        <div class="pet-card">
          <img src="/PETVET/views/shared/images/cat4.png"/>
          <h4>Mino</h4>
          <p>2 years | Male</p>
          <p>📍 Malabe</p>
          <button onclick="window.location.href='petprofile.php'">View Profile</button>
        </div>
        <div class="pet-card">
          <img src="/PETVET/views/shared/images/cat5.png"/>
          <h4>Mimmy</h4>
          <p>5 years | Male</p>
          <p>📍 Angoda</p>
          <button onclick="window.location.href='petprofile.php'">View Profile</button>
        </div>
      </div>
    </div>
  </section>


  <!-- Modal: Adoption Form -->
  <div id="adoptionModal" class="modal" aria-hidden="true" role="dialog" aria-labelledby="adoptionModalTitle">
    <div class="modal__dialog">
      <section class="adoption-form">
        <button type="button" class="modal__close" aria-label="Close">&times;</button>
        <h2 id="adoptionModalTitle">Want to Help Someone Get Adopted?</h2>
        <p>If you have a pet that needs a new home, fill out this form and we'll list them here.</p>
        <form>
          <div class="grid">
            <input type="text" placeholder="Pet Name" required />
            <select required>
              <option>Dog</option>
              <option>Cat</option>
              <option>Other</option>
            </select>
          </div>
          <div class="grid">
            <input type="text" placeholder="Age (e.g., 2 yrs, 4 months)" required />
            <input type="text" placeholder="Location " required />
          </div>
          <textarea rows="4" placeholder="Pet description, personality, habits, etc." required></textarea>
          <label>Upload Photos</label>
          <input type="file" multiple accept="image/*" />
          <h4>Contact Information</h4>
          <div class="grid">
            <input type="text" placeholder="Your Name" required />
            <input type="email" placeholder="Email" required />
          </div>
          <input type="tel" placeholder="Phone Number" />
          <button type="submit">Submit Pet for Adoption</button>
        </form>
      </section>
    </div>
  </div>

  <script>
    // Open/close modal helpers
    (function() {
      const openBtn = document.getElementById('openAdoptionFormBtn');
      const modal = document.getElementById('adoptionModal');
      const closeBtn = modal.querySelector('.modal__close');

      const open = () => {
        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');
        // focus first field for accessibility
        const firstInput = modal.querySelector('input, select, textarea, button');
        if (firstInput) setTimeout(() => firstInput.focus(), 50);
      };

      const close = () => {
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-open');
        openBtn && openBtn.focus();
      };

      openBtn && openBtn.addEventListener('click', open);
      closeBtn && closeBtn.addEventListener('click', close);
      modal.addEventListener('click', (e) => {
        if (e.target === modal) close();
      });
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.classList.contains('open')) close();
      });
    })();
  </script>

</body>
</html>
