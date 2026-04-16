// my-pets.js
document.addEventListener("DOMContentLoaded", () => {

  // Open a popup
  function openDialog(dialogId) {
    const dialog = document.getElementById(dialogId);
    if (dialog) dialog.showModal();
  }

  // Close a popup
  function closeDialog(dialogId) {
    const dialog = document.getElementById(dialogId);
    if (dialog) dialog.close("cancel");
  }

  // ----- Add Pet -----
  const addPetBtn = document.getElementById("addPetBtn");
  const addPetDialog = document.getElementById("addPetDialog");
  const addPetForm = document.getElementById("addPetForm");

  if (addPetBtn && addPetDialog) {
    addPetBtn.addEventListener("click", () => openDialog("addPetDialog"));
    addPetDialog.querySelectorAll("button[value='cancel']").forEach(btn => {
      btn.addEventListener("click", () => closeDialog("addPetDialog"));
    });
  }

  // Handle add pet form submission
  if (addPetForm) {
    addPetForm.addEventListener("submit", async (e) => {
      e.preventDefault();

      const formData = new FormData(addPetForm);

      try {
        const response = await fetch('/PETVET/api/pet-owner/pets/add.php', {
          method: 'POST',
          body: formData
        });

        const result = await response.json();

        if (result.success) {
          showToast('Pet added successfully!');
          closeDialog("addPetDialog");
          
          // Reload page to show new pet
          setTimeout(() => {
            window.location.reload();
          }, 1000);
        } else {
          showToast('Error: ' + result.message);
        }
      } catch (error) {
        console.error('Add pet error:', error);
        showToast('Error adding pet');
      }
    });
  }

  // ----- Pet Profile -----
  const petProfileDialog = document.getElementById("petProfileDialog");
  const petProfileForm = document.getElementById("petProfileForm");
  const petProfileImg = document.getElementById("petProfileImg");
  const petProfileImgInput = document.getElementById("petProfileImgInput");
  const editPetId = document.getElementById("editPetId");

  document.querySelectorAll(".pet-actions .btn[href*='pet-profile']").forEach(btn => {
    btn.addEventListener("click", e => {
      e.preventDefault();
      const url = new URL(btn.href, window.location.origin);
      const petId = url.searchParams.get("id");
      const pet = window.petsData.find(p => p.id == petId);

      if (pet && petProfileForm) {
        // Set pet ID for update
        editPetId.value = pet.id;
        
        // Fill form with pet data
        for (let field in pet) {
          if (petProfileForm[field]) petProfileForm[field].value = pet[field];
        }
        // Show image
        petProfileImg.src = pet.photo || "../../images/sample-dog.jpg";
        petProfileImgInput.value = "";
        openDialog("petProfileDialog");
      }
    });
  });

  // Cancel button for profile popup
  if (petProfileDialog) {
    petProfileDialog.querySelectorAll("button[value='cancel']").forEach(btn => {
      btn.addEventListener("click", () => closeDialog("petProfileDialog"));
    });
  }

  // Change profile image preview
  if (petProfileImgInput) {
    petProfileImgInput.addEventListener("change", e => {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = ev => { petProfileImg.src = ev.target.result; };
        reader.readAsDataURL(file);
      }
    });
  }

  // Handle edit pet form submission
  if (petProfileForm) {
    petProfileForm.addEventListener("submit", async (e) => {
      e.preventDefault();

      const formData = new FormData(petProfileForm);

      try {
        const response = await fetch('/PETVET/api/pet-owner/pets/update.php', {
          method: 'POST',
          body: formData
        });

        const result = await response.json();

        if (result.success) {
          showToast('Pet profile updated successfully!');
          closeDialog("petProfileDialog");
          
          // Reload page to show updated pet
          setTimeout(() => {
            window.location.reload();
          }, 1000);
        } else {
          showToast('Error: ' + result.message);
        }
      } catch (error) {
        console.error('Update pet error:', error);
        showToast('Error updating pet profile');
      }
    });
  }

  // ----- Mark Missing -----
  const missingDialog = document.getElementById("markMissingDialog");
  const rewardCheckbox = document.getElementById("rewardCheckbox");
  const rewardAmountWrap = document.getElementById("rewardAmountWrap");
  const dateField = missingDialog?.querySelector("input[name='date']");
  const timeField = missingDialog?.querySelector("input[name='time']");
  const missingLocationInput = missingDialog?.querySelector("input[name='location']");
  const missingLatInput = missingDialog?.querySelector("input[name='latitude']");
  const missingLngInput = missingDialog?.querySelector("input[name='longitude']");
  const missingPhotosInput = document.getElementById("missingPhotos");
  const missingPhotoPreview = document.getElementById("missingPhotoPreview");
  const missingPetName = document.getElementById("missingPetName");
  const missingPetSpecies = document.getElementById("missingPetSpecies");
  const missingPetBreed = document.getElementById("missingPetBreed");
  const missingPetColor = document.getElementById("missingPetColor");
  const markMissingForm = document.getElementById("markMissingForm");
  const missingPetsData = window.petsData || [];
  let currentMissingPetId = null;
  let missingMap = null;
  let missingMarker = null;

  // Open missing popup
  document.querySelectorAll(".markMissingBtn").forEach(btn => {
    btn.addEventListener("click", e => {
      e.preventDefault();
      currentMissingPetId = btn.getAttribute("data-pet");

      const pet = missingPetsData.find(p => String(p.id) === String(currentMissingPetId));
      if (pet) {
        if (missingPetName) missingPetName.value = pet.name || "";
        if (missingPetSpecies) missingPetSpecies.value = pet.species || "";
        if (missingPetBreed) missingPetBreed.value = pet.breed || "Unknown";
        if (missingPetColor) missingPetColor.value = pet.color || "Unknown";
      }

      if (rewardAmountWrap && rewardCheckbox) {
        rewardAmountWrap.style.display = rewardCheckbox.checked ? "block" : "none";
      }

      openDialog("markMissingDialog");

      // Initialize map after dialog opens
      setTimeout(() => {
        initMissingMap();
      }, 120);
    });
  });

  // Cancel button for missing popup
  if (missingDialog) {
    missingDialog.querySelectorAll("button[value='cancel']").forEach(btn => {
      btn.addEventListener("click", () => {
        closeDialog("markMissingDialog");
        resetMarkMissingFormUi();
      });
    });
  }

  // Show/Hide reward input
  if (rewardCheckbox && rewardAmountWrap) {
    rewardCheckbox.addEventListener("change", () => {
      rewardAmountWrap.style.display = rewardCheckbox.checked ? "block" : "none";
    });
  }

  // Set current date and time
  if (dateField && timeField) {
    const now = new Date();
    dateField.value = now.toISOString().slice(0, 10);
    timeField.value = now.toTimeString().slice(0, 5);
  }

  if (missingPhotosInput && missingPhotoPreview) {
    missingPhotosInput.addEventListener("change", () => {
      missingPhotoPreview.innerHTML = "";
      let files = Array.from(missingPhotosInput.files || []);

      if (files.length > 3) {
        files = files.slice(0, 3);
        showToast("You can upload up to 3 photos.");
      }

      if (files.length === 0) {
        missingPhotoPreview.style.display = "none";
        return;
      }

      missingPhotoPreview.style.display = "flex";
      files.forEach((file, idx) => {
        const reader = new FileReader();
        reader.onload = (ev) => {
          const img = document.createElement("img");
          img.src = ev.target.result;
          img.alt = `Photo preview ${idx + 1}`;
          img.style.width = "88px";
          img.style.height = "88px";
          img.style.objectFit = "cover";
          img.style.borderRadius = "8px";
          img.style.border = "2px solid #dbe3f0";
          missingPhotoPreview.appendChild(img);
        };
        reader.readAsDataURL(file);
      });
    });
  }

  function initMissingMap() {
    if (!missingDialog || missingDialog.open !== true) return;

    if (missingMap) {
      missingMap.invalidateSize();
      return;
    }

    if (typeof L === "undefined") {
      console.error("Leaflet is not loaded.");
      return;
    }

    const defaultLat = 6.9271;
    const defaultLng = 79.8612;

    missingMap = L.map("missingMapContainer").setView([defaultLat, defaultLng], 13);
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
      attribution: "&copy; OpenStreetMap contributors",
      maxZoom: 19
    }).addTo(missingMap);

    missingMap.on("click", (e) => {
      const { lat, lng } = e.latlng;
      if (missingLatInput) missingLatInput.value = lat.toFixed(6);
      if (missingLngInput) missingLngInput.value = lng.toFixed(6);

      if (missingMarker) {
        missingMarker.setLatLng([lat, lng]);
      } else {
        missingMarker = L.marker([lat, lng]).addTo(missingMap);
      }

      fetchAddress(lat, lng);
    });

    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        (position) => {
          const { latitude, longitude } = position.coords;
          missingMap.setView([latitude, longitude], 15);
        },
        () => {}
      );
    }
  }

  async function fetchAddress(lat, lng) {
    if (!missingLocationInput) return;
    missingLocationInput.value = "Getting location...";

    try {
      const response = await fetch(`/PETVET/api/pet-owner/reverse-geocode.php?lat=${lat}&lng=${lng}`);
      const data = await response.json();

      if (data.success && data.location) {
        missingLocationInput.value = data.location;
      } else if (data.fallback) {
        missingLocationInput.value = data.fallback;
      } else {
        missingLocationInput.value = `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
      }
    } catch (err) {
      missingLocationInput.value = `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
    }
  }

  function resetMarkMissingFormUi() {
    if (missingPhotoPreview) {
      missingPhotoPreview.innerHTML = "";
      missingPhotoPreview.style.display = "none";
    }
    if (missingLocationInput) missingLocationInput.value = "";
    if (missingLatInput) missingLatInput.value = "";
    if (missingLngInput) missingLngInput.value = "";
    if (missingMarker && missingMap) {
      missingMap.removeLayer(missingMarker);
      missingMarker = null;
    }
    if (dateField && timeField) {
      const now = new Date();
      dateField.value = now.toISOString().slice(0, 10);
      timeField.value = now.toTimeString().slice(0, 5);
    }
  }

  // Submit mark missing report to Lost & Found
  if (markMissingForm) {
    markMissingForm.addEventListener("submit", async (e) => {
      e.preventDefault();

      if (!currentMissingPetId) {
        showToast("Unable to identify pet");
        return;
      }

      const locationEl = markMissingForm.querySelector("input[name='location']");
      const dateEl = markMissingForm.querySelector("input[name='date']");
      const timeEl = markMissingForm.querySelector("input[name='time']");
      const circumstancesEl = markMissingForm.querySelector("textarea[name='circumstances']");
      const featuresEl = markMissingForm.querySelector("input[name='features']");
      const rewardEl = markMissingForm.querySelector("input[name='reward']");
      const submitBtn = markMissingForm.querySelector("button[value='submit']");

      if (!locationEl?.value || !dateEl?.value || !timeEl?.value) {
        showToast("Please fill required fields");
        return;
      }

      if (!missingLatInput?.value || !missingLngInput?.value) {
        showToast("Please select the last seen location on the map");
        return;
      }

      const originalText = submitBtn ? submitBtn.textContent : "Report";
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = "Submitting...";
      }

      try {
        const formData = new FormData();
        formData.append("pet_id", currentMissingPetId);
        formData.append("location", locationEl.value);
        formData.append("date", dateEl.value);
        formData.append("time", timeEl.value);
        formData.append("latitude", missingLatInput.value);
        formData.append("longitude", missingLngInput.value);
        formData.append("circumstances", circumstancesEl?.value || "");
        formData.append("features", featuresEl?.value || "");

        if (rewardCheckbox?.checked && rewardEl?.value) {
          formData.append("reward", rewardEl.value);
        }

        if (missingPhotosInput?.files?.length) {
          Array.from(missingPhotosInput.files).slice(0, 3).forEach((file) => {
            formData.append("photos[]", file);
          });
        }

        const response = await fetch("/PETVET/api/pet-owner/mark-pet-missing.php", {
          method: "POST",
          body: formData
        });

        const result = await response.json();

        if (result.success) {
          closeDialog("markMissingDialog");
          markMissingForm.reset();
          rewardAmountWrap.style.display = "none";
          resetMarkMissingFormUi();
          showToast("Pet marked as missing successfully");

          setTimeout(() => {
            window.location.href = "/PETVET/?module=pet-owner&page=lost-found";
          }, 900);
        } else {
          showToast("Error: " + (result.message || "Failed to mark missing"));
        }
      } catch (error) {
        console.error("Mark missing error:", error);
        showToast("Error submitting missing pet report");
      } finally {
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.textContent = originalText;
        }
      }
    });
  }

  // ----- Book Appointment -----
  const pets = window.petsData || [];
  const bookBtns = document.querySelectorAll(".btn[href*='book-appointment']");
  const bookDialog = document.getElementById("bookAppointmentDialog");
  const bookForm = document.getElementById("bookAppointmentForm");
  const header = document.getElementById("appointmentHeader");
  const petInfo = document.getElementById("appointmentPetInfo");
  const healthNotes = document.getElementById("appointmentHealthNotes");
  const confirmBtn = document.getElementById("appointmentConfirmBtn");
  const confirmationView = document.getElementById("appointmentConfirmation");
  const formContent = document.getElementById("appointmentFormContent");
  const summary = document.getElementById("appointmentSummary");
  const appointmentCancelBtn = document.getElementById("appointmentCancelBtn");
  
  // Review screen elements
  const appointmentReview = document.getElementById("appointmentReview");
  const appointmentReviewSummary = document.getElementById("appointmentReviewSummary");
  const appointmentBackBtn = document.getElementById("appointmentBackBtn");
  const appointmentFinalConfirmBtn = document.getElementById("appointmentFinalConfirmBtn");
  
  // Form fields
  const appointmentType = document.getElementById("appointmentType");
  const clinicSelect = document.getElementById("clinicSelect");
  const vetSection = document.getElementById("vetSection");
  const vetsList = document.getElementById("vetsList");
  const dateSection = document.getElementById("dateSection");
  const timeSection = document.getElementById("timeSection");
  const appointmentDate = document.getElementById("appointmentDate");
  const appointmentTime = document.getElementById("appointmentTime");
  const noticeSection = document.getElementById("noticeSection");
  const selectedVetId = document.getElementById("selectedVetId");
  
  let currentPet = null;
  let selectedVet = null;

  // Replace default link behavior with popup
  bookBtns.forEach(btn => {
    let touchHandled = false;
    
    const handleBooking = (e) => {
      e.preventDefault();
      e.stopPropagation();
      
      // Prevent double-firing on mobile (touchend + click)
      if (e.type === 'click' && touchHandled) {
        touchHandled = false;
        return;
      }
      
      if (e.type === 'touchend') {
        touchHandled = true;
        setTimeout(() => touchHandled = false, 500);
      }
      
      // Button is already disabled on server-side if pet has upcoming appointment
      if (btn.disabled || btn.tagName === 'BUTTON') {
        return;
      }
      
      const petId = btn.getAttribute("href")?.split("=")[1];
      if (!petId) return;
      
      currentPet = pets.find(p => p.id == petId);
      if (!currentPet) return;

      if (header) header.textContent = `Book Appointment for ${currentPet.name}`;
      if (petInfo) petInfo.textContent = `${currentPet.name} • ${currentPet.breed} • ${calculateAge(currentPet.date_of_birth)}y`;
      if (healthNotes) healthNotes.textContent = "";

      // Reset form
      resetAppointmentForm();
      
      // Open dialog with fallback for mobile
      if (bookDialog) {
        try {
          if (typeof bookDialog.showModal === 'function') {
            bookDialog.showModal();
          } else {
            // Fallback for browsers that don't support dialog.showModal()
            bookDialog.setAttribute('open', '');
            bookDialog.style.display = 'flex';
          }
        } catch (error) {
          // Ultimate fallback
          bookDialog.setAttribute('open', '');
          bookDialog.style.display = 'flex';
        }
      }
    };
    
    // Handle both touch and click events properly
    btn.addEventListener("touchend", handleBooking, { passive: false });
    btn.addEventListener("click", handleBooking);
  });

  function resetAppointmentForm() {
    bookForm.reset();
    formContent.style.display = "block";
    confirmationView.style.display = "none";
    appointmentReview.style.display = "none";
    confirmBtn.disabled = true;
    confirmBtn.style.display = "inline-flex";
    appointmentBackBtn.style.display = "none";
    appointmentFinalConfirmBtn.style.display = "none";
    appointmentCancelBtn.textContent = "Cancel";
    selectedVet = null;
    selectedVetId.value = "";
    
    // Hide all progressive sections
    vetSection.style.display = "none";
    dateSection.style.display = "none";
    timeSection.style.display = "none";
    noticeSection.style.display = "none";
    
    // Clear vet selection
    vetsList.innerHTML = "";
    
    // Reset calendar state
    if (typeof window.initializeCalendar === 'function') {
      selectedDate = null;
      disabledDates = [];
      currentClinicId = null;
      currentVetId = null;
    }
  }

  // Clinic selection handler
  clinicSelect.addEventListener("change", function() {
    if (this.value) {
      // Fetch vets for this clinic
      fetchVets(this.value);
      
      // Show vet section
      vetSection.style.display = "block";
      
      // Load disabled dates for calendar
      if (typeof window.loadDisabledDates === 'function') {
        window.loadDisabledDates(this.value);
      }
    } else {
      vetSection.style.display = "none";
      dateSection.style.display = "none";
      timeSection.style.display = "none";
      noticeSection.style.display = "none";
    }
    
    validateBookingForm();
  });

  // Fetch vets by clinic
  function fetchVets(clinicId) {
    vetsList.innerHTML = '<p style="text-align:center; color:#64748b;">Loading veterinarians...</p>';
    
    fetch(`/PETVET/api/get-vets.php?clinic_id=${clinicId}`)
      .then(response => response.json())
      .then(data => {
        if (data.success && data.vets) {
          displayVets(data.vets);
        } else {
          vetsList.innerHTML = '<p style="text-align:center; color:#ef4444;">Failed to load veterinarians</p>';
        }
      })
      .catch(error => {
        console.error('Error fetching vets:', error);
        vetsList.innerHTML = '<p style="text-align:center; color:#ef4444;">Error loading veterinarians</p>';
      });
  }

  // Display vets as selectable cards
  function displayVets(vets) {
    vetsList.innerHTML = '';
    
    // Add "Any Available Vet" option
    const anyVetCard = document.createElement('div');
    anyVetCard.className = 'vet-card any-available';
    anyVetCard.innerHTML = `
      <span class="vet-icon">👨‍⚕️</span>
      <p class="vet-name">Any Available Vet</p>
      <p class="vet-specialization">First available</p>
      <div class="vet-checkmark">✓</div>
    `;
    anyVetCard.setAttribute('data-vet-id', '0');
    anyVetCard.addEventListener('click', () => selectVet(anyVetCard, 0, 'Any Available Vet'));
    vetsList.appendChild(anyVetCard);
    
    // Add individual vets
    vets.forEach(vet => {
      const vetCard = document.createElement('div');
      vetCard.className = 'vet-card';
      vetCard.innerHTML = `
        <img src="${vet.avatar}" alt="${vet.name}" class="vet-avatar">
        <p class="vet-name">${vet.name}</p>
        <p class="vet-specialization">${vet.specialization}</p>
        <div class="vet-checkmark">✓</div>
      `;
      vetCard.setAttribute('data-vet-id', vet.id);
      vetCard.addEventListener('click', () => selectVet(vetCard, vet.id, vet.name));
      vetsList.appendChild(vetCard);
    });
  }

  // Select a vet
  function selectVet(card, vetId, vetName) {
    // Remove previous selection
    document.querySelectorAll('.vet-card').forEach(c => c.classList.remove('selected'));
    
    // Select current
    card.classList.add('selected');
    selectedVet = { id: vetId, name: vetName };
    selectedVetId.value = vetId;
    currentVetId = String(vetId);
    
    // Show date section and render calendar
    if (dateSection) {
      dateSection.style.display = 'block';
    }
    
    // Render calendar with loaded disabled dates
    if (typeof window.renderCalendarNow === 'function') {
      window.renderCalendarNow();
    }
    
    // If date is already selected, reload time slots for new vet
    if (appointmentDate && appointmentDate.value && typeof window.loadTimeSlotsForVet === 'function') {
      window.loadTimeSlotsForVet(appointmentDate.value);
    }
    
    validateBookingForm();
  }

  // Simple age calculator
  function calculateAge(dob) {
    const birth = new Date(dob);
    const today = new Date();
    let age = today.getFullYear() - birth.getFullYear();
    const m = today.getMonth() - birth.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) {
      age--;
    }
    return age;
  }

  // Enable confirm when all required fields are filled
  function validateBookingForm() {
    const hasType = appointmentType.value !== "";
    const hasClinic = clinicSelect.value !== "";
    const hasVet = selectedVet !== null;
    const hasDate = appointmentDate.value !== "";
    const hasTime = appointmentTime.value !== "";
    
    confirmBtn.disabled = !(hasType && hasClinic && hasVet && hasDate && hasTime);
    
    // Also call global validation if it exists
    if (typeof window.validateBookingForm === 'function') {
      window.validateBookingForm();
    }
  }

  // Listen to form inputs
  appointmentType.addEventListener("change", validateBookingForm);

  // Confirm booking - Show review screen first
  confirmBtn.addEventListener("click", e => {
    e.preventDefault();
    
    if (confirmBtn.disabled) return;
    
    const clinicName = window.selectedClinicData ? window.selectedClinicData.clinic_name : 'Selected Clinic';
    const appointmentTypeName = appointmentType.options[appointmentType.selectedIndex].text;
    
    // Format time for display
    const timeValue = appointmentTime.value;
    const [hours, minutes] = timeValue.split(':');
    const hour = parseInt(hours);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const displayHour = hour > 12 ? hour - 12 : (hour === 0 ? 12 : hour);
    const formattedTime = `${displayHour}:${minutes} ${ampm}`;
    
    // Hide form, show review screen
    formContent.style.display = "none";
    appointmentReview.style.display = "block";
    confirmBtn.style.display = "none";
    appointmentBackBtn.style.display = "inline-flex";
    appointmentFinalConfirmBtn.style.display = "inline-flex";
    appointmentCancelBtn.textContent = "Cancel";

    appointmentReviewSummary.innerHTML = `
      <div style="display:grid; gap:12px;">
        <div style="padding-bottom:12px; border-bottom:1px solid #e2e8f0;">
          <p style="font-size:12px; color:#64748b; margin:0; font-weight:600;">PET</p>
          <p style="font-size:15px; font-weight:600; color:#0f172a; margin:4px 0 0;">
            ${currentPet.name}${currentPet.breed && currentPet.breed !== 'Unknown' ? ' (' + currentPet.breed + ')' : ''}
          </p>
        </div>
        
        <div style="padding-bottom:12px; border-bottom:1px solid #e2e8f0;">
          <p style="font-size:12px; color:#64748b; margin:0; font-weight:600;">APPOINTMENT TYPE</p>
          <p style="font-size:15px; font-weight:600; color:#0f172a; margin:4px 0 0;">
            ${appointmentTypeName}
          </p>
        </div>
        
        <div style="padding-bottom:12px; border-bottom:1px solid #e2e8f0;">
          <p style="font-size:12px; color:#64748b; margin:0; font-weight:600;">CLINIC</p>
          <p style="font-size:15px; font-weight:600; color:#0f172a; margin:4px 0 0;">
            ${clinicName}
          </p>
        </div>
        
        <div style="padding-bottom:12px; border-bottom:1px solid #e2e8f0;">
          <p style="font-size:12px; color:#64748b; margin:0; font-weight:600;">VETERINARIAN</p>
          <p style="font-size:15px; font-weight:600; color:#0f172a; margin:4px 0 0;">
            ${selectedVet.name}
          </p>
        </div>
        
        <div>
          <p style="font-size:12px; color:#64748b; margin:0; font-weight:600;">DATE & TIME</p>
          <p style="font-size:15px; font-weight:600; color:#0f172a; margin:4px 0 0;">
            ${appointmentDate.value} at ${formattedTime}
          </p>
          <p style="font-size:12px; color:#64748b; margin:4px 0 0;">
            Duration: 20 minutes
          </p>
        </div>
      </div>
    `;
  });

  // Back button - Return to form
  appointmentBackBtn.addEventListener("click", e => {
    e.preventDefault();
    appointmentReview.style.display = "none";
    formContent.style.display = "block";
    confirmBtn.style.display = "inline-flex";
    appointmentBackBtn.style.display = "none";
    appointmentFinalConfirmBtn.style.display = "none";
    appointmentCancelBtn.textContent = "Cancel";
  });

  // Final confirmation - Book the appointment
  if (appointmentFinalConfirmBtn) {
    appointmentFinalConfirmBtn.addEventListener("click", async e => {
      e.preventDefault();
      
      console.log('=== BOOKING APPOINTMENT ===');
      console.log('Current Pet:', currentPet);
      
      // Prevent double submission
      if (appointmentFinalConfirmBtn.disabled) {
        return;
      }
      
      // Disable button
      appointmentFinalConfirmBtn.disabled = true;
      appointmentFinalConfirmBtn.textContent = '⏳ Booking...';
      
      const clinicName = window.selectedClinicData ? window.selectedClinicData.clinic_name : 'Selected Clinic';
      const appointmentTypeName = appointmentType.options[appointmentType.selectedIndex].text;
    
    // Format time for display
    const timeValue = appointmentTime.value;
    const [hours, minutes] = timeValue.split(':');
    const hour = parseInt(hours);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const displayHour = hour > 12 ? hour - 12 : (hour === 0 ? 12 : hour);
    const formattedTime = `${displayHour}:${minutes} ${ampm}`;
    
      // Prepare booking data
      const bookingData = {
        pet_id: currentPet.id,
        clinic_id: clinicSelect.value,
        vet_id: selectedVetId.value,
        appointment_type: appointmentType.value,
        symptoms: '', // Symptoms field removed from form
        appointment_date: appointmentDate.value,
        appointment_time: appointmentTime.value
      };
      
      console.log('Booking Data:', bookingData);
      
      try {
        // Submit booking to API
        console.log('Calling API...');
        const response = await fetch('/PETVET/api/appointments/book.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(bookingData)
        });
        
        console.log('API Response Status:', response.status);
        const result = await response.json();
        console.log('API Result:', result);
      
      if (result.success) {
        // Hide review, show success screen
        appointmentReview.style.display = "none";
        confirmationView.style.display = "block";
        appointmentBackBtn.style.display = "none";
        appointmentFinalConfirmBtn.style.display = "none";
        appointmentCancelBtn.textContent = "Close";

        summary.innerHTML = `
      <div style="display:grid; gap:12px;">
        <div>
          <p style="font-size:12px; color:#64748b; margin:0;">Pet</p>
          <p style="font-size:15px; font-weight:600; color:#0f172a; margin:4px 0 0;">
            ${currentPet.name}${currentPet.breed && currentPet.breed !== 'Unknown' ? ' (' + currentPet.breed + ')' : ''}
          </p>
        </div>
        
        <div>
          <p style="font-size:12px; color:#64748b; margin:0;">Appointment Type</p>
          <p style="font-size:15px; font-weight:600; color:#0f172a; margin:4px 0 0;">
            ${appointmentTypeName}
          </p>
        </div>
        
        <div>
          <p style="font-size:12px; color:#64748b; margin:0;">Clinic</p>
          <p style="font-size:15px; font-weight:600; color:#0f172a; margin:4px 0 0;">
            ${clinicName}
          </p>
        </div>
        
        <div>
          <p style="font-size:12px; color:#64748b; margin:0;">Veterinarian</p>
          <p style="font-size:15px; font-weight:600; color:#0f172a; margin:4px 0 0;">
            ${selectedVet.name}
          </p>
        </div>
        
        <div>
          <p style="font-size:12px; color:#64748b; margin:0;">Date & Time</p>
          <p style="font-size:15px; font-weight:600; color:#0f172a; margin:4px 0 0;">
            ${appointmentDate.value} at ${formattedTime}
          </p>
          <p style="font-size:12px; color:#64748b; margin:4px 0 0;">
            Duration: 20 minutes
          </p>
        </div>
      </div>
    `;
      } else {
        // Show error message
        console.error('Booking failed:', result);
        showToast('Error: ' + (result.error || 'Failed to book appointment'));
        // Re-enable button
        appointmentFinalConfirmBtn.disabled = false;
        appointmentFinalConfirmBtn.textContent = 'Yes, Book Appointment';
      }
      } catch (error) {
        console.error('Booking error:', error);
        showToast('Error booking appointment. Please try again.');
        // Re-enable button
        appointmentFinalConfirmBtn.disabled = false;
        appointmentFinalConfirmBtn.textContent = 'Yes, Book Appointment';
      }
    });
  }

  // Cancel button for book appointment popup
  if (appointmentCancelBtn) {
    appointmentCancelBtn.addEventListener("click", () => {
      // Check if showing confirmation (Close button)
      if (appointmentCancelBtn.textContent === "Close") {
        // Booking was successful, reload page to update UI
        window.location.reload();
      } else {
        // Just close the dialog
        resetAppointmentForm();
        bookDialog.close();
      }
    });
  }

  // ----- Delete Pet -----
  const deletePetDialog = document.getElementById("deletePetDialog");
  const deletePetNameEl = document.getElementById("deletePetName");
  const confirmDeletePetBtn = document.getElementById("confirmDeletePetBtn");
  let currentDeletePetId = null;

  // Open delete confirmation dialog
  document.addEventListener("click", (e) => {
    if (e.target.closest(".pet-delete-btn")) {
      const btn = e.target.closest(".pet-delete-btn");
      currentDeletePetId = parseInt(btn.dataset.petId);
      const petName = btn.dataset.petName;
      
      if (deletePetNameEl) {
        deletePetNameEl.textContent = petName;
      }
      
      openDialog("deletePetDialog");
    }
  });

  // Cancel delete
  if (deletePetDialog) {
    deletePetDialog.querySelectorAll("button[value='cancel']").forEach(btn => {
      btn.addEventListener("click", () => {
        currentDeletePetId = null;
        closeDialog("deletePetDialog");
      });
    });
    
    // Close on backdrop click
    deletePetDialog.addEventListener("click", (e) => {
      if (e.target === deletePetDialog) {
        currentDeletePetId = null;
        closeDialog("deletePetDialog");
      }
    });
  }

  // Confirm delete
  if (confirmDeletePetBtn) {
    confirmDeletePetBtn.addEventListener("click", async () => {
      if (!currentDeletePetId) return;

      try {
        const formData = new FormData();
        formData.append('id', currentDeletePetId);

        const response = await fetch('/PETVET/api/pet-owner/pets/delete.php', {
          method: 'POST',
          body: formData
        });

        const result = await response.json();

        if (result.success) {
          // Remove pet card from UI
          const petCard = document.querySelector(`.pet-card[data-pet-id="${currentDeletePetId}"]`);
          if (petCard) {
            petCard.style.transition = 'all 0.3s ease';
            petCard.style.opacity = '0';
            petCard.style.transform = 'scale(0.9)';
            setTimeout(() => {
              petCard.remove();
              
              // Check if no pets left
              const petsGrid = document.getElementById('petsGrid');
              if (petsGrid && petsGrid.children.length === 0) {
                petsGrid.innerHTML = `
                  <div style="grid-column: 1/-1; text-align: center; padding: 60px 20px;">
                    <p style="font-size: 48px; margin: 0 0 16px;">🐾</p>
                    <h3 style="color: #6b7280; font-weight: 500; margin: 0 0 8px;">No pets yet</h3>
                    <p style="color: #9ca3af; margin: 0;">Click "+ Add Pet" to add your first pet</p>
                  </div>
                `;
              }
            }, 300);
          }

          closeDialog("deletePetDialog");
          currentDeletePetId = null;

          // Show success message
          showToast('Pet profile deleted successfully');
        } else {
          showToast('Error: ' + result.message);
        }
      } catch (error) {
        console.error('Delete error:', error);
        showToast('Error deleting pet profile');
      }
    });
  }

  // Toast notification helper
  function showToast(message) {
    const toast = document.createElement('div');
    toast.style.cssText = `
      position: fixed;
      top: 24px;
      left: 50%;
      transform: translateX(-50%);
      background: #1f2937;
      color: white;
      padding: 12px 24px;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      z-index: 999999;
      font-size: 14px;
      font-weight: 500;
      animation: slideDown 0.3s ease;
    `;
    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(() => {
      toast.style.animation = 'slideUp 0.3s ease';
      setTimeout(() => toast.remove(), 300);
    }, 2000);
  }

  // Add keyframes for toast animation
  if (!document.getElementById('toast-animations')) {
    const style = document.createElement('style');
    style.id = 'toast-animations';
    style.textContent = `
      @keyframes slideDown {
        from {
          opacity: 0;
          transform: translateX(-50%) translateY(-20px);
        }
        to {
          opacity: 1;
          transform: translateX(-50%) translateY(0);
        }
      }
      @keyframes slideUp {
        from {
          opacity: 1;
          transform: translateX(-50%) translateY(0);
        }
        to {
          opacity: 0;
          transform: translateX(-50%) translateY(-20px);
        }
      }
    `;
    document.head.appendChild(style);
  }

  // Initialize Modern Clinic Selector
  if (typeof ClinicSelector !== 'undefined' && document.getElementById('clinicSelectorContainer')) {
    try {
      clinicSelectorInstance = new ClinicSelector({
        containerId: 'clinicSelectorContainer',
        onSelect: (clinic) => {
          // Update hidden input for form submission
          const hiddenInput = document.getElementById('clinicSelect');
          if (hiddenInput) {
            hiddenInput.value = clinic.id;
            
            // Trigger change event to update vets and calendar
            const event = new Event('change', { bubbles: true });
            hiddenInput.dispatchEvent(event);
          }
          
          // Store selected clinic data for display
          window.selectedClinicData = clinic;
        }
      });
      
      // Initialize without location first
      clinicSelectorInstance.init(null);
      
      // Update with location when available
      let checkCount = 0;
      const maxChecks = 40; // 20 seconds total
      const checkLocation = setInterval(() => {
        checkCount++;
        
        if (window.clinicDistanceCalculator?.petOwnerLocation) {
          clinicSelectorInstance.updateLocation(window.clinicDistanceCalculator.petOwnerLocation);
          clearInterval(checkLocation);
        } else if (checkCount >= maxChecks) {
          clearInterval(checkLocation);
        }
      }, 500);
    } catch (error) {
      console.error('Error initializing clinic selector:', error);
    }
  }
});
