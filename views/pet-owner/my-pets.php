<?php
$currentPage = basename($_SERVER['PHP_SELF']);
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
  <link rel="stylesheet" href="/PETVET/public/css/pet-owner/my-pets.css">
  <style>
    /* Prevent background scroll when dialog is open */
    body.dialog-open {
      overflow: hidden !important;
      position: fixed;
      width: 100%;
      height: 100%;
    }

    /* Make dialog footer buttons consistent across all popups on this page */
    dialog .dialog-actions {
      display: flex;
      gap: 12px;
      justify-content: flex-end;
      align-items: stretch;
    }
    dialog .dialog-actions .btn {
      min-width: 140px; /* same minimum width for all buttons */
      padding: 10px 18px;
      box-sizing: border-box;
      text-align: center;
      height: 44px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      line-height: 1.2;
      vertical-align: middle;
    }
    /* Ensure primary and ghost/outline buttons share height/line-height */
    dialog .dialog-actions .btn.primary,
    dialog .dialog-actions .btn.ghost,
    dialog .dialog-actions .btn.outline {
      height: 44px;
      line-height: 1.2;
    }

    /* Mobile responsive fixes for dialogs */
    @media (max-width: 768px) {
      dialog.dialog {
        width: 100vw !important;
        max-width: 100vw !important;
        height: 100vh !important;
        max-height: 100vh !important;
        margin: 0 !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        border-radius: 0 !important;
      }

      .dialog-card {
        max-height: 100vh;
        height: 100vh;
        border-radius: 0 !important;
        display: flex;
        flex-direction: column;
      }

      .dialog-header {
        flex-shrink: 0;
        padding: 20px 16px 12px !important;
      }

      .dialog-body {
        flex: 1;
        overflow-y: auto !important;
        overflow-x: hidden;
        -webkit-overflow-scrolling: touch;
        padding: 20px 16px !important;
        max-height: none !important;
      }

      .dialog-actions {
        flex-shrink: 0;
        padding: 16px !important;
      }

      .grid-2 {
        grid-template-columns: 1fr !important;
        gap: 12px !important;
      }

      .field-col {
        grid-column: span 1 !important;
      }
    }

    @media (max-width: 480px) {
      .page-header {
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
      }
      
      .page-header .btn {
        width: 100%;
      }

      dialog .dialog-actions {
        flex-direction: column;
        gap: 10px;
      }

      dialog .dialog-actions .btn {
        width: 100%;
        min-width: 100%;
      }
    }
  </style>
