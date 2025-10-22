<?php
/* Veterinarian Registration page */
require_once __DIR__ . '/../../config/auth_helper.php';

// Enable error display for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
  <title>Veterinarian Registration - PetVet</title>
  <link rel="stylesheet" href="/PETVET/public/css/guest/reg-vet.css">
</head>

<body>
  <div class="card-wrapper">
    <div class="card">
      <div class="card-image">
        <button class="gobackbtn" onclick="window.location.href='/PETVET/index.php?module=guest&page=home'">Home</button>
        <img src="/PETVET/views/shared/images/vet-reg-cover.jpg" alt="Veterinarian">
      </div>

      <div class="form-container">
        <h2>Veterinarian Registration</h2>
        <p>Please fill out the form to create an account.</p>

        <?php
        // Display errors if any
        if (isset($_SESSION['registration_errors'])) {
            echo '<div style="background: #fee; border: 1px solid #fcc; padding: 10px; margin: 10px 0; border-radius: 5px; color: #c00;">';
            echo '<strong>Registration Errors:</strong><ul style="margin: 5px 0;">';
            foreach ($_SESSION['registration_errors'] as $error) {
                echo '<li>' . htmlspecialchars($error) . '</li>';
            }
            echo '</ul></div>';
            unset($_SESSION['registration_errors']);
        }
        
        // Display error from URL parameter
        if (isset($_GET['error'])) {
            echo '<div style="background: #fee; border: 1px solid #fcc; padding: 10px; margin: 10px 0; border-radius: 5px; color: #c00;">';
            echo '<strong>Error:</strong> ' . htmlspecialchars($_GET['error']);
            echo '</div>';
        }
        
        // Debug: Show if this is a POST request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo '<div style="background: #ffe; border: 1px solid #cc0; padding: 10px; margin: 10px 0; border-radius: 5px; color: #660;">';
            echo '<strong>DEBUG:</strong> Form was submitted via POST. Processing registration...';
            echo '</div>';
        }
        ?>

        <form id="vetForm" action="/PETVET/index.php?module=guest&page=vet-register" method="post" enctype="multipart/form-data" novalidate>
          
          <!-- Hidden field to specify role -->
          <input type="hidden" name="roles[]" value="vet">

          <!-- Step 1 - Basic Information -->
          <div id="step-1" class="form-step active-step">
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
              <input type="tel" id="phone" name="phone" placeholder="Phone Number" required>
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
            <button type="button" onclick="nextStep()">Next</button>
          </div>

          <!-- Step 2 - Veterinarian Specific Information -->
          <div id="step-2" class="form-step">
            <div>
              <select name="vet_clinic_id" id="clinic" required>
                <option value="">Select Vet Clinic</option>
                <option value="1">Happy Paws Veterinary Clinic</option>
                <option value="2">Pet Care Medical Center</option>
                <option value="3">Animal Health Clinic</option>
              </select>
              <span class="error-msg" id="clinic-error"></span>
            </div>

            <div>
              <input type="number" id="experience" name="vet_experience" placeholder="Years of Experience" min="0" step="1" required>
              <span class="error-msg" id="experience-error"></span>
            </div>
            <div>
              <input type="text" id="specialization" name="vet_specialization" placeholder="Specialization (e.g., Surgery, Internal Medicine)" required>
              <span class="error-msg" id="specialization-error"></span>
            </div>
            <div>
              <input type="text" id="license_number" name="vet_license_number" placeholder="Medical License Number" required>
              <span class="error-msg" id="license-error"></span>
            </div>
            <div>
              <label>Upload Medical License (PDF, Optional):</label>
              <input type="file" name="vet_license" accept=".pdf">
            </div>
            <div>
              <input type="checkbox" id="consent" required> I confirm the information is accurate.
            </div>
            <div>
              <input type="checkbox" id="terms" required> I agree to the terms and conditions.
            </div>
            <button type="button" onclick="prevStep()">Back</button>
            <button type="submit">Register as a Veterinarian</button>
          </div>

        </form>
      </div>
    </div>
  </div>

  <script>
    const form = document.getElementById("vetForm");
    const step1 = document.getElementById("step-1");
    const step2 = document.getElementById("step-2");

    console.log("JavaScript loaded!");
    console.log("Form element:", form);
    console.log("Step 1:", step1);
    console.log("Step 2:", step2);

    // Test if form exists
    if (!form) {
      alert("ERROR: Form not found!");
    }

    const validationRules = {
      fname: {
        validate: value => /^[A-Za-z]{2,}$/.test(value),
        message: "First name must be at least 2 letters with no numbers or spaces."
      },
      lname: {
        validate: value => /^[A-Za-z]{2,}$/.test(value),
        message: "Last name must be at least 2 letters with no numbers or spaces."
      },
      email: {
        validate: value => /^[a-zA-Z0-9._%+-]+@[a-zA-Z]+\.[a-zA-Z]{2,}$/i.test(value),
        message: "Please enter a valid email address (e.g. user@example.com)"
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

    function validateField(id) {
      const input = document.getElementById(id);
      const error = document.getElementById(`${id}-error`);
      const value = input.value;
      const isValid = validationRules[id].validate(value);
      if (!isValid) {
        error.textContent = validationRules[id].message;
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
      Object.keys(validationRules).forEach(id => {
        if (!validateField(id)) isValid = false;
      });
      return isValid;
    }

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

    async function nextStep() {
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
          errorEl.textContent = '';
          errorEl.style.display = 'none';
        }
      }
      
      // Disable required validation on step 1 fields
      document.querySelectorAll('#step-1 input[required]').forEach(input => {
        input.removeAttribute('required');
      });
      
      step1.classList.remove("active-step");
      step2.classList.add("active-step");
    }

    function prevStep() {
      // Re-enable required validation on step 1 fields
      document.getElementById('fname').setAttribute('required', '');
      document.getElementById('lname').setAttribute('required', '');
      document.getElementById('email').setAttribute('required', '');
      document.getElementById('phone').setAttribute('required', '');
      document.getElementById('address').setAttribute('required', '');
      document.getElementById('password').setAttribute('required', '');
      document.getElementById('confirm_password').setAttribute('required', '');
      
      step2.classList.remove("active-step");
      step1.classList.add("active-step");
    }

    // ✅ Add real-time validation for Step 1 fields
    Object.keys(validationRules).forEach(id => {
      const input = document.getElementById(id);
      if (input) {
        input.addEventListener("keyup", () => validateField(id));
      }
    });

    form.addEventListener("submit", function(e) {
      console.log("=== FORM SUBMIT EVENT TRIGGERED ===");
      console.log("Event:", e);
      console.log("Current step 2 active?", step2.classList.contains("active-step"));
      
      // Don't use form.checkValidity() - it checks hidden fields too
      // Just make sure we're on step 2 (form complete)
      if (!step2.classList.contains("active-step")) {
        e.preventDefault();
        console.log("BLOCKED: Not on step 2");
        alert("Please complete step 1 first.");
        return false;
      }
      
      // Check if required fields in step 2 are filled
      const clinic = document.getElementById("clinic");
      const experience = document.getElementById("experience");
      const specialization = document.getElementById("specialization");
      const licenseNum = document.getElementById("license_number");
      const consent = document.getElementById("consent");
      const terms = document.getElementById("terms");
      
      console.log("Clinic:", clinic.value);
      console.log("Experience:", experience.value);
      console.log("Specialization:", specialization.value);
      console.log("License:", licenseNum.value);
      console.log("Consent:", consent.checked);
      console.log("Terms:", terms.checked);
      
      if (!clinic.value || !experience.value || !specialization.value || !licenseNum.value) {
        e.preventDefault();
        console.log("BLOCKED: Missing required fields");
        alert("Please fill in all required fields.");
        return false;
      }
      
      if (!consent.checked || !terms.checked) {
        e.preventDefault();
        console.log("BLOCKED: Checkboxes not checked");
        alert("Please accept the consent and terms.");
        return false;
      }
      
      console.log("✅ Form validation passed! Submitting...");
      // Allow form to submit
      return true;
    });
    
    console.log("Submit event listener attached!");
    
    // Add direct button click listener for debugging
    const submitButton = document.querySelector('button[type="submit"]');
    if (submitButton) {
      console.log("Submit button found:", submitButton);
      submitButton.addEventListener('click', function(e) {
        console.log("!!! SUBMIT BUTTON CLICKED !!!");
      });
    } else {
      console.error("Submit button NOT found!");
    }
  </script>

</body>

</html>
