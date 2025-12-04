// Receptionist Appointments - Pending Requests Handler

/**
 * Accept a pending appointment
 */
async function acceptAppointment(appointmentId) {
  const card = document.querySelector(`.pending-card[data-id="${appointmentId}"]`);
  
  if (!card) {
    console.error('Card not found for appointment ID:', appointmentId);
    return;
  }
  
  // Get appointment details for confirmation
  const petName = card.querySelector('.pet-name').textContent;
  const details = card.querySelectorAll('.pending-detail .value');
  const owner = details[0].textContent;
  const appointmentType = details[1].textContent;
  const dateTime = details[2].textContent;
  const vet = details[3].textContent;
  
  // Get clinic_id, date, and time from data attributes
  const clinicId = card.dataset.clinicId;
  const appointmentDate = card.dataset.appointmentDate;
  const appointmentTime = card.dataset.appointmentTime;
  
  // Fetch available vets for this time slot
  try {
    const response = await fetch(`/PETVET/api/appointments/get-available-vets.php?clinic_id=${clinicId}&appointment_date=${appointmentDate}&appointment_time=${appointmentTime}&exclude_appointment_id=${appointmentId}`);
    const data = await response.json();
    
    if (data.success) {
      // Show custom confirmation popup with available vets
      showAcceptConfirmation({
        appointmentId,
        petName,
        owner,
        appointmentType,
        dateTime,
        vet,
        card,
        availableVets: data.available_vets
      });
    } else {
      alert('Error loading available vets: ' + (data.error || 'Unknown error'));
    }
  } catch (error) {
    console.error('Error fetching available vets:', error);
    alert('Error loading available vets. Please try again.');
  }
}

/**
 * Decline a pending appointment
 */
function declineAppointment(appointmentId) {
  const card = document.querySelector(`.pending-card[data-id="${appointmentId}"]`);
  
  if (!card) {
    console.error('Card not found for appointment ID:', appointmentId);
    return;
  }
  
  // Get appointment details
  const petName = card.querySelector('.pet-name').textContent;
  const details = card.querySelectorAll('.pending-detail .value');
  const owner = details[0].textContent;
  const dateTime = details[3].textContent;
  
  // Show custom decline popup
  showDeclineConfirmation({
    appointmentId,
    petName,
    owner,
    dateTime,
    card
  });
}

/**
 * Show accept confirmation popup
 */
function showAcceptConfirmation(data) {
  const overlay = createOverlay();
  
  const isAnyVet = data.vet === 'Any Available Vet';
  const availableVets = data.availableVets || [];
  
  let vetDropdownHtml = '';
  if (availableVets.length > 0) {
    vetDropdownHtml = `
      <div class="detail-row" style="flex-direction: column; align-items: flex-start; gap: 8px; margin-top: 16px; padding-top: 16px; border-top: 1px solid #e2e8f0;">
        <label for="acceptVetSelect" style="font-weight: 600; color: #334155; font-size: 14px;">
          ${isAnyVet ? 'Assign Veterinarian: *' : 'Veterinarian (can be changed):'}
        </label>
        <select id="acceptVetSelect" style="width: 100%; padding: 8px 12px; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 14px; background: white;" ${isAnyVet ? 'required' : ''}>
          ${isAnyVet ? '<option value="">-- Select a Veterinarian --</option>' : ''}
          ${availableVets.map(vet => `<option value="${vet.id}" ${!isAnyVet && vet.name === data.vet ? 'selected' : ''}>${vet.name}</option>`).join('')}
        </select>
        ${availableVets.length === 0 ? '<p style="color: #dc2626; font-size: 13px; margin-top: 4px;">⚠️ All vets are booked at this time</p>' : ''}
      </div>
    `;
  } else {
    vetDropdownHtml = `
      <div class="detail-row" style="flex-direction: column; align-items: flex-start; gap: 8px; margin-top: 16px; padding-top: 16px; border-top: 1px solid #e2e8f0;">
        <p style="color: #dc2626; font-size: 14px; font-weight: 600;">⚠️ No vets available at this time</p>
        <p style="color: #64748b; font-size: 13px;">All veterinarians are already booked for this time slot. Please reschedule or decline this appointment.</p>
      </div>
    `;
  }
  
  const popup = document.createElement('div');
  popup.className = 'confirmation-popup';
  popup.innerHTML = `
    <div class="confirmation-header accept-header">
      <h3>✓ Accept Appointment</h3>
    </div>
    <div class="confirmation-body">
      <p class="confirmation-message">Review the appointment details and ${isAnyVet ? 'assign a veterinarian' : 'confirm or change the veterinarian'}:</p>
      <div class="appointment-details">
        <div class="detail-row">
          <span class="detail-label">Pet:</span>
          <span class="detail-value">${data.petName}</span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Owner:</span>
          <span class="detail-value">${data.owner}</span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Type:</span>
          <span class="detail-value">${data.appointmentType}</span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Date & Time:</span>
          <span class="detail-value">${data.dateTime}</span>
        </div>
        ${vetDropdownHtml}
      </div>
      <p class="confirmation-note" style="margin-top: 16px;">The pet owner will be notified via email.</p>
    </div>
    <div class="confirmation-actions">
      <button class="btn-cancel" onclick="closeConfirmationPopup()">Cancel</button>
      <button class="btn-confirm-accept" onclick="confirmAccept(${data.appointmentId}, ${isAnyVet})">Accept Appointment</button>
    </div>
  `;
  
  overlay.appendChild(popup);
  document.body.appendChild(overlay);
  
  // Store card reference and data
  window.currentAppointmentCard = data.card;
  window.currentAppointmentData = data;
}

