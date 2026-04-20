/**
 * Receptionist Appointment Booking System
 * Step-by-step booking flow with calendar and time picker
 */

// Receptionist booking state
let receptionistCalendarMonth = new Date();
let receptionistSelectedDate = null;
let receptionistSelectedTime = null;
let receptionistSelectedVet = null;
let receptionistDisabledDates = [];
let walkinCustomerPhone = null;

/**
 * Validation Rules:
 * - Phone: Must start with 07, exactly 10 digits (0701234567)
 * - Client Name: Letters and spaces only, no numbers
 * - Pet Name: Letters and spaces only, no numbers
 */

/**
 * Validate phone number: starts with 07, exactly 10 digits
 */
function validatePhone(phone) {
  const cleanPhone = phone.trim().replace(/\s/g, '');
  const phoneRegex = /^07\d{8}$/;
  return phoneRegex.test(cleanPhone);
}

/**
 * Validate names: letters and spaces only, no numbers
 */
function validateName(name) {
  const cleanName = name.trim();
  const nameRegex = /^[a-zA-Z\s]+$/;
  return nameRegex.test(cleanName) && cleanName.length > 0;
}

/**
 * Show validation error message for phone
 */
function showPhoneError(message) {
  const errorDiv = document.getElementById('phoneError');
  if (errorDiv) {
    if (message) {
      errorDiv.textContent = message;
      errorDiv.style.display = 'block';
    } else {
      errorDiv.style.display = 'none';
    }
  }
}

/**
 * Show validation error message for client name
 */
function showClientNameError(message) {
  const errorDiv = document.getElementById('clientNameError');
  if (errorDiv) {
    if (message) {
      errorDiv.textContent = message;
      errorDiv.style.display = 'block';
    } else {
      errorDiv.style.display = 'none';
    }
  }
}

/**
 * Show validation error message for pet name
 */
function showPetNameError(message) {
  const errorDiv = document.getElementById('petNameError');
  if (errorDiv) {
    if (message) {
      errorDiv.textContent = message;
      errorDiv.style.display = 'block';
    } else {
      errorDiv.style.display = 'none';
    }
  }
}

/**
 * Real-time phone validation
 */
function validatePhoneInput(e) {
  const phoneInput = document.getElementById('newCustomerPhone');
  const phone = phoneInput.value.trim();
  
  // Allow empty (not yet filled)
  if (phone === '') {
    showPhoneError('');
    return;
  }
  
  const cleanPhone = phone.replace(/\s/g, '');
  
  // Check if it starts with 07
  if (!cleanPhone.startsWith('07')) {
    showPhoneError('❌ Phone must start with 07');
    return;
  }
  
  // Check if it has exactly 10 digits
  if (cleanPhone.length < 10) {
    showPhoneError(`❌ Phone must have 10 digits (${cleanPhone.length}/10)`);
    return;
  }
  
  if (cleanPhone.length > 10) {
    showPhoneError('❌ Phone must have exactly 10 digits');
    return;
  }
  
  // Check if all characters after 07 are digits
  if (!/^\d{8}$/.test(cleanPhone.substring(2))) {
    showPhoneError('❌ Phone must contain only digits');
    return;
  }
  
  // Valid phone
  showPhoneError('');
}

/**
 * Real-time client name validation
 */
function validateClientNameInput(e) {
  const clientNameInput = document.getElementById('newClientName');
  const clientName = clientNameInput.value.trim();
  
  // Allow empty (not yet filled)
  if (clientName === '') {
    showClientNameError('');
    return;
  }
  
  // Check if contains only letters and spaces
  if (!/^[a-zA-Z\s]+$/.test(clientName)) {
    showClientNameError('❌ Client name must contain letters only (no numbers or special characters)');
    return;
  }
  
  // Check if at least 2 characters
  if (clientName.length < 2) {
    showClientNameError('❌ Client name must be at least 2 characters');
    return;
  }
  
  // Valid
  showClientNameError('');
}

/**
 * Real-time pet name validation
 */
