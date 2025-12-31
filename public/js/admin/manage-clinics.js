document.addEventListener('DOMContentLoaded', function() {
    const statusFilter = document.getElementById('statusFilter');
    const searchInput = document.getElementById('searchInput');
    let allClinics = [];

    // Load clinics on page load
    loadClinics();

    // Event listeners
    statusFilter.addEventListener('change', loadClinics);
    searchInput.addEventListener('input', filterClinics);

    async function loadClinics() {
        const status = statusFilter.value;
        
        try {
            const response = await fetch(`/PETVET/api/admin/get-clinics.php?status=${status}`);
            const data = await response.json();

            if (data.success) {
                allClinics = data.clinics;
                updateStats(data.stats);
                displayClinics(allClinics);
            } else {
                console.error('Error loading clinics:', data.error);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    function updateStats(stats) {
        document.getElementById('totalClinics').textContent = stats.total || 0;
        document.getElementById('pendingClinics').textContent = stats.pending || 0;
        document.getElementById('approvedClinics').textContent = stats.approved || 0;
        document.getElementById('activeClinics').textContent = stats.active || 0;
    }

    function displayClinics(clinics) {
        const tbody = document.getElementById('clinicsTableBody');
        const noResults = document.getElementById('noResults');

        if (clinics.length === 0) {
            tbody.innerHTML = '';
            noResults.style.display = 'block';
            return;
        }

        noResults.style.display = 'none';

        tbody.innerHTML = clinics.map(clinic => `
            <tr>
                <td>
                    <div class="clinic-info">
                        ${clinic.clinic_logo ? 
                            `<img src="${clinic.clinic_logo}" alt="${clinic.clinic_name}" class="clinic-logo">` :
                            `<div class="clinic-logo-placeholder">${clinic.clinic_name.charAt(0)}</div>`
                        }
                        <div class="clinic-name-email">
                            <h4>${clinic.clinic_name}</h4>
                            <p>${clinic.clinic_email || 'No email'}</p>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="contact-info">
                        <div><i class="fas fa-phone"></i> ${clinic.clinic_phone || 'N/A'}</div>
                    </div>
                </td>
                <td>
                    <span class="location-badge">
                        <i class="fas fa-map-marker-alt"></i> ${clinic.city || 'N/A'}, ${clinic.district || 'N/A'}
                    </span>
                </td>
                <td>
                    <div class="stats-info">
                        <div class="stat-item">
                            <i class="fas fa-user-md"></i>
                            <span>${clinic.staff_count} Staff</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-calendar-check"></i>
                            <span>${clinic.appointment_count} Appointments</span>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="status-badge status-${clinic.verification_status}">
                        ${clinic.verification_status}
                    </span>
                </td>
                <td>
                    <div class="date-info">
                        ${formatDate(clinic.created_at)}
                    </div>
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-view" onclick="viewClinic(${clinic.id})">
                            <i class="fas fa-eye"></i> View
                        </button>
                        ${clinic.verification_status === 'pending' ? `
                            <button class="btn btn-approve" onclick="updateClinicStatus(${clinic.id}, 'approved')">
                                <i class="fas fa-check"></i> Approve
                            </button>
                            <button class="btn btn-reject" onclick="updateClinicStatus(${clinic.id}, 'rejected')">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        ` : ''}
                    </div>
                </td>
            </tr>
        `).join('');
    }

    function filterClinics() {
        const searchTerm = searchInput.value.toLowerCase();
        const filtered = allClinics.filter(clinic => 
            clinic.clinic_name.toLowerCase().includes(searchTerm) ||
            (clinic.clinic_email && clinic.clinic_email.toLowerCase().includes(searchTerm)) ||
            (clinic.city && clinic.city.toLowerCase().includes(searchTerm)) ||
            (clinic.district && clinic.district.toLowerCase().includes(searchTerm))
        );
        displayClinics(filtered);
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });
    }

    // Global functions for button clicks
    window.viewClinic = function(clinicId) {
        const clinic = allClinics.find(c => c.id == clinicId);
        if (!clinic) return;

        const modal = document.getElementById('clinicModal');
        const details = document.getElementById('clinicDetails');

        details.innerHTML = `
            <h2 style="margin-bottom: 20px; color: #2d3748;">
                <i class="fas fa-hospital"></i> ${clinic.clinic_name}
            </h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 20px;">
                ${clinic.clinic_logo ? `
                    <img src="${clinic.clinic_logo}" alt="${clinic.clinic_name}" style="width: 100%; max-width: 200px; border-radius: 10px; border: 2px solid #e2e8f0;">
                ` : ''}
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div>
                    <h3 style="color: #667eea; margin-bottom: 10px;"><i class="fas fa-info-circle"></i> Basic Information</h3>
                    <p><strong>Email:</strong> ${clinic.clinic_email || 'N/A'}</p>
                    <p><strong>Phone:</strong> ${clinic.clinic_phone || 'N/A'}</p>
                    <p><strong>Status:</strong> <span class="status-badge status-${clinic.verification_status}">${clinic.verification_status}</span></p>
                    <p><strong>Active:</strong> ${clinic.is_active ? 'Yes' : 'No'}</p>
                </div>

                <div>
                    <h3 style="color: #667eea; margin-bottom: 10px;"><i class="fas fa-map-marker-alt"></i> Location</h3>
                    <p><strong>Address:</strong> ${clinic.clinic_address}</p>
                    <p><strong>City:</strong> ${clinic.city || 'N/A'}</p>
                    <p><strong>District:</strong> ${clinic.district || 'N/A'}</p>
                </div>

                <div>
                    <h3 style="color: #667eea; margin-bottom: 10px;"><i class="fas fa-chart-bar"></i> Statistics</h3>
                    <p><strong>Staff Members:</strong> ${clinic.staff_count}</p>
                    <p><strong>Total Appointments:</strong> ${clinic.appointment_count}</p>
                    <p><strong>Registered:</strong> ${formatDate(clinic.created_at)}</p>
                </div>
            </div>

            ${clinic.clinic_description ? `
                <div style="margin-top: 20px;">
                    <h3 style="color: #667eea; margin-bottom: 10px;"><i class="fas fa-file-alt"></i> Description</h3>
                    <p>${clinic.clinic_description}</p>
                </div>
            ` : ''}

            ${clinic.verification_status === 'pending' ? `
                <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #e2e8f0; display: flex; gap: 15px; justify-content: center;">
                    <button class="btn btn-approve" onclick="updateClinicStatus(${clinic.id}, 'approved'); document.getElementById('clinicModal').style.display='none';">
                        <i class="fas fa-check"></i> Approve Clinic
                    </button>
                    <button class="btn btn-reject" onclick="updateClinicStatus(${clinic.id}, 'rejected'); document.getElementById('clinicModal').style.display='none';">
                        <i class="fas fa-times"></i> Reject Clinic
                    </button>
                </div>
            ` : ''}
        `;

        modal.style.display = 'block';
    };

    window.updateClinicStatus = async function(clinicId, status) {
        if (!confirm(`Are you sure you want to ${status} this clinic?`)) {
            return;
        }

        try {
            const response = await fetch('/PETVET/api/admin/update-clinic-status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    clinic_id: clinicId,
                    status: status
                })
            });

            const data = await response.json();

            if (data.success) {
                alert(`Clinic ${status} successfully!`);
                loadClinics();
            } else {
                alert('Error: ' + data.error);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred');
        }
    };

    // Modal close functionality
    const modal = document.getElementById('clinicModal');
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
