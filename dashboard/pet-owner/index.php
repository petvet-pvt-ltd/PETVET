<?php
$currentPage = basename($_SERVER['PHP_SELF']);

// Simulated pet details array (as if fetched from a database)
$pets = array(
  array(
    'id' => 1,
    'name' => 'Rocky',
    'species' => 'Dog',
    'breed' => 'Golden Retriever',
    'sex' => 'Male',
    'date_of_birth' => '2022-04-15',
    'weight' => '30.5',
    'color' => 'Golden',
    'allergies' => 'None',
    'notes' => 'Loves people. No known allergies.',
    'photo' => '../../images/sample-dog.jpg',
    'owner' => 'John Doe'
  ),
  array(
    'id' => 2,
    'name' => 'Whiskers',
    'species' => 'Cat',
    'breed' => 'Siamese',
    'sex' => 'Female',
    'date_of_birth' => '2023-01-10',
    'weight' => '4.2',
    'color' => 'Cream with brown points',
    'allergies' => 'Fish',
    'notes' => 'Very playful. Needs special diet.',
    'photo' => '../../images/sample-dog.jpg',
    'owner' => 'Jane Smith'
  ),
  array(
    'id' => 3,
    'name' => 'Tweety',
    'species' => 'Bird',
    'breed' => 'Canary',
    'sex' => 'Unknown',
    'date_of_birth' => '2024-06-01',
    'weight' => '0.03',
    'color' => 'Yellow',
    'allergies' => 'None',
    'notes' => 'Sings every morning.',
    'photo' => '../../images/sample-dog.jpg',
    'owner' => 'Alice Brown'
  )
);

