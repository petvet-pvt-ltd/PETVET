/**
 * Shop Clinics - Distance Calculator & Favorites for Pet Owner
 * Uses backend API to calculate distances (same as booking appointment)
 */

class ShopClinicDistance {
    constructor() {
        this.userLocation = null;
        this.favorites = new Set();
        this.clinicsWithDistance = []; // Store clinics data with distance
        this.filters = {
            nearby: true, // Default active
            favorites: false,
            search: ''
        };
        this.loadFavorites();
    }

    /**
     * Initialize
     */
    async init() {
        // Initial filter application (for favorites/search if any)
        this.applyFiltersAndSort();

        // Check if geolocation is supported
        if (!navigator.geolocation) {
            console.warn('Geolocation not supported');
            this.hideAllDistanceLoaders();
            return;
        }

        // Request user location
        this.requestLocation();
    }

    /**
     * Request user's current location
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
        this.userLocation = {
            latitude: position.coords.latitude,
            longitude: position.coords.longitude
        };

        // Fetch clinics with distances from backend API
        await this.loadClinicsWithDistance();
    }

    /**
     * Handle location error
     */
    onLocationError(error) {
        console.warn('Location error:', error.message);
        this.hideAllDistanceLoaders();
    }

    /**
     * Load clinics with distance from backend API
     */
    async loadClinicsWithDistance() {
        try {
            const url = `/PETVET/api/pet-owner/get-clinics-by-distance.php?latitude=${this.userLocation.latitude}&longitude=${this.userLocation.longitude}`;
            
            const response = await fetch(url);
            const data = await response.json();
            
            if (data.success && data.clinics) {
                this.clinicsWithDistance = data.clinics;
                this.updateDistanceBadges();
                this.applyFiltersAndSort();
            }
        } catch (error) {
            console.error('Error loading clinics by distance:', error);
            this.hideAllDistanceLoaders();
        }
    }

    /**
     * Update just the distance badges text
     */
    updateDistanceBadges() {
        this.clinicsWithDistance.forEach(clinic => {
            const distanceElement = document.querySelector(`.clinic-distance[data-clinic-id="${clinic.id}"]`);
            
            if (distanceElement && clinic.distance_formatted) {
                distanceElement.innerHTML = `
                    <span class="clinic-item-distance">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        ${clinic.distance_formatted}
                    </span>
                `;
            }
        });
    }

    /**
     * Hide all distance loaders
     */
    hideAllDistanceLoaders() {
        document.querySelectorAll('.clinic-distance').forEach(el => {
            if (el.querySelector('.distance-loader')) {
                el.innerHTML = '';
            }
        });
    }

    /**
     * Load favorites from API
     */
    async loadFavorites() {
        try {
            const response = await fetch('/PETVET/api/pet-owner/shop-favorites.php');
            const data = await response.json();
            if (data.success) {
                this.favorites = new Set(data.favorites.map(String));
                this.updateFavoriteButtons();
            }
        } catch (e) {
            console.error('Error loading favorites:', e);
        }
    }

