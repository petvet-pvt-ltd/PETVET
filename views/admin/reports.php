<?php
// Admin Reports - Simple version with downloadable reports
require_once __DIR__ . '/../../config/connect.php';
$pdo = db();
?>
<link rel="stylesheet" href="/PETVET/public/css/admin/styles.css">
<link rel="stylesheet" href="/PETVET/public/css/admin/dashboard.css">

<div class="main-content">
  <?php include __DIR__ . '/../shared/components/user-welcome-header.php'; ?>
  
  <section class="overview">
    <div style="margin-bottom: 30px;">
      <h2 style="font-size: 28px; font-weight: 700; color: #1F2937; margin: 0 0 8px 0;">Reports</h2>
      <p style="color: #6B7280; margin: 0;">Generate and download system reports</p>
    </div>

    <!-- Report Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; margin-bottom: 30px;">
      
      <!-- Users Report -->
      <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #E5E7EB;">
        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 16px;">
          <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
          </div>
          <div>
            <h3 style="font-size: 18px; font-weight: 600; color: #1F2937; margin: 0 0 4px 0;">Users Report</h3>
            <p style="font-size: 14px; color: #6B7280; margin: 0;">All registered users by role</p>
          </div>
        </div>
        <div style="display: flex; gap: 12px;">
          <a href="/PETVET/api/admin/download-report.php?type=users&format=csv" style="flex: 1; text-decoration: none;">
            <button style="width: 100%; padding: 10px 16px; background: #4F46E5; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#4338CA'" onmouseout="this.style.background='#4F46E5'">
              Download CSV
            </button>
          </a>
          <button onclick="viewReport('users')" style="flex: 1; padding: 10px 16px; background: white; color: #4F46E5; border: 2px solid #4F46E5; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#EEF2FF'" onmouseout="this.style.background='white'">
            View Report
          </button>
        </div>
      </div>

      <!-- Clinics Report -->
      <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #E5E7EB;">
        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 16px;">
          <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
              <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2zM12 11v6M9 14h6"/>
            </svg>
          </div>
          <div>
            <h3 style="font-size: 18px; font-weight: 600; color: #1F2937; margin: 0 0 4px 0;">Clinics Report</h3>
            <p style="font-size: 14px; color: #6B7280; margin: 0;">All registered clinics</p>
          </div>
        </div>
        <div style="display: flex; gap: 12px;">
          <a href="/PETVET/api/admin/download-report.php?type=clinics&format=csv" style="flex: 1; text-decoration: none;">
            <button style="width: 100%; padding: 10px 16px; background: #EC4899; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#DB2777'" onmouseout="this.style.background='#EC4899'">
              Download CSV
            </button>
          </a>
          <button onclick="viewReport('clinics')" style="flex: 1; padding: 10px 16px; background: white; color: #EC4899; border: 2px solid #EC4899; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#FCE7F3'" onmouseout="this.style.background='white'">
            View Report
          </button>
        </div>
      </div>

      <!-- Appointments Report -->
      <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #E5E7EB;">
        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 16px;">
          <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/>
              <line x1="8" y1="2" x2="8" y2="6"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
          </div>
          <div>
            <h3 style="font-size: 18px; font-weight: 600; color: #1F2937; margin: 0 0 4px 0;">Appointments Report</h3>
            <p style="font-size: 14px; color: #6B7280; margin: 0;">All appointments data</p>
          </div>
        </div>
        <div style="display: flex; gap: 12px;">
          <a href="/PETVET/api/admin/download-report.php?type=appointments&format=csv" style="flex: 1; text-decoration: none;">
            <button style="width: 100%; padding: 10px 16px; background: #06B6D4; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#0891B2'" onmouseout="this.style.background='#06B6D4'">
              Download CSV
            </button>
          </a>
          <button onclick="viewReport('appointments')" style="flex: 1; padding: 10px 16px; background: white; color: #06B6D4; border: 2px solid #06B6D4; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#CFFAFE'" onmouseout="this.style.background='white'">
            View Report
          </button>
        </div>
      </div>

      <!-- Activity Report -->
      <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #E5E7EB;">
        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 16px;">
          <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
              <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
            </svg>
          </div>
          <div>
            <h3 style="font-size: 18px; font-weight: 600; color: #1F2937; margin: 0 0 4px 0;">Activity Report</h3>
            <p style="font-size: 14px; color: #6B7280; margin: 0;">System activity summary</p>
          </div>
        </div>
        <div style="display: flex; gap: 12px;">
          <a href="/PETVET/api/admin/download-report.php?type=activity&format=csv" style="flex: 1; text-decoration: none;">
            <button style="width: 100%; padding: 10px 16px; background: #10B981; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#059669'" onmouseout="this.style.background='#10B981'">
              Download CSV
            </button>
          </a>
          <button onclick="viewReport('activity')" style="flex: 1; padding: 10px 16px; background: white; color: #10B981; border: 2px solid #10B981; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#D1FAE5'" onmouseout="this.style.background='white'">
            View Report
          </button>
        </div>
      </div>

    </div>

    <!-- Report Viewer Modal -->
    <div id="reportModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; padding: 20px; overflow-y: auto;">
      <div style="max-width: 1200px; margin: 40px auto; background: white; border-radius: 16px; padding: 32px; position: relative;">
        <button onclick="closeReport()" style="position: absolute; top: 16px; right: 16px; width: 40px; height: 40px; border: none; background: #F3F4F6; border-radius: 8px; font-size: 24px; cursor: pointer; color: #6B7280;" onmouseover="this.style.background='#E5E7EB'" onmouseout="this.style.background='#F3F4F6'">&times;</button>
        <h2 id="reportTitle" style="font-size: 24px; font-weight: 700; color: #1F2937; margin: 0 0 24px 0;">Report</h2>
        <div id="reportContent" style="overflow-x: auto;">
          <!-- Report content will be loaded here -->
        </div>
      </div>
    </div>

  </section>
</div>

<script>
function viewReport(type) {
  const modal = document.getElementById('reportModal');
  const title = document.getElementById('reportTitle');
  const content = document.getElementById('reportContent');
  
  title.textContent = type.charAt(0).toUpperCase() + type.slice(1) + ' Report';
  content.innerHTML = '<p style="text-align:center;padding:40px;color:#999;">Loading...</p>';
  modal.style.display = 'block';
  
  // Fetch report data
  fetch('/PETVET/api/admin/view-report.php?type=' + type, {
    credentials: 'same-origin'
  })
    .then(response => {
      if (!response.ok) {
        throw new Error('HTTP ' + response.status);
      }
      return response.json();
    })
    .then(data => {
      if (data.success) {
        content.innerHTML = data.html;
      } else {
        content.innerHTML = '<p style="text-align:center;padding:40px;color:#EF4444;">Error: ' + (data.error || 'Unknown error') + '</p>';
      }
    })
    .catch(error => {
      content.innerHTML = '<p style="text-align:center;padding:40px;color:#EF4444;">Network error: ' + error.message + '</p>';
    });
}

function closeReport() {
  document.getElementById('reportModal').style.display = 'none';
}

// Close modal on outside click
document.getElementById('reportModal')?.addEventListener('click', function(e) {
  if (e.target === this) {
    closeReport();
  }
});
</script>
