<?php /* Explore Pets marketplace - Guest version matching admin dashboard */ 
if (!isset($_SESSION)) session_start();
$isLoggedIn = isset($_SESSION['user_id']);
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Pets | PetVet</title>
    <link rel="stylesheet" href="/PETVET/public/css/guest/navbar.css">
    <link rel="stylesheet" href="/PETVET/public/css/pet-owner/explore-pets.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
         integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
         crossorigin=""/>
    <style>
        /* Guest explore pets layout - no sidebar */
        body {
            overflow-x: hidden;
        }
        /* Override card colors to light blue */
        :root {
            --card: #E8F4FF;
            --gradient-card: linear-gradient(180deg, #E8F4FF 0%, #D4E9FF 100%);
            --border: #B3D9FF;
        }
        .card {
            background: #E8F4FF !important;
            border-color: #B3D9FF !important;
            transition: all 0.3s ease;
        }
        .card:hover {
            box-shadow: 0 12px 24px rgba(59, 130, 246, 0.15) !important;
            transform: translateY(-4px);
        }
        .navbar {
            z-index: 1000 !important;
            position: sticky;
        }
        .main-content {
            margin-left: 0;
            padding: 24px 28px 40px !important;
            padding-top: 20px !important;
        }
        @media (max-width: 900px) {
            .main-content {
                padding: 18px 22px 42px !important;
                padding-top: 20px !important;
            }
        }
    </style>
</head>
<body data-user-id="<?php echo $isLoggedIn ? $_SESSION['user_id'] : ''; ?>">

<?php require_once __DIR__ . '/navbar.php'; ?>

<div class="main-content">
<?php
// Data comes from the controller via GuestExplorePetsModel and GuestAdoptModel
$currentUser = $currentUser ?? ['id' => null, 'name' => 'Guest'];
$sellers = $sellers ?? [];
$pets = $pets ?? [];
$adoptionPetsBySpecies = $adoptionPetsBySpecies ?? ['dogs' => [], 'cats' => [], 'birds' => [], 'other' => []];

// Separate pets by listing type
$sellingPets = [];
$adoptingPets = [];

// Selling pets - filter what we get from controller
foreach($pets as $pet) {
  if (($pet['listing_type'] ?? 'sale') === 'sale') {
    $sellingPets[] = $pet;
  }
}

// Adoption pets - flatten from species groups
foreach($adoptionPetsBySpecies as $speciesList) {
  foreach($speciesList as $pet) {
    $adoptingPets[] = $pet;
  }
}
?>
  <header class="page-header" style="background: linear-gradient(135deg, #FFFFFF 0%, #87CEEB 100%); color: #1a1a1a; padding: 40px 28px; border-radius: 16px; margin-bottom: 32px; box-shadow: 0 10px 30px rgba(135, 206, 235, 0.2); display: flex; justify-content: space-between; align-items: center;">
    <div class="title-wrap">
      <h2 style="margin: 0 0 8px 0; font-size: 2.5rem; font-weight: 800; letter-spacing: -0.5px;">🐾 Find Your Perfect Companion</h2>
      <p class="subtitle" style="margin: 0; font-size: 1.1rem; opacity: 0.85; color: #333; font-weight: 500;">Discover adorable pets ready for loving homes. Browse our collection of pets for sale and adoption.</p>
    </div>
    <div class="header-buttons" style="display: flex; gap: 12px; align-items: center;">
      <button class="btn outline" id="btnMyAdoptionListings" style="display:none; white-space:nowrap;">My Listings</button>
      <button class="btn primary" id="btnListPet" style="display:none; white-space:nowrap;">List a Pet</button>
    </div>
  </header>

  <section class="explore-controls">
    <div class="segmented" role="tablist" aria-label="Selling or Adopting">
      <button type="button" role="tab" aria-selected="true" class="seg-btn is-active" data-view="sale">For Sale</button>
      <button type="button" role="tab" aria-selected="false" class="seg-btn" data-view="adoption">For Adoption</button>
    </div>
    <div class="filters">
      <div class="field grow"><input type="text" id="searchInput" placeholder="Search by name, breed, or seller..."></div>
      <div class="field">
        <select id="speciesFilter">
          <option value="">All Species</option><option>Dog</option><option>Cat</option><option>Bird</option><option>Other</option>
        </select>
      </div>
      <div class="field">
        <select id="sortBy">
          <option value="newest">Sort: Newest</option>
          <option value="priceLow">Price: Low → High</option>
          <option value="priceHigh">Price: High → Low</option>
          <option value="age">Age</option>
        </select>
      </div>
    </div>
  </section>

  <section id="sellingGrid" class="grid">
  <?php foreach($sellingPets as $pet): 
    $seller=$sellers[$pet['seller_id']] ?? ['name'=>'Unknown','location'=>'','phone'=>'','phone2'=>'','email'=>''];
    $images = $pet['images'] ?? [];
  ?>
    <article class="card" data-species="<?= htmlspecialchars($pet['species']) ?>" data-price="<?= (int)$pet['price'] ?>" data-pet-id="<?= $pet['id'] ?>" data-latitude="<?= $pet['latitude'] ?? '' ?>" data-longitude="<?= $pet['longitude'] ?? '' ?>" data-listing-type="<?= htmlspecialchars($pet['listing_type'] ?? 'sale') ?>">
      <div class="media">
        <?php if(!empty($images)): ?>
          <img src="<?= htmlspecialchars($images[0]) ?>" alt="<?= htmlspecialchars($pet['name']) ?>" class="carousel-image" data-index="0">
          <?php if(count($images) > 1): ?>
            <?php foreach(array_slice($images, 1) as $idx => $image): ?>
              <img src="<?= htmlspecialchars($image) ?>" alt="Photo <?= $idx + 2 ?>" class="carousel-image" style="display:none;" data-index="<?= $idx + 1 ?>">
            <?php endforeach; ?>
            <button class="carousel-nav prev" data-direction="prev"></button>
            <button class="carousel-nav next" data-direction="next"></button>
            <div class="carousel-indicators">
              <?php foreach($images as $idx => $image): ?>
                <button class="carousel-indicator <?= $idx === 0 ? 'active' : '' ?>" data-index="<?= $idx ?>"></button>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        <?php endif; ?>
        <span class="price">Rs <?= number_format($pet['price']) ?></span>
      </div>
      <div class="body">
        <div class="line1">
          <h3><?= htmlspecialchars($pet['name']) ?></h3>
          <span class="meta"><?= htmlspecialchars($pet['species']) ?> • <?= htmlspecialchars($pet['breed']) ?> • <?= htmlspecialchars($pet['age']) ?></span>
        </div>
        <p class="desc"><?= htmlspecialchars($pet['desc']) ?></p>
        <div class="badges">
          <?php foreach($pet['badges'] as $b): ?><span class="badge"><?= htmlspecialchars($b) ?></span><?php endforeach; ?>
        </div>
        <div class="seller">
          <span class="seller-name">Posted by <strong><?= htmlspecialchars($seller['name']) ?></strong></span>
          <span class="seller-loc"><?= htmlspecialchars($pet['location'] ?? '') ?></span>
        </div>
      </div>
      <div class="actions-row">
        <button class="btn buy contact-seller-btn" 
          data-id="<?= $pet['id'] ?>"
          data-name="<?= htmlspecialchars($seller['name']) ?>"
          data-phone="<?= htmlspecialchars($seller['phone'] ?? '') ?>"
          data-phone2="<?= htmlspecialchars($seller['phone2'] ?? '') ?>"
          data-email="<?= htmlspecialchars($seller['email'] ?? '') ?>">
          Contact Seller
        </button>
      </div>
    </article>
  <?php endforeach; ?>
  </section>

  <section id="adoptingGrid" class="grid hidden">
  <?php foreach($adoptingPets as $pet): 
    $seller=$sellers[$pet['seller_id']] ?? ['name'=>'Unknown','location'=>'','phone'=>'','phone2'=>'','email'=>''];
    $images = $pet['images'] ?? [];
  ?>
    <article class="card" data-species="<?= htmlspecialchars($pet['species']) ?>" data-price="<?= (int)$pet['price'] ?>" data-pet-id="<?= $pet['id'] ?>" data-latitude="<?= $pet['latitude'] ?? '' ?>" data-longitude="<?= $pet['longitude'] ?? '' ?>" data-listing-type="<?= htmlspecialchars($pet['listing_type'] ?? 'adoption') ?>">
      <div class="media">
        <?php if(!empty($images)): ?>
          <img src="<?= htmlspecialchars($images[0]) ?>" alt="<?= htmlspecialchars($pet['name']) ?>" class="carousel-image" data-index="0">
          <?php if(count($images) > 1): ?>
            <?php foreach(array_slice($images, 1) as $idx => $image): ?>
              <img src="<?= htmlspecialchars($image) ?>" alt="Photo <?= $idx + 2 ?>" class="carousel-image" style="display:none;" data-index="<?= $idx + 1 ?>">
            <?php endforeach; ?>
            <button class="carousel-nav prev" data-direction="prev"></button>
            <button class="carousel-nav next" data-direction="next"></button>
            <div class="carousel-indicators">
              <?php foreach($images as $idx => $image): ?>
                <button class="carousel-indicator <?= $idx === 0 ? 'active' : '' ?>" data-index="<?= $idx ?>"></button>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        <?php endif; ?>
      </div>
      <div class="body">
        <div class="line1">
          <h3><?= htmlspecialchars($pet['name']) ?></h3>
          <span class="meta"><?= htmlspecialchars($pet['species']) ?> • <?= htmlspecialchars($pet['breed']) ?> • <?= htmlspecialchars($pet['age']) ?></span>
        </div>
        <p class="desc"><?= htmlspecialchars($pet['desc']) ?></p>
        <div class="badges">
          <?php foreach($pet['badges'] as $b): ?><span class="badge"><?= htmlspecialchars($b) ?></span><?php endforeach; ?>
        </div>
        <div class="seller">
          <span class="seller-name">Posted by <strong><?= htmlspecialchars($seller['name']) ?></strong></span>
          <span class="seller-loc"><?= htmlspecialchars($pet['location'] ?? '') ?></span>
        </div>
      </div>
      <div class="actions-row">
        <button class="btn buy contact-seller-btn" 
          data-id="<?= $pet['id'] ?>"
          data-name="<?= htmlspecialchars($seller['name']) ?>"
          data-phone="<?= htmlspecialchars($seller['phone'] ?? '') ?>"
          data-phone2="<?= htmlspecialchars($seller['phone2'] ?? '') ?>"
          data-email="<?= htmlspecialchars($seller['email'] ?? '') ?>">
          Contact Owner
        </button>
      </div>
    </article>
  <?php endforeach; ?>
  </section>

  <!-- List Adoption Pet Modal -->
  <div class="modal-overlay" id="listPetModal" hidden>
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="listPetTitle">
      <h3 id="listPetTitle">List a Pet for Adoption</h3>
      <form id="listPetForm" autocomplete="off">
        <div class="form-grid">
          <label>Pet Name
            <input type="text" name="name" id="adoptPetName" required placeholder="e.g., Rocky, Bella" 
              pattern="^[A-Za-z\s]+$" title="Name should only contain letters and spaces" autocomplete="off">
            <small class="field-hint">Only letters and spaces allowed</small>
          </label>
          <label>Species
            <select name="species" required>
              <option value="">Select...</option>
              <option>Dog</option>
              <option>Cat</option>
              <option>Bird</option>
              <option>Other</option>
            </select>
          </label>
          <label>Breed
            <input type="text" name="breed" id="adoptBreed" required placeholder="e.g., Golden Retriever"
              pattern="^[A-Za-z\s]+$" title="Breed should only contain letters and spaces" autocomplete="off">
            <small class="field-hint">Only letters and spaces allowed</small>
          </label>
          <label>Age
            <input type="number" name="age" id="adoptAge" placeholder="e.g., 2" required 
              min="0" max="30" title="Age must be between 0 and 30" autocomplete="off">
            <small class="field-hint">Age must be between 0 and 30</small>
          </label>
          <label>Gender
            <select name="gender" required>
              <option value="">Select...</option>
              <option>Male</option>
              <option>Female</option>
            </select>
          </label>
          <label class="full">Location
            <input type="text" name="location" id="adoptLocation" required placeholder="e.g., Colombo 07" autocomplete="off">
          </label>
          <label class="full">Description<textarea name="desc" rows="3" required placeholder="Tell people about this pet's personality, behavior, health..."></textarea></label>
          <label class="full">
            <span style="font-weight:600;margin-bottom:6px;display:block;">Health Badges</span>
            <div class="checks">
              <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
                <input type="checkbox" name="badges[]" value="Vaccinated">
                <span>Vaccinated</span>
              </label>
              <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
                <input type="checkbox" name="badges[]" value="Microchipped">
                <span>Microchipped</span>
              </label>
            </div>
          </label>
          <label>Primary Phone
            <input type="tel" name="phone" id="adoptPhone" placeholder="0771234567" required 
              pattern="^[0-9]{10}$" title="Phone must be 10 digits" maxlength="10" autocomplete="off">
            <small class="field-hint">Must be 10 digits, numbers only</small>
          </label>
          <label>Secondary Phone (Optional)
            <input type="tel" name="phone2" placeholder="0771234567" 
              pattern="^[0-9]{10}$" title="Phone must be 10 digits" maxlength="10" autocomplete="off">
            <small class="field-hint">Must be 10 digits, numbers only</small>
          </label>
          <label class="full">Email
            <input type="email" name="email" required placeholder="your.email@example.com">
            <small class="field-hint">Required so people can contact you about this pet</small>
          </label>
          <label class="full">Photos (Max 3)
            <input type="file" name="images[]" id="adoptImages" accept="image/*" multiple data-max-files="3">
            <small style="display:block;color:var(--muted);margin-top:6px;">Upload up to 3 photos. First image will be the listing cover.</small>
            <div id="adoptImagePreviews" style="margin-top:12px;display:none;display:flex;gap:10px;flex-wrap:wrap;"></div>
          </label>
        </div>

        <div class="modal-actions">
          <button type="button" class="btn ghost" id="cancelListPet">Cancel</button>
          <button type="submit" class="btn primary">List for Adoption</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Pet Details Modal -->
  <div class="modal" id="detailsModal" aria-hidden="true">
    <div class="modal-dialog">
      <header class="modal-header">
        <h3 id="detailsTitle">Pet Details</h3>
        <button class="icon-btn close" data-close="detailsModal" aria-label="Close">×</button>
      </header>
      <div class="modal-body" id="detailsBody"></div>
    </div>
  </div>

  <!-- Contact Seller Modal -->
  <div class="modal-overlay contact-modal-overlay" id="contactModal" style="display:none;">
    <div class="contact-modal-content">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <h3 style="margin:0;">Contact Seller</h3>
        <button class="modal-close-btn" id="closeContact">&times;</button>
      </div>
      <div id="contactContent"></div>
    </div>
  </div>

  <!-- My Adoption Listings Modal -->
  <div class="modal-overlay" id="myListingsModal" hidden>
    <div class="modal" style="max-width: 1000px; max-height: 85vh; overflow-y: auto; border-radius: 16px; box-shadow: 0 20px 25px rgba(0, 0, 0, 0.15);">
      <div style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); padding: 28px 32px; border-bottom: 1px solid #cffafe; display: flex; justify-content: space-between; align-items: center;">
        <div>
          <h2 style="margin: 0; font-size: 1.75rem; font-weight: 800; color: #0c4a6e; letter-spacing: -0.5px;">My Adoption Listings</h2>
          <p style="margin: 6px 0 0 0; color: #0369a1; font-size: 0.95rem;">Manage your pet listings</p>
        </div>
        <button id="closeMyListingsModal" style="background: white; border: 1px solid #e0e7ff; padding: 8px 11px; border-radius: 8px; cursor: pointer; font-size: 1.5rem; color: #6b7280; transition: all 0.2s ease; display: flex; align-items: center; justify-content: center;" onmouseover="this.style.background='#f3f4f6'; this.style.color='#1f2937'" onmouseout="this.style.background='white'; this.style.color='#6b7280'">×</button>
      </div>
      <div id="myListingsContent" style="display: grid; gap: 20px; padding: 32px;">
        <p style="text-align: center; color: #64748b;">Loading your listings...</p>
      </div>
    </div>
  </div>

