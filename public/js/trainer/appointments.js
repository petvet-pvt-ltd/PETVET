// Shared Bookings Page JavaScript

// Filter bookings by status
function filterBookings(status) {
    const cards = document.querySelectorAll('.booking-card');
    const filterButtons = document.querySelectorAll('.filter-btn');
    
    // Remove active class from all buttons
    filterButtons.forEach(btn => btn.classList.remove('active'));
    
    // Add active class to clicked button
    const activeButton = document.querySelector(`[data-filter="${status}"]`);
    if (activeButton) {
        activeButton.classList.add('active');
    }
    
    // Show/hide cards based on status
    cards.forEach(card => {
        if (card.dataset.status === status) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Initialize: show pending bookings by default
document.addEventListener('DOMContentLoaded', function() {
    filterBookings('pending');
});

// Contact Modal Functions
function showContactModal(ownerName, phone1, phone2) {
    const modal = document.getElementById('contactModal');
    const modalOwnerName = document.getElementById('modalOwnerName');
    const phoneList = document.getElementById('phoneList');
    
    // Set owner name
    modalOwnerName.textContent = ownerName;
    
    // Clear previous phone numbers
    phoneList.innerHTML = '';
    
    // Add phone 1
    if (phone1) {
        const phoneItem = document.createElement('div');
        phoneItem.className = 'phone-item';
        phoneItem.innerHTML = `
            <div class="phone-number">${phone1}</div>
            <a href="tel:${phone1}" class="call-btn">
                Call
            </a>
        `;
        phoneList.appendChild(phoneItem);
    }
    
    // Add phone 2 if exists
    if (phone2 && phone2.trim() !== '') {
        const phoneItem = document.createElement('div');
        phoneItem.className = 'phone-item';
        phoneItem.innerHTML = `
            <div class="phone-number">${phone2}</div>
            <a href="tel:${phone2}" class="call-btn">
                Call
            </a>
        `;
        phoneList.appendChild(phoneItem);
    }
    
    // Show modal and freeze background
    modal.classList.add('active');
    document.body.classList.add('modal-open');
}

function closeContactModal() {
    const modal = document.getElementById('contactModal');
    modal.classList.remove('active');
    document.body.classList.remove('modal-open');
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('contactModal');
    if (event.target === modal) {
        closeContactModal();
    }
});

// Confirmation Dialog Functions
let pendingAction = null;
let pendingBookingId = null;

function confirmAction(action, petName, bookingId) {
    const confirmModal = document.getElementById('confirmModal');
    const confirmTitle = document.getElementById('confirmTitle');
    const confirmMessage = document.getElementById('confirmMessage');
    const confirmButton = document.getElementById('confirmButton');
    
    pendingAction = action;
    pendingBookingId = bookingId;
    
    // Set modal content based on action
    if (action === 'accept') {
        confirmTitle.textContent = 'Accept Booking?';
        confirmMessage.textContent = `Are you sure you want to accept the booking for ${petName}? This will confirm the booking and notify the owner.`;
        confirmButton.textContent = 'Yes, Accept';
        confirmButton.className = 'confirm-btn confirm-btn-confirm';
    } else if (action === 'decline') {
        confirmTitle.textContent = 'Decline Booking?';
        confirmMessage.textContent = `Are you sure you want to decline the booking for ${petName}? The owner will be notified of your decision.`;
        confirmButton.textContent = 'Yes, Decline';
        confirmButton.className = 'confirm-btn confirm-btn-danger';
    } else if (action === 'complete') {
        confirmTitle.textContent = 'Mark as Complete?';
        confirmMessage.textContent = `Are you sure you want to mark the booking for ${petName} as complete? This action cannot be undone.`;
        confirmButton.textContent = 'Yes, Complete';
        confirmButton.className = 'confirm-btn confirm-btn-confirm';
    }
    
    // Show modal and freeze background
    confirmModal.classList.add('active');
    document.body.classList.add('modal-open');
}

function closeConfirmModal() {
    const confirmModal = document.getElementById('confirmModal');
    confirmModal.classList.remove('active');
    document.body.classList.remove('modal-open');
    pendingAction = null;
    pendingBookingId = null;
}

function executeAction() {
    if (!pendingAction || !pendingBookingId) return;
    
    // Send AJAX request to controller
    const formData = new FormData();
    formData.append('action', pendingAction);
    formData.append('booking_id', pendingBookingId);
    
    fetch('/PETVET/index.php?module=sitter&action=handleBookingAction', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            alert(data.message);
            // Reload page to reflect changes
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    })
    .finally(() => {
        closeConfirmModal();
    });
}

// Close confirm modal when clicking outside
document.addEventListener('click', function(event) {
    const confirmModal = document.getElementById('confirmModal');
    if (event.target === confirmModal) {
        closeConfirmModal();
    }
});

// Complete Session Modal Functions
function showCompleteSessionModal(session) {
    const modal = document.getElementById('completeSessionModal');
    const form = document.getElementById('completeSessionForm');
    
    // Populate session ID
    document.getElementById('sessionId').value = session.session_id;
    
    // Clear form fields
    document.getElementById('sessionNotes').value = '';
    document.getElementById('nextSessionDate').value = '';
    document.getElementById('nextSessionTime').value = '';
    document.getElementById('nextSessionGoals').value = '';
    document.getElementById('markProgramComplete').checked = false;
    
    // Check if this is the last session - suggest marking complete
    if (session.session_count >= session.total_sessions) {
        document.getElementById('markProgramComplete').checked = true;
    }
    
    // Show modal
    modal.style.display = 'flex';
    document.body.classList.add('modal-open');
}

function closeCompleteSessionModal() {
    const modal = document.getElementById('completeSessionModal');
    modal.style.display = 'none';
    document.body.classList.remove('modal-open');
}

// Handle complete session form submission
document.addEventListener('DOMContentLoaded', function() {
    const completeForm = document.getElementById('completeSessionForm');
    if (completeForm) {
        completeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(completeForm);
            const notes = formData.get('notes');
            
            if (!notes || notes.trim() === '') {
                alert('Please enter session notes');
                return;
            }
            
            // Determine action based on checkbox
            const markComplete = document.getElementById('markProgramComplete').checked;
            formData.set('action', markComplete ? 'mark_program_complete' : 'complete');
            
            if (markComplete) {
                formData.set('final_notes', notes);
            }
            
            // Send AJAX request
            fetch('/PETVET/index.php?module=trainer&page=appointments&action=handleTrainingAction', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message || 'Session completed successfully!');
                    closeCompleteSessionModal();
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });
    }
});

