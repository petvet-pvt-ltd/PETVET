/**
 * Modern Clinic Selector with Search and Favorites
 * Replaces traditional dropdown with beautiful card-based UI
 */

class ClinicSelector {
    constructor(options = {}) {
        this.containerId = options.containerId || 'clinicSelectorContainer';
        this.onSelect = options.onSelect || (() => {});
        this.clinics = [];
        this.favorites = new Set();
        this.selectedClinic = null;
        this.searchTerm = '';
        this.activeFilter = 'all'; // 'all', 'favorites', 'nearest'
        this.userLocation = null;
    }

    /**
     * Initialize the selector
     */
    async init(userLocation = null) {
        this.userLocation = userLocation;
        this.isLoadingDistance = !userLocation; // Show loading if no location yet
        await this.loadFavorites();
        await this.loadClinics();
        this.render();
        this.attachEventListeners();
    }

    /**
     * Update user location and reload clinics with distance
     */
    async updateLocation(userLocation) {
        this.userLocation = userLocation;
        this.isLoadingDistance = false; // Distance loaded
        await this.loadClinics();
        
        // Update the trigger if a clinic is selected
        if (this.selectedClinic) {
            // Find the updated clinic data with distance
            const updatedClinic = this.clinics.find(c => c.id === this.selectedClinic.id);
            if (updatedClinic) {
                this.selectedClinic = updatedClinic;
                const trigger = document.getElementById('clinicSelectorTrigger');
                if (trigger) {
                    trigger.innerHTML = this.renderTriggerContent();
                }
            }
        }
        
        this.updateClinicList();
    }

    /**
     * Load user's favorite clinics
     */
    async loadFavorites() {
        try {
            const response = await fetch('/PETVET/api/pet-owner/favorites.php?action=get');
            const text = await response.text();
            
            // Try to parse as JSON
            try {
                const data = JSON.parse(text);
                if (data.success && data.favorites) {
                    this.favorites = new Set(data.favorites.map(f => f.id));
                }
            } catch (parseError) {
                // Silently fail - user might not be logged in or API error
                // Don't log error as this is expected behavior for non-authenticated users
            }
        } catch (error) {
            // Network error - silently fail
        }
    }

    /**
     * Load clinics (with distance if location available)
     */
    async loadClinics() {
        try {
            let url = '/PETVET/api/pet-owner/get-clinics-by-distance.php';
            
            if (this.userLocation) {
                url += `?latitude=${this.userLocation.latitude}&longitude=${this.userLocation.longitude}`;
            }
            
            const response = await fetch(url);
            const data = await response.json();
            
            if (data.success) {
                this.clinics = data.clinics || [];
            }
        } catch (error) {
            console.error('Error loading clinics:', error);
        }
    }

