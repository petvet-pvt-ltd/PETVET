<link rel="stylesheet" href="/PETVET/public/css/admin/styles.css">
<link rel="stylesheet" href="/PETVET/public/css/admin/manage-cards.css">
<link rel="stylesheet" href="/PETVET/public/css/admin/manage-users-new.css">

<div class="main-content">
    <?php include __DIR__ . '/../shared/components/user-welcome-header.php'; ?>

    <section class="overview">
        <h2 class="section-title">Manage Users by Role</h2>
        
        <!-- Statistics Cards -->
        <div class="dashboard-top-cards">
            <div class="stat-card card-hover">
                <div class="stat-icon users-icon">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-label">TOTAL REGISTERED USERS</div>
                    <div class="stat-value" id="totalUsers">0</div>
                    <div class="stat-growth success">
                        <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M7 14l5-5 5 5z"/>
                        </svg>
                        <span id="userGrowth">All approved users</span>
                    </div>
                </div>
            </div>

            <div class="stat-card card-hover">
                <div class="stat-icon pending-icon">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-label">PET OWNERS</div>
                    <div class="stat-value" id="petOwners">0</div>
                    <div class="stat-info">Most common user role</div>
                </div>
            </div>

            <div class="stat-card card-hover">
                <div class="stat-icon active-icon">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-label">SERVICE PROVIDERS</div>
                    <div class="stat-value" id="serviceProviders">0</div>
                    <div class="stat-breakdown">
                        Vets: <span id="vetsCount">0</span> | Groomers: <span id="groomersCount">0</span> | Sitters: <span id="sittersCount">0</span> | Breeders: <span id="breedersCount">0</span> | Trainers: <span id="trainersCount">0</span>
                    </div>
                </div>
            </div>

            <div class="stat-card card-hover">
                <div class="stat-icon clinics-icon">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm-2 14l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-label">VETERINARIANS</div>
                    <div class="stat-value" id="vets">0</div>
                    <div class="stat-info">Licensed professionals</div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-bar">
            <select id="roleFilter" class="filter-select">
                <option value="all">All Users</option>
                <option value="pet_owner">Pet Owners</option>
                <option value="vet">Veterinarians</option>
                <option value="groomer">Groomers</option>
                <option value="sitter">Pet Sitters</option>
                <option value="breeder">Breeders</option>
                <option value="trainer">Trainers</option>
            </select>
            <input type="text" id="searchInput" class="search-input" placeholder="Search by name, email, or phone...">
        </div>

        <!-- Users Table -->
        <div class="data-table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>User Info</th>
                        <th>Contact</th>
                        <th>Roles</th>
                        <th>Additional Info</th>
                        <th>Last Login</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px;">Loading users...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="no-results" id="noResults" style="display: none; text-align: center; padding: 40px;">
            <p>No users found</p>
        </div>
    </section>

    <!-- User Details Modal -->
    <div id="userModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="userDetails"></div>
        </div>
    </div>

    <script src="/PETVET/public/js/admin/manage-users-new.js"></script>
</div>
