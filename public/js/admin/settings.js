// Admin Settings interactions (vanilla JS)
(function(){
  const $ = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));

  // Tabs
  $$('.settings-tabs .tab').forEach(btn => {
    btn.addEventListener('click', () => {
      $$('.settings-tabs .tab').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      const tab = btn.dataset.tab;
      $$('.tab-panel').forEach(p => p.classList.remove('active'));
      const active = $('#tab-' + tab);
      if (active) active.classList.add('active');
    });
  });

  // Unsaved changes tracker
  const saveStatus = $('#saveStatus');
  const markDirty = () => { if (saveStatus) saveStatus.textContent = 'Unsaved changes'; };
  $$('.tab-panel input, .tab-panel select, .tab-panel textarea').forEach(el => {
    el.addEventListener('change', markDirty);
    el.addEventListener('input', markDirty);
  });

  // Branding: logo preview
  const logoInput = $('#logoInput');
  const logoPreview = $('#logoPreview');
  if (logoInput && logoPreview) {
    logoInput.addEventListener('change', (e) => {
      const file = e.target.files && e.target.files[0];
      if (!file) return;
      if (file.size > 1024 * 1024) { // 1MB
        alert('Logo is too large. Max size is 1MB.');
        e.target.value = '';
        return;
      }
      const reader = new FileReader();
      reader.onload = () => { logoPreview.src = reader.result; markDirty(); };
      reader.readAsDataURL(file);
    });
  }

  // Save/Reset/Discard handlers (client-side demo)
  const saveBtn = $('#saveBtn');
  const saveAllBtn = $('#saveAllBtn');
  const resetBtn = $('#resetBtn');
  const discardBtn = $('#discardBtn');

  function collectSettings() {
    return {
      general: {
        siteName: $('#siteName')?.value || 'PETVET',
        currency: $('#defaultCurrency')?.value || 'LKR',
        timezone: $('#timezone')?.value || 'Asia/Colombo',
        maintenance: $('#maintenanceMode')?.checked || false,
      },
      branding: {
        primaryColor: $('#primaryColor')?.value || '#3b82f6',
        accentColor: $('#accentColor')?.value || '#10b981',
        // logo: handled via upload in backend (not implemented here)
      },
      notifications: {
        emailAlerts: $('#emailAlerts')?.checked || false,
        smsAlerts: $('#smsAlerts')?.checked || false,
        senderEmail: $('#senderEmail')?.value || '',
        dailySummaryTime: $('#dailySummaryTime')?.value || '09:00',
      },
      security: {
        twoFA: $('#twoFA')?.checked || false,
        sessionTimeout: Number($('#sessionTimeout')?.value || 30),
        allowedIPs: $('#allowedIPs')?.value || '',
      },
      integrations: {
        stripeEnabled: $('#stripeEnabled')?.checked || false,
        s3Enabled: $('#s3Enabled')?.checked || false,
      },
      billing: {
        billingEmail: $('#billingEmail')?.value || '',
        invoicePrefix: $('#invoicePrefix')?.value || 'PV-',
        taxRate: Number($('#taxRate')?.value || 8.0),
      }
    };
  }

  function saveSettings() {
    const data = collectSettings();
    // In production, send to backend via fetch('/api/settings', { method:'POST', body: JSON.stringify(data) })
    console.log('Saving settings (demo):', data);
    if (saveStatus) saveStatus.textContent = 'All changes saved';
    // Snackbar
    showToast('Settings saved');
  }

  function resetSettings() {
    document.location.reload();
  }

  function discardChanges() {
    document.location.reload();
  }

  saveBtn?.addEventListener('click', saveSettings);
  saveAllBtn?.addEventListener('click', saveSettings);
  resetBtn?.addEventListener('click', resetSettings);
  discardBtn?.addEventListener('click', discardChanges);

  function showToast(message) {
    const el = document.createElement('div');
    el.className = 'settings-toast';
    el.textContent = message;
    document.body.appendChild(el);
    setTimeout(() => el.classList.add('show'), 10);
    setTimeout(() => {
      el.classList.remove('show');
      setTimeout(() => el.remove(), 300);
    }, 2500);
  }

  // Toast styles (injected)
  const style = document.createElement('style');
  style.textContent = `
    .settings-toast { position: fixed; right: 20px; bottom: 20px; background: #111827; color: #fff; padding: 10px 14px; border-radius: 8px; opacity: 0; transform: translateY(10px); transition: .25s; z-index: 9999; }
    .settings-toast.show { opacity: 1; transform: translateY(0); }
  `;
  document.head.appendChild(style);
})();
