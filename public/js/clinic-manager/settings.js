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

// ========== Shop & Delivery Settings ==========
function initShopSettings() {
  const addBtn = $('#btnAddDeliveryRule');
  const rulesList = $('#deliveryRulesList');
  const template = $('#deliveryRuleTemplate');
  const baseChargeInput = $('#baseDeliveryCharge');
  const maxDistanceInput = $('#maxDeliveryDistance');
  const previewBase = $('#previewBase');
  const previewList = $('#deliveryPreview .preview-list');
  const form = $('#formShopSettings');
  
  if (!addBtn || !rulesList || !template) return;
  
  let ruleCount = 0;
  
  // Load existing settings from database
  async function loadSettings() {
    try {
      const response = await apiRequest('/PETVET/api/clinic-manager/delivery-settings.php', { action: 'get_settings' });
      if (response.success && response.settings) {
        const settings = response.settings;
        
        // Set base values
        if (baseChargeInput) baseChargeInput.value = settings.base_delivery_charge || 0;
        if (maxDistanceInput) maxDistanceInput.value = settings.max_delivery_distance || 0;
        if ($('#maxItemsPerOrder')) $('#maxItemsPerOrder').value = settings.max_items_per_order || 10;
        
        // Load delivery rules
        if (settings.delivery_rules && settings.delivery_rules.length > 0) {
          // Clear empty state
          rulesList.innerHTML = '';
          
          settings.delivery_rules.forEach(rule => {
            addRuleFromData(rule.distance, rule.charge_per_km);
          });
        }
        
        // Capture initial form state after loading
        setTimeout(() => {
          captureFormState('#formShopSettings');
          updateButtonState('#formShopSettings', '#formShopSettings button[type="submit"]');
        }, 100);
      }
    } catch (error) {
      console.error('Error loading delivery settings:', error);
    }
  }
  
  // Add a rule with pre-filled data
  function addRuleFromData(distance, charge) {
    const clone = template.content.cloneNode(true);
    const ruleItem = clone.querySelector('.delivery-rule-item');
    ruleItem.dataset.ruleId = ruleCount++;
    
    const distanceInput = clone.querySelector('.distance-value');
    const chargeInput = clone.querySelector('.charge-value');
    
    distanceInput.value = distance;
    chargeInput.value = charge;
    
    rulesList.appendChild(clone);
    
    // Wire up the rule after adding to DOM
    const addedRule = rulesList.querySelector(`[data-rule-id="${ruleCount - 1}"]`);
    if (addedRule) wireDeliveryRule(addedRule);
  }
  
  // Load settings on initialization
  loadSettings();
  
  // Update preview when base charge changes
  if (baseChargeInput && previewBase) {
    baseChargeInput.addEventListener('input', () => {
      previewBase.textContent = parseFloat(baseChargeInput.value || 0).toFixed(2);
    });
  }
  
  // Revalidate all rules when max distance changes
  if (maxDistanceInput) {
    maxDistanceInput.addEventListener('input', () => {
      const allRules = rulesList.querySelectorAll('.delivery-rule-item');
      allRules.forEach(rule => validateDistanceOrder(rule));
    });
  }
  
  // Add new delivery rule
  addBtn.addEventListener('click', () => {
    const clone = template.content.cloneNode(true);
    const ruleItem = clone.querySelector('.delivery-rule-item');
    ruleItem.dataset.ruleId = ruleCount++;
    
    // Remove empty state if exists
    const emptyState = rulesList.querySelector('.empty-state');
    if (emptyState) emptyState.remove();
    
    rulesList.appendChild(clone);
    
    // Wire up the new rule
    const newRule = rulesList.querySelector(`[data-rule-id="${ruleCount - 1}"]`);
    wireDeliveryRule(newRule);
    
    // Update preview
    updateDeliveryPreview();
    
    // Trigger form change
    if (form) form.dispatchEvent(new Event('input', { bubbles: true }));
  });
  
  // Wire a delivery rule item
  function wireDeliveryRule(ruleItem) {
    const removeBtn = ruleItem.querySelector('.btn-remove-rule');
    const distanceInput = ruleItem.querySelector('.distance-value');
    const chargeInput = ruleItem.querySelector('.charge-value');
    const exampleCharge = ruleItem.querySelector('.example-charge');
    const exampleCondition = ruleItem.querySelector('.example-condition');
    
    // Remove rule
    removeBtn.addEventListener('click', () => {
      ruleItem.remove();
      updateDeliveryPreview();
      
      // Show empty state if no rules left
      if (!rulesList.querySelector('.delivery-rule-item')) {
        rulesList.innerHTML = '<div class="empty-state">No delivery rules yet. Click "Add Rule" to create distance-based pricing.</div>';
      }
      
      // Trigger form change
      if (form) form.dispatchEvent(new Event('input', { bubbles: true }));
    });
    
    // Update example text
    function updateExample() {
      const distance = distanceInput.value || '5';
      const charge = chargeInput.value || '10';
      
      exampleCharge.textContent = charge;
      exampleCondition.textContent = `> ${distance} km`;
    }
    
    // Validate distance is greater than previous rules
    distanceInput.addEventListener('input', () => {
      validateDistanceOrder(ruleItem);
      updateExample();
      updateDeliveryPreview();
      if (form) form.dispatchEvent(new Event('input', { bubbles: true }));
    });
    
    chargeInput.addEventListener('input', () => {
      updateExample();
      updateDeliveryPreview();
      if (form) form.dispatchEvent(new Event('input', { bubbles: true }));
    });
    
    // Initial example update
    updateExample();
  }
  
  // Validate that distance values are in increasing order
  function validateDistanceOrder(currentRule) {
    const allRules = Array.from(rulesList.querySelectorAll('.delivery-rule-item'));
    const currentIndex = allRules.indexOf(currentRule);
    const currentInput = currentRule.querySelector('.distance-value');
    const currentValue = parseFloat(currentInput.value);
    
    if (isNaN(currentValue)) return;
    
    // Check against maximum delivery distance
    const maxDistance = parseFloat(maxDistanceInput?.value || 0);
    if (maxDistance > 0 && currentValue >= maxDistance) {
      currentInput.setCustomValidity(`Distance must be less than maximum delivery distance (${maxDistance} km)`);
      currentInput.reportValidity();
      return;
    }
    
    // Check previous rule
    if (currentIndex > 0) {
      const prevRule = allRules[currentIndex - 1];
      const prevValue = parseFloat(prevRule.querySelector('.distance-value').value);
      
      if (!isNaN(prevValue) && currentValue <= prevValue) {
        currentInput.setCustomValidity(`Distance must be greater than ${prevValue} km`);
        currentInput.reportValidity();
        return;
      }
    }
    
    // Check next rule
    if (currentIndex < allRules.length - 1) {
      const nextRule = allRules[currentIndex + 1];
      const nextInput = nextRule.querySelector('.distance-value');
      const nextValue = parseFloat(nextInput.value);
      
      if (!isNaN(nextValue) && currentValue >= nextValue) {
        currentInput.setCustomValidity(`Distance must be less than ${nextValue} km`);
        currentInput.reportValidity();
        return;
      }
    }
    
    currentInput.setCustomValidity('');
  }
  
  // Update delivery preview (removed since preview section was deleted)
  function updateDeliveryPreview() {
    // Preview section removed - this function kept for compatibility
  }
  
  // Form submission
  if (form) {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      
      const submitBtn = form.querySelector('button[type="submit"]');
      const originalText = submitBtn.textContent;
      submitBtn.disabled = true;
      submitBtn.textContent = 'Saving...';
      
      const baseCharge = parseFloat(baseChargeInput?.value || 0);
      const maxDistance = parseFloat(maxDistanceInput?.value || 0);
      const maxItems = parseInt($('#maxItemsPerOrder')?.value || 10);
      const rules = Array.from(rulesList.querySelectorAll('.delivery-rule-item')).map(item => {
        return {
          distance: parseFloat(item.querySelector('.distance-value').value || 0),
          charge_per_km: parseFloat(item.querySelector('.charge-value').value || 0)
        };
      }).filter(r => r.distance > 0 && r.charge_per_km >= 0).sort((a, b) => a.distance - b.distance);
      
      const data = {
        action: 'save_settings',
        base_delivery_charge: baseCharge,
        max_delivery_distance: maxDistance,
        max_items_per_order: maxItems,
        delivery_rules: JSON.stringify(rules)
      };
      
      const result = await apiRequest('/PETVET/api/clinic-manager/delivery-settings.php', data);
      
      submitBtn.disabled = false;
      submitBtn.textContent = originalText;
      
      if (result.success) {
        toast('Delivery settings saved successfully!', 'success');
        captureFormState('#formShopSettings');
        updateButtonState('#formShopSettings', '#formShopSettings button[type="submit"]');
      } else {
        toast(result.message || 'Failed to save delivery settings', 'error');
      }
    });
    
    // Track form changes for submit button state
    form.addEventListener('input', () => {
      updateButtonState('#formShopSettings', '#formShopSettings button[type="submit"]');
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
initShopSettings();
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
