// UI helpers
const $ = (s, el=document) => el.querySelector(s);
const $$ = (s, el=document) => Array.from(el.querySelectorAll(s));
const toast = (msg, type='success') => { 
  const t = $('#toast'); 
  t.textContent = msg; 
  t.className = 'toast show ' + type;
  setTimeout(()=>t.classList.remove('show'), 3000); 
};

// Form change tracking
const formStates = new Map();
const fileInputStates = new Map();

function captureFormState(formId) {
  const form = $(formId);
  if (!form) return;
  const data = new FormData(form);
  // Store text/checkbox/radio inputs, excluding file inputs
  const entries = [...data.entries()].filter(([key, value]) => !(value instanceof File));
  formStates.set(formId, JSON.stringify(entries));
  
  // Track file inputs separately
  const fileInputs = form.querySelectorAll('input[type="file"]');
  fileInputStates.set(formId, Array.from(fileInputs).every(input => !input.files.length));
}

function hasFormChanged(formId) {
  const form = $(formId);
  if (!form) return false;
  
  // Check if any file input has a file selected
  const fileInputs = form.querySelectorAll('input[type="file"]');
  const hasFiles = Array.from(fileInputs).some(input => input.files.length > 0);
  
  // If files are selected, form has changed
  if (hasFiles) return true;
  
  // Otherwise check text inputs
  const data = new FormData(form);
  const entries = [...data.entries()].filter(([key, value]) => !(value instanceof File));
  const current = JSON.stringify(entries);
  return formStates.get(formId) !== current;
}

function updateButtonState(formId, buttonSelector) {
  const btn = $(buttonSelector);
  if (!btn) return;
  if (hasFormChanged(formId)) {
    btn.disabled = false;
    btn.style.opacity = '1';
  } else {
    btn.disabled = true;
    btn.style.opacity = '0.5';
  }
}

// API helper
async function apiRequest(url, data) {
  try {
    const response = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams(data)
    });
    return await response.json();
  } catch (error) {
    console.error('API Error:', error);
    return { success: false, message: 'Network error occurred' };
  }
}

// Image previewers
function bindImagePreview(inputId, previewContainerId, formId = null) {
  const input = document.getElementById(inputId);
  const preview = document.getElementById(previewContainerId);
  if (!input || !preview) return;
  input.addEventListener('change', (e) => {
    const files = Array.from(e.target.files || []);
    if (!files.length) return;
    preview.innerHTML = '';
    files.slice(0, 1).forEach(file => {
      const url = URL.createObjectURL(file);
      const item = document.createElement('div');
      item.className = 'image-preview-item' + (preview.classList.contains('cover') ? ' hero' : '');
      const img = document.createElement('img');
      img.src = url;
      item.appendChild(img);
      preview.appendChild(item);
    });
    
    // Trigger form change detection if formId is provided
    if (formId) {
      const form = $(formId);
      if (form) {
        // Manually update the form state by dispatching an input event
        form.dispatchEvent(new Event('input', { bubbles: true }));
      }
    }
  });
}

function initHoursToggles() {
  $$('.hours-row').forEach(row => {
    const toggle = $('.open-toggle', row);
    const start = $('.time-start', row);
    const end = $('.time-end', row);
    toggle.addEventListener('change', () => {
      const on = toggle.checked;
      start.disabled = !on; end.disabled = !on;
    });
  });
}

function initHolidays() {
  const list = $('#holidayList');
  const addBtn = $('#btnAddHoliday');
  const newInput = $('#newHoliday');
  if (!list || !addBtn || !newInput) return;

  addBtn.addEventListener('click', () => {
    if (!newInput.value) { toast('Pick a date'); return; }
  const div = document.createElement('div');
  div.className = 'holiday-item';
  div.innerHTML = `<input type="date" value="${newInput.value}" />\
<button type="button" class="icon-btn remove" aria-label="Remove holiday" title="Remove">×</button>`;
    list.appendChild(div);
    newInput.value = '';
  });
  list.addEventListener('click', (e) => {
    if (e.target.classList.contains('remove')) {
      e.target.closest('.holiday-item').remove();
    }
  });
}