</div>

<!-- Leaflet JavaScript -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>
<script>window.EXPLORE_CURRENT_USER_NAME = 'Guest';</script>

<!-- Guest List Pet Functionality -->
<script>
document.addEventListener('DOMContentLoaded', () => {
  const listPetBtn = document.getElementById('btnListPet');
  const listPetModal = document.getElementById('listPetModal');
  const listPetForm = document.getElementById('listPetForm');
  const cancelListPetBtn = document.getElementById('cancelListPet');
  const segButtons = Array.from(document.querySelectorAll('.seg-btn'));

  // Toggle List Pet button visibility based on active tab
  const updateListPetVisibility = () => {
    if (!listPetBtn) return;
    const activeButton = segButtons.find((btn) => btn.classList.contains('is-active'));
    const activeView = activeButton?.getAttribute('data-view') || 'sale';
    listPetBtn.style.display = activeView === 'adoption' ? 'inline-flex' : 'none';
    // Show My Listings button for ALL in adoption tab (both logged-in and guests)
    if (myListingsBtn) {
      myListingsBtn.style.display = activeView === 'adoption' ? 'inline-flex' : 'none';
    }
  };

  // Update on toggle click
  segButtons.forEach((btn) => {
    btn.addEventListener('click', () => {
      setTimeout(updateListPetVisibility, 0);
    });
  });

  // Initial check
  setTimeout(updateListPetVisibility, 0);

  // Open modal
  listPetBtn?.addEventListener('click', () => {
    listPetModal.hidden = false;
  });

  // Close modal
  cancelListPetBtn?.addEventListener('click', () => {
    listPetModal.hidden = true;
    listPetForm.reset();
  });

  // Close on overlay click
  listPetModal?.addEventListener('click', (e) => {
    if (e.target === listPetModal) {
      listPetModal.hidden = true;
      listPetForm.reset();
    }
  });

  // Handle form submission
  listPetForm?.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(listPetForm);
    formData.append('listing_type', 'adoption');

    try {
      const response = await fetch('/PETVET/api/guest/list-adoption-pet.php', {
        method: 'POST',
        body: formData
      });

      const responseText = await response.text();
      console.log('Raw response:', responseText);

      let result;
      try {
        result = JSON.parse(responseText);
      } catch (parseError) {
        console.error('JSON parse error:', parseError);
        alert('Server error: Invalid response from server\n\nResponse: ' + responseText.substring(0, 200));
        return;
      }

      if (result.success) {
        alert('Pet listed successfully! It will be visible after admin approval.');
        listPetModal.hidden = true;
        listPetForm.reset();
        // Reload page to show new pet
        location.reload();
      } else {
        alert('Error: ' + (result.message || 'Failed to list pet') + '\n\n' + (result.error ? 'Details: ' + result.error : ''));
      }
    } catch (error) {
      console.error('Fetch error:', error);
      alert('An error occurred while listing the pet: ' + error.message);
    }
  });

  // My Listings Button Handler
  const myListingsBtn = document.getElementById('btnMyAdoptionListings');
  const myListingsModal = document.getElementById('myListingsModal');
  const closeMyListingsBtn = document.getElementById('closeMyListingsModal');
  const myListingsContent = document.getElementById('myListingsContent');
  
  // Store guest email for deletion operations
  let currentGuestEmail = null;

  // Helper function to render listings
  const renderListings = (listings, refreshCallback) => {
    if (listings.length > 0) {
      return listings.map((listing) => {
        const statusColor = listing.status === 'approved' ? '#10b981' : 
                          listing.status === 'rejected' ? '#ef4444' : 
                          listing.status === 'pending' ? '#f59e0b' : '#64748b';
        const statusBg = listing.status === 'approved' ? '#ecfdf5' : 
                        listing.status === 'rejected' ? '#fef2f2' : 
                        listing.status === 'pending' ? '#fffbeb' : '#f8fafc';
        const statusText = listing.status === 'approved' ? 'Approved - Live' : 
                          listing.status === 'rejected' ? 'Rejected' : 
                          listing.status === 'pending' ? 'Pending Approval' : listing.status;

        return `
          <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07), 0 1px 3px rgba(0, 0, 0, 0.06); transition: all 0.3s ease; border: 1px solid #e5e7eb;">
            <div style="display: flex; gap: 20px; padding: 20px;">
              <!-- Image Section -->
              <div style="flex-shrink: 0;">
                ${listing.images?.[0] ? `<img src="${listing.images[0]}" alt="${listing.name}" style="width: 140px; height: 140px; object-fit: cover; border-radius: 10px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">` : `<div style="width: 140px; height: 140px; background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #9ca3af; font-size: 2.5rem;">🐾</div>`}
              </div>

              <!-- Content Section -->
              <div style="flex: 1; display: flex; flex-direction: column;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                  <div>
                    <h3 style="margin: 0 0 6px 0; font-size: 1.25rem; font-weight: 700; color: #1f2937;">${listing.name}</h3>
                    <p style="margin: 0; color: #6b7280; font-size: 0.95rem; font-weight: 500;">${listing.species} • ${listing.breed} • ${listing.age} ${listing.age === 1 ? 'year' : 'years'} old</p>
                  </div>
                  <span style="background: ${statusBg}; color: ${statusColor}; padding: 6px 14px; border-radius: 20px; font-size: 0.85em; font-weight: 700; text-transform: capitalize; border: 1px solid ${statusColor}33; white-space: nowrap;">${statusText}</span>
                </div>

                <p style="margin: 12px 0; color: #4b5563; font-size: 0.95rem; line-height: 1.5; text-align: justify;">${listing.description}</p>

                <!-- Badges -->
                <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 12px;">
                  ${listing.badges?.map(b => `<span style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #0c4a6e; padding: 4px 12px; border-radius: 16px; font-size: 0.8em; font-weight: 600; border: 1px solid #7dd3fc33;">✓ ${b}</span>`).join('') || ''}
                </div>

                <!-- Location & Phone -->
                <div style="display: flex; gap: 16px; margin-top: auto; padding-top: 12px; border-top: 1px solid #f3f4f6;">
                  <div style="display: flex; align-items: center; gap: 6px; color: #6b7280; font-size: 0.9em;">
                    <span style="font-size: 1.1em;">📍</span>
                    <span>${listing.location}</span>
                  </div>
                  <div style="display: flex; align-items: center; gap: 6px; color: #6b7280; font-size: 0.9em;">
                    <span style="font-size: 1.1em;">📞</span>
                    <span>${listing.phone}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Footer with Delete Button -->
            <div style="background: #f9fafb; padding: 12px 20px; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end;">
              <button onclick="deleteListing(${listing.id})" style="background: #ef4444; color: white; border: none; border-radius: 6px; padding: 8px 18px; cursor: pointer; font-size: 0.9em; font-weight: 600; transition: all 0.2s ease; box-shadow: 0 2px 4px rgba(239, 68, 68, 0.2);" onmouseover="this.style.background='#dc2626'; this.style.boxShadow='0 4px 8px rgba(239, 68, 68, 0.3)'; this.style.transform='translateY(-1px)'" onmouseout="this.style.background='#ef4444'; this.style.boxShadow='0 2px 4px rgba(239, 68, 68, 0.2)'; this.style.transform='translateY(0)'" title="Delete this listing">
                Delete Listing
              </button>
            </div>
          </div>
        `;
      }).join('');
    } else {
      return '<div style="background: white; border-radius: 12px; padding: 60px 40px; text-align: center; border: 1px solid #e5e7eb;"><p style="margin: 0; color: #6b7280; font-size: 1.1rem;">You haven\'t listed any adoption pets yet.</p></div>';
    }
  };

  // Delete listing function
  window.deleteListing = async (listingId) => {
    if (!confirm('Are you sure you want to delete this listing? This action cannot be undone.')) {
      return;
    }

    try {
      const formData = new FormData();
      formData.append('listing_id', listingId);
      
      const userId = document.body.getAttribute('data-user-id');
      if (!userId && currentGuestEmail) {
        formData.append('email', currentGuestEmail);
      }

      const response = await fetch('/PETVET/api/guest/delete-adoption-listing.php', {
        method: 'POST',
        body: formData
      });

      const result = await response.json();

      if (result.success) {
        alert('Listing deleted successfully');
        // Reload the listings
        myListingsBtn.click();
      } else {
        alert('Error: ' + (result.message || 'Failed to delete listing'));
      }
    } catch (error) {
      console.error('Error:', error);
      alert('An error occurred while deleting the listing');
    }
  };

  // Open My Listings Modal
  myListingsBtn?.addEventListener('click', async () => {
    myListingsModal.hidden = false;
    myListingsContent.innerHTML = '<p style="text-align: center; color: #64748b;">Loading your listings...</p>';

    const userId = document.body.getAttribute('data-user-id');
    let email = null;

    try {
      // If not logged in, ask for email (but only if we don't have it stored already)
      if (!userId) {
        if (currentGuestEmail) {
          // Use the stored email
          email = currentGuestEmail;
        } else {
          // Ask for email
          email = prompt('Enter your email address to view your adoption listings:', '');
          if (!email) {
            myListingsModal.hidden = true;
            return;
          }
          currentGuestEmail = email;
        }
      }

      // Fetch listings
      let fetchUrl = '/PETVET/api/guest/';
      let fetchOptions = { method: 'POST' };

      if (userId) {
        fetchUrl += 'get-my-adoption-listings.php';
        fetchOptions.body = new FormData();
      } else {
        fetchUrl += 'view-my-listings.php';
        const formData = new FormData();
        formData.append('email', email || currentGuestEmail);
        fetchOptions.body = formData;
      }

      const response = await fetch(fetchUrl, fetchOptions);
      const result = await response.json();

      if (result.success) {
        myListingsContent.innerHTML = renderListings(result.listings);
      } else {
        myListingsContent.innerHTML = `<p style="text-align: center; color: #ef4444;">${result.message || 'Failed to load listings'}</p>`;
      }
    } catch (error) {
      console.error('Error:', error);
      myListingsContent.innerHTML = '<p style="text-align: center; color: #ef4444;">An error occurred while loading your listings</p>';
    }
  });

  // Close My Listings Modal
  closeMyListingsBtn?.addEventListener('click', () => {
    myListingsModal.hidden = true;
  });

  myListingsModal?.addEventListener('click', (e) => {
    if (e.target === myListingsModal) {
      myListingsModal.hidden = true;
    }
  });
});
</script>

<script src="/PETVET/public/js/pet-owner/explore-pets.js"></script>

</body>
</html>