function validatePetNameInput(e) {
  const petNameInput = document.getElementById('newPetName');
  const petName = petNameInput.value.trim();
  
  // Allow empty (not yet filled)
  if (petName === '') {
    showPetNameError('');
    return;
  }
  
  // Check if contains only letters and spaces
  if (!/^[a-zA-Z\s]+$/.test(petName)) {
    showPetNameError('❌ Pet name must contain letters only (no numbers or special characters)');
    return;
  }
  
  // Check if at least 2 characters
  if (petName.length < 2) {
    showPetNameError('❌ Pet name must be at least 2 characters');
    return;
  }
  
  // Valid
  showPetNameError('');
}

/**
 * Handle phone number input validation
 */
function handlePhoneInput() {
  const phoneInput = document.getElementById('newCustomerPhone');
  const phone = phoneInput.value.trim();

  // Store phone without validation
  walkinCustomerPhone = phone;
}

/**
 * Manually check history when button is clicked
 */
function handleCheckHistoryButtonClick() {
  const phoneInput = document.getElementById('newCustomerPhone');
  const phone = phoneInput.value.trim();
  
  if (!phone) {
    showNotification('Please enter a valid phone number', 'Invalid Phone', 'error');
    return;
  }
  
  walkinCustomerPhone = phone;
  checkPreviousVisits(phone);
}

/**
 * Check for previous visits by phone number with loading state
 */
function checkPreviousVisits(phone) {
  const checkHistoryBtn = document.getElementById('checkHistoryBtn');
  const summaryDiv = document.getElementById('previousVisitsSummary');
  const visitsList = document.getElementById('previousVisitsList');
  const module = new URLSearchParams(window.location.search).get('module') || 'clinic-manager';
  
  // Show loading state
  if (checkHistoryBtn) {
    checkHistoryBtn.disabled = true;
    checkHistoryBtn.innerHTML = '<span style="margin-right: 8px;">⏳</span>Checking...';
  }
  
  // Show loading in previous visits section
  if (visitsList) {
    visitsList.innerHTML = '<div style="padding: 20px; text-align: center; color: #64748b;"><span style="font-size: 20px; display: inline-block;">⏳</span> Loading...</div>';
    if (summaryDiv) summaryDiv.style.display = 'block';
  }
  
  fetch(`/PETVET/api/${module}/check-walk-in-history.php?phone=` + encodeURIComponent(phone), {
    credentials: 'same-origin'
  })
  .then(response => {
    // Check for non-200 status
    if (!response.ok) {
      return response.json().then(data => {
        throw new Error(data.error || `Server error: ${response.status}`);
      });
    }
    return response.json();
  })
  .then(data => {
    // Restore button
    if (checkHistoryBtn) {
      checkHistoryBtn.disabled = false;
      checkHistoryBtn.innerHTML = 'Check History';
    }
    
    if (!data.success) {
      const errorMsg = 'Error: ' + (data.error || 'Failed to check history');
      if (typeof showNotification === 'function') {
        showNotification(errorMsg, 'Error', 'error');
      } else {
        console.error(errorMsg);
        alert(errorMsg);
      }
      if (summaryDiv) summaryDiv.style.display = 'none';
      return;
    }
    
    // Show registered user info if applicable
    const registeredUserInfo = document.getElementById('registeredUserInfo');
    const userNameEl = document.getElementById('registeredUserName');
    const petSelectionSection = document.getElementById('receptionistPetSelectionSection');
    
    if (data.is_registered_user) {
      if (userNameEl) userNameEl.textContent = data.user_name;
      if (registeredUserInfo) registeredUserInfo.style.display = 'block';
      if (petSelectionSection) petSelectionSection.style.display = 'block';
      
      // Populate pet dropdown
      populatePetDropdown(data.user_pets, data.user_id);
      
      // Mark as registered user
      window.currentRegisteredUserId = data.user_id;
    } else {
      if (registeredUserInfo) registeredUserInfo.style.display = 'none';
      if (petSelectionSection) petSelectionSection.style.display = 'none';
      window.currentRegisteredUserId = null;
    }
    
    // Show previous visits
    if (data.previous_visits && data.previous_visits.length > 0) {
      visitsList.innerHTML = data.previous_visits.map(visit => `
        <div style="
          padding: 10px;
          border-bottom: 1px solid #e2e8f0;
          font-size: 12px;
          color: #334155;
        ">
          <strong>${visit.date}</strong> - ${visit.pet_name} (${visit.pet_type}) - ${visit.type}
        </div>
      `).join('');
      summaryDiv.style.display = 'block';
    } else {
      visitsList.innerHTML = '<div style="padding: 15px; text-align: center; color: #94a3b8; font-size: 13px;">No previous visits found</div>';
      summaryDiv.style.display = 'block';
    }
    
    // Show vet selection section
    const vetSection = document.getElementById('receptionistVetSection');
    if (vetSection) vetSection.style.display = 'block';
  })
  .catch(err => {
    console.error('Error checking previous visits:', err);
    
    // Restore button
    if (checkHistoryBtn) {
      checkHistoryBtn.disabled = false;
      checkHistoryBtn.innerHTML = 'Check History';
    }
    
    // Show error without showNotification (fallback if function not available)
    if (typeof showNotification === 'function') {
      showNotification('Failed to check history: ' + err.message, 'Error', 'error');
    } else {
      console.error('Error:', err.message);
      alert('Failed to check history: ' + err.message);
    }
    if (summaryDiv) summaryDiv.style.display = 'none';
  });
}