</head>
<body>
  <?php //require_once '../sidebar.php'; ?>

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
          <a href="pet-profile.php?id=<?php echo $pet['id']; ?>" class="btn outline">View Profile</a>

          <!-- ✅ Route through the controller (no direct /views/ link) -->
          <a href="/PETVET/?module=pet-owner&page=medical-records&pet=<?php echo (int)$pet['id']; ?>" class="btn outline">Medical Records</a>

          <a href="#book-appointment?pet=<?php echo $pet['id']; ?>" class="btn primary">Book Appointment</a>
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
        <button class="btn ghost" value="cancel">Cancel</button>
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
          <label class="btn ghost" style="margin-bottom:10px;cursor:pointer;">
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
        <button class="btn ghost" value="cancel">Cancel</button>
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
        <button class="btn ghost" value="cancel">Cancel</button>
        <button class="btn primary" value="submit">Report</button>
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
        <!-- Section A: Appointment Type & Symptoms -->
        <div class="form-section">
          <h4 class="section-title">Appointment Details</h4>
          <label class="field">
            <span>Appointment Type *</span>
            <select class="select" name="appointment_type" id="appointmentType" required>
              <option value="">Select Appointment Type</option>
              <option value="routine">Routine Check-up</option>
              <option value="vaccination">Vaccination</option>
              <option value="grooming">Grooming</option>
              <option value="dental">Dental Cleaning</option>
              <option value="illness">Illness/Injury Consultation</option>
              <option value="surgery">Surgery</option>
              <option value="emergency">Emergency</option>
              <option value="other">Other</option>
            </select>
          </label>
          <label class="field field-col">
            <span>Symptoms / Reason for Visit *</span>
            <textarea class="input" name="symptoms" id="appointmentSymptoms" rows="3" placeholder="Please describe symptoms, concerns, or reason for visit..." required></textarea>
          </label>
        </div>

        <!-- Section B: Clinic Selection -->
        <div class="form-section">
          <h4 class="section-title">Select Clinic</h4>
          <label class="field">
            <span>Clinic Location *</span>
            <select class="select" name="clinic" id="clinicSelect" required>
              <option value="">Select a Clinic</option>
              <?php foreach($clinics as $clinic): ?>
                <option value="<?= $clinic['id'] ?>" data-address="<?= htmlspecialchars($clinic['address']) ?>" data-phone="<?= htmlspecialchars($clinic['phone']) ?>">
                  <?= htmlspecialchars($clinic['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </label>
          <div id="clinicInfo" style="display:none;">
            <p style="margin:0;"><strong id="clinicInfoName"></strong></p>
            <p style="margin:4px 0 0;">
              📍 <span id="clinicInfoAddress"></span><br>
              📞 <span id="clinicInfoPhone"></span>
            </p>
          </div>
        </div>

        <!-- Section C: Veterinarian Selection -->
        <div class="form-section" id="vetSection" style="display:none;">
          <h4 class="section-title">Select Veterinarian</h4>
          <div id="vetsList" class="vets-grid">
            <!-- Vets will be loaded here dynamically -->
          </div>
          <input type="hidden" name="vet_id" id="selectedVetId">
        </div>

        <!-- Section D: Date & Time -->
        <div class="form-section" id="dateTimeSection" style="display:none;">
          <h4 class="section-title">Date & Time</h4>
          <div class="grid-2">
            <label class="field">
              <span>Select Date *</span>
              <input type="date" class="input" name="date" id="appointmentDate" required>
            </label>
            <label class="field">
              <span>Enter Time *</span>
              <input type="time" class="input" name="time" id="appointmentTime" required>
            </label>
          </div>
          <div id="timeValidation">
            <span id="timeValidationMessage"></span>
          </div>
          <p style="margin:8px 0 0; font-size:11px; color:#64748b;">
            ⏱️ Default slot duration: 20 minutes
          </p>
        </div>

        <!-- Important Notice -->
        <div class="form-section" id="noticeSection" style="display:none;">
          <div>
            <p>
              <strong>⚠️ Important:</strong> If you arrive late to your appointment, it may be cancelled. Please arrive 10 minutes early.
            </p>
          </div>
        </div>
      </div>

      <!-- Confirmation View (hidden initially) -->
      <div class="dialog-body" id="appointmentConfirmation" style="display:none;text-align:center;">
        <div style="font-size:64px; margin-bottom:16px;">✅</div>
        <h3 style="color:#16a34a; margin-bottom:16px;">Appointment Confirmed!</h3>
        <div id="appointmentSummary" style="text-align:left; background:#f8fafc; padding:20px; border-radius:8px; margin-top:20px;">
        </div>
      </div>

      <!-- Review/Confirmation View (before final confirmation) -->
      <div class="dialog-body" id="appointmentReview" style="display:none;">
        <div style="text-align:center; margin-bottom:20px;">
          <div style="font-size:48px; margin-bottom:12px;">📋</div>
          <h3 style="color:#1e293b; margin-bottom:8px; font-size:20px;">Review Your Appointment</h3>
          <p style="color:#64748b; font-size:13px; margin:0;">Please review the details before confirming</p>
        </div>
        <div id="appointmentReviewSummary" style="background:#f8fafc; padding:16px; border-radius:8px; border:1px solid #e2e8f0;">
        </div>
      </div>

      <footer class="dialog-actions">
        <button class="btn ghost" type="button" value="cancel" id="appointmentCancelBtn">Cancel</button>
        <button class="btn outline" type="button" id="appointmentBackBtn" style="display:none;">Back</button>
        <button class="btn primary" type="button" value="save" id="appointmentConfirmBtn" disabled>Confirm Appointment</button>
        <button class="btn primary" type="button" id="appointmentFinalConfirmBtn" style="display:none;">Yes, Book Appointment</button>
      </footer>
    </form>
  </dialog>


  <?php
  // Pass pets array and clinics to JS
  echo '<script>window.petsData = ' . json_encode($pets) . ';</script>';
  echo '<script>window.clinicsData = ' . json_encode($clinics) . ';</script>';
  ?>
  <script src="/PETVET/public/js/pet-owner/my-pets.js"></script>
  <script>
    // Prevent background scroll when dialog is open
    (function() {
      const dialogs = document.querySelectorAll('dialog');
      
      dialogs.forEach(dialog => {
        // When dialog opens
        dialog.addEventListener('click', function(e) {
          if (e.target === dialog) {
            // Clicked on backdrop
            document.body.classList.remove('dialog-open');
          }
        });

        // MutationObserver to detect when dialog opens/closes
        const observer = new MutationObserver(function(mutations) {
          mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'open') {
              if (dialog.hasAttribute('open')) {
                document.body.classList.add('dialog-open');
              } else {
                document.body.classList.remove('dialog-open');
              }
            }
          });
        });

        observer.observe(dialog, {
          attributes: true
        });

        // Handle form close
        dialog.addEventListener('close', function() {
          document.body.classList.remove('dialog-open');
        });

        // Handle cancel button
        const cancelButtons = dialog.querySelectorAll('button[value="cancel"]');
        cancelButtons.forEach(btn => {
          btn.addEventListener('click', function() {
            document.body.classList.remove('dialog-open');
          });
        });
      });

      // Also handle programmatic showModal calls
      const originalShowModal = HTMLDialogElement.prototype.showModal;
      HTMLDialogElement.prototype.showModal = function() {
        originalShowModal.call(this);
        document.body.classList.add('dialog-open');
      };

      const originalClose = HTMLDialogElement.prototype.close;
      HTMLDialogElement.prototype.close = function() {
        originalClose.call(this);
        document.body.classList.remove('dialog-open');
      };
    })();
  </script>
</body>
</html>