/**
 * Show decline confirmation popup
 */
function showDeclineConfirmation(data) {
  const overlay = createOverlay();
  
  const popup = document.createElement('div');
  popup.className = 'confirmation-popup';
  popup.innerHTML = `
    <div class="confirmation-header decline-header">
      <h3>✕ Decline Appointment</h3>
    </div>
    <div class="confirmation-body">
      <p class="confirmation-message">Are you sure you want to decline this appointment?</p>
      <div class="appointment-details">
        <div class="detail-row">
          <span class="detail-label">Pet:</span>
          <span class="detail-value">${data.petName}</span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Owner:</span>
          <span class="detail-value">${data.owner}</span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Date & Time:</span>
          <span class="detail-value">${data.dateTime}</span>
        </div>
      </div>
      <div class="reason-input-group">
        <label for="declineReason">Reason (Optional):</label>
        <textarea id="declineReason" placeholder="Enter reason for declining (optional)..." rows="3"></textarea>
      </div>
      <p class="confirmation-note warning">The pet owner will be notified about the cancellation.</p>
    </div>
    <div class="confirmation-actions">
      <button class="btn-cancel" onclick="closeConfirmationPopup()">Cancel</button>
      <button class="btn-confirm-decline" onclick="confirmDecline(${data.appointmentId})">Decline Appointment</button>
    </div>
  `;
  
  overlay.appendChild(popup);
  document.body.appendChild(overlay);
  
  // Store card reference
  window.currentAppointmentCard = data.card;
}

/**
 * Create overlay element
 */
function createOverlay() {
  const overlay = document.createElement('div');
  overlay.className = 'confirmation-overlay';
  overlay.onclick = function(e) {
    if (e.target === overlay) {
      closeConfirmationPopup();
    }
  };
  return overlay;
}

/**
 * Close confirmation popup
 */
function closeConfirmationPopup() {
  const overlay = document.querySelector('.confirmation-overlay');
  if (overlay) {
    overlay.remove();
  }
  window.currentAppointmentCard = null;
}

/**
 * Confirm accept appointment
 */
