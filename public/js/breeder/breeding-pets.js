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
    document.getElementById('petAge').value = '';
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
    
    // Set age from database
    document.getElementById('petAge').value = pet.age || '';
    
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

// Calculate Age from Date of Birth
function calculateAge() {
    const dobInput = document.getElementById('petDob').value;
    const ageInput = document.getElementById('petAge');
    
    if (!dobInput) {
        ageInput.value = '';
        return;
    }
    
    const dob = new Date(dobInput);
    const today = new Date();
    let age = today.getFullYear() - dob.getFullYear();
    
    // Adjust if birthday hasn't occurred this year
    if (today.getMonth() < dob.getMonth() || 
        (today.getMonth() === dob.getMonth() && today.getDate() < dob.getDate())) {
        age--;
    }
    
    ageInput.value = age + (age === 1 ? ' year' : ' years');
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
    
    // Add event listener for DOB field
    const dobField = document.getElementById('petDob');
    if (dobField) {
        dobField.addEventListener('change', function() {
            calculateAge();
        });
    }
    
    // Initialize filters
    initializeFilters();
});

// Initialize filter dropdowns with unique values
function initializeFilters() {
    // Populate breed filter with unique breeds
    const breeds = [...new Set(breedingPetsData.map(pet => pet.breed))].sort();
    
    // Populate gender - already static (Male/Female)
    // Populate age range - already static
    // Populate status - already static (Active/Inactive)
}

// Apply filters to the table
function applyFilters() {
    const breedFilter = document.getElementById('filterBreed')?.value.toLowerCase() || '';
    const ageFilter = document.getElementById('filterAge')?.value || '';
    const statusFilter = document.getElementById('filterStatus')?.value || '';
    
    const tableRows = document.querySelectorAll('.pets-table tbody tr[data-pet-id]');
    let visibleCount = 0;
    
    tableRows.forEach(row => {
        const petId = row.getAttribute('data-pet-id');
        const pet = breedingPetsData.find(p => p.id == petId);
        
        if (!pet) return;
        
        let matches = true;
        
        // Check breed filter
        if (breedFilter && !pet.breed.toLowerCase().includes(breedFilter)) {
            matches = false;
        }
        
       
        
        // Check age filter
        if (ageFilter) {
            const ageMatch = applyAgeFilter(pet.age, ageFilter);
            if (!ageMatch) {
                matches = false;
            }
        }
        
        // Check status filter
        if (statusFilter) {
            const petStatus = pet.is_active ? 'Active' : 'Inactive';
            if (petStatus !== statusFilter) {
                matches = false;
            }
        }
        
        // Show or hide row
        if (matches) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Show "no data" message if no pets match filters
    const noDataRow = document.querySelector('.pets-table tbody tr:not([data-pet-id])');
    if (visibleCount === 0 && noDataRow) {
        noDataRow.style.display = '';
    } else if (noDataRow) {
        noDataRow.style.display = 'none';
    }
}

// Helper function to check age range
function applyAgeFilter(ageText, ageRange) {
    // Extract age number from "X year(s)" format
    const ageMatch = ageText?.match(/\d+/);
    if (!ageMatch) return false;
    
    const age = parseInt(ageMatch[0]);
    
    switch(ageRange) {
        case '0-2':
            return age >= 0 && age <= 2;
        case '3-5':
            return age >= 3 && age <= 5;
        case '6+':
            return age >= 6;
        default:
            return true;
    }
}

// Reset all filters
function resetFilters() {
    document.getElementById('filterBreed').value = '';
    document.getElementById('filterAge').value = '';
    document.getElementById('filterStatus').value = '';
    applyFilters();
}
