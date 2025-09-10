<?php
// ---------------------------------------------
// Explore Pets (Marketplace) - Standalone View
// DB simulated with arrays
// ---------------------------------------------

// Logged-in user simulation
$currentUser = [
  'id' => 1,
  'name' => 'You'
];

// Sellers
$sellers = [
  1 => ['id'=>1,'name'=>'You','location'=>'Colombo'],
  2 => ['id'=>2,'name'=>'Kasun Perera','location'=>'Kandy'],
  3 => ['id'=>3,'name'=>'Nirmala','location'=>'Galle'],
];

// Pet listings (simulate DB)
$pets = [
  [
    'id'=>101,
    'name'=>'Rocky',
    'species'=>'Dog',
    'breed'=>'Golden Retriever',
    'age'=>'3y',
    'gender'=>'Male',
    'badges'=>['Vaccinated','Microchipped'],
    'price'=>95000,
    'desc'=>'Friendly and well-trained. Great with kids.',
    'images'=>['https://images.unsplash.com/photo-1552053831-71594a27632d?q=80&w=1400&auto=format&fit=crop'],
    'seller_id'=>2
  ],
  [
    'id'=>102,
    'name'=>'Whiskers',
    'species'=>'Cat',
    'breed'=>'Siamese',
    'age'=>'2y',
    'gender'=>'Female',
    'badges'=>['Vaccinated'],
    'price'=>45000,
    'desc'=>'Playful, litter-trained, prefers quiet home.',
    'images'=>['https://images.unsplash.com/photo-1543852786-1cf6624b9987?q=80&w=1400&auto=format&fit=crop'],
    'seller_id'=>3
  ],
  [
    'id'=>103,
    'name'=>'Tweety',
    'species'=>'Bird',
    'breed'=>'Canary',
    'age'=>'1y',
    'gender'=>'Female',
    'badges'=>['Microchipped'],
    'price'=>12000,
    'desc'=>'Sings every morning. Healthy and active.',
    'images'=>['https://thvnext.bing.com/th/id/OIP.ikE0KSiA5itZmKSZ4koCqAHaFj?w=284&h=213&c=7&r=0&o=7&cb=ucfimgc2&dpr=1.3&pid=1.7&rm=3'],
    'seller_id'=>2
  ],
  [
    'id'=>104,
    'name'=>'Bruno',
    'species'=>'Dog',
    'breed'=>'Beagle',
    'age'=>'1y',
    'gender'=>'Male',
    'badges'=>['Vaccinated'],
    'price'=>80000,
    'desc'=>'Curious and energetic. Loves walks.',
    'images'=>['https://images.unsplash.com/photo-1543466835-00a7907e9de1?q=80&w=1400&auto=format&fit=crop'],
    'seller_id'=>1 // belongs to current user -> will appear in My Listings
  ],
];

