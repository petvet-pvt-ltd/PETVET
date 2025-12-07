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

/**
 * Initialize receptionist booking calendar
 */
function initReceptionistBooking() {
  console.log('Initializing receptionist booking system');
  
  // Load disabled dates for the clinic
  loadReceptionistDisabledDates();
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
    const response = await fetch(`/PETVET/api/appointments/get-available-times.php?clinic_id=1&vet_id=${vetId}&date=${dateString}`);
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
    const response = await fetch('/PETVET/api/appointments/get-available-dates.php?clinic_id=1');
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
      
      // If vet changed, reset date, time, and details
      if (previousVet && previousVet !== receptionistSelectedVet) {
        resetFromVetChange();
      }
      
      // If date is already selected, reload time slots for new vet
      if (receptionistSelectedDate) {
        const dateString = formatDateForAPI(receptionistSelectedDate);
        loadReceptionistTimeSlots(dateString);
      }
      
      // Render calendar with vet-specific disabled dates
      renderReceptionistCalendar();
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
  const saveBtn = document.getElementById('saveAppointmentBtn');
  
  if (!saveBtn) return;
  
  const isValid = newDate?.value &&
                  newTime?.value &&
                  newVetName?.value &&
                  newPetName?.value &&
                  newClientName?.value &&
                  newAppointmentType?.value;
  
  saveBtn.disabled = !isValid;
  
  if (isValid) {
    saveBtn.style.opacity = '1';
    saveBtn.style.cursor = 'pointer';
  } else {
    saveBtn.style.opacity = '0.5';
    saveBtn.style.cursor = 'not-allowed';
  }
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
  receptionistSelectedDate = null;
  receptionistSelectedTime = null;
  
  document.getElementById('newDate').value = '';
  document.getElementById('newTime').value = '';
  document.getElementById('newPetName').value = '';
  document.getElementById('newClientName').value = '';
  
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
  document.getElementById('newPetName').value = '';
  document.getElementById('newClientName').value = '';
  
  const detailsSection = document.getElementById('receptionistDetailsSection');
  if (detailsSection) detailsSection.style.display = 'none';
}

/**
 * Reset from time change (cascade reset)
 */
function resetFromTimeChange() {
  document.getElementById('newPetName').value = '';
  document.getElementById('newClientName').value = '';
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
  
  const dateSection = document.getElementById('receptionistDateSection');
  const timeSection = document.getElementById('receptionistTimeSection');
  const detailsSection = document.getElementById('receptionistDetailsSection');
  
  if (dateSection) dateSection.style.display = 'none';
  if (timeSection) timeSection.style.display = 'none';
  if (detailsSection) detailsSection.style.display = 'none';
  
  const appointmentTypeField = document.getElementById('newAppointmentType');
  const newDateField = document.getElementById('newDate');
  const newTimeField = document.getElementById('newTime');
  const newVetNameField = document.getElementById('newVetName');
  const newPetNameField = document.getElementById('newPetName');
  const newClientNameField = document.getElementById('newClientName');
  
  if (appointmentTypeField) appointmentTypeField.value = '';
  if (newDateField) newDateField.value = '';
  if (newTimeField) newTimeField.value = '';
  if (newVetNameField) newVetNameField.value = '';
  if (newPetNameField) newPetNameField.value = '';
  if (newClientNameField) newClientNameField.value = '';
  
  loadReceptionistDisabledDates();
  validateReceptionistBooking();
}

// Initialize when modal opens
document.addEventListener('DOMContentLoaded', () => {
  handleReceptionistVetSelection();
  
  // Add validation listeners for form fields
  const formFields = ['newPetName', 'newClientName', 'newAppointmentType'];
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
