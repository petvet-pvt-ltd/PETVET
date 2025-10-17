<?php
// Admin Dashboard - simplified layout matching the design: top stat cards, large growth chart and role distribution donut.
// Safe defaults for variables to avoid notices during early testing.
$totalUsers = isset($totalUsers) ? $totalUsers : 1234;
$usersGrowth = isset($usersGrowth) ? $usersGrowth : '+12%';
$activeUsersToday = isset($activeUsersToday) ? $activeUsersToday : 89;
$pendingRequests = isset($pendingRequests) ? $pendingRequests : 7;
$totalClinics = isset($totalClinics) ? $totalClinics : 45;

?>
<link rel="stylesheet" href="/PETVET/public/css/admin/styles.css">
<link rel="stylesheet" href="/PETVET/public/css/admin/dashboard.css">

<div class="main-content">
  <header class="topbar">
    <div class="topbar-left">
      <h2>Dashboard Overview</h2>
      <p class="breadcrumb">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:inline-block;vertical-align:middle;margin-right:4px">
          <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Admin / Dashboard
      </p>
    </div>
    <div class="actions">
      <button class="icon-btn refresh-btn" title="Refresh Data">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M21 2v6h-6M3 22v-6h6M21 8a10 10 0 0 0-17-4.5M3 16a10 10 0 0 0 17 4.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
      <button class="icon-btn export-btn" title="Export Report">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
      <div class="profile">
        <div class="circle">AJ</div>
        <span>Admin User</span>
      </div>
    </div>
  </header>

  <section class="overview">
    <!-- Enhanced Stat Cards -->
    <div class="dashboard-top-cards">
      <div class="stat-card card-hover" data-count="<?php echo $totalUsers; ?>">
        <div class="stat-card-header">
          <div class="stat-icon users-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <h4>Total Users</h4>
        </div>
        <div class="stat-value animated-number" data-target="<?php echo $totalUsers; ?>">0</div>
        <div class="stat-sub success">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:inline-block;vertical-align:middle">
            <path d="M18 15l-6-6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <?php echo htmlspecialchars($usersGrowth); ?> since last month
        </div>
        <div class="stat-sparkline" data-type="users"></div>
      </div>

      <div class="stat-card card-hover" data-count="<?php echo $activeUsersToday; ?>">
        <div class="stat-card-header">
          <div class="stat-icon active-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
              <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
          </div>
          <h4>Active Users Today</h4>
        </div>
        <div class="stat-value animated-number" data-target="<?php echo $activeUsersToday; ?>">0</div>
        <div class="stat-sub success">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:inline-block;vertical-align:middle">
            <path d="M18 15l-6-6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          +5% since yesterday
        </div>
        <div class="stat-sparkline" data-type="active"></div>
      </div>

      <div class="stat-card card-hover <?php echo $pendingRequests > 0 ? 'has-pending' : ''; ?>" data-count="<?php echo $pendingRequests; ?>">
        <div class="stat-card-header">
          <div class="stat-icon pending-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10zM12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <h4>Pending Requests</h4>
        </div>
        <div class="stat-value animated-number" data-target="<?php echo $pendingRequests; ?>">0</div>
        <div class="stat-sub warning">
          <?php if($pendingRequests > 0): ?>
            <span class="pulse-dot"></span>
            Awaiting approval
          <?php else: ?>
            All caught up!
          <?php endif; ?>
        </div>
        <?php if($pendingRequests > 0): ?>
          <button class="stat-action-btn">Review Now →</button>
        <?php endif; ?>
      </div>

      <div class="stat-card card-hover" data-count="<?php echo $totalClinics; ?>">
        <div class="stat-card-header">
          <div class="stat-icon clinics-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2zM12 11v6M9 14h6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <h4>Total Clinics</h4>
        </div>
        <div class="stat-value animated-number" data-target="<?php echo $totalClinics; ?>">0</div>
        <div class="stat-sub info">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:inline-block;vertical-align:middle">
            <path d="M18 15l-6-6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          +1 since last week
        </div>
        <div class="stat-sparkline" data-type="clinics"></div>
      </div>
    </div>

    <!-- Enhanced Charts Section -->
    <div class="dashboard-grid">
      <div class="chart-large">
        <div class="chart-header">
          <div>
            <h3>User Growth</h3>
            <p class="muted">Monthly active users — last 12 months</p>
          </div>
          <div class="chart-controls">
            <button class="chart-btn active" data-period="12m">12M</button>
            <button class="chart-btn" data-period="6m">6M</button>
            <button class="chart-btn" data-period="3m">3M</button>
            <button class="chart-btn" data-period="1m">1M</button>
          </div>
        </div>
        <div class="chart-placeholder" id="growthChart">
          <div class="loading-spinner"></div>
        </div>
      </div>

      <div class="chart-side">
        <div class="chart-header">
          <div>
            <h3>Role Distribution</h3>
            <p class="muted">Active users by role</p>
          </div>
        </div>
        <div class="donut-placeholder" id="roleDonut">
          <div class="loading-spinner"></div>
        </div>
        <div class="legend-list">
          <div class="legend-item" data-role="owners">
            <span class="legend-dot" style="background:#3b82f6"></span>
            <span class="legend-label">Pet Owners</span>
            <span class="legend-value">720</span>
            <span class="legend-percent">58%</span>
          </div>
          <div class="legend-item" data-role="sitters">
            <span class="legend-dot" style="background:#60a5fa"></span>
            <span class="legend-label">Pet Sitters</span>
            <span class="legend-value">145</span>
            <span class="legend-percent">12%</span>
          </div>
          <div class="legend-item" data-role="trainers">
            <span class="legend-dot" style="background:#10b981"></span>
            <span class="legend-label">Trainers</span>
            <span class="legend-value">189</span>
            <span class="legend-percent">15%</span>
          </div>
          <div class="legend-item" data-role="groomers">
            <span class="legend-dot" style="background:#f59e0b"></span>
            <span class="legend-label">Groomers</span>
            <span class="legend-value">135</span>
            <span class="legend-percent">11%</span>
          </div>
          <div class="legend-item" data-role="clinics">
            <span class="legend-dot" style="background:#ef4444"></span>
            <span class="legend-label">Clinics</span>
            <span class="legend-value">45</span>
            <span class="legend-percent">4%</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Additional Info Cards -->
    <div class="info-cards-row">
      <div class="info-card">
        <div class="info-icon activity-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M22 12h-4l-3 9L9 3l-3 9H2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <div class="info-content">
          <div class="info-title">Recent Activity</div>
          <div class="info-value">1,428 actions today</div>
        </div>
      </div>

      <div class="info-card">
        <div class="info-icon revenue-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
            <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </div>
        <div class="info-content">
          <div class="info-title">Avg. Response Time</div>
          <div class="info-value">2.4 hours</div>
        </div>
      </div>

      <div class="info-card">
        <div class="info-icon satisfaction-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
            <path d="M8 14s1.5 2 4 2 4-2 4-2M9 9h.01M15 9h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </div>
        <div class="info-content">
          <div class="info-title">User Satisfaction</div>
          <div class="info-value">94.5%</div>
        </div>
      </div>

      <div class="info-card">
        <div class="info-icon support-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <div class="info-content">
          <div class="info-title">Support Tickets</div>
          <div class="info-value">12 open</div>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Enhanced Dashboard JavaScript -->
