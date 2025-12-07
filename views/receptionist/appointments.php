<?php
$currentPage = basename($_SERVER['PHP_SELF']);

// Set user role for shared components
$userRole = 'receptionist';

// Include the shared appointments model
require_once __DIR__ . '/../../models/SharedAppointmentsModel.php';

// Initialize the model
$appointmentsModel = new SharedAppointmentsModel();

// Get filter parameters
$selectedVet = $_GET['vet'] ?? 'all';
$view = $_GET['view'] ?? 'today';

// Get data from model
$appointments = $appointmentsModel->getAppointments($selectedVet);
$pendingAppointments = $appointmentsModel->getPendingAppointments();
$vetNames = $appointmentsModel->getVetNames();
$weekDays = $appointmentsModel->getWeekDates();
$monthDays = $appointmentsModel->getMonthDates();
$moduleName = $appointmentsModel->getModuleName($userRole);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Appointments | Receptionist</title>
  <link rel="stylesheet" href="/PETVET/public/css/clinic-manager/enhanced-global.css">
  <link rel="stylesheet" href="/PETVET/public/css/clinic-manager/appointments.css">
  <link rel="stylesheet" href="/PETVET/public/css/shared/appointments.css">
  <link rel="stylesheet" href="/PETVET/public/css/receptionist/appointments-receptionist.css">
</head>
<body>

<div class="main-content">
  <div class="page-header">
    <div>
      <h1 class="page-title">Appointments</h1>
      <p class="page-subtitle">Manage and schedule patient appointments</p>
    </div>
    <div>
      <button class="btn btn-primary" onclick="openAddModal()">
        ➕ New Appointment
      </button>
    </div>
  </div>

  <!-- Two Column Layout -->
  <div class="appointments-layout">
    <!-- Left: Pending Appointments -->
    <aside class="pending-appointments-sidebar">
      <div class="pending-header">
        <h3>Pending Requests</h3>
        <span class="pending-count"><?= count($pendingAppointments) ?></span>
        <span id="refreshIndicator" style="font-size: 12px; color: #10b981; margin-left: 10px; opacity: 0;">🔄 Updated</span>
      </div>
      
      <div class="pending-list">
        <?php if (empty($pendingAppointments)): ?>
          <div class="empty-state">
            <div class="empty-icon">📭</div>
            <p>No pending requests</p>
          </div>
        <?php else: ?>
          <?php foreach ($pendingAppointments as $pending): ?>
            <div class="pending-card" 
                 data-id="<?= $pending['id'] ?>" 
                 data-requested-at="<?= $pending['requested_at'] ?>"
                 data-clinic-id="<?= $pending['clinic_id'] ?>"
                 data-appointment-date="<?= $pending['requested_date'] ?>"
                 data-appointment-time="<?= $pending['requested_time'] ?>">
              <div class="pending-card-header">
                <div class="pending-pet-info">
                  <span class="pet-name"><?= htmlspecialchars($pending['pet']) ?></span>
                  <span class="pet-type"><?= htmlspecialchars($pending['pet_type']) ?></span>
                </div>
                <span class="pending-time-ago" data-timestamp="<?= $pending['requested_at'] ?>"><?= timeAgo($pending['requested_at']) ?></span>
              </div>
              
              <div class="pending-card-body">
                <div class="pending-detail">
                  <span class="label">Owner:</span>
                  <span class="value"><?= htmlspecialchars($pending['owner']) ?></span>
                </div>
                <div class="pending-detail">
                  <span class="label">Type:</span>
                  <span class="value"><?= htmlspecialchars($pending['appointment_type']) ?></span>
                </div>
                <div class="pending-detail">
                  <span class="label">Date:</span>
                  <span class="value"><?= date('M j, Y', strtotime($pending['requested_date'])) ?> at <?= date('g:i A', strtotime($pending['requested_time'])) ?></span>
                </div>
                <div class="pending-detail">
                  <span class="label">Vet:</span>
                  <span class="value"><?= htmlspecialchars($pending['requested_vet']) ?></span>
                </div>
              </div>
              
              <div class="pending-card-actions">
                <button class="btn-accept" onclick="acceptAppointment(<?= $pending['id'] ?>)">
                  ✓ Accept
                </button>
                <button class="btn-decline" onclick="declineAppointment(<?= $pending['id'] ?>)">
                  ✕ Decline
                </button>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </aside>

    <!-- Right: Calendar View -->
    <main class="calendar-section">
      <?php
        // Include complete shared appointments component
        include __DIR__ . '/../shared/appointments/appointments-complete.php';
      ?>
    </main>
  </div>