    /**
     * Render the clinic selector UI
     */
    render() {
        const container = document.getElementById(this.containerId);
        if (!container) return;

        container.innerHTML = `
            <div class="clinic-selector-wrapper">
                <div class="clinic-selector-trigger" id="clinicSelectorTrigger">
                    ${this.renderTriggerContent()}
                </div>
                
                <div class="clinic-selector-dropdown" id="clinicSelectorDropdown">
                    <div class="clinic-selector-header">
                        <div class="clinic-selector-search">
                            <svg class="clinic-selector-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.35-4.35"></path>
                            </svg>
                            <input 
                                type="text" 
                                id="clinicSearchInput" 
                                placeholder="Search clinics by name or location..."
                                value="${this.searchTerm}"
                            />
                        </div>
                        
                        <div class="clinic-selector-filters">
                            <button type="button" class="clinic-filter-btn ${this.activeFilter === 'all' ? 'active' : ''}" data-filter="all">
                                🏥 All Clinics
                            </button>
                            <button type="button" class="clinic-filter-btn ${this.activeFilter === 'favorites' ? 'active' : ''}" data-filter="favorites">
                                ⭐ Favorites
                            </button>
                        </div>
                    </div>
                    
                    <div class="clinic-selector-list" id="clinicSelectorList">
                        ${this.renderClinicList()}
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Render trigger button content
     */
    renderTriggerContent() {
        if (this.selectedClinic) {
            return `
                <div class="clinic-selector-selected">
                    ${this.selectedClinic.clinic_logo ? `
                        <img src="${this.selectedClinic.clinic_logo}" alt="${this.selectedClinic.clinic_name}" class="clinic-selector-selected-logo">
                    ` : ''}
                    <div class="clinic-selector-selected-info">
                        <div class="clinic-selector-selected-name">${this.selectedClinic.clinic_name}</div>
                        <div class="clinic-selector-selected-distance">
                            ${this.selectedClinic.distance_formatted ? `
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                ${this.selectedClinic.distance_formatted} away
                            ` : this.selectedClinic.city || ''}
                        </div>
                    </div>
                </div>
                <svg class="clinic-selector-arrow" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            `;
        } else {
            return `
                <span class="clinic-selector-placeholder">Select a clinic</span>
                <svg class="clinic-selector-arrow" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            `;
        }
    }

    /**
     * Render clinic list
     */
    renderClinicList() {
        const filteredClinics = this.getFilteredClinics();

        if (filteredClinics.length === 0) {
            return `
                <div class="clinic-selector-empty">
                    <div class="clinic-selector-empty-icon">🏥</div>
                    <div>No clinics found</div>
                </div>
            `;
        }

        return filteredClinics.map((clinic, index) => {
            const isFavorite = this.favorites.has(clinic.id);
            const isSelected = this.selectedClinic && this.selectedClinic.id === clinic.id;
            const isNearest = index === 0 && this.activeFilter === 'nearest';

            return `
                <div class="clinic-item ${isSelected ? 'selected' : ''}" data-clinic-id="${clinic.id}">
                    ${clinic.clinic_logo ? `
                        <img src="${clinic.clinic_logo}" alt="${clinic.clinic_name}" class="clinic-item-logo">
                    ` : ''}
                    
                    <div class="clinic-item-info">
                        <div class="clinic-item-name">
                            ${clinic.clinic_name}
                        </div>
                        <div class="clinic-item-details">
                            ${clinic.distance_formatted ? `
                                <span class="clinic-item-distance">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                        <circle cx="12" cy="10" r="3"></circle>
                                    </svg>
                                    ${clinic.distance_formatted}
                                </span>
                            ` : (this.isLoadingDistance ? `
                                <span class="clinic-item-distance loading">
                                    <span class="distance-loader"></span>
                                    Calculating distance...
                                </span>
                            ` : '')}
                            ${clinic.city ? `
                                <span class="clinic-item-city">
                                    📌 ${clinic.city}
                                </span>
                            ` : ''}
                        </div>
                    </div>

                    <button type="button" class="clinic-favorite-btn ${isFavorite ? 'favorited' : ''}" data-clinic-id="${clinic.id}" onclick="event.stopPropagation(); event.preventDefault(); clinicSelectorInstance.toggleFavorite(${clinic.id})">
                        <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                        </svg>
                    </button>
                </div>
            `;
        }).join('');
    }

    /**
     * Get filtered clinics based on search and filter
     */
    getFilteredClinics() {
        let filtered = [...this.clinics];

        // Apply search
        if (this.searchTerm) {
            const term = this.searchTerm.toLowerCase();
            filtered = filtered.filter(clinic => 
                clinic.clinic_name.toLowerCase().includes(term) ||
                (clinic.city && clinic.city.toLowerCase().includes(term)) ||
                (clinic.district && clinic.district.toLowerCase().includes(term))
            );
        }

        // Apply filter
        switch (this.activeFilter) {
            case 'favorites':
                filtered = filtered.filter(clinic => this.favorites.has(clinic.id));
                break;
        }

        return filtered;
    }

    /**
     * Attach event listeners
     */
    attachEventListeners() {
        // Toggle dropdown
        const trigger = document.getElementById('clinicSelectorTrigger');
        const dropdown = document.getElementById('clinicSelectorDropdown');
        const arrow = trigger?.querySelector('.clinic-selector-arrow');

        trigger?.addEventListener('click', (e) => {
            e.preventDefault();
            dropdown?.classList.toggle('show');
            arrow?.classList.toggle('rotated');
            trigger?.classList.toggle('active');
        });

        // Close on outside click
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.clinic-selector-wrapper')) {
                dropdown?.classList.remove('show');
                arrow?.classList.remove('rotated');
                trigger?.classList.remove('active');
            }
        });

        // Search
        const searchInput = document.getElementById('clinicSearchInput');
        searchInput?.addEventListener('input', (e) => {
            this.searchTerm = e.target.value;
            this.updateClinicList();
        });

        // Filter buttons
        document.querySelectorAll('.clinic-filter-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                this.activeFilter = btn.dataset.filter;
                document.querySelectorAll('.clinic-filter-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                this.updateClinicList();
            });
        });

        // Clinic selection
        document.querySelectorAll('.clinic-item').forEach(item => {
            item.addEventListener('click', (e) => {
                if (e.target.closest('.clinic-favorite-btn')) return;
                
                const clinicId = parseInt(item.dataset.clinicId);
                this.selectClinic(clinicId);
            });
        });
    }

    /**
     * Update clinic list without full re-render
     */
    updateClinicList() {
        const listContainer = document.getElementById('clinicSelectorList');
        if (listContainer) {
            listContainer.innerHTML = this.renderClinicList();
            
            // Reattach clinic item listeners
            document.querySelectorAll('.clinic-item').forEach(item => {
                item.addEventListener('click', (e) => {
                    if (e.target.closest('.clinic-favorite-btn')) return;
                    
                    const clinicId = parseInt(item.dataset.clinicId);
                    this.selectClinic(clinicId);
                });
            });
        }
    }

    /**
     * Select a clinic
     */
    selectClinic(clinicId) {
        const clinic = this.clinics.find(c => c.id === clinicId);
        if (!clinic) return;

        this.selectedClinic = clinic;
        
        // Update trigger
        const trigger = document.getElementById('clinicSelectorTrigger');
        if (trigger) {
            trigger.innerHTML = this.renderTriggerContent();
        }

        // Close dropdown
        const dropdown = document.getElementById('clinicSelectorDropdown');
        const arrow = trigger?.querySelector('.clinic-selector-arrow');
        dropdown?.classList.remove('show');
        arrow?.classList.remove('rotated');
        trigger?.classList.remove('active');

        // Update selected state in list
        document.querySelectorAll('.clinic-item').forEach(item => {
            item.classList.toggle('selected', parseInt(item.dataset.clinicId) === clinicId);
        });

        // Callback
        this.onSelect(clinic);
    }

    /**
     * Toggle favorite status
     */
    async toggleFavorite(clinicId) {
        const isFavorite = this.favorites.has(clinicId);
        const action = isFavorite ? 'remove' : 'add';

        try {
            const formData = new FormData();
            formData.append('action', action);
            formData.append('clinic_id', clinicId);

            const response = await fetch('/PETVET/api/pet-owner/favorites.php', {
                method: 'POST',
                body: formData
            });

            const text = await response.text();
            
            // Try to parse as JSON
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                return;
            }
            
            if (data.success) {
                if (isFavorite) {
                    this.favorites.delete(clinicId);
                } else {
                    this.favorites.add(clinicId);
                }

                // Update UI
                this.updateClinicList();
            }
        } catch (error) {
            // Silently handle errors
        }
    }
}

// Global instance
let clinicSelectorInstance;