/**
 * Populate pet dropdown for registered users
 */
function populatePetDropdown(userPets, userId) {
  const petSelect = document.getElementById('registeredUserPetSelect');
  if (!petSelect) return;
  
  // Clear existing pets (keep Select pet and Add New Pet options)
  petSelect.innerHTML = '<option value="">Select pet</option>';
  
  // Add user's pets
  userPets.forEach(pet => {
    const option = document.createElement('option');
    option.value = pet.id;
    option.textContent = pet.name;
    petSelect.appendChild(option);
  });
  
  // Add "Add New Pet" option
  const newPetOption = document.createElement('option');
  newPetOption.value = 'other';
  newPetOption.textContent = '+ Add New Pet';
  petSelect.appendChild(newPetOption);
  
  // Add listener
  petSelect.addEventListener('change', function() {
    const newPetFields = document.getElementById('newPetFieldsForRegistered');
    if (this.value === 'other') {
      if (newPetFields) newPetFields.style.display = 'block';
    } else {
      if (newPetFields) newPetFields.style.display = 'none';
    }
  });
}

/**
 * Initialize receptionist booking calendar
 */
function initReceptionistBooking() {
  console.log('Initializing receptionist booking system');
  
  // Load disabled dates for the clinic
  loadReceptionistDisabledDates();
  
  // Add phone input listener
  const phoneInput = document.getElementById('newCustomerPhone');
  if (phoneInput) {
    phoneInput.addEventListener('input', handlePhoneInput);
  }
}

/**
 * Render receptionist calendar
 */
function renderReceptionistCalendar() {
  const calendarWidget = document.getElementById('receptionistCalendarWidget');
  if (!calendarWidget) return;
  
  const year = receptionistCalendarMonth.getFullYear();
  const month = receptionistCalendarMonth.getMonth();
  
  const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'];
  
  const firstDay = new Date(year, month, 1);
  const lastDay = new Date(year, month + 1, 0);
  const daysInMonth = lastDay.getDate();
  const startDayOfWeek = firstDay.getDay();
  
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  const maxDate = new Date();
  maxDate.setDate(maxDate.getDate() + 30);
  maxDate.setHours(0, 0, 0, 0);
  
  let html = `
    <div class="calendar-header">
      <div class="calendar-month">${monthNames[month]} ${year}</div>
      <div class="calendar-nav">
        <button type="button" id="receptionistPrevMonth" ${month === today.getMonth() && year === today.getFullYear() ? 'disabled' : ''}>‹</button>
        <button type="button" id="receptionistNextMonth">›</button>
      </div>
    </div>
    <div class="calendar-weekdays">
      <div class="calendar-weekday">Sun</div>
      <div class="calendar-weekday">Mon</div>
      <div class="calendar-weekday">Tue</div>
      <div class="calendar-weekday">Wed</div>
      <div class="calendar-weekday">Thu</div>
      <div class="calendar-weekday">Fri</div>
      <div class="calendar-weekday">Sat</div>
    </div>
    <div class="calendar-days">
  `;
  
  for (let i = 0; i < startDayOfWeek; i++) {
    html += '<div class="calendar-day empty"></div>';
  }
  
  for (let day = 1; day <= daysInMonth; day++) {
    const date = new Date(year, month, day);
    date.setHours(0, 0, 0, 0);
    const dateString = formatDateForAPI(date);
    
    let classes = ['calendar-day'];
    let clickable = true;
    
    if (date.getTime() === today.getTime()) {
      classes.push('today');
    }
    
    if (receptionistSelectedDate && formatDateForAPI(receptionistSelectedDate) === dateString) {
      classes.push('selected');
    }
    
    if (date < today || date > maxDate || receptionistDisabledDates.includes(dateString)) {
      classes.push('disabled');
      clickable = false;
    }
    
    html += `<div class="${classes.join(' ')}" ${clickable ? `data-date="${dateString}"` : ''}>${day}</div>`;
  }
  
  html += '</div></div>';
  calendarWidget.innerHTML = html;
  
  attachReceptionistCalendarListeners();
}