</div>

<script src="/PETVET/public/js/shared/appointments.js"></script>
<script src="/PETVET/public/js/receptionist/receptionist-booking.js"></script>
<script src="/PETVET/public/js/receptionist/appointments-receptionist.js"></script>
<script>
// Force immediate execution - don't wait for other scripts
(function() {
  'use strict';
  
  console.log('🔧 AJAX Refresh Script Loading...');
  
  
// AJAX auto-refresh for pending appointments
let lastAppointmentIds = new Set();

// Helper function for time ago
function timeAgo(datetime) {
  // Handle MySQL datetime format (YYYY-MM-DD HH:MM:SS) as UTC
  // Add 'Z' suffix to treat as UTC
  const isoFormat = datetime.replace(' ', 'T') + 'Z';
  const date = new Date(isoFormat);
  
  const now = new Date();
  const diff = Math.floor((now - date) / 1000);
  
  // Handle negative differences (future timestamps)
  if (diff < 0) return 'Just now';
  
  if (diff < 60) return 'Just now';
  if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
  if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
  return Math.floor(diff / 86400) + 'd ago';
}

// Function to update pending list
async function refreshPendingAppointments() {
  const refreshIndicator = document.getElementById('refreshIndicator');
  
  try {
    const response = await fetch('/PETVET/api/appointments/get-pending.php', {
      credentials: 'same-origin' // Include session cookies
    });
    
    if (!response.ok) {
      console.error('Failed to fetch pending appointments:', response.status, response.statusText);
      return;
    }
    
    const data = await response.json();
    console.log('AJAX Refresh - Fetched appointments:', data);
    
    // Show refresh indicator
    if (refreshIndicator) {
      refreshIndicator.style.opacity = '1';
      setTimeout(() => {
        refreshIndicator.style.opacity = '0';
      }, 1500);
    }
    
    if (data.success) {
      const pendingList = document.querySelector('.pending-list');
      const pendingCount = document.querySelector('.pending-count');
      const appointments = data.appointments;
      
      // Update count
      pendingCount.textContent = appointments.length;
      
      // Check for new appointments
      const currentIds = new Set(appointments.map(a => a.id));
      const hasNewAppointments = appointments.some(a => !lastAppointmentIds.has(a.id));
      lastAppointmentIds = currentIds;
      
      // Rebuild the list
      if (appointments.length === 0) {
        pendingList.innerHTML = `
          <div class="empty-state">
            <div class="empty-icon">📭</div>
            <p>No pending requests</p>
          </div>
        `;
      } else {
        let html = '';
        appointments.forEach(pending => {
          const requestedDate = new Date(pending.requested_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
          const requestedTime = new Date('2000-01-01 ' + pending.requested_time).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
          
          html += `
            <div class="pending-card" 
                 data-id="${pending.id}" 
                 data-requested-at="${pending.requested_at}"
                 data-clinic-id="${pending.clinic_id}"
                 data-appointment-date="${pending.requested_date}"
                 data-appointment-time="${pending.requested_time}">
              <div class="pending-card-header">
                <div class="pending-pet-info">
                  <span class="pet-name">${escapeHtml(pending.pet)}</span>
                  <span class="pet-type">${escapeHtml(pending.pet_type)}</span>
                </div>
                <span class="pending-time-ago" data-timestamp="${pending.requested_at}">${timeAgo(pending.requested_at)}</span>
              </div>
              
              <div class="pending-card-body">
                <div class="pending-detail">
                  <span class="label">Owner:</span>
                  <span class="value">${escapeHtml(pending.owner)}</span>
                </div>
                <div class="pending-detail">
                  <span class="label">Type:</span>
                  <span class="value">${escapeHtml(pending.appointment_type)}</span>
                </div>
                <div class="pending-detail">
                  <span class="label">Date:</span>
                  <span class="value">${requestedDate} at ${requestedTime}</span>
                </div>
                <div class="pending-detail">
                  <span class="label">Vet:</span>
                  <span class="value">${escapeHtml(pending.requested_vet)}</span>
                </div>
              </div>
              
              <div class="pending-card-actions">
                <button class="btn-accept" onclick="acceptAppointment(${pending.id})">
                  ✓ Accept
                </button>
                <button class="btn-decline" onclick="declineAppointment(${pending.id})">
                  ✕ Decline
                </button>
              </div>
            </div>
          `;
        });
        pendingList.innerHTML = html;
      }
      
      // Show notification if there are new appointments
      if (hasNewAppointments && lastAppointmentIds.size > 0) {
        console.log('🔔 NEW APPOINTMENT DETECTED!');
        // Flash the pending count
        pendingCount.style.animation = 'none';
        setTimeout(() => {
          pendingCount.style.animation = 'pulse 0.5s ease-in-out 3';
        }, 10);
      }
    } else {
      console.error('API returned error:', data);
    }
  } catch (error) {
    console.error('Error refreshing pending appointments:', error);
  }
}

// HTML escape function
function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

// Update time ago displays without full refresh
function updateTimeDisplays() {
  document.querySelectorAll('.pending-time-ago').forEach(timeEl => {
    const card = timeEl.closest('.pending-card');
    if (card && card.dataset.requestedAt) {
      timeEl.textContent = timeAgo(card.dataset.requestedAt);
    }
  });
}

// Initialize last IDs on page load
document.addEventListener('DOMContentLoaded', () => {
  console.log('🚀 Receptionist AJAX auto-refresh initialized');
  
  const cards = document.querySelectorAll('.pending-card');
  cards.forEach(card => {
    lastAppointmentIds.add(parseInt(card.dataset.id));
    // Store timestamp for time updates
    const timeAgoEl = card.querySelector('.pending-time-ago');
    if (timeAgoEl && timeAgoEl.dataset.timestamp) {
      card.dataset.requestedAt = timeAgoEl.dataset.timestamp;
    }
  });
  
  console.log('Initial pending appointments:', Array.from(lastAppointmentIds));
  
  // Refresh every 5 seconds
  setInterval(refreshPendingAppointments, 5000);
  console.log('✅ Auto-refresh every 5 seconds started');
  
  // Update time displays every 30 seconds
  setInterval(updateTimeDisplays, 30000);
  
  // Also do an immediate refresh after 2 seconds (to catch new bookings)
  setTimeout(() => {
    console.log('⏰ Running initial refresh check...');
    refreshPendingAppointments();
  }, 2000);
});

// Add pulse animation for count
const style = document.createElement('style');
style.textContent = `
  @keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.2); }
  }
`;
document.head.appendChild(style);

console.log('✅ AJAX Refresh Script Loaded Successfully');

})(); // End of IIFE
</script>
</body>
</html>

<?php
// Helper function for time ago
function timeAgo($datetime) {
    // Create DateTime objects in UTC to ensure consistent timezone comparison
    $now = new DateTime('now', new DateTimeZone('UTC'));
    $time = new DateTime($datetime, new DateTimeZone('UTC'));
    $diff = $now->getTimestamp() - $time->getTimestamp();
    
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . 'm ago';
    if ($diff < 86400) return floor($diff / 3600) . 'h ago';
    return floor($diff / 86400) . 'd ago';
}
?>

<script>
// Pass vet names to JavaScript for use in acceptance popup
window.availableVets = <?= json_encode($vetNames); ?>;
</script>