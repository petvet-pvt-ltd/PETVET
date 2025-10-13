<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Registration</title>
  <link rel="stylesheet" href="/PETVET/public/css/guest/reg-client.css">
  <style>
    .step-indicator {
      display: flex;
      justify-content: center;
      margin-bottom: 30px;
    }
    .step {
      display: flex;
      align-items: center;
      margin: 0 10px;
    }
    .step-number {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      background-color: #ddd;
      color: #666;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      margin-right: 10px;
    }
    .step.active .step-number {
      background-color: #007bff;
      color: white;
    }
    .step.completed .step-number {
      background-color: #28a745;
      color: white;
    }
    .role-selection {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
      margin: 20px 0;
    }
    .role-card {
      border: 2px solid #e0e0e0;
      border-radius: 10px;
      padding: 20px;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    .role-card:hover {
      border-color: #007bff;
      background-color: #f8f9fa;
    }
    .role-card.selected {
      border-color: #007bff;
      background-color: #e7f3ff;
    }
    .role-card input[type="checkbox"] {
      display: none;
    }
    .role-icon {
      font-size: 2em;
      margin-bottom: 10px;
    }
    .step-content {
      display: none;
    }
    .step-content.active {
      display: block;
    }
    .nav-buttons {
      display: flex;
      justify-content: space-between;
      margin-top: 30px;
    }
    .btn-secondary {
      background-color: #6c757d;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
    }
    .file-upload-area {
      border: 2px dashed #ddd;
      border-radius: 10px;
      padding: 30px;
      text-align: center;
      margin: 15px 0;
    }
    .file-upload-area.dragover {
      border-color: #007bff;
      background-color: #f8f9fa;
    }
  </style>
</head>
<body>
  <div class="card-wrapper">
    <div class="card">
      <div class="card-image">
        <button class=gobackbtn onclick="window.location.href='/PETVET/index.php?module=guest&page=home'">Home</button>
        <img src="/PETVET/views/shared/images/kid-and-pet.avif" alt="User Registration">
      </div>
      <div class="form-container">
        <!-- Step Indicator -->
        <div class="step-indicator">
          <div class="step active" id="step-indicator-1">
            <div class="step-number">1</div>
            <span>Basic Info</span>
          </div>
          <div class="step" id="step-indicator-2">
            <div class="step-number">2</div>
            <span>Your Roles</span>
          </div>
          <div class="step" id="step-indicator-3">
            <div class="step-number">3</div>
            <span>Complete Setup</span>
          </div>
        </div>
        <form action="registration_process.php" method="post" enctype="multipart/form-data" novalidate>
          
          <!-- Step 1: Basic Information -->
          <div class="step-content active" id="step-1">
            <h2>Basic Information</h2>
            <p>Please fill out your basic information to get started.</p>
            
            <div>
              <input type="text" id="fname" name="fname" placeholder="First Name" required>
              <span class="error-msg" id="fname-error"></span>
            </div>

            <div>
              <input type="text" id="lname" name="lname" placeholder="Last Name" required>
              <span class="error-msg" id="lname-error"></span>
            </div>

            <div>
              <input type="email" id="email" name="email" placeholder="Email Address" required>
              <span class="error-msg" id="email-error"></span>
            </div>

            <div>
              <input type="tel" id="phone" name="phone" placeholder="Phone Number">
              <span class="error-msg" id="phone-error"></span>
            </div>

            <div>
              <input type="text" id="address" name="address" placeholder="Address" required>
              <span class="error-msg" id="address-error"></span>
            </div>

            <div>
              <input type="password" id="password" name="password" placeholder="Password" required>
              <span class="error-msg" id="password-error"></span>
            </div>

            <div>
              <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
              <span class="error-msg" id="confirm_password-error"></span>
            </div>
          </div>

          <!-- Step 2: Role Selection -->
          <div class="step-content" id="step-2">
            <h2>What describes you?</h2>
            <p>Select all that apply. You can add more roles later.</p>
            
            <div class="role-selection">
              <div class="role-card" onclick="toggleRole('pet_owner')">
                <div class="role-icon">🐾</div>
                <h4>Pet Owner</h4>
                <p>I have pets and want to find services</p>
                <input type="checkbox" id="role_pet_owner" name="roles[]" value="pet_owner">
              </div>
              
              <div class="role-card" onclick="toggleRole('trainer')">
                <div class="role-icon">👨‍🏫</div>
                <h4>Trainer</h4>
                <p>I provide pet training services</p>
                <input type="checkbox" id="role_trainer" name="roles[]" value="trainer">
              </div>
              
              <div class="role-card" onclick="toggleRole('groomer')">
                <div class="role-icon">✂️</div>
                <h4>Groomer</h4>
                <p>I provide pet grooming services</p>
                <input type="checkbox" id="role_groomer" name="roles[]" value="groomer">
              </div>
              
              <div class="role-card" onclick="toggleRole('sitter')">
                <div class="role-icon">🏠</div>
                <h4>Pet Sitter</h4>
                <p>I provide pet sitting services</p>
                <input type="checkbox" id="role_sitter" name="roles[]" value="sitter">
              </div>
              
              <div class="role-card" onclick="toggleRole('breeder')">
                <div class="role-icon">🐕‍🦺</div>
                <h4>Breeder</h4>
                <p>I breed and sell pets</p>
                <input type="checkbox" id="role_breeder" name="roles[]" value="breeder">
              </div>
            </div>
          </div>

          <!-- Step 3: Role-Specific Setup -->
          <div class="step-content" id="step-3">
            <h2>Complete Your Setup</h2>
            <p>Please provide additional information for your selected roles.</p>
            
            <div id="role-specific-forms">
              <!-- Forms will be dynamically inserted here based on selected roles -->
            </div>
          </div>

          <!-- Navigation Buttons -->
          <div class="nav-buttons">
            <button type="button" class="btn-secondary" id="prev-btn" onclick="previousStep()" style="display: none;">Previous</button>
            <button type="button" id="next-btn" onclick="nextStep()">Next</button>
            <button type="submit" id="submit-btn" style="display: none;">Complete Registration</button>
          </div>
        </form>
      </div>
    </div>
  </div>

<script>
  let currentStep = 1;
  const totalSteps = 3;
  
  // Form validation rules (existing)
  const nameRegex = /^[A-Za-z]{2,}$/;
  const fields = {
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
      validate: value => /^07\d{8}$/.test(value),
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

  // Step navigation functions
  function nextStep() {
    if (currentStep === 1) {
      if (!validateStep1()) return;
    } else if (currentStep === 2) {
      if (!validateStep2()) return;
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
    document.querySelectorAll('.step-content').forEach(content => {
      content.classList.remove('active');
    });
    
    // Show current step
    document.getElementById(`step-${step}`).classList.add('active');
    
    // Update step indicators
    document.querySelectorAll('.step').forEach((indicator, index) => {
      indicator.classList.remove('active', 'completed');
      if (index + 1 < step) {
        indicator.classList.add('completed');
      } else if (index + 1 === step) {
        indicator.classList.add('active');
      }
    });
    
    // Update navigation buttons
    document.getElementById('prev-btn').style.display = step > 1 ? 'block' : 'none';
    document.getElementById('next-btn').style.display = step < totalSteps ? 'block' : 'none';
    document.getElementById('submit-btn').style.display = step === totalSteps ? 'block' : 'none';
  }

  // Role selection functions
  function toggleRole(role) {
    const checkbox = document.getElementById(`role_${role}`);
    const card = checkbox.closest('.role-card');
    
    checkbox.checked = !checkbox.checked;
    card.classList.toggle('selected', checkbox.checked);
  }

  // Validation functions
  function validateField(fieldId) {
    const input = document.getElementById(fieldId);
    const error = document.getElementById(`${fieldId}-error`);
    const value = input.value;

    const isValid = fields[fieldId].validate(value);

    if (!isValid) {
      error.textContent = fields[fieldId].message;
      input.style.borderColor = "#ef4444";
      return false;
    } else {
      error.textContent = "";
      input.style.borderColor = "#10b981";
      return true;
    }
  }

  function validateStep1() {
    let isValid = true;
    Object.keys(fields).forEach(fieldId => {
      const valid = validateField(fieldId);
      if (!valid) isValid = false;
    });
    return isValid;
  }

  function validateStep2() {
    const selectedRoles = document.querySelectorAll('input[name="roles[]"]:checked');
    if (selectedRoles.length === 0) {
      alert('Please select at least one role to continue.');
      return false;
    }
    return true;
  }

  // Generate role-specific forms
  function generateRoleSpecificForms() {
    const selectedRoles = document.querySelectorAll('input[name="roles[]"]:checked');
    const formsContainer = document.getElementById('role-specific-forms');
    formsContainer.innerHTML = '';

    selectedRoles.forEach(roleCheckbox => {
      const role = roleCheckbox.value;
      const formHTML = getRoleSpecificForm(role);
      formsContainer.innerHTML += formHTML;
    });
  }

  function getRoleSpecificForm(role) {
    switch(role) {
      case 'pet_owner':
        return `
          <div class="role-form" id="pet-owner-form">
            <h4>🐾 Pet Owner Information</h4>
            <div>
              <input type="text" name="pet_owner_info" placeholder="Tell us about your pets (optional)">
            </div>
          </div>
        `;
      
      case 'trainer':
        return `
          <div class="role-form" id="trainer-form">
            <h4>👨‍🏫 Trainer Information</h4>
            <div>
              <input type="text" name="trainer_specialization" placeholder="Specialization (e.g., Obedience, Agility)" required>
            </div>
            <div>
              <input type="number" name="trainer_experience" placeholder="Years of Experience" required>
            </div>
            <div>
              <input type="number" name="trainer_hourly_rate" placeholder="Hourly Rate ($)" step="0.01">
            </div>
            <div>
              <textarea name="trainer_certifications" placeholder="Certifications and qualifications"></textarea>
            </div>
            <div>
              <label for="trainer_license">License/Certification Documents (PDF, Optional):</label>
              <div class="file-upload-area" onclick="document.getElementById('trainer_license').click()">
                <p>Click to upload or drag & drop PDF files</p>
                <input type="file" id="trainer_license" name="trainer_license" accept=".pdf" style="display: none;">
              </div>
            </div>
          </div>
        `;
      
      case 'groomer':
        return `
          <div class="role-form" id="groomer-form">
            <h4>✂️ Groomer Information</h4>
            <div>
              <textarea name="groomer_services" placeholder="Services offered (e.g., Bath, Nail trim, Full groom)" required></textarea>
            </div>
            <div>
              <input type="number" name="groomer_experience" placeholder="Years of Experience" required>
            </div>
            <div>
              <textarea name="groomer_pricing" placeholder="Pricing structure"></textarea>
            </div>
            <div>
              <label for="groomer_license">Business License (PDF, Required):</label>
              <div class="file-upload-area" onclick="document.getElementById('groomer_license').click()">
                <p>Click to upload business license PDF</p>
                <input type="file" id="groomer_license" name="groomer_license" accept=".pdf" required style="display: none;">
              </div>
            </div>
          </div>
        `;
      
      case 'sitter':
        return `
          <div class="role-form" id="sitter-form">
            <h4>🏠 Pet Sitter Information</h4>
            <div>
              <textarea name="sitter_pet_types" placeholder="Types of pets you can care for" required></textarea>
            </div>
            <div>
              <select name="sitter_home_type" required>
                <option value="">Select home type</option>
                <option value="apartment">Apartment</option>
                <option value="house">House</option>
                <option value="house_with_yard">House with Yard</option>
              </select>
            </div>
            <div>
              <input type="number" name="sitter_daily_rate" placeholder="Daily Rate ($)" step="0.01" required>
            </div>
            <div>
              <input type="number" name="sitter_max_pets" placeholder="Maximum pets at once" required>
            </div>
            <div>
              <label>
                <input type="checkbox" name="sitter_overnight" value="1"> Overnight care available
              </label>
            </div>
          </div>
        `;
      
      case 'breeder':
        return `
          <div class="role-form" id="breeder-form">
            <h4>🐕‍🦺 Breeder Information</h4>
            <div>
              <input type="text" name="breeder_breeds" placeholder="Breeds you specialize in" required>
            </div>
            <div>
              <input type="number" name="breeder_experience" placeholder="Years of Breeding Experience" required>
            </div>
            <div>
              <textarea name="breeder_philosophy" placeholder="Breeding philosophy and approach"></textarea>
            </div>
            <div>
              <input type="text" name="breeder_kennel_registration" placeholder="Kennel Registration Number">
            </div>
            <div>
              <label for="breeder_license">Breeding License (PDF, Required):</label>
              <div class="file-upload-area" onclick="document.getElementById('breeder_license').click()">
                <p>Click to upload breeding license PDF</p>
                <input type="file" id="breeder_license" name="breeder_license" accept=".pdf" required style="display: none;">
              </div>
            </div>
          </div>
        `;
      
      default:
        return '';
    }
  }

  // Event listeners
  Object.keys(fields).forEach(fieldId => {
    const input = document.getElementById(fieldId);
    if (input) {
      input.addEventListener("keyup", () => validateField(fieldId));
    }
  });

  // File upload handling
  document.addEventListener('change', function(e) {
    if (e.target.type === 'file') {
      const file = e.target.files[0];
      if (file) {
        const uploadArea = e.target.closest('.file-upload-area');
        uploadArea.querySelector('p').textContent = `Selected: ${file.name}`;
      }
    }
  });

  // Form submission
  document.querySelector('form').addEventListener('submit', function(e) {
    if (currentStep !== totalSteps) {
      e.preventDefault();
      return;
    }
    
    // Validate all required fields in role-specific forms
    const requiredInputs = document.querySelectorAll('#role-specific-forms input[required], #role-specific-forms select[required], #role-specific-forms textarea[required]');
    let allValid = true;
    
    requiredInputs.forEach(input => {
      if (!input.value.trim()) {
        input.style.borderColor = '#ef4444';
        allValid = false;
      } else {
        input.style.borderColor = '#10b981';
      }
    });
    
    if (!allValid) {
      e.preventDefault();
      alert('Please fill in all required fields.');
    }
  });
</script>

</body>
</html>
