// Groomer Packages JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('packageModal');
    const packageForm = document.getElementById('packageForm');
    const addPackageBtn = document.getElementById('addPackageBtn');
    const addFirstPackage = document.getElementById('addFirstPackage');
    const closeModal = document.getElementById('closeModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const toast = document.getElementById('toast');
    const modalTitle = document.getElementById('modalTitle');
    const originalPriceInput = document.getElementById('originalPrice');
    const discountedPriceInput = document.getElementById('discountedPrice');
    const discountPercent = document.getElementById('discountPercent');
    const savings = document.getElementById('savings');
    const serviceSelector = document.getElementById('serviceSelector');
    const includedServicesInput = document.getElementById('includedServices');

    let availableServices = [];
    let selectedServices = [];

    // Fetch available services from the database
    async function fetchServices() {
        try {
            const formData = new FormData();
            formData.append('action', 'list');
            
            const response = await fetch('/PETVET/api/groomer/services.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success && result.services) {
                availableServices = result.services.filter(service => service.available);
                renderServiceSelector();
            } else {
                showEmptyServiceSelector();
            }
        } catch (error) {
            console.error('Error fetching services:', error);
            showEmptyServiceSelector();
        }
    }

    // Render the service selector
    function renderServiceSelector() {
        if (availableServices.length === 0) {
            showEmptyServiceSelector();
            return;
        }

        serviceSelector.innerHTML = availableServices.map(service => `
            <div class="service-item" data-service-id="${service.id}" data-service-price="${service.price}" data-service-name="${service.name}" data-for-dogs="${service.for_dogs}" data-for-cats="${service.for_cats}">
                <input type="checkbox" class="service-checkbox" id="service-${service.id}" value="${service.id}">
                <label for="service-${service.id}" class="service-item-details">
                    <div class="service-item-name">${service.name}</div>
                    <div class="service-item-meta">
                        ${service.duration ? `<span>⏱️ ${service.duration}</span>` : ''}
                        ${service.for_dogs ? '<span>🐕</span>' : ''}
                        ${service.for_cats ? '<span>🐈</span>' : ''}
                    </div>
                </label>
                <div class="service-item-price">LKR ${parseFloat(service.price).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>
            </div>
        `).join('');

        // Add event listeners to service items
        document.querySelectorAll('.service-item').forEach(item => {
            const checkbox = item.querySelector('.service-checkbox');
            
            // Click on item to toggle checkbox
            item.addEventListener('click', function(e) {
                if (e.target !== checkbox) {
                    checkbox.checked = !checkbox.checked;
                    checkbox.dispatchEvent(new Event('change'));
                }
            });
            
            // Handle checkbox change
            checkbox.addEventListener('change', function(e) {
                e.stopPropagation();
                
                // If checking a checkbox, validate pet type compatibility
                if (this.checked) {
                    const item = this.closest('.service-item');
                    const forDogs = item.dataset.forDogs === 'true';
                    const forCats = item.dataset.forCats === 'true';
                    
                    // Check if there are already selected services
                    const alreadySelected = document.querySelectorAll('.service-checkbox:checked').length > 1;
                    
                    if (alreadySelected && selectedServices.length > 0) {
                        // Check if this service shares at least one pet type with all existing services
                        const hasCommonPetType = selectedServices.every(existingService => {
                            return (existingService.for_dogs && forDogs) || (existingService.for_cats && forCats);
                        });
                        
                        if (!hasCommonPetType) {
                            // Uncheck and show warning
                            this.checked = false;
                            showToast('All services in a package must share at least one common pet type. Cannot mix Dog-only with Cat-only services.');
                            return;
                        }
                    }
                }
                
                updateSelectedServices();
            });
        });
    }

    // Show empty state when no services available
    function showEmptyServiceSelector() {
        serviceSelector.innerHTML = `
            <div class="service-selector-empty">
                <p>You haven't added any services yet.</p>
                <p><a href="/PETVET/index.php?module=groomer&page=services">Add services first</a> to create packages.</p>
            </div>
        `;
    }

    // Update selected services and calculate total price
    function updateSelectedServices() {
        selectedServices = [];
        let totalPrice = 0;

        document.querySelectorAll('.service-checkbox:checked').forEach(checkbox => {
            const item = checkbox.closest('.service-item');
            const serviceId = item.dataset.serviceId;
            const serviceName = item.dataset.serviceName;
            const servicePrice = parseFloat(item.dataset.servicePrice);
            const forDogs = item.dataset.forDogs === 'true';
            const forCats = item.dataset.forCats === 'true';
            
            selectedServices.push({
                id: serviceId,
                name: serviceName,
                price: servicePrice,
                for_dogs: forDogs,
                for_cats: forCats
            });
            
            totalPrice += servicePrice;
            
            // Add visual indication
            item.classList.add('selected');
        });

        // Remove visual indication from unchecked items
        document.querySelectorAll('.service-checkbox:not(:checked)').forEach(checkbox => {
            checkbox.closest('.service-item').classList.remove('selected');
        });

        // Update hidden input with service names (comma-separated)
        const serviceNames = selectedServices.map(s => s.name).join(', ');
        includedServicesInput.value = serviceNames;

        // Update original price (readonly)
        originalPriceInput.value = totalPrice.toFixed(2);

        // Auto-select animal types based on selected services
        updateAnimalTypes();

        // Recalculate discount
        calculateDiscount();

        // Update form validity
        if (selectedServices.length > 0) {
            includedServicesInput.setCustomValidity('');
        } else {
            includedServicesInput.setCustomValidity('Please select at least one service');
        }
    }
    
    // Auto-select animal types based on selected services
    function updateAnimalTypes() {
        if (selectedServices.length === 0) {
            // No services selected, don't change anything
            return;
        }
        
        // Determine the common pet types across all selected services
        let allForDogs = true;
        let allForCats = true;
        
        selectedServices.forEach(service => {
            if (!service.for_dogs) allForDogs = false;
            if (!service.for_cats) allForCats = false;
        });
        
        // Update checkboxes
        const forDogsCheckbox = document.getElementById('forDogs');
        const forCatsCheckbox = document.getElementById('forCats');
        
        if (forDogsCheckbox) forDogsCheckbox.checked = allForDogs;
        if (forCatsCheckbox) forCatsCheckbox.checked = allForCats;
    }

    // Calculate discount percentage
    function calculateDiscount() {
        const original = parseFloat(originalPriceInput.value) || 0;
        const discounted = parseFloat(discountedPriceInput.value) || 0;
        
        if (original > 0 && discounted > 0 && discounted < original) {
            const discount = ((original - discounted) / original) * 100;
            const savingsAmount = original - discounted;
            
            discountPercent.textContent = `Discount: ${discount.toFixed(1)}%`;
            savings.textContent = `You save: LKR ${savingsAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        } else {
            discountPercent.textContent = 'Discount: 0%';
            savings.textContent = 'You save: LKR 0.00';
        }
    }

    if (originalPriceInput && discountedPriceInput) {
        discountedPriceInput.addEventListener('input', calculateDiscount);
    }

    // Open modal for adding new package
    async function openAddModal() {
        modalTitle.textContent = 'Add New Package';
        packageForm.reset();
        document.getElementById('packageId').value = '';
        selectedServices = [];
        
        // Fetch and render services
        await fetchServices();
        
        // Reset all checkboxes
        document.querySelectorAll('.service-checkbox').forEach(cb => cb.checked = false);
        document.querySelectorAll('.service-item').forEach(item => item.classList.remove('selected'));
        
        calculateDiscount();
        modal.classList.add('active');
    }

    if (addPackageBtn) {
        addPackageBtn.addEventListener('click', openAddModal);
    }

    if (addFirstPackage) {
        addFirstPackage.addEventListener('click', openAddModal);
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

    // Edit package - using event delegation
    document.addEventListener('click', async function(e) {
        if (e.target.closest('.btn-icon.edit')) {
            const btn = e.target.closest('.btn-icon.edit');
            const card = btn.closest('.package-card');
            const packageId = card.dataset.packageId;
            
            modalTitle.textContent = 'Edit Package';
            document.getElementById('packageId').value = packageId;
            
            // Get existing data from the card
            const packageName = card.querySelector('.package-name').textContent.trim();
            const packageDescription = card.querySelector('.package-description').textContent.trim();
            
            // Get included services from list items
            const serviceItems = card.querySelectorAll('.included-services li');
            const servicesArray = Array.from(serviceItems).map(li => li.textContent.replace('✓', '').trim());
            
            // Get duration
            const metaValue = card.querySelector('.meta-value');
            const packageDuration = metaValue ? metaValue.textContent.trim() : '';
            
            // Get prices
            const originalPriceElement = card.querySelector('.original-price .value.crossed');
            const discountedPriceElement = card.querySelector('.discounted-price .value');
            const originalPriceText = originalPriceElement ? originalPriceElement.textContent.replace('LKR', '').replace(',', '').trim() : '';
            const discountedPriceText = discountedPriceElement ? discountedPriceElement.textContent.replace('LKR', '').replace(',', '').trim() : '';
            
            // Check pet type badges
            const hasDogs = card.querySelector('.badge.dog') !== null;
            const hasCats = card.querySelector('.badge.cat') !== null;
            
            // Fetch services first
            await fetchServices();
            
            // Populate form fields
            document.getElementById('packageName').value = packageName;
            document.getElementById('packageDescription').value = packageDescription;
            document.getElementById('packageDuration').value = packageDuration;
            document.getElementById('discountedPrice').value = discountedPriceText;
            
            // Set pet type checkboxes
            const forDogsCheckbox = document.getElementById('forDogs');
            const forCatsCheckbox = document.getElementById('forCats');
            if (forDogsCheckbox) forDogsCheckbox.checked = hasDogs;
            if (forCatsCheckbox) forCatsCheckbox.checked = hasCats;
            
            // Select the services that are in this package
            document.querySelectorAll('.service-checkbox').forEach(checkbox => {
                const item = checkbox.closest('.service-item');
                const serviceName = item.dataset.serviceName;
                
                // Check if this service is in the package
                if (servicesArray.includes(serviceName)) {
                    checkbox.checked = true;
                    item.classList.add('selected');
                }
            });
            
            // Update selected services and calculate price
            updateSelectedServices();
            
            // Recalculate discount display
            calculateDiscount();
            
            modal.classList.add('active');
        }
    });

    // Delete package - using event delegation with custom modal
    document.addEventListener('click', async function(e) {
        if (e.target.closest('.btn-icon.delete')) {
            const btn = e.target.closest('.btn-icon.delete');
            const card = btn.closest('.package-card');
            const packageName = card.querySelector('.package-name').textContent.trim();
            
            // Use custom confirmation modal instead of alert
            const confirmed = await ConfirmModal.show({
                title: 'Delete Package?',
                message: `Are you sure you want to delete "${packageName}"? This action cannot be undone.`,
                type: 'danger',
                confirmText: 'Delete',
                cancelText: 'Cancel'
            });
            
            if (confirmed) {
                const packageId = card.dataset.packageId;
                
                // Send delete request to server
                try {
                    const formData = new FormData();
                    formData.append('action', 'delete');
                    formData.append('package_id', packageId);
                    
                    const response = await fetch('/PETVET/api/groomer/packages.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        card.style.opacity = '0';
                        setTimeout(() => {
                            card.remove();
                            
                            // Check if no more packages, show empty state
                            const packagesGrid = document.querySelector('.packages-grid');
                            const remainingCards = packagesGrid.querySelectorAll('.package-card');
                            if (remainingCards.length === 0) {
                                packagesGrid.innerHTML = `
                                    <div class="empty-state">
                                        <div class="empty-icon">📦</div>
                                        <h3>No Packages Yet</h3>
                                        <p>Create combo packages to offer better value to your clients</p>
                                        <button class="btn primary" id="addFirstPackage">Create Your First Package</button>
                                    </div>
                                `;
                            }
                            
                            showToast(result.message);
                        }, 300);
                    } else {
                        showToast(result.message || 'Failed to delete package');
                    }
                } catch (error) {
                    console.error('Error deleting package:', error);
                    showToast('An error occurred while deleting the package');
                }
            }
        }
    });

    // Toggle package availability
    document.addEventListener('change', async function(e) {
        if (e.target.matches('.package-footer .toggle-switch input')) {
            const toggle = e.target;
            const card = toggle.closest('.package-card');
            const packageId = card.dataset.packageId;
            const isAvailable = toggle.checked;
            
            try {
                const formData = new FormData();
                formData.append('action', 'toggle_availability');
                formData.append('package_id', packageId);
                
                const response = await fetch('/PETVET/api/groomer/packages.php', {
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
    packageForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Validate that at least one service is selected
        if (selectedServices.length === 0) {
            showToast('Please select at least one service for this package');
            return;
        }
        
        const formData = new FormData(packageForm);
        const packageId = document.getElementById('packageId').value;
        const action = packageId ? 'update' : 'add';
        
        formData.append('action', action);
        if (packageId) {
            formData.append('package_id', packageId);
        }

        // Add service IDs as comma-separated string
        const serviceIds = selectedServices.map(s => s.id).join(',');
        formData.append('service_ids', serviceIds);

        // Validate that discounted price is less than original
        const original = parseFloat(originalPriceInput.value);
        const discounted = parseFloat(discountedPriceInput.value);
        
        if (!discounted || discounted <= 0) {
            showToast('Please enter a valid package price!');
            return;
        }
        
        if (discounted >= original) {
            showToast('Package price must be less than regular price!');
            return;
        }

        // Disable submit button to prevent double submission
        const saveBtn = document.getElementById('saveBtn');
        const originalText = saveBtn.textContent;
        saveBtn.disabled = true;
        saveBtn.textContent = 'Saving...';

        try {
            const response = await fetch('/PETVET/api/groomer/packages.php', {
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
                showToast(result.message || 'Failed to save package');
                saveBtn.disabled = false;
                saveBtn.textContent = originalText;
            }
        } catch (error) {
            console.error('Error saving package:', error);
            showToast('An error occurred while saving the package');
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
});
