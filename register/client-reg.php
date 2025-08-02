<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Client Register</title>
  <link rel="stylesheet" href="../styles/reg-client.css">
</head>
<body>
  <div class="card-wrapper">
    <div class="card">
      <div class="card-image">

        <!-- From Uiverse.io by SteveBloX -->  
        <button class=gobackbtn onclick="history.back()">Go Back</button>

        <img src="../images/kid-and-pet.avif" alt="Pet Owner">
      </div>
      <div class="form-container">
        <h2>Owner Registration</h2>
        <p>Please fill out the form to create an account.</p>
        <form action="owner_register_process.php" method="post" novalidate>
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


          <button type="submit">Register as an owner</button>
        </form>
      </div>
    </div>
  </div>

<script>
  const form = document.querySelector("form");

  const nameRegex = /^[A-Za-z]{2,}$/; // Only letters, minimum 2 characters

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

  Object.keys(fields).forEach(fieldId => {
    const input = document.getElementById(fieldId);
    input.addEventListener("keyup", () => validateField(fieldId));
  });

  form.addEventListener("submit", function (e) {
    let isFormValid = true;
    Object.keys(fields).forEach(fieldId => {
      const valid = validateField(fieldId);
      if (!valid) isFormValid = false;
    });

    if (!isFormValid) e.preventDefault();
  });
</script>

</body>
</html>