// Close complete session modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('completeSessionModal');
    if (event.target === modal) {
        closeCompleteSessionModal();
    }
});

// Session History Modal Functions
function showSessionHistoryModal(sessionId, petName) {
    const modal = document.getElementById('sessionHistoryModal');
    const title = document.getElementById('historyModalTitle');
    const content = document.getElementById('sessionHistoryContent');
    
    // Set title
    title.textContent = `Session History - ${petName}`;
    
    // Show loading state
    content.innerHTML = '<div style="text-align: center; padding: 2rem; color: #6b7280;"><p>Loading session history...</p></div>';
    
    // Show modal
    modal.style.display = 'flex';
    document.body.classList.add('modal-open');
    
    // Fetch session history via AJAX
    const formData = new FormData();
    formData.append('action', 'get_session_history');
    formData.append('session_id', sessionId);
    
    fetch('/PETVET/index.php?module=trainer&page=appointments&action=handleTrainingAction', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.history && data.history.length > 0) {
            // Build session history HTML
            let historyHTML = '';
            data.history.forEach((session, index) => {
                historyHTML += `
                    <div style="margin-bottom: 1.5rem; padding: 1rem; border-left: 4px solid #8b5cf6; background: #f9fafb; border-radius: 8px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                            <h4 style="margin: 0; color: #8b5cf6; font-size: 1rem;">Session ${session.session_number}</h4>
                            <span style="font-size: 0.875rem; color: #6b7280;">${formatDate(session.session_date)}</span>
                        </div>
                        <div style="margin-bottom: 0.5rem;">
                            <strong style="color: #374151;">Notes:</strong>
                            <p style="margin: 0.25rem 0 0 0; color: #6b7280; line-height: 1.6;">${escapeHtml(session.notes)}</p>
                        </div>
                        ${session.goals_for_next ? `
                        <div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid #e5e7eb;">
                            <strong style="color: #374151;">Goals for Next Session:</strong>
                            <p style="margin: 0.25rem 0 0 0; color: #6b7280; line-height: 1.6;">${escapeHtml(session.goals_for_next)}</p>
                        </div>
                        ` : ''}
                    </div>
                `;
            });
            content.innerHTML = historyHTML;
        } else {
            content.innerHTML = '<div style="text-align: center; padding: 2rem; color: #6b7280;"><p>No previous session notes found.</p></div>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        content.innerHTML = '<div style="text-align: center; padding: 2rem; color: #ef4444;"><p>Error loading session history. Please try again.</p></div>';
    });
}

function closeSessionHistoryModal() {
    const modal = document.getElementById('sessionHistoryModal');
    modal.style.display = 'none';
    document.body.classList.remove('modal-open');
}

// Helper function to format date
function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

// Helper function to escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Close session history modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('sessionHistoryModal');
    if (event.target === modal) {
        closeSessionHistoryModal();
    }
});
