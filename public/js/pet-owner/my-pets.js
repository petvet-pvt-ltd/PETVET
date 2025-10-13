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

  if (addPetBtn && addPetDialog) {
    addPetBtn.addEventListener("click", () => openDialog("addPetDialog"));
    addPetDialog.querySelectorAll("button[value='cancel']").forEach(btn => {
      btn.addEventListener("click", () => closeDialog("addPetDialog"));
    });
  }

  // ----- Pet Profile -----
  const petProfileDialog = document.getElementById("petProfileDialog");
  const petProfileForm = document.getElementById("petProfileForm");
  const petProfileImg = document.getElementById("petProfileImg");
  const petProfileImgInput = document.getElementById("petProfileImgInput");

  document.querySelectorAll(".pet-actions .btn[href*='pet-profile']").forEach(btn => {
    btn.addEventListener("click", e => {
      e.preventDefault();
      const url = new URL(btn.href, window.location.origin);
      const petId = url.searchParams.get("id");
      const pet = window.petsData.find(p => p.id == petId);

      if (pet && petProfileForm) {
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
});

document.addEventListener("DOMContentLoaded", () => {
  const pets = window.petsData || [];
  const bookBtns = document.querySelectorAll(".pet-actions .btn[href*='appointments/book']");
  const bookDialog = document.getElementById("bookAppointmentDialog");
  const bookForm = document.getElementById("bookAppointmentForm");
  const header = document.getElementById("appointmentHeader");
  const petInfo = document.getElementById("appointmentPetInfo");
  const healthNotes = document.getElementById("appointmentHealthNotes");
  const timeSlotsWrap = document.getElementById("timeSlotsWrap");
  const confirmBtn = document.getElementById("appointmentConfirmBtn");
  const confirmationView = document.getElementById("appointmentConfirmation");
  const formContent = document.getElementById("appointmentFormContent");
  const summary = document.getElementById("appointmentSummary");
  const appointmentCancelBtn = document.getElementById("appointmentCancelBtn");
  let selectedTime = null;
  let currentPet = null;

  // Replace default link behavior with popup
  bookBtns.forEach(btn => {
    btn.addEventListener("click", e => {
      e.preventDefault();
      const petId = btn.getAttribute("href").split("=")[1];
      currentPet = pets.find(p => p.id == petId);

      if (!currentPet) return;

      header.textContent = `Book Appointment for ${currentPet.name}`;
      petInfo.textContent = `${currentPet.name} • ${currentPet.breed} • ${calculateAge(currentPet.date_of_birth)}y`;
      healthNotes.textContent = currentPet.allergies && currentPet.allergies !== "None"
        ? `Allergy: ${currentPet.allergies}`
        : "";

      // Reset form
      bookForm.reset();
      formContent.style.display = "block";
      confirmationView.style.display = "none";
      confirmBtn.disabled = true;
      selectedTime = null;
      generateTimeSlots();

      bookDialog.showModal();
    });
  });

  // Generate time slot buttons
  function generateTimeSlots() {
    const slots = ["9:00 AM","9:30 AM","10:00 AM","10:30 AM","11:00 AM","2:00 PM","2:30 PM","3:00 PM"];
    timeSlotsWrap.innerHTML = "";
    slots.forEach(slot => {
      const btn = document.createElement("button");
      btn.type = "button";
      btn.textContent = slot;
      btn.className = "btn";
      btn.style.margin = "4px";
      btn.addEventListener("click", () => {
        selectedTime = slot;
        [...timeSlotsWrap.querySelectorAll("button")].forEach(b => b.classList.remove("primary"));
        btn.classList.add("primary");
        validateForm();
      });
      timeSlotsWrap.appendChild(btn);
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

  // Enable confirm when required fields filled
  bookForm.addEventListener("input", validateForm);
  function validateForm() {
    const type = bookForm.querySelector("[name='appointment_type']").value;
    const date = bookForm.querySelector("[name='date']").value;
    const location = bookForm.querySelector("[name='location']").value;
    confirmBtn.disabled = !(type && date && location && selectedTime);
  }

  // Confirm booking
  confirmBtn.addEventListener("click", e => {
    e.preventDefault();
    formContent.style.display = "none";
    confirmationView.style.display = "block";

    summary.innerHTML = `
      <strong>For:</strong> ${currentPet.name} (${currentPet.breed})<br>
      <strong>Date & Time:</strong> ${bookForm.date.value} at ${selectedTime}<br>
      <strong>With:</strong> ${bookForm.vet.value || "Any Available Vet"}<br>
      <strong>Reason:</strong> ${bookForm.reason.value || "N/A"}
    `;
  });

// Cancel button for book appointment popup

if (appointmentCancelBtn) {
  appointmentCancelBtn.addEventListener("click", () => {
    formContent.style.display = "none";
    bookDialog.close();
    confirmationView.style.display = "none";
  });
}
});
