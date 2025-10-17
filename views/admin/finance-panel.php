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
      <div class="stat-value" id="totalRevenue">LKR 458,000</div>
      <div class="stat-change positive">
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
          <path d="M6 10V2M6 2L2 6M6 2l4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span>+6.2%</span>
      </div>
    </div>

    <div class="fp-stat-card commission">
      <div class="stat-header">
        <span class="stat-icon">📊</span>
        <span class="stat-label">Commission Income</span>
      </div>
      <div class="stat-value" id="commissionIncome">LKR 154,000</div>
      <div class="stat-change positive">
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
          <path d="M6 10V2M6 2L2 6M6 2l4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span>+3.1%</span>
      </div>
    </div>

    <div class="fp-stat-card direct-sales">
      <div class="stat-header">
        <span class="stat-icon">💵</span>
        <span class="stat-label">Direct Sales Income</span>
      </div>
      <div class="stat-value" id="directSales">LKR 98,000</div>
      <div class="stat-change positive">
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
          <path d="M6 10V2M6 2L2 6M6 2l4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span>+4.4%</span>
      </div>
    </div>

    <div class="fp-stat-card expenses">
      <div class="stat-header">
        <span class="stat-icon">💳</span>
        <span class="stat-label">Total Expenses</span>
      </div>
      <div class="stat-value" id="totalExpenses">LKR 72,000</div>
      <div class="stat-change negative">
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
          <path d="M6 2v8M6 10l-4-4M6 10l4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span>-1.2%</span>
      </div>
    </div>

    <div class="fp-stat-card profit">
      <div class="stat-header">
        <span class="stat-icon">📈</span>
        <span class="stat-label">Net Profit</span>
      </div>
      <div class="stat-value" id="netProfit">LKR 386,000</div>
      <div class="stat-change positive">
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
          <path d="M6 10V2M6 2L2 6M6 2l4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span>+4.8%</span>
      </div>
    </div>

    <div class="fp-stat-card pending">
      <div class="stat-header">
        <span class="stat-icon">⚠️</span>
        <span class="stat-label">Pending Payments</span>
      </div>
      <div class="stat-value" id="pendingPayments">LKR 15,000</div>
      <div class="stat-change positive">
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
          <path d="M6 10V2M6 2L2 6M6 2l4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span>+0.5%</span>
      </div>
    </div>
  </div>

  <!-- Charts Section -->
  <div class="fp-charts">
    <div class="fp-chart-card revenue-chart">
      <div class="chart-header">
        <div>
          <h3>Revenue vs Expenses</h3>
          <p class="chart-subtitle">This Month</p>
        </div>
        <div class="chart-legend">
          <span class="legend-item">
            <span class="legend-dot revenue"></span> Revenue
          </span>
          <span class="legend-item">
            <span class="legend-dot expenses"></span> Expenses
          </span>
        </div>
      </div>
      <div id="revenueExpensesChart" class="chart-canvas"></div>
    </div>

    <div class="fp-chart-card income-breakdown">
      <div class="chart-header">
        <div>
          <h3>Income Breakdown</h3>
          <p class="chart-subtitle"></p>
        </div>
      </div>
      <div id="incomeBreakdownChart" class="chart-canvas donut-chart">
        <svg width="200" height="200" viewBox="0 0 200 200">
          <circle cx="100" cy="100" r="80" fill="none" stroke="#3b82f6" stroke-width="40" stroke-dasharray="314.16 188.5" transform="rotate(-90 100 100)"/>
          <circle cx="100" cy="100" r="80" fill="none" stroke="#10b981" stroke-width="40" stroke-dasharray="188.5 314.16" stroke-dashoffset="-314.16" transform="rotate(-90 100 100)"/>
          <text x="100" y="95" text-anchor="middle" font-size="16" font-weight="700" fill="#1e293b">LKR 458k</text>
        </svg>
        <div class="donut-legend">
          <div class="donut-legend-item">
            <span class="legend-dot" style="background: #3b82f6;"></span>
            <span class="legend-text">Commission (61%)</span>
          </div>
          <div class="donut-legend-item">
            <span class="legend-dot" style="background: #10b981;"></span>
            <span class="legend-text">Direct Sales (39%)</span>
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
          <option value="all">Filter: Type</option>
          <option value="Commission">Commission</option>
          <option value="Direct Sale">Direct Sale</option>
          <option value="Expense">Expense</option>
        </select>
        <select id="statusFilter" class="fp-filter-select">
          <option value="all">Filter: Status</option>
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
            <th>Type</th>
            <th>Partner / Source</th>
            <th>Amount (LKR)</th>
            <th>Status</th>
            <th>Notes</th>
          </tr>
        </thead>
        <tbody id="fpTableBody">
          <tr data-type="Commission" data-status="Paid" data-date="2023-10-26">
            <td>2023-10-26</td>
            <td><span class="type-badge commission">Commission</span></td>
            <td>Happy Paws Clinic</td>
            <td class="amount positive">1,250.00</td>
            <td><span class="status-badge paid">Paid</span></td>
            <td class="notes">Q3 Commission</td>
          </tr>
          <tr data-type="Direct Sale" data-status="Paid" data-date="2023-10-25">
            <td>2023-10-25</td>
            <td><span class="type-badge sale">Direct Sale</span></td>
            <td>Online Shop</td>
            <td class="amount positive">3,500.00</td>
            <td><span class="status-badge paid">Paid</span></td>
            <td class="notes">Order #1234</td>
          </tr>
          <tr data-type="Expense" data-status="Paid" data-date="2023-10-24">
            <td>2023-10-24</td>
            <td><span class="type-badge expense">Expense</span></td>
            <td>AWS Hosting</td>
            <td class="amount negative">-5,000.00</td>
            <td><span class="status-badge paid">Paid</span></td>
            <td class="notes">Monthly Bill</td>
          </tr>
          <tr data-type="Commission" data-status="Pending" data-date="2023-10-23">
            <td>2023-10-23</td>
            <td><span class="type-badge commission">Commission</span></td>
            <td>The Vet Hub</td>
            <td class="amount positive">850.00</td>
            <td><span class="status-badge pending">Pending</span></td>
            <td class="notes">Q3 Commission</td>
          </tr>
          <tr data-type="Direct Sale" data-status="Paid" data-date="2023-10-22">
            <td>2023-10-22</td>
            <td><span class="type-badge sale">Direct Sale</span></td>
            <td>Online Shop</td>
            <td class="amount positive">999.00</td>
            <td><span class="status-badge paid">Paid</span></td>
            <td class="notes">Order #1232</td>
          </tr>
          <tr data-type="Direct Sale" data-status="Paid" data-date="2023-10-21">
            <td>2023-10-21</td>
            <td><span class="type-badge sale">Direct Sale</span></td>
            <td>Online Shop</td>
            <td class="amount positive">2,150.00</td>
            <td><span class="status-badge paid">Paid</span></td>
            <td class="notes">Order #1233</td>
          </tr>
          <tr data-type="Commission" data-status="Paid" data-date="2023-10-20">
            <td>2023-10-20</td>
            <td><span class="type-badge commission">Commission</span></td>
            <td>Happy Paws Clinic</td>
            <td class="amount positive">1,800.00</td>
            <td><span class="status-badge paid">Paid</span></td>
            <td class="notes">Q3 Commission</td>
          </tr>
          <tr data-type="Expense" data-status="Paid" data-date="2023-10-19">
            <td>2023-10-19</td>
            <td><span class="type-badge expense">Expense</span></td>
            <td>Marketing Campaign</td>
            <td class="amount negative">-8,500.00</td>
            <td><span class="status-badge paid">Paid</span></td>
            <td class="notes">Social Media Ads</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="/PETVET/public/js/admin/finance_panel.js"></script>
