/**
 * Clinic Distance Calculator
 * Gets pet owner's location and shows clinics sorted by distance
 */

class ClinicDistanceCalculator {
    constructor() {
        this.petOwnerLocation = null;
        this.clinics = [];
        this.loading = false;
    }

    /**
     * Initialize - request location and load clinics
     */
    async init() {
        // Check if geolocation is supported
        if (!navigator.geolocation) {
            console.warn('Geolocation is not supported by this browser');
            return;
        }

        // Try to get location with user permission
        this.requestLocation();
    }

    /**
     * Request pet owner's current location
     */
    requestLocation() {
        const options = {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 300000 // Cache for 5 minutes
        };

        navigator.geolocation.getCurrentPosition(
            (position) => this.onLocationSuccess(position),
            (error) => this.onLocationError(error),
            options
        );
    }

    /**
     * Handle successful location retrieval
     */
    async onLocationSuccess(position) {
        this.petOwnerLocation = {
            latitude: position.coords.latitude,
            longitude: position.coords.longitude,
            accuracy: position.coords.accuracy
        };

        console.log('Pet owner location:', this.petOwnerLocation);

        // Load clinics sorted by distance
        await this.loadClinicsByDistance();
    }

    /**
     * Handle location retrieval errors
     */
    onLocationError(error) {
        console.warn('Location error:', error.message, 'Code:', error.code);
        console.log('Protocol:', window.location.protocol, 'Hostname:', window.location.hostname);

        let message = '';
        let showRetryButton = false;
        
        switch (error.code) {
            case error.PERMISSION_DENIED:
                if (window.location.protocol !== 'https:' && window.location.hostname !== 'localhost') {
                    message = '⚠️ Location requires HTTPS. Using default clinic order.';
                } else {
                    message = '📍 Location denied. Reset permissions in browser settings or ';
                    showRetryButton = true;
                }
                break;
            case error.POSITION_UNAVAILABLE:
                message = 'Location unavailable. Check device GPS settings.';
                showRetryButton = true;
                break;
            case error.TIMEOUT:
                message = 'Location timed out. ';
                showRetryButton = true;
                break;
            default:
                message = 'Unable to get location. ';
                showRetryButton = true;
        }

        // Show notification with retry option
        this.showLocationNotification(message, showRetryButton);
        
        console.log('Falling back to default clinic order');
    }

    /**
     * Fetch clinics sorted by distance from API
     */
    async loadClinicsByDistance() {
        if (!this.petOwnerLocation) {
            console.warn('No location available');
            return;
        }

        this.loading = true;
        this.showLoadingState();

        try {
            const response = await fetch(
                `/PETVET/api/pet-owner/get-clinics-by-distance.php?latitude=${this.petOwnerLocation.latitude}&longitude=${this.petOwnerLocation.longitude}`
            );

            const data = await response.json();

            if (data.success && data.clinics) {
                this.clinics = data.clinics;
                this.updateClinicSelect();
            } else {
                console.error('Failed to load clinics:', data.error);
            }
        } catch (error) {
            console.error('Error loading clinics by distance:', error);
        } finally {
            this.loading = false;
            this.hideLoadingState();
        }
    }

    /**
     * Update clinic dropdown with distance information
     */
    updateClinicSelect() {
        const clinicSelect = document.getElementById('clinicSelect');
        
        // Skip if using modern clinic selector (it's now a hidden input)
        if (document.getElementById('clinicSelectorContainer')) {
            return;
        }
        
        if (!clinicSelect || this.clinics.length === 0) {
            return;
        }

        // Clear existing options except the first placeholder
        while (clinicSelect.options.length > 1) {
            clinicSelect.remove(1);
        }

        // Add clinics with distance
        this.clinics.forEach(clinic => {
            const option = document.createElement('option');
            option.value = clinic.id;
            option.textContent = `${clinic.clinic_name} - ${clinic.distance_formatted} away`;
            
            // Store clinic data in option
            option.dataset.address = clinic.clinic_address;
            option.dataset.phone = clinic.clinic_phone || '';
            option.dataset.distance = clinic.distance_km;
            option.dataset.duration = clinic.duration_min || '';
            option.dataset.latitude = clinic.latitude;
            option.dataset.longitude = clinic.longitude;

            clinicSelect.appendChild(option);
        });

        // Add visual indicator that clinics are sorted by distance
        this.addDistanceIndicator();
    }

    /**
     * Add indicator showing clinics are sorted by distance
     */
    addDistanceIndicator() {
        const clinicSelect = document.getElementById('clinicSelect');
        if (!clinicSelect) return;

        // Remove existing indicator
        const existingIndicator = document.querySelector('.distance-indicator');
        if (existingIndicator) {
            existingIndicator.remove();
        }

        // Create new indicator
        const indicator = document.createElement('p');
        indicator.className = 'distance-indicator';
        indicator.style.cssText = `
            margin: 8px 0 0;
            font-size: 12px;
            color: #10b981;
            display: flex;
            align-items: center;
            gap: 6px;
        `;
        indicator.innerHTML = `
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                <circle cx="12" cy="10" r="3"/>
            </svg>
            <span>Clinics sorted by distance from your location (nearest first)</span>
        `;

        clinicSelect.parentElement.insertBefore(indicator, clinicSelect.nextSibling);
    }

