<link rel="stylesheet" href="/PETVET/public/css/admin/finance_panel.css">

<div class="main-content">
  <div class="fp-header">
    <h1>Finance & Reports</h1>
    <div class="fp-header-actions">
      <select id="monthFilter" class="fp-month-select">
        <option value="current">📅 This Month</option>
        <option value="last">Last Month</option>
        <option value="3months">Last 3 Months</option>
        <option value="6months">Last 6 Months</option>
        <option value="year">This Year</option>
      </select>
      <button class="fp-export-btn">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
          <path d="M14 10v2.667A1.333 1.333 0 0112.667 14H3.333A1.333 1.333 0 012 12.667V10M11.333 5.333L8 2m0 0L4.667 5.333M8 2v8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Export Report
      </button>
      <button class="fp-statement-btn">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
          <path d="M13 13H3a1 1 0 01-1-1V4a1 1 0 011-1h10a1 1 0 011 1v8a1 1 0 01-1 1z" stroke="currentColor" stroke-width="1.5"/>
          <path d="M5 7h6M5 10h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        Generate Statement
      </button>
    </div>
  </div>

  <!-- Search -->
  <div class="fp-search-box">
    <svg class="search-icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
      <path d="M9 17A8 8 0 1 0 9 1a8 8 0 0 0 0 16zM19 19l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
    </svg>
    <input type="text" id="fpSearchInput" placeholder="Search transactions or partners..." />
  </div>

  <!-- Stats Cards -->
  <div class="fp-stats">
    <div class="fp-stat-card revenue">
      <div class="stat-header">
        <span class="stat-icon">💰</span>
        <span class="stat-label">Total Revenue</span>
      </div>
      <div class="stat-value" id="totalRevenue">LKR 87,500</div>
      <div class="stat-change positive">
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
          <path d="M6 10V2M6 2L2 6M6 2l4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span>+15%</span>
      </div>
    </div>

    <div class="fp-stat-card commission">
      <div class="stat-header">
        <span class="stat-icon">📊</span>
        <span class="stat-label">Pet Shop Commission</span>
      </div>
      <div class="stat-value" id="commissionIncome">LKR 87,500</div>
      <div class="stat-change positive">
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
          <path d="M6 10V2M6 2L2 6M6 2l4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span>+15%</span>
      </div>
    </div>

    <div class="fp-stat-card pending">
      <div class="stat-header">
        <span class="stat-icon">⏳</span>
        <span class="stat-label">Pending Payments</span>
      </div>
      <div class="stat-value" id="pendingPayments">LKR 8,750</div>
      <div class="stat-change">
        <span>2 shops</span>
      </div>
    </div>

    <div class="fp-stat-card profit">
      <div class="stat-header">
        <span class="stat-icon"> ️</span>
        <span class="stat-label">Products Sold</span>
      </div>
      <div class="stat-value" id="totalTransactions">1,247</div>
      <div class="stat-change">
        <span>This month</span>
      </div>
    </div>
  </div>

  <!-- Charts Section -->
  <div class="fp-charts">
    <div class="fp-chart-card revenue-chart full-width">
      <div class="chart-header">
        <div>
          <h3>Revenue by Pet Shop</h3>
          <p class="chart-subtitle">Commission from Partner Stores</p>
        </div>
      </div>
      <div id="revenueExpensesChart" class="chart-canvas category-chart">
        <div class="category-bars">
          <div class="category-bar-item">
            <div class="category-info">
              <span class="category-name">  PetMart Store</span>
              <span class="category-value">LKR 35,000</span>
            </div>
            <div class="category-bar-bg">
              <div class="category-bar-fill" style="width: 100%; background: linear-gradient(90deg, #3b82f6, #2563eb);"></div>
            </div>
          </div>
          <div class="category-bar-item">
            <div class="category-info">
              <span class="category-name">🏪 Pet Supplies Co.</span>
              <span class="category-value">LKR 26,250</span>
            </div>
            <div class="category-bar-bg">
              <div class="category-bar-fill" style="width: 75%; background: linear-gradient(90deg, #10b981, #059669);"></div>
            </div>
          </div>
          <div class="category-bar-item">
            <div class="category-info">
              <span class="category-name">🏪 Happy Pets Shop</span>
              <span class="category-value">LKR 18,500</span>
            </div>
            <div class="category-bar-bg">
              <div class="category-bar-fill" style="width: 53%; background: linear-gradient(90deg, #f59e0b, #d97706);"></div>
            </div>
          </div>
          <div class="category-bar-item">
            <div class="category-info">
              <span class="category-name">  Paws & Claws</span>
              <span class="category-value">LKR 14,200</span>
            </div>
            <div class="category-bar-bg">
              <div class="category-bar-fill" style="width: 41%; background: linear-gradient(90deg, #8b5cf6, #7c3aed);"></div>
            </div>
          </div>
          <div class="category-bar-item">
            <div class="category-info">
              <span class="category-name">  Furry Friends Store</span>
              <span class="category-value">LKR 10,500</span>
            </div>
            <div class="category-bar-bg">
              <div class="category-bar-fill" style="width: 30%; background: linear-gradient(90deg, #ec4899, #db2777);"></div>
            </div>
          </div>
          <div class="category-bar-item">
            <div class="category-info">
              <span class="category-name">🏪 Pet Paradise</span>
              <span class="category-value">LKR 8,750</span>
            </div>
            <div class="category-bar-bg">
              <div class="category-bar-fill" style="width: 25%; background: linear-gradient(90deg, #06b6d4, #0891b2);"></div>
            </div>
          </div>
          <div class="category-bar-item">
            <div class="category-info">
              <span class="category-name">🏪 Animal Kingdom</span>
              <span class="category-value">LKR 6,300</span>
            </div>
            <div class="category-bar-bg">
              <div class="category-bar-fill" style="width: 18%; background: linear-gradient(90deg, #f97316, #ea580c);"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Financial Records Table -->
  <div class="fp-table-section">
    <div class="table-header">
      <h3>Recent Financial Records</h3>
      <div class="table-filters">
        <select id="typeFilter" class="fp-filter-select">
          <option value="all">All Pet Shops</option>
          <option value="PetMart Store">PetMart Store</option>
          <option value="Pet Supplies Co.">Pet Supplies Co.</option>
          <option value="Happy Pets Shop">Happy Pets Shop</option>
          <option value="Paws & Claws">Paws & Claws</option>
        </select>
        <select id="statusFilter" class="fp-filter-select">
          <option value="all">All Status</option>
          <option value="Paid">Paid</option>
          <option value="Pending">Pending</option>
        </select>
      </div>
    </div>

    <div class="fp-table-container">
      <table class="fp-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Pet Shop</th>
            <th>Products Sold</th>
            <th>Commission (LKR)</th>
            <th>Status</th>
            <th>Invoice</th>
          </tr>
        </thead>
        <tbody id="fpTableBody">
          <tr data-type="PetMart Store" data-status="Paid" data-date="2025-10-21">
            <td>2025-10-21</td>
            <td><span class="type-badge sale">PetMart Store</span></td>
            <td>142 products</td>
            <td class="amount positive">14,250.00</td>
            <td><span class="status-badge paid">Paid</span></td>
            <td class="notes">#INV-2025-1021</td>
          </tr>
          <tr data-type="Pet Supplies Co." data-status="Paid" data-date="2025-10-20">
            <td>2025-10-20</td>
            <td><span class="type-badge sale">Pet Supplies Co.</span></td>
            <td>98 products</td>
            <td class="amount positive">10,850.00</td>
            <td><span class="status-badge paid">Paid</span></td>
            <td class="notes">#INV-2025-1020</td>
          </tr>
          <tr data-type="Happy Pets Shop" data-status="Pending" data-date="2025-10-19">
            <td>2025-10-19</td>
            <td><span class="type-badge sale">Happy Pets Shop</span></td>
            <td>76 products</td>
            <td class="amount positive">8,750.00</td>
            <td><span class="status-badge pending">Pending</span></td>
            <td class="notes">#INV-2025-1019</td>
          </tr>
          <tr data-type="PetMart Store" data-status="Paid" data-date="2025-10-18">
            <td>2025-10-18</td>
            <td><span class="type-badge sale">PetMart Store</span></td>
            <td>125 products</td>
            <td class="amount positive">13,200.00</td>
            <td><span class="status-badge paid">Paid</span></td>
            <td class="notes">#INV-2025-1018</td>
          </tr>
          <tr data-type="Pet Supplies Co." data-status="Paid" data-date="2025-10-17">
            <td>2025-10-17</td>
            <td><span class="type-badge sale">Pet Supplies Co.</span></td>
            <td>89 products</td>
            <td class="amount positive">9,650.00</td>
            <td><span class="status-badge paid">Paid</span></td>
            <td class="notes">#INV-2025-1017</td>
          </tr>
          <tr data-type="Paws & Claws" data-status="Paid" data-date="2025-10-16">
            <td>2025-10-16</td>
            <td><span class="type-badge sale">Paws & Claws</span></td>
            <td>54 products</td>
            <td class="amount positive">6,100.00</td>
            <td><span class="status-badge paid">Paid</span></td>
            <td class="notes">#INV-2025-1016</td>
          </tr>
          <tr data-type="Happy Pets Shop" data-status="Paid" data-date="2025-10-15">
            <td>2025-10-15</td>
            <td><span class="type-badge sale">Happy Pets Shop</span></td>
            <td>103 products</td>
            <td class="amount positive">11,450.00</td>
            <td><span class="status-badge paid">Paid</span></td>
            <td class="notes">#INV-2025-1015</td>
          </tr>
          <tr data-type="PetMart Store" data-status="Paid" data-date="2025-10-14">
            <td>2025-10-14</td>
            <td><span class="type-badge sale">PetMart Store</span></td>
            <td>118 products</td>
            <td class="amount positive">12,750.00</td>
            <td><span class="status-badge paid">Paid</span></td>
            <td class="notes">#INV-2025-1014</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="/PETVET/public/js/admin/finance_panel.js"></script>