function initWeeklySchedule() {
  $$('.schedule-day').forEach(dayRow => {
    const toggle = dayRow.querySelector('.toggle-switch input[type="checkbox"]');
    const startInput = dayRow.querySelector('input[type="time"][name*="_start"]');
    const endInput = dayRow.querySelector('input[type="time"][name*="_end"]');
    
    if (!toggle || !startInput || !endInput) return;
    
    toggle.addEventListener('change', () => {
      const isEnabled = toggle.checked;
      startInput.disabled = !isEnabled;
      endInput.disabled = !isEnabled;
      
      if (isEnabled) {
        dayRow.classList.add('active');
      } else {
        dayRow.classList.remove('active');
      }
    });
  });
}

function initBlockedDays() {
  const dateInput = $('#newBlockedDate');
  const reasonInput = $('#newBlockedReason');
  const addBtn = $('#btnAddBlockedDay');
  const list = $('#blockedDaysList');
  const saveBtn = $('#btnSaveBlockedDays');
  
  if (!dateInput || !reasonInput || !addBtn || !list) return;

  // Capture initial state of blocked days
  let initialBlockedDays = getBlockedDaysState();
  
  // Disable save button initially
  if (saveBtn) {
    saveBtn.disabled = true;
    saveBtn.style.opacity = '0.5';
  }
  
  // Helper to get current state
  function getBlockedDaysState() {
    const days = [];
    $$('.blocked-day-item').forEach(item => {
      const date = item.getAttribute('data-date');
      const reason = item.querySelector('.blocked-reason')?.textContent || '';
      if (date) days.push({ date, reason });
    });
    return JSON.stringify(days);
  }
  
  // Helper to update save button state
  function updateSaveButtonState() {
    if (!saveBtn) return;
    const currentState = getBlockedDaysState();
    if (currentState !== initialBlockedDays) {
      saveBtn.disabled = false;
      saveBtn.style.opacity = '1';
    } else {
      saveBtn.disabled = true;
      saveBtn.style.opacity = '0.5';
    }
  }

  // Add new blocked day
  addBtn.addEventListener('click', () => {
    const dateValue = dateInput.value;
    const reasonValue = reasonInput.value.trim();
    
    if (!dateValue) {
      toast('Please select a date');
      return;
    }
    if (!reasonValue) {
      toast('Please enter a reason');
      return;
    }
    
    // Check if date already blocked
    const existingItem = list.querySelector(`[data-date="${dateValue}"]`);
    if (existingItem) {
      toast('This date is already blocked');
      return;
    }
    
    // Remove empty state if exists
    const emptyState = list.querySelector('.empty-state');
    if (emptyState) emptyState.remove();
    
    // Format date for display
    const dateObj = new Date(dateValue + 'T00:00:00');
    const displayDate = dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    
    // Create new blocked day item
    const item = document.createElement('div');
    item.className = 'blocked-day-item';
    item.setAttribute('data-date', dateValue);
    item.innerHTML = `
      <div class="blocked-date">${displayDate}</div>
      <div class="blocked-reason">${reasonValue}</div>
      <button type="button" class="btn-remove" data-action="remove">Remove</button>
    `;
    
    list.appendChild(item);
    
    // Clear inputs
    dateInput.value = '';
    reasonInput.value = '';
    
    // Update save button state
    updateSaveButtonState();
    
    toast('Date added to blocked list');
  });
  
  // Remove blocked day
  list.addEventListener('click', (e) => {
    const removeBtn = e.target.closest('[data-action="remove"]');
    if (!removeBtn) return;
    
    const item = removeBtn.closest('.blocked-day-item');
    if (item) {
      item.remove();
      
      // Show empty state if no items left
      if (!list.querySelector('.blocked-day-item')) {
        const emptyDiv = document.createElement('div');
        emptyDiv.className = 'empty-state';
        emptyDiv.textContent = 'No blocked days yet. Add dates you won\'t be available.';
        list.appendChild(emptyDiv);
      }
      
      // Update save button state
      updateSaveButtonState();
      
      toast('Date removed from blocked list');
    }
  });
  
  // Save blocked days
  if (saveBtn) {
    saveBtn.addEventListener('click', async () => {
      const blockedDays = [];
      $$('.blocked-day-item').forEach(item => {
        const date = item.getAttribute('data-date');
        const reason = item.querySelector('.blocked-reason')?.textContent || '';
        if (date) {
          blockedDays.push({ date, reason });
        }
      });
      
      const result = await fetch('/PETVET/api/clinic-manager/settings.php?action=save_blocked_days', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ dates: blockedDays })
      }).then(r => r.json());
      
      if (result.success) {
        toast(result.message);
        // Update initial state after successful save
        initialBlockedDays = getBlockedDaysState();
        updateSaveButtonState();
      } else {
        toast(result.message, 'error');
      }
    });
  }
}

