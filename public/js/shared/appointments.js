/**
 * Shared Appointment JavaScript Functions
 * Used by both Clinic Manager and Receptionist appointments pages
 * Contains common functionality for calendar view switching and form handling
 */

// Auto-submit vet filter on change & active tab class management
(function(){
    const sel = document.getElementById('vetSelect');
    const viewInput = document.getElementById('currentViewInput');
    
    // Auto-submit form when vet filter changes
    if(sel){ 
        sel.addEventListener('change', () => sel.form.submit()); 
    }
    
    // Calendar view switching functionality
    const tabs = ['today','week','month'];
    
    window.showCalendarView = function(view){
        tabs.forEach(v => {
            const cal = document.getElementById('calendar-'+v);
            const b = document.getElementById('btn-'+v);
            if(cal) cal.classList.remove('active');
            if(b) b.classList.remove('active');
        });
        
        const targetCal = document.getElementById('calendar-'+view);
        const targetBtn = document.getElementById('btn-'+view);
        if(targetCal) targetCal.classList.add('active');
        if(targetBtn) targetBtn.classList.add('active');
        
        // Persist selection on next submit
        if(viewInput) viewInput.value = view; 
    };
    
    // Reschedule state management
    let rescheduleCalendarMonth = new Date();
    let rescheduleSelectedDate = null;
    let rescheduleSelectedTime = null;
    let rescheduleSelectedVet = null;
    let rescheduleDisabledDates = [];

    // Normalize time to HH:MM (strip seconds if present)
    function normalizeTime(timeStr) {
        if (!timeStr) return '';
        const parts = String(timeStr).trim().split(':');
        if (parts.length < 2) return String(timeStr).trim();
        return `${parts[0].padStart(2, '0')}:${parts[1].padStart(2, '0')}`;
    }

    // Modal functionality
    window.openDetailsFromEl = function(element) {
        const appointmentId = element.getAttribute('data-id');
        const pet = element.getAttribute('data-pet');
        const animal = element.getAttribute('data-animal');
        const client = element.getAttribute('data-client');
        const vet = element.getAttribute('data-vet');
        const vetId = (element.getAttribute('data-vet-id') || '').toString().trim();
        const type = element.getAttribute('data-type') || 'General Checkup';
        const phone = element.getAttribute('data-phone') || 'N/A';
        const date = element.getAttribute('data-date');
        const time = element.getAttribute('data-time');
        const normalizedTime = normalizeTime(time);
        
        // Store appointment ID and original values globally for cancel/reschedule
        window.currentAppointmentId = appointmentId;
        window.originalAppointmentData = {
            vet: vet,
            vetId: vetId,
            date: date,
            time: normalizedTime,
            type: type
        };
        
        // Reset reschedule state and pre-populate with current values
        rescheduleCalendarMonth = new Date(date + 'T00:00:00');
        rescheduleSelectedDate = new Date(date + 'T00:00:00');
        rescheduleSelectedTime = normalizedTime;
        rescheduleSelectedVet = vetId;
        
        // Populate modal fields
        const modal = document.getElementById('detailsModal');
        if (modal) {
            const petSpan = document.getElementById('dPet');
            const speciesSpan = document.getElementById('dSpecies');
            const clientSpan = document.getElementById('dClient');
            const phoneSpan = document.getElementById('dPhone');
            const apptTimeSpan = document.getElementById('dApptTime');
            const typeSelect = document.getElementById('dAppointmentType');
            const vetSelect = document.getElementById('dVet');
            const dateInput = document.getElementById('dDate');
            const timeInput = document.getElementById('dTime');
            const rescheduleBtn = document.getElementById('rescheduleBtn');
            
            if (petSpan) petSpan.textContent = pet;
            if (speciesSpan) speciesSpan.textContent = animal || 'N/A';
            if (clientSpan) clientSpan.textContent = client;
            if (phoneSpan) phoneSpan.textContent = phone;
            if (apptTimeSpan) apptTimeSpan.textContent = normalizedTime ? formatTime12Hour(normalizedTime) : '';

            if (typeSelect) {
                // Try to match stored type to known option values
                const normalized = (type || '').toString().trim().toLowerCase();
                typeSelect.value = typeSelect.querySelector(`option[value="${normalized}"]`) ? normalized : '';
                typeSelect.removeEventListener('change', validateRescheduleForm);
                typeSelect.addEventListener('change', validateRescheduleForm);
            }
            
            // Set vet selection and hidden inputs with current values
            if (vetSelect) {
                vetSelect.value = vetId;
                // If direct set fails, try to match an option value
                if (!vetSelect.value && vetId) {
                    const match = Array.from(vetSelect.options).find(opt => (opt.value || '').toString().trim() === vetId);
                    if (match) vetSelect.value = match.value;
                }
                rescheduleSelectedVet = vetSelect.value;
            }
            if (dateInput) {
                dateInput.value = date;
            }
            if (timeInput) {
                // Keep time in HH:MM so it matches available slots format
                timeInput.value = normalizedTime;
            }
            
            // Attach vet change handler
            if (vetSelect) {
                vetSelect.removeEventListener('change', handleRescheduleVetSelection);
                vetSelect.addEventListener('change', handleRescheduleVetSelection);
            }
            
            // Disable reschedule button initially (no changes yet)
            if (rescheduleBtn) {
                rescheduleBtn.disabled = true;
                rescheduleBtn.style.opacity = '0.5';
                rescheduleBtn.style.cursor = 'not-allowed';
            }
            
            // Load disabled dates and initialize calendar with current date selected
            loadRescheduleDisabledDates().then(() => {
                // Show date and time sections with pre-selected values
                const dateSection = document.getElementById('rescheduleDateSection');
                const timeSection = document.getElementById('rescheduleTimeSection');
                
                if (dateSection) {
                    dateSection.style.display = 'block';
                }
                if (timeSection) {
                    timeSection.style.display = 'block';
                }
                
                // Render calendar with current date selected
                renderRescheduleCalendar();
                
                // Load time slots with current time pre-selected
                loadRescheduleTimeSlots(date);
            });
            
            // Show modal
            modal.classList.remove('hidden');
            modal.classList.add('active');
        }
    };

    // Handle vet selection in reschedule modal
    function handleRescheduleVetSelection() {
        const vetSelect = document.getElementById('dVet');
        if (!vetSelect) return;
        
        const previousVet = rescheduleSelectedVet;
        rescheduleSelectedVet = vetSelect.value;
        
        if (rescheduleSelectedVet) {
            // Show date section when vet is selected
            const dateSection = document.getElementById('rescheduleDateSection');
            if (dateSection) {
                dateSection.style.display = 'block';
            }
            
            // If vet changed, reset date and time only if it's different from original
            if (previousVet && previousVet !== rescheduleSelectedVet) {
                // Keep current selections, but reload time slots
                if (rescheduleSelectedDate) {
                    const dateString = formatDateForAPI(rescheduleSelectedDate);
                    loadRescheduleTimeSlots(dateString);
                }
            }
            
            // Render calendar
            renderRescheduleCalendar();
        } else {
            // Hide all subsequent sections if vet is deselected
            const dateSection = document.getElementById('rescheduleDateSection');
            const timeSection = document.getElementById('rescheduleTimeSection');
            if (dateSection) dateSection.style.display = 'none';
            if (timeSection) timeSection.style.display = 'none';
        }
        
        validateRescheduleForm();
    }

    // Render reschedule calendar
    function renderRescheduleCalendar() {
        const calendarWidget = document.getElementById('rescheduleCalendarWidget');
        if (!calendarWidget) return;
        
        const year = rescheduleCalendarMonth.getFullYear();
        const month = rescheduleCalendarMonth.getMonth();
        
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
                    <button type="button" id="reschedulePrevMonth" ${month === today.getMonth() && year === today.getFullYear() ? 'disabled' : ''}>‹</button>
                    <button type="button" id="rescheduleNextMonth">›</button>
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
            
            if (rescheduleSelectedDate && formatDateForAPI(rescheduleSelectedDate) === dateString) {
                classes.push('selected');
            }
            
            if (date < today || date > maxDate || rescheduleDisabledDates.includes(dateString)) {
                classes.push('disabled');
                clickable = false;
            }
            
            html += `<div class="${classes.join(' ')}" ${clickable ? `data-date="${dateString}"` : ''}>${day}</div>`;
        }
        
        html += '</div></div>';
        calendarWidget.innerHTML = html;
        
        attachRescheduleCalendarListeners();
    }

    // Attach reschedule calendar event listeners
    function attachRescheduleCalendarListeners() {
        const prevBtn = document.getElementById('reschedulePrevMonth');
        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                rescheduleCalendarMonth.setMonth(rescheduleCalendarMonth.getMonth() - 1);
                renderRescheduleCalendar();
            });
        }
        
        const nextBtn = document.getElementById('rescheduleNextMonth');
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                rescheduleCalendarMonth.setMonth(rescheduleCalendarMonth.getMonth() + 1);
                renderRescheduleCalendar();
            });
        }
        
        const dayElements = document.querySelectorAll('#rescheduleCalendarWidget .calendar-day[data-date]');
        dayElements.forEach(dayEl => {
            dayEl.addEventListener('click', () => {
                const dateString = dayEl.getAttribute('data-date');
                selectRescheduleDate(dateString);
            });
        });
    }

    // Select reschedule date and show time slots
    function selectRescheduleDate(dateString) {
        rescheduleSelectedDate = new Date(dateString + 'T00:00:00');
        
        const dateInput = document.getElementById('dDate');
        if (dateInput) {
            dateInput.value = dateString;
        }
        
        renderRescheduleCalendar();
        
        // Only reset time if date changed from original
        if (dateString !== window.originalAppointmentData.date) {
            rescheduleSelectedTime = null;
            document.getElementById('dTime').value = '';
        }
        
        const timeSection = document.getElementById('rescheduleTimeSection');
        if (timeSection) {
            timeSection.style.display = 'block';
        }
        
        loadRescheduleTimeSlots(dateString);
        validateRescheduleForm();
    }

    // Load reschedule time slots
    async function loadRescheduleTimeSlots(dateString) {
        const timeSlotsGrid = document.getElementById('rescheduleTimeSlotsGrid');
        if (!timeSlotsGrid) return;
        
        timeSlotsGrid.innerHTML = '<div class="time-slots-loading">Loading available times...</div>';
        
        try {
            const vetId = rescheduleSelectedVet || 'any';
            const clinicId = window.CLINIC_ID || 1;
            const response = await fetch(`/PETVET/api/appointments/get-available-times.php?clinic_id=${clinicId}&vet_id=${vetId}&date=${dateString}&exclude_appointment=${window.currentAppointmentId}`);
            const data = await response.json();
            
            if (data.success) {
                if (data.available_slots && data.available_slots.length > 0) {
                    renderRescheduleTimeSlots(data.available_slots);
                } else {
                    timeSlotsGrid.innerHTML = '<div class="time-slots-empty">😔 No available time slots for this date. Please select another date.</div>';
                }
            } else {
                timeSlotsGrid.innerHTML = `<div class="time-slots-empty">Error: ${data.error || 'Failed to load time slots'}</div>`;
            }
        } catch (error) {
            console.error('Error loading reschedule time slots:', error);
            timeSlotsGrid.innerHTML = '<div class="time-slots-empty">Error loading time slots</div>';
        }
    }

    // Render reschedule time slots
    function renderRescheduleTimeSlots(slots) {
        const timeSlotsGrid = document.getElementById('rescheduleTimeSlotsGrid');
        if (!timeSlotsGrid) return;
        
        timeSlotsGrid.innerHTML = '';
        
        const now = new Date();
        const isToday = rescheduleSelectedDate && 
                        rescheduleSelectedDate.getFullYear() === now.getFullYear() &&
                        rescheduleSelectedDate.getMonth() === now.getMonth() &&
                        rescheduleSelectedDate.getDate() === now.getDate();
        
        const currentTimeMinutes = isToday ? (now.getHours() * 60 + now.getMinutes()) : -1;
        
        // Check if we should pre-select the original time
        const currentDateString = rescheduleSelectedDate ? formatDateForAPI(rescheduleSelectedDate) : '';
        const shouldPreselect = currentDateString === window.originalAppointmentData.date;
        const originalTime = normalizeTime(window.originalAppointmentData.time);
        const selectedTime = normalizeTime(rescheduleSelectedTime);
        
        console.log('Rendering time slots - Should preselect:', shouldPreselect, 'Original time:', originalTime);
        
        slots.forEach(timeString => {
            const normalizedSlot = normalizeTime(timeString);
            const [hours, minutes] = timeString.split(':').map(Number);
            const slotTimeMinutes = hours * 60 + minutes;
            
            if (isToday && slotTimeMinutes <= currentTimeMinutes) {
                return;
            }
            
            const slotDiv = document.createElement('div');
            slotDiv.className = 'time-slot';
            slotDiv.textContent = formatTime12Hour(timeString);
            slotDiv.setAttribute('data-time', timeString);
            
            // Pre-select the current time if on the same date and time matches
            if (shouldPreselect && normalizedSlot === originalTime) {
                console.log('Pre-selecting time slot:', timeString);
                slotDiv.classList.add('selected');
                rescheduleSelectedTime = normalizedSlot;
                const timeInput = document.getElementById('dTime');
                if (timeInput) {
                    timeInput.value = normalizedSlot;
                }
            } else if (selectedTime && normalizedSlot === selectedTime) {
                slotDiv.classList.add('selected');
            }
            
            slotDiv.addEventListener('click', () => selectRescheduleTimeSlot(slotDiv, normalizedSlot));
            
            timeSlotsGrid.appendChild(slotDiv);
        });
        
        if (timeSlotsGrid.children.length === 0) {
            timeSlotsGrid.innerHTML = '<div class="time-slots-empty">😔 No available time slots for this date. Please select another date.</div>';
        }
        
        // Validate after rendering
        validateRescheduleForm();
    }

    // Select reschedule time slot
    function selectRescheduleTimeSlot(slotElement, timeString) {
        document.querySelectorAll('#rescheduleTimeSlotsGrid .time-slot').forEach(slot => {
            slot.classList.remove('selected');
        });
        
        slotElement.classList.add('selected');
        rescheduleSelectedTime = normalizeTime(timeString);
        
        const timeInput = document.getElementById('dTime');
        if (timeInput) {
            timeInput.value = normalizeTime(timeString);
        }
        
        validateRescheduleForm();
    }

    // Load reschedule disabled dates
    async function loadRescheduleDisabledDates() {
        try {
            const clinicId = window.CLINIC_ID || 1;
            const response = await fetch(`/PETVET/api/appointments/get-available-dates.php?clinic_id=${clinicId}`);
            const data = await response.json();
            
            if (data.success) {
                rescheduleDisabledDates = data.disabled_dates || [];
            }
            return true;
        } catch (error) {
            console.error('Error loading disabled dates:', error);
            return false;
        }
    }

    // Validate reschedule form
    function validateRescheduleForm() {
        const dateInput = document.getElementById('dDate');
        const timeInput = document.getElementById('dTime');
        const vetSelect = document.getElementById('dVet');
        const typeSelect = document.getElementById('dAppointmentType');
        const rescheduleBtn = document.getElementById('rescheduleBtn');
        
        if (!rescheduleBtn) return;
        
        // Check if all fields are filled
        const allFieldsFilled = dateInput?.value && timeInput?.value && vetSelect?.value && typeSelect?.value;
        
        // Check if any value changed from original
        const vetChanged = vetSelect?.value && vetSelect.value !== window.originalAppointmentData.vetId;
        const dateChanged = dateInput?.value && dateInput.value !== window.originalAppointmentData.date;
        const timeChanged = timeInput?.value && normalizeTime(timeInput.value) !== normalizeTime(window.originalAppointmentData.time);
        const typeChanged = typeSelect?.value && typeSelect.value !== ((window.originalAppointmentData.type || '').toString().trim().toLowerCase());
        
        const hasChanges = vetChanged || dateChanged || timeChanged || typeChanged;
        
        // Enable button only if all fields filled AND something changed
        const isValid = allFieldsFilled && hasChanges;
        
        rescheduleBtn.disabled = !isValid;
        rescheduleBtn.style.opacity = isValid ? '1' : '0.5';
        rescheduleBtn.style.cursor = isValid ? 'pointer' : 'not-allowed';
    }

    // Format date for API
    function formatDateForAPI(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    // Format time to 12-hour
    function formatTime12Hour(time24) {
        const [hours, minutes] = time24.split(':');
        const hour = parseInt(hours);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const hour12 = hour > 12 ? hour - 12 : (hour === 0 ? 12 : hour);
        return `${hour12}:${minutes} ${ampm}`;
    }
    
    window.openAddModal = function() {
        const modal = document.getElementById('addModal');
        if (modal) {
            // Reset receptionist booking flow
            if (typeof window.resetReceptionistBooking === 'function') {
                window.resetReceptionistBooking();
            }
            
            // Initialize receptionist booking
            if (typeof window.initReceptionistBooking === 'function') {
                window.initReceptionistBooking();
            }
            
            modal.classList.remove('hidden');
            modal.classList.add('active');
        }
    };
    
    window.closeModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('active');
        }
    };

    window.exportAppointments = function() {
        // Placeholder function for exporting appointments
        showNotification('Export functionality would be implemented here', 'Information');
    };
    
    // Global variable to store the action to be executed after confirmation
    let pendingAction = null;
    
    // Show confirmation dialog
    function showConfirmation(title, message, action, buttonText = 'Confirm', buttonClass = 'btn-primary') {
        const modal = document.getElementById('confirmModal');
        const titleEl = document.getElementById('confirmTitle');
        const messageEl = document.getElementById('confirmMessage');
        const buttonEl = document.getElementById('confirmButton');
        
        if (modal && titleEl && messageEl && buttonEl) {
            titleEl.textContent = title;
            messageEl.innerHTML = message;
            buttonEl.textContent = buttonText;
            buttonEl.className = 'btn ' + buttonClass;
            
            pendingAction = action;
            
            modal.classList.remove('hidden');
            modal.classList.add('active');
        }
    }
    
    // Show notification dialog
    function showNotification(message, title = 'Notification', type = 'info') {
        const modal = document.getElementById('notificationModal');
        const titleEl = document.getElementById('notificationTitle');
        const messageEl = document.getElementById('notificationMessage');
        
        if (modal && titleEl && messageEl) {
            titleEl.textContent = title;
            messageEl.innerHTML = message;
            
            // Add type-specific styling
            const modalContent = modal.querySelector('.notification-modal');
            modalContent.className = 'modal-content notification-modal ' + type;
            
            modal.classList.remove('hidden');
            modal.classList.add('active');
        }
    }
    
    // Close confirmation modal
    window.closeConfirmation = function() {
        const modal = document.getElementById('confirmModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('active');
        }
        pendingAction = null;
    };
    
    // Close notification modal
    window.closeNotification = function() {
        const modal = document.getElementById('notificationModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('active');
        }
    };
    
    // Execute the confirmed action
    window.executeConfirmedAction = function() {
        if (pendingAction && typeof pendingAction === 'function') {
            pendingAction();
        }
        closeConfirmation();
    };
    
    // Reschedule appointment with validation and confirmation
    window.rescheduleAppointment = function() {
        const dateInput = document.getElementById('dDate');
        const timeInput = document.getElementById('dTime');
        const vetSelect = document.getElementById('dVet');
        const typeSelect = document.getElementById('dAppointmentType');
        const petSpan = document.getElementById('dPet');
        
        if (!dateInput || !timeInput || !petSpan || !vetSelect || !typeSelect) {
            showNotification('Error: Required fields not found', 'Error', 'error');
            return;
        }
        
        const newDate = dateInput.value;
        const newTime = timeInput.value;
        const newVetId = vetSelect.value;
        const newType = typeSelect.value;
        const petName = petSpan.textContent;
        
        // Validate that date, time, and vet are provided
        if (!newType || !newDate || !newTime || !newVetId) {
            showNotification('Please select appointment type, veterinarian, date and time for rescheduling', 'Validation Error', 'error');
            return;
        }
        
        // Validate that the date is in the future
        const selectedDateTime = new Date(newDate + 'T' + newTime);
        const now = new Date();
        
        if (selectedDateTime <= now) {
            showNotification('Please select a future date and time for the appointment', 'Invalid Date/Time', 'error');
            return;
        }
        
        // Format date and time for display
        const dateFormatted = new Date(newDate).toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        const timeFormatted = formatTime12Hour(newTime);
        
        // Get selected vet name
        const selectedVetName = vetSelect.options[vetSelect.selectedIndex].text;
        const selectedTypeText = typeSelect.options[typeSelect.selectedIndex]?.text || newType;
        
        // Show confirmation dialog
        const confirmMessage = `
            <div class="reschedule-intro">
                Are you sure you want to reschedule<br>
                <strong>${petName}'s</strong> appointment?
            </div>
            <div class="reschedule-grid">
                <div class="reschedule-label">New Type:</div>
                <div class="reschedule-value">${selectedTypeText}</div>

                <div class="reschedule-label">New Vet:</div>
                <div class="reschedule-value">${selectedVetName}</div>

                <div class="reschedule-label">New Date:</div>
                <div class="reschedule-value">${dateFormatted}</div>

                <div class="reschedule-label">New Time:</div>
                <div class="reschedule-value">${timeFormatted}</div>
            </div>
        `;
        
        showConfirmation('Reschedule Appointment', confirmMessage, function() {
            // Execute reschedule via API
            fetch('/PETVET/api/appointments/reschedule.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    appointment_id: window.currentAppointmentId,
                    date: newDate,
                    time: newTime,
                    vet_id: newVetId,
                    appointment_type: newType
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(`Appointment for ${petName} has been rescheduled successfully!`, 'Success', 'success');
                    closeModal('detailsModal');
                    // Refresh calendar immediately without page reload
                    console.log('🔄 Triggering forced calendar refresh after reschedule...');
                    setTimeout(() => {
                        if (window.refreshCalendarNow) {
                            window.refreshCalendarNow(true); // Force update bypassing timestamp check
                        } else {
                            console.error('❌ window.refreshCalendarNow is not defined!');
                        }
                    }, 300);
                } else {
                    showNotification(data.error || 'Failed to reschedule appointment', 'Error', 'error');
                }
            })
            .catch(error => {
                console.error('Reschedule error:', error);
                showNotification('An error occurred while rescheduling the appointment', 'Error', 'error');
            });
        }, 'Reschedule', 'btn-primary');
    };
    
    // Cancel appointment with confirmation
    window.cancelAppointment = function() {
        const petSpan = document.getElementById('dPet');
        const clientSpan = document.getElementById('dClient');
        const dateInput = document.getElementById('dDate');
        const timeInput = document.getElementById('dTime');
        
        if (!petSpan || !clientSpan || !dateInput || !timeInput) {
            showNotification('Error: Required information not found', 'Error', 'error');
            return;
        }
        
        const petName = petSpan.textContent;
        const clientName = clientSpan.textContent;
        const appointmentDate = new Date(dateInput.value).toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        const appointmentTime = new Date('1970-01-01T' + timeInput.value).toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
        
        // Show confirmation dialog with warning
        const confirmMessage = `
            <div class="warning-box">
                <span class="warning-title">Warning: This action cannot be undone</span>
                <span class="warning-text">Are you sure you want to cancel this appointment?</span>
                <div class="appt-details-list">
                    <div class="appt-detail-row">
                        <span class="appt-detail-label">Pet:</span>
                        <span class="appt-detail-value">${petName}</span>
                    </div>
                    <div class="appt-detail-row">
                        <span class="appt-detail-label">Owner:</span>
                        <span class="appt-detail-value">${clientName}</span>
                    </div>
                    <div class="appt-detail-row">
                        <span class="appt-detail-label">Date:</span>
                        <span class="appt-detail-value">${appointmentDate}</span>
                    </div>
                    <div class="appt-detail-row">
                        <span class="appt-detail-label">Time:</span>
                        <span class="appt-detail-value">${appointmentTime}</span>
                    </div>
                </div>
            </div>
        `;
        
        showConfirmation('Cancel Appointment', confirmMessage, function() {
            // Execute cancellation via API
            if (!window.currentAppointmentId) {
                showNotification('Error: Appointment ID not found', 'Error', 'error');
                return;
            }
            
            fetch('/PETVET/api/appointments/cancel.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ appointment_id: window.currentAppointmentId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(`Appointment for ${petName} (${clientName}) on ${appointmentDate} at ${appointmentTime} has been cancelled.`, 'Appointment Cancelled', 'warning');
                    closeModal('detailsModal');
                    // Refresh calendar immediately without page reload
                    // Add small delay to ensure DB transaction is fully committed
                    setTimeout(() => {
                        if (window.refreshCalendarNow) {
                            window.refreshCalendarNow(true); // Force update bypassing timestamp check
                        }
                    }, 300);
                } else {
                    showNotification('Failed to cancel appointment: ' + (data.error || 'Unknown error'), 'Error', 'error');
                }
            })
            .catch(error => {
                console.error('Cancel error:', error);
                showNotification('An error occurred while cancelling the appointment', 'Error', 'error');
            });
        }, 'Cancel Appointment', 'btn-danger');
    };
    
    // Add appointment with validation
    window.saveAppointment = function() {
        const petInput = document.getElementById('newPetName');
        const clientInput = document.getElementById('newClientName');
        const vetInput = document.getElementById('newVetName');
        const dateInput = document.getElementById('newDate');
        const timeInput = document.getElementById('newTime');
        const appointmentTypeInput = document.getElementById('newAppointmentType');
        
        // Validate all fields are filled
        if (!petInput.value.trim() || !clientInput.value.trim() || !vetInput.value.trim() || 
            !dateInput.value || !timeInput.value || !appointmentTypeInput.value) {
            showNotification('Please complete all steps and fill in all required fields', 'Validation Error', 'error');
            return;
        }
        
        // Validate future date/time
        const selectedDateTime = new Date(dateInput.value + 'T' + timeInput.value);
        const now = new Date();
        
        if (selectedDateTime <= now) {
            showNotification('Please select a future date and time for the appointment', 'Invalid Date/Time', 'error');
            return;
        }
        
        // Get vet name from select element
        const vetSelect = document.getElementById('newVetName');
        const vetName = vetSelect.options[vetSelect.selectedIndex].text;
        
        // Format for confirmation
        const dateFormatted = new Date(dateInput.value).toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        const timeFormatted = new Date('1970-01-01T' + timeInput.value).toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
        
        const confirmMessage = `Please confirm the new appointment details:<br><br>
                               <strong>Pet:</strong> ${petInput.value}<br>
                               <strong>Owner:</strong> ${clientInput.value}<br>
                               <strong>Type:</strong> ${appointmentTypeInput.options[appointmentTypeInput.selectedIndex].text}<br>
                               <strong>Vet:</strong> ${vetName}<br>
                               <strong>Date:</strong> ${dateFormatted}<br>
                               <strong>Time:</strong> ${timeFormatted}`;
        
        showConfirmation('Confirm New Appointment', confirmMessage, function() {
            // Disable button to prevent double submission
            const saveBtn = document.getElementById('saveAppointmentBtn');
            if (saveBtn) {
                saveBtn.disabled = true;
                saveBtn.textContent = 'Saving...';
            }
            
            // Detect current module from URL
            const urlParams = new URLSearchParams(window.location.search);
            const module = urlParams.get('module') || 'clinic-manager';
            
            // Submit to API
            fetch(`/PETVET/api/${module}/add-appointment.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    pet_name: petInput.value.trim(),
                    client_name: clientInput.value.trim(),
                    vet_id: vetInput.value,
                    appointment_date: dateInput.value,
                    appointment_time: timeInput.value,
                    appointment_type: appointmentTypeInput.value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.textContent = 'Save Appointment';
                }
                
                if (data.success) {
                    showNotification(`Appointment successfully created for ${petInput.value} on ${dateFormatted} at ${timeFormatted}`, 'Appointment Created', 'success');
                    closeModal('addModal');
                    
                    // Reset form using receptionist booking reset function
                    if (typeof window.resetReceptionistBooking === 'function') {
                        window.resetReceptionistBooking();
                    }
                    
                    // Refresh calendar immediately without page reload
                    if (window.refreshCalendarNow) {
                        setTimeout(() => window.refreshCalendarNow(), 500);
                    }
                } else {
                    showNotification(data.error || 'Failed to create appointment', 'Error', 'error');
                }
            })
            .catch(error => {
                console.error('Error saving appointment:', error);
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.textContent = 'Save Appointment';
                }
                showNotification('Failed to create appointment. Please try again.', 'Error', 'error');
            });
        }, 'Create Appointment', 'btn-primary');
    };
    
    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal')) {
            const modalId = event.target.id;
            if (modalId) {
                if (modalId === 'confirmModal') {
                    closeConfirmation();
                } else if (modalId === 'notificationModal') {
                    closeNotification();
                } else {
                    closeModal(modalId);
                }
            }
        }
    });
    
    // Close modal on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const activeModal = document.querySelector('.modal.active');
            if (activeModal) {
                if (activeModal.id === 'confirmModal') {
                    closeConfirmation();
                } else if (activeModal.id === 'notificationModal') {
                    closeNotification();
                } else {
                    closeModal(activeModal.id);
                }
            }
        }
    });
    
    // Real-time calendar refresh with AJAX
    let refreshInterval = null;
    let lastUpdateTimestamp = 0;
    
    function refreshCalendar(forceUpdate = false) {
        const activeView = document.querySelector('.calendar-view.active');
        const currentView = activeView?.id.replace('calendar-', '') || 'week';
        
        console.log(`🔍 Active view element:`, activeView);
        console.log(`🔍 Detected view: ${currentView}`);
        
        const vetSelect = document.getElementById('vetSelect');
        const selectedVet = vetSelect ? vetSelect.value : 'all';
        
        // Detect current module from URL
        const urlParams = new URLSearchParams(window.location.search);
        const module = urlParams.get('module') || 'clinic-manager';
        
        // Map module name to API path (handle both hyphenated and underscore versions)
        const apiModule = module.replace('_', '-');
        
        // Use module-specific API endpoint
        const apiUrl = `/PETVET/api/${apiModule}/get-appointments.php?view=${currentView}&vet=${encodeURIComponent(selectedVet)}`;
        
        console.log(`🔄 Refreshing calendar (${currentView} view) from: ${apiUrl}${forceUpdate ? ' [FORCED]' : ''}`);
        
        fetch(apiUrl, {
            method: 'GET',
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log(`📦 Received data: success=${data.success}, timestamp=${data.timestamp}, lastUpdate=${lastUpdateTimestamp}, force=${forceUpdate}`);
            if (data.success && (forceUpdate || data.timestamp > lastUpdateTimestamp)) {
                lastUpdateTimestamp = data.timestamp;
                updateCalendarUI(data.appointments, currentView);
                console.log(`✅ Calendar updated (${Object.keys(data.appointments).length} dates)${forceUpdate ? ' [FORCED]' : ''}`);
            } else if (!data.success) {
                console.error('❌ API error:', data.error);
            } else {
                console.log(`⏭️ Skipped update - timestamp not newer (${data.timestamp} <= ${lastUpdateTimestamp})`);
            }
        })
        .catch(error => {
            console.error('❌ Calendar refresh error:', error);
        });
    }
    
    function updateCalendarUI(appointments, view) {
        console.log(`📊 Updating UI for view: ${view}`, appointments);
        
        if (view === 'today') {
            updateTodayView(appointments);
        } else if (view === 'week') {
            updateWeekView(appointments);
        } else if (view === 'month') {
            updateMonthView(appointments);
        }
        
        // Also update other visible views in case they're shown
        // This ensures consistency across all calendar views
        if (view !== 'today' && document.getElementById('calendar-today')) {
            updateTodayView(appointments);
        }
        if (view !== 'week' && document.querySelector('#calendar-week .day-appointments-container')) {
            updateWeekView(appointments);
        }
        if (view !== 'month' && document.querySelector('#calendar-month .day-appointments-container')) {
            updateMonthView(appointments);
        }
    }
    
    function updateTodayView(appointments) {
        const today = new Date().toISOString().split('T')[0];
        
        // Try to find the today view container - it's directly inside #calendar-today, not nested
        const todayCalendar = document.getElementById('calendar-today');
        const container = todayCalendar?.querySelector('.day-col.today-col > div:nth-child(2)');
        
        console.log(`🔍 Today calendar element:`, todayCalendar);
        console.log(`🔍 Today container:`, container);
        
        if (!container) {
            console.warn('⚠️ Today appointments container not found');
            return;
        }
        
        const todayAppts = appointments[today] || [];
        
        console.log(`📅 Updating TODAY view for ${today}:`, todayAppts);
        
        if (todayAppts.length === 0) {
            container.innerHTML = '<div style="color:#64748b; font-size:15px; text-align:center; margin-top:18px;">No appointments for today.</div>';
        } else {
            container.innerHTML = todayAppts.map(appt => {
                console.log(`  - Appt #${appt.id}: ${appt.client} -> Vet: ${appt.vet}`);
                return `
                <div class="event"
                     data-id="${appt.id}"
                     data-pet="${escapeHtml(appt.pet)}"
                     data-animal="${escapeHtml(appt.animal)}"
                     data-client="${escapeHtml(appt.client)}"
                     data-vet="${escapeHtml(appt.vet)}"
                     data-vet-id="${appt.vet_id || ''}"
                     data-type="${escapeHtml(appt.type)}"
                     data-phone="${escapeHtml(appt.client_phone || 'N/A')}"
                     data-date="${today}"
                     data-time="${appt.time}"
                     onclick="openDetailsFromEl(this)">
                    <span class="evt-time">${formatTime(appt.time)}</span>
                    <span class="evt-client">${escapeHtml(appt.client)}</span>
                    <span class="evt-vet">${escapeHtml(appt.vet)}</span>
                </div>
            `;
            }).join('');
        }
    }
    
    function updateWeekView(appointments) {
        document.querySelectorAll('#calendar-week .day-appointments-container').forEach(container => {
            const date = container.closest('.day-col')?.querySelector('.day-date-stripe')?.textContent;
            if (!date) return;
            
            const dateKey = parseDateFromStripe(date);
            const dayAppts = appointments[dateKey] || [];
            
            container.innerHTML = dayAppts.map(appt => {
                const vetFirstName = appt.vet.split(' ')[0];
                return `
                    <div class="event"
                         data-id="${appt.id}"
                         data-pet="${escapeHtml(appt.pet)}"
                         data-animal="${escapeHtml(appt.animal)}"
                         data-client="${escapeHtml(appt.client)}"
                         data-vet="${escapeHtml(appt.vet)}"
                         data-vet-id="${appt.vet_id || ''}"
                         data-type="${escapeHtml(appt.type)}"
                         data-phone="${escapeHtml(appt.client_phone || 'N/A')}"
                         data-date="${dateKey}"
                         data-time="${appt.time}"
                         onclick="openDetailsFromEl(this)">
                        <span class="evt-compact">
                            <span class="evt-time">${formatTime(appt.time)}</span>
                            <span class="evt-vet-short">${escapeHtml(vetFirstName)}</span>
                        </span>
                    </div>
                `;
            }).join('');
        });
    }
    
    function updateMonthView(appointments) {
        document.querySelectorAll('#calendar-month .day-appointments-container').forEach(container => {
            const date = container.closest('.day-col')?.querySelector('.day-date-stripe')?.textContent;
            if (!date) return;
            
            const dateKey = parseDateFromStripe(date);
            const dayAppts = appointments[dateKey] || [];
            
            container.innerHTML = dayAppts.map(appt => {
                const vetFirstName = appt.vet.split(' ')[0];
                return `
                    <div class="event"
                         data-id="${appt.id}"
                         data-pet="${escapeHtml(appt.pet)}"
                         data-animal="${escapeHtml(appt.animal)}"
                         data-client="${escapeHtml(appt.client)}"
                         data-vet="${escapeHtml(appt.vet)}"
                         data-vet-id="${appt.vet_id || ''}"
                         data-type="${escapeHtml(appt.type)}"
                         data-phone="${escapeHtml(appt.client_phone || 'N/A')}"
                         data-date="${dateKey}"
                         data-time="${appt.time}"
                         onclick="openDetailsFromEl(this)">
                        <span class="evt-compact">
                            <span class="evt-time">${formatTime(appt.time)}</span>
                            <span class="evt-vet-short">${escapeHtml(vetFirstName)}</span>
                        </span>
                    </div>
                `;
            }).join('');
        });
    }
    
    function formatTime(time) {
        const [hours, minutes] = time.split(':');
        const hour = parseInt(hours);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const displayHour = hour > 12 ? hour - 12 : (hour === 0 ? 12 : hour);
        return `${displayHour}:${minutes} ${ampm}`;
    }
    
    function parseDateFromStripe(dateStr) {
        // Parse "Dec 3 - Wed" format to "2025-12-03"
        const currentYear = new Date().getFullYear();
        const months = {
            'Jan': '01', 'Feb': '02', 'Mar': '03', 'Apr': '04',
            'May': '05', 'Jun': '06', 'Jul': '07', 'Aug': '08',
            'Sep': '09', 'Oct': '10', 'Nov': '11', 'Dec': '12'
        };
        
        const parts = dateStr.split(' - ')[0].trim().split(' ');
        const month = months[parts[0]];
        const day = parts[1].padStart(2, '0');
        
        return `${currentYear}-${month}-${day}`;
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Start auto-refresh (every 5 seconds)
    function startAutoRefresh() {
        if (refreshInterval) return; // Already running
        
        refreshInterval = setInterval(refreshCalendar, 5000); // 5 seconds
        console.log('📅 Calendar auto-refresh started (5s interval)');
    }
    
    function stopAutoRefresh() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
            refreshInterval = null;
            console.log('📅 Calendar auto-refresh stopped');
        }
    }
    
    // Start auto-refresh on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', startAutoRefresh);
    } else {
        startAutoRefresh();
    }
    
    // Stop refresh when page is hidden
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            stopAutoRefresh();
        } else {
            startAutoRefresh();
        }
    });
    
    // Expose refresh function globally
    window.refreshCalendarNow = refreshCalendar;
})();