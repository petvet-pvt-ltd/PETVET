/**
 * Custom Calendar and Booking Flow for Appointment System
 * NO EXTERNAL LIBRARIES - Pure Vanilla JavaScript
 */

// Calendar state
let currentCalendarMonth = new Date();
let selectedDate = null;
let disabledDates = [];
let currentClinicId = null;
let currentVetId = null;

/**
 * Initialize calendar widget
 */
function initializeCalendar() {
  const calendarWidget = document.getElementById('calendarWidget');
  if (!calendarWidget) return;
  
  renderCalendar();
}

/**
 * Render calendar for current month
 */
function renderCalendar() {
  const calendarWidget = document.getElementById('calendarWidget');
  if (!calendarWidget) return;
  
  const year = currentCalendarMonth.getFullYear();
  const month = currentCalendarMonth.getMonth();
  
  // Month names
  const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'];
  
  // Get first day of month and number of days
  const firstDay = new Date(year, month, 1);
  const lastDay = new Date(year, month + 1, 0);
  const daysInMonth = lastDay.getDate();
  const startDayOfWeek = firstDay.getDay(); // 0 = Sunday
  
  // Calculate date limits
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  const maxDate = new Date();
  maxDate.setDate(maxDate.getDate() + 30);
  maxDate.setHours(0, 0, 0, 0);
  
  // Build calendar HTML
  let html = `
    <div class="calendar-header">
      <div class="calendar-month">${monthNames[month]} ${year}</div>
      <div class="calendar-nav">
        <button type="button" id="prevMonth" ${month === today.getMonth() && year === today.getFullYear() ? 'disabled' : ''}>‹</button>
        <button type="button" id="nextMonth">›</button>
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
  
  // Empty cells before first day
  for (let i = 0; i < startDayOfWeek; i++) {
    html += '<div class="calendar-day empty"></div>';
  }
  
  // Days of the month
  for (let day = 1; day <= daysInMonth; day++) {
    const date = new Date(year, month, day);
    date.setHours(0, 0, 0, 0);
    const dateString = formatDateForAPI(date);
    
    let classes = ['calendar-day'];
    let clickable = true;
    
    // Check if today
    if (date.getTime() === today.getTime()) {
      classes.push('today');
    }
    
    // Check if selected
    if (selectedDate && formatDateForAPI(selectedDate) === dateString) {
      classes.push('selected');
    }
    
    // Check if disabled (past, beyond 30 days, or in disabled list)
    if (date < today || date > maxDate || disabledDates.includes(dateString)) {
      classes.push('disabled');
      clickable = false;
    }
    
    html += `<div class="${classes.join(' ')}" ${clickable ? `data-date="${dateString}"` : ''}>${day}</div>`;
  }
  
  html += '</div></div>';
  
  calendarWidget.innerHTML = html;
  
  // Attach event listeners
  attachCalendarListeners();
}

/**
 * Attach event listeners to calendar elements
 */
function attachCalendarListeners() {
  // Previous month button
  const prevBtn = document.getElementById('prevMonth');
  if (prevBtn) {
    prevBtn.addEventListener('click', () => {
      currentCalendarMonth.setMonth(currentCalendarMonth.getMonth() - 1);
      renderCalendar();
    });
  }
  
  // Next month button
  const nextBtn = document.getElementById('nextMonth');
  if (nextBtn) {
    nextBtn.addEventListener('click', () => {
      currentCalendarMonth.setMonth(currentCalendarMonth.getMonth() + 1);
      renderCalendar();
    });
  }
  
  // Day click handlers
  const dayElements = document.querySelectorAll('.calendar-day[data-date]');
  dayElements.forEach(dayEl => {
    dayEl.addEventListener('click', () => {
      const dateString = dayEl.getAttribute('data-date');
      selectDate(dateString);
    });
  });
}

/**
 * Select a date and load time slots
 */
function selectDate(dateString) {
  selectedDate = new Date(dateString + 'T00:00:00');
  
  // Update hidden input
  const appointmentDate = document.getElementById('appointmentDate');
  if (appointmentDate) {
    appointmentDate.value = dateString;
  }
  
  // Re-render calendar to show selection
  renderCalendar();
  
  // Show time section and load time slots
  const timeSection = document.getElementById('timeSection');
  if (timeSection) {
    timeSection.style.display = 'block';
  }
  
  loadTimeSlots(dateString);
}

/**
 * Load time slots for selected date
 */
async function loadTimeSlots(dateString) {
  const timeSlotsGrid = document.getElementById('timeSlotsGrid');
  if (!timeSlotsGrid) return;
  
  if (!currentClinicId || !currentVetId) {
    timeSlotsGrid.innerHTML = '<div class="time-slots-empty">Please select a clinic and vet first</div>';
    return;
  }
  
  // Show loading
  timeSlotsGrid.innerHTML = '<div class="time-slots-loading">Loading available times...</div>';
  
  try {
    const vetParam = currentVetId === '0' ? 'any' : currentVetId;
    const response = await fetch(`/PETVET/api/appointments/get-available-times.php?clinic_id=${currentClinicId}&vet_id=${vetParam}&date=${dateString}`);
    const data = await response.json();
    
    if (data.success) {
      if (data.available_slots && data.available_slots.length > 0) {
        renderTimeSlots(data.available_slots);
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
 * Render time slots grid
 */
function renderTimeSlots(slots) {
  const timeSlotsGrid = document.getElementById('timeSlotsGrid');
  if (!timeSlotsGrid) return;
  
  timeSlotsGrid.innerHTML = '';
  
  // Get current time if selected date is today
  const now = new Date();
  const isToday = selectedDate && 
                  selectedDate.getFullYear() === now.getFullYear() &&
                  selectedDate.getMonth() === now.getMonth() &&
                  selectedDate.getDate() === now.getDate();
  
  const currentTimeMinutes = isToday ? (now.getHours() * 60 + now.getMinutes()) : -1;
  
  slots.forEach(timeString => {
    const [hours, minutes] = timeString.split(':').map(Number);
    const slotTimeMinutes = hours * 60 + minutes;
    
    // Skip past time slots if today
    if (isToday && slotTimeMinutes <= currentTimeMinutes) {
      return;
    }
    
    const slotDiv = document.createElement('div');
    slotDiv.className = 'time-slot';
    slotDiv.textContent = formatTime12Hour(timeString);
    slotDiv.setAttribute('data-time', timeString);
    
    slotDiv.addEventListener('click', () => selectTimeSlot(slotDiv, timeString));
    
    timeSlotsGrid.appendChild(slotDiv);
  });
  
  // Show message if no slots available after filtering
  if (timeSlotsGrid.children.length === 0) {
    timeSlotsGrid.innerHTML = '<div class="time-slots-empty">😔 No available time slots for this date. Please select another date.</div>';
  }
}

/**
 * Select a time slot
 */
function selectTimeSlot(slotElement, timeString) {
  // Remove previous selection
  document.querySelectorAll('.time-slot').forEach(slot => {
    slot.classList.remove('selected');
  });
  
  // Select current
  slotElement.classList.add('selected');
  
  // Update hidden input
  const appointmentTime = document.getElementById('appointmentTime');
  if (appointmentTime) {
    appointmentTime.value = timeString;
  }
  
  // Show notice section
  const noticeSection = document.getElementById('noticeSection');
  if (noticeSection) {
    noticeSection.style.display = 'block';
  }
  
  // Enable confirm button
  validateBookingForm();
}

/**
 * Load disabled dates from API
 */
async function loadDisabledDates(clinicId) {
  currentClinicId = clinicId;
  
  try {
    const response = await fetch(`/PETVET/api/appointments/get-available-dates.php?clinic_id=${clinicId}`);
    const data = await response.json();
    
    console.log('Disabled dates loaded:', data);
    
    if (data.success) {
      disabledDates = data.disabled_dates || [];
      console.log('Disabled dates array:', disabledDates);
      
      // Calendar is ready but date section will show when vet is selected
      // (renderCalendar will be called when vet is selected)
    }
  } catch (error) {
    console.error('Error loading disabled dates:', error);
  }
}

/**
 * Format date for API (YYYY-MM-DD)
 */
function formatDateForAPI(date) {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}

/**
 * Format time to 12-hour format
 */
function formatTime12Hour(time24) {
  const [hours, minutes] = time24.split(':');
  const hour = parseInt(hours);
  const ampm = hour >= 12 ? 'PM' : 'AM';
  const hour12 = hour > 12 ? hour - 12 : (hour === 0 ? 12 : hour);
  return `${hour12}:${minutes} ${ampm}`;
}

/**
 * Validate booking form
 */
function validateBookingForm() {
  const appointmentType = document.getElementById('appointmentType');
  const clinicSelect = document.getElementById('clinicSelect');
  const selectedVetId = document.getElementById('selectedVetId');
  const appointmentDate = document.getElementById('appointmentDate');
  const appointmentTime = document.getElementById('appointmentTime');
  const confirmBtn = document.getElementById('appointmentConfirmBtn');
  
  if (!confirmBtn) return;
  
  const isValid = appointmentType?.value &&
                  clinicSelect?.value &&
                  selectedVetId?.value &&
                  appointmentDate?.value &&
                  appointmentTime?.value;
  
  confirmBtn.disabled = !isValid;
}

// Export functions to global scope
window.initializeCalendar = initializeCalendar;
window.loadDisabledDates = loadDisabledDates;
window.validateBookingForm = validateBookingForm;
window.loadTimeSlotsForVet = loadTimeSlots;
window.renderCalendarNow = renderCalendar;
window.currentVetId = null;