/**
 * Attach calendar event listeners
 */
function attachReceptionistCalendarListeners() {
  const prevBtn = document.getElementById('receptionistPrevMonth');
  if (prevBtn) {
    prevBtn.addEventListener('click', () => {
      receptionistCalendarMonth.setMonth(receptionistCalendarMonth.getMonth() - 1);
      renderReceptionistCalendar();
    });
  }
  
  const nextBtn = document.getElementById('receptionistNextMonth');
  if (nextBtn) {
    nextBtn.addEventListener('click', () => {
      receptionistCalendarMonth.setMonth(receptionistCalendarMonth.getMonth() + 1);
      renderReceptionistCalendar();
    });
  }
  
  const dayElements = document.querySelectorAll('#receptionistCalendarWidget .calendar-day[data-date]');
  dayElements.forEach(dayEl => {
    dayEl.addEventListener('click', () => {
      const dateString = dayEl.getAttribute('data-date');
      selectReceptionistDate(dateString);
    });
  });
}

/**
 * Select date and show time slots
 */
function selectReceptionistDate(dateString) {
  const previousDate = receptionistSelectedDate ? formatDateForAPI(receptionistSelectedDate) : null;
  receptionistSelectedDate = new Date(dateString + 'T00:00:00');
  
  const newDate = document.getElementById('newDate');
  if (newDate) {
    newDate.value = dateString;
  }
  
  renderReceptionistCalendar();
  
  // If date changed, reset time and details
  if (previousDate && previousDate !== dateString) {
    resetFromDateChange();
  }
  
  const timeSection = document.getElementById('receptionistTimeSection');
  if (timeSection) {
    timeSection.style.display = 'block';
  }
  
  loadReceptionistTimeSlots(dateString);
  validateReceptionistBooking();
}

/**
 * Load available time slots
 */
async function loadReceptionistTimeSlots(dateString) {
  const timeSlotsGrid = document.getElementById('receptionistTimeSlotsGrid');
  if (!timeSlotsGrid) return;
  
  timeSlotsGrid.innerHTML = '<div class="time-slots-loading">Loading available times...</div>';
  
  try {
    // Use selected vet ID, or 'any' if not selected yet
    const vetId = receptionistSelectedVet || 'any';
    const clinicId = window.CLINIC_ID || 1;
    const response = await fetch(`/PETVET/api/appointments/get-available-times.php?clinic_id=${clinicId}&vet_id=${vetId}&date=${dateString}`);
    const data = await response.json();
    
    if (data.success) {
      if (data.available_slots && data.available_slots.length > 0) {
        renderReceptionistTimeSlots(data.available_slots);
      } else {
        timeSlotsGrid.innerHTML = '<div class="time-slots-empty">😔 No available time slots for this date. Please select another date.</div>';
      }
    } else {
      timeSlotsGrid.innerHTML = `<div class="time-slots-empty">Error: ${data.error || 'Failed to load time slots'}</div>`;
    }
  } catch (error) {
    console.error('Error loading time slots:', error);
    timeSlotsGrid.innerHTML = '<div class="time-slots-empty">Error loading time slots</div>';
  }
}

/**
 * Render time slots
 */
