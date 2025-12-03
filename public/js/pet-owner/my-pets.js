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
  const datetimeField = missingDialog?.querySelector("input[name='datetime']");

  // Open missing popup
  document.querySelectorAll(".markMissingBtn").forEach(btn => {
    btn.addEventListener("click", e => {
      e.preventDefault();
      openDialog("markMissingDialog");
    });
  });

  // Cancel button for missing popup
  if (missingDialog) {
    missingDialog.querySelectorAll("button[value='cancel']").forEach(btn => {
      btn.addEventListener("click", () => closeDialog("markMissingDialog"));
    });
  }

  // Show/Hide reward input
  if (rewardCheckbox && rewardAmountWrap) {
    rewardCheckbox.addEventListener("change", () => {
      rewardAmountWrap.style.display = rewardCheckbox.checked ? "block" : "none";
    });
  }

  // Set current datetime
  if (datetimeField) {
    datetimeField.value = new Date().toISOString().slice(0, 16);
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
  const appointmentSymptoms = document.getElementById("appointmentSymptoms");
  const clinicSelect = document.getElementById("clinicSelect");
  const clinicInfo = document.getElementById("clinicInfo");
  const vetSection = document.getElementById("vetSection");
  const vetsList = document.getElementById("vetsList");
  const dateTimeSection = document.getElementById("dateTimeSection");
  const appointmentDate = document.getElementById("appointmentDate");
  const appointmentTime = document.getElementById("appointmentTime");
  const timeValidation = document.getElementById("timeValidation");
  const timeValidationMessage = document.getElementById("timeValidationMessage");
  const noticeSection = document.getElementById("noticeSection");
  const selectedVetId = document.getElementById("selectedVetId");
  
  let currentPet = null;
  let selectedVet = null;
  let timeCheckTimeout = null;

  // Replace default link behavior with popup
  bookBtns.forEach(btn => {
    btn.addEventListener("click", e => {
      e.preventDefault();
      
      // Button is already disabled on server-side if pet has upcoming appointment
      if (btn.disabled || btn.tagName === 'BUTTON') {
        return;
      }
      
      const petId = btn.getAttribute("href").split("=")[1];
      currentPet = pets.find(p => p.id == petId);

      if (!currentPet) return;

      header.textContent = `Book Appointment for ${currentPet.name}`;
      petInfo.textContent = `${currentPet.name} • ${currentPet.breed} • ${calculateAge(currentPet.date_of_birth)}y`;
      healthNotes.textContent = "";

      // Reset form
      resetAppointmentForm();
      bookDialog.showModal();
    });
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
    clinicInfo.style.display = "none";
    vetSection.style.display = "none";
    dateTimeSection.style.display = "none";
    noticeSection.style.display = "none";
    timeValidation.style.display = "none";
    timeValidation.className = "";
    appointmentTime.className = "input";
    
    // Clear vet selection
    vetsList.innerHTML = "";
  }

  // Set min and max dates (today to today + 28 days)
  function setDateLimits() {
    const today = new Date();
    const maxDate = new Date();
    maxDate.setDate(today.getDate() + 28);
    
    const formatDate = (date) => {
      return date.toISOString().split('T')[0];
    };
    
    appointmentDate.min = formatDate(today);
    appointmentDate.max = formatDate(maxDate);
  }
  
  setDateLimits();

  // Clinic selection handler
  clinicSelect.addEventListener("change", function() {
    const selectedOption = this.options[this.selectedIndex];
    
    if (this.value) {
      // Show clinic info
      const clinicName = selectedOption.textContent;
      const clinicAddress = selectedOption.getAttribute("data-address");
      const clinicPhone = selectedOption.getAttribute("data-phone");
      
      document.getElementById("clinicInfoName").textContent = clinicName;
      document.getElementById("clinicInfoAddress").textContent = clinicAddress;
      document.getElementById("clinicInfoPhone").textContent = clinicPhone;
      clinicInfo.style.display = "block";
      
      // Fetch vets for this clinic
      fetchVets(this.value);
      
      // Show vet section
      vetSection.style.display = "block";
    } else {
      clinicInfo.style.display = "none";
      vetSection.style.display = "none";
      dateTimeSection.style.display = "none";
      noticeSection.style.display = "none";
    }
    
    validateForm();
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
    
    // Show date/time section
    dateTimeSection.style.display = "block";
    noticeSection.style.display = "block";
    
    // Revalidate time if already entered
    if (appointmentDate.value && appointmentTime.value) {
      checkTimeAvailability();
    }
    
    validateForm();
  }

  // Date change handler
  appointmentDate.addEventListener("change", function() {
    if (appointmentTime.value) {
      checkTimeAvailability();
    }
    validateForm();
  });

  // Time input handler with debouncing
  appointmentTime.addEventListener("input", function() {
    // Clear previous timeout
    if (timeCheckTimeout) {
      clearTimeout(timeCheckTimeout);
    }
    
    // Reset validation display
    timeValidation.style.display = "none";
    timeValidation.className = "";
    appointmentTime.classList.remove('valid', 'invalid');
    
    if (this.value && appointmentDate.value) {
      // Show loading state
      timeValidation.style.display = "block";
      timeValidation.className = "";
      timeValidationMessage.textContent = "⏳ Checking availability...";
      timeValidation.style.background = "#f1f5f9";
      timeValidation.style.borderLeft = "3px solid #94a3b8";
      timeValidation.style.color = "#475569";
      
      // Debounce the API call
      timeCheckTimeout = setTimeout(() => {
        checkTimeAvailability();
      }, 800);
    }
    
    validateForm();
  });

  // Check time availability via API
  function checkTimeAvailability() {
    const date = appointmentDate.value;
    const time = appointmentTime.value;
    const vetId = selectedVetId.value || 0;
    const clinicId = clinicSelect.value || 0;
    
    if (!date || !time) return;
    
    fetch(`/PETVET/api/check-availability.php?date=${date}&time=${time}&vet_id=${vetId}&clinic_id=${clinicId}`)
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          if (data.available) {
            // Time is available
            appointmentTime.classList.remove('invalid');
            appointmentTime.classList.add('valid');
            timeValidation.className = "success";
            timeValidation.style.display = "block";
            timeValidationMessage.textContent = "✓ This time slot is available";
          } else {
            // Time is not available
            appointmentTime.classList.remove('valid');
            appointmentTime.classList.add('invalid');
            timeValidation.className = "error";
            timeValidation.style.display = "block";
            timeValidationMessage.textContent = "✗ This time slot is already booked. Please choose another time.";
          }
        }
        validateForm();
      })
      .catch(error => {
        console.error('Error checking availability:', error);
        timeValidation.style.display = "block";
        timeValidation.style.background = "#fef2f2";
        timeValidation.style.borderLeft = "3px solid #ef4444";
        timeValidation.style.color = "#991b1b";
        timeValidationMessage.textContent = "⚠️ Unable to check availability. Please try again.";
      });
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

  // Enable confirm when all required fields are filled and time is available
  function validateForm() {
    const hasType = appointmentType.value !== "";
    const hasSymptoms = appointmentSymptoms.value.trim() !== "";
    const hasClinic = clinicSelect.value !== "";
    const hasVet = selectedVet !== null;
    const hasDate = appointmentDate.value !== "";
    const hasTime = appointmentTime.value !== "";
    const timeIsValid = appointmentTime.classList.contains('valid');
    
    confirmBtn.disabled = !(hasType && hasSymptoms && hasClinic && hasVet && hasDate && hasTime && timeIsValid);
  }

  // Listen to all form inputs
  appointmentType.addEventListener("change", validateForm);
  appointmentSymptoms.addEventListener("input", validateForm);

  // Confirm booking - Show review screen first
  confirmBtn.addEventListener("click", e => {
    e.preventDefault();
    
    if (confirmBtn.disabled) return;
    
    const clinicName = clinicSelect.options[clinicSelect.selectedIndex].text;
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
          <p style="font-size:12px; color:#64748b; margin:0; font-weight:600;">SYMPTOMS / REASON</p>
          <p style="font-size:14px; color:#0f172a; margin:4px 0 0;">
            ${appointmentSymptoms.value}
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
      
      // Disable button to prevent double submission
      appointmentFinalConfirmBtn.disabled = true;
      appointmentFinalConfirmBtn.textContent = 'Booking...';
      
      const clinicName = clinicSelect.options[clinicSelect.selectedIndex].text;
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
        symptoms: appointmentSymptoms.value,
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
          <p style="font-size:12px; color:#64748b; margin:0;">Symptoms / Reason</p>
          <p style="font-size:14px; color:#0f172a; margin:4px 0 0;">
            ${appointmentSymptoms.value}
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
      bottom: 24px;
      left: 50%;
      transform: translateX(-50%);
      background: #1f2937;
      color: white;
      padding: 12px 24px;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      z-index: 10000;
      font-size: 14px;
      font-weight: 500;
      animation: slideUp 0.3s ease;
    `;
    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(() => {
      toast.style.animation = 'slideDown 0.3s ease';
      setTimeout(() => toast.remove(), 300);
    }, 2000);
  }

  // Add keyframes for toast animation
  if (!document.getElementById('toast-animations')) {
    const style = document.createElement('style');
    style.id = 'toast-animations';
    style.textContent = `
      @keyframes slideUp {
        from {
          opacity: 0;
          transform: translateX(-50%) translateY(20px);
        }
        to {
          opacity: 1;
          transform: translateX(-50%) translateY(0);
        }
      }
      @keyframes slideDown {
        from {
          opacity: 1;
          transform: translateX(-50%) translateY(0);
        }
        to {
          opacity: 0;
          transform: translateX(-50%) translateY(20px);
        }
      }
    `;
    document.head.appendChild(style);
  }
});
