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
    // Fetch breeding pets from server
    fetch('/PETVET/api/breeder/manage-requests.php?action=get_active_pets')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const select = document.getElementById('selectBreedingPet');
                select.innerHTML = '<option value="">Choose a pet...</option>';
                
                if (data.pets && data.pets.length > 0) {
                    data.pets.forEach(pet => {
                        const option = document.createElement('option');
                        option.value = pet.id;
                        option.textContent = `${pet.name} (${pet.breed})`;
                        select.appendChild(option);
                    });
                } else {
                    select.innerHTML = '<option value="">No active breeding pets available</option>';
                }
            } else {
                console.error('Failed to load breeding pets:', data.message);
                alert('Failed to load breeding pets. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error loading breeding pets:', error);
            alert('Error loading breeding pets: ' + error.message);
        });
}

function confirmAcceptRequest() {
    const breedingPetId = document.getElementById('selectBreedingPet').value;
    
    if (!breedingPetId) {
        alert('Please select a breeding pet');
        return;
    }
    
    // Make API call to accept the request
    const formData = new FormData();
    formData.append('action', 'accept');
    formData.append('request_id', currentRequestId);
    formData.append('breeder_pet_id', breedingPetId);
    
    fetch('/PETVET/api/breeder/manage-requests.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            closeAcceptModal();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while accepting the request');
    });
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
    
    // Make API call to decline the request
    const formData = new FormData();
    formData.append('action', 'decline');
    formData.append('request_id', currentRequestId);
    formData.append('reason', reason);
    
    fetch('/PETVET/api/breeder/manage-requests.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            closeDeclineModal();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while declining the request');
    });
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
    
    // Make API call to mark as complete
    const formData = new FormData();
    formData.append('action', 'complete');
    formData.append('request_id', currentRequestId);
    formData.append('final_notes', notes);
    
    fetch('/PETVET/api/breeder/manage-requests.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            closeCompleteModal();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while completing the request');
    });
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