    /**
     * Toggle favorite status for a clinic
     */
    async toggleFavorite(clinicId) {
        const clinicIdStr = String(clinicId);
        
        // Optimistic update
        if (this.favorites.has(clinicIdStr)) {
            this.favorites.delete(clinicIdStr);
        } else {
            this.favorites.add(clinicIdStr);
        }
        this.updateFavoriteButtons();
        
        // If favorites filter is active, re-apply filters to hide/show
        if (this.filters.favorites) {
            this.applyFiltersAndSort();
        }

        try {
            const response = await fetch('/PETVET/api/pet-owner/shop-favorites.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ clinic_id: clinicId })
            });
            const data = await response.json();
            if (!data.success) {
                // Revert on error
                if (this.favorites.has(clinicIdStr)) {
                    this.favorites.delete(clinicIdStr);
                } else {
                    this.favorites.add(clinicIdStr);
                }
                this.updateFavoriteButtons();
                console.error('Error toggling favorite:', data.error);
            }
        } catch (e) {
            console.error('Error toggling favorite:', e);
            // Revert on error
             if (this.favorites.has(clinicIdStr)) {
                this.favorites.delete(clinicIdStr);
            } else {
                this.favorites.add(clinicIdStr);
            }
            this.updateFavoriteButtons();
        }
    }

    /**
     * Update favorite button states
     */
    updateFavoriteButtons() {
        document.querySelectorAll('.favorite-btn').forEach(btn => {
            const clinicId = String(btn.dataset.clinicId);
            const isFavorite = this.favorites.has(clinicId);
            
            if (isFavorite) {
                btn.classList.add('active');
                btn.querySelector('path').setAttribute('fill', 'currentColor');
            } else {
                btn.classList.remove('active');
                btn.querySelector('path').setAttribute('fill', 'none');
            }
        });
    }

    /**
     * Set filter state
     */
    setFilter(type, isActive) {
        if (type === 'nearby') this.filters.nearby = isActive;
        if (type === 'favorites') this.filters.favorites = isActive;
        
        // Update UI chips
        const chip = document.getElementById(type === 'nearby' ? 'filterNearby' : 'filterFavorites');
        if (chip) {
            if (isActive) chip.classList.add('active');
            else chip.classList.remove('active');
        }

        this.applyFiltersAndSort();
    }

    /**
     * Set search query
     */
    setSearch(query) {
        this.filters.search = query.toLowerCase().trim();
        this.applyFiltersAndSort();
    }

    /**
     * Apply all filters and sorting
     */
    applyFiltersAndSort() {
        const grid = document.getElementById('clinicsGrid');
        const cards = Array.from(grid.children);
        
        // 1. Filter
        cards.forEach(card => {
            const clinicId = String(card.dataset.clinicId);
            const name = card.querySelector('.clinic-name').textContent.toLowerCase();
            
            let visible = true;

            // Search filter
            if (this.filters.search && !name.includes(this.filters.search)) {
                visible = false;
            }

            // Favorites filter
            if (this.filters.favorites && !this.favorites.has(clinicId)) {
                visible = false;
            }

            card.style.display = visible ? 'flex' : 'none';
        });

        // 2. Sort (only if Nearby is active and we have distance data)
        if (this.filters.nearby && this.clinicsWithDistance.length > 0) {
            // Create a map of clinic ID to distance value for sorting
            const distanceMap = new Map();
            this.clinicsWithDistance.forEach(c => {
                // Use distance_km from API (fallback to very high number if missing)
                distanceMap.set(String(c.id), c.distance_km !== undefined ? parseFloat(c.distance_km) : 999999999);
            });

            const sortedCards = cards.sort((a, b) => {
                const idA = String(a.dataset.clinicId);
                const idB = String(b.dataset.clinicId);
                const distA = distanceMap.has(idA) ? distanceMap.get(idA) : 999999999;
                const distB = distanceMap.has(idB) ? distanceMap.get(idB) : 999999999;
                return distA - distB;
            });

            // Re-append in new order
            sortedCards.forEach(card => grid.appendChild(card));
        }
    }
}

// Global instance
let shopClinicDistance;

// Global functions called from HTML
function toggleFavorite(clinicId) {
    if (shopClinicDistance) {
        shopClinicDistance.toggleFavorite(clinicId);
    }
}

function toggleFilter(type) {
    if (!shopClinicDistance) return;
    
    const chip = document.getElementById(type === 'nearby' ? 'filterNearby' : 'filterFavorites');
    const isActive = !chip.classList.contains('active');
    
    shopClinicDistance.setFilter(type, isActive);
}

function filterClinics() {
    if (!shopClinicDistance) return;
    const query = document.getElementById('shopSearch').value;
    shopClinicDistance.setSearch(query);
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    shopClinicDistance = new ShopClinicDistance();
    shopClinicDistance.init();
    shopClinicDistance.updateFavoriteButtons();
});
