<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Clinic Manager Register</title>
  <link rel="stylesheet" href="/PETVET/public/css/guest/clinic-manager-reg.css">
</head>

<body>
  <div class="card-wrapper">
    <div class="card">
      <div class="card-image">
        <button class="gobackbtn" onclick="window.location.href='/PETVET/index.php?module=guest&page=home'">Home</button>
        <img src="/PETVET/views/shared/images/clinic-manager.jpg" alt="Clinic Manager">
      </div>

      <div class="form-container">
        <h2>Clinic Manager Registration</h2>
        <p>Please fill out the form to create an account.</p>

        <form id="clinicManagerForm" action="" method="post" enctype="multipart/form-data" novalidate>

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
              <input type="tel" id="phone" name="phone" placeholder="Phone Number" required>
              <span class="error-msg" id="phone-error"></span>
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
              <input type="text" id="clinic_name" name="clinic_name" placeholder="Clinic Name" required>
              <span class="error-msg" id="clinic_name-error"></span>
            </div>
            <div>
              <input type="text" id="clinic_address" name="clinic_address" placeholder="Clinic Address" required>
              <span class="error-msg" id="clinic_address-error"></span>
            </div>

            <select name="district" id="district" required>
              <option value="">Select District</option>
              <option value="Ampara">Ampara</option>
              <option value="Anuradhapura">Anuradhapura</option>
              <option value="Badulla">Badulla</option>
              <option value="Batticaloa">Batticaloa</option>
              <option value="Colombo">Colombo</option>
              <option value="Galle">Galle</option>
              <option value="Gampaha">Gampaha</option>
              <option value="Hambantota">Hambantota</option>
              <option value="Jaffna">Jaffna</option>
              <option value="Kalutara">Kalutara</option>
              <option value="Kandy">Kandy</option>
              <option value="Kegalle">Kegalle</option>
              <option value="Kilinochchi">Kilinochchi</option>
              <option value="Kurunegala">Kurunegala</option>
              <option value="Mannar">Mannar</option>
              <option value="Matale">Matale</option>
              <option value="Matara">Matara</option>
              <option value="Monaragala">Monaragala</option>
              <option value="Mullaitivu">Mullaitivu</option>
              <option value="Nuwara Eliya">Nuwara Eliya</option>
              <option value="Polonnaruwa">Polonnaruwa</option>
              <option value="Puttalam">Puttalam</option>
              <option value="Ratnapura">Ratnapura</option>
              <option value="Trincomalee">Trincomalee</option>
              <option value="Vavuniya">Vavuniya</option>
            </select>

            <div>
              <input type="email" id="clinic_email" name="clinic_email" placeholder="Clinic Email" required>
              <span class="error-msg" id="clinic_email-error"></span>
            </div>

            <div>
              <input type="tel" id="clinic_phone" name="clinic_phone" placeholder="Clinic Phone" required>
              <span class="error-msg" id="clinic_phone-error"></span>
            </div>

            <!-- Upload label and file input -->
            <div>
              <label for="license_file">Upload PDF with the documents:</label>
              <input type="file" name="license_file" id="license_file" accept=".pdf,.jpg,.jpeg,.png" required>
            </div>

            <!-- Checkbox -->
            <div class="checkbox-wrapper">
              <input type="checkbox" id="consent" required>
              <label for="consent">I confirm the information is accurate.</label>
            </div>

            <!-- Navigation Buttons -->
            <div class="button-group">
              <button type="button" onclick="prevStep()">Back</button>
              <button type="submit">Register</button>
            </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    const form = document.getElementById("clinicManagerForm");
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
        message: "Please enter a valid email address."
      },
      phone: {
        validate: value => /^07\d{8}$/.test(value),
        message: "Phone number must be 10 digits and start with 07."
      },
      password: {
        validate: value => value.length >= 6,
        message: "Password must be at least 6 characters long."
      },
      confirm_password: {
        validate: () => document.getElementById("password").value === document.getElementById("confirm_password").value,
        message: "Passwords do not match."
      },
      clinic_name: {
        validate: value => value.trim().length >= 3,
        message: "Clinic name must be at least 3 characters."
      },
      clinic_address: {
        validate: value => value.trim().length >= 5,
        message: "Clinic address must be at least 5 characters."
      },
      clinic_email: {
        validate: value =>
          /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/i.test(value),
        message: "Please enter a valid clinic email."
      },
      clinic_phone: {
        validate: value => /^07\d{8}$/.test(value),
        message: "Clinic phone must be 10 digits and start with 07."
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
      return ["fname", "lname", "email", "phone", "password", "confirm_password"]
        .map(validateField)
        .every(Boolean);
    }

    function validateStep2() {
      return ["clinic_name", "clinic_address", "clinic_email", "clinic_phone"]
        .map(validateField)
        .every(Boolean);
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

    // Real-time validation for both steps
    Object.keys(validationRules).forEach(id => {
      const input = document.getElementById(id);
      if (input) {
        input.addEventListener("keyup", () => validateField(id));
      }
    });

    form.addEventListener("submit", function(e) {
      if (!form.checkValidity() || !validateStep2()) {
        e.preventDefault();
        alert("Please complete all required fields correctly.");
      }
    });
  </script>

</body>

</html>
