// Finance Panel JavaScript

// Search functionality
document.getElementById('fpSearchInput').addEventListener('input', function(e) {
  const searchTerm = e.target.value.toLowerCase();
  filterTable();
});

// Filter functionality
document.getElementById('typeFilter').addEventListener('change', filterTable);
document.getElementById('statusFilter').addEventListener('change', filterTable);

function filterTable() {
  const searchTerm = document.getElementById('fpSearchInput').value.toLowerCase();
  const typeFilter = document.getElementById('typeFilter').value;
  const statusFilter = document.getElementById('statusFilter').value;
  
  const rows = document.querySelectorAll('#fpTableBody tr');
  
  rows.forEach(row => {
    const rowText = row.textContent.toLowerCase();
    const rowType = row.getAttribute('data-type');
    const rowStatus = row.getAttribute('data-status');
    
    const matchesSearch = rowText.includes(searchTerm);
    const matchesType = typeFilter === 'all' || rowType === typeFilter;
    const matchesStatus = statusFilter === 'all' || rowStatus === statusFilter;
    
    if (matchesSearch && matchesType && matchesStatus) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
  
  updateStats();
}

function updateStats() {
  const rows = document.querySelectorAll('#fpTableBody tr');
  let totalRevenue = 0;
  let totalExpenses = 0;
  let commissionIncome = 0;
  let directSales = 0;
  let pendingPayments = 0;
  
  rows.forEach(row => {
    if (row.style.display !== 'none') {
      const type = row.getAttribute('data-type');
      const status = row.getAttribute('data-status');
      const amountCell = row.querySelector('.amount');
      const amount = parseFloat(amountCell.textContent.replace(/,/g, '').replace('-', ''));
      
      if (type === 'Expense') {
        totalExpenses += amount;
      } else {
        totalRevenue += amount;
        
        if (type === 'Commission') {
          commissionIncome += amount;
        } else if (type === 'Direct Sale') {
          directSales += amount;
        }
      }
      
      if (status === 'Pending') {
        pendingPayments += amount;
      }
    }
  });
  
  const netProfit = totalRevenue - totalExpenses;
  
  // Update stat cards (optional - uncomment to enable dynamic updates)
  // document.getElementById('totalRevenue').textContent = 'LKR ' + totalRevenue.toLocaleString();
  // document.getElementById('commissionIncome').textContent = 'LKR ' + commissionIncome.toLocaleString();
  // document.getElementById('directSales').textContent = 'LKR ' + directSales.toLocaleString();
  // document.getElementById('totalExpenses').textContent = 'LKR ' + totalExpenses.toLocaleString();
  // document.getElementById('netProfit').textContent = 'LKR ' + netProfit.toLocaleString();
  // document.getElementById('pendingPayments').textContent = 'LKR ' + pendingPayments.toLocaleString();
}

// Revenue vs Expenses Chart
function drawRevenueExpensesChart() {
  const container = document.getElementById('revenueExpensesChart');
  if (!container) return;
  
  const width = container.clientWidth;
  const height = 250;
  const padding = 40;
  
  // Sample data for the month (30 days)
  const revenueData = generateSampleData(30, 10000, 25000);
  const expensesData = generateSampleData(30, 3000, 12000);
  
  const maxValue = Math.max(...revenueData, ...expensesData);
  const minValue = 0;
  const range = maxValue - minValue;
  
  // Create SVG
  const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
  svg.setAttribute('width', width);
  svg.setAttribute('height', height);
  svg.style.overflow = 'visible';
  
  // Grid lines
  for (let i = 0; i <= 5; i++) {
    const y = padding + (height - 2 * padding) * i / 5;
    const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
    line.setAttribute('x1', padding);
    line.setAttribute('y1', y);
    line.setAttribute('x2', width - padding);
    line.setAttribute('y2', y);
    line.setAttribute('stroke', '#f1f5f9');
    line.setAttribute('stroke-width', '1');
    svg.appendChild(line);
  }
  
  // Draw lines
  drawLine(svg, revenueData, '#3b82f6', width, height, padding, minValue, range);
  drawLine(svg, expensesData, '#ef4444', width, height, padding, minValue, range);
  
  container.appendChild(svg);
}

function drawLine(svg, data, color, width, height, padding, minValue, range) {
  const chartWidth = width - 2 * padding;
  const chartHeight = height - 2 * padding;
  const stepX = chartWidth / (data.length - 1);
  
  let pathD = '';
  
  data.forEach((value, index) => {
    const x = padding + index * stepX;
    const y = height - padding - ((value - minValue) / range) * chartHeight;
    
    if (index === 0) {
      pathD += `M ${x} ${y}`;
    } else {
      pathD += ` L ${x} ${y}`;
    }
  });
  
  const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
  path.setAttribute('d', pathD);
  path.setAttribute('stroke', color);
  path.setAttribute('stroke-width', '3');
  path.setAttribute('fill', 'none');
  path.setAttribute('stroke-linecap', 'round');
  path.setAttribute('stroke-linejoin', 'round');
  svg.appendChild(path);
}

function generateSampleData(points, min, max) {
  const data = [];
  let current = (min + max) / 2;
  
  for (let i = 0; i < points; i++) {
    const change = (Math.random() - 0.5) * (max - min) * 0.3;
    current = Math.max(min, Math.min(max, current + change));
    data.push(Math.round(current));
  }
  
  return data;
}

// Month filter
document.getElementById('monthFilter').addEventListener('change', function() {
  console.log('Month filter changed to:', this.value);
  // Implement month filtering logic here
});

// Export button
document.querySelector('.fp-export-btn').addEventListener('click', function() {
  exportToCSV();
});

// Generate Statement button
document.querySelector('.fp-statement-btn').addEventListener('click', function() {
  generateStatement();
});

// Export to CSV function
function exportToCSV() {
  const rows = document.querySelectorAll('#fpTableBody tr');
  let csvContent = 'Date,Type,Partner/Source,Amount (LKR),Status,Notes\n';
  
  rows.forEach(row => {
    if (row.style.display !== 'none') {
      const cells = row.querySelectorAll('td');
      const rowData = [];
      
      cells.forEach((cell, index) => {
        let text = cell.textContent.trim();
        // Escape commas and quotes in CSV
        if (text.includes(',') || text.includes('"')) {
          text = '"' + text.replace(/"/g, '""') + '"';
        }
        rowData.push(text);
      });
      
      csvContent += rowData.join(',') + '\n';
    }
  });
  
  // Create blob and download
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  const url = URL.createObjectURL(blob);
  
  const today = new Date();
  const dateStr = today.getFullYear() + '-' + 
                  String(today.getMonth() + 1).padStart(2, '0') + '-' + 
                  String(today.getDate()).padStart(2, '0');
  
  link.setAttribute('href', url);
  link.setAttribute('download', 'financial_records_' + dateStr + '.csv');
  link.style.visibility = 'hidden';
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  
  // Show success message
  showNotification('Export successful! CSV file downloaded.', 'success');
}

// Generate Statement function
function generateStatement() {
  const rows = document.querySelectorAll('#fpTableBody tr');
  let totalRevenue = 0;
  let totalExpenses = 0;
  let commissionIncome = 0;
  let directSales = 0;
  let pendingPayments = 0;
  let paidCount = 0;
  let pendingCount = 0;
  
  const transactions = [];
  
  rows.forEach(row => {
    if (row.style.display !== 'none') {
      const cells = row.querySelectorAll('td');
      const type = row.getAttribute('data-type');
      const status = row.getAttribute('data-status');
      const amountCell = row.querySelector('.amount');
      const amount = parseFloat(amountCell.textContent.replace(/,/g, '').replace('-', ''));
      
      transactions.push({
        date: cells[0].textContent.trim(),
        type: cells[1].textContent.trim(),
        partner: cells[2].textContent.trim(),
        amount: cells[3].textContent.trim(),
        status: cells[4].textContent.trim(),
        notes: cells[5].textContent.trim()
      });
      
      if (type === 'Expense') {
        totalExpenses += amount;
      } else {
        totalRevenue += amount;
        
        if (type === 'Commission') {
          commissionIncome += amount;
        } else if (type === 'Direct Sale') {
          directSales += amount;
        }
      }
      
      if (status === 'Pending') {
        pendingPayments += amount;
        pendingCount++;
      } else {
        paidCount++;
      }
    }
  });
  
  const netProfit = totalRevenue - totalExpenses;
  
  // Generate HTML statement
  const statementHTML = generateStatementHTML({
    totalRevenue,
    totalExpenses,
    netProfit,
    commissionIncome,
    directSales,
    pendingPayments,
    paidCount,
    pendingCount,
    transactions
  });
  
  // Open in new window and print
  const printWindow = window.open('', '_blank');
  printWindow.document.write(statementHTML);
  printWindow.document.close();
  
  // Auto print after a short delay
  setTimeout(() => {
    printWindow.print();
  }, 500);
  
  showNotification('Statement generated! Print dialog opened.', 'success');
}

// Generate Statement HTML
function generateStatementHTML(data) {
  const today = new Date();
  const dateStr = today.toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  });
  
  const monthFilter = document.getElementById('monthFilter').value;
  let periodText = 'This Month';
  if (monthFilter === 'last') periodText = 'Last Month';
  else if (monthFilter === '3months') periodText = 'Last 3 Months';
  else if (monthFilter === '6months') periodText = 'Last 6 Months';
  else if (monthFilter === 'year') periodText = 'This Year';
  
  return `
    <!DOCTYPE html>
    <html>
    <head>
      <meta charset="UTF-8">
      <title>Financial Statement - ${dateStr}</title>
      <style>
        * {
          margin: 0;
          padding: 0;
          box-sizing: border-box;
        }
        body {
          font-family: Arial, sans-serif;
          padding: 40px;
          color: #1e293b;
          line-height: 1.6;
        }
        .header {
          text-align: center;
          margin-bottom: 40px;
          border-bottom: 3px solid #3b82f6;
          padding-bottom: 20px;
        }
        .header h1 {
          font-size: 32px;
          color: #1e293b;
          margin-bottom: 5px;
        }
        .header p {
          font-size: 14px;
          color: #64748b;
        }
        .period {
          background: #f8fafc;
          padding: 15px;
          border-radius: 8px;
          margin-bottom: 30px;
          text-align: center;
          font-weight: 600;
          color: #475569;
        }
        .summary {
          display: grid;
          grid-template-columns: repeat(3, 1fr);
          gap: 20px;
          margin-bottom: 40px;
        }
        .summary-card {
          background: #f8fafc;
          padding: 20px;
          border-radius: 8px;
          border-left: 4px solid #3b82f6;
        }
        .summary-card.revenue {
          border-left-color: #10b981;
        }
        .summary-card.expense {
          border-left-color: #ef4444;
        }
        .summary-card.profit {
          border-left-color: #3b82f6;
        }
        .summary-card h3 {
          font-size: 13px;
          color: #64748b;
          margin-bottom: 10px;
          text-transform: uppercase;
          letter-spacing: 0.5px;
        }
        .summary-card .value {
          font-size: 28px;
          font-weight: 700;
          color: #1e293b;
        }
        .breakdown {
          margin-bottom: 30px;
        }
        .breakdown h2 {
          font-size: 18px;
          margin-bottom: 15px;
          color: #1e293b;
        }
        .breakdown-grid {
          display: grid;
          grid-template-columns: repeat(2, 1fr);
          gap: 15px;
          background: #f8fafc;
          padding: 20px;
          border-radius: 8px;
        }
        .breakdown-item {
          display: flex;
          justify-content: space-between;
          padding: 10px;
          background: white;
          border-radius: 6px;
        }
        .breakdown-item label {
          font-size: 14px;
          color: #64748b;
        }
        .breakdown-item value {
          font-size: 14px;
          font-weight: 600;
          color: #1e293b;
        }
        .transactions {
          margin-top: 40px;
        }
        .transactions h2 {
          font-size: 18px;
          margin-bottom: 15px;
          color: #1e293b;
        }
        table {
          width: 100%;
          border-collapse: collapse;
          margin-bottom: 30px;
        }
        thead {
          background: #f8fafc;
        }
        th {
          padding: 12px;
          text-align: left;
          font-size: 11px;
          font-weight: 700;
          color: #64748b;
          text-transform: uppercase;
          letter-spacing: 0.5px;
          border-bottom: 2px solid #e2e8f0;
        }
        td {
          padding: 12px;
          font-size: 13px;
          color: #475569;
          border-bottom: 1px solid #f1f5f9;
        }
        tr:hover {
          background: #f8fafc;
        }
        .footer {
          margin-top: 60px;
          padding-top: 20px;
          border-top: 2px solid #e2e8f0;
          text-align: center;
          font-size: 12px;
          color: #94a3b8;
        }
        @media print {
          body {
            padding: 20px;
          }
          .summary {
            grid-template-columns: repeat(3, 1fr);
          }
          @page {
            margin: 1cm;
          }
        }
      </style>
    </head>
    <body>
      <div class="header">
        <h1>🐾 PETVET</h1>
        <p>Financial Statement</p>
      </div>
      
      <div class="period">
        Period: ${periodText} | Generated on: ${dateStr}
      </div>
      
      <div class="summary">
        <div class="summary-card revenue">
          <h3>Total Revenue</h3>
          <div class="value">LKR ${data.totalRevenue.toLocaleString()}</div>
        </div>
        <div class="summary-card expense">
          <h3>Total Expenses</h3>
          <div class="value">LKR ${data.totalExpenses.toLocaleString()}</div>
        </div>
        <div class="summary-card profit">
          <h3>Net Profit</h3>
          <div class="value">LKR ${data.netProfit.toLocaleString()}</div>
        </div>
      </div>
      
      <div class="breakdown">
        <h2>Income Breakdown</h2>
        <div class="breakdown-grid">
          <div class="breakdown-item">
            <label>Commission Income:</label>
            <value>LKR ${data.commissionIncome.toLocaleString()}</value>
          </div>
          <div class="breakdown-item">
            <label>Direct Sales:</label>
            <value>LKR ${data.directSales.toLocaleString()}</value>
          </div>
          <div class="breakdown-item">
            <label>Pending Payments:</label>
            <value>LKR ${data.pendingPayments.toLocaleString()}</value>
          </div>
          <div class="breakdown-item">
            <label>Transaction Count:</label>
            <value>${data.paidCount} Paid, ${data.pendingCount} Pending</value>
          </div>
        </div>
      </div>
      
      <div class="transactions">
        <h2>Transaction Details</h2>
        <table>
          <thead>
            <tr>
              <th>Date</th>
              <th>Type</th>
              <th>Partner/Source</th>
              <th>Amount (LKR)</th>
              <th>Status</th>
              <th>Notes</th>
            </tr>
          </thead>
          <tbody>
            ${data.transactions.map(t => `
              <tr>
                <td>${t.date}</td>
                <td>${t.type}</td>
                <td>${t.partner}</td>
                <td>${t.amount}</td>
                <td>${t.status}</td>
                <td>${t.notes}</td>
              </tr>
            `).join('')}
          </tbody>
        </table>
      </div>
      
      <div class="footer">
        <p>This is a computer-generated statement and does not require a signature.</p>
        <p>&copy; ${today.getFullYear()} PETVET. All rights reserved.</p>
      </div>
    </body>
    </html>
  `;
}

// Notification function
function showNotification(message, type = 'info') {
  // Create notification element
  const notification = document.createElement('div');
  notification.className = `fp-notification ${type}`;
  notification.innerHTML = `
    <div class="notification-content">
      <span class="notification-icon">${type === 'success' ? '✓' : 'ℹ'}</span>
      <span class="notification-message">${message}</span>
    </div>
  `;
  
  // Add styles if not already present
  if (!document.getElementById('notification-styles')) {
    const style = document.createElement('style');
    style.id = 'notification-styles';
    style.textContent = `
      .fp-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        background: white;
        padding: 16px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 10000;
        animation: slideIn 0.3s ease;
        border-left: 4px solid #3b82f6;
      }
      .fp-notification.success {
        border-left-color: #10b981;
      }
      .notification-content {
        display: flex;
        align-items: center;
        gap: 12px;
      }
      .notification-icon {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 16px;
      }
      .fp-notification.success .notification-icon {
        background: #d1fae5;
        color: #065f46;
      }
      .notification-message {
        font-size: 14px;
        font-weight: 500;
        color: #1e293b;
      }
      @keyframes slideIn {
        from {
          transform: translateX(400px);
          opacity: 0;
        }
        to {
          transform: translateX(0);
          opacity: 1;
        }
      }
    `;
    document.head.appendChild(style);
  }
  
  document.body.appendChild(notification);
  
  // Remove after 3 seconds
  setTimeout(() => {
    notification.style.animation = 'slideIn 0.3s ease reverse';
    setTimeout(() => {
      document.body.removeChild(notification);
    }, 300);
  }, 3000);
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
  drawRevenueExpensesChart();
  updateStats();
  
  // Redraw chart on window resize
  let resizeTimeout;
  window.addEventListener('resize', function() {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(function() {
      const container = document.getElementById('revenueExpensesChart');
      if (container) {
        container.innerHTML = '';
        drawRevenueExpensesChart();
      }
    }, 250);
  });
});