// Helper function to calculate age from date of birth
function calculateAge($dob) {
    $birthDate = new DateTime($dob);
    $today = new DateTime();
    $age = $today->diff($birthDate)->y;
    return $age;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>My Pets</title>
  <link rel="stylesheet" href="../../styles/dashboard/pet-owner/my-pets.css">
</head>
<body>
  <?php require_once '../sidebar.php'; ?>

  <main class="main-content">
    <header class="page-header">
      <h2>My Pets</h2>
      <button type="button" class="btn primary" id="addPetBtn">+ Add Pet</button>
    </header>

    <section class="pets-grid" id="petsGrid">
      <!-- Sample Pet Cards -->
      <?php foreach ($pets as $pet): ?>
      <article class="pet-card">
        <div class="pet-hero">
          <img src="../../images/sample-dog.jpg" alt="<?php echo $pet['name']; ?>">
        </div>
        <div class="pet-body">
          <div class="pet-title">
            <h3 class="pet-name"><?php echo $pet['name']; ?></h3>
          </div>
          <p class="pet-meta">
            <?php echo $pet['species']; ?> • 
            <?php echo $pet['breed']; ?> • 
            <?php echo calculateAge($pet['date_of_birth']); ?>y
          </p>
          <ul class="pet-tags">
            <li class="tag">Microchipped</li>
            <li class="tag success">Vaccinated</li>
          </ul>
          <p class="pet-notes">Loves people. No known allergies.</p>
        </div>
        <div class="pet-actions">
          <a href="pet-profile.php?id=<?php echo $pet['id']; ?>" class="btn">View Profile</a>
          <a href="../records/index.php?pet=<?php echo $pet['id']; ?>" class="btn">Medical Records</a>
          <a href="../appointments/book.php?pet=<?php echo $pet['id']; ?>" class="btn">Book Appointment</a>
          <button class="btn danger">Mark as Missing</button>
        </div>
      </article>
      <?php endforeach; ?>
    </section>
  </main>

  <dialog id="addPetDialog" class="dialog">
    <form method="dialog" class="dialog-card">
      <header class="dialog-header">
        <h3>Add New Pet</h3>
        <p class="dialog-subtitle">Fill in your pet's details</p>
      </header>
      <div class="dialog-body">
        <div class="form-section">
          <h4 class="section-title">Basic Information</h4>
          <div class="grid-2">
            <label class="field">
              <span>Name *</span>
              <input type="text" class="input" required placeholder="e.g. Buddy">
            </label>
            <label class="field">
              <span>Species *</span>
              <select class="select" required>
                <option value="">Select species</option>
                <option>Dog</option>
                <option>Cat</option>
                <option>Bird</option>
                <option>Other</option>
              </select>
            </label>
            <label class="field">
              <span>Breed</span>
              <input type="text" class="input" placeholder="e.g. Labrador">
            </label>
            <label class="field">
              <span>Sex</span>
              <select class="select">
                <option value="">Select sex</option>
                <option>Male</option>
                <option>Female</option>
                <option>Unknown</option>
              </select>
            </label>
            <label class="field">
              <span>Date of Birth</span>
              <input type="date" class="input">
            </label>
            <label class="field">
              <span>Weight (kg)</span>
              <input type="number" step="0.01" class="input" placeholder="e.g. 12.5">
            </label>
          </div>
        </div>

        <div class="form-section">
          <h4 class="section-title">Appearance</h4>
          <div class="grid-2">
            <label class="field field-col">
              <span>Color/Markings</span>
              <textarea class="input" rows="2" placeholder="e.g. Brown with white paws"></textarea>
            </label>
          </div>
        </div>

        <div class="form-section">
          <h4 class="section-title">Health & Notes</h4>
          <div class="grid-2">
            <label class="field field-col">
              <span>Allergies</span>
              <textarea class="input" rows="2" placeholder="List any known allergies"></textarea>
            </label>
            <label class="field field-col">
              <span>Additional Notes</span>
              <textarea class="input" rows="2" placeholder="Temperament, special needs, etc."></textarea>
            </label>
          </div>
        </div>

        <div class="form-section">
          <h4 class="section-title">Pet Photo</h4>
          <label class="field field-col">
            <span>Upload Photo</span>
            <input type="file" accept="image/*" class="input">
          </label>
    </div>
      </div>
      <footer class="dialog-actions">
        <button class="btn link" value="cancel">Cancel</button>
        <button class="btn primary" value="save">Save Pet</button>
      </footer>
    </form>
  </dialog>

  <?php
  // Pass pets array to JS
  echo '<script>window.petsData = ' . json_encode($pets) . ';</script>';
  ?>

  <!-- Improved Pet Profile Dialog -->
  <dialog id="petProfileDialog" class="dialog">
    <form method="dialog" class="dialog-card" id="petProfileForm" enctype="multipart/form-data">
      <header class="dialog-header">
        <h3>Edit Pet Profile</h3>
        <p class="dialog-subtitle">View and edit your pet's details</p>
      </header>
      <div class="dialog-body">
        <div class="form-section" style="align-items:center;display:flex;flex-direction:column;">
          <!-- Pet Image Preview -->
          <div id="petProfileImgWrap" style="margin-bottom:18px;">
            <img id="petProfileImg" src="../../images/sample-dog.jpg" alt="Pet Photo" style="width:110px;height:110px;object-fit:cover;border-radius:50%;border:2px solid #e5e7eb;box-shadow:0 2px 8px rgba(37,99,235,.10);background:#f5f7fb;">
          </div>
          <label class="btn link" style="margin-bottom:10px;cursor:pointer;">
            <input type="file" id="petProfileImgInput" accept="image/*" style="display:none;">
            <span>Change Photo</span>
          </label>
        </div>
        <div class="form-section">
          <h4 class="section-title">Basic Information</h4>
          <div class="grid-2">
            <label class="field">
              <span>Name *</span>
              <input type="text" class="input" name="name" required>
            </label>
            <label class="field">
              <span>Species *</span>
              <select class="select" name="species" required>
                <option value="">Select species</option>
                <option>Dog</option>
                <option>Cat</option>
                <option>Bird</option>
                <option>Other</option>
              </select>
            </label>
            <label class="field">
              <span>Breed</span>
              <input type="text" class="input" name="breed">
            </label>
            <label class="field">
              <span>Sex</span>
              <select class="select" name="sex">
                <option value="">Select sex</option>
                <option>Male</option>
                <option>Female</option>
                <option>Unknown</option>
              </select>
            </label>
            <label class="field">
              <span>Date of Birth</span>
              <input type="date" class="input" name="date_of_birth">
            </label>
            <label class="field">
              <span>Weight (kg)</span>
              <input type="number" step="0.01" class="input" name="weight">
            </label>
          </div>
        </div>
        <div class="form-section">
          <h4 class="section-title">Appearance</h4>
          <div class="grid-2">
            <label class="field field-col">
              <span>Color/Markings</span>
              <textarea class="input" name="color" rows="2"></textarea>
            </label>
          </div>
        </div>
        <div class="form-section">
          <h4 class="section-title">Health & Notes</h4>
          <div class="grid-2">
            <label class="field field-col">
              <span>Allergies</span>
              <textarea class="input" name="allergies" rows="2"></textarea>
            </label>
            <label class="field field-col">
              <span>Additional Notes</span>
              <textarea class="input" name="notes" rows="2"></textarea>
            </label>
          </div>
        </div>
        <div class="form-section">
          <h4 class="section-title">Pet Photo</h4>
          <label class="field field-col">
            <span>Upload Photo</span>
            <input type="file" accept="image/*" class="input" id="petProfileImgInput2">
          </label>
        </div>
      </div>
      <footer class="dialog-actions">
        <button class="btn link" value="cancel">Cancel</button>
        <button class="btn primary" value="save">Save Changes</button>
      </footer>
    </form>
  </dialog>

  <script>
    (function () {
      const addBtns = [document.getElementById('addPetBtn')].filter(Boolean);
      const dlg = document.getElementById('addPetDialog');
      addBtns.forEach(b => b.addEventListener('click', () => dlg.showModal()));
      dlg.addEventListener('click', (e) => { if (e.target === dlg) dlg.close('cancel'); });
    })();

    (function () {
      // Pet profile dialog logic
      const petProfileDialog = document.getElementById('petProfileDialog');
      const petProfileForm = document.getElementById('petProfileForm');
      const petProfileImg = document.getElementById('petProfileImg');
      const petProfileImgInput = document.getElementById('petProfileImgInput');

      // Attach click event to all "View Profile" buttons
      document.querySelectorAll('.pet-actions .btn').forEach(btn => {
        if (btn.textContent.trim() === 'View Profile') {
          btn.addEventListener('click', function(e) {
            e.preventDefault();
            // Get pet id from href
            const url = new URL(btn.href, window.location.origin);
            const petId = url.searchParams.get('id');
            const pet = window.petsData.find(p => p.id == petId);
            if (pet) {
              // Fill form fields
              petProfileForm.name.value = pet.name || '';
              petProfileForm.species.value = pet.species || '';
              petProfileForm.breed.value = pet.breed || '';
              petProfileForm.sex.value = pet.sex || '';
              petProfileForm.date_of_birth.value = pet.date_of_birth || '';
              petProfileForm.weight.value = pet.weight || '';
              petProfileForm.color.value = pet.color || '';
              petProfileForm.allergies.value = pet.allergies || '';
              petProfileForm.notes.value = pet.notes || '';
              petProfileImg.src = pet.photo || '../../images/sample-dog.jpg';
              petProfileImgInput.value = ""; // Reset file input
              petProfileDialog.showModal();
            }
          });
        }
      });

      // Preview new image when selected
      petProfileImgInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = function(ev) {
            petProfileImg.src = ev.target.result;
          };
          reader.readAsDataURL(file);
        }
      });

      petProfileDialog.addEventListener('click', (e) => {
        if (e.target === petProfileDialog) petProfileDialog.close('cancel');
      });
    })();
  </script>
</body>
</html>
