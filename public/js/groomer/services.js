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
            const servicePrice = priceElement ? priceElement.textContent.replace('$', '').replace(',', '').trim() : '';
            const durationElement = card.querySelector('.meta-value');
            const serviceDuration = durationElement ? durationElement.textContent.trim() : '';
            
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
                
                // In real app, send delete request to server
                card.style.opacity = '0';
                setTimeout(() => {
                    card.remove();
                    showToast('Service deleted successfully');
                }, 300);
            }
        }
    });

    // Toggle service availability
    document.querySelectorAll('.service-footer .toggle-switch input').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const card = this.closest('.service-card');
            const serviceId = card.dataset.serviceId;
            const isAvailable = this.checked;
            
            if (isAvailable) {
                card.classList.remove('unavailable');
                this.nextElementSibling.nextElementSibling.textContent = 'Available';
            } else {
                card.classList.add('unavailable');
                this.nextElementSibling.nextElementSibling.textContent = 'Unavailable';
            }
            
            // In real app, send update to server
            showToast(isAvailable ? 'Service is now available' : 'Service is now unavailable');
        });
    });

    // Form submission
    serviceForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(serviceForm);
        const serviceId = document.getElementById('serviceId').value;
        const action = serviceId ? 'update' : 'add';
        
        formData.append('action', action);
        if (serviceId) {
            formData.append('service_id', serviceId);
        }

        // In real app, send to server
        console.log('Form data:', Object.fromEntries(formData));
        
        showToast(serviceId ? 'Service updated successfully' : 'Service added successfully');
        closeModalHandler();
        
        // Reload page or update UI
        setTimeout(() => {
            // location.reload();
        }, 1500);
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
