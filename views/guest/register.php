<?php
/* Registration page */
require_once __DIR__ . '/../../config/auth_helper.php';

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
    redirectToDashboard();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Join PetVet - Registration</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #265B7F, #BCE3F5);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .registration-container {
      background: white;
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
      overflow: hidden;
      max-width: 900px;
      width: 100%;
      display: grid;
      grid-template-columns: 1fr 2fr;
      min-height: 600px;
    }

    .left-panel {
      background: linear-gradient(45deg, #1a4059, #265B7F);
      color: white;
      padding: 40px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      position: relative;
      overflow: hidden;
    }

    .left-panel::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -50%;
      width: 200%;
      height: 200%;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="20" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="40" r="1.5" fill="rgba(255,255,255,0.1)"/><circle cx="90" cy="70" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="10" cy="80" r="1" fill="rgba(255,255,255,0.1)"/></svg>');
      animation: float 20s infinite linear;
    }

    @keyframes float {
      0% { transform: translateX(-100px) translateY(-100px); }
      100% { transform: translateX(100px) translateY(100px); }
    }

    .left-content {
      position: relative;
      z-index: 2;
    }

    .logo {
      font-size: 2.5em;
      font-weight: bold;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .welcome-text h2 {
      font-size: 1.8em;
      margin-bottom: 15px;
      font-weight: 600;
    }

    .welcome-text p {
      font-size: 1.1em;
      opacity: 0.9;
      line-height: 1.6;
    }

    .back-home {
      position: absolute;
      top: 20px;
      left: 20px;
      background: rgba(255, 255, 255, 0.2);
      color: white;
      border: 3px solid #315cfd;
      padding: 10px 20px;
      border-radius: 25px;
      cursor: pointer;
      font-size: 14px;
      backdrop-filter: blur(10px);
      transition: all 0.3s ease;
      font-weight: 550;
      z-index: 10;
    }

    .back-home:hover {
      background: #315cfd;
      color: white;
      transform: translateY(-2px);
    }

    .left-content {
      position: relative;
      z-index: 2;
      margin-top: 60px;
    }

    .logo {
      font-size: 2.5em;
      font-weight: bold;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .right-panel {
      padding: 40px;
      display: flex;
      flex-direction: column;
    }

    .progress-container {
      margin-bottom: 30px;
    }

    .progress-bar {
      width: 100%;
      height: 6px;
      background: #f1f5f9;
      border-radius: 3px;
      overflow: hidden;
      margin-bottom: 20px;
    }

    .progress-fill {
      height: 100%;
      background: #2563eb;
      border-radius: 3px;
      transition: width 0.4s ease;
      width: 33.33%;
    }

    .step-indicators {
      display: flex;
      justify-content: space-between;
      margin-bottom: 10px;
    }

    .step-indicator {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 14px;
      color: #64748b;
    }

    .step-indicator.active {
      color: #2563eb;
      font-weight: 600;
    }

    .step-indicator.completed {
      color: #10b981;
    }

    .step-number {
      width: 24px;
      height: 24px;
      border-radius: 50%;
      background: #f1f5f9;
      color: #64748b;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      font-weight: bold;
    }

    .step-indicator.active .step-number {
      background: #2563eb;
      color: white;
    }

    .step-indicator.completed .step-number {
      background: #10b981;
      color: white;
    }

    .form-content {
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .step {
      display: none;
    }

    .step.active {
      display: block;
      animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
      from { opacity: 0; transform: translateX(20px); }
      to { opacity: 1; transform: translateX(0); }
    }

    .step-title {
      font-size: 1.8em;
      color: #1e293b;
      margin-bottom: 8px;
      font-weight: 600;
    }

    .step-description {
      color: #64748b;
      margin-bottom: 30px;
      font-size: 1.1em;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px;
    }

    .input-group {
      position: relative;
    }

    .form-input {
      width: 100%;
      padding: 15px 20px;
      border: 2px solid #e2e8f0;
      border-radius: 12px;
      font-size: 16px;
      transition: all 0.3s ease;
      background: #ffffff;
    }

    .form-input:focus {
      outline: none;
      border-color: #2563eb;
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .form-input.error {
      border-color: #ef4444;
      box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }

    .form-input.success {
      border-color: #10b981;
    }

    .error-message {
      color: #ef4444;
      font-size: 14px;
      margin-top: 5px;
      display: block;
    }

    .role-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 15px;
      margin: 20px 0;
    }

    .role-card {
      border: 2px solid #e2e8f0;
      border-radius: 16px;
      padding: 25px 20px;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s ease;
      background: white;
      position: relative;
      overflow: hidden;
    }

    .role-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: #2563eb;
      transform: scaleX(0);
      transition: transform 0.3s ease;
    }

    .role-card:hover {
      border-color: #2563eb;
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(37, 99, 235, 0.15);
    }

    .role-card:hover::before {
      transform: scaleX(1);
    }

    .role-card.selected {
      border-color: #2563eb;
      background: linear-gradient(135f, #f8faff, #f0f4ff);
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(37, 99, 235, 0.15);
    }

    .role-card.selected::before {
      transform: scaleX(1);
    }

    .role-icon {
      font-size: 2.5em;
      margin-bottom: 15px;
      display: block;
    }

    .role-title {
      font-size: 1.2em;
      font-weight: 600;
      color: #1e293b;
      margin-bottom: 8px;
    }

    .role-description {
      color: #64748b;
      font-size: 14px;
      line-height: 1.4;
    }

    .role-card input[type="checkbox"] {
      display: none;
    }

    .role-forms {
      margin-top: 30px;
    }

    .role-form {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      padding: 25px;
      margin-bottom: 20px;
    }

    .role-form-title {
      font-size: 1.3em;
      font-weight: 600;
      color: #1e293b;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .form-textarea {
      width: 100%;
      padding: 15px 20px;
      border: 2px solid #e2e8f0;
      border-radius: 12px;
      font-size: 16px;
      resize: vertical;
      min-height: 100px;
      font-family: inherit;
      transition: all 0.3s ease;
    }

    .form-textarea:focus {
      outline: none;
      border-color: #2563eb;
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .form-select {
      width: 100%;
      padding: 15px 20px;
      border: 2px solid #e2e8f0;
      border-radius: 12px;
      font-size: 16px;
      background: white;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .form-select:focus {
      outline: none;
      border-color: #2563eb;
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .file-upload-area {
      border: 2px dashed #cbd5e1;
      border-radius: 12px;
      padding: 30px 20px;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s ease;
      background: white;
      position: relative;
    }

    .file-upload-area:hover {
      border-color: #2563eb;
      background: #f8faff;
    }

    .file-upload-area.dragover {
      border-color: #2563eb;
      background: #f0f4ff;
      transform: scale(1.02);
    }

    .file-upload-icon {
      font-size: 2em;
      color: #64748b;
      margin-bottom: 10px;
    }

    .file-upload-text {
      color: #64748b;
      margin-bottom: 5px;
    }

    .file-upload-subtext {
      color: #94a3b8;
      font-size: 14px;
    }

    .file-selected {
      color: #10b981;
      font-weight: 600;
    }

    .checkbox-group {
      display: flex;
      align-items: center;
      gap: 10px;
      margin: 15px 0;
    }

    .custom-checkbox {
      width: 20px;
      height: 20px;
      border: 2px solid #cbd5e1;
      border-radius: 4px;
      cursor: pointer;
      position: relative;
      transition: all 0.3s ease;
    }

    .custom-checkbox.checked {
      background: #2563eb;
      border-color: #2563eb;
    }

    .custom-checkbox.checked::after {
      content: '✓';
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      color: white;
      font-size: 12px;
      font-weight: bold;
    }

    .navigation-buttons {
      display: flex;
      justify-content: space-between;
      margin-top: 40px;
      padding-top: 20px;
      border-top: 1px solid #e2e8f0;
    }

    .btn {
      padding: 12px 30px;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .btn-secondary {
      background: #f1f5f9;
      color: #64748b;
    }

    .btn-secondary:hover {
      background: #e2e8f0;
      transform: translateY(-2px);
    }

    .btn-primary {
      background: #2563eb;
      color: white;
    }

    .btn-primary:hover {
      background: #1d4ed8;
      transform: translateY(-2px);
    }

    .btn:disabled {
      opacity: 0.6;
      cursor: not-allowed;
      transform: none !important;
    }

    @media (max-width: 768px) {
      .registration-container {
        grid-template-columns: 1fr;
        max-width: 500px;
      }

      .left-panel {
        padding: 30px;
        text-align: center;
      }

      .right-panel {
        padding: 30px;
      }

      .form-row {
        grid-template-columns: 1fr;
      }

      .role-grid {
        grid-template-columns: repeat(2, 1fr);
      }

      .step-indicators {
        flex-direction: column;
        gap: 10px;
      }
    }

    @media (max-width: 480px) {
      body {
        padding: 10px;
      }

      .registration-container {
        border-radius: 0;
        min-height: 100vh;
      }

      .role-grid {
        grid-template-columns: 1fr;
      }

      .navigation-buttons {
        flex-direction: column;
        gap: 15px;
      }
    }
  </style>
</head>
<body>
  <div class="registration-container">
    <!-- Left Panel -->
    <div class="left-panel">
      <button class="back-home" onclick="window.location.href='/PETVET/index.php?module=guest&page=home'">
        Home
      </button>
      
      <div class="left-content">
        <div class="logo">PETVET</div>
        <div class="welcome-text">
          <h2>Join Our Community</h2>
          <p>Create your account and connect with pet lovers and professional service providers in your area. Whether you're a pet owner or provide pet services, we've got you covered.</p>
        </div>
      </div>
    </div>

    <!-- Right Panel -->
    <div class="right-panel">
      <!-- Progress Bar -->
      <div class="progress-container">
        <div class="progress-bar">
          <div class="progress-fill" id="progressFill"></div>
        </div>
        <div class="step-indicators">
          <div class="step-indicator active" id="indicator-1">
            <div class="step-number">1</div>
            <span>Basic Info</span>
          </div>
          <div class="step-indicator" id="indicator-2">
            <div class="step-number">2</div>
            <span>Your Roles</span>
          </div>
          <div class="step-indicator" id="indicator-3">
            <div class="step-number">3</div>
            <span>Complete Setup</span>
          </div>
        </div>
      </div>

      <!-- Form -->
      <form class="form-content" action="/PETVET/index.php?module=guest&page=register" method="post" enctype="multipart/form-data">
        
        <!-- Step 1: Basic Information -->
        <div class="step active" id="step-1">
          <h2 class="step-title">Let's get started</h2>
          <p class="step-description">Tell us a bit about yourself to create your account.</p>
          
          <div class="form-group">
            <div class="form-row">
              <div class="input-group">
                <input type="text" class="form-input" id="fname" name="fname" placeholder="First Name" required>
                <span class="error-message" id="fname-error"></span>
              </div>
              <div class="input-group">
                <input type="text" class="form-input" id="lname" name="lname" placeholder="Last Name" required>
                <span class="error-message" id="lname-error"></span>
              </div>
            </div>
          </div>

          <div class="form-group">
            <div class="input-group">
              <input type="email" class="form-input" id="email" name="email" placeholder="Email Address" required>
              <span class="error-message" id="email-error"></span>
            </div>
          </div>

          <div class="form-group">
            <div class="form-row">
              <div class="input-group">
                <input type="tel" class="form-input" id="phone" name="phone" placeholder="Phone Number">
                <span class="error-message" id="phone-error"></span>
              </div>
              <div class="input-group">
                <input type="text" class="form-input" id="address" name="address" placeholder="Address" required>
                <span class="error-message" id="address-error"></span>
              </div>
            </div>
          </div>

          <div class="form-group">
            <div class="form-row">
              <div class="input-group">
                <input type="password" class="form-input" id="password" name="password" placeholder="Password" required>
                <span class="error-message" id="password-error"></span>
              </div>
              <div class="input-group">
                <input type="password" class="form-input" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                <span class="error-message" id="confirm_password-error"></span>
              </div>
            </div>
          </div>
        </div>

        <!-- Step 2: Role Selection -->
        <div class="step" id="step-2">
          <h2 class="step-title">What describes you?</h2>
          <p class="step-description">Select all roles that apply to you. You can always add more later.</p>
          
          <div class="role-grid">
            <div class="role-card" onclick="toggleRole('pet_owner')">
              <div class="role-icon">🐾</div>
              <div class="role-title">Pet Owner</div>
              <div class="role-description">I have pets and want to find services for them</div>
              <input type="checkbox" id="role_pet_owner" name="roles[]" value="pet_owner">
            </div>
            
            <div class="role-card" onclick="toggleRole('trainer')">
              <div class="role-icon">🎓</div>
              <div class="role-title">Pet Trainer</div>
              <div class="role-description">I provide professional pet training services</div>
              <input type="checkbox" id="role_trainer" name="roles[]" value="trainer">
            </div>
            
            <div class="role-card" onclick="toggleRole('groomer')">
              <div class="role-icon">✂️</div>
              <div class="role-title">Pet Groomer</div>
              <div class="role-description">I offer pet grooming and styling services</div>
              <input type="checkbox" id="role_groomer" name="roles[]" value="groomer">
            </div>
            
            <div class="role-card" onclick="toggleRole('sitter')">
              <div class="role-icon">🏠</div>
              <div class="role-title">Pet Sitter</div>
              <div class="role-description">I provide pet sitting and care services</div>
              <input type="checkbox" id="role_sitter" name="roles[]" value="sitter">
            </div>
            
            <div class="role-card" onclick="toggleRole('breeder')">
              <div class="role-icon">🐕‍🦺</div>
              <div class="role-title">Pet Breeder</div>
              <div class="role-description">I breed and sell pets professionally</div>
              <input type="checkbox" id="role_breeder" name="roles[]" value="breeder">
            </div>
          </div>
        </div>

        <!-- Step 3: Role-Specific Setup -->
        <div class="step" id="step-3">
          <h2 class="step-title">Complete your profile</h2>
          <p class="step-description">Provide additional information for your selected roles.</p>
          
          <div class="role-forms" id="roleFormsContainer">
            <!-- Role-specific forms will be inserted here dynamically -->
          </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="navigation-buttons">
          <button type="button" class="btn btn-secondary" id="prevBtn" onclick="previousStep()" style="display: none;">
            Previous
          </button>
          <button type="button" class="btn btn-primary" id="nextBtn" onclick="nextStep()">
            Next
          </button>
          <button type="submit" class="btn btn-primary" id="submitBtn" style="display: none;">
            Complete Registration
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    let currentStep = 1;
    const totalSteps = 3;
    let selectedRoles = [];
    
    // Form validation rules
    const nameRegex = /^[A-Za-z]{2,}$/;
    const validationRules = {
      fname: {
        validate: value => nameRegex.test(value.trim()),
        message: "First name must be at least 2 letters with no numbers or spaces."
      },
      lname: {
        validate: value => nameRegex.test(value.trim()),
        message: "Last name must be at least 2 letters with no numbers or spaces."
      },
      email: {
        validate: value => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
        message: "Please enter a valid email address."
      },
      phone: {
        validate: value => value === '' || /^07\d{8}$/.test(value),
        message: "Phone number must be 10 digits and start with 07."
      },
      address: {
        validate: value => value.trim().length >= 5,
        message: "Address must be at least 5 characters long."
      },
      password: {
        validate: value => value.length >= 6,
        message: "Password must be at least 6 characters long."
      },
      confirm_password: {
        validate: () => document.getElementById("password").value === document.getElementById("confirm_password").value,
        message: "Passwords do not match."
      }
    };

    // Check email availability via API
    async function checkEmailAvailability(email) {
      try {
        const response = await fetch('/PETVET/api/check-email.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email: email })
        });
        
        const result = await response.json();
        return result;
      } catch (error) {
        console.error('Email check error:', error);
        return { error: 'Could not verify email' };
      }
    }

    // Step navigation
    async function nextStep() {
      if (currentStep === 1) {
        if (!validateStep1()) return;
        
        // Check email availability before proceeding
        const emailInput = document.getElementById('email');
        const email = emailInput.value.trim();
        
        if (email) {
          // Show loading state
          const nextBtn = document.getElementById('nextBtn');
          const originalText = nextBtn.textContent;
          nextBtn.disabled = true;
          nextBtn.textContent = 'Checking email...';
          
          const emailCheck = await checkEmailAvailability(email);
          
          // Restore button
          nextBtn.disabled = false;
          nextBtn.textContent = originalText;
          
          if (emailCheck.exists) {
            // Email is already taken
            const errorEl = document.getElementById('email-error');
            emailInput.classList.add('error');
            emailInput.classList.remove('success');
            errorEl.textContent = 'This email is already registered. Please use a different email or login.';
            errorEl.style.display = 'block';
            return;
          } else if (emailCheck.available) {
            // Email is available - continue
            emailInput.classList.remove('error');
            emailInput.classList.add('success');
            const errorEl = document.getElementById('email-error');
            errorEl.style.display = 'none';
          }
        }
      }
      
      if (currentStep === 2 && !validateStep2()) return;
      
      if (currentStep === 2) {
        generateRoleSpecificForms();
      }
      
      if (currentStep < totalSteps) {
        currentStep++;
        showStep(currentStep);
      }
    }

    function previousStep() {
      if (currentStep > 1) {
        currentStep--;
        showStep(currentStep);
      }
    }

    function showStep(step) {
      // Hide all steps
      document.querySelectorAll('.step').forEach(stepEl => {
        stepEl.classList.remove('active');
      });
      
      // Show current step
      document.getElementById(`step-${step}`).classList.add('active');
      
      // Update progress bar
      const progress = (step / totalSteps) * 100;
      document.getElementById('progressFill').style.width = `${progress}%`;
      
      // Update indicators
      for (let i = 1; i <= totalSteps; i++) {
        const indicator = document.getElementById(`indicator-${i}`);
        indicator.classList.remove('active', 'completed');
        
        if (i < step) {
          indicator.classList.add('completed');
        } else if (i === step) {
          indicator.classList.add('active');
        }
      }
      
      // Update navigation buttons
      document.getElementById('prevBtn').style.display = step > 1 ? 'flex' : 'none';
      document.getElementById('nextBtn').style.display = step < totalSteps ? 'flex' : 'none';
      document.getElementById('submitBtn').style.display = step === totalSteps ? 'flex' : 'none';
    }

    // Role selection
    function toggleRole(role) {
      const checkbox = document.getElementById(`role_${role}`);
      const card = checkbox.closest('.role-card');
      
      checkbox.checked = !checkbox.checked;
      
      if (checkbox.checked) {
        card.classList.add('selected');
        if (!selectedRoles.includes(role)) {
          selectedRoles.push(role);
        }
      } else {
        card.classList.remove('selected');
        selectedRoles = selectedRoles.filter(r => r !== role);
      }
    }

    // Form validation
    function validateField(fieldId) {
      const input = document.getElementById(fieldId);
      const errorEl = document.getElementById(`${fieldId}-error`);
      const rule = validationRules[fieldId];
      
      if (!rule) return true;
      
      const isValid = rule.validate(input.value);
      
      if (!isValid) {
        input.classList.remove('success');
        input.classList.add('error');
        errorEl.textContent = rule.message;
        errorEl.style.display = 'block';
        return false;
      } else {
        input.classList.remove('error');
        input.classList.add('success');
        errorEl.style.display = 'none';
        return true;
      }
    }

    function validateStep1() {
      let isValid = true;
      Object.keys(validationRules).forEach(fieldId => {
        if (!validateField(fieldId)) {
          isValid = false;
        }
      });
      return isValid;
    }

    function validateStep2() {
      if (selectedRoles.length === 0) {
        alert('Please select at least one role to continue.');
        return false;
      }
      return true;
    }

    // Generate role-specific forms
    function generateRoleSpecificForms() {
      const container = document.getElementById('roleFormsContainer');
      container.innerHTML = '';

      selectedRoles.forEach(role => {
        const formHTML = getRoleSpecificForm(role);
        container.innerHTML += formHTML;
      });

      // Initialize file upload handlers
      initializeFileUploads();
    }

    function getRoleSpecificForm(role) {
      const forms = {
        pet_owner: `
          <div class="role-form">
            <div class="role-form-title">
              Pet Owner Information
            </div>
            <div class="form-group">
              <textarea class="form-textarea" name="pet_owner_info" placeholder="Tell us about your pets (optional)"></textarea>
            </div>
          </div>
        `,
        
        trainer: `
          <div class="role-form">
            <div class="role-form-title">
              🎓 Pet Trainer Information
            </div>
            <div class="form-group">
              <div class="form-row">
                <input type="text" class="form-input" name="trainer_specialization" placeholder="Specialization (e.g., Obedience, Agility)" required>
                <input type="number" class="form-input" name="trainer_experience" placeholder="Years of Experience" min="0" step="1" required>
              </div>
            </div>
            <div class="form-group">
              <input type="text" class="form-input" name="trainer_service_area" placeholder="Service Area" style="width: 100%;">
            </div>
            <div class="form-group">
              <textarea class="form-textarea" name="trainer_certifications" placeholder="Certifications and qualifications"></textarea>
            </div>
            <div class="form-group">
              <label style="display: block; margin-bottom: 10px; font-weight: 600; color: #374151;">License/Certification Documents (PDF, Optional):</label>
              <div class="file-upload-area" onclick="document.getElementById('trainer_license').click()">
                <div class="file-upload-icon">📄</div>
                <div class="file-upload-text">Click to upload or drag & drop</div>
                <div class="file-upload-subtext">PDF files only, max 5MB</div>
                <input type="file" id="trainer_license" name="trainer_license" accept=".pdf" style="display: none;">
              </div>
            </div>
          </div>
        `,
        
        groomer: `
          <div class="role-form">
            <div class="role-form-title">
              ✂️ Pet Groomer Information
            </div>
            <div class="form-group">
              <div class="form-row">
                <input type="number" class="form-input" name="groomer_experience" placeholder="Years of Experience" min="0" step="1" required>
                <input type="text" class="form-input" name="groomer_business_name" placeholder="Business Name">
              </div>
            </div>
            <div class="form-group">
              <textarea class="form-textarea" name="groomer_services" placeholder="Services offered (e.g., Bath, Nail trim, Full groom)" required></textarea>
            </div>
            <div class="form-group">
              <textarea class="form-textarea" name="groomer_pricing" placeholder="Pricing structure and rates"></textarea>
            </div>
            <div class="form-group">
              <label style="display: block; margin-bottom: 10px; font-weight: 600; color: #374151;">Business License (PDF, Optional):</label>
              <div class="file-upload-area" onclick="document.getElementById('groomer_license').click()">
                <div class="file-upload-icon">📄</div>
                <div class="file-upload-text">Click to upload business license</div>
                <div class="file-upload-subtext">PDF files only, max 5MB</div>
                <input type="file" id="groomer_license" name="groomer_license" accept=".pdf" style="display: none;">
              </div>
            </div>
          </div>
        `,
        
        sitter: `
          <div class="role-form">
            <div class="role-form-title">
              🏠 Pet Sitter Information
            </div>
            <div class="form-group">
              <div class="form-row">
                <select class="form-select" name="sitter_home_type" required>
                  <option value="">Select home type</option>
                  <option value="apartment">Apartment</option>
                  <option value="house">House</option>
                  <option value="house_with_yard">House with Yard</option>
                </select>
                <input type="number" class="form-input" name="sitter_daily_rate" placeholder="Daily Rate ($)" step="0.01" required>
              </div>
            </div>
            <div class="form-group">
              <input type="number" class="form-input" name="sitter_experience" placeholder="Years of Experience" min="0" step="1" style="width: 100%;">
            </div>
            <div class="form-group">
              <textarea class="form-textarea" name="sitter_pet_types" placeholder="Types of pets you can care for" required></textarea>
            </div>
            <div class="checkbox-group">
              <div class="custom-checkbox" onclick="toggleCheckbox(this, 'sitter_overnight')"></div>
              <label style="color: #374151; font-weight: 500;">Overnight care available</label>
              <input type="hidden" id="sitter_overnight" name="sitter_overnight" value="0">
            </div>
          </div>
        `,
        
        breeder: `
          <div class="role-form">
            <div class="role-form-title">
              🐕‍🦺 Pet Breeder Information
            </div>
            <div class="form-group">
              <div class="form-row">
                <input type="text" class="form-input" name="breeder_breeds" placeholder="Breeds you specialize in" required>
                <input type="number" class="form-input" name="breeder_experience" placeholder="Years of Breeding Experience" min="0" step="1" required>
              </div>
            </div>
            <div class="form-group">
              <input type="text" class="form-input" name="breeder_kennel_registration" placeholder="Kennel Registration Number">
            </div>
            <div class="form-group">
              <textarea class="form-textarea" name="breeder_philosophy" placeholder="Breeding philosophy and approach"></textarea>
            </div>
            <div class="form-group">
              <label style="display: block; margin-bottom: 10px; font-weight: 600; color: #374151;">Breeding License (PDF, Optional):</label>
              <div class="file-upload-area" onclick="document.getElementById('breeder_license').click()">
                <div class="file-upload-icon">📄</div>
                <div class="file-upload-text">Click to upload breeding license</div>
                <div class="file-upload-subtext">PDF files only, max 5MB</div>
                <input type="file" id="breeder_license" name="breeder_license" accept=".pdf" style="display: none;">
              </div>
            </div>
          </div>
        `
      };
      
      return forms[role] || '';
    }

    // Custom checkbox functionality
    function toggleCheckbox(checkboxEl, inputId) {
      const input = document.getElementById(inputId);
      const isChecked = input.value === '1';
      
      input.value = isChecked ? '0' : '1';
      checkboxEl.classList.toggle('checked', !isChecked);
    }

    // File upload handling
    function initializeFileUploads() {
      document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', function(e) {
          const file = e.target.files[0];
          const uploadArea = e.target.closest('.file-upload-area');
          
          if (file) {
            if (file.size > 5 * 1024 * 1024) { // 5MB limit
              alert('File size must be less than 5MB');
              e.target.value = '';
              return;
            }
            
            uploadArea.querySelector('.file-upload-text').textContent = `Selected: ${file.name}`;
            uploadArea.classList.add('file-selected');
          }
        });
      });

      // Drag and drop functionality
      document.querySelectorAll('.file-upload-area').forEach(area => {
        area.addEventListener('dragover', function(e) {
          e.preventDefault();
          this.classList.add('dragover');
        });

        area.addEventListener('dragleave', function(e) {
          e.preventDefault();
          this.classList.remove('dragover');
        });

        area.addEventListener('drop', function(e) {
          e.preventDefault();
          this.classList.remove('dragover');
          
          const files = e.dataTransfer.files;
          const input = this.querySelector('input[type="file"]');
          
          if (files.length > 0 && files[0].type === 'application/pdf') {
            input.files = files;
            input.dispatchEvent(new Event('change'));
          }
        });
      });
    }

    // Initialize event listeners
    document.addEventListener('DOMContentLoaded', function() {
      // Add real-time validation
      Object.keys(validationRules).forEach(fieldId => {
        const input = document.getElementById(fieldId);
        if (input) {
          input.addEventListener('blur', () => validateField(fieldId));
          input.addEventListener('input', () => {
            if (input.classList.contains('error')) {
              validateField(fieldId);
            }
          });
        }
      });

      // Form submission validation
      document.querySelector('form').addEventListener('submit', function(e) {
        // Validate required fields in role-specific forms
        const requiredInputs = document.querySelectorAll('#roleFormsContainer input[required], #roleFormsContainer select[required], #roleFormsContainer textarea[required]');
        let allValid = true;
        
        requiredInputs.forEach(input => {
          if (!input.value.trim()) {
            input.classList.add('error');
            allValid = false;
          } else {
            input.classList.remove('error');
          }
        });
        
        if (!allValid) {
          e.preventDefault();
          alert('Please fill in all required fields.');
        }
      });
    });
  </script>
</body>
</html>