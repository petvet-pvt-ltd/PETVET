// Breeder Requests JavaScript

// Filter requests by status
function filterRequests(status) {
    // Update filter buttons
    const filterBtns = document.querySelectorAll('.filter-btn');
    filterBtns.forEach(btn => {
        if (btn.getAttribute('data-filter') === status) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });

    // Show/hide request cards
    const cards = document.querySelectorAll('.booking-card');
    cards.forEach(card => {
        if (card.getAttribute('data-status') === status) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Show Accept Request Modal
let currentRequestId = null;

function showAcceptModal(requestId, petName, ownerName) {
    currentRequestId = requestId;
    document.getElementById('acceptPetName').textContent = petName;
    document.getElementById('acceptOwnerName').textContent = ownerName;
    
    // Load breeding pets into select dropdown
    loadBreedingPets();
    
    document.getElementById('acceptModal').classList.add('active');
}

function closeAcceptModal() {
    document.getElementById('acceptModal').classList.remove('active');
    document.getElementById('selectBreedingPet').value = '';
    currentRequestId = null;
}

function loadBreedingPets() {
    // This would normally fetch from server
    // For now, using mock data
    const select = document.getElementById('selectBreedingPet');
    select.innerHTML = '<option value="">Choose a pet...</option>';
    
    // Mock breeding pets - replace with actual API call
    const mockPets = [
        { id: 1, name: 'Max', breed: 'Golden Retriever' },
        { id: 2, name: 'Daisy', breed: 'Golden Retriever' },
        { id: 3, name: 'Duke', breed: 'German Shepherd' },
        { id: 4, name: 'Bella', breed: 'Labrador Retriever' },
        { id: 5, name: 'Rex', breed: 'Golden Retriever' },
        { id: 6, name: 'Molly', breed: 'Labrador Retriever' }
    ];
    
    mockPets.forEach(pet => {
        const option = document.createElement('option');
        option.value = pet.id;
        option.textContent = `${pet.name} (${pet.breed})`;
        select.appendChild(option);
    });
}

function confirmAcceptRequest() {
    const breedingPetId = document.getElementById('selectBreedingPet').value;
    
    if (!breedingPetId) {
        alert('Please select a breeding pet');
        return;
    }
    
    // Here you would make an API call to accept the request
    console.log('Accepting request:', {
        requestId: currentRequestId,
        breedingPetId
    });
    
    // Show success message
    alert('Request accepted successfully!');
    
    // Close modal
    closeAcceptModal();
    
    // Refresh page or update UI
    location.reload();
}

// Show Decline Request Modal
function showDeclineModal(requestId, petName, ownerName) {
    currentRequestId = requestId;
    document.getElementById('declinePetName').textContent = petName;
    document.getElementById('declineOwnerName').textContent = ownerName;
    document.getElementById('declineModal').classList.add('active');
}

function closeDeclineModal() {
    document.getElementById('declineModal').classList.remove('active');
    document.getElementById('declineReason').value = '';
    currentRequestId = null;
}

function confirmDeclineRequest() {
    const reason = document.getElementById('declineReason').value;
    
    // Here you would make an API call to decline the request
    console.log('Declining request:', {
        requestId: currentRequestId,
        reason
    });
    
    // Show success message
    alert('Request declined successfully!');
    
    // Close modal
    closeDeclineModal();
    
    // Refresh page or update UI
    location.reload();
}

// Show Mark Complete Modal
function showCompleteModal(requestId, petName, ownerName) {
    currentRequestId = requestId;
    document.getElementById('completePetName').textContent = petName;
    document.getElementById('completeOwnerName').textContent = ownerName;
    document.getElementById('completeModal').classList.add('active');
}

function closeCompleteModal() {
    document.getElementById('completeModal').classList.remove('active');
    document.getElementById('completeNotes').value = '';
    currentRequestId = null;
}

function confirmCompleteRequest() {
    const notes = document.getElementById('completeNotes').value;
    
    // Here you would make an API call to mark as complete
    console.log('Marking request as complete:', {
        requestId: currentRequestId,
        notes
    });
    
    // Show success message
    alert('Breeding marked as complete successfully!');
    
    // Close modal
    closeCompleteModal();
    
    // Refresh page or update UI
    location.reload();
}

// Contact Owner Modal
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

// Close modals when clicking outside
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-overlay')) {
        closeAcceptModal();
        closeDeclineModal();
        closeCompleteModal();
        closeContactModal();
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Show pending requests by default
    filterRequests('pending');
});
