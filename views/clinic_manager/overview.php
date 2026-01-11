<?php
$currentPage = basename($_SERVER['PHP_SELF']);
date_default_timezone_set('Asia/Colombo');
$today = date('l, F j, Y');


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Clinic Manager Dashboard</title>
  <link rel="stylesheet" href="/PETVET/public/css/clinic-manager/overview.css">
  <style>
    .main-content {
    margin-left: 240px;
    padding: 24px;
  }

  @media (max-width: 768px) {
    .main-content {
      margin-left: 0;
      width: 100%;
    }
  }
  </style>
</head>
<body>
  <main class="main-content">
    <?php 
    // Include user welcome header
    include __DIR__ . '/../shared/components/user-welcome-header.php'; 
    ?>
    
    <div class="page-frame">
      <section class="cm-layout">
        <!-- LEFT: KPIs + Appointments -->
        <div class="left-col">
          <!-- KPI Cards -->
          <section class="cm-kpis" role="region" aria-label="Key Performance Indicators">
            <?php if (empty($kpis)): ?>
              <p>No KPI data available.</p>
            <?php else: ?>
              <?php foreach ($kpis as $kpi): ?>
                <article class="kpi-card" role="article" aria-label="<?php echo htmlspecialchars($kpi['label']); ?>">
                  <div class="kpi-number"><?php echo $kpi['value']; ?></div>
                  <div class="kpi-label"><?php echo htmlspecialchars($kpi['label']); ?></div>
                </article>
              <?php endforeach; ?>
            <?php endif; ?>
          </section>

          <!-- Ongoing Appointments -->
          <section class="card table-card">
            <div class="card-head">
              <h2>Ongoing Appointments</h2>
              <a class="link-muted" href="/PETVET/index.php?module=clinic-manager&page=appointments" aria-label="View all appointments">View all</a>
            </div>
            <div class="table-wrap" role="region" aria-label="Ongoing Appointments">
              <?php if (empty($ongoingAppointments)): ?>
                <p class="no-data">No vets are in an appointment right now.</p>
              <?php else: ?>
                <table class="cm-table">
                  <thead>
                    <tr>
                      <th scope="col">Vet</th>
                      <th scope="col">Animal</th>
                      <th scope="col">Client</th>
                      <th scope="col">Type</th>
                      <th scope="col">Time</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($ongoingAppointments as $row): ?>
                      <?php if (!$row['hasAppointment']): ?>
                        <tr>
                          <td><?php echo htmlspecialchars($row['vet']); ?></td>
                          <td colspan="4" class="no-appointment">No current appointment</td>
                        </tr>
                      <?php else: ?>
                        <tr>
                          <td><?php echo htmlspecialchars($row['vet']); ?></td>
                          <td><?php echo htmlspecialchars($row['animal']); ?></td>
                          <td><?php echo htmlspecialchars($row['client']); ?></td>
                          <td><?php echo htmlspecialchars($row['type']); ?></td>
                          <td><?php echo htmlspecialchars($row['time_range']); ?></td>
                        </tr>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              <?php endif; ?>
            </div>
          </section>
        </div>

        <!-- RIGHT: Staff on Duty -->
        <aside class="right-col">
          <section class="card staff-card">
            <h2>Staff on Duty Today</h2>
            <div class="staff-body">
              <?php if (empty($staff)): ?>
                <p>No staff scheduled today.</p>
              <?php else: ?>
                <?php foreach ($staff as $role => $people): ?>
                  <div class="staff-block">
                    <h3><?php echo htmlspecialchars($role); ?></h3>
                    <ul class="staff-list">
                      <?php foreach ($people as $person): ?>
                        <li class="pill-row">
                          <span class="dot <?php echo $person['status']; ?>" aria-label="<?php echo $person['status']; ?> status"></span>
                          <span class="pill-name"><?php echo htmlspecialchars($person['name']); ?></span>
                          <span class="pill-time"><?php echo $person['time']; ?></span>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
            <button type="button" class="btn-primary btn-full" onclick="openEditStaffModal()" aria-label="Edit staff on duty">✏️ Edit Staff</button>
          </section>
        </aside>
      </section>
    </div>
  </main>

  <!-- Edit Staff Modal -->
  <div id="editStaffModal" class="modal-overlay" style="display: none;">
    <div class="modal-content-staff">
      <div class="modal-header">
        <h2>Edit Today's Staff Schedule</h2>
        <button class="modal-close" onclick="closeEditStaffModal()">&times;</button>
      </div>
      <div class="modal-body">
        <p class="modal-note">Select staff members working today and assign their shift times. At least 1 receptionist is required.</p>
        
        <!-- Receptionists -->
        <div class="staff-section">
          <h3>Receptionists <span style="color: #ef4444;">*</span></h3>
          <div id="receptionists-list" class="editable-staff-list">
            <!-- Dynamic content will be loaded here -->
          </div>
        </div>

        <!-- Veterinary Assistants -->
        <div class="staff-section">
          <h3>Veterinary Assistants</h3>
          <div id="assistants-list" class="editable-staff-list">
            <!-- Dynamic content will be loaded here -->
          </div>
        </div>

        <!-- Front Desk -->
        <div class="staff-section">
          <h3>Front Desk</h3>
          <div id="frontdesk-list" class="editable-staff-list">
            <!-- Dynamic content will be loaded here -->
          </div>
        </div>

        <!-- Support Staff -->
        <div class="staff-section">
          <h3>Support Staff</h3>
          <div id="support-list" class="editable-staff-list">
            <!-- Dynamic content will be loaded here -->
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-secondary" onclick="closeEditStaffModal()">Cancel</button>
        <button type="button" class="btn-primary" onclick="saveStaffChanges()">Save Changes</button>
      </div>
    </div>
  </div>

  <?php if (!empty($allStaff)): ?>
    <!-- All Staff Count: <?php echo count($allStaff); ?> -->
  <?php else: ?>
    <!-- Warning: $allStaff is empty or not set -->
  <?php endif; ?>

  <script>
    const allStaffMembers = <?php echo json_encode($allStaff ?? []); ?>;
    const currentDutyStaff = <?php echo json_encode($staff ?? []); ?>;

    const roleMapping = {
      'Receptionist': 'receptionists',
      'Veterinary Assistant': 'assistants',
      'Front Desk': 'frontdesk',
      'Support Staff': 'support'
    };

    function openEditStaffModal() {
      document.getElementById('editStaffModal').style.display = 'flex';
      loadStaffMembers();
    }

    function closeEditStaffModal() {
      document.getElementById('editStaffModal').style.display = 'none';
    }

    function loadStaffMembers() {
      const staffByRole = {
        'receptionists': [],
        'assistants': [],
        'frontdesk': [],
        'support': []
      };
      
      const onDutyMap = new Map();
      Object.keys(currentDutyStaff).forEach(roleKey => {
        const staffList = currentDutyStaff[roleKey];
        const staffArray = Array.isArray(staffList) ? staffList : Object.values(staffList);
        staffArray.forEach(staff => {
          onDutyMap.set(staff.name, staff.time || '08:00 – 16:00');
        });
      });
      
      allStaffMembers.forEach(member => {
        const category = roleMapping[member.role];
        if (category && member.status === 'Active') {
          const isOnDuty = onDutyMap.has(member.name);
          staffByRole[category].push({
            id: member.id,
            name: member.name,
            photo: member.photo,
            role: member.role,
            checked: isOnDuty,
            time: isOnDuty ? onDutyMap.get(member.name) : '08:00 – 16:00'
          });
        }
      });
      
      ['receptionists', 'assistants', 'frontdesk', 'support'].forEach(category => {
        const container = document.getElementById(`${category}-list`);
        container.innerHTML = '';
        
        if (staffByRole[category].length === 0) {
          container.innerHTML = '<p class="no-staff">No staff members available in this category</p>';
          return;
        }
        
        staffByRole[category].forEach((member) => {
          const row = createStaffRow(member);
          container.appendChild(row);
        });
      });
    }

    function createStaffRow(member) {
      const div = document.createElement('div');
      div.className = 'staff-edit-row';
      div.innerHTML = `
        <input type="checkbox" 
               id="staff-${member.id}" 
               class="staff-checkbox" 
               data-id="${member.id}"
               data-name="${member.name}"
               data-role="${member.role}"
               ${member.checked ? 'checked' : ''}>
        <label for="staff-${member.id}" class="staff-label">
          <img src="${member.photo}" alt="${member.name}" class="staff-photo-small">
          <span class="staff-name">${member.name}</span>
        </label>
        <input type="text" 
               value="${member.time}" 
               placeholder="08:00 – 16:00" 
               class="staff-time-input" 
               data-id="${member.id}"
               ${member.checked ? '' : 'disabled'}>
      `;
      
      const checkbox = div.querySelector('.staff-checkbox');
      const timeInput = div.querySelector('.staff-time-input');
      checkbox.addEventListener('change', function() {
        timeInput.disabled = !this.checked;
        if (this.checked && !timeInput.value) {
          timeInput.value = '08:00 – 16:00';
        }
      });
      
      return div;
    }

    function saveStaffChanges() {
      const selectedStaff = [];
      document.querySelectorAll('.staff-checkbox:checked').forEach(checkbox => {
        const id = checkbox.dataset.id;
        const name = checkbox.dataset.name;
        const role = checkbox.dataset.role;
        const timeInput = document.querySelector(`.staff-time-input[data-id="${id}"]`);
        const time = timeInput ? timeInput.value : '08:00 – 16:00';
        
        selectedStaff.push({
          id: parseInt(id),
          name: name,
          role: role,
          time: time
        });
      });

      // Check if at least one receptionist is selected
      const receptionistCount = selectedStaff.filter(s => s.role === 'Receptionist').length;
      if (receptionistCount === 0) {
        alert('At least 1 receptionist must be on duty!');
        return;
      }

      if (selectedStaff.length === 0) {
        alert('Please select at least one staff member');
        return;
      }

      // Save to database
      fetch('/PETVET/api/clinic-manager/save-duty-staff.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
          staff: selectedStaff, 
          date: '<?php echo date('Y-m-d'); ?>' 
        })
      })
      .then(response => {
        if (!response.ok) {
          return response.json().then(err => {
            throw new Error(err.message || 'Server error');
          });
        }
        return response.json();
      })
      .then(data => {
        if (data.success) {
          alert('Staff schedule saved successfully! (' + data.count + ' staff members on duty)');
          closeEditStaffModal();
          location.reload();
        } else {
          alert('Error: ' + (data.message || 'Failed to save staff schedule'));
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred: ' + error.message);
      });
    }

    window.onclick = function(event) {
      const modal = document.getElementById('editStaffModal');
      if (event.target === modal) {
        closeEditStaffModal();
      }
    }
  </script>

  <style>
    .modal-overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.5);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 1000;
    }

    .modal-content-staff {
      background: white;
      border-radius: 12px;
      width: 90%;
      max-width: 700px;
      max-height: 85vh;
      display: flex;
      flex-direction: column;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
      padding: 24px;
      border-bottom: 2px solid #e2e8f0;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .modal-header h2 {
      margin: 0;
      font-size: 1.5rem;
      font-weight: 700;
      color: #1e293b;
    }

    .modal-close {
      background: none;
      border: none;
      font-size: 2rem;
      color: #64748b;
      cursor: pointer;
      line-height: 1;
      padding: 0;
      width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 6px;
      transition: all 0.2s;
    }

    .modal-close:hover {
      background: #f1f5f9;
      color: #1e293b;
    }

    .modal-body {
      padding: 24px;
      overflow-y: auto;
      flex: 1;
    }

    .modal-note {
      background: #eff6ff;
      border-left: 4px solid #3b82f6;
      padding: 12px 16px;
      margin-bottom: 20px;
      border-radius: 6px;
      color: #1e40af;
      font-size: 14px;
      line-height: 1.5;
    }

    .staff-section {
      margin-bottom: 28px;
    }

    .staff-section h3 {
      font-size: 1rem;
      font-weight: 700;
      color: #1e293b;
      margin-bottom: 12px;
      text-transform: uppercase;
      font-size: 0.875rem;
      letter-spacing: 0.05em;
      color: #64748b;
    }

    .editable-staff-list {
      display: flex;
      flex-direction: column;
      gap: 10px;
      margin-bottom: 12px;
    }

    .staff-edit-row {
      display: grid;
      grid-template-columns: auto 1fr auto;
      gap: 12px;
      align-items: center;
      padding: 12px;
      background: #f8fafc;
      border-radius: 8px;
      margin-bottom: 8px;
      transition: all 0.2s;
    }

    .staff-edit-row:hover {
      background: #f1f5f9;
    }

    .staff-checkbox {
      width: 20px;
      height: 20px;
      cursor: pointer;
      accent-color: #3b82f6;
    }

    .staff-label {
      display: flex;
      align-items: center;
      gap: 10px;
      cursor: pointer;
      flex: 1;
    }

    .staff-photo-small {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      object-fit: cover;
    }

    .staff-name {
      font-weight: 600;
      color: #1e293b;
      font-size: 14px;
    }

    .staff-time-input {
      padding: 8px 12px;
      border: 1px solid #cbd5e1;
      border-radius: 6px;
      font-size: 13px;
      font-family: inherit;
      width: 150px;
      transition: all 0.2s;
    }

    .staff-time-input:focus {
      outline: none;
      border-color: #3b82f6;
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .staff-time-input:disabled {
      background: #e2e8f0;
      color: #94a3b8;
      cursor: not-allowed;
    }

    .no-staff {
      color: #64748b;
      font-style: italic;
      text-align: center;
      padding: 20px;
    }

    .modal-footer {
      padding: 20px 24px;
      border-top: 2px solid #e2e8f0;
      display: flex;
      justify-content: flex-end;
      gap: 12px;
    }

    .btn-secondary {
      background: #f1f5f9;
      color: #475569;
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
    }

    .btn-secondary:hover {
      background: #e2e8f0;
    }

    .modal-footer .btn-primary {
      margin: 0;
      padding: 10px 24px;
      width: auto;
    }
  </style>
</body>
</html>