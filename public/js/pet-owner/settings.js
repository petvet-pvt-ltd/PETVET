// Pet Owner Settings JS
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

  // Avatar preview - support pet-owner, receptionist, and clinic manager
  const avatarInput = $('#ownerAvatar') || $('#receptionistAvatar') || $('#managerAvatar');
  const avatarPreview = ($('#ownerAvatarPreview .image-preview-item img') || $('#receptionistAvatarPreview .image-preview-item img') || $('#managerAvatarPreview .image-preview-item img'));
  document.addEventListener('click', e=>{
    const btn = e.target.closest('[data-for="ownerAvatar"]') || e.target.closest('[data-for="receptionistAvatar"]') || e.target.closest('[data-for="managerAvatar"]');
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

  // Real-time phone validation
  const phoneInput = $('#phoneInput');
  const phoneError = $('#phoneError');
  if(phoneInput && phoneError){
    phoneInput.addEventListener('input', e=>{
      const phone = phoneInput.value.trim();
      
      if(phone === '') {
        phoneError.style.display = 'none';
        phoneInput.style.borderColor = '';
        return;
      }
      
      const phoneRegex = /^07\d{8}$/;
      
      if(!phone.startsWith('07')) {
        phoneError.textContent = 'Phone number must start with 07';
        phoneError.style.display = 'block';
        phoneInput.style.borderColor = '#ef4444';
      } else if(phone.length < 10) {
        phoneError.textContent = 'Phone number must be 10 digits';
        phoneError.style.display = 'block';
        phoneInput.style.borderColor = '#ef4444';
      } else if(phone.length > 10) {
        phoneError.textContent = 'Phone number cannot exceed 10 digits';
        phoneError.style.display = 'block';
        phoneInput.style.borderColor = '#ef4444';
      } else if(!phoneRegex.test(phone)) {
        phoneError.textContent = 'Invalid phone number format';
        phoneError.style.display = 'block';
        phoneInput.style.borderColor = '#ef4444';
      } else {
        phoneError.style.display = 'none';
        phoneInput.style.borderColor = '#10b981';
      }
    });
  }

  // Profile form submission
  const formProfile = $('#formProfile');
  if(formProfile){
    formProfile.addEventListener('submit', async e=>{
      e.preventDefault();
      
      // Validate phone number
      const phoneInput = formProfile.querySelector('input[name="phone"]');
      if(phoneInput && phoneInput.value.trim()) {
        const phone = phoneInput.value.trim();
        const phoneRegex = /^07\d{8}$/;
        if(!phoneRegex.test(phone)) {
          showToast('Phone number must be 10 digits starting with 07');
          return;
        }
      }
      
      // Handle avatar upload
      const avatarFile = avatarInput?.files?.[0];
      const formData = new FormData(formProfile);
      
      if(avatarFile){
        formData.set('avatar', avatarFile);
      }
      
      const submitBtn = formProfile.querySelector('button[type="submit"]');
      const originalText = submitBtn.textContent;
      submitBtn.disabled = true;
      submitBtn.textContent = 'Saving...';
      
      try {
        const response = await fetch('/PETVET/api/pet-owner/update-profile.php', {
          method: 'POST',
          body: formData
        });
        
        const result = await response.json();
        
        if(result.success){
          showToast(result.message || 'Profile updated successfully!');
          formProfile.dataset.clean = 'true';
          updateButtonState(formProfile, submitBtn);
          // Reload page to show updated avatar and data
          setTimeout(() => window.location.reload(), 1000);
        } else {
          showToast(result.message || result.errors?.join(', ') || 'Failed to update profile');
        }
      } catch(error){
        showToast('An error occurred. Please try again.');
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
      }
    });
  }

  // Password form submission
  const formPassword = $('#formPassword');
  if(formPassword){
    formPassword.addEventListener('submit', async e=>{
      e.preventDefault();
      const np = formPassword.querySelector('input[name="new_password"]').value.trim();
      const cp = formPassword.querySelector('input[name="confirm_password"]').value.trim();
      
      if(np.length < 8){ showToast('Password must be at least 8 characters'); return; }
      if(np !== cp){ showToast('Passwords do not match'); return; }
      
      const formData = new FormData(formPassword);
      const data = Object.fromEntries(formData.entries());
      
      const submitBtn = formPassword.querySelector('button[type="submit"]');
      const originalText = submitBtn.textContent;
      submitBtn.disabled = true;
      submitBtn.textContent = 'Updating...';
      
      try {
        const response = await fetch('/PETVET/api/pet-owner/update-password.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(data)
        });
        
        const result = await response.json();
        if(result.success){
          showToast(result.message || 'Password updated successfully!');
          formPassword.reset();
          formPassword.dataset.clean = 'true';
          updateButtonState(formPassword, submitBtn);
        } else {
          showToast(result.message || result.errors?.join(', ') || 'Failed to update password');
        }
      } catch(error){
        showToast('An error occurred. Please try again.');
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
      }
    });
  }

  // Preferences form (keeping as mock for now since no preferences table exists)
  const formPrefs = $('#formPrefs');
  if(formPrefs){
    formPrefs.addEventListener('submit', e=>{
      e.preventDefault();
      const submitBtn = formPrefs.querySelector('button[type="submit"]');
      showToast('Preferences saved');
      formPrefs.dataset.clean = 'true';
      if(submitBtn) updateButtonState(formPrefs, submitBtn);
    });
  }

  // Set current reminder value if select has value attribute (server injected)
  const reminderSelect = document.querySelector('select[name="reminder_appointments"]');
  if(reminderSelect){
    const val = reminderSelect.getAttribute('value');
    if(val && !reminderSelect.value) reminderSelect.value = val;
  }

  // Dirty form tracking and button state management
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
    ['#formProfile','#formPassword','#formPrefs'].forEach(id=>{
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
      
      // Also track file input changes for avatar
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
    const dirty = ['#formProfile','#formPassword','#formPrefs'].some(id=>{
      const f=$(id); return f && f.dataset.clean==='false';
    });
    if(dirty){ e.preventDefault(); e.returnValue=''; }
  });

  // Role switching functionality
  const roleOptions = $$('.role-option');
  const roleForm = $('#formRole');
  
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
    roleForm.addEventListener('submit', async e=>{
      e.preventDefault();
      const selected = roleForm.querySelector('input[name="active_role"]:checked');
      if(selected){
        const roleValue = selected.value;
        
        // Show loading state
        const submitBtn = roleForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Switching...';
        
        try {
          // Call API to switch role
          const response = await fetch('/PETVET/api/switch-role.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({ role: roleValue })
          });
          
          const result = await response.json();
          
          if (result.success) {
            showToast('Role switched successfully!');
            setTimeout(() => {
              window.location.href = result.redirect;
            }, 500);
          } else {
            showToast(result.message || 'Failed to switch role', 'error');
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
          }
        } catch (error) {
          showToast('An error occurred. Please try again.', 'error');
          submitBtn.disabled = false;
          submitBtn.textContent = originalText;
        }
      }
    });
  }
})();
