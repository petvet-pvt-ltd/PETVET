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

  <script>
    // Store all records data
    const allRecords = <?php echo json_encode($paymentRecords); ?>;

    function viewInvoice(invoiceNumber) {
      alert('View invoice: ' + invoiceNumber + '\n\nIn production, this would open the saved invoice PDF.');
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