// Derive "my listings"
$myListings = array_values(array_filter($pets, fn($p) => $p['seller_id'] === $currentUser['id']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Explore Pets</title>
  <link rel="stylesheet" href="explore-pets.css">
</head>
<body>
  <?php require_once '../sidebar.php'; ?>

  <main class="main-content">
    <!-- Page Header -->
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

    <!-- Search & Filters -->
    <section class="filters">
      <div class="field grow">
        <input type="text" id="searchInput" placeholder="Search by name, breed, or seller...">
      </div>
      <div class="field">
        <select id="speciesFilter">
          <option value="">All Species</option>
          <option>Dog</option>
          <option>Cat</option>
          <option>Bird</option>
          <option>Other</option>
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

    <!-- Listings Grid -->
    <section id="listingsGrid" class="grid">
      <?php foreach ($pets as $pet): 
        $seller = $sellers[$pet['seller_id']] ?? ['name'=>'Unknown','location'=>''];
      ?>
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
            <?php foreach ($pet['badges'] as $b): ?>
              <span class="badge"><?= htmlspecialchars($b) ?></span>
            <?php endforeach; ?>
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
  </main>

  <!-- ============ Modals ============ -->

  <!-- Sell Pet Modal -->
  <div class="modal" id="sellModal" aria-hidden="true">
    <div class="modal-dialog">
      <header class="modal-header">
        <h3>Sell a Pet</h3>
        <button class="icon-btn close" data-close="sellModal" aria-label="Close">×</button>
      </header>
      <div class="modal-body">
        <form id="sellForm">
          <div class="form-grid">
            <label>Pet Name
              <input type="text" name="name" required>
            </label>
            <label>Species
              <select name="species" required>
                <option>Dog</option><option>Cat</option><option>Bird</option><option>Other</option>
              </select>
            </label>
            <label>Breed
              <input type="text" name="breed" required>
            </label>
            <label>Age
              <input type="text" name="age" placeholder="e.g., 2y" required>
            </label>
            <label>Gender
              <select name="gender"><option>Male</option><option>Female</option></select>
            </label>
            <label>Price (Rs)
              <input type="number" name="price" min="0" step="500" required>
            </label>
            <label class="full">Health Badges
              <div class="checks">
                <label><input type="checkbox" name="badges[]" value="Vaccinated"> Vaccinated</label>
                <label><input type="checkbox" name="badges[]" value="Microchipped"> Microchipped</label>
              </div>
            </label>
            <label class="full">Short Description
              <textarea name="desc" rows="3" required></textarea>
            </label>
            <label class="full">Image URL
              <input type="url" name="image" placeholder="https://example.com/pet.jpg" required>
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

  <!-- My Listings Modal -->
  <div class="modal" id="myListingsModal" aria-hidden="true">
    <div class="modal-dialog">
      <header class="modal-header">
        <h3>My Listings</h3>
        <button class="icon-btn close" data-close="myListingsModal" aria-label="Close">×</button>
      </header>
      <div class="modal-body">
        <?php if (empty($myListings)): ?>
          <p class="empty">You don’t have any listings yet. Click <strong>Sell a Pet</strong> to post one.</p>
        <?php else: ?>
          <div class="mini-grid" id="myListingsWrap">
            <?php foreach ($myListings as $p): ?>
              <div class="mini-card" data-id="<?= $p['id'] ?>">
                <img src="<?= htmlspecialchars($p['images'][0]) ?>" alt="">
                <div class="info">
                  <strong><?= htmlspecialchars($p['name']) ?></strong>
                  <span class="muted"><?= htmlspecialchars($p['species']) ?> • Rs <?= number_format($p['price']) ?></span>
                </div>
                <div class="mini-actions">
                  <button class="btn sm outline mark-sold">Mark as Sold</button>
                  <button class="btn sm danger remove">Remove</button>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- View Details Modal (dynamic content) -->
  <div class="modal" id="detailsModal" aria-hidden="true">
    <div class="modal-dialog">
      <header class="modal-header">
        <h3 id="detailsTitle">Pet Details</h3>
        <button class="icon-btn close" data-close="detailsModal" aria-label="Close">×</button>
      </header>
      <div class="modal-body" id="detailsBody">
        <!-- Filled by JS -->
      </div>
    </div>
  </div>

  <!-- Lightweight JS for interactions -->
  <script>
    // Helpers
    const qs = (s, el=document) => el.querySelector(s);
    const qsa = (s, el=document) => Array.from(el.querySelectorAll(s));
    const money = n => 'Rs ' + Number(n).toLocaleString();

    // Open/Close modals
    function openModal(id){ const m=qs('#'+id); m?.setAttribute('aria-hidden','false'); m?.classList.add('show'); }
    function closeModal(id){ const m=qs('#'+id); m?.setAttribute('aria-hidden','true'); m?.classList.remove('show'); }
    qsa('[data-close]').forEach(btn=>btn.addEventListener('click',e=>closeModal(e.currentTarget.getAttribute('data-close'))));
    qsa('.modal').forEach(m=>m.addEventListener('click',e=>{ if(e.target===m) closeModal(m.id); }));

    // Buttons
    qs('#btnSellPet').addEventListener('click',()=>openModal('sellModal'));
    qs('#btnMyListings').addEventListener('click',()=>openModal('myListingsModal'));

    // Search / Filter / Sort
    const searchInput = qs('#searchInput');
    const speciesFilter = qs('#speciesFilter');
    const sortBy = qs('#sortBy');
    const grid = qs('#listingsGrid');

    function applyFilters(){
      const term = searchInput.value.trim().toLowerCase();
      const species = speciesFilter.value;
      let cards = qsa('.card', grid);

      cards.forEach(c=>{
        const text = c.innerText.toLowerCase();
        const matchTerm = !term || text.includes(term);
        const matchSpecies = !species || c.dataset.species === species;
        c.style.display = (matchTerm && matchSpecies) ? '' : 'none';
      });

      // sort
      cards = qsa('.card', grid).filter(c=>c.style.display!=='none');
      const mode = sortBy.value;
      cards.sort((a,b)=>{
        const pa = +a.dataset.price, pb = +b.dataset.price;
        if(mode==='priceLow') return pa - pb;
        if(mode==='priceHigh') return pb - pa;
        if(mode==='age') return a.innerText.indexOf('1y')>-1 ? -1 : 1; // light demo
        return 0; // newest (as-is order)
      });
      cards.forEach(c=>grid.appendChild(c));
    }
    [searchInput, speciesFilter, sortBy].forEach(el=>el.addEventListener('input',applyFilters));

    // Buy Now (demo)
    grid.addEventListener('click', e=>{
      if(e.target.classList.contains('call')){
        const card = e.target.closest('.card');
        const sellerName = card.querySelector('.seller-name strong').textContent;
        const sellerLoc = card.querySelector('.seller-loc').textContent;
        // For demo, just alert seller info
        alert('Call seller: ' + sellerName + ' (' + sellerLoc + ')');
      }
      if(e.target.classList.contains('view')){
        const card = e.target.closest('.card');
        const title = card.querySelector('h3').textContent;
        const price = card.dataset.price;
        const img = card.querySelector('img').src;
        const meta = card.querySelector('.meta').textContent;
        const desc = card.querySelector('.desc').textContent;
        const sellerName = card.querySelector('.seller-name strong').textContent;
        const sellerLoc = card.querySelector('.seller-loc').textContent;

        // For demo, show static contact info (could be dynamic if available)
        let contactDetails = '';
        if (sellerName === 'You') {
          contactDetails = '<div class="seller-lg"><strong>Contact:</strong> 077-123-4567<br>Email: you@example.com</div>';
        } else if (sellerName === 'Kasun Perera') {
          contactDetails = '<div class="seller-lg"><strong>Contact:</strong> 077-987-6543<br>Email: kasun.perera@petvet.lk</div>';
        } else if (sellerName === 'Nirmala') {
          contactDetails = '<div class="seller-lg"><strong>Contact:</strong> 076-555-1212<br>Email: nirmala@example.com</div>';
        } else {
          contactDetails = '<div class="seller-lg"><strong>Contact:</strong> Not available</div>';
        }

        qs('#detailsTitle').textContent = title;
        qs('#detailsBody').innerHTML = `
          <div class="details details-modal-custom">
            <div class="details-img-wrap">
              <img src="${img}" alt="${title}">
            </div>
            <div class="details-info">
              <div class="price-lg highlight">${money(price)}</div>
              <div class="meta-lg"><span class="pet-species">${meta}</span></div>
              <p class="desc-lg">${desc}</p>
              <div class="seller-lg">
                <div class="seller-row">
                  <span class="seller-label">Posted by</span>
                  <span class="seller-name"><strong>${sellerName}</strong></span>
                  <span class="seller-loc">${sellerLoc}</span>
                </div>
                <div class="contact-row">
                  <span class="contact-label">Contact</span>
                  <span class="contact-details">${contactDetails}</span>
                </div>
              </div>
            </div>
          </div>
        `;
        openModal('detailsModal');
      }
    });

    // Sell form (demo adds to DOM only)
    qs('#sellForm').addEventListener('submit', (e)=>{
      e.preventDefault();
      const fd = new FormData(e.target);
      const badges = fd.getAll('badges[]');
      const species = fd.get('species');
      const price = fd.get('price');

      const card = document.createElement('article');
      card.className = 'card';
      card.dataset.species = species;
      card.dataset.price = price;
      card.innerHTML = `
        <div class="media">
          <img src="${fd.get('image')}" alt="${fd.get('name')}">
          <span class="price">${money(price)}</span>
        </div>
        <div class="body">
          <div class="line1">
            <h3>${fd.get('name')}</h3>
            <span class="meta">${species} • ${fd.get('breed')} • ${fd.get('age')}</span>
          </div>
          <p class="desc">${fd.get('desc')}</p>
          <div class="badges">${badges.map(b=>`<span class="badge">${b}</span>`).join('')}</div>
          <div class="seller"><span class="seller-name">Posted by <strong><?= htmlspecialchars($currentUser['name']) ?></strong></span><span class="seller-loc">Colombo</span></div>
        </div>
        <div class="actions-row">
          <button class="btn ghost view">View Details</button>
          <button class="btn buy">Buy Now</button>
        </div>
      `;
      grid.prepend(card);
      closeModal('sellModal');
      e.target.reset();
      applyFilters();
      alert('Listing published (demo only).');
    });

    // My listings modal actions (demo)
    const myWrap = qs('#myListingsWrap');
    if(myWrap){
      myWrap.addEventListener('click', e=>{
        const card = e.target.closest('.mini-card'); if(!card) return;
        if(e.target.classList.contains('mark-sold')){
          card.classList.toggle('sold');
          e.target.textContent = card.classList.contains('sold') ? 'Sold' : 'Mark as Sold';
        }
        if(e.target.classList.contains('remove')){
          card.remove();
        }
      });
    }
  </script>
</body>
</html>
