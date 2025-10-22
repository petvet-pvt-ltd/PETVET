<?php
/**
 * DEPRECATED: Vet registration now uses MVC routing
 * Please use: /PETVET/index.php?module=guest&page=vet-register
 */
header('Location: /PETVET/index.php?module=guest&page=vet-register');
exit;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vet Register</title>
  <link rel="stylesheet" href="/PETVET/public/css/guest/reg-vet.css">
</head>

<body>
  <div class="card-wrapper">
    <div class="card">
      <div class="card-image">
        <button class="gobackbtn" onclick="window.location.href='/PETVET/index.php?module=guest&page=home'">Home</button>
        <img src="/PETVET/views/shared/images/vet-reg-cover.jpg" alt="Pet Owner">
      </div>

      <div class="form-container">
        <h2>Veterinarian Registration</h2>
        <p>Please fill out the form to create an account.</p>

        <form id="vetForm" action="" method="post" enctype="multipart/form-data" novalidate>

          <!-- Step 1 -->
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
            <button type="button" onclick="nextStep()">Next</button>
          </div>

          <!-- Step 2 -->
          <div id="step-2" class="form-step">
            <div>
              <select name="clinic" id="clinic" required>
                <option value="">Select Vet Clinic</option>
                <option value="clinic1">Happy Paws Clinic</option>
                <option value="clinic2">Cure & Care Veterinary</option>
              </select>
              <span class="error-msg" id="clinic-error"></span>
            </div>

            <div>
              <input type="number" id="experience" name="experience" placeholder="Years of Experience" required>
              <span class="error-msg" id="experience-error"></span>
            </div>
            <div>
              <input type="text" id="specialization" name="specialization" placeholder="Specialization (optional)">
            </div>
            <div>
              <label>Upload Medical License:</label>
              <input type="file" name="license_file" accept=".pdf,.jpg,.jpeg,.png" required>
            </div>
            <div>
              <label>Upload National ID:</label>
              <input type="file" name="id_file" accept=".pdf,.jpg,.jpeg,.png" required>
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

    function nextStep() {
      if (validateStep1()) {
        step1.classList.remove("active-step");
        step2.classList.add("active-step");
      }
    }

    function prevStep() {
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
      if (!form.checkValidity()) {
        e.preventDefault();
        alert("Please complete all required fields.");
      }
    });
  </script>

</body>

</html>
