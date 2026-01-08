// Breeding Pets Management JavaScript

// Global state
let editingPetId = null;
let currentDeletePetId = null;

// Show Add Pet Modal
function showAddPetModal() {
    editingPetId = null;
    document.getElementById('modalTitle').textContent = 'Add New Breeding Pet';
    document.getElementById('petForm').reset();
    document.getElementById('petId').value = '';
    document.getElementById('photoPreview').innerHTML = '<span class="photo-placeholder">📷</span>';
    document.getElementById('petModal').classList.add('active');
}

// Show Edit Pet Modal
function showEditPetModal(petId) {
    editingPetId = petId;
    document.getElementById('modalTitle').textContent = 'Edit Breeding Pet';
    
    // Find pet data
    const pet = breedingPetsData.find(p => p.id == petId);
    if (!pet) return;
    
    // Populate form
    document.getElementById('petId').value = pet.id;
    document.getElementById('petName').value = pet.name;
    document.getElementById('petBreed').value = pet.breed;
    document.getElementById('petGender').value = pet.gender;
    document.getElementById('petDob').value = pet.dob;
    document.getElementById('petDescription').value = pet.description || '';
    document.getElementById('petActive').checked = pet.is_active;
    
    // Show photo if exists
    if (pet.photo) {
        document.getElementById('photoPreview').innerHTML = `<img src="${pet.photo}" alt="Pet Photo">`;
    }
    
    document.getElementById('petModal').classList.add('active');
}

// Close Pet Modal
function closePetModal() {
    document.getElementById('petModal').classList.remove('active');
    document.getElementById('petForm').reset();
    editingPetId = null;
}

// Preview Photo
function previewPhoto(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('photoPreview').innerHTML = `<img src="${e.target.result}" alt="Pet Photo">`;
        };
        reader.readAsDataURL(file);
    }
}

// Save Pet (Add or Edit)
function savePet() {
    const form = document.getElementById('petForm');
    
    // Validate form
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Validate date of birth is not in the future
    const dob = document.getElementById('petDob').value;
    if (dob) {
        const dobDate = new Date(dob);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        if (dobDate > today) {
            alert('Date of birth cannot be in the future');
            return;
        }
    }
    
    // Collect form data
    const formData = new FormData(form);
    formData.append('action', editingPetId ? 'update' : 'add');
    
    // Make API call to save the pet
    fetch('/PETVET/api/breeder/manage-breeding-pets.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        // Check if response is ok
        if (!response.ok) {
            return response.text().then(text => {
                throw new Error(`Server error (${response.status}): ${text}`);
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert(data.message);
            closePetModal();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving the pet: ' + error.message);
    });
}

// Toggle Pet Status
function togglePetStatus(petId, isActive) {
    // Make API call to update the pet status
    const formData = new FormData();
    formData.append('action', 'toggle_status');
    formData.append('pet_id', petId);
    formData.append('is_active', isActive ? 1 : 0);
    
    fetch('/PETVET/api/breeder/manage-breeding-pets.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update status text
            const row = document.querySelector(`tr[data-pet-id="${petId}"]`);
            if (row) {
                const statusText = row.querySelector('.status-text');
                if (statusText) {
                    statusText.textContent = isActive ? 'Active' : 'Inactive';
                }
            }
            showToast(data.message);
        } else {
            alert('Error: ' + data.message);
            // Revert checkbox
            const checkbox = document.querySelector(`tr[data-pet-id="${petId}"] input[type="checkbox"]`);
            if (checkbox) {
                checkbox.checked = !isActive;
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the pet status');
        // Revert checkbox
        const checkbox = document.querySelector(`tr[data-pet-id="${petId}"] input[type="checkbox"]`);
        if (checkbox) {
            checkbox.checked = !isActive;
        }
    });
}

// Show Delete Confirmation
function showDeleteConfirmation(petId, petName) {
    currentDeletePetId = petId;
    document.getElementById('deletePetName').textContent = petName;
    document.getElementById('deletePetId').value = petId;
    document.getElementById('deleteModal').classList.add('active');
}

// Close Delete Modal
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('active');
    currentDeletePetId = null;
}

// Confirm Delete
function confirmDelete() {
    const petId = document.getElementById('deletePetId').value;
    
    // Make API call to delete the pet
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('pet_id', petId);
    
    fetch('/PETVET/api/breeder/manage-breeding-pets.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            closeDeleteModal();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the pet');
    });
}

// Show Toast Notification
function showToast(message) {
    // Create toast element if it doesn't exist
    let toast = document.getElementById('toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'toast';
        toast.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #10b981;
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 10000;
            opacity: 0;
            transition: opacity 0.3s ease;
        `;
        document.body.appendChild(toast);
    }
    
    toast.textContent = message;
    toast.style.opacity = '1';
    
    setTimeout(() => {
        toast.style.opacity = '0';
    }, 3000);
}

// Close modals when clicking outside
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-overlay')) {
        if (e.target.id === 'petModal') closePetModal();
        if (e.target.id === 'deleteModal') closeDeleteModal();
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Breeding Pets page loaded');
    console.log('Total pets:', breedingPetsData.length);
});