    /**
     * Show loading state while fetching clinics
     */
    showLoadingState() {
        const clinicSelect = document.getElementById('clinicSelect');
        if (clinicSelect) {
            clinicSelect.disabled = true;
            clinicSelect.innerHTML = '<option value="">📍 Loading clinics by distance...</option>';
        }
    }

    /**
     * Hide loading state
     */
    hideLoadingState() {
        const clinicSelect = document.getElementById('clinicSelect');
        if (clinicSelect) {
            clinicSelect.disabled = false;
        }
    }

    /**
     * Show notification about location status
     */
    showLocationNotification(message, showRetryButton = false) {
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = 'location-toast';
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: #f59e0b;
            color: white;
            padding: 16px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            z-index: 10000;
            animation: slideInDown 0.3s ease-out;
            max-width: 90%;
            font-size: 14px;
            line-height: 1.5;
            text-align: center;
        `;
        
        const messageSpan = document.createElement('span');
        messageSpan.textContent = message;
        toast.appendChild(messageSpan);

        // Add retry button if needed
        if (showRetryButton) {
            const retryBtn = document.createElement('button');
            retryBtn.textContent = 'Retry';
            retryBtn.style.cssText = `
                background: white;
                color: #f59e0b;
                border: none;
                padding: 6px 16px;
                border-radius: 4px;
                margin-left: 12px;
                cursor: pointer;
                font-weight: 600;
                font-size: 13px;
            `;
            retryBtn.onclick = () => {
                toast.remove();
                this.requestLocation();
            };
            toast.appendChild(retryBtn);
        }

        document.body.appendChild(toast);

        // Auto remove after 8 seconds
        setTimeout(() => {
            if (document.body.contains(toast)) {
                toast.style.animation = 'slideOutUp 0.3s ease-out';
                setTimeout(() => toast.remove(), 300);
            }
        }, 8000);
    }

    /**
     * Update clinic info display with distance and duration
     */
    updateClinicInfo(clinicId) {
        // Skip if using modern clinic selector
        if (document.getElementById('clinicSelectorContainer')) {
            return;
        }
        
        const clinic = this.clinics.find(c => c.id == clinicId);
        if (!clinic) return;

        const clinicInfo = document.getElementById('clinicInfo');
        const clinicInfoName = document.getElementById('clinicInfoName');
        const clinicInfoAddress = document.getElementById('clinicInfoAddress');
        const clinicInfoPhone = document.getElementById('clinicInfoPhone');

        if (clinicInfo && clinicInfoName && clinicInfoAddress && clinicInfoPhone) {
            clinicInfoName.textContent = clinic.clinic_name;
            
            let addressText = clinic.clinic_address;
            if (clinic.distance_formatted) {
                addressText += ` (${clinic.distance_formatted} away`;
                if (clinic.duration_formatted) {
                    addressText += ` • ~${clinic.duration_formatted} drive`;
                }
                addressText += ')';
            }
            
            clinicInfoAddress.textContent = addressText;
            clinicInfoPhone.textContent = clinic.clinic_phone || 'N/A';
            clinicInfo.style.display = 'block';
        }
    }
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translate(-50%, -20px);
        }
        to {
            opacity: 1;
            transform: translate(-50%, 0);
        }
    }
    
    @keyframes slideOutUp {
        from {
            opacity: 1;
            transform: translate(-50%, 0);
        }
        to {
            opacity: 0;
            transform: translate(-50%, -20px);
        }
    }
    
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(100px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes slideOutRight {
        from {
            opacity: 1;
            transform: translateX(0);
        }
        to {
            opacity: 0;
            transform: translateX(100px);
        }
    }
    
    .distance-indicator {
        animation: fadeIn 0.5s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
`;
document.head.appendChild(style);

// Initialize on page load
let clinicDistanceCalculator;

document.addEventListener('DOMContentLoaded', () => {
    // Only initialize on pages with clinic selection (but not if modern selector is used)
    if (document.getElementById('clinicSelect') && !document.getElementById('clinicSelectorContainer')) {
        clinicDistanceCalculator = new ClinicDistanceCalculator();
        
        // Ask for location permission after a brief delay for better UX
        setTimeout(() => {
            clinicDistanceCalculator.init();
        }, 1000);

        // Update clinic info when selection changes
        document.getElementById('clinicSelect').addEventListener('change', (e) => {
            if (clinicDistanceCalculator && e.target.value) {
                clinicDistanceCalculator.updateClinicInfo(e.target.value);
            }
        });
    } else if (document.getElementById('clinicSelectorContainer')) {
        // Modern selector is present, just get location for it
        clinicDistanceCalculator = new ClinicDistanceCalculator();
        window.clinicDistanceCalculator = clinicDistanceCalculator; // Expose globally
        // Initialize immediately so location is available for clinic selector
        clinicDistanceCalculator.init();
    }
});
