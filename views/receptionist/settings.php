<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Settings | Receptionist</title>
  <link rel="stylesheet" href="/PETVET/public/css/clinic-manager/enhanced-global.css">
  <style>
    .settings-container {
      max-width: 800px;
      margin: 0 auto;
    }
    
    .settings-section {
      background: white;
      border-radius: var(--border-radius-lg);
      padding: 24px;
      margin-bottom: 24px;
      box-shadow: var(--shadow-sm);
    }
    
    .section-title {
      font-size: 1.25rem;
      font-weight: 700;
      color: var(--gray-900);
      margin-bottom: 16px;
      padding-bottom: 8px;
      border-bottom: 2px solid var(--primary);
    }
    
    .form-group {
      margin-bottom: 20px;
    }
    
    .form-label {
      display: block;
      margin-bottom: 6px;
      font-weight: 600;
      color: var(--gray-700);
      font-size: 14px;
    }
    
    .form-input, .form-select, .form-textarea {
      width: 100%;
      padding: 12px 16px;
      border: 2px solid var(--gray-200);
      border-radius: var(--border-radius);
      font-size: 16px;
      transition: var(--transition);
      background: white;
    }
    
    .form-input:focus, .form-select:focus, .form-textarea:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }
    
    .btn {
      padding: 12px 24px;
      border: none;
      border-radius: var(--border-radius);
      font-weight: 600;
      cursor: pointer;
      transition: var(--transition);
      font-size: 14px;
    }
    
    .btn-primary {
      background: var(--primary);
      color: white;
    }
    
    .btn-primary:hover {
      background: var(--primary-dark);
      transform: translateY(-1px);
    }
    
    .btn-secondary {
      background: var(--gray-200);
      color: var(--gray-700);
    }
    
    .btn-secondary:hover {
      background: var(--gray-300);
    }
    
    @media (max-width: 768px) {
      .form-row {
        grid-template-columns: 1fr;
      }
      
      .settings-section {
        padding: 16px;
      }
    }
  </style>
</head>
<body>

<div class="main-content">
  <div class="page-header">
    <div>
      <h1 class="page-title">Settings</h1>
      <p class="page-subtitle">Manage your account and preferences</p>
    </div>
  </div>

  <div class="settings-container">
    
    <!-- Profile Settings -->
    <div class="settings-section">
      <h2 class="section-title">Profile Information</h2>
      <form id="profileForm">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="firstName">First Name</label>
            <input type="text" id="firstName" name="firstName" class="form-input" value="Jane" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="lastName">Last Name</label>
            <input type="text" id="lastName" name="lastName" class="form-input" value="Smith" required>
          </div>
        </div>
        
        <div class="form-group">
          <label class="form-label" for="email">Email Address</label>
          <input type="email" id="email" name="email" class="form-input" value="jane.smith@clinic.com" required>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" class="form-input" value="0712345678">
          </div>
          <div class="form-group">
            <label class="form-label" for="position">Position</label>
            <input type="text" id="position" name="position" class="form-input" value="Front Desk Receptionist" readonly>
          </div>
        </div>
        
        <div class="form-group">
          <button type="submit" class="btn btn-primary">Update Profile</button>
        </div>
      </form>
    </div>

    <!-- Work Preferences -->
    <div class="settings-section">
      <h2 class="section-title">Work Preferences</h2>
      <form id="preferencesForm">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="shiftPreference">Preferred Shift</label>
            <select id="shiftPreference" name="shiftPreference" class="form-select">
              <option value="morning">Morning (8 AM - 4 PM)</option>
              <option value="afternoon" selected>Afternoon (12 PM - 8 PM)</option>
              <option value="evening">Evening (4 PM - 12 AM)</option>
              <option value="flexible">Flexible</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label" for="timezone">Timezone</label>
            <select id="timezone" name="timezone" class="form-select">
              <option value="Asia/Colombo" selected>Asia/Colombo (Sri Lanka)</option>
              <option value="Asia/Kolkata">Asia/Kolkata (India)</option>
              <option value="UTC">UTC</option>
            </select>
          </div>
        </div>
        
        <div class="form-group">
          <label class="form-label" for="skills">Skills & Experience</label>
          <textarea id="skills" name="skills" class="form-textarea" placeholder="List your relevant skills, software experience, etc.">Customer service, appointment scheduling, veterinary software (VetBlue), Microsoft Office, phone support</textarea>
        </div>
        
        <div class="form-group">
          <button type="submit" class="btn btn-primary">Save Preferences</button>
        </div>
      </form>
    </div>

    <!-- Security Settings -->
    <div class="settings-section">
      <h2 class="section-title">Security</h2>
      <form id="securityForm">
        <div class="form-group">
          <label class="form-label" for="currentPassword">Current Password</label>
          <input type="password" id="currentPassword" name="currentPassword" class="form-input" required>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="newPassword">New Password</label>
            <input type="password" id="newPassword" name="newPassword" class="form-input">
          </div>
          <div class="form-group">
            <label class="form-label" for="confirmPassword">Confirm New Password</label>
            <input type="password" id="confirmPassword" name="confirmPassword" class="form-input">
          </div>
        </div>
        
        <div class="form-group">
          <button type="submit" class="btn btn-primary">Change Password</button>
        </div>
      </form>
    </div>

    <!-- Notification Settings -->
    <div class="settings-section">
      <h2 class="section-title">Notifications</h2>
      <form id="notificationsForm">
        <div class="form-group">
          <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
            <input type="checkbox" checked>
            <span>Email notifications for new appointments</span>
          </label>
        </div>
        
        <div class="form-group">
          <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
            <input type="checkbox" checked>
            <span>SMS alerts for emergency appointments</span>
          </label>
        </div>
        
        <div class="form-group">
          <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
            <input type="checkbox">
            <span>Daily appointment summary emails</span>
          </label>
        </div>
        
        <div class="form-group">
          <button type="submit" class="btn btn-primary">Save Notification Settings</button>
        </div>
      </form>
    </div>

  </div>
</div>

<script>
// Form submission handlers
document.getElementById('profileForm').addEventListener('submit', function(e) {
  e.preventDefault();
  alert('Profile updated successfully!');
});

document.getElementById('preferencesForm').addEventListener('submit', function(e) {
  e.preventDefault();
  alert('Preferences saved successfully!');
});

document.getElementById('securityForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const newPassword = document.getElementById('newPassword').value;
  const confirmPassword = document.getElementById('confirmPassword').value;
  
  if (newPassword && newPassword !== confirmPassword) {
    alert('New passwords do not match!');
    return;
  }
  
  alert('Password changed successfully!');
  e.target.reset();
});

document.getElementById('notificationsForm').addEventListener('submit', function(e) {
  e.preventDefault();
  alert('Notification settings saved successfully!');
});
</script>

</body>
</html>