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
    
    // Collect form data
    const formData = new FormData(form);
    const petData = {
        id: document.getElementById('petId').value,
        name: document.getElementById('petName').value,
        breed: document.getElementById('petBreed').value,
        gender: document.getElementById('petGender').value,
        dob: document.getElementById('petDob').value,
        description: document.getElementById('petDescription').value,
        is_active: document.getElementById('petActive').checked
    };
    
    // Here you would make an API call to save the pet
    console.log(editingPetId ? 'Updating pet:' : 'Adding new pet:', petData);
    
    // Show success message
    alert(editingPetId ? 'Pet updated successfully!' : 'Pet added successfully!');
    
    // Close modal
    closePetModal();
    
    // Refresh page or update UI
    location.reload();
}

// Toggle Pet Status
function togglePetStatus(petId, isActive) {
    // Here you would make an API call to update the pet status
    console.log('Toggling pet status:', { petId, isActive });
    
    // Update status text
    const row = document.querySelector(`tr[data-pet-id="${petId}"]`);
    if (row) {
        const statusText = row.querySelector('.status-text');
        if (statusText) {
            statusText.textContent = isActive ? 'Active' : 'Inactive';
        }
    }
    
    // Show toast notification
    showToast(`Pet ${isActive ? 'activated' : 'deactivated'} successfully`);
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
    
    // Here you would make an API call to delete the pet
    console.log('Deleting pet:', petId);
    
    // Show success message
    alert('Pet deleted successfully!');
    
    // Close modal
    closeDeleteModal();
    
    // Refresh page or update UI
    location.reload();
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
