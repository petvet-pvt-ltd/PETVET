<link rel="stylesheet" href="/PETVET/public/css/admin/styles.css">
<link rel="stylesheet" href="/PETVET/public/css/admin/manage-cards.css">
<link rel="stylesheet" href="/PETVET/public/css/admin/manage-clinics.css">

<div class="main-content">
    <?php include __DIR__ . '/../shared/components/user-welcome-header.php'; ?>

    <section class="overview">
        <h2 class="section-title">Manage Veterinary Clinics</h2>
        
        <!-- Statistics Cards -->
        <div class="dashboard-top-cards">
            <div class="stat-card card-hover">
                <div class="stat-icon users-icon">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-label">TOTAL REGISTERED CLINICS</div>
                    <div class="stat-value" id="totalClinics">0</div>
                    <div class="stat-growth success">
                        <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M7 14l5-5 5 5z"/>
                        </svg>
                        +10% from last month
                    </div>
                </div>
            </div>

            <div class="stat-card card-hover">
                <div class="stat-icon pending-icon">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                        <path d="M12.5 7H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-label">PENDING APPROVAL REQUESTS</div>
                    <div class="stat-value" id="pendingClinics">0</div>
                    <button class="review-btn" onclick="document.getElementById('statusFilter').value='pending'; document.getElementById('statusFilter').dispatchEvent(new Event('change'));">Review Now</button>
                </div>
            </div>

            <div class="stat-card card-hover">
                <div class="stat-icon active-icon">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-label">VERIFIED CLINICS</div>
                    <div class="stat-value" id="approvedClinics">0</div>
                    <div class="stat-info">Approved Partners</div>
                </div>
            </div>

            <div class="stat-card card-hover">
                <div class="stat-icon clinics-icon">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-label">ACTIVE CLINICS</div>
                    <div class="stat-value" id="activeClinics">0</div>
                    <div class="stat-info">Currently Operating</div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-bar">
            <select id="statusFilter" class="filter-select">
                <option value="all">All Clinics</option>
                <option value="pending">Pending Approval</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
            <input type="text" id="searchInput" class="search-input" placeholder="Search by clinic name, email, or city...">
        </div>

        <!-- Clinics Table -->
        <div class="data-table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Clinic Info</th>
                        <th>Contact</th>
                        <th>Location</th>
                        <th>Stats</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="clinicsTableBody">
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px;">Loading clinics...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="no-results" id="noResults" style="display: none; text-align: center; padding: 40px;">
            <p>No clinics found</p>
        </div>
    </section>

    <!-- Clinic Details Modal -->
    <div id="clinicModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="clinicDetails"></div>
        </div>
    </div>

    <script src="/PETVET/public/js/admin/manage-clinics.js"></script>
</div>
