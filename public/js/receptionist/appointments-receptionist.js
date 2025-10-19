// Receptionist Appointments - Pending Requests Handler

/**
 * Accept a pending appointment
 */
function acceptAppointment(appointmentId) {
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
  
  // Show custom confirmation popup
  showAcceptConfirmation({
    appointmentId,
    petName,
    owner,
    appointmentType,
    dateTime,
    vet,
    card
  });
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
  const dateTime = details[2].textContent;
  
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
  
  const popup = document.createElement('div');
  popup.className = 'confirmation-popup';
  popup.innerHTML = `
    <div class="confirmation-header accept-header">
      <h3>✓ Accept Appointment</h3>
    </div>
    <div class="confirmation-body">
      <p class="confirmation-message">Are you sure you want to accept this appointment?</p>
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
        <div class="detail-row">
          <span class="detail-label">Veterinarian:</span>
          <span class="detail-value">${data.vet}</span>
        </div>
      </div>
      <p class="confirmation-note">The pet owner will be notified.</p>
    </div>
    <div class="confirmation-actions">
      <button class="btn-cancel" onclick="closeConfirmationPopup()">Cancel</button>
      <button class="btn-confirm-accept" onclick="confirmAccept(${data.appointmentId})">Accept Appointment</button>
    </div>
  `;
  
  overlay.appendChild(popup);
  document.body.appendChild(overlay);
  
  // Store card reference
  window.currentAppointmentCard = data.card;
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
function confirmAccept(appointmentId) {
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
    fetch('/PETVET/api/appointments/approve.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ appointment_id: appointmentId })
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