function renderReceptionistTimeSlots(slots) {
  const timeSlotsGrid = document.getElementById('receptionistTimeSlotsGrid');
  if (!timeSlotsGrid) return;
  
  timeSlotsGrid.innerHTML = '';
  
  const now = new Date();
  const isToday = receptionistSelectedDate && 
                  receptionistSelectedDate.getFullYear() === now.getFullYear() &&
                  receptionistSelectedDate.getMonth() === now.getMonth() &&
                  receptionistSelectedDate.getDate() === now.getDate();
  
  const currentTimeMinutes = isToday ? (now.getHours() * 60 + now.getMinutes()) : -1;
  
  slots.forEach(timeString => {
    const [hours, minutes] = timeString.split(':').map(Number);
    const slotTimeMinutes = hours * 60 + minutes;
    
    if (isToday && slotTimeMinutes <= currentTimeMinutes) {
      return;
    }
    
    const slotDiv = document.createElement('div');
    slotDiv.className = 'time-slot';
    slotDiv.textContent = formatTime12Hour(timeString);
    slotDiv.setAttribute('data-time', timeString);
    
    if (receptionistSelectedTime === timeString) {
      slotDiv.classList.add('selected');
    }
    
    slotDiv.addEventListener('click', () => selectReceptionistTimeSlot(slotDiv, timeString));
    
    timeSlotsGrid.appendChild(slotDiv);
  });
  
  if (timeSlotsGrid.children.length === 0) {
    timeSlotsGrid.innerHTML = '<div class="time-slots-empty">😔 No available time slots for this date. Please select another date.</div>';
  }
}

/**
 * Select time slot
 */
function selectReceptionistTimeSlot(slotElement, timeString) {
  document.querySelectorAll('#receptionistTimeSlotsGrid .time-slot').forEach(slot => {
    slot.classList.remove('selected');
  });
  
  slotElement.classList.add('selected');
  const previousTime = receptionistSelectedTime;
  receptionistSelectedTime = timeString;
  
  const newTime = document.getElementById('newTime');
  if (newTime) {
    newTime.value = timeString;
  }
  
  // If time changed, reset details section
  if (previousTime && previousTime !== timeString) {
    resetFromTimeChange();
  }
  
  const detailsSection = document.getElementById('receptionistDetailsSection');
  if (detailsSection) {
    detailsSection.style.display = 'block';
  }
  
  validateReceptionistBooking();
}

/**
 * Load disabled dates
 */
async function loadReceptionistDisabledDates() {
  try {
    // Get clinic_id from receptionist's session/context
    const clinicId = window.CLINIC_ID || 1;
    const response = await fetch(`/PETVET/api/appointments/get-available-dates.php?clinic_id=${clinicId}`);
    const data = await response.json();
    
    if (data.success) {
      receptionistDisabledDates = data.disabled_dates || [];
      renderReceptionistCalendar();
    }
  } catch (error) {
    console.error('Error loading disabled dates:', error);
    renderReceptionistCalendar();
  }
}

/**
 * Handle vet selection
 */
function handleReceptionistVetSelection() {
  const vetSelect = document.getElementById('newVetName');
  if (!vetSelect) return;
  
  vetSelect.addEventListener('change', () => {
    const previousVet = receptionistSelectedVet;
    receptionistSelectedVet = vetSelect.value;
    
    if (receptionistSelectedVet) {
      // Show date section when vet is selected
      const dateSection = document.getElementById('receptionistDateSection');
      if (dateSection) {
        dateSection.style.display = 'block';
      }
      
      // If vet changed, reload dates and reset time selection
      if (previousVet && previousVet !== receptionistSelectedVet) {
        resetFromVetChange();
      }

      // Reload disabled dates and calendar, then reload time slots if date selected
      loadReceptionistDisabledDates();
      if (receptionistSelectedDate) {
        const dateString = formatDateForAPI(receptionistSelectedDate);
        loadReceptionistTimeSlots(dateString);
      }
    } else {
      // Hide all subsequent sections if vet is deselected
      hideSubsequentSections('vet');
    }
    
    validateReceptionistBooking();
  });
}

/**
 * Validate booking form
 */