function confirmAccept(appointmentId, requiresVet) {
  // Get selected vet if dropdown exists
  const vetSelect = document.getElementById('acceptVetSelect');
  const selectedVet = vetSelect ? vetSelect.value : null;
  
  // Validate vet selection if required
  if (requiresVet && (!selectedVet || selectedVet === '')) {
    alert('Please select a veterinarian before accepting the appointment.');
    return;
  }
  
  const card = window.currentAppointmentCard;
  if (!card) return;
  
  closeConfirmationPopup();
  
  const petName = card.querySelector('.pet-name').textContent;
  
  // Show loading state
  const acceptBtn = card.querySelector('.btn-accept');
  const originalText = acceptBtn.innerHTML;
  acceptBtn.innerHTML = '⏳ Processing...';
  acceptBtn.disabled = true;
  
  setTimeout(() => {
    const requestBody = { appointment_id: appointmentId };
    if (selectedVet) {
      requestBody.vet_id = parseInt(selectedVet);
    }
    
    fetch('/PETVET/api/appointments/approve.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(requestBody)
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Show success animation
        card.style.background = '#dcfce7';
        card.style.borderColor = '#86efac';
        
        setTimeout(() => {
          // Remove card with animation
          card.style.opacity = '0';
          card.style.transform = 'translateX(-100%)';
          
          setTimeout(() => {
            card.remove();
            updatePendingCount();
            showSuccessNotification(`Appointment for ${petName} has been accepted!`);
            
            // Reload page to show appointment in calendar
            setTimeout(() => {
              window.location.reload();
            }, 1000);
          }, 300);
        }, 500);
      } else {
        // Reset button on error
        acceptBtn.innerHTML = originalText;
        acceptBtn.disabled = false;
        showSuccessNotification('Failed to accept appointment. Please try again.', true);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      acceptBtn.innerHTML = originalText;
      acceptBtn.disabled = false;
      showSuccessNotification('An error occurred. Please try again.', true);
    });
  }, 500);
}

/**
 * Confirm decline appointment
 */
function confirmDecline(appointmentId) {
  const card = window.currentAppointmentCard;
  if (!card) return;
  
  const reason = document.getElementById('declineReason').value.trim();
  
  closeConfirmationPopup();
  
  const petName = card.querySelector('.pet-name').textContent;
  
  // Show loading state
  const declineBtn = card.querySelector('.btn-decline');
  const originalText = declineBtn.innerHTML;
  declineBtn.innerHTML = '⏳ Processing...';
  declineBtn.disabled = true;
  
  setTimeout(() => {
    fetch('/PETVET/api/appointments/decline.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ 
        appointment_id: appointmentId,
        reason: reason 
      })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Show decline animation
        card.style.background = '#fee2e2';
        card.style.borderColor = '#fca5a5';
        
        setTimeout(() => {
          // Remove card with animation
          card.style.opacity = '0';
          card.style.transform = 'translateX(-100%)';
          
          setTimeout(() => {
            card.remove();
            updatePendingCount();
            showSuccessNotification(`Appointment for ${petName} has been declined.`);
          }, 300);
        }, 500);
      } else {
        // Reset button on error
        declineBtn.innerHTML = originalText;
        declineBtn.disabled = false;
        showSuccessNotification('Failed to decline appointment. Please try again.', true);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      declineBtn.innerHTML = originalText;
      declineBtn.disabled = false;
      showSuccessNotification('An error occurred. Please try again.', true);
    });
  }, 500);
}/**
 * Update pending count badge
 */
function updatePendingCount() {
  const pendingCards = document.querySelectorAll('.pending-card');
  const countBadge = document.querySelector('.pending-count');
  const count = pendingCards.length;
  
  if (countBadge) {
    countBadge.textContent = count;
  }
  
  // Show empty state if no pending appointments
  if (count === 0) {
    const pendingList = document.querySelector('.pending-list');
    pendingList.innerHTML = `
      <div class="empty-state">
        <div class="empty-icon">📭</div>
        <p>No pending requests</p>
      </div>
    `;
  }
}

/**
 * Show success notification
 */
function showSuccessNotification(message, isError = false) {
  // Create notification element
  const notification = document.createElement('div');
  notification.style.cssText = `
    position: fixed;
    top: 80px;
    right: 20px;
    background: ${isError ? '#ef4444' : '#10b981'};
    color: white;
    padding: 14px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    font-size: 14px;
    font-weight: 600;
    z-index: 9999;
    animation: slideInRight 0.3s ease;
  `;
  notification.textContent = message;
  
  document.body.appendChild(notification);
  
  // Remove after 3 seconds
  setTimeout(() => {
    notification.style.animation = 'slideOutRight 0.3s ease';
    setTimeout(() => {
      notification.remove();
    }, 300);
  }, 3000);
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
  @keyframes slideInRight {
    from {
      transform: translateX(100%);
      opacity: 0;
    }
    to {
      transform: translateX(0);
      opacity: 1;
    }
  }
  
  @keyframes slideOutRight {
    from {
      transform: translateX(0);
      opacity: 1;
    }
    to {
      transform: translateX(100%);
      opacity: 0;
    }
  }
`;
document.head.appendChild(style);
