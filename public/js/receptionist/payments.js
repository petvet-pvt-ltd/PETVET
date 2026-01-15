// Payments Page JavaScript (UI-only invoice builder, no frameworks)
let currentAppointment = null;
let invoiceData = null;

let editorItems = [];
let editorDetected = { medications: [], vaccinations: [] };

function pad2(n) {
  return String(n).padStart(2, '0');
}

function todayISO() {
  const d = new Date();
  return `${d.getFullYear()}-${pad2(d.getMonth() + 1)}-${pad2(d.getDate())}`;
}

function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text == null ? '' : String(text);
  return div.innerHTML;
}

function money(n) {
  const v = Number.isFinite(n) ? n : 0;
  return v.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

function parseMoneyInput(el) {
  const v = parseFloat(el?.value);
  return Number.isFinite(v) ? v : 0;
}

function openPaymentForm(appointment) {
  currentAppointment = appointment;

  document.getElementById('summary-client').textContent = appointment.client;
  document.getElementById('summary-pet').textContent = `${appointment.pet} (${appointment.animal})`;
  document.getElementById('summary-vet').textContent = appointment.vet;
  document.getElementById('summary-type').textContent = appointment.type;
  document.getElementById('appointment-id').value = appointment.id;

  // Reset invoice editor inputs
  document.getElementById('paymentForm').reset();
  document.getElementById('invoice-date').value = todayISO();
  // UI-only invoice no (auto). Later this will come from DB when saving.
  document.getElementById('invoice-no').value = String(Date.now()).slice(-4);
  document.getElementById('payment-method').value = 'CASH';
  document.getElementById('client-phone').value = '';
  document.getElementById('invoice-note').value = '';
  document.getElementById('invoice-discount').value = '0';
  document.getElementById('invoice-cardfee').value = '0';

  // Fetch real data from database
  fetch(`/PETVET/api/receptionist/get-invoice-data.php?appointment_id=${appointment.id}`)
    .then(res => res.json())
    .then(data => {
      if (!data.success) {
        alert('Error loading invoice data: ' + (data.error || 'Unknown error'));
        return;
      }

      // Store clinic data globally for invoice generation
      window.currentClinicData = data.clinic;

      // Process medications and vaccinations
      editorDetected = {
        medications: data.medications.map(m => ({
          name: m.medication,
          dosage: m.dosage,
          qty: 1
        })),
        vaccinations: data.vaccinations.map(v => ({
          name: v.vaccine,
          nextDue: v.next_due,
          qty: 1
        }))
      };
      
      // Store globally for invoice generation
      window.currentMedicationsData = editorDetected.medications;
      window.currentVaccinationsData = editorDetected.vaccinations;

      // Build default rows: summary rows only (not item-by-item)
      editorItems = [];
      if (editorDetected.medications.length > 0) {
        editorItems.push({ id: cryptoId(), description: 'Medications', qty: editorDetected.medications.length, unitPrice: 0, locked: true });
      }
      if (editorDetected.vaccinations.length > 0) {
        editorItems.push({ id: cryptoId(), description: 'Vaccinations', qty: editorDetected.vaccinations.length, unitPrice: 0, locked: true });
      }

      // Start with an empty editable row so receptionist can add charges quickly
      editorItems.push({ id: cryptoId(), description: 'Consultation', qty: 1, unitPrice: 0, locked: false });

      renderDetected();
      renderItemsTable();
      recalcTotals();

      document.getElementById('paymentModal').style.display = 'flex';
    })
    .catch(err => {
      console.error('Error fetching invoice data:', err);
      alert('Failed to load invoice data. Please try again.');
    });
}

function closePaymentModal() {
  document.getElementById('paymentModal').style.display = 'none';
  currentAppointment = null;
}

function hidePaymentModal() {
  // Hide without clearing editor state (used for invoice preview → back)
  document.getElementById('paymentModal').style.display = 'none';
}

function backToPaymentDetails() {
  // Keep currentAppointment + editor state and return to editing
  document.getElementById('invoiceModal').style.display = 'none';
  document.getElementById('paymentModal').style.display = 'flex';
}

function cryptoId() {
  // No library; avoid Math.random collisions in tables
  return `i_${Date.now()}_${Math.random().toString(16).slice(2)}`;
}

function renderDetected() {
  const el = document.getElementById('detected-items');
  if (!el) return;

  const medsCount = editorDetected.medications.length;
  const vaccCount = editorDetected.vaccinations.length;

  if (medsCount === 0 && vaccCount === 0) {
    el.innerHTML = '';
    return;
  }

  let medsHtml = '';
  if (medsCount > 0) {
    const medsList = editorDetected.medications.map(m => 
      `${escapeHtml(m.name)}${m.dosage ? ` (${escapeHtml(m.dosage)})` : ''}`
    ).join(', ');
    medsHtml = `<div class="detected-line"><strong>Medications:</strong> ${medsList}</div>`;
  }

  let vaccHtml = '';
  if (vaccCount > 0) {
    const vaccList = editorDetected.vaccinations.map(v => 
      `${escapeHtml(v.name)}${v.nextDue ? ` [Next: ${escapeHtml(v.nextDue)}]` : ''}`
    ).join(', ');
    vaccHtml = `<div class="detected-line"><strong>Vaccinations:</strong> ${vaccList}</div>`;
  }

  el.innerHTML = `
    <div class="detected-pill">
      <div class="detected-header"><strong>From vet records:</strong></div>
      ${medsHtml}
      ${vaccHtml}
    </div>
  `;
}

function renderItemsTable() {
  const tbody = document.getElementById('itemsTbody');
  if (!tbody) return;

  tbody.innerHTML = editorItems.map((item) => {
    const lineTotal = (Number(item.qty) || 0) * (Number(item.unitPrice) || 0);
    const disabled = item.locked ? 'disabled' : '';
    const lockBadge = item.locked ? '<span class="lock-badge">AUTO</span>' : '';

    return `
      <tr data-id="${escapeHtml(item.id)}">
        <td>
          <div class="desc-cell">
            <input class="cell-input" data-field="description" type="text" value="${escapeHtml(item.description)}" ${disabled}>
            ${lockBadge}
          </div>
        </td>
        <td class="num">
          <input class="cell-input num-input" data-field="qty" type="number" min="1" step="1" value="${escapeHtml(item.qty)}">
        </td>
        <td class="num">
          <input class="cell-input num-input" data-field="unitPrice" type="number" min="0" step="0.01" value="${escapeHtml(item.unitPrice)}">
        </td>
        <td class="num">
          <span class="line-total">${money(lineTotal)}</span>
        </td>
        <td class="num">
          <button type="button" class="row-del" onclick="removeInvoiceRow('${escapeHtml(item.id)}')" aria-label="Remove row">×</button>
        </td>
      </tr>
    `;
  }).join('');
}

function addInvoiceRow(scrollIntoView = false) {
  editorItems.push({ id: cryptoId(), description: '', qty: 1, unitPrice: 0, locked: false });
  renderItemsTable();
  recalcTotals();

  if (scrollIntoView) {
    // Focus the newly added row description
    const tbody = document.getElementById('itemsTbody');
    const lastRow = tbody?.querySelector('tr:last-child');
    lastRow?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    const input = lastRow?.querySelector('input[data-field="description"]');
    if (input) input.focus();
  }
}

function removeInvoiceRow(id) {
  const idx = editorItems.findIndex(i => i.id === id);
  if (idx === -1) return;
  editorItems.splice(idx, 1);
  if (editorItems.length === 0) {
    editorItems.push({ id: cryptoId(), description: '', qty: 1, unitPrice: 0, locked: false });
  }
  renderItemsTable();
  recalcTotals();
}

function attachEditorListeners() {
  const tbody = document.getElementById('itemsTbody');
  if (!tbody) return;

  tbody.addEventListener('input', (e) => {
    const target = e.target;
    if (!(target instanceof HTMLInputElement)) return;

    const field = target.getAttribute('data-field');
    if (!field) return;

    const tr = target.closest('tr');
    const id = tr?.getAttribute('data-id');
    if (!id) return;

    const item = editorItems.find(i => i.id === id);
    if (!item) return;

    if (field === 'description') item.description = target.value;
    if (field === 'qty') item.qty = Math.max(1, parseInt(target.value || '1', 10));
    if (field === 'unitPrice') item.unitPrice = Math.max(0, parseFloat(target.value || '0'));

    // Update UI line total only for this row
    const lineTotalEl = tr.querySelector('.line-total');
    if (lineTotalEl) {
      lineTotalEl.textContent = money((Number(item.qty) || 0) * (Number(item.unitPrice) || 0));
    }

    recalcTotals();
  });

  ['invoice-discount', 'invoice-cardfee', 'payment-method', 'invoice-date', 'invoice-no', 'client-phone', 'invoice-note'].forEach((id) => {
    const el = document.getElementById(id);
    el?.addEventListener('input', recalcTotals);
    el?.addEventListener('change', recalcTotals);
  });
}

function recalcTotals() {
  const gross = editorItems.reduce((sum, it) => sum + (Number(it.qty) || 0) * (Number(it.unitPrice) || 0), 0);
  const discount = parseMoneyInput(document.getElementById('invoice-discount'));
  const cardFee = parseMoneyInput(document.getElementById('invoice-cardfee'));
  const total = Math.max(0, gross - discount + cardFee);

  const grossEl = document.getElementById('gross');
  const totalEl = document.getElementById('total');
  if (grossEl) grossEl.textContent = `LKR ${money(gross)}`;
  if (totalEl) totalEl.textContent = `LKR ${money(total)}`;
}

document.addEventListener('DOMContentLoaded', function () {
  attachEditorListeners();
});

function generateInvoice() {
  const invoiceDate = document.getElementById('invoice-date')?.value || todayISO();
  const invoiceNo = (document.getElementById('invoice-no')?.value || '').trim() || String(Date.now()).slice(-4);
  const paymentMethod = document.getElementById('payment-method')?.value || 'CASH';
  const clientPhone = (document.getElementById('client-phone')?.value || '').trim();
  const note = (document.getElementById('invoice-note')?.value || '').trim();
  const discount = parseMoneyInput(document.getElementById('invoice-discount'));
  const cardFee = parseMoneyInput(document.getElementById('invoice-cardfee'));

  const cleanItems = editorItems
    .map(i => ({
      description: String(i.description || '').trim(),
      qty: Math.max(1, parseInt(i.qty || 1, 10)),
      unitPrice: Math.max(0, parseFloat(i.unitPrice || 0)),
    }))
    .filter(i => i.description.length > 0);

  if (!currentAppointment) return;
  if (cleanItems.length === 0) {
    alert('Please add at least 1 invoice row.');
    return;
  }

  const gross = cleanItems.reduce((sum, it) => sum + it.qty * it.unitPrice, 0);
  const total = Math.max(0, gross - discount + cardFee);

  invoiceData = {
    appointment: currentAppointment,
    invoiceNo,
    paymentMethod,
    invoiceDate,
    clientPhone,
    note,
    items: cleanItems,
    gross,
    discount,
    cardFee,
    total,
  };

  document.getElementById('invoice-content').innerHTML = buildReceiptInvoiceHTML(invoiceData);

  hidePaymentModal();
  document.getElementById('invoiceModal').style.display = 'flex';
}

function buildReceiptInvoiceHTML(data) {
  // Use real clinic data from API
  const clinic = window.currentClinicData || {};
  const header = {
    name: clinic.name || 'PetVet Clinic',
    addr1: clinic.address || '',
    tel: clinic.phone || '',
    mob: '',  // Use email if no separate mobile
    email: clinic.email || '',
    logo: clinic.logo || '/PETVET/views/shared/images/sidebar/petvet-logo-web.png'
  };

  // Build invoice lines with categories for medications and vaccinations
  let linesHtml = '';
  
  // Calculate prices for medications and vaccinations from invoice items
  const medItem = data.items.find(it => it.description.toLowerCase() === 'medications');
  const vacItem = data.items.find(it => it.description.toLowerCase() === 'vaccinations');
  const medTotal = medItem ? (medItem.qty * medItem.unitPrice) : 0;
  const vacTotal = vacItem ? (vacItem.qty * vacItem.unitPrice) : 0;
  
  // Add medications section if any
  const medications = window.currentMedicationsData || [];
  if (medications.length > 0) {
    linesHtml += `
      <tr class="category-row">
        <td colspan="3" class="category-header">MEDICATIONS</td>
        <td class="category-total">${medTotal > 0 ? money(medTotal) : '-'}</td>
      </tr>
    `;
    medications.forEach(med => {
      linesHtml += `
        <tr>
          <td class="c-desc" colspan="4">${escapeHtml(med.name)}${med.dosage ? ` <span class="item-detail">(${escapeHtml(med.dosage)})</span>` : ''}</td>
        </tr>
      `;
    });
  }
  
  // Add vaccinations section if any
  const vaccinations = window.currentVaccinationsData || [];
  if (vaccinations.length > 0) {
    linesHtml += `
      <tr class="category-row">
        <td colspan="3" class="category-header">VACCINATIONS</td>
        <td class="category-total">${vacTotal > 0 ? money(vacTotal) : '-'}</td>
      </tr>
    `;
    vaccinations.forEach(vac => {
      linesHtml += `
        <tr>
          <td class="c-desc" colspan="4">${escapeHtml(vac.name)}${vac.nextDue ? ` <span class="item-detail">[Next: ${escapeHtml(vac.nextDue)}]</span>` : ''}</td>
        </tr>
      `;
    });
  }
  
  // Add other billable items
  const billableItems = data.items.filter(it => {
    const desc = it.description.toLowerCase();
    return desc !== 'medications' && desc !== 'vaccinations';
  });
  
  if (billableItems.length > 0) {
    linesHtml += `
      <tr class="category-row">
        <td colspan="4" class="category-header">CHARGES</td>
      </tr>
    `;
    billableItems.forEach(it => {
      const lineTotal = it.qty * it.unitPrice;
      linesHtml += `
        <tr>
          <td class="c-desc">${escapeHtml(it.description)}</td>
          <td class="c-num">${escapeHtml(it.qty)}</td>
          <td class="c-num">${money(it.unitPrice)}</td>
          <td class="c-num">${money(lineTotal)}</td>
        </tr>
      `;
    });
  }

  const invoiceMeta = `${escapeHtml(data.invoiceNo)} | ${escapeHtml(data.paymentMethod)}`;
  const clientMeta = `${escapeHtml(data.appointment.client)}${data.clientPhone ? ` | ${escapeHtml(data.clientPhone)}` : ''}`;
  const noteText = (data.note || '').trim();

  return `
    <section class="receipt">
      <div class="r-header r-header--split">
        <div class="r-logo-box">
          <img class="r-logo" src="${escapeHtml(header.logo)}" alt="Clinic Logo" />
        </div>
        <div class="r-header-text">
          <div class="r-clinic-name">${escapeHtml(header.name)}</div>
          ${header.addr1 ? `<div class="r-clinic-info">${escapeHtml(header.addr1)}</div>` : ''}
          ${header.tel ? `<div class="r-clinic-info">Tel: ${escapeHtml(header.tel)}</div>` : ''}
          ${header.email ? `<div class="r-clinic-info">${escapeHtml(header.email)}</div>` : ''}
        </div>
      </div>

      <div class="r-divider"></div>

      <div class="r-info-section">
        <div class="r-info-grid">
          <div class="r-info-cell"><span class="r-label">Date:</span><span class="r-value">${escapeHtml(data.invoiceDate)}</span></div>
          <div class="r-info-cell"><span class="r-label">Invoice:</span><span class="r-value">${invoiceMeta}</span></div>
          <div class="r-info-cell"><span class="r-label">Client:</span><span class="r-value">${clientMeta}</span></div>
        </div>
        ${noteText ? `<div class="r-info-row r-note-row"><span class="r-label">Note:</span><span class="r-value">${escapeHtml(noteText)}</span></div>` : ''}
      </div>

      <div class="r-divider"></div>

      <table class="r-table">
        <thead>
          <tr>
            <th class="r-th-left">Description</th>
            <th class="r-th-right">Qty</th>
            <th class="r-th-right">Price</th>
            <th class="r-th-right">Total</th>
          </tr>
        </thead>
        <tbody>
          ${linesHtml}
        </tbody>
      </table>

      <div class="r-divider"></div>

      <div class="r-totals">
        <div class="r-total-row"><span class="r-total-label">Gross Amount:</span><span class="r-total-value">${money(data.gross)}</span></div>
        <div class="r-total-row"><span class="r-total-label">Item Discount:</span><span class="r-total-value">${money(data.discount)}</span></div>
        <div class="r-total-row"><span class="r-total-label">Card Fee:</span><span class="r-total-value">${money(data.cardFee)}</span></div>
        <div class="r-total-final">
          <span class="r-total-label">Total Amount:</span>
          <span class="r-total-value">${money(data.total)}</span>
        </div>
      </div>

      <div class="r-divider"></div>

      <div class="r-footer">
        <div class="r-footer-line">Exchange within 07 days</div>
        <div class="r-footer-line">Product and pack should be in good condition</div>
        <div class="r-footer-line">Receipt is essential</div>
        <div class="r-thank-you">Thank you, come again</div>
      </div>
    </section>
  `;
}

// Close invoice modal
function closeInvoiceModal() {
  document.getElementById('invoiceModal').style.display = 'none';
}

// Print invoice
function printInvoice() {
  if (!invoiceData) return;

  // Ensure the invoice preview has content (this is what our CSS prints)
  const invoiceContent = document.getElementById('invoice-content');
  if (invoiceContent && !String(invoiceContent.innerHTML || '').trim()) {
    invoiceContent.innerHTML = buildReceiptInvoiceHTML(invoiceData);
  }

  // Use the browser print dialog (includes "Save as PDF")
  window.print();
}

// Show confirm payment dialog
function showConfirmPaymentDialog() {
  document.getElementById('confirm-client').textContent = invoiceData.appointment.client;
  document.getElementById('confirm-invoice').textContent = invoiceData.invoiceNo;
  document.getElementById('confirm-amount').textContent = `LKR ${money(invoiceData.total)}`;
  
  document.getElementById('confirmPaymentModal').style.display = 'flex';
}

// Close confirm payment dialog
function closeConfirmPaymentModal() {
  document.getElementById('confirmPaymentModal').style.display = 'none';
}

// Confirm payment (final)
function confirmPaymentFinal() {
  // In production, send data to server
  console.log('Payment confirmed:', invoiceData);
  
  /*
  fetch('/PETVET/receptionist/confirm-payment', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(invoiceData)
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      showSuccessMessage();
    }
  });
  */
  
  // Close modals
  closeConfirmPaymentModal();
  closeInvoiceModal();
  
  // Show success message
  showSuccessMessage();
}

// Show success modal
function showSuccessMessage() {
  document.getElementById('success-invoice').textContent = invoiceData.invoiceNo;
  document.getElementById('successModal').style.display = 'flex';
}

// Close success modal
function closeSuccessModal() {
  document.getElementById('successModal').style.display = 'none';
  // In production, reload to update the list
  // window.location.reload();
}

// Close modal when clicking outside
window.onclick = function(event) {
  const paymentModal = document.getElementById('paymentModal');
  const invoiceModal = document.getElementById('invoiceModal');
  const confirmModal = document.getElementById('confirmPaymentModal');
  const successModal = document.getElementById('successModal');
  
  if (event.target === paymentModal) {
    closePaymentModal();
  }
  if (event.target === invoiceModal) {
    closeInvoiceModal();
  }
  if (event.target === confirmModal) {
    closeConfirmPaymentModal();
  }
  if (event.target === successModal) {
    closeSuccessModal();
  }
}
