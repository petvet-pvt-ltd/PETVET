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
  <?php foreach($pets as $pet): $seller=$sellers[$pet['seller_id']] ?? ['name'=>'Unknown','location'=>'']; ?>
    <article class="card" data-species="<?= htmlspecialchars($pet['species']) ?>" data-price="<?= (int)$pet['price'] ?>">
      <div class="media">
        <img src="<?= htmlspecialchars($pet['images'][0]) ?>" alt="<?= htmlspecialchars($pet['name']) ?>">
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
        <button class="btn buy call" data-id="<?= $pet['id'] ?>">Call</button>
      </div>
    </article>
  <?php endforeach; ?>
  </section>

  <!-- Sell Pet -->
  <div class="modal" id="sellModal" aria-hidden="true">
    <div class="modal-dialog">
      <header class="modal-header">
        <h3>Sell a Pet</h3>
        <button class="icon-btn close" data-close="sellModal" aria-label="Close">×</button>
      </header>
      <div class="modal-body">
        <form id="sellForm">
          <div class="form-grid">
            <label>Pet Name<input type="text" name="name" required></label>
            <label>Species
              <select name="species" required>
                <option>Dog</option><option>Cat</option><option>Bird</option><option>Other</option>
              </select>
            </label>
            <label>Breed<input type="text" name="breed" required></label>
            <label>Age<input type="text" name="age" placeholder="e.g., 2y" required></label>
            <label>Gender
              <select name="gender"><option>Male</option><option>Female</option></select>
            </label>
            <label>Price (Rs)<input type="number" name="price" min="0" step="500" required></label>
            <label class="full">Health Badges
              <div class="checks">
                <label><input type="checkbox" name="badges[]" value="Vaccinated"> Vaccinated</label>
                <label><input type="checkbox" name="badges[]" value="Microchipped"> Microchipped</label>
              </div>
            </label>
            <label class="full">Short Description<textarea name="desc" rows="3" required></textarea></label>
            <label class="full">Photos 
              <input type="file" name="images[]" id="sellImages" accept="image/*" multiple required>
              <small class="muted">You can upload multiple photos. First image will be used as the listing cover.</small>
              <div id="sellImagePreviews" class="image-previews" aria-hidden="true"></div>
              <!-- For demo: we keep a hidden single image field for backward compatibility; JS will populate it with the first selected image -->
              <input type="hidden" name="image" id="sellImageFallback">
            </label>
          </div>
          <div class="modal-actions">
            <button type="button" class="btn outline" data-close="sellModal">Cancel</button>
            <button type="submit" class="btn primary">Publish Listing</button>
          </div>
        </form>
      </div>
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
        <?php if(empty($myListings)): ?>
          <p class="empty">You don’t have any listings yet. Click <strong>Sell a Pet</strong> to post one.</p>
        <?php else: ?>
          <div class="listings-table" id="myListingsWrap">
            <?php foreach($myListings as $p):
              $loc = $sellers[$p['seller_id']]['location'] ?? '';
            ?>
              <div class="listing-row" data-id="<?= $p['id'] ?>">
                <img src="<?= htmlspecialchars($p['images'][0]) ?>" alt="">
                <div class="listing-info">
                  <h4><?= htmlspecialchars($p['name']) ?></h4>
                  <p><?= htmlspecialchars($p['species']) ?> • Rs <?= number_format($p['price']) ?></p>
                  <span class="muted"><?= htmlspecialchars($p['breed']) ?> • <?= htmlspecialchars($p['age']) ?> • <?= htmlspecialchars($p['gender']) ?> • <?= htmlspecialchars($loc) ?></span>
                </div>
                <div class="listing-actions">
                  <!-- data-* provided so your JS can prefill the Edit form -->
                  <button
                    class="btn sm outline edit"
                    data-id="<?= $p['id'] ?>"
                    data-name="<?= htmlspecialchars($p['name']) ?>"
                    data-species="<?= htmlspecialchars($p['species']) ?>"
                    data-breed="<?= htmlspecialchars($p['breed']) ?>"
                    data-age="<?= htmlspecialchars($p['age']) ?>"
                    data-gender="<?= htmlspecialchars($p['gender']) ?>"
                    data-price="<?= (int)$p['price'] ?>"
                    data-desc="<?= htmlspecialchars($p['desc']) ?>"
                    data-location="<?= htmlspecialchars($loc) ?>"
                  >Edit</button>
                  <button class="btn sm danger remove" data-id="<?= $p['id'] ?>">Remove</button>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
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
            <label>Pet Name<input type="text" name="name" required></label>
            <label>Species
              <select name="species">
                <option>Dog</option><option>Cat</option><option>Bird</option><option>Other</option>
              </select>
            </label>
            <label>Breed<input type="text" name="breed" required></label>
            <label>Age<input type="text" name="age" required></label>
            <label>Gender
              <select name="gender"><option>Male</option><option>Female</option></select>
            </label>
            <label>Price (Rs)<input type="number" name="price" min="0" step="500" required></label>
            <label class="full">Description<textarea name="desc" rows="3"></textarea></label>
            <label class="full">Location<input type="text" name="location" required></label>
            <!-- Edit photos: allow adding/removing multiple images -->
            <label class="full">Photos
              <div class="muted">Manage listing photos. You can add new photos or remove existing ones.</div>
              <input type="file" name="editImages[]" id="editImages" accept="image/*" multiple>
              <small class="muted">Add new photos to include in the listing. You can also remove existing photos below.</small>
              <div id="editImagePreviews" class="image-previews" aria-hidden="true"></div>
              <!-- Existing images (JSON) will be stored here for demo use; JS keeps this in sync -->
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
</div>

<script>window.EXPLORE_CURRENT_USER_NAME = <?= json_encode($currentUser['name']) ?>;</script>
<script src="/PETVET/public/js/pet-owner/explore-pets.js"></script>
