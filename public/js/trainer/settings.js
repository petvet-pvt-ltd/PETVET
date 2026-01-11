// Pet Owner Settings JS
(function(){
  const $ = s=>document.querySelector(s);
  const $$ = s=>Array.from(document.querySelectorAll(s));
  const toastEl = $('#toast');
  function showToast(msg){
    if(!toastEl) return; toastEl.textContent = msg; toastEl.classList.add('show');
    clearTimeout(showToast._t); showToast._t = setTimeout(()=>toastEl.classList.remove('show'),2600);
  }

  const SRI_LANKA_DISTRICTS = [
    'Ampara','Anuradhapura','Badulla','Batticaloa','Colombo','Galle','Gampaha','Hambantota',
    'Jaffna','Kalutara','Kandy','Kegalle','Kilinochchi','Kurunegala','Mannar','Matale',
    'Matara','Monaragala','Mullaitivu','Nuwara Eliya','Polonnaruwa','Puttalam','Ratnapura',
    'Trincomalee','Vavuniya'
  ];

  function parseAreaList(value) {
    if (Array.isArray(value)) {
      return value.map(v => String(v).trim()).filter(Boolean);
    }
    const str = (value ?? '').toString().trim();
    if (!str) return [];

    if (str.startsWith('[')) {
      try {
        const arr = JSON.parse(str);
        if (Array.isArray(arr)) return arr.map(v => String(v).trim()).filter(Boolean);
      } catch (e) {
        // ignore
      }
    }
    return str.split(',').map(s => s.trim()).filter(Boolean);
  }

  function uniqueCaseInsensitive(items) {
    const seen = new Set();
    const out = [];
    items.forEach(item => {
      const key = String(item).toLowerCase();
      if (!seen.has(key)) {
        seen.add(key);
        out.push(String(item));
      }
    });
    return out;
  }

  function canonicalDistrict(name) {
    const n = String(name || '').trim();
    if (!n) return '';
    const match = SRI_LANKA_DISTRICTS.find(d => d.toLowerCase() === n.toLowerCase());
    return match || n;
  }

  function createWorkAreaPicker({ selectId, addBtnId, chipsId, hiddenId }) {
    const selectEl = document.getElementById(selectId);
    const addBtn = document.getElementById(addBtnId);
    const chipsEl = document.getElementById(chipsId);
    const hiddenEl = document.getElementById(hiddenId);
    if (!selectEl || !addBtn || !chipsEl || !hiddenEl) return null;

    let areas = [];

    function render() {
      chipsEl.innerHTML = '';
      areas.forEach(area => {
        const chip = document.createElement('span');
        chip.style.display = 'inline-flex';
        chip.style.alignItems = 'center';
        chip.style.gap = '8px';
        chip.style.padding = '6px 10px';
        chip.style.border = '1px solid #e2e8f0';
        chip.style.borderRadius = '999px';
        chip.style.background = '#f8fafc';
        chip.style.fontSize = '13px';

        const label = document.createElement('span');
        label.textContent = area;

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.textContent = '×';
        btn.setAttribute('aria-label', 'Remove ' + area);
        btn.style.border = 'none';
        btn.style.background = 'transparent';
        btn.style.cursor = 'pointer';
        btn.style.fontSize = '16px';
        btn.style.lineHeight = '1';
        btn.addEventListener('click', () => {
          areas = areas.filter(a => a.toLowerCase() !== area.toLowerCase());
          sync();
        });

        chip.appendChild(label);
        chip.appendChild(btn);
        chipsEl.appendChild(chip);
      });
    }

    function sync() {
      areas = uniqueCaseInsensitive(areas.map(canonicalDistrict)).filter(Boolean);
      areas = areas.slice(0, 5);
      hiddenEl.value = JSON.stringify(areas);
      hiddenEl.dispatchEvent(new Event('input', { bubbles: true }));
      render();
    }

    function addSelected() {
      const selected = canonicalDistrict(selectEl.value);
      if (!selected) return;
      const exists = areas.some(a => a.toLowerCase() === selected.toLowerCase());
      if (!exists && areas.length >= 5) {
        showToast('You can add up to 5 working areas');
        return;
      }
      areas.push(selected);
      sync();
    }

    // Fill select
    selectEl.innerHTML = '';
    const ph = document.createElement('option');
    ph.value = '';
    ph.textContent = 'Select a district...';
    ph.disabled = true;
    ph.selected = true;
    selectEl.appendChild(ph);
    SRI_LANKA_DISTRICTS.forEach(d => {
      const opt = document.createElement('option');
      opt.value = d;
      opt.textContent = d;
      selectEl.appendChild(opt);
    });

    addBtn.addEventListener('click', addSelected);
    selectEl.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        addSelected();
      }
    });

    return {
      setFromAny(value) {
        areas = parseAreaList(value);
        sync();
      },
      getAreas() {
        return areas.slice();
      }
    };
  }

  const trainerAreaPicker = createWorkAreaPicker({
    selectId: 'trainerWorkAreaSelect',
    addBtnId: 'trainerAddWorkAreaBtn',
    chipsId: 'trainerWorkAreaChips',
    hiddenId: 'trainerWorkAreasJson'
  });

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
  const avatarInput = $('#trainerAvatar');
  const avatarPreview = $('#trainerAvatarPreview .image-preview-item img');
  document.addEventListener('click', e=>{
    const btn = e.target.closest('[data-for="trainerAvatar"]');
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

  // Cover photo preview
  const coverPhotoInput = document.getElementById('coverPhoto');
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
      console.log('Fetching from:', '/PETVET/api/trainer/get-settings.php');
      const response = await fetch('/PETVET/api/trainer/get-settings.php');
      console.log('Response status:', response.status, response.statusText);
      
      const text = await response.text();
      console.log('Response text:', text.substring(0, 200));
      
      const data = JSON.parse(text);
      
      if (data.success) {
        // Populate profile section
        if (data.profile) {
          $('#first_name').value = data.profile.first_name || '';
          $('#last_name').value = data.profile.last_name || '';
          $('#email').value = data.profile.email || '';
          $('#phone').value = data.profile.phone || '';
          $('#address').value = data.profile.address || '';
          if (avatarPreview) {
            const avatarUrl = data.profile.avatar || '/PETVET/public/images/emptyProfPic.png';
            // Add timestamp to prevent caching
            avatarPreview.src = avatarUrl + (avatarUrl.includes('?') ? '&' : '?') + 't=' + Date.now();
          }
        }
        
        // Populate trainer section
        if (data.trainer) {
          const t = data.trainer;
          const assocField = $('input[name="association_name"]');
          const expField = $('input[name="experience"]');
          const certField = $('textarea[name="certifications"]');
          const specField = $('input[name="specialization"]');
          const bioField = $('textarea[name="bio"]');
          const phone1Field = $('input[name="phone_primary"]');
          const phone2Field = $('input[name="phone_secondary"]');
          
          if (assocField) assocField.value = t.business_name || '';
          if (trainerAreaPicker) trainerAreaPicker.setFromAny(t.service_areas || t.service_area || []);
          if (expField) expField.value = t.experience_years || '';
          if (certField) certField.value = t.certifications || '';
          if (specField) specField.value = t.specializations || '';
          if (bioField) bioField.value = t.bio || '';
          if (phone1Field) phone1Field.value = t.phone_primary || '';
          if (phone2Field) phone2Field.value = t.phone_secondary || '';
          
          // Training types
          const basicToggle = $('#trainingBasicToggle');
          const basicCharge = $('#trainingBasicCharge');
          const intToggle = $('#trainingIntermediateToggle');
          const intCharge = $('#trainingIntermediateCharge');
          const advToggle = $('#trainingAdvancedToggle');
          const advCharge = $('#trainingAdvancedCharge');
          
          if (basicToggle) basicToggle.checked = t.training_basic_enabled;
          if (basicCharge) {
            basicCharge.value = t.training_basic_charge || '';
            basicCharge.disabled = !t.training_basic_enabled;
          }
          
          if (intToggle) intToggle.checked = t.training_intermediate_enabled;
          if (intCharge) {
            intCharge.value = t.training_intermediate_charge || '';
            intCharge.disabled = !t.training_intermediate_enabled;
          }
          
          if (advToggle) advToggle.checked = t.training_advanced_enabled;
          if (advCharge) {
            advCharge.value = t.training_advanced_charge || '';
            advCharge.disabled = !t.training_advanced_enabled;
          }
        }
        
        // Populate preferences
        if (data.preferences) {
          const p = data.preferences;
          const emailNotif = $('input[name="email_notifications"]');
          if (emailNotif) emailNotif.checked = p.email_notifications;
        }
        
        // Mark forms as clean
        ['#formProfile','#formPassword','#formTrainer','#formPrefs'].forEach(id=>{
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
    
    if (formId === '#formProfile') {
      // Use FormData for profile to support avatar upload
      const fd = new FormData();
      fd.append('profile[first_name]', $('#first_name').value.trim());
      fd.append('profile[last_name]', $('#last_name').value.trim());
      fd.append('profile[phone]', $('#phone').value.trim());
      fd.append('profile[address]', $('#address').value.trim());
      
      // Add avatar file if selected
      const avatarInput = $('#trainerAvatar');
      if (avatarInput && avatarInput.files && avatarInput.files[0]) {
        fd.append('avatar', avatarInput.files[0]);
      }
      
      try {
        const response = await fetch('/PETVET/api/trainer/update-settings.php', {
          method: 'POST',
          body: fd
        });
        
        const result = await response.json();
        
        if (result.success) {
          showToast('Profile saved successfully');
          form.dataset.clean = 'true';
          updateButtonState(form, submitBtn);
          // Update avatar preview if new avatar was uploaded
          if (result.avatar && avatarPreview) {
            // Add timestamp to prevent caching
            avatarPreview.src = result.avatar + '?t=' + Date.now();
          }
          // Clear the file input
          if (avatarInput) {
            avatarInput.value = '';
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
      return;
    }
    
    const formData = {};
    
    if (formId === '#formPassword') {
      const current = form.querySelector('input[name="current_password"]').value;
      const newPwd = form.querySelector('input[name="new_password"]').value;
      const confirm = form.querySelector('input[name="confirm_password"]').value;
      
      if (newPwd.length < 6) {
        showToast('Password too short (min 6)');
        return;
      }
      if (newPwd !== confirm) {
        showToast('Passwords do not match');
        return;
      }
      
      formData.password = {
        current_password: current,
        new_password: newPwd
      };
    } else if (formId === '#formTrainer') {
      const assocField = form.querySelector('input[name="association_name"]');
      const expField = form.querySelector('input[name="experience"]');
      const certField = form.querySelector('textarea[name="certifications"]');
      const specField = form.querySelector('input[name="specialization"]');
      const bioField = form.querySelector('textarea[name="bio"]');
      const phone1Field = form.querySelector('input[name="phone_primary"]');
      const phone2Field = form.querySelector('input[name="phone_secondary"]');
      
      const basicToggle = $('#trainingBasicToggle');
      const basicCharge = $('#trainingBasicCharge');
      const intToggle = $('#trainingIntermediateToggle');
      const intCharge = $('#trainingIntermediateCharge');
      const advToggle = $('#trainingAdvancedToggle');
      const advCharge = $('#trainingAdvancedCharge');

      const anyTrainingEnabled = (basicToggle && basicToggle.checked) || (intToggle && intToggle.checked) || (advToggle && advToggle.checked);
      if (!anyTrainingEnabled) {
        showToast('Note: You won\'t appear on Services until at least one training type is enabled.');
      }

      const areas = trainerAreaPicker ? trainerAreaPicker.getAreas() : [];
      if (!areas.length) {
        showToast('Please add at least 1 working area');
        return;
      }
      
      formData.trainer = {
        business_name: assocField ? assocField.value.trim() : '',
        service_area: areas,
        experience_years: expField ? expField.value.trim() : '',
        certifications: certField ? certField.value.trim() : '',
        specializations: specField ? specField.value.trim() : '',
        bio: bioField ? bioField.value.trim() : '',
        phone_primary: phone1Field ? phone1Field.value.trim() : '',
        phone_secondary: phone2Field ? phone2Field.value.trim() : '',
        training_basic_enabled: basicToggle ? basicToggle.checked : false,
        training_basic_charge: basicCharge ? basicCharge.value.trim() : '',
        training_intermediate_enabled: intToggle ? intToggle.checked : false,
        training_intermediate_charge: intCharge ? intCharge.value.trim() : '',
        training_advanced_enabled: advToggle ? advToggle.checked : false,
        training_advanced_charge: advCharge ? advCharge.value.trim() : ''
      };
    } else if (formId === '#formPrefs') {
      const emailNotif = form.querySelector('input[name="email_notifications"]');
      
      formData.preferences = {
        email_notifications: emailNotif ? emailNotif.checked : true,
        sms_notifications: false,
        push_notifications: false,
        auto_accept_bookings: false,
        require_deposit: false,
        show_availability_calendar: true,
        accept_emergency_bookings: false,
        show_phone_in_profile: true,
        show_address_in_profile: false,
        accept_online_payments: true
      };
    }
    
    try {
      const response = await fetch('/PETVET/api/trainer/update-settings.php', {
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

  // Attach form handlers
  const formProfile = $('#formProfile');
  const formPassword = $('#formPassword');
  const formTrainer = $('#formTrainer');
  const formPrefs = $('#formPrefs');
  
  if (formProfile) formProfile.addEventListener('submit', e => { e.preventDefault(); saveSettings('#formProfile', 'Profile'); });
  if (formPassword) formPassword.addEventListener('submit', e => { e.preventDefault(); saveSettings('#formPassword', 'Password'); });
  if (formTrainer) formTrainer.addEventListener('submit', e => { e.preventDefault(); saveSettings('#formTrainer', 'Trainer Info'); });
  if (formPrefs) formPrefs.addEventListener('submit', e => { e.preventDefault(); saveSettings('#formPrefs', 'Preferences'); });
  
  // Load settings on page load
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
    ['#formProfile','#formPassword','#formTrainer','#formPrefs'].forEach(id=>{
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
    const dirty = ['#formProfile','#formPassword','#formTrainer','#formPrefs'].some(id=>{
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
