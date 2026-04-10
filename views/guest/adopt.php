<?php 
/* public guest page */ 
// Data provided by controller: $dogs, $cats, $birds, $other
$dogs = $GLOBALS['dogs'] ?? [];
$cats = $GLOBALS['cats'] ?? [];
$birds = $GLOBALS['birds'] ?? [];
$other = $GLOBALS['other'] ?? [];
?>

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
    </div>
  </div>

  <section class="intro">
    <div class="intro-container">
      <p>
        Our adoption process is designed to match you with the perfect companion for your lifestyle. All pets listed here have been submitted by loving owners looking to find them forever homes.
      </p>
      <div class="features">
        <div class="feature">
          <div class="icon">❤️</div>
          <h3>With Love</h3>
          <p>Every pet is looking for a loving home where they can thrive and be happy.</p>
        </div>
        <div class="feature">
          <div class="icon">🏠</div>
          <h3>Perfect Match</h3>
          <p>We help you find the perfect companion that fits your lifestyle and home.</p>
        </div>
        <div class="feature">
          <div class="icon">🤝</div>
          <h3>Direct Contact</h3>
          <p>Connect directly with pet owners to learn more and arrange a meet-and-greet.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="pets-section">
    <h2>Meet Our Pets Available for Adoption</h2>

    <!-- DOGS SECTION -->
    <?php if (!empty($dogs)): ?>
    <div class="pet-category">
      <h3><span class="tag2">🐕 Dogs</span> Find Your Perfect Canine Friend</h3>
      <div class="pet-cards">
        <?php foreach ($dogs as $dog): 
          $mainImage = !empty($dog['images']) ? $dog['images'][0] : '/PETVET/public/images/placeholder-pet.jpg';
          $age = htmlspecialchars($dog['age'] ?? 'Unknown');
          $gender = ucfirst(htmlspecialchars($dog['gender'] ?? ''));
        ?>
          <div class="pet-card" data-pet-id="<?= $dog['id'] ?>">
            <img src="<?= htmlspecialchars($mainImage) ?>" alt="<?= htmlspecialchars($dog['name']) ?>" />
            <h4><?= htmlspecialchars($dog['name']) ?></h4>
            <p><?= htmlspecialchars($dog['breed'] ?? 'Mixed') ?></p>
            <p><?= $age ?> • <?= $gender ?></p>
            <p>📍 <?= htmlspecialchars($dog['location'] ?? 'Location not specified') ?></p>
            <button class="view-details-btn" onclick="showPetDetails(<?= $dog['id'] ?>)">View Details</button>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php else: ?>
    <div class="pet-category">
      <h3><span class="tag2">🐕 Dogs</span> Find Your Perfect Canine Friend</h3>
      <p class="empty-message">No dogs available for adoption at the moment. Check back soon!</p>
    </div>
    <?php endif; ?>

    <!-- CATS SECTION -->
    <?php if (!empty($cats)): ?>
    <div class="pet-category">
      <h3><span class="tag4">🐈 Cats</span> Elegant Feline Friends</h3>
      <div class="pet-cards">
        <?php foreach ($cats as $cat): 
          $mainImage = !empty($cat['images']) ? $cat['images'][0] : '/PETVET/public/images/placeholder-pet.jpg';
          $age = htmlspecialchars($cat['age'] ?? 'Unknown');
          $gender = ucfirst(htmlspecialchars($cat['gender'] ?? ''));
        ?>
          <div class="pet-card" data-pet-id="<?= $cat['id'] ?>">
            <img src="<?= htmlspecialchars($mainImage) ?>" alt="<?= htmlspecialchars($cat['name']) ?>" />
            <h4><?= htmlspecialchars($cat['name']) ?></h4>
            <p><?= htmlspecialchars($cat['breed'] ?? 'Mixed') ?></p>
            <p><?= $age ?> • <?= $gender ?></p>
            <p>📍 <?= htmlspecialchars($cat['location'] ?? 'Location not specified') ?></p>
            <button class="view-details-btn" onclick="showPetDetails(<?= $cat['id'] ?>)">View Details</button>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php else: ?>
    <div class="pet-category">
      <h3><span class="tag4">🐈 Cats</span> Elegant Feline Friends</h3>
      <p class="empty-message">No cats available for adoption at the moment. Check back soon!</p>
    </div>
    <?php endif; ?>

    <!-- BIRDS SECTION -->
    <?php if (!empty($birds)): ?>
    <div class="pet-category">
      <h3><span class="tag1">🐦 Birds</span> Feathered Companions</h3>
      <div class="pet-cards">
        <?php foreach ($birds as $bird): 
          $mainImage = !empty($bird['images']) ? $bird['images'][0] : '/PETVET/public/images/placeholder-pet.jpg';
          $age = htmlspecialchars($bird['age'] ?? 'Unknown');
          $gender = ucfirst(htmlspecialchars($bird['gender'] ?? ''));
        ?>
          <div class="pet-card" data-pet-id="<?= $bird['id'] ?>">
            <img src="<?= htmlspecialchars($mainImage) ?>" alt="<?= htmlspecialchars($bird['name']) ?>" />
            <h4><?= htmlspecialchars($bird['name']) ?></h4>
            <p><?= htmlspecialchars($bird['breed'] ?? 'Unknown breed') ?></p>
            <p><?= $age ?> • <?= $gender ?></p>
            <p>📍 <?= htmlspecialchars($bird['location'] ?? 'Location not specified') ?></p>
            <button class="view-details-btn" onclick="showPetDetails(<?= $bird['id'] ?>)">View Details</button>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php else: ?>
    <div class="pet-category">
      <h3><span class="tag1">🐦 Birds</span> Feathered Companions</h3>
      <p class="empty-message">No birds available for adoption at the moment. Check back soon!</p>
    </div>
    <?php endif; ?>

    <!-- OTHER PETS SECTION -->
    <?php if (!empty($other)): ?>
    <div class="pet-category">
      <h3><span class="tag3">🐾 Other Pets</span> Unique Companions</h3>
      <div class="pet-cards">
        <?php foreach ($other as $pet): 
          $mainImage = !empty($pet['images']) ? $pet['images'][0] : '/PETVET/public/images/placeholder-pet.jpg';
          $age = htmlspecialchars($pet['age'] ?? 'Unknown');
          $gender = ucfirst(htmlspecialchars($pet['gender'] ?? ''));
        ?>
          <div class="pet-card" data-pet-id="<?= $pet['id'] ?>">
            <img src="<?= htmlspecialchars($mainImage) ?>" alt="<?= htmlspecialchars($pet['name']) ?>" />
            <h4><?= htmlspecialchars($pet['name']) ?></h4>
            <p><?= htmlspecialchars($pet['species']) ?> • <?= htmlspecialchars($pet['breed'] ?? 'Unknown breed') ?></p>
            <p><?= $age ?> • <?= $gender ?></p>
            <p>📍 <?= htmlspecialchars($pet['location'] ?? 'Location not specified') ?></p>
            <button class="view-details-btn" onclick="showPetDetails(<?= $pet['id'] ?>)">View Details</button>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php else: ?>
    <div class="pet-category">
      <h3><span class="tag3">🐾 Other Pets</span> Unique Companions</h3>
      <p class="empty-message">No other pets available for adoption at the moment. Check back soon!</p>
    </div>
    <?php endif; ?>

    <?php if (empty($dogs) && empty($cats) && empty($birds) && empty($other)): ?>
    <div class="empty-all">
      <p>😊 Currently no pets are available for adoption. Please check back soon!</p>
    </div>
    <?php endif; ?>
  </section>

  <!-- Pet Details Modal -->
  <div id="petDetailsModal" class="modal" aria-hidden="true" role="dialog">
    <div class="modal__dialog modal__dialog--large">
      <button type="button" class="modal__close" aria-label="Close">&times;</button>
      <div id="petDetailsContent">
        <!-- Content will be loaded dynamically -->
      </div>
    </div>
  </div>

  <script>
    // Store all pets data for quick access
    const petsData = {
      <?php 
      $allPets = array_merge($dogs, $cats, $birds, $other);
      foreach ($allPets as $index => $pet): 
      ?>
      <?= $pet['id'] ?>: <?= json_encode($pet) ?><?= $index < count($allPets) - 1 ? ',' : '' ?>
      <?php endforeach; ?>
    };

    function showPetDetails(petId) {
      const pet = petsData[petId];
      if (!pet) {
        alert('Pet details not found');
        return;
      }

      const modal = document.getElementById('petDetailsModal');
      const content = document.getElementById('petDetailsContent');
      
      // Build images HTML
      let imagesHTML = '';
      if (pet.images && pet.images.length > 0) {
        imagesHTML = `
          <div class="pet-images">
            <img src="${pet.images[0]}" alt="${pet.name}" class="main-image" id="mainPetImage" />
            ${pet.images.length > 1 ? `
              <div class="thumbnail-images">
                ${pet.images.map((img, idx) => 
                  `<img src="${img}" alt="Photo ${idx + 1}" class="thumbnail" onclick="changeMainImage('${img}')" />`
                ).join('')}
              </div>
            ` : ''}
          </div>
        `;
      } else {
        imagesHTML = `<div class="pet-images"><img src="/PETVET/public/images/placeholder-pet.jpg" alt="${pet.name}" class="main-image" /></div>`;
      }

      content.innerHTML = `
        <div class="pet-details">
          ${imagesHTML}
          <div class="pet-info">
            <h2>${pet.name}</h2>
            <div class="pet-meta">
              <span><strong>Species:</strong> ${pet.species}</span>
              <span><strong>Breed:</strong> ${pet.breed || 'Mixed'}</span>
              <span><strong>Age:</strong> ${pet.age}</span>
              <span><strong>Gender:</strong> ${pet.gender}</span>
            </div>
            ${pet.description ? `<div class="pet-description"><h3>About ${pet.name}</h3><p>${pet.description}</p></div>` : ''}
            <div class="pet-location">
              <strong>📍 Location:</strong> ${pet.location || 'Not specified'}
            </div>
            <div class="contact-info">
              <h3>Contact Owner</h3>
              <p><strong>Name:</strong> ${pet.owner_name || 'Not provided'}</p>
              ${pet.phone ? `<p><strong>Phone:</strong> <a href="tel:${pet.phone}">${pet.phone}</a></p>` : ''}
              ${pet.phone2 ? `<p><strong>Alternative Phone:</strong> <a href="tel:${pet.phone2}">${pet.phone2}</a></p>` : ''}
              ${pet.email ? `<p><strong>Email:</strong> <a href="mailto:${pet.email}">${pet.email}</a></p>` : ''}
            </div>
          </div>
        </div>
      `;

      modal.classList.add('open');
      modal.setAttribute('aria-hidden', 'false');
      document.body.classList.add('modal-open');
    }

    function changeMainImage(src) {
      const mainImg = document.getElementById('mainPetImage');
      if (mainImg) {
        mainImg.src = src;
      }
    }

    // Modal close handlers
    (function() {
      const modal = document.getElementById('petDetailsModal');
      const closeBtn = modal?.querySelector('.modal__close');

      const close = () => {
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-open');
      };

      closeBtn && closeBtn.addEventListener('click', close);
      modal && modal.addEventListener('click', (e) => {
        if (e.target === modal) close();
      });
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal && modal.classList.contains('open')) close();
      });
    })();
  </script>

</body>
</html>
