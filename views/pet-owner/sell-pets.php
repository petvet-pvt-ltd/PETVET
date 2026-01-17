<?php /* Sell Pets page (enhanced with validation) */ ?>
<link rel="stylesheet" href="/PETVET/public/css/pet-owner/explore-pets.css">
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
<style>
  .field-hint {
    display: block;
    margin-top: 4px;
    font-size: 12px;
    color: #64748b;
    font-weight: 400;
    transition: color 0.2s ease;
  }
  
  input:focus + .field-hint,
  input:valid + .field-hint {
    color: #10b981;
  }
  
  input.error {
    border-color: #ef4444 !important;
    background-color: #fef2f2 !important;
  }
  
  input.error + .field-hint {
    color: #ef4444 !important;
    font-weight: 500 !important;
  }
  
  /* Make sure border styles are visible */
  input[type="text"],
  input[type="tel"],
  input[type="number"],
  input[type="url"],
  textarea,
  select {
    border: 2px solid #d1d5db;
    padding: 10px;
    border-radius: 6px;
    transition: all 0.2s ease;
  }
  
  input[type="text"]:focus,
  input[type="tel"]:focus,
  input[type="number"]:focus,
  input[type="url"]:focus,
  textarea:focus,
  select:focus {
    outline: none;
    border-color: #3b82f6;
  }
  
  /* Remove spinner arrows from number inputs */
  input[type="number"]::-webkit-inner-spin-button,
  input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    appearance: none;
    margin: 0;
  }
  
  input[type="number"] {
    -moz-appearance: textfield;
    appearance: textfield;
  }
  
  /* Map styling */
  #sellMapContainer {
    height: 450px;
    width: 100%;
    border-radius: 8px;
    border: 2px solid #d1d5db;
    margin-top: 8px;
  }
  
  .location-input-readonly {
    background-color: #f9fafb !important;
    cursor: not-allowed;
  }
</style>
<div class="main-content">
  <header class="page-header">
    <div class="title-wrap">
      <h2>Create a Pet Listing</h2>
      <p class="subtitle">Fill the form below to publish a new pet listing (demo only).</p>
    </div>
    <div class="actions">
      <a class="btn outline" href="/PETVET/index.php?module=pet-owner&page=explore-pets">Back to Explore</a>
    </div>
  </header>

  <section>
    <form id="sellFormSingle" novalidate>
      <div class="form-grid">

        <!-- Pet Name -->
        <label>Pet Name
          <input type="text" name="name" id="petName" required 
            pattern="^[A-Za-z\s]+$"
            placeholder="e.g., Buddy"
            title="Name should only contain letters and spaces."
            autocomplete="off">
          <small class="field-hint">Only letters and spaces allowed</small>
        </label>

        <!-- Species -->
        <label>Species
          <select name="species" required>
            <option value="">Select species</option>
            <option>Dog</option>
            <option>Cat</option>
            <option>Bird</option>
            <option>Other</option>
          </select>
        </label>

        <!-- Breed -->
        <label>Breed
          <input type="text" name="breed" id="breed" required
            pattern="^[A-Za-z\s]+$"
            placeholder="e.g., Golden Retriever"
            title="Breed should only contain letters and spaces."
            autocomplete="off">
          <small class="field-hint">Only letters and spaces allowed</small>
        </label>

        <!-- Age -->
        <label>Age (years)
          <input type="number" name="age" id="age" min="0" max="99" placeholder="e.g., 2" required
            title="Age must be between 0 and 99 years."
            autocomplete="off">
          <small class="field-hint">Age must be less than 100</small>
        </label>

        <!-- Gender -->
        <label>Gender
          <select name="gender" required>
            <option value="">Select gender</option>
            <option>Male</option>
            <option>Female</option>
          </select>
        </label>

        <!-- Price -->
        <label>Price (Rs)
          <input type="number" name="price" id="price" min="0" step="500" required
            placeholder="e.g., 5000"
            title="Price must be a positive number."
            autocomplete="off">
          <small class="field-hint">Enter price in Sri Lankan Rupees</small>
        </label>

        <!-- Phone -->
        <label>Contact Number
          <input type="tel" name="phone" id="phone" maxlength="10" required
            pattern="^[0-9]{10}$"
            placeholder="e.g., 0771234567"
            title="Phone number should be 10 digits and contain only numbers."
            autocomplete="off">
          <small class="field-hint">Must be 10 digits, numbers only</small>
        </label>

        <!-- Location -->
        <label class="full">Pet Location
          <input type="text" name="location" id="sellLocation" readonly required
            class="location-input-readonly"
            placeholder="Click on the map to select location">
          <input type="hidden" name="latitude" id="sellLatitude">
          <input type="hidden" name="longitude" id="sellLongitude">
          <button type="button" id="getCurrentLocationBtn" class="btn outline" style="margin-top: 8px; width: auto;">
            📍 Use My Current Location
          </button>
          <div id="sellMapContainer"></div>
          <small class="field-hint">Click on the map to select where the pet is located</small>
        </label>

        <!-- Health Badges -->
        <label class="full">Health Badges
          <div class="checks">
            <label><input type="checkbox" name="badges[]" value="Vaccinated"> Vaccinated</label>
            <label><input type="checkbox" name="badges[]" value="Microchipped"> Microchipped</label>
          </div>
        </label>

        <!-- Description -->
        <label class="full">Short Description
          <textarea name="desc" rows="3" required></textarea>
        </label>

        <!-- Image -->
        <label class="full">Image URL
          <input type="url" name="image" placeholder="https://example.com/pet.jpg" required>
        </label>

      </div>

      <div class="modal-actions" style="margin-top:18px">
        <button type="reset" class="btn outline">Reset</button>
        <button type="submit" class="btn primary">Publish Listing</button>
      </div>
      <p class="empty" style="margin-top:12px;color:#64748b">Demo only – submission won’t persist.</p>
    </form>
  </section>