function validateReceptionistBooking() {
  const newDate = document.getElementById('newDate');
  const newTime = document.getElementById('newTime');
  const newVetName = document.getElementById('newVetName');
  const newPetName = document.getElementById('newPetName');
  const newClientName = document.getElementById('newClientName');
  const newAppointmentType = document.getElementById('newAppointmentType');
  const newCustomerPhone = document.getElementById('newCustomerPhone');
  const newPetType = document.getElementById('newPetType');
  const saveBtn = document.getElementById('saveAppointmentBtn');
  
  if (!saveBtn) return;
  
  // Check all fields are filled
  const allFieldsFilled = newDate?.value &&
                          newTime?.value &&
                          newVetName?.value &&
                          newPetName?.value &&
                          newClientName?.value &&
                          newAppointmentType?.value &&
                          newCustomerPhone?.value &&
                          newPetType?.value;
  
  // Check validation rules for specific fields
  let isValidPhone = true;
  let isValidClientName = true;
  let isValidPetName = true;
  
  if (newCustomerPhone?.value) {
    isValidPhone = validatePhone(newCustomerPhone.value);
  }
  
  if (newClientName?.value) {
    isValidClientName = validateName(newClientName.value);
  }
  
  if (newPetName?.value) {
    isValidPetName = validateName(newPetName.value);
  }
  
  const isValid = allFieldsFilled && isValidPhone && isValidClientName && isValidPetName;
  
  // Update button state
  saveBtn.disabled = !isValid;
  
  if (isValid) {
    saveBtn.style.opacity = '1';
    saveBtn.style.cursor = 'pointer';
    saveBtn.classList.remove('disabled');
  } else {
    saveBtn.style.opacity = '0.5';
    saveBtn.style.cursor = 'not-allowed';
    saveBtn.classList.add('disabled');
  }
  
  console.log('Validation check:', {
    allFieldsFilled,
    isValidPhone,
    isValidClientName,
    isValidPetName,
    isValid,
    buttonDisabled: saveBtn.disabled
  });
}

/**
 * Format date for API
 */
function formatDateForAPI(date) {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}

/**
 * Format time to 12-hour
 */
function formatTime12Hour(time24) {
  const [hours, minutes] = time24.split(':');
  const hour = parseInt(hours);
  const ampm = hour >= 12 ? 'PM' : 'AM';
  const hour12 = hour > 12 ? hour - 12 : (hour === 0 ? 12 : hour);
  return `${hour12}:${minutes} ${ampm}`;
}

/**
 * Reset from vet change (cascade reset)
 */
function resetFromVetChange() {
  receptionistSelectedTime = null;

  document.getElementById('newTime').value = '';

  const timeSection = document.getElementById('receptionistTimeSection');
  const detailsSection = document.getElementById('receptionistDetailsSection');
  
  if (timeSection) timeSection.style.display = 'none';
  if (detailsSection) detailsSection.style.display = 'none';
  
  // Clear time slots
  const timeSlotsGrid = document.getElementById('receptionistTimeSlotsGrid');
  if (timeSlotsGrid) timeSlotsGrid.innerHTML = '';
}

/**
 * Reset from date change (cascade reset)
 */
function resetFromDateChange() {
  receptionistSelectedTime = null;
  
  document.getElementById('newTime').value = '';
  
  const detailsSection = document.getElementById('receptionistDetailsSection');
  if (detailsSection) detailsSection.style.display = 'none';
}

/**
 * Reset from time change (cascade reset)
 */
function resetFromTimeChange() {
  return;
}

/**
 * Hide subsequent sections based on change point
 */
function hideSubsequentSections(from) {
  const dateSection = document.getElementById('receptionistDateSection');
  const timeSection = document.getElementById('receptionistTimeSection');
  const detailsSection = document.getElementById('receptionistDetailsSection');
  
  if (from === 'vet') {
    if (dateSection) dateSection.style.display = 'none';
    if (timeSection) timeSection.style.display = 'none';
    if (detailsSection) detailsSection.style.display = 'none';
    
    receptionistSelectedDate = null;
    receptionistSelectedTime = null;
    document.getElementById('newDate').value = '';
    document.getElementById('newTime').value = '';
    document.getElementById('newPetName').value = '';
    document.getElementById('newClientName').value = '';
  }
}

/**
 * Reset booking form
 */
