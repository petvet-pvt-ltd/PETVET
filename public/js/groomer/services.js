// Groomer Services JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('serviceModal');
    const serviceForm = document.getElementById('serviceForm');
    const addServiceBtn = document.getElementById('addServiceBtn');
    const addFirstService = document.getElementById('addFirstService');
    const closeModal = document.getElementById('closeModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const toast = document.getElementById('toast');
    const modalTitle = document.getElementById('modalTitle');

    // Open modal for adding new service
    function openAddModal() {
        modalTitle.textContent = 'Add New Service';
        serviceForm.reset();
        document.getElementById('serviceId').value = '';
        modal.classList.add('active');
    }

    if (addServiceBtn) {
        addServiceBtn.addEventListener('click', openAddModal);
    }

    if (addFirstService) {
        addFirstService.addEventListener('click', openAddModal);
    }

    // Close modal
    function closeModalHandler() {
        modal.classList.remove('active');
    }

    if (closeModal) {
        closeModal.addEventListener('click', closeModalHandler);
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', closeModalHandler);
    }

    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModalHandler();
        }
    });

    // Edit service - using event delegation for better compatibility
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-icon.edit')) {
            const btn = e.target.closest('.btn-icon.edit');
            const card = btn.closest('.service-card');
            const serviceId = card.dataset.serviceId;
            
            modalTitle.textContent = 'Edit Service';
            document.getElementById('serviceId').value = serviceId;
            
            // Get existing data from the card
            const serviceName = card.querySelector('.service-name').textContent.trim();
            const serviceDescription = card.querySelector('.service-description').textContent.trim();
            const priceElement = card.querySelector('.meta-value.price');
            const servicePrice = priceElement ? priceElement.textContent.replace('LKR', '').replace(',', '').trim() : '';
            const metaItems = card.querySelectorAll('.meta-item');
            let serviceDuration = '';
            
            // Get duration from meta items
            metaItems.forEach(item => {
                const label = item.querySelector('.meta-label');
                if (label && label.textContent.trim() === 'Duration:') {
                    const valueElement = item.querySelector('.meta-value');
                    serviceDuration = valueElement ? valueElement.textContent.trim() : '';
                }
            });
            
            // Check pet type badges
            const hasDogs = card.querySelector('.badge.dog') !== null;
            const hasCats = card.querySelector('.badge.cat') !== null;
            
            // Populate form fields
            document.getElementById('serviceName').value = serviceName;
            document.getElementById('serviceDescription').value = serviceDescription;
            document.getElementById('servicePrice').value = servicePrice;
            document.getElementById('serviceDuration').value = serviceDuration;
            
            // Set pet type checkboxes
            const forDogsCheckbox = document.getElementById('forDogs');
            const forCatsCheckbox = document.getElementById('forCats');
            if (forDogsCheckbox) forDogsCheckbox.checked = hasDogs;
            if (forCatsCheckbox) forCatsCheckbox.checked = hasCats;
            
            modal.classList.add('active');
        }
    });

    // Delete service - using event delegation with custom modal
    document.addEventListener('click', async function(e) {
        if (e.target.closest('.btn-icon.delete')) {
            const btn = e.target.closest('.btn-icon.delete');
            const card = btn.closest('.service-card');
            const serviceName = card.querySelector('.service-name').textContent.trim();
            
            // Use custom confirmation modal instead of alert
            const confirmed = await ConfirmModal.show({
                title: 'Delete Service?',
                message: `Are you sure you want to delete "${serviceName}"? This action cannot be undone.`,
                type: 'danger',
                confirmText: 'Delete',
                cancelText: 'Cancel'
            });
            
            if (confirmed) {
                const serviceId = card.dataset.serviceId;
                
                // Send delete request to server
                try {
                    const formData = new FormData();
                    formData.append('action', 'delete');
                    formData.append('service_id', serviceId);
                    
                    const response = await fetch('/PETVET/api/groomer/services.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        card.style.opacity = '0';
                        setTimeout(() => {
                            card.remove();
                            
                            // Check if no more services, show empty state
                            const servicesGrid = document.querySelector('.services-grid');
                            const remainingCards = servicesGrid.querySelectorAll('.service-card');
                            if (remainingCards.length === 0) {
                                servicesGrid.innerHTML = `
                                    <div class="empty-state">
                                        <div class="empty-icon">✂️</div>
                                        <h3>No Services Yet</h3>
                                        <p>Start adding your grooming services to attract more clients</p>
                                        <button class="btn primary" id="addFirstService">Add Your First Service</button>
                                    </div>
                                `;
                            }
                            
                            showToast(result.message);
                        }, 300);
                    } else {
                        showToast(result.message || 'Failed to delete service');
                    }
                } catch (error) {
                    console.error('Error deleting service:', error);
                    showToast('An error occurred while deleting the service');
                }
            }
        }
    });

    // Toggle service availability
    document.addEventListener('change', async function(e) {
        if (e.target.matches('.service-footer .toggle-switch input')) {
            const toggle = e.target;
            const card = toggle.closest('.service-card');
            const serviceId = card.dataset.serviceId;
            const isAvailable = toggle.checked;
            
            try {
                const formData = new FormData();
                formData.append('action', 'toggle_availability');
                formData.append('service_id', serviceId);
                
                const response = await fetch('/PETVET/api/groomer/services.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    if (result.available) {
                        card.classList.remove('unavailable');
                        toggle.nextElementSibling.nextElementSibling.textContent = 'Available';
                    } else {
                        card.classList.add('unavailable');
                        toggle.nextElementSibling.nextElementSibling.textContent = 'Unavailable';
                    }
                    
                    showToast(result.message);
                } else {
                    // Revert toggle state on error
                    toggle.checked = !isAvailable;
                    showToast(result.message || 'Failed to update availability');
                }
            } catch (error) {
                console.error('Error toggling availability:', error);
                // Revert toggle state on error
                toggle.checked = !isAvailable;
                showToast('An error occurred while updating availability');
            }
        }
    });

    // Form submission
    serviceForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(serviceForm);
        const serviceId = document.getElementById('serviceId').value;
        const action = serviceId ? 'update' : 'add';
        
        formData.append('action', action);
        if (serviceId) {
            formData.append('service_id', serviceId);
        }

        // Disable submit button to prevent double submission
        const saveBtn = document.getElementById('saveBtn');
        const originalText = saveBtn.textContent;
        saveBtn.disabled = true;
        saveBtn.textContent = 'Saving...';

        try {
            const response = await fetch('/PETVET/api/groomer/services.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showToast(result.message);
                closeModalHandler();
                
                // Reload page to show updated data
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showToast(result.message || 'Failed to save service');
                saveBtn.disabled = false;
                saveBtn.textContent = originalText;
            }
        } catch (error) {
            console.error('Error saving service:', error);
            showToast('An error occurred while saving the service');
            saveBtn.disabled = false;
            saveBtn.textContent = originalText;
        }
    });

    // Pet type toggle styling
    document.querySelectorAll('.pet-toggle input').forEach(input => {
        input.addEventListener('change', function() {
            if (this.checked) {
                this.nextElementSibling.style.transform = 'scale(1.05)';
                setTimeout(() => {
                    this.nextElementSibling.style.transform = 'scale(1)';
                }, 200);
            }
        });
    });

    // Toast notification
    function showToast(message) {
        toast.textContent = message;
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    // File upload button handlers
    document.querySelectorAll('button[data-for]').forEach(btn => {
        btn.addEventListener('click', function() {
            const inputId = this.dataset.for;
            document.getElementById(inputId).click();
        });
    });
});