</div>

<!-- Leaflet JavaScript -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  console.log('✅ Validation script loaded!');
  
  const form = document.getElementById('sellFormSingle');
  const petName = document.getElementById('petName');
  const breed = document.getElementById('breed');
  const age = document.getElementById('age');
  const phone = document.getElementById('phone');
  const price = document.getElementById('price');

  console.log('Form elements found:', { form, petName, breed, age, phone, price });

  // Leaflet map initialization
  let sellMap = null;
  let sellMarker = null;
  
  // Initialize map
  function initSellMap(lat = 6.9271, lng = 79.8612) {
    if (sellMap) {
      sellMap.remove();
    }
    
    sellMap = L.map('sellMapContainer').setView([lat, lng], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
      maxZoom: 19
    }).addTo(sellMap);
    
    // Add click handler
    sellMap.on('click', function(e) {
      const { lat, lng } = e.latlng;
      
      // Add or update marker
      if (sellMarker) {
        sellMarker.setLatLng([lat, lng]);
      } else {
        sellMarker = L.marker([lat, lng]).addTo(sellMap);
      }
      
      // Update hidden inputs
      document.getElementById('sellLatitude').value = lat;
      document.getElementById('sellLongitude').value = lng;
      
      // Fetch address
      fetchAddress(lat, lng);
    });
  }
  
  // Fetch address from coordinates
  async function fetchAddress(lat, lng) {
    const locationInput = document.getElementById('sellLocation');
    locationInput.value = 'Getting location...';
    
    try {
      const response = await fetch(`/PETVET/api/pet-owner/reverse-geocode.php?lat=${lat}&lon=${lng}`);
      const data = await response.json();
      
      if (data.success && data.location) {
        locationInput.value = data.location;
      } else {
        locationInput.value = `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
      }
    } catch (error) {
      console.error('Geocoding error:', error);
      locationInput.value = `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
    }
  }
  
  // Get current location button
  document.getElementById('getCurrentLocationBtn').addEventListener('click', function() {
    if (!navigator.geolocation) {
      alert('Geolocation is not supported by your browser');
      return;
    }
    
    this.disabled = true;
    this.textContent = '📍 Getting location...';
    
    navigator.geolocation.getCurrentPosition(
      (position) => {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;
        
        // Center map on current location
        sellMap.setView([lat, lng], 15);
        
        // Add marker
        if (sellMarker) {
          sellMarker.setLatLng([lat, lng]);
        } else {
          sellMarker = L.marker([lat, lng]).addTo(sellMap);
        }
        
        // Update inputs
        document.getElementById('sellLatitude').value = lat;
        document.getElementById('sellLongitude').value = lng;
        
        // Fetch address
        fetchAddress(lat, lng);
        
        this.disabled = false;
        this.textContent = '📍 Use My Current Location';
      },
      (error) => {
        console.error('Geolocation error:', error);
        alert('Unable to get your location. Please click on the map to select a location.');
        this.disabled = false;
        this.textContent = '📍 Use My Current Location';
      },
      {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 0
      }
    );
  });
  
  // Initialize map on page load
  initSellMap();

  console.log('Form elements found:', { form, petName, breed, age, phone, price });

  // Helper function to add visual feedback
  const addErrorState = (field, message) => {
    field.style.borderColor = '#ef4444';
    field.style.backgroundColor = '#fef2f2';
    field.classList.add('error');
    const hint = field.nextElementSibling;
    if (hint && hint.classList.contains('field-hint')) {
      hint.style.color = '#ef4444';
      hint.style.fontWeight = '500';
      hint.textContent = message;
    } else {
      console.log('Hint not found for field:', field.name);
    }
  };

  const removeErrorState = (field, defaultMessage) => {
    field.style.borderColor = '';
    field.style.backgroundColor = '';
    field.classList.remove('error');
    const hint = field.nextElementSibling;
    if (hint && hint.classList.contains('field-hint')) {
      hint.style.color = '#64748b';
      hint.style.fontWeight = '400';
      hint.textContent = defaultMessage;
    }
  };

  // Prevent numbers/special chars in name (only letters and spaces)
  petName.addEventListener('input', e => {
    console.log('Pet name input:', e.target.value);
    const original = e.target.value;
    const cleaned = original.replace(/[^A-Za-z\s]/g, '');
    e.target.value = cleaned;
    
    // Show error immediately if invalid characters were typed
    if (original !== cleaned) {
      console.log('Invalid characters detected in pet name!');
      addErrorState(petName, '❌ Only letters and spaces allowed');
    } else if (cleaned.trim().length > 0) {
      removeErrorState(petName, '✓ Only letters and spaces allowed');
    } else {
      removeErrorState(petName, 'Only letters and spaces allowed');
    }
  });

  petName.addEventListener('paste', e => {
    e.preventDefault();
    const pastedText = (e.clipboardData || window.clipboardData).getData('text');
    const cleaned = pastedText.replace(/[^A-Za-z\s]/g, '');
    document.execCommand('insertText', false, cleaned);
    
    // Show error if pasted text had invalid characters
    if (pastedText !== cleaned) {
      addErrorState(petName, '❌ Invalid characters removed from paste');
      setTimeout(() => removeErrorState(petName, 'Only letters and spaces allowed'), 2000);
    }
  });

  petName.addEventListener('blur', e => {
    const value = e.target.value.trim();
    if (value.length === 0) {
      addErrorState(petName, '❌ Pet name is required');
    } else if (!/^[A-Za-z\s]+$/.test(value)) {
      addErrorState(petName, '❌ Only letters and spaces allowed');
    } else {
      removeErrorState(petName, '✓ Valid name');
    }
  });

  // Prevent numbers/special chars in breed (only letters and spaces)
  breed.addEventListener('input', e => {
    const original = e.target.value;
    const cleaned = original.replace(/[^A-Za-z\s]/g, '');
    e.target.value = cleaned;
    
    // Show error immediately if invalid characters were typed
    if (original !== cleaned) {
      addErrorState(breed, '❌ Only letters and spaces allowed');
    } else if (cleaned.trim().length > 0) {
      removeErrorState(breed, '✓ Only letters and spaces allowed');
    } else {
      removeErrorState(breed, 'Only letters and spaces allowed');
    }
  });

  breed.addEventListener('paste', e => {
    e.preventDefault();
    const pastedText = (e.clipboardData || window.clipboardData).getData('text');
    const cleaned = pastedText.replace(/[^A-Za-z\s]/g, '');
    document.execCommand('insertText', false, cleaned);
    
    // Show error if pasted text had invalid characters
    if (pastedText !== cleaned) {
      addErrorState(breed, '❌ Invalid characters removed from paste');
      setTimeout(() => removeErrorState(breed, 'Only letters and spaces allowed'), 2000);
    }
  });

  breed.addEventListener('blur', e => {
    const value = e.target.value.trim();
    if (value.length === 0) {
      addErrorState(breed, '❌ Breed is required');
    } else if (!/^[A-Za-z\s]+$/.test(value)) {
      addErrorState(breed, '❌ Only letters and spaces allowed');
    } else {
      removeErrorState(breed, '✓ Valid breed');
    }
  });

  // Prevent letters in age and limit to < 100 (only numbers)
  age.addEventListener('input', e => {
    const original = e.target.value;
    // Remove any non-digit characters
    const cleaned = original.replace(/[^0-9]/g, '');
    
    // Parse as integer
    let numValue = parseInt(cleaned);
    
    // Show error immediately if invalid characters were typed
    if (original !== cleaned && original.length > 0) {
      addErrorState(age, '❌ Only numbers allowed (0-99)');
      e.target.value = cleaned;
      return;
    }
    
    // Limit to 0-99
    if (numValue > 99) {
      numValue = 99;
      e.target.value = numValue;
      addErrorState(age, '❌ Age must be less than 100');
    } else if (numValue < 0) {
      numValue = 0;
      e.target.value = numValue;
      addErrorState(age, '❌ Age cannot be negative');
    } else if (cleaned.length > 0) {
      e.target.value = numValue;
      removeErrorState(age, '✓ Age must be less than 100');
    } else {
      e.target.value = '';
      removeErrorState(age, 'Age must be less than 100');
    }
  });

  age.addEventListener('paste', e => {
    e.preventDefault();
    const pastedText = (e.clipboardData || window.clipboardData).getData('text');
    const cleaned = pastedText.replace(/[^0-9]/g, '');
    let numValue = parseInt(cleaned);
    
    // Show error if pasted text had invalid characters
    if (pastedText !== cleaned) {
      addErrorState(age, '❌ Invalid characters removed from paste');
      setTimeout(() => removeErrorState(age, 'Age must be less than 100'), 2000);
    }
    
    if (numValue > 99) {
      numValue = 99;
      addErrorState(age, '❌ Age limited to 99');
    }
    if (numValue < 0) numValue = 0;
    age.value = isNaN(numValue) ? '' : numValue;
  });

  age.addEventListener('blur', e => {
    const value = e.target.value.trim();
    if (value === '') {
      addErrorState(age, '❌ Age is required');
    } else {
      const numValue = parseInt(value);
      if (isNaN(numValue) || numValue < 0 || numValue > 99) {
        addErrorState(age, '❌ Age must be between 0 and 99');
      } else {
        removeErrorState(age, '✓ Valid age');
      }
    }
  });

  // Prevent age from accepting 'e', '+', '-', '.' via keyboard
  age.addEventListener('keydown', e => {
    if (e.key === 'e' || e.key === 'E' || e.key === '+' || e.key === '-' || e.key === '.') {
      e.preventDefault();
      addErrorState(age, '❌ Only numbers 0-9 allowed');
      setTimeout(() => {
        if (age.value.trim().length > 0) {
          removeErrorState(age, '✓ Age must be less than 100');
        } else {
          removeErrorState(age, 'Age must be less than 100');
        }
      }, 1500);
    }
  });

  // Prevent letters in phone and limit to 10 digits (only numbers)
  phone.addEventListener('input', e => {
    const original = e.target.value;
    const cleaned = original.replace(/[^0-9]/g, '').slice(0, 10);
    e.target.value = cleaned;
    
    // Show error immediately if invalid characters were typed
    if (original !== cleaned && original.length > 0) {
      addErrorState(phone, '❌ Only numbers allowed (10 digits)');
    } else if (cleaned.length === 10) {
      removeErrorState(phone, '✓ Valid phone number');
    } else if (cleaned.length > 0) {
      addErrorState(phone, `⚠️ ${10 - cleaned.length} more digit(s) needed`);
    } else {
      removeErrorState(phone, 'Must be 10 digits, numbers only');
    }
  });

  phone.addEventListener('paste', e => {
    e.preventDefault();
    const pastedText = (e.clipboardData || window.clipboardData).getData('text');
    const cleaned = pastedText.replace(/[^0-9]/g, '').slice(0, 10);
    document.execCommand('insertText', false, cleaned);
    
    // Show error if pasted text had invalid characters
    if (pastedText.replace(/[^0-9]/g, '') !== cleaned || pastedText !== cleaned) {
      addErrorState(phone, '❌ Invalid characters removed from paste');
      setTimeout(() => {
        if (cleaned.length === 10) {
          removeErrorState(phone, '✓ Valid phone number');
        } else {
          removeErrorState(phone, 'Must be 10 digits, numbers only');
        }
      }, 2000);
    }
  });

  phone.addEventListener('blur', e => {
    const value = e.target.value.trim();
    if (value.length === 0) {
      addErrorState(phone, '❌ Phone number is required');
    } else if (!/^[0-9]{10}$/.test(value)) {
      addErrorState(phone, '❌ Must be exactly 10 digits');
    } else {
      removeErrorState(phone, '✓ Valid phone number');
    }
  });

  // Prevent phone from accepting non-numeric keyboard input
  phone.addEventListener('keydown', e => {
    if (e.key === 'e' || e.key === 'E' || e.key === '+' || e.key === '-' || e.key === '.') {
      e.preventDefault();
      addErrorState(phone, '❌ Only numbers 0-9 allowed');
      setTimeout(() => {
        if (phone.value.length === 10) {
          removeErrorState(phone, '✓ Valid phone number');
        } else if (phone.value.length > 0) {
          addErrorState(phone, `⚠️ ${10 - phone.value.length} more digit(s) needed`);
        } else {
          removeErrorState(phone, 'Must be 10 digits, numbers only');
        }
      }, 1500);
    }
  });

  // Price validation (prevent negative values)
  price.addEventListener('input', e => {
    const value = parseFloat(e.target.value);
    if (e.target.value && (isNaN(value) || value < 0)) {
      e.target.value = 0;
      addErrorState(price, '❌ Price cannot be negative');
    } else if (value >= 0) {
      removeErrorState(price, '✓ Valid price');
    } else {
      removeErrorState(price, 'Enter price in Sri Lankan Rupees');
    }
  });

  price.addEventListener('keydown', e => {
    if (e.key === 'e' || e.key === 'E' || e.key === '+' || e.key === '-') {
      e.preventDefault();
      addErrorState(price, '❌ Only positive numbers allowed');
      setTimeout(() => {
        if (price.value && parseFloat(price.value) >= 0) {
          removeErrorState(price, '✓ Valid price');
        } else {
          removeErrorState(price, 'Enter price in Sri Lankan Rupees');
        }
      }, 1500);
    }
  });

  // Validate before submit
  form.addEventListener('submit', e => {
    e.preventDefault();
    
    // Clear all previous error states
    [petName, breed, age, phone, price].forEach(field => {
      field.style.borderColor = '';
      field.style.backgroundColor = '';
    });

    let hasErrors = false;
    const errors = [];

    // Validate pet name
    if (!petName.value.trim() || !/^[A-Za-z\s]+$/.test(petName.value.trim())) {
      addErrorState(petName, '❌ Name should only contain letters and spaces');
      errors.push('Pet name is invalid');
      hasErrors = true;
    }

    // Validate breed
    if (!breed.value.trim() || !/^[A-Za-z\s]+$/.test(breed.value.trim())) {
      addErrorState(breed, '❌ Breed should only contain letters and spaces');
      errors.push('Breed is invalid');
      hasErrors = true;
    }

    // Validate age
    const ageValue = parseInt(age.value);
    if (isNaN(ageValue) || ageValue < 0 || ageValue > 99) {
      addErrorState(age, '❌ Age must be between 0 and 99');
      errors.push('Age must be between 0 and 99');
      hasErrors = true;
    }

    // Validate phone
    if (!/^[0-9]{10}$/.test(phone.value)) {
      addErrorState(phone, '❌ Phone must be exactly 10 digits');
      errors.push('Phone number must be exactly 10 digits');
      hasErrors = true;
    }

    // Validate price
    const priceValue = parseFloat(price.value);
    if (isNaN(priceValue) || priceValue < 0) {
      addErrorState(price, '❌ Price must be a positive number');
      errors.push('Price is invalid');
      hasErrors = true;
    }

    // Check form validity
    if (!form.checkValidity() || hasErrors) {
      alert('⚠️ Please fix the following errors:\n\n' + (errors.length > 0 ? errors.join('\n') : 'Please fill all required fields correctly.'));
      form.reportValidity();
      return;
    }

    alert('✅ Listing published successfully (demo only). Returning to Explore.');
    location.href = '/PETVET/index.php?module=pet-owner&page=explore-pets';
  });
});
</script>
