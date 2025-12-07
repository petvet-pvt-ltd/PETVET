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
    
    // Modal functionality
    window.openDetailsFromEl = function(element) {
        const appointmentId = element.getAttribute('data-id');
        const pet = element.getAttribute('data-pet');
        const animal = element.getAttribute('data-animal');
        const client = element.getAttribute('data-client');
        const vet = element.getAttribute('data-vet');
        const type = element.getAttribute('data-type') || 'General Checkup';
        const phone = element.getAttribute('data-phone') || 'N/A';
        const date = element.getAttribute('data-date');
        const time = element.getAttribute('data-time');
        
        // Store appointment ID and original values globally for cancel/reschedule
        window.currentAppointmentId = appointmentId;
        window.originalAppointmentData = {
            vet: vet,
            date: date,
            time: time
        };
        
        // Populate modal fields
        const modal = document.getElementById('detailsModal');
        if (modal) {
            const petSpan = document.getElementById('dPet');
            const speciesSpan = document.getElementById('dSpecies');
            const clientSpan = document.getElementById('dClient');
            const typeSpan = document.getElementById('dType');
            const phoneSpan = document.getElementById('dPhone');
            const vetSelect = document.getElementById('dVet');
            const dateInput = document.getElementById('dDate');
            const timeInput = document.getElementById('dTime');
            const rescheduleBtn = document.getElementById('rescheduleBtn');
            
            if (petSpan) petSpan.textContent = pet;
            if (speciesSpan) speciesSpan.textContent = animal || 'N/A';
            if (clientSpan) clientSpan.textContent = client;
            if (typeSpan) typeSpan.textContent = type;
            if (phoneSpan) phoneSpan.textContent = phone;
            if (vetSelect) vetSelect.value = vet;
            if (dateInput) dateInput.value = date;
            if (timeInput) timeInput.value = time;
            
            // Disable reschedule button initially
            if (rescheduleBtn) {
                rescheduleBtn.disabled = true;
                rescheduleBtn.style.opacity = '0.5';
                rescheduleBtn.style.cursor = 'not-allowed';
            }
            
            // Add change listeners to enable reschedule button
            const enableReschedule = () => {
                const changed = vetSelect.value !== window.originalAppointmentData.vet ||
                               dateInput.value !== window.originalAppointmentData.date ||
                               timeInput.value !== window.originalAppointmentData.time;
                               
                if (rescheduleBtn) {
                    rescheduleBtn.disabled = !changed;
                    rescheduleBtn.style.opacity = changed ? '1' : '0.5';
                    rescheduleBtn.style.cursor = changed ? 'pointer' : 'not-allowed';
                }
            };
            
            if (vetSelect) vetSelect.addEventListener('change', enableReschedule);
            if (dateInput) dateInput.addEventListener('change', enableReschedule);
            if (timeInput) timeInput.addEventListener('input', enableReschedule);
            
            // Show modal
            modal.classList.remove('hidden');
            modal.classList.add('active');
        }
    };
    
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
        const petSpan = document.getElementById('dPet');
        
        if (!dateInput || !timeInput || !petSpan) {
            showNotification('Error: Required fields not found', 'Error', 'error');
            return;
        }
        
        const newDate = dateInput.value;
        const newTime = timeInput.value;
        const petName = petSpan.textContent;
        
        // Validate that date and time are provided
        if (!newDate || !newTime) {
            showNotification('Please select both date and time for rescheduling', 'Validation Error', 'error');
            return;
        }
        
        // Validate that the date is in the future
        const selectedDateTime = new Date(newDate + 'T' + newTime);
        const now = new Date();
        
        if (selectedDateTime <= now) {
            showNotification('Please select a future date and time for the appointment', 'Invalid Date/Time', 'error');
            return;
        }
        
        // Validate business hours (8 AM to 6 PM)
        const selectedHour = parseInt(newTime.split(':')[0]);
        if (selectedHour < 8 || selectedHour >= 18) {
            showNotification('Please select a time between 8:00 AM and 6:00 PM', 'Invalid Time', 'error');
            return;
        }
        
        // Format date and time for display
        const dateFormatted = new Date(newDate).toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        const timeFormatted = new Date('1970-01-01T' + newTime).toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
        
        // Get selected vet
        const vetSelect = document.getElementById('dVet');
        const selectedVet = vetSelect ? vetSelect.value : null;
        
        // Show confirmation dialog
        const confirmMessage = `Are you sure you want to reschedule <strong>${petName}'s</strong> appointment?<br><br>
                               <strong>New Date:</strong> ${dateFormatted}<br>
                               <strong>New Time:</strong> ${timeFormatted}${selectedVet ? '<br><strong>Vet:</strong> ' + selectedVet : ''}`;
        
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
                    vet: selectedVet
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(`Appointment for ${petName} has been rescheduled successfully!`, 'Success', 'success');
                    closeModal('detailsModal');
                    // Refresh calendar immediately without page reload
                    if (window.refreshCalendarNow) {
                        window.refreshCalendarNow();
                    }
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
        const confirmMessage = `<div class="warning-content">
                               <strong>Warning: This action cannot be undone</strong><br><br>
                               Are you sure you want to cancel this appointment?<br><br>
                               <strong>Pet:</strong> ${petName}<br>
                               <strong>Owner:</strong> ${clientName}<br>
                               <strong>Date:</strong> ${appointmentDate}<br>
                               <strong>Time:</strong> ${appointmentTime}
                               </div>`;
        
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
                    if (window.refreshCalendarNow) {
                        window.refreshCalendarNow();
                    }
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
                               <strong>Vet:</strong> ${vetInput.value}<br>
                               <strong>Date:</strong> ${dateFormatted}<br>
                               <strong>Time:</strong> ${timeFormatted}`;
        
        showConfirmation('Confirm New Appointment', confirmMessage, function() {
            // Execute save
            showNotification(`Appointment scheduled for ${petInput.value} on ${dateFormatted} at ${timeFormatted}`, 'Appointment Created', 'success');
            closeModal('addModal');
            
            // Reset form using receptionist booking reset function
            if (typeof window.resetReceptionistBooking === 'function') {
                window.resetReceptionistBooking();
            }
            
            // Refresh calendar immediately without page reload
            if (window.refreshCalendarNow) {
                window.refreshCalendarNow();
            }
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
    
    function refreshCalendar() {
        const currentView = document.querySelector('.calendar-view.active')?.id.replace('calendar-', '') || 'week';
        const vetSelect = document.getElementById('vetSelect');
        const selectedVet = vetSelect ? vetSelect.value : 'all';
        
        fetch(`/PETVET/api/appointments/get-appointments.php?view=${currentView}&vet=${encodeURIComponent(selectedVet)}`, {
            method: 'GET',
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.timestamp > lastUpdateTimestamp) {
                lastUpdateTimestamp = data.timestamp;
                updateCalendarUI(data.appointments, currentView);
            }
        })
        .catch(error => {
            console.error('Calendar refresh error:', error);
        });
    }
    
    function updateCalendarUI(appointments, view) {
        if (view === 'today') {
            updateTodayView(appointments);
        } else if (view === 'week') {
            updateWeekView(appointments);
        } else if (view === 'month') {
            updateMonthView(appointments);
        }
    }
    
    function updateTodayView(appointments) {
        const today = new Date().toISOString().split('T')[0];
        const container = document.querySelector('#calendar-today .today-appointments-container');
        
        if (!container) return;
        
        const todayAppts = appointments[today] || [];
        
        if (todayAppts.length === 0) {
            container.innerHTML = '<div style="color:#64748b; font-size:15px; text-align:center; margin-top:18px;">No appointments for today.</div>';
        } else {
            container.innerHTML = todayAppts.map(appt => `
                <div class="event"
                     data-id="${appt.id}"
                     data-pet="${escapeHtml(appt.pet)}"
                     data-animal="${escapeHtml(appt.animal)}"
                     data-client="${escapeHtml(appt.client)}"
                     data-vet="${escapeHtml(appt.vet)}"
                     data-type="${escapeHtml(appt.type)}"
                     data-phone="${escapeHtml(appt.client_phone || 'N/A')}"
                     data-date="${today}"
                     data-time="${appt.time}"
                     onclick="openDetailsFromEl(this)">
                    <span class="evt-time">${formatTime(appt.time)}</span>
                    <span class="evt-client">${escapeHtml(appt.client)}</span>
                    <span class="evt-vet">${escapeHtml(appt.vet)}</span>
                </div>
            `).join('');
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