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
      <?php foreach ($pets as $pet): ?>
      <article class="pet-card">
        <div class="pet-hero">
          <img src="<?php echo $pet['photo']; ?>" alt="<?php echo $pet['name']; ?>">
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
          <p class="pet-notes"><?php echo $pet['notes']; ?></p>
        </div>
        <div class="pet-actions">
          <a href="pet-profile.php?id=<?php echo $pet['id']; ?>" class="btn">View Profile</a>
          <a href="medical-records.php?pet=<?php echo $pet['id']; ?>" class="btn">Medical Records</a>
          <a href="../appointments/book.php?pet=<?php echo $pet['id']; ?>" class="btn">Book Appointment</a>
          <button class="btn danger markMissingBtn" data-pet="<?php echo $pet['id']; ?>">Mark as Missing</button>
        </div>
      </article>
      <?php endforeach; ?>
    </section>
  </main>

  <!-- Add Pet Dialog -->
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

  <!-- Pet Profile Dialog -->
  <dialog id="petProfileDialog" class="dialog">
    <form method="dialog" class="dialog-card" id="petProfileForm" enctype="multipart/form-data">
      <header class="dialog-header">
        <h3>Edit Pet Profile</h3>
        <p class="dialog-subtitle">View and edit your pet's details</p>
      </header>
      <div class="dialog-body">
        <div class="form-section" style="align-items:center;display:flex;flex-direction:column;">
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

  <!-- Mark as Missing Dialog -->
  <dialog id="markMissingDialog" class="dialog">
    <form method="dialog" class="dialog-card" id="markMissingForm">
      <header class="dialog-header">
        <h3>Report Missing Pet</h3>
        <p class="dialog-subtitle">Please provide details to help find your pet</p>
      </header>
      <div class="dialog-body">
        <div class="form-section">
          <label class="field field-col">
            <span>Last Seen Location *</span>
            <input type="text" class="input" name="location" required placeholder="Enter address or location">
          </label><br>
          <label class="field">
            <span>Last Seen Date & Time *</span>
            <input type="datetime-local" class="input" name="datetime" required>
          </label><br>
          <label class="field field-col">
            <span>Circumstances</span>
            <textarea class="input" name="circumstances" rows="2" maxlength="250" placeholder="How did your pet go missing?"></textarea>
          </label><br>
          <label class="field">
            <span>Distinguishing Features</span>
            <input type="text" class="input" name="features" placeholder="Special markings, collar, etc.">
          </label><br>
          <div class="grid-2">
            <label class="field">
              <span>Contact Phone</span>
              <input type="tel" class="input" name="phone" value="+94 77 123 4567">
            </label>
            <label class="field">
              <span>Contact Email</span>
              <input type="email" class="input" name="email" value="user@example.com">
            </label>
          </div><br>
          <label class="field field-col">
            <span>Upload Photos</span>
            <input type="file" class="input" name="photos[]" accept="image/*" multiple>
          </label><br>
          <div class="grid-2">
            <label class="field">
              <span><input type="checkbox" id="rewardCheckbox">
              Offer reward for safe return</span>
            </label><br>
            <label class="field" id="rewardAmountWrap" style="display:none;">
              <span>Reward Amount</span>
              <input type="number" class="input" name="reward" step="0.01" placeholder="e.g. 5000.00">
            </label><br>
          </div><br>
        </div>
      </div>
      <footer class="dialog-actions">
        <button class="btn link" value="cancel">Cancel</button>
        <button class="btn primary" value="submit">Submit Report</button>
      </footer>
    </form>
  </dialog>
  
    <!-- Book Appointment Dialog -->
  <dialog id="bookAppointmentDialog" class="dialog">
    <form method="dialog" class="dialog-card" id="bookAppointmentForm">
      <header class="dialog-header">
        <h3 id="appointmentHeader">Book Appointment</h3>
        <p class="dialog-subtitle" id="appointmentPetInfo">Pet Info</p>
        <p class="dialog-subtitle" id="appointmentHealthNotes" style="color:#b91c1c;"></p>
      </header>

      <div class="dialog-body" id="appointmentFormContent">
        <!-- Section A: Appointment Type & Reason -->
        <div class="form-section">
          <h4 class="section-title">Appointment Details</h4><br>
          <label class="field">
            <span>Appointment Type *</span>
            <select class="select" name="appointment_type" required>
              <option value="">Select Appointment Type</option>
              <option>Routine Check-up</option>
              <option>Vaccination</option>
              <option>Grooming</option>
              <option>Dental Cleaning</option>
              <option>Illness/Injury Consultation</option>
              <option>Surgery</option>
              <option>Other</option>
            </select>
          </label><br>
          <label class="field field-col">
            <span>Reason for Visit / Symptoms</span>
            <textarea class="input" name="reason" rows="2" placeholder="Please describe any symptoms or concerns"></textarea>
          </label><br>
        </div>

        <!-- Section B: Date & Time -->
        <div class="form-section">
          <h4 class="section-title">Date & Time</h4>
          <label class="field">
            <span>Select a Date *</span>
            <input type="date" class="input" name="date" required>
          </label><br>
          <div class="time-slots" id="timeSlotsWrap" style="margin-top:10px;">
            <!-- JS will generate available time buttons here -->
          </div>
        </div>

        <!-- Section C: Veterinarian -->
        <div class="form-section">
          <h4 class="section-title">Veterinarian</h4>
          <label class="field">
            <span>Preferred Veterinarian (Optional)</span>
            <select class="select" name="vet">
              <option value="">Any Available Vet</option>
              <option>Dr. Smith</option>
              <option>Dr. Jones</option>
            </select>
          </label><br>
        </div>

        <!-- Section D: Clinic Location -->
        <div class="form-section">
          <h4 class="section-title">Clinic Location</h4>
          <label class="field">
            <span>Select Clinic Location *</span>
            <select class="select" name="location" required>
              <option value="">Select Location</option>
              <option>Main Clinic</option>
              <option>Branch A</option>
              <option>Branch B</option>
            </select>
          </label><br>
        </div>

        

      <!-- Confirmation View (hidden initially) -->
      <div class="dialog-body" id="appointmentConfirmation" style="display:none;text-align:center;">
        <div style="font-size:48px;color:#16a34a;">✔</div>
        <h3>Appointment Confirmed!</h3>
        <p id="appointmentSummary"></p>
      </div>

      <footer class="dialog-actions">
        <button class="btn link" value="cancel" id="appointmentCancelBtn">Cancel</button>
        <button class="btn primary" value="save" id="appointmentConfirmBtn" disabled>Confirm Booking</button>
      </footer>
    </form>
  </dialog>


  <?php
  // Pass pets array to JS
  echo '<script>window.petsData = ' . json_encode($pets) . ';</script>';
  ?>
  <script src="my-pets.js"></script>
</body>
</html>
