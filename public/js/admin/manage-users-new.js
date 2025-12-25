document.addEventListener('DOMContentLoaded', function() {
    const roleFilter = document.getElementById('roleFilter');
    const searchInput = document.getElementById('searchInput');
    let allUsers = [];

    // Load users on page load
    loadUsers();

    // Event listeners
    roleFilter.addEventListener('change', loadUsers);
    searchInput.addEventListener('input', filterUsers);

    async function loadUsers() {
        const role = roleFilter.value;
        
        try {
            const response = await fetch(`/PETVET/api/admin/get-users-by-role.php?role=${role}`);
            const data = await response.json();

            if (data.success) {
                allUsers = data.users;
                updateStats(data.stats);
                displayUsers(allUsers);
            } else {
                console.error('Error loading users:', data.error);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    function updateStats(stats) {
        document.getElementById('totalUsers').textContent = stats.total_users || 0;
        document.getElementById('petOwners').textContent = stats.pet_owners || 0;
        document.getElementById('vets').textContent = stats.vets || 0;
        
        // Calculate service providers total
        const serviceProviders = (parseInt(stats.vets) || 0) + 
                                (parseInt(stats.groomers) || 0) + 
                                (parseInt(stats.sitters) || 0) + 
                                (parseInt(stats.breeders) || 0) + 
                                (parseInt(stats.trainers) || 0);
        document.getElementById('serviceProviders').textContent = serviceProviders;
        
        // Update service provider breakdown
        document.getElementById('vetsCount').textContent = stats.vets || 0;
        document.getElementById('groomersCount').textContent = stats.groomers || 0;
        document.getElementById('breedersCount').textContent = stats.breeders || 0;
        document.getElementById('sittersCount').textContent = stats.sitters || 0;
        document.getElementById('trainersCount').textContent = stats.trainers || 0;
    }

    function displayUsers(users) {
        const tbody = document.getElementById('usersTableBody');
        const noResults = document.getElementById('noResults');

        if (users.length === 0) {
            tbody.innerHTML = '';
            noResults.style.display = 'block';
            return;
        }

        noResults.style.display = 'none';

        tbody.innerHTML = users.map(user => {
            const roleNames = user.role_names ? user.role_names.split(',') : [];
            const roleBadges = user.roles ? user.roles.split(', ').map((role, index) => {
                const roleName = roleNames[index] || '';
                return `<span class="role-badge role-${roleName}">${role}</span>`;
            }).join('') : '';
            
            let additionalInfo = '';
            if (user.role_data) {
                if (user.role_data.pet_count !== undefined) {
                    additionalInfo += `<div><i class="fas fa-paw"></i> ${user.role_data.pet_count} Pets</div>`;
                }
                if (user.role_data.clinic_name) {
                    additionalInfo += `<div><i class="fas fa-hospital"></i> ${user.role_data.clinic_name}</div>`;
                }
            }

            return `
                <tr>
                    <td>
                        <div class="user-info">
                            ${user.avatar ? 
                                `<img src="${user.avatar}" alt="${user.first_name}" class="user-avatar">` :
                                `<div class="user-avatar-placeholder">${user.first_name.charAt(0)}${user.last_name.charAt(0)}</div>`
                            }
                            <div class="user-name-email">
                                <h4>${user.first_name} ${user.last_name}</h4>
                                <p>${user.email}</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="contact-info">
                            ${user.phone ? `<div><i class="fas fa-phone"></i> ${user.phone}</div>` : '<div><i class="fas fa-phone"></i> N/A</div>'}
                            ${user.address ? `<div><i class="fas fa-map-marker-alt"></i> ${user.address.substring(0, 30)}...</div>` : ''}
                        </div>
                    </td>
                    <td>
                        <div class="role-badges">
                            ${roleBadges}
                        </div>
                    </td>
                    <td>
                        <div class="additional-info">
                            ${additionalInfo || '<div style="color: #a0aec0;">No additional info</div>'}
                        </div>
                    </td>
                    <td>
                        <div class="date-info">
                            ${user.last_login ? formatDate(user.last_login) : 'Never'}
                        </div>
                    </td>
                    <td>
                        <button class="btn btn-view" onclick="viewUser(${user.id})">
                            <i class="fas fa-eye"></i> View
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function filterUsers() {
        const searchTerm = searchInput.value.toLowerCase();
        const filtered = allUsers.filter(user => 
            (user.first_name + ' ' + user.last_name).toLowerCase().includes(searchTerm) ||
            user.email.toLowerCase().includes(searchTerm) ||
            (user.phone && user.phone.toLowerCase().includes(searchTerm)) ||
            (user.roles && user.roles.toLowerCase().includes(searchTerm))
        );
        displayUsers(filtered);
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diff = now - date;
        const days = Math.floor(diff / (1000 * 60 * 60 * 24));

        if (days === 0) return 'Today';
        if (days === 1) return 'Yesterday';
        if (days < 7) return `${days} days ago`;
        
        return date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });
    }

    // Global function for view button
    window.viewUser = function(userId) {
        const user = allUsers.find(u => u.id == userId);
        if (!user) return;

        const modal = document.getElementById('userModal');
        const details = document.getElementById('userDetails');

        const roleNames = user.role_names ? user.role_names.split(',') : [];
        const roleBadges = user.roles ? user.roles.split(', ').map((role, index) => {
            const roleName = roleNames[index] || '';
            return `<span class="role-badge role-${roleName}" style="font-size: 13px; padding: 6px 12px;">${role}</span>`;
        }).join('') : '';

        let additionalSections = '';
        
        if (user.role_data) {
            if (user.role_data.pet_count !== undefined) {
                additionalSections += `
                    <div>
                        <h3 style="color: #667eea; margin-bottom: 10px;"><i class="fas fa-paw"></i> Pet Owner Info</h3>
                        <p><strong>Total Pets:</strong> ${user.role_data.pet_count}</p>
                    </div>
                `;
            }
            if (user.role_data.clinic_name) {
                additionalSections += `
                    <div>
                        <h3 style="color: #667eea; margin-bottom: 10px;"><i class="fas fa-hospital"></i> Clinic Information</h3>
                        <p><strong>Clinic:</strong> ${user.role_data.clinic_name}</p>
                    </div>
                `;
            }
        }

        details.innerHTML = `
            <div style="text-align: center; margin-bottom: 30px;">
                ${user.avatar ? 
                    `<img src="${user.avatar}" alt="${user.first_name}" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid #667eea; margin-bottom: 15px;">` :
                    `<div style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 48px; font-weight: bold; margin: 0 auto 15px; border: 4px solid #667eea;">
                        ${user.first_name.charAt(0)}${user.last_name.charAt(0)}
                    </div>`
                }
                <h2 style="color: #2d3748; margin-bottom: 5px;">${user.first_name} ${user.last_name}</h2>
                <p style="color: #718096; font-size: 16px;">${user.email}</p>
                <div style="margin-top: 15px; display: flex; gap: 8px; justify-content: center; flex-wrap: wrap;">
                    ${roleBadges}
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px;">
                <div>
                    <h3 style="color: #667eea; margin-bottom: 10px;"><i class="fas fa-info-circle"></i> Basic Information</h3>
                    <p><strong>User ID:</strong> #${user.id}</p>
                    <p><strong>Phone:</strong> ${user.phone || 'N/A'}</p>
                    <p><strong>Primary Role:</strong> ${user.primary_role || 'N/A'}</p>
                    <p><strong>Account Status:</strong> 
                        <span class="status-badge status-${user.is_blocked ? 'blocked' : (user.is_active ? 'active' : 'inactive')}">
                            ${user.is_blocked ? 'Blocked' : (user.is_active ? 'Active' : 'Inactive')}
                        </span>
                    </p>
                </div>

                <div>
                    <h3 style="color: #667eea; margin-bottom: 10px;"><i class="fas fa-calendar"></i> Activity</h3>
                    <p><strong>Joined:</strong> ${formatDate(user.created_at)}</p>
                    <p><strong>Last Login:</strong> ${user.last_login ? formatDate(user.last_login) : 'Never'}</p>
                </div>

                ${user.address ? `
                    <div>
                        <h3 style="color: #667eea; margin-bottom: 10px;"><i class="fas fa-map-marker-alt"></i> Address</h3>
                        <p>${user.address}</p>
                    </div>
                ` : ''}

                ${additionalSections}
            </div>
        `;

        modal.style.display = 'block';
    };

    // Modal close functionality
    const modal = document.getElementById('userModal');
    const closeBtn = document.querySelector('.close');

    closeBtn.onclick = function() {
        modal.style.display = 'none';
    };

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    };
});