function wireForms() {
  // Profile Form
  const formProfile = $('#formProfile');
  if (formProfile) {
    captureFormState('#formProfile');
    formProfile.addEventListener('input', () => updateButtonState('#formProfile', '#formProfile button[type="submit"]'));
    formProfile.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(formProfile);
      formData.append('action', 'update_profile');
      
      const result = await apiRequest('/PETVET/api/clinic-manager/settings.php', Object.fromEntries(formData));
      if (result.success) {
        toast(result.message);
        captureFormState('#formProfile');
        updateButtonState('#formProfile', '#formProfile button[type="submit"]');
      } else {
        toast(result.message, 'error');
      }
    });
  }

  // Password Form
  const formPassword = $('#formPassword');
  if (formPassword) {
    captureFormState('#formPassword');
    formPassword.addEventListener('input', () => updateButtonState('#formPassword', '#formPassword button[type="submit"]'));
    formPassword.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(formPassword);
      formData.append('action', 'update_password');
      
      const result = await apiRequest('/PETVET/api/clinic-manager/settings.php', Object.fromEntries(formData));
      if (result.success) {
        toast(result.message);
        formPassword.reset();
        captureFormState('#formPassword');
        updateButtonState('#formPassword', '#formPassword button[type="submit"]');
      } else {
        toast(result.message, 'error');
      }
    });
  }

  // Clinic Form
  const formClinic = $('#formClinic');
  if (formClinic) {
    captureFormState('#formClinic');
    updateButtonState('#formClinic', '#formClinic button[type="submit"]'); // Disable initially
    formClinic.addEventListener('input', () => updateButtonState('#formClinic', '#formClinic button[type="submit"]'));
    formClinic.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(formClinic);
      formData.append('action', 'update_clinic');
      
      // Send FormData directly to preserve file uploads
      try {
        const response = await fetch('/PETVET/api/clinic-manager/settings.php', {
          method: 'POST',
          body: formData
        });
        const result = await response.json();
        
        if (result.success) {
          toast(result.message);
          captureFormState('#formClinic');
          updateButtonState('#formClinic', '#formClinic button[type="submit"]');
          // Reload page to show updated clinic name everywhere
          setTimeout(() => location.reload(), 1500);
        } else {
          toast(result.message, 'error');
        }
      } catch (error) {
        console.error('Upload error:', error);
        toast('Failed to update clinic profile', 'error');
      }
    });
  }

  // Preferences Form
  const formPrefs = $('#formPrefs');
  if (formPrefs) {
    captureFormState('#formPrefs');
    formPrefs.addEventListener('input', () => updateButtonState('#formPrefs', '#formPrefs button[type="submit"]'));
    formPrefs.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(formPrefs);
      formData.append('action', 'update_preferences');
      
      const result = await apiRequest('/PETVET/api/clinic-manager/settings.php', Object.fromEntries(formData));
      if (result.success) {
        toast(result.message);
        captureFormState('#formPrefs');
        updateButtonState('#formPrefs', '#formPrefs button[type="submit"]');
      } else {
        toast(result.message, 'error');
      }
    });
  }

  // Weekly Schedule Form
  const formWeeklySchedule = $('#formWeeklySchedule');
  if (formWeeklySchedule) {
    captureFormState('#formWeeklySchedule');
    updateButtonState('#formWeeklySchedule', '#formWeeklySchedule button[type="submit"]'); // Disable initially
    formWeeklySchedule.addEventListener('input', () => updateButtonState('#formWeeklySchedule', '#formWeeklySchedule button[type="submit"]'));
    formWeeklySchedule.addEventListener('change', () => updateButtonState('#formWeeklySchedule', '#formWeeklySchedule button[type="submit"]'));
    formWeeklySchedule.addEventListener('submit', async (e) => {
      e.preventDefault();
      
      // Collect schedule data
      const scheduleData = {};
      $$('.schedule-day').forEach(dayRow => {
        const dayId = dayRow.getAttribute('data-day');
        const toggle = dayRow.querySelector('.toggle-switch input[type="checkbox"]');
        const startInput = dayRow.querySelector('input[type="time"][name*="_start"]');
        const endInput = dayRow.querySelector('input[type="time"][name*="_end"]');
        
        scheduleData[dayId] = {
          enabled: toggle.checked,
          start: startInput.value,
          end: endInput.value
        };
      });
      
      const result = await fetch('/PETVET/api/clinic-manager/settings.php?action=update_weekly_schedule', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ schedule: scheduleData })
      }).then(r => r.json());
      
      if (result.success) {
        toast(result.message);
        captureFormState('#formWeeklySchedule');
        updateButtonState('#formWeeklySchedule', '#formWeeklySchedule button[type="submit"]');
      } else {
        toast(result.message, 'error');
      }
    });
  }
}

