<link rel="stylesheet" href="/PETVET/public/css/admin/styles.css" />
<div class="main-content">
  <header class="topbar">
    <h2>Finance Panel</h2>
    <div class="actions">
      <input type="text" placeholder="Search..." class="search-bar" />
      <button class="btn">Export Report</button>
      <button class="btn primary">View All Analytics</button>
      <div class="profile">
        <div class="circle">AJ</div>
        <span>Admin User</span>
      </div>
    </div>
  </header>

  <section class="overview">
    <h1>Finance Panel</h1>
    <p>Manage financial transactions and reporting</p>

    <div class="cards">
      <div class="card"><h3>Total Revenue</h3><div class="value-with-icon"><p class="number"><?php echo htmlspecialchars($stats['totalRevenue']); ?></p><div class="icon">💲</div></div><span class="success"><?php echo htmlspecialchars($stats['revenueGrowth']); ?></span></div>
      <div class="card"><h3>Expenses</h3><div class="value-with-icon"><p class="number"><?php echo htmlspecialchars($stats['expenses']); ?></p><div class="icon">💳</div></div><span class="error"><?php echo htmlspecialchars($stats['expensesGrowth']); ?></span></div>
      <div class="card"><h3>Net Profit</h3><div class="value-with-icon"><p class="number"><?php echo htmlspecialchars($stats['netProfit']); ?></p><div class="icon">📈</div></div><span class="success"><?php echo htmlspecialchars($stats['profitGrowth']); ?></span></div>
      <div class="card"><h3>Pending Payments</h3><div class="value-with-icon"><p class="number"><?php echo htmlspecialchars($stats['pendingPayments']); ?></p><div class="icon">📅</div></div><span class="success"><?php echo htmlspecialchars($stats['pendingGrowth']); ?></span></div>
    </div>
  </section>

  <section class="charts">
    <div class="chart-container">
      <h3>Revenue Overview</h3>
      <p>Monthly revenue for current year</p>
      <canvas id="lineChart" width="400" height="250"></canvas>
    </div>
    <div class="chart-container">
      <h3>Revenue by Service</h3>
      <p>Distribution of revenue sources</p>
      <canvas id="pieChart" width="300" height="250"></canvas>
    </div>
  </section>

  <section class="tables">
    <div class="card table-card">
      <h3>Recent Transactions</h3>
      <table>
        <thead>
          <tr><th>Invoice ID</th><th>Customer</th><th>Service</th><th>Amount</th><th>Date</th><th>Status</th></tr>
        </thead>
        <tbody>
          <?php foreach ($transactions as $transaction): ?>
          <tr>
            <td><?php echo htmlspecialchars($transaction['invoiceId']); ?></td>
            <td><?php echo htmlspecialchars($transaction['customer']); ?></td>
            <td><?php echo htmlspecialchars($transaction['service']); ?></td>
            <td><?php echo htmlspecialchars($transaction['amount']); ?></td>
            <td><?php echo htmlspecialchars($transaction['date']); ?></td>
            <td><?php echo htmlspecialchars($transaction['status']); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="card table-card">
      <h3>Recent Expenses</h3>
      <table>
        <thead>
          <tr><th>Expense ID</th><th>Category</th><th>Vendor</th><th>Amount</th><th>Date</th><th>Status</th></tr>
        </thead>
        <tbody>
          <?php foreach ($expenses as $expense): ?>
          <tr>
            <td><?php echo htmlspecialchars($expense['expenseId']); ?></td>
            <td><?php echo htmlspecialchars($expense['category']); ?></td>
            <td><?php echo htmlspecialchars($expense['vendor']); ?></td>
            <td><?php echo htmlspecialchars($expense['amount']); ?></td>
            <td><?php echo htmlspecialchars($expense['date']); ?></td>
            <td><?php echo htmlspecialchars($expense['status']); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>
<script src="/PETVET/public/js/admin/script-finance.js"></script>
