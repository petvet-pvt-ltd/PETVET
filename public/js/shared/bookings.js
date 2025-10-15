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
