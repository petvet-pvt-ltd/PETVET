// Breeder Settings JS
(function(){
  const $ = s=>document.querySelector(s);
  const $$ = s=>Array.from(document.querySelectorAll(s));
  const toastEl = $('#toast');
  function showToast(msg){
    if(!toastEl) return; toastEl.textContent = msg; toastEl.classList.add('show');
    clearTimeout(showToast._t); showToast._t = setTimeout(()=>toastEl.classList.remove('show'),2600);
  }

  // Scroll spy via IntersectionObserver for reliable highlighting
  const navLinks = $$('.quick-nav a');
  const sectionMap = new Map();
  navLinks.forEach(a=>{
    const id = a.getAttribute('href').replace('#','');
    const sec = document.getElementById(id);
    if(sec) sectionMap.set(sec, a);
  });
  const observer = new IntersectionObserver(entries=>{
    entries.forEach(entry=>{
      if(entry.isIntersecting){
        const link = sectionMap.get(entry.target);
        if(link){ navLinks.forEach(l=>l.classList.remove('active')); link.classList.add('active'); }
      }
    });
  }, {rootMargin:'-40% 0px -50% 0px', threshold:[0, .25, .5, 1]});
  sectionMap.forEach((_, sec)=>observer.observe(sec));
  // Smooth scroll assist: active state will be handled by observer
  // Smooth scroll for nav
  navLinks.forEach(a=>a.addEventListener('click', e=>{
    const id = a.getAttribute('href');
    if(id && id.startsWith('#')){
      e.preventDefault();
      const target = document.querySelector(id);
      if(target){ target.scrollIntoView({behavior:'smooth', block:'start'}); }
    }
  }));

  // Reveal animation for cards
  const cards = Array.from(document.querySelectorAll('.settings-grid > section.card'));
  if('IntersectionObserver' in window){
    const ro = new IntersectionObserver(entries=>{
      entries.forEach(en=>{
        if(en.isIntersecting){ en.target.classList.add('reveal-in'); ro.unobserve(en.target); }
      });
    }, {threshold:.2});
    cards.forEach(c=>{c.classList.add('reveal-ready'); ro.observe(c);});
  }

  // Avatar preview
  const avatarInput = $('#breederAvatar');
  const avatarPreview = $('#breederAvatarPreview .image-preview-item img');
  document.addEventListener('click', e=>{
    const btn = e.target.closest('[data-for="breederAvatar"]');
    if(btn && avatarInput){ avatarInput.click(); }
  });
  if(avatarInput){
    avatarInput.addEventListener('change', e=>{
      const file = avatarInput.files && avatarInput.files[0];
      if(!file) return;
      if(!file.type.startsWith('image/')){ showToast('Please select an image file'); return; }
      const url = URL.createObjectURL(file);
      if(avatarPreview){ avatarPreview.src = url; }
    });
  }

  // Real-time profile phone validation (07xxxxxxxx)
  const phoneInput = $('#phoneInput');
  const phoneError = $('#phoneError');
  if(phoneInput && phoneError){
    phoneInput.addEventListener('input', ()=>{
      const phone = phoneInput.value.trim();
      if(phone === ''){
        phoneError.textContent = '';
        phoneError.classList.remove('show');
        phoneInput.classList.remove('error');
        return;
      }
      const phoneRegex = /^07\d{8}$/;
      if(!phoneRegex.test(phone)){
        phoneError.textContent = 'Phone number must be 10 digits starting with 07';
        phoneError.classList.add('show');
        phoneInput.classList.add('error');
      } else {
        phoneError.textContent = '';
        phoneError.classList.remove('show');
        phoneInput.classList.remove('error');
      }
    });
  }

  // Breeder phone validation (0xxxxxxxxx)
  const phonePrimary = $('#phonePrimary');
  const phonePrimaryError = $('#phonePrimaryError');
  const phoneSecondary = $('#phoneSecondary');
  const phoneSecondaryError = $('#phoneSecondaryError');
  const servicePhoneRegex = /^0\d{9}$/;

  function validateServicePhone(input, errorEl, isRequired){
    if(!input || !errorEl) return true;
    const val = input.value.trim();
    if(val === ''){
      if(isRequired){
        errorEl.textContent = 'Phone number is required';
        errorEl.classList.add('show');
        input.classList.add('error');
        return false;
      }
      errorEl.textContent = '';
      errorEl.classList.remove('show');
      input.classList.remove('error');
      return true;
    }
    if(!servicePhoneRegex.test(val)){
      errorEl.textContent = 'Phone number must be 10 digits starting with 0';
      errorEl.classList.add('show');
      input.classList.add('error');
      return false;
    }
    errorEl.textContent = '';
    errorEl.classList.remove('show');
    input.classList.remove('error');
    return true;
  }

  if(phonePrimary && phonePrimaryError){
    phonePrimary.addEventListener('input', ()=>validateServicePhone(phonePrimary, phonePrimaryError, true));
  }
  if(phoneSecondary && phoneSecondaryError){
    phoneSecondary.addEventListener('input', ()=>validateServicePhone(phoneSecondary, phoneSecondaryError, false));
  }

  // Cover photo preview
  const coverPhotoInput = document.getElementById('coverPhoto');
  document.addEventListener('click', e=>{
    const btn = e.target.closest('[data-for="coverPhoto"]');
    if(btn && coverPhotoInput){ coverPhotoInput.click(); }
  });
  if (coverPhotoInput) {
    coverPhotoInput.addEventListener('change', function(e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
          const preview = document.querySelector('#coverPhotoPreview img');
          if (preview) {
            preview.src = event.target.result;
            showToast('Cover photo updated (not saved yet)');
          }
        };
        reader.readAsDataURL(file);
      }
    });
  }

  // Load settings from API
  async function loadSettings() {
    try {
      const response = await fetch('/PETVET/api/breeder/get-settings.php');
      const data = await response.json();
      
      if (data.success) {
        // Populate profile
        if (data.profile) {
          const firstName = $('#first_name');
          const lastName = $('#last_name');
          const email = $('#email');
          const phone = $('#phone');
          const address = $('#address');
          
          if (firstName) firstName.value = data.profile.first_name || '';
          if (lastName) lastName.value = data.profile.last_name || '';
          if (email) email.value = data.profile.email || '';
          if (phone) phone.value = data.profile.phone || '';
          if (address) address.value = data.profile.address || '';
          
          // Update avatar
          const avatarImg = document.querySelector('#breederAvatarPreview img');
          if (avatarImg) avatarImg.src = data.profile.avatar || '/PETVET/public/images/emptyProfPic.png';
        }
        
        // Populate breeder section
        if (data.breeder) {
          const businessName = document.querySelector('input[name="business_name"]');
          const licenseNumber = document.querySelector('input[name="license_number"]');
          const workArea = document.querySelector('input[name="work_area"]');
          const experience = document.querySelector('input[name="experience"]');
          const specialization = document.querySelector('input[name="specialization"]');
          const servicesDesc = document.querySelector('textarea[name="services_description"]');
          
          if (businessName) businessName.value = data.breeder.business_name || '';
          if (licenseNumber) licenseNumber.value = data.breeder.license_number || '';
          if (workArea) workArea.value = data.breeder.service_area || '';
          if (experience) experience.value = data.breeder.experience_years || '';
          if (specialization) specialization.value = data.breeder.specializations || '';
          if (servicesDesc) servicesDesc.value = data.breeder.services_description || '';
          if(phonePrimary) phonePrimary.value = data.breeder.phone_primary || '';
          if(phoneSecondary) phoneSecondary.value = data.breeder.phone_secondary || '';
        }
        
        // Populate preferences
        if (data.preferences) {
          const emailNotif = document.querySelector('input[name="email_notifications"]');
          const smsNotif = document.querySelector('input[name="sms_notifications"]');
          const bookingReq = document.querySelector('input[name="booking_requests"]');
          
          if (emailNotif) emailNotif.checked = data.preferences.email_notifications;
          if (smsNotif) smsNotif.checked = data.preferences.sms_notifications;
          if (bookingReq) bookingReq.checked = data.preferences.auto_accept_bookings;
        }
        
        ['#formProfile','#formPassword','#formBreeder','#formPrefs'].forEach(id=>{
          const f=$(id); 
          if(f) {
            const originalState = captureFormState(f);
            f.dataset.originalState = originalState;
            f.dataset.clean='true';
            const btn = f.querySelector('button[type="submit"]');
            if(btn) updateButtonState(f, btn);
          }
        });
      }
    } catch (error) {
      console.error('Failed to load settings:', error);
      showToast('Failed to load settings');
    }
  }

  // Save settings function
  async function saveSettings(formId, dataKey) {
    console.log('Saving settings:', formId, dataKey);
    const form = $(formId);
    if (!form) return;
    
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Saving...';
    
    const formData = {};
    
    if (formId === '#formProfile') {
      const phoneVal = ($('#phone')?.value || '').trim();
      const phoneRegex = /^07\d{8}$/;
      if (phoneVal !== '' && !phoneRegex.test(phoneVal)) {
        showToast('Phone must be 10 digits starting with 07');
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
        return;
      }
      
      formData.profile = {
        first_name: $('#first_name').value.trim(),
        last_name: $('#last_name').value.trim(),
        phone: phoneVal,
        address: $('#address').value.trim()
      };
    } else if (formId === '#formPassword') {
      const np = form.querySelector('input[name="new_password"]').value.trim();
      const cp = form.querySelector('input[name="confirm_password"]').value.trim();
      if(np.length < 6){ showToast('Password too short (min 6)'); return; }
      if(np !== cp){ showToast('Passwords do not match'); return; }
      
      formData.password = {
        current_password: form.querySelector('input[name="current_password"]').value,
        new_password: np
      };
    } else if (formId === '#formBreeder') {
      const okPrimary = validateServicePhone(phonePrimary, phonePrimaryError, true);
      const okSecondary = validateServicePhone(phoneSecondary, phoneSecondaryError, false);
      if(!okPrimary || !okSecondary){
        showToast('Please fix phone number errors');
        return;
      }
      
      const businessName = form.querySelector('input[name="business_name"]');
      const licenseNumber = form.querySelector('input[name="license_number"]');
      const workArea = form.querySelector('input[name="work_area"]');
      const experience = form.querySelector('input[name="experience"]');
      const specialization = form.querySelector('input[name="specialization"]');
      const servicesDesc = form.querySelector('textarea[name="services_description"]');
      
      formData.breeder = {
        business_name: businessName ? businessName.value.trim() : '',
        license_number: licenseNumber ? licenseNumber.value.trim() : '',
        service_area: workArea ? workArea.value.trim() : '',
        experience_years: experience ? experience.value.trim() : '',
        specializations: specialization ? specialization.value.trim() : '',
        services_description: servicesDesc ? servicesDesc.value.trim() : '',
        phone_primary: phonePrimary ? phonePrimary.value.trim() : '',
        phone_secondary: phoneSecondary ? phoneSecondary.value.trim() : ''
      };
    } else if (formId === '#formPrefs') {
      const emailNotif = form.querySelector('input[name="email_notifications"]');
      const smsNotif = form.querySelector('input[name="sms_notifications"]');
      const bookingReq = form.querySelector('input[name="booking_requests"]');
      
      formData.preferences = {
        email_notifications: emailNotif ? emailNotif.checked : true,
        sms_notifications: smsNotif ? smsNotif.checked : true,
        push_notifications: false,
        auto_accept_bookings: bookingReq ? bookingReq.checked : false,
        require_deposit: false,
        show_availability_calendar: true,
        accept_emergency_bookings: false,
        show_phone_in_profile: true,
        show_address_in_profile: false,
        accept_online_payments: true
      };
    }
    
    try {
      const response = await fetch('/PETVET/api/breeder/update-settings.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData)
      });
      
      const result = await response.json();
      
      if (result.success) {
        showToast(dataKey + ' saved successfully');
        form.dataset.clean = 'true';
        updateButtonState(form, submitBtn);
        if (formId === '#formPassword') {
          form.reset();
        }
      } else {
        showToast(result.message || 'Failed to save');
      }
    } catch (error) {
      console.error('Save error:', error);
      showToast('Error saving settings');
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = originalText;
    }
  }

  const formProfile = $('#formProfile');
  const formPassword = $('#formPassword');
  const formBreeder = $('#formBreeder');
  const formPrefs = $('#formPrefs');
  
  if (formProfile) formProfile.addEventListener('submit', e => { e.preventDefault(); saveSettings('#formProfile', 'Profile'); });
  if (formPassword) formPassword.addEventListener('submit', e => { e.preventDefault(); saveSettings('#formPassword', 'Password'); });
  if (formBreeder) formBreeder.addEventListener('submit', e => { e.preventDefault(); saveSettings('#formBreeder', 'Breeder Info'); });
  if (formPrefs) formPrefs.addEventListener('submit', e => { e.preventDefault(); saveSettings('#formPrefs', 'Preferences'); });
  
  loadSettings();

  // Set current reminder value if select has value attribute (server injected)
  const reminderSelect = document.querySelector('select[name="reminder_appointments"]');
  if(reminderSelect){
    const val = reminderSelect.getAttribute('value');
    if(val && !reminderSelect.value) reminderSelect.value = val;
  }

  // Button state management functions
  function updateButtonState(form, button) {
    if (form.dataset.clean === 'true') {
      button.disabled = true;
      button.style.opacity = '0.5';
      button.style.cursor = 'not-allowed';
      button.style.pointerEvents = 'none';
    } else {
      button.disabled = false;
      button.style.opacity = '1';
      button.style.cursor = 'pointer';
      button.style.pointerEvents = 'auto';
    }
  }

  function captureFormState(form) {
    const formData = new FormData(form);
    const state = {};
    for (let [key, value] of formData.entries()) {
      state[key] = value;
    }
    return JSON.stringify(state);
  }

  function hasFormChanged(form, originalState) {
    const currentState = captureFormState(form);
    return currentState !== originalState;
  }

  // Initialize all forms with disabled buttons
  setTimeout(() => {
    ['#formProfile','#formPassword','#formBreeder','#formPrefs'].forEach(id=>{
      const f = $(id);
      if(!f) return;
      
      // Capture original state
      const originalState = captureFormState(f);
      f.dataset.originalState = originalState;
      f.dataset.clean='true';
      
      const btn = f.querySelector('button[type="submit"]');
      if(btn) {
        updateButtonState(f, btn);
      }
      
      f.addEventListener('input', ()=>{ 
        const changed = hasFormChanged(f, f.dataset.originalState);
        f.dataset.clean = changed ? 'false' : 'true';
        if(btn) updateButtonState(f, btn);
      });
      
      // Also track file input changes
      const fileInputs = f.querySelectorAll('input[type="file"]');
      fileInputs.forEach(inp => {
        inp.addEventListener('change', () => {
          if(inp.files && inp.files.length > 0) {
            f.dataset.clean='false';
            if(btn) updateButtonState(f, btn);
          }
        });
      });
    });
  }, 100);
  
  window.addEventListener('beforeunload', e=>{
    const dirty = ['#formProfile','#formPassword','#formBreeder','#formPrefs'].some(id=>{
      const f=$(id); return f && f.dataset.clean==='false';
    });
    if(dirty){ e.preventDefault(); e.returnValue=''; }
  });

  // Role switching functionality
  const roleOptions = $$('.role-option');
  const roleForm = $('#formRole');
  const roleMap = {
    'pet_owner': '/PETVET/index.php?module=pet-owner&page=my-pets',
    'trainer': '/PETVET/index.php?module=trainer&page=dashboard',
    'sitter': '/PETVET/index.php?module=sitter&page=dashboard',
    'breeder': '/PETVET/index.php?module=breeder&page=dashboard',
    'groomer': '/PETVET/index.php?module=groomer&page=services',
    'vet': '/PETVET/index.php?module=vet&page=dashboard',
    'clinic_manager': '/PETVET/index.php?module=clinic-manager&page=overview',
    'receptionist': '/PETVET/index.php?module=receptionist&page=dashboard',
    'admin': '/PETVET/index.php?module=admin&page=dashboard'
  };
  
  roleOptions.forEach(opt=>{
    const radio = opt.querySelector('input[type=radio]');
    opt.addEventListener('click', ()=>{
      if(radio && !radio.checked){
        radio.checked = true;
        roleOptions.forEach(o=>o.classList.remove('active'));
        opt.classList.add('active');
      }
    });
  });
  
  if(roleForm){
    roleForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const selected = roleForm.querySelector('input[name="active_role"]:checked');
      if(selected){
        const roleValue = selected.value;
        const redirectUrl = roleMap[roleValue];
        if(redirectUrl){
          try {
            showToast('Switching to ' + roleValue.replace('_', ' ') + '...');
            
            // Call API to switch role in session
            const response = await fetch('/PETVET/api/switch-role.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ role: roleValue })
            });
            
            const result = await response.json();
            
            if (result.success) {
              // Redirect to the new role's dashboard
              window.location.href = redirectUrl;
            } else {
              showToast(result.message || 'Failed to switch role', 'error');
            }
          } catch (error) {
            showToast('Error switching role', 'error');
            console.error('Role switch error:', error);
          }
        }
      }
    });
  }
})();
