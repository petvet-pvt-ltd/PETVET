<?php /* Explore Pets marketplace - now uses controller data */ ?>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<link rel="stylesheet" href="/PETVET/public/css/pet-owner/explore-pets.css">
<div class="main-content">
<?php
// Data comes from the controller via ExplorePetsModel
$currentUser = $currentUser ?? ['id' => 1, 'name' => 'You'];
$sellers = $sellers ?? [];
$pets = $pets ?? [];
$myListings = $myListings ?? [];
$availableSpecies = $availableSpecies ?? [];
?>
  <header class="page-header">
    <div class="title-wrap">
      <h2>Explore Pets</h2>
      <p class="subtitle">Browse pets for sale or list your own.</p>
    </div>
    <div class="actions">
      <button class="btn outline" id="btnMyListings">My Listings</button>
      <button class="btn primary" id="btnSellPet">Sell a Pet</button>
    </div>
  </header>

  <section class="filters">
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
  </section>

  <section id="listingsGrid" class="grid">
  <?php foreach($pets as $pet): 
    $seller=$sellers[$pet['seller_id']] ?? ['name'=>'Unknown','location'=>'','phone'=>'','phone2'=>'','email'=>''];
    $images = $pet['images'] ?? [];
  ?>
    <article class="card" data-species="<?= htmlspecialchars($pet['species']) ?>" data-price="<?= (int)$pet['price'] ?>">
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
          <span class="seller-loc"><?= htmlspecialchars($seller['location']) ?></span>
        </div>
      </div>
      <div class="actions-row">
        <button class="btn ghost view" data-id="<?= $pet['id'] ?>">View Details</button>
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

  <!-- Sell Pet -->
  <div class="modal-overlay" id="sellModal" hidden>
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="sellTitle">
      <h3 id="sellTitle">Sell a Pet</h3>
      <form id="sellForm" autocomplete="off">
        <div class="form-grid">
          <label>Pet Name
            <input type="text" name="name" id="sellPetName" required placeholder="e.g., Rocky, Bella" 
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
            <input type="text" name="breed" id="sellBreed" required placeholder="e.g., Golden Retriever"
              pattern="^[A-Za-z\s]+$" title="Breed should only contain letters and spaces" autocomplete="off">
            <small class="field-hint">Only letters and spaces allowed</small>
          </label>
          <label>Age
            <input type="number" name="age" id="sellAge" placeholder="e.g., 2" required 
              min="0" max="99" title="Age must be between 0 and 99" autocomplete="off">
            <small class="field-hint">Age must be less than 100</small>
          </label>
          <label>Gender
            <select name="gender" required>
              <option value="">Select...</option>
              <option>Male</option>
              <option>Female</option>
            </select>
          </label>
          <label>Price (Rs)
            <input type="number" name="price" id="sellPrice" min="0" step="500" required placeholder="e.g., 50000" autocomplete="off">
            <small class="field-hint">Enter price in Sri Lankan Rupees</small>
          </label>
          <label class="full">Location<input type="text" name="location" required placeholder="e.g., Colombo 07"></label>
          <label class="full">Description<textarea name="desc" rows="3" required placeholder="Tell potential buyers about this pet's personality, behavior, health..."></textarea></label>
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
            <input type="tel" name="phone" id="sellPhone" placeholder="0771234567" required 
              pattern="^[0-9]{10}$" title="Phone must be 10 digits" maxlength="10" autocomplete="off">
            <small class="field-hint">Must be 10 digits, numbers only</small>
          </label>
          <label>Secondary Phone (Optional)<input type="tel" name="phone2" placeholder="+94 76 555 1212"></label>
          <label class="full">Email (Optional)<input type="email" name="email" placeholder="your.email@example.com"></label>
          <label class="full">Photos (Max 3)
            <input type="file" name="images[]" id="sellImages" accept="image/*" multiple data-max-files="3">
            <small style="display:block;color:var(--muted);margin-top:6px;">Upload up to 3 photos. First image will be the listing cover.</small>
            <div id="sellImagePreviews" style="margin-top:12px;display:none;display:flex;gap:10px;flex-wrap:wrap;"></div>
          </label>
        </div>

        <div class="modal-actions">
          <button type="button" class="btn ghost" id="cancelSell">Cancel</button>
          <button type="submit" class="btn primary">Publish Listing</button>
        </div>
      </form>
    </div>
  </div>

  <!-- My Listings (improved look) -->
  <div class="modal" id="myListingsModal" aria-hidden="true">
    <div class="modal-dialog wide">
      <header class="modal-header">
        <h3>My Listings</h3>
        <button class="icon-btn close" data-close="myListingsModal" aria-label="Close">×</button>
      </header>
      <div class="modal-body">
        <div class="listings-grid" id="myListingsContent">
          <!-- JavaScript will populate this dynamically -->
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Listing -->
  <div class="modal" id="editListingModal" aria-hidden="true">
    <div class="modal-dialog">
      <header class="modal-header">
        <h3>Edit Listing</h3>
        <button class="icon-btn close" data-close="editListingModal" aria-label="Close">×</button>
      </header>
      <div class="modal-body">
        <form id="editForm">
          <input type="hidden" name="id">
          <div class="form-grid">
            <label>Pet Name
              <input type="text" name="name" id="editPetName" required 
                pattern="^[A-Za-z\s]+$" title="Name should only contain letters and spaces" autocomplete="off">
              <small class="field-hint">Only letters and spaces allowed</small>
            </label>
            <label>Species
              <select name="species">
                <option>Dog</option><option>Cat</option><option>Bird</option><option>Other</option>
              </select>
            </label>
            <label>Breed
              <input type="text" name="breed" id="editBreed" required
                pattern="^[A-Za-z\s]+$" title="Breed should only contain letters and spaces" autocomplete="off">
              <small class="field-hint">Only letters and spaces allowed</small>
            </label>
            <label>Age
              <input type="number" name="age" id="editAge" required 
                min="0" max="99" title="Age must be between 0 and 99" autocomplete="off">
              <small class="field-hint">Age must be less than 100</small>
            </label>
            <label>Gender
              <select name="gender"><option>Male</option><option>Female</option></select>
            </label>
            <label>Price (Rs)
              <input type="number" name="price" id="editPrice" min="0" step="500" required autocomplete="off">
              <small class="field-hint">Enter price in Sri Lankan Rupees</small>
            </label>
            <label class="full">Description<textarea name="desc" rows="3"></textarea></label>
            <label class="full">Location<input type="text" name="location" required></label>
            <label>Primary Phone
              <input type="tel" name="phone" id="editPhone" placeholder="0771234567" required 
                pattern="^[0-9]{10}$" title="Phone must be 10 digits" maxlength="10" autocomplete="off">
              <small class="field-hint">Must be 10 digits, numbers only</small>
            </label>
            <label>Secondary Phone (Optional)<input type="tel" name="phone2" placeholder="+94 76 555 1212"></label>
            <label class="full">Email (Optional)<input type="email" name="email" placeholder="your.email@example.com"></label>
            
            <!-- Existing Photos Preview -->
            <label class="full">Current Photos
              <div id="editExistingPhotos" class="edit-photos-preview"></div>
            </label>
            
            <!-- Add New Photos -->
            <label class="full">Add New Photos (Max 3 total)
              <input type="file" name="editImages[]" id="editImages" accept="image/*" multiple data-max-files="3">
              <small class="muted">You can add new photos or remove existing ones above.</small>
              <div id="editImagePreviews" class="image-previews" style="margin-top:12px;"></div>
              <input type="hidden" name="existingImages" id="existingImages">
            </label>
          </div>
          <div class="modal-actions">
            <button type="button" class="btn outline" data-close="editListingModal">Cancel</button>
            <button type="submit" class="btn primary">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Pet Details -->
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

  <!-- Confirm Delete Dialog -->
  <div class="modal-overlay contact-modal-overlay" id="confirmDialog" style="display:none;">
    <div class="confirm-dialog">
      <h3>Confirm Delete</h3>
      <p class="confirm-message">
        Are you sure you want to delete the listing for <span class="confirm-highlight" id="confirmPetName"></span>? This action cannot be undone.
      </p>
      <div class="confirm-actions">
        <button class="btn outline" id="cancelConfirm">Cancel</button>
        <button class="btn danger" id="confirmDelete">Delete</button>
      </div>
    </div>
  </div>
</div>

<script>window.EXPLORE_CURRENT_USER_NAME = <?= json_encode($currentUser['name']) ?>;</script>
<script src="/PETVET/public/js/pet-owner/explore-pets.js"></script>
