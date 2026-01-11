// Sitter Settings JS
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

  const sitterAreaPicker = createWorkAreaPicker({
    selectId: 'sitterWorkAreaSelect',
    addBtnId: 'sitterAddWorkAreaBtn',
    chipsId: 'sitterWorkAreaChips',
    hiddenId: 'sitterWorkAreasJson'
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
  const avatarInput = $('#sitterAvatar');
  const avatarPreview = $('#sitterAvatarPreview .image-preview-item img');
  document.addEventListener('click', e=>{
    const btn = e.target.closest('[data-for="sitterAvatar"]');
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

  function countWords(text){
    return text
      .trim()
      .split(/\s+/)
      .filter(Boolean)
      .length;
  }

  // Load settings from API
  async function loadSettings() {
    try {
      const response = await fetch('/PETVET/api/sitter/get-settings.php');
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
          const avatarImg = document.querySelector('#sitterAvatarPreview img');
          if (avatarImg) avatarImg.src = data.profile.avatar || '/PETVET/public/images/emptyProfPic.png';
        }
        
        // Populate sitter section
        if (data.sitter) {
          const experienceYears = $('#experienceYears');
          const sitterDescription = $('#sitterDescription');
          const homeType = $('#homeType');
          const petTypes = $('#petTypes');
          
          if (sitterAreaPicker) sitterAreaPicker.setFromAny(data.sitter.service_areas || data.sitter.service_area || []);
          if (experienceYears) experienceYears.value = data.sitter.experience_years || '';
          if (sitterDescription) sitterDescription.value = data.sitter.bio || '';
          
          // Convert enum home_type back to display format
          if (homeType && data.sitter.home_type) {
            const homeTypeMap = {
              'apartment': 'Apartment',
              'house_with_yard': 'House with Yard',
              'house_without_yard': 'House without Yard',
              'farm': 'Farm',
              'other': 'Other'
            };
            homeType.value = homeTypeMap[data.sitter.home_type] || data.sitter.home_type;
          }
          
          if (petTypes && data.sitter.pet_types) petTypes.value = data.sitter.pet_types.join(', ');
          
          const phonePrimary = $('#phonePrimary');
          const phoneSecondary = $('#phoneSecondary');
          if (phonePrimary) phonePrimary.value = data.sitter.phone_primary || '';
          if (phoneSecondary) phoneSecondary.value = data.sitter.phone_secondary || '';
          
          // Pet types checkboxes
          if (Array.isArray(data.sitter.pet_types)) {
            $$('input[name="pet_types[]"]').forEach(cb => {
              cb.checked = data.sitter.pet_types.includes(cb.value);
            });
          }
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
        
        ['#formProfile','#formPassword','#formSitter','#formPrefs'].forEach(id=>{
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
      formData.profile = {
        first_name: $('#first_name').value.trim(),
        last_name: $('#last_name').value.trim(),
        phone: $('#phone').value.trim(),
        address: $('#address').value.trim()
      };
    } else if (formId === '#formPassword') {
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
    } else if (formId === '#formSitter') {
      const desc = $('#sitterDescription').value.trim();
      const words = desc ? countWords(desc) : 0;
      if (words > 50) {
        showToast('Description must be 50 words or less');
        return;
      }

      const phonePrimary = $('#phonePrimary').value.trim();
      const phoneSecondary = $('#phoneSecondary').value.trim();
      const phoneRegex = /^0\d{9}$/;

      if (!phoneRegex.test(phonePrimary)) {
        showToast('Primary phone must be 10 digits starting with 0');
        return;
      }
      if (phoneSecondary && !phoneRegex.test(phoneSecondary)) {
        showToast('Secondary phone must be 10 digits starting with 0');
        return;
      }
      
      const experienceYears = $('#experienceYears');
      const homeType = $('#homeType');
      const petTypesField = $('#petTypes');
      
      // Parse pet types from comma-separated string
      const petTypes = petTypesField && petTypesField.value.trim() 
        ? petTypesField.value.split(',').map(p => p.trim()).filter(p => p)
        : [];
      
      const areas = sitterAreaPicker ? sitterAreaPicker.getAreas() : [];
      if (areas.length > 5) {
        showToast('You can add up to 5 working areas');
        return;
      }

      formData.sitter = {
        service_area: areas,
        experience_years: experienceYears ? experienceYears.value.trim() : '',
        bio: desc,
        pet_types: petTypes,
        home_type: homeType ? homeType.value : '',
        phone_primary: phonePrimary,
        phone_secondary: phoneSecondary
      };
    } else if (formId === '#formPrefs') {
      formData.preferences = {
        email_notifications: $('#email_notifications').checked,
        sms_notifications: $('#sms_notifications').checked,
        push_notifications: false,
        auto_accept_bookings: $('#booking_requests').checked,
        require_deposit: false,
        show_availability_calendar: true,
        accept_emergency_bookings: false,
        show_phone_in_profile: true,
        show_address_in_profile: false,
        accept_online_payments: true
      };
    }
    
    try {
      const response = await fetch('/PETVET/api/sitter/update-settings.php', {
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
  const formSitter = $('#formSitter');
  const formPrefs = $('#formPrefs');
  
  if (formProfile) formProfile.addEventListener('submit', e => { e.preventDefault(); saveSettings('#formProfile', 'Profile'); });
  if (formPassword) formPassword.addEventListener('submit', e => { e.preventDefault(); saveSettings('#formPassword', 'Password'); });
  if (formSitter) formSitter.addEventListener('submit', e => { e.preventDefault(); saveSettings('#formSitter', 'Sitter Info'); });
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
    ['#formProfile','#formPassword','#formSitter','#formPrefs'].forEach(id=>{
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
    const dirty = ['#formProfile','#formPassword','#formSitter','#formPrefs'].some(id=>{
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