// Init
bindImagePreview('mgrAvatar', 'mgrAvatarPreview');
bindImagePreview('clinicLogo', 'clinicLogoPreview', '#formClinic');
bindImagePreview('clinicCover', 'clinicCoverPreview', '#formClinic');
initHoursToggles();
initHolidays();
initWeeklySchedule();
initBlockedDays();
wireForms();

// Hook custom uploader buttons to hidden inputs
document.addEventListener('click', (e) => {
  const btn = e.target.closest('button[data-for]');
  if (!btn) return;
  const id = btn.getAttribute('data-for');
  const input = document.getElementById(id);
  if (input) input.click();
});

// Active quick-nav highlighting (IntersectionObserver)
const links = Array.from(document.querySelectorAll('.quick-nav a'));
const sectionEls = links.map(a => document.querySelector(a.getAttribute('href'))).filter(Boolean);

if ('IntersectionObserver' in window) {
  const io = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const id = entry.target.id;
        links.forEach(l => l.classList.toggle('active', l.getAttribute('href') === `#${id}`));
      }
    });
  }, {rootMargin: '-45% 0px -45% 0px', threshold:[0,1]});
  sectionEls.forEach(sec => io.observe(sec));
} else {
  // Fallback: mark first section
  if (sectionEls[0]) links[0].classList.add('active');
}

// Reveal animations for cards
const cmCards = Array.from(document.querySelectorAll('.settings-grid > section.card'));
if('IntersectionObserver' in window){
  const revealObs = new IntersectionObserver(es=>{
    es.forEach(en=>{ if(en.isIntersecting){ en.target.classList.add('reveal-in'); revealObs.unobserve(en.target);} });
  },{threshold:.2});
  cmCards.forEach(c=>{c.classList.add('reveal-ready'); revealObs.observe(c);});
}
