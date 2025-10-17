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

    // Calculate discount percentage
    function calculateDiscount() {
        const original = parseFloat(originalPriceInput.value) || 0;
        const discounted = parseFloat(discountedPriceInput.value) || 0;
        
        if (original > 0 && discounted > 0 && discounted < original) {
            const discount = ((original - discounted) / original) * 100;
            const savingsAmount = original - discounted;
            
            discountPercent.textContent = `Discount: ${discount.toFixed(1)}%`;
            savings.textContent = `You save: $${savingsAmount.toFixed(2)}`;
        } else {
            discountPercent.textContent = 'Discount: 0%';
            savings.textContent = 'You save: $0.00';
        }
    }

    if (originalPriceInput && discountedPriceInput) {
        originalPriceInput.addEventListener('input', calculateDiscount);
        discountedPriceInput.addEventListener('input', calculateDiscount);
    }

    // Open modal for adding new package
    function openAddModal() {
        modalTitle.textContent = 'Add New Package';
        packageForm.reset();
        document.getElementById('packageId').value = '';
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
    document.addEventListener('click', function(e) {
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
            const includedServices = servicesArray.join(', ');
            
            // Get duration
            const metaValue = card.querySelector('.meta-value');
            const packageDuration = metaValue ? metaValue.textContent.trim() : '';
            
            // Get prices
            const originalPriceElement = card.querySelector('.original-price .value.crossed');
            const discountedPriceElement = card.querySelector('.discounted-price .value');
            const originalPriceText = originalPriceElement ? originalPriceElement.textContent.replace('$', '').replace(',', '').trim() : '';
            const discountedPriceText = discountedPriceElement ? discountedPriceElement.textContent.replace('$', '').replace(',', '').trim() : '';
            
            // Check pet type badges
            const hasDogs = card.querySelector('.badge.dog') !== null;
            const hasCats = card.querySelector('.badge.cat') !== null;
            
            // Populate form fields
            document.getElementById('packageName').value = packageName;
            document.getElementById('packageDescription').value = packageDescription;
            document.getElementById('includedServices').value = includedServices;
            document.getElementById('packageDuration').value = packageDuration;
            document.getElementById('originalPrice').value = originalPriceText;
            document.getElementById('discountedPrice').value = discountedPriceText;
            
            // Set pet type checkboxes
            const forDogsCheckbox = document.getElementById('forDogs');
            const forCatsCheckbox = document.getElementById('forCats');
            if (forDogsCheckbox) forDogsCheckbox.checked = hasDogs;
            if (forCatsCheckbox) forCatsCheckbox.checked = hasCats;
            
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
                
                // In real app, send delete request to server
                card.style.opacity = '0';
                setTimeout(() => {
                    card.remove();
                    showToast('Package deleted successfully');
                }, 300);
            }
        }
    });

    // Toggle package availability
    document.querySelectorAll('.package-footer .toggle-switch input').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const card = this.closest('.package-card');
            const packageId = card.dataset.packageId;
            const isAvailable = this.checked;
            
            if (isAvailable) {
                card.classList.remove('unavailable');
                this.nextElementSibling.nextElementSibling.textContent = 'Available';
            } else {
                card.classList.add('unavailable');
                this.nextElementSibling.nextElementSibling.textContent = 'Unavailable';
            }
            
            // In real app, send update to server
            showToast(isAvailable ? 'Package is now available' : 'Package is now unavailable');
        });
    });

    // Form submission
    packageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(packageForm);
        const packageId = document.getElementById('packageId').value;
        const action = packageId ? 'update' : 'add';
        
        formData.append('action', action);
        if (packageId) {
            formData.append('package_id', packageId);
        }

        // Validate that discounted price is less than original
        const original = parseFloat(originalPriceInput.value);
        const discounted = parseFloat(discountedPriceInput.value);
        
        if (discounted >= original) {
            showToast('Package price must be less than regular price!');
            return;
        }

        // In real app, send to server
        console.log('Form data:', Object.fromEntries(formData));
        
        showToast(packageId ? 'Package updated successfully' : 'Package added successfully');
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
});