<script>
(function() {
  'use strict';

  // ============================================
  // ANIMATED NUMBER COUNTERS
  // ============================================
  function animateNumbers() {
    document.querySelectorAll('.animated-number').forEach(elem => {
      const target = parseInt(elem.dataset.target) || 0;
      const duration = 1500;
      const startTime = performance.now();
      
      function update(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        // Easing function (easeOutExpo)
        const easeProgress = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
        const current = Math.floor(easeProgress * target);
        
        elem.textContent = current.toLocaleString();
        
        if (progress < 1) {
          requestAnimationFrame(update);
        }
      }
      
      requestAnimationFrame(update);
    });
  }

  // ============================================
  // SPARKLINE CHARTS
  // ============================================
  function drawSparklines() {
    const sparklines = {
      users: [50, 60, 55, 70, 65, 80, 75, 90],
      active: [30, 40, 35, 50, 45, 60, 70, 65],
      clinics: [10, 15, 12, 20, 18, 25, 30, 28]
    };

    document.querySelectorAll('.stat-sparkline').forEach(sparkline => {
      const type = sparkline.dataset.type;
      const data = sparklines[type] || sparklines.users;
      
      const width = sparkline.offsetWidth || 200;
      const height = 40;
      const max = Math.max(...data);
      const points = data.map((val, i) => {
        const x = (i / (data.length - 1)) * width;
        const y = height - (val / max) * height;
        return `${x},${y}`;
      }).join(' ');

      sparkline.innerHTML = `
        <svg width="${width}" height="${height}" viewBox="0 0 ${width} ${height}" style="display:block">
          <defs>
            <linearGradient id="gradient-${type}" x1="0%" y1="0%" x2="0%" y2="100%">
              <stop offset="0%" style="stop-color:currentColor;stop-opacity:0.3" />
              <stop offset="100%" style="stop-color:currentColor;stop-opacity:0" />
            </linearGradient>
          </defs>
          <polygon points="0,${height} ${points} ${width},${height}" fill="url(#gradient-${type})" />
          <polyline points="${points}" fill="none" stroke="currentColor" stroke-width="2" />
        </svg>
      `;
    });
  }

  // ============================================
  // GROWTH CHART
  // ============================================
  function drawGrowthChart() {
    const growth = document.getElementById('growthChart');
    if (!growth) return;

    // Sample data for 12 months
    const data = [820, 932, 901, 934, 1090, 1130, 1210, 1180, 1250, 1320, 1290, 1234];
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    
    const width = growth.offsetWidth || 800;
    const height = 360;
    const padding = { top: 40, right: 40, bottom: 50, left: 60 };
    const chartWidth = width - padding.left - padding.right;
    const chartHeight = height - padding.top - padding.bottom;
    
    const max = Math.max(...data);
    const min = Math.min(...data);
    const range = max - min;

    // Calculate points
    const points = data.map((val, i) => {
      const x = padding.left + (i / (data.length - 1)) * chartWidth;
      const y = padding.top + chartHeight - ((val - min) / range) * chartHeight;
      return { x, y, val };
    });

    const pathData = points.map((p, i) => `${i === 0 ? 'M' : 'L'} ${p.x},${p.y}`).join(' ');
    const areaData = `M ${padding.left},${height - padding.bottom} ${pathData} L ${points[points.length - 1].x},${height - padding.bottom} Z`;

    let svg = `<svg width="100%" height="${height}" viewBox="0 0 ${width} ${height}">`;
    
    // Grid lines
    svg += '<g class="grid">';
    for (let i = 0; i <= 5; i++) {
      const y = padding.top + (chartHeight / 5) * i;
      const value = Math.round(max - (range / 5) * i);
      svg += `<line x1="${padding.left}" y1="${y}" x2="${width - padding.right}" y2="${y}" stroke="#e5e7eb" stroke-width="1"/>`;
      svg += `<text x="${padding.left - 10}" y="${y + 4}" text-anchor="end" font-size="11" fill="#9ca3af">${value}</text>`;
    }
    svg += '</g>';

    // Area gradient
    svg += `<defs>
      <linearGradient id="chartGradient" x1="0%" y1="0%" x2="0%" y2="100%">
        <stop offset="0%" style="stop-color:#3b82f6;stop-opacity:0.2" />
        <stop offset="100%" style="stop-color:#3b82f6;stop-opacity:0" />
      </linearGradient>
    </defs>`;
    svg += `<path d="${areaData}" fill="url(#chartGradient)" />`;

    // Line
    svg += `<path d="${pathData}" fill="none" stroke="#3b82f6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>`;

    // Points
    points.forEach((p, i) => {
      svg += `<circle cx="${p.x}" cy="${p.y}" r="5" fill="#fff" stroke="#3b82f6" stroke-width="2" class="chart-point" data-value="${p.val}" data-month="${months[i]}"/>`;
    });

    // X-axis labels
    months.forEach((month, i) => {
      const x = padding.left + (i / (data.length - 1)) * chartWidth;
      svg += `<text x="${x}" y="${height - padding.bottom + 25}" text-anchor="middle" font-size="11" fill="#64748b">${month}</text>`;
    });

    svg += '</svg>';
    
    growth.innerHTML = svg;

    // Add hover effects
    growth.querySelectorAll('.chart-point').forEach(point => {
      point.addEventListener('mouseenter', function() {
        const value = this.dataset.value;
        const month = this.dataset.month;
        
        const tooltip = document.createElement('div');
        tooltip.className = 'chart-tooltip';
        tooltip.innerHTML = `<strong>${month}</strong><br>${parseInt(value).toLocaleString()} users`;
        tooltip.style.cssText = `
          position: absolute;
          background: #0f172a;
          color: white;
          padding: 8px 12px;
          border-radius: 6px;
          font-size: 12px;
          pointer-events: none;
          z-index: 1000;
          white-space: nowrap;
          box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;
        
        const rect = this.getBoundingClientRect();
        const containerRect = growth.getBoundingClientRect();
        tooltip.style.left = (rect.left - containerRect.left + rect.width / 2) + 'px';
        tooltip.style.top = (rect.top - containerRect.top - 10) + 'px';
        tooltip.style.transform = 'translate(-50%, -100%)';
        
        growth.style.position = 'relative';
        growth.appendChild(tooltip);
        
        this.setAttribute('r', '7');
      });
      
      point.addEventListener('mouseleave', function() {
        const tooltip = growth.querySelector('.chart-tooltip');
        if (tooltip) tooltip.remove();
        this.setAttribute('r', '5');
      });
    });
  }

  // ============================================
  // DONUT CHART
  // ============================================
  function drawDonutChart() {
    const donut = document.getElementById('roleDonut');
    if (!donut) return;

    const data = [
      { label: 'Owners', value: 720, color: '#3b82f6' },
      { label: 'Sitters', value: 145, color: '#60a5fa' },
      { label: 'Trainers', value: 189, color: '#10b981' },
      { label: 'Groomers', value: 135, color: '#f59e0b' },
      { label: 'Clinics', value: 45, color: '#ef4444' }
    ];

    const total = data.reduce((sum, d) => sum + d.value, 0);
    let cumulativePercent = 0;

    let svg = `<svg width="220" height="220" viewBox="0 0 42 42" style="transform: rotate(-90deg)">`;
    
    // Background circle
    svg += `<circle cx="21" cy="21" r="15.91549431" fill="transparent" stroke="#f1f5f9" stroke-width="4"></circle>`;

    // Segments
    data.forEach((item, index) => {
      const percent = (item.value / total) * 100;
      const offset = cumulativePercent;
      cumulativePercent += percent;

      svg += `<circle 
        class="donut-segment" 
        data-label="${item.label}" 
        data-value="${item.value}"
        cx="21" cy="21" r="15.91549431" 
        fill="transparent" 
        stroke="${item.color}" 
        stroke-width="4" 
        stroke-dasharray="${percent} ${100 - percent}" 
        stroke-dashoffset="${-offset}"
        style="transition: stroke-width 0.3s ease, opacity 0.3s ease; cursor: pointer"
      ></circle>`;
    });

    // Center circle
    svg += `<circle cx="21" cy="21" r="11" fill="#fff"></circle>`;
    svg += `</svg>`;

    // Center text
    svg = `<div style="position: relative; display: inline-block;">${svg}
      <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
        <div style="font-size: 24px; font-weight: 800; color: #0f172a;">${total.toLocaleString()}</div>
        <div style="font-size: 11px; color: #64748b; margin-top: 2px;">Total</div>
      </div>
    </div>`;

    donut.innerHTML = svg;

    // Add hover effects to segments
    donut.querySelectorAll('.donut-segment').forEach((segment, index) => {
      segment.addEventListener('mouseenter', function() {
        this.style.strokeWidth = '5';
        this.style.opacity = '1';
        
        // Highlight corresponding legend item
        const legendItems = document.querySelectorAll('.legend-item');
        legendItems[index].style.background = '#f8fafc';
        legendItems[index].style.transform = 'translateX(4px)';
      });
      
      segment.addEventListener('mouseleave', function() {
        this.style.strokeWidth = '4';
        
        // Reset legend item
        const legendItems = document.querySelectorAll('.legend-item');
        legendItems[index].style.background = '';
        legendItems[index].style.transform = '';
      });
    });
  }

  // ============================================
  // REFRESH BUTTON
  // ============================================
  document.querySelector('.refresh-btn')?.addEventListener('click', function() {
    this.style.transform = 'rotate(360deg)';
    this.style.transition = 'transform 0.6s ease';
    
    setTimeout(() => {
      this.style.transform = '';
      animateNumbers();
      showNotification('Dashboard data refreshed!', 'success');
    }, 600);
  });

  // ============================================
  // EXPORT BUTTON
  // ============================================
  document.querySelector('.export-btn')?.addEventListener('click', function() {
    showNotification('Exporting dashboard report...', 'info');
    
    setTimeout(() => {
      showNotification('Report exported successfully!', 'success');
    }, 1500);
  });

  // ============================================
  // CHART PERIOD BUTTONS
  // ============================================
  document.querySelectorAll('.chart-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.chart-btn').forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      
      const period = this.dataset.period;
      showNotification(`Showing data for ${period}`, 'info');
    });
  });

  // ============================================
  // NOTIFICATION SYSTEM
  // ============================================
  function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
      position: fixed;
      top: 80px;
      right: 20px;
      padding: 12px 18px;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 600;
      z-index: 10000;
      animation: slideInRight 0.3s ease;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    `;
    
    if (type === 'success') {
      notification.style.background = 'linear-gradient(135deg, #10b981, #059669)';
      notification.style.color = 'white';
    } else if (type === 'info') {
      notification.style.background = 'linear-gradient(135deg, #3b82f6, #2563eb)';
      notification.style.color = 'white';
    } else if (type === 'warning') {
      notification.style.background = 'linear-gradient(135deg, #f59e0b, #d97706)';
      notification.style.color = 'white';
    }
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
      notification.style.animation = 'slideOutRight 0.3s ease';
      setTimeout(() => notification.remove(), 300);
    }, 3000);
  }

  // ============================================
  // CARD HOVER EFFECTS
  // ============================================
  document.querySelectorAll('.stat-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
      this.style.transform = 'translateY(-4px)';
      this.style.boxShadow = '0 12px 28px rgba(2,6,23,0.12)';
    });
    
    card.addEventListener('mouseleave', function() {
      this.style.transform = '';
      this.style.boxShadow = '';
    });
  });

  // ============================================
  // LEGEND ITEM INTERACTIONS
  // ============================================
  document.querySelectorAll('.legend-item').forEach((item, index) => {
    item.addEventListener('mouseenter', function() {
      const segments = document.querySelectorAll('.donut-segment');
      if (segments[index]) {
        segments[index].style.strokeWidth = '5';
        segments[index].style.opacity = '1';
      }
      this.style.background = '#f8fafc';
      this.style.transform = 'translateX(4px)';
    });
    
    item.addEventListener('mouseleave', function() {
      const segments = document.querySelectorAll('.donut-segment');
      if (segments[index]) {
        segments[index].style.strokeWidth = '4';
      }
      this.style.background = '';
      this.style.transform = '';
    });
  });

  // ============================================
  // INITIALIZE ON LOAD
  // ============================================
  window.addEventListener('load', function() {
    // Stagger animations for better visual effect
    setTimeout(() => animateNumbers(), 100);
    setTimeout(() => drawSparklines(), 200);
    setTimeout(() => drawGrowthChart(), 300);
    setTimeout(() => drawDonutChart(), 400);
    
    // Fade in cards with stagger
    document.querySelectorAll('.stat-card').forEach((card, index) => {
      card.style.opacity = '0';
      card.style.transform = 'translateY(20px)';
      
      setTimeout(() => {
        card.style.transition = 'all 0.4s ease';
        card.style.opacity = '1';
        card.style.transform = 'translateY(0)';
      }, index * 100);
    });
  });

  // Add animation styles
  const style = document.createElement('style');
  style.textContent = `
    @keyframes slideInRight {
      from { transform: translateX(400px); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOutRight {
      from { transform: translateX(0); opacity: 1; }
      to { transform: translateX(400px); opacity: 0; }
    }
  `;
  document.head.appendChild(style);

})();
</script>