function resetReceptionistBooking() {
  receptionistCalendarMonth = new Date();
  receptionistSelectedDate = null;
  receptionistSelectedTime = null;
  receptionistSelectedVet = null;
  walkinCustomerPhone = null;
  window.currentRegisteredUserId = null;
  
  const vetSection = document.getElementById('receptionistVetSection');
  const dateSection = document.getElementById('receptionistDateSection');
  const timeSection = document.getElementById('receptionistTimeSection');
  const detailsSection = document.getElementById('receptionistDetailsSection');

  if (vetSection) vetSection.style.display = 'block';
  if (dateSection) dateSection.style.display = 'none';
  if (timeSection) timeSection.style.display = 'none';
  if (detailsSection) detailsSection.style.display = 'none';
  
  const phoneField = document.getElementById('newCustomerPhone');
  const appointmentTypeField = document.getElementById('newAppointmentType');
  const newDateField = document.getElementById('newDate');
  const newTimeField = document.getElementById('newTime');
  const newVetNameField = document.getElementById('newVetName');
  const newPetNameField = document.getElementById('newPetName');
  const newClientNameField = document.getElementById('newClientName');
  const newPetTypeField = document.getElementById('newPetType');
  const newEmailField = document.getElementById('newCustomerEmail');
  
  if (phoneField) phoneField.value = '';
  if (appointmentTypeField) appointmentTypeField.value = '';
  if (newDateField) newDateField.value = '';
  if (newTimeField) newTimeField.value = '';
  if (newVetNameField) newVetNameField.value = '';
  if (newPetNameField) newPetNameField.value = '';
  if (newClientNameField) newClientNameField.value = '';
  if (newPetTypeField) newPetTypeField.value = 'other';
  if (newEmailField) newEmailField.value = '';
  
  // Clear validation error messages
  showPhoneError('');
  showClientNameError('');
  showPetNameError('');
  
  loadReceptionistDisabledDates();
  validateReceptionistBooking();
}

// Initialize when modal opens
document.addEventListener('DOMContentLoaded', () => {
  handleReceptionistVetSelection();
  
  // Add real-time validation listeners for contact and pet details
  const phoneField = document.getElementById('newCustomerPhone');
  const clientNameField = document.getElementById('newClientName');
  const petNameField = document.getElementById('newPetName');
  
  if (phoneField) {
    phoneField.addEventListener('input', (e) => {
      validatePhoneInput(e);
      validateReceptionistBooking();
    });
    phoneField.addEventListener('change', (e) => {
      validatePhoneInput(e);
      validateReceptionistBooking();
    });
    phoneField.addEventListener('blur', (e) => {
      validatePhoneInput(e);
      validateReceptionistBooking();
    });
  }
  
  if (clientNameField) {
    clientNameField.addEventListener('input', (e) => {
      validateClientNameInput(e);
      validateReceptionistBooking();
    });
    clientNameField.addEventListener('change', (e) => {
      validateClientNameInput(e);
      validateReceptionistBooking();
    });
    clientNameField.addEventListener('blur', (e) => {
      validateClientNameInput(e);
      validateReceptionistBooking();
    });
  }
  
  if (petNameField) {
    petNameField.addEventListener('input', (e) => {
      validatePetNameInput(e);
      validateReceptionistBooking();
    });
    petNameField.addEventListener('change', (e) => {
      validatePetNameInput(e);
      validateReceptionistBooking();
    });
    petNameField.addEventListener('blur', (e) => {
      validatePetNameInput(e);
      validateReceptionistBooking();
    });
  }
  
  // Add validation listeners for form fields
  const formFields = ['newPetName', 'newClientName', 'newAppointmentType', 'newDate', 'newTime', 'newVetName'];
  formFields.forEach(fieldId => {
    const field = document.getElementById(fieldId);
    if (field) {
      field.addEventListener('input', validateReceptionistBooking);
      field.addEventListener('change', validateReceptionistBooking);
    }
  });
  
  // Add listener for appointment type to ensure validation
  const appointmentTypeField = document.getElementById('newAppointmentType');
  if (appointmentTypeField) {
    appointmentTypeField.addEventListener('change', validateReceptionistBooking);
  }
});

// Export to global scope
window.initReceptionistBooking = initReceptionistBooking;
window.resetReceptionistBooking = resetReceptionistBooking;
window.validateReceptionistBooking = validateReceptionistBooking;
