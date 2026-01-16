<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Payment Records | Receptionist</title>
  <link rel="stylesheet" href="/PETVET/public/css/receptionist/payment-records.css">
</head>
<body>
  <main class="main-content">
    <div class="page-container">
      <header class="page-header">
        <h1>Payment Records</h1>
        <p>History of completed payments and invoices</p>
      </header>

      <!-- Filters Section -->
      <section class="filters-section">
        <div class="filters-row">
          <div class="search-box">
            <input type="text" id="searchInput" placeholder="Search by client, pet, invoice number..." oninput="filterRecords()">
            <span class="search-icon">🔍</span>
          </div>
          
          <div class="filter-group">
            <label for="dateFilter">Date:</label>
            <select id="dateFilter" onchange="filterRecords()">
              <option value="all">All Time</option>
              <option value="today">Today</option>
              <option value="week">This Week</option>
              <option value="month">This Month</option>
            </select>
          </div>

          <div class="filter-group">
            <label for="vetFilter">Vet:</label>
            <select id="vetFilter" onchange="filterRecords()">
              <option value="all">All Vets</option>
              <?php
              $vets = array_unique(array_column($paymentRecords, 'vet'));
              foreach ($vets as $vet): ?>
                <option value="<?php echo htmlspecialchars($vet); ?>"><?php echo htmlspecialchars($vet); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <button class="btn-reset" onclick="resetFilters()">Reset Filters</button>
        </div>
      </section>

      <?php if (empty($paymentRecords)): ?>
        <div class="empty-state">
          <p>📋 No payment records found</p>
        </div>
      <?php else: ?>
        <section class="records-table-container">
          <div class="table-info">
            <p>Showing <strong id="recordCount"><?php echo count($paymentRecords); ?></strong> records</p>
          </div>
          
          <table class="records-table" id="recordsTable">
            <thead>
              <tr>
                <th>Invoice #</th>
                <th>Date</th>
                <th>Client</th>
                <th>Pet</th>
                <th>Vet</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($paymentRecords as $record): ?>
                <tr data-invoice="<?php echo htmlspecialchars($record['invoice_number']); ?>"
                    data-date="<?php echo htmlspecialchars($record['date']); ?>"
                    data-client="<?php echo htmlspecialchars(strtolower($record['client'])); ?>"
                    data-pet="<?php echo htmlspecialchars(strtolower($record['pet'])); ?>"
                    data-vet="<?php echo htmlspecialchars($record['vet']); ?>">
                  <td><strong><?php echo htmlspecialchars($record['invoice_number']); ?></strong></td>
                  <td><?php echo htmlspecialchars($record['date']); ?></td>
                  <td><?php echo htmlspecialchars($record['client']); ?></td>
                  <td><?php echo htmlspecialchars($record['pet']); ?></td>
                  <td><?php echo htmlspecialchars($record['vet']); ?></td>
                  <td><strong>LKR <?php echo number_format($record['amount'], 2); ?></strong></td>
                  <td><span class="badge badge-paid">Paid</span></td>
                  <td>
                    <button class="btn-view" onclick="viewInvoice('<?php echo htmlspecialchars($record['invoice_number']); ?>')">View</button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </section>
      <?php endif; ?>
    </div>
  </main>

  <!-- No results message -->
  <div id="noResults" class="empty-state" style="display: none;">
    <p>🔍 No records match your filters</p>
  </div>

  <!-- Invoice View Modal -->
  <div id="invoiceModal" class="modal-overlay" style="display: none;">
    <div class="modal-content modal-large">
      <div class="modal-header">
        <h2>Invoice Preview</h2>
        <button class="modal-close" onclick="closeInvoiceModal()">&times;</button>
      </div>
      
      <div class="modal-body">
        <div id="invoice-content" class="invoice-preview">
          <!-- Invoice will be generated here -->
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn-secondary" onclick="closeInvoiceModal()">Close</button>
        <button class="btn-outline" onclick="printInvoice()">🖨️ Print Invoice</button>
      </div>
    </div>
  </div>

  <script>
    // Store all records data
    const allRecords = <?php echo json_encode($paymentRecords); ?>;

    let invoiceData = null;

    function escapeHtml(text) {
      const div = document.createElement('div');
      div.textContent = text == null ? '' : String(text);
      return div.innerHTML;
    }

    function money(n) {
      const v = Number.isFinite(n) ? n : 0;
      return v.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    function safeJsonParse(str, fallback) {
      try {
        return JSON.parse(str);
      } catch {
        return fallback;
      }
    }

    function viewInvoice(invoiceNumber) {
      // Find the payment record
      const record = allRecords.find(r => r.invoice_number === invoiceNumber);
      if (!record) {
        alert('Payment record not found');
        return;
      }

      // Fetch payment details, then fetch clinic + meds/vacc data (same as payments page)
      fetch(`/PETVET/api/receptionist/get-payment-details.php?invoice=${encodeURIComponent(invoiceNumber)}`)
        .then(response => response.json())
        .then(async (data) => {
          if (!data.success) {
            alert('Error: ' + (data.error || 'Unknown error'));
            return;
          }

          const payment = data.payment;
          const appointmentId = parseInt(payment.appointment_id, 10) || 0;

          // Load clinic + medications/vaccinations for the receipt renderer
          try {
            const invRes = await fetch(`/PETVET/api/receptionist/get-invoice-data.php?appointment_id=${appointmentId}`);
            const invData = await invRes.json();
            if (invData.success) {
              window.currentClinicData = invData.clinic;
              window.currentMedicationsData = (invData.medications || []).map(m => ({
                name: m.medication,
                dosage: m.dosage,
                qty: 1
              }));
              window.currentVaccinationsData = (invData.vaccinations || []).map(v => ({
                name: v.vaccine,
                nextDue: v.next_due,
                qty: 1
              }));
            }
          } catch (e) {
            console.warn('Failed to load clinic/medical data for invoice:', e);
          }

          const items = safeJsonParse(payment.items_data || '[]', []);
          const cleanItems = (Array.isArray(items) ? items : []).map(i => ({
            description: String(i.description || '').trim(),
            qty: Math.max(1, parseInt(i.qty || 1, 10)),
            unitPrice: Math.max(0, parseFloat(i.unitPrice || 0)),
          })).filter(i => i.description.length > 0);

          invoiceData = {
            appointment: {
              id: appointmentId,
              client: payment.client_name || '',
              pet: payment.pet_name || '',
              animal: '',
              vet: payment.vet_name || '',
              type: ''
            },
            invoiceNo: payment.invoice_number || invoiceNumber,
            paymentMethod: payment.payment_method || 'CASH',
            invoiceDate: payment.payment_date || '',
            clientPhone: (payment.client_phone || payment.client_phone_number || '').trim(),
            note: (payment.invoice_note || '').trim(),
            items: cleanItems,
            gross: parseFloat(payment.gross_amount || 0) || 0,
            discount: parseFloat(payment.discount_amount || 0) || 0,
            cardFee: parseFloat(payment.card_fee || 0) || 0,
            total: parseFloat(payment.total_amount || 0) || 0,
          };

          document.getElementById('invoice-content').innerHTML = buildReceiptInvoiceHTML(invoiceData);
          document.getElementById('invoiceModal').style.display = 'flex';
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Failed to load invoice details');
        });
    }

    function buildReceiptInvoiceHTML(data) {
      // Use real clinic data from API (same behavior as payments page)
      const clinic = window.currentClinicData || {};
      const header = {
        name: clinic.name || 'PetVet Clinic',
        addr1: clinic.address || '',
        tel: clinic.phone || '',
        mob: '',
        email: clinic.email || '',
        logo: clinic.logo || '/PETVET/views/shared/images/sidebar/petvet-logo-web.png'
      };

      let linesHtml = '';

      const medItem = (data.items || []).find(it => String(it.description || '').toLowerCase() === 'medications');
      const vacItem = (data.items || []).find(it => String(it.description || '').toLowerCase() === 'vaccinations');
      const medTotal = medItem ? (medItem.qty * medItem.unitPrice) : 0;
      const vacTotal = vacItem ? (vacItem.qty * vacItem.unitPrice) : 0;

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

      const billableItems = (data.items || []).filter(it => {
        const desc = String(it.description || '').toLowerCase();
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

    function closeInvoiceModal() {
      document.getElementById('invoiceModal').style.display = 'none';
    }

    function printInvoice() {
      if (!invoiceData) return;

      const invoiceContent = document.getElementById('invoice-content');
      if (invoiceContent && !String(invoiceContent.innerHTML || '').trim()) {
        invoiceContent.innerHTML = buildReceiptInvoiceHTML(invoiceData);
      }

      window.print();
    }

    function filterRecords() {
      const searchTerm = document.getElementById('searchInput').value.toLowerCase();
      const dateFilter = document.getElementById('dateFilter').value;
      const vetFilter = document.getElementById('vetFilter').value;
      
      const rows = document.querySelectorAll('#recordsTable tbody tr');
      let visibleCount = 0;
      
      rows.forEach(row => {
        const invoice = row.dataset.invoice.toLowerCase();
        const client = row.dataset.client;
        const pet = row.dataset.pet;
        const vet = row.dataset.vet;
        const date = row.dataset.date;
        
        // Search filter
        const matchesSearch = searchTerm === '' || 
          invoice.includes(searchTerm) || 
          client.includes(searchTerm) || 
          pet.includes(searchTerm);
        
        // Vet filter
        const matchesVet = vetFilter === 'all' || vet === vetFilter;
        
        // Date filter
        let matchesDate = true;
        if (dateFilter !== 'all') {
          const recordDate = new Date(date);
          const today = new Date();
          today.setHours(0, 0, 0, 0);
          
          if (dateFilter === 'today') {
            matchesDate = recordDate.toDateString() === today.toDateString();
          } else if (dateFilter === 'week') {
            const weekAgo = new Date(today);
            weekAgo.setDate(weekAgo.getDate() - 7);
            matchesDate = recordDate >= weekAgo;
          } else if (dateFilter === 'month') {
            const monthAgo = new Date(today);
            monthAgo.setMonth(monthAgo.getMonth() - 1);
            matchesDate = recordDate >= monthAgo;
          }
        }
        
        // Show/hide row
        if (matchesSearch && matchesVet && matchesDate) {
          row.style.display = '';
          visibleCount++;
        } else {
          row.style.display = 'none';
        }
      });
      
      // Update count
      document.getElementById('recordCount').textContent = visibleCount;
      
      // Show/hide no results message
      const tableContainer = document.querySelector('.records-table-container');
      const noResults = document.getElementById('noResults');
      
      if (visibleCount === 0) {
        tableContainer.style.display = 'none';
        noResults.style.display = 'block';
      } else {
        tableContainer.style.display = 'block';
        noResults.style.display = 'none';
      }
    }

    function resetFilters() {
      document.getElementById('searchInput').value = '';
      document.getElementById('dateFilter').value = 'all';
      document.getElementById('vetFilter').value = 'all';
      filterRecords();
    }
  </script>
</body>
</html>
