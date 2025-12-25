<?php
// Admin Dashboard - Load data with PHP (Simple version)
require_once __DIR__ . '/../../config/connect.php';
$pdo = db();

// Get total users
$stmt = $pdo->query("SELECT COUNT(DISTINCT u.id) as total FROM users u 
    INNER JOIN user_roles ur ON u.id = ur.user_id 
    INNER JOIN roles r ON ur.role_id = r.id 
    WHERE r.role_name != 'admin' AND ur.verification_status = 'approved'");
$totalUsers = $stmt->fetchColumn() ?: 0;

// Get active users today
$stmt = $pdo->query("SELECT COUNT(DISTINCT id) as active FROM users WHERE DATE(last_login) = CURDATE()");
$activeUsersToday = $stmt->fetchColumn() ?: 0;

// Get pending clinics
$stmt = $pdo->query("SELECT COUNT(*) as pending FROM clinics WHERE verification_status = 'pending'");
$pendingRequests = $stmt->fetchColumn() ?: 0;

// Get total clinics
$stmt = $pdo->query("SELECT COUNT(*) as total FROM clinics");
$totalClinics = $stmt->fetchColumn() ?: 0;

// Get today's appointments
$stmt = $pdo->query("SELECT COUNT(*) as count FROM appointments WHERE DATE(appointment_date) = CURDATE()");
$todayAppointments = $stmt->fetchColumn() ?: 0;

// Get new users today
$stmt = $pdo->query("SELECT COUNT(DISTINCT u.id) as count FROM users u 
    INNER JOIN user_roles ur ON u.id = ur.user_id 
    INNER JOIN roles r ON ur.role_id = r.id 
    WHERE r.role_name != 'admin' AND DATE(u.created_at) = CURDATE()");
$newUsersToday = $stmt->fetchColumn() ?: 0;

// Get active clinics
$stmt = $pdo->query("SELECT COUNT(*) as count FROM clinics WHERE is_active = 1");
$activeClinics = $stmt->fetchColumn() ?: 0;
?>
<link rel="stylesheet" href="/PETVET/public/css/admin/styles.css">
<link rel="stylesheet" href="/PETVET/public/css/admin/dashboard.css">

<div class="main-content">
  <?php 
  // Include user welcome header
  include __DIR__ . '/../shared/components/user-welcome-header.php'; 
  ?>
  
  <section class="overview">
    <!-- Enhanced Stat Cards -->
    <div class="dashboard-top-cards">
      <div class="stat-card card-hover">
        <div class="stat-card-header">
          <div class="stat-icon users-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <h4>Total Users</h4>
        </div>
        <div class="stat-value"><?= $totalUsers ?></div>
        <div class="stat-sub success">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:inline-block;vertical-align:middle">
            <path d="M18 15l-6-6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <span>Approved users</span>
        </div>
      </div>

      <div class="stat-card card-hover">
        <div class="stat-card-header">
          <div class="stat-icon active-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
              <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
          </div>
          <h4>Active Users Today</h4>
        </div>
        <div class="stat-value"><?= $activeUsersToday ?></div>
        <div class="stat-sub success">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:inline-block;vertical-align:middle">
            <path d="M18 15l-6-6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          Logged in today
        </div>
      </div>

      <div class="stat-card card-hover" id="pendingCard">
        <div class="stat-card-header">
          <div class="stat-icon pending-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10zM12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <h4>Pending Requests</h4>
        </div>
        <div class="stat-value"><?= $pendingRequests ?></div>
        <div class="stat-sub warning">
          <span class="pulse-dot"></span>
          Awaiting approval
        </div>
        <?php if ($pendingRequests > 0): ?>
        <button class="stat-action-btn" onclick="window.location.href='/PETVET/index.php?module=admin&page=manage-clinics&filter=pending'">Review Now →</button>
        <?php endif; ?>
      </div>

      <div class="stat-card card-hover">
        <div class="stat-card-header">
          <div class="stat-icon clinics-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2zM12 11v6M9 14h6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <h4>Total Clinics</h4>
        </div>
        <div class="stat-value"><?= $totalClinics ?></div>
        <div class="stat-sub info">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:inline-block;vertical-align:middle">
            <path d="M18 15l-6-6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          Registered partners
        </div>
      </div>
    </div>

    <!-- Enhanced Charts Section -->
    <div class="dashboard-grid">
      <div class="chart-large">
        <div class="chart-header">
          <div>
            <h3>Quick Actions</h3>
            <p class="muted">Common administrative tasks</p>
          </div>
        </div>
        <div style="padding: 30px;">
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
            <a href="/PETVET/index.php?module=admin&page=manage-users-by-role" style="text-decoration: none;">
              <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 25px; border-radius: 12px; color: white; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">Manage</div>
                <div style="font-size: 20px; font-weight: 700;">Users</div>
              </div>
            </a>
            <a href="/PETVET/index.php?module=admin&page=manage-clinics" style="text-decoration: none;">
              <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 25px; border-radius: 12px; color: white; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">Manage</div>
                <div style="font-size: 20px; font-weight: 700;">Clinics</div>
              </div>
            </a>
            <a href="/PETVET/index.php?module=admin&page=reports" style="text-decoration: none;">
              <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); padding: 25px; border-radius: 12px; color: white; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">View</div>
                <div style="font-size: 20px; font-weight: 700;">Reports</div>
              </div>
            </a>
            <div style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); padding: 25px; border-radius: 12px; color: white; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
              <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">System</div>
              <div style="font-size: 20px; font-weight: 700;">Settings</div>
            </div>
          </div>
        </div>
      </div>

      <div class="chart-side">
        <div class="chart-header">
          <div>
            <h3>System Status</h3>
            <p class="muted">Platform health</p>
          </div>
        </div>
        <div style="padding: 20px;">
          <div style="margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
              <span style="font-size: 14px; color: #6B7280;">Database</span>
              <span style="font-size: 14px; font-weight: 600; color: #059669;">Online</span>
            </div>
            <div style="height: 6px; background: #E5E7EB; border-radius: 3px; overflow: hidden;">
              <div style="width: 100%; height: 100%; background: linear-gradient(90deg, #059669, #10B981);"></div>
            </div>
          </div>
          <div style="margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
              <span style="font-size: 14px; color: #6B7280;">Server</span>
              <span style="font-size: 14px; font-weight: 600; color: #059669;">Healthy</span>
            </div>
            <div style="height: 6px; background: #E5E7EB; border-radius: 3px; overflow: hidden;">
              <div style="width: 95%; height: 100%; background: linear-gradient(90deg, #059669, #10B981);"></div>
            </div>
          </div>
          <div style="margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
              <span style="font-size: 14px; color: #6B7280;">API Status</span>
              <span style="font-size: 14px; font-weight: 600; color: #059669;">Active</span>
            </div>
            <div style="height: 6px; background: #E5E7EB; border-radius: 3px; overflow: hidden;">
              <div style="width: 100%; height: 100%; background: linear-gradient(90deg, #059669, #10B981);"></div>
            </div>
          </div>
          <div style="margin-top: 30px; padding: 15px; background: #F3F4F6; border-radius: 8px;">
            <div style="font-size: 12px; color: #6B7280; margin-bottom: 5px;">Total Users</div>
            <div style="font-size: 24px; font-weight: 700; color: #1F2937;"><?= $totalUsers ?></div>
          </div>
          <div style="margin-top: 15px; padding: 15px; background: #F3F4F6; border-radius: 8px;">
            <div style="font-size: 12px; color: #6B7280; margin-bottom: 5px;">Active Clinics</div>
            <div style="font-size: 24px; font-weight: 700; color: #1F2937;"><?= $activeClinics ?></div>
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
          <div class="info-title">Appointments Today</div>
          <div class="info-value"><?= $todayAppointments ?></div>
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
          <div class="info-title">New Users Today</div>
          <div class="info-value"><?= $newUsersToday ?></div>
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
          <div class="info-title">Active Clinics</div>
          <div class="info-value"><?= $activeClinics ?></div>
        </div>
      </div>

      <div class="info-card">
        <div class="info-icon support-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <div class="info-content">
          <div class="info-title">Total System Users</div>
          <div class="info-value"><?= $totalUsers ?></div>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Enhanced Dashboard JavaScript -->
<script>
(function() {
  'use strict';

  let dashboardData = null;

  // Load dashboard data on page load
  document.addEventListener('DOMContentLoaded', function() {
    loadDashboardData();
  });

  async function loadDashboardData() {
    try {
      const response = await fetch('/PETVET/api/admin/get-dashboard-stats.php');
      const data = await response.json();
      
      if (data.success) {
        dashboardData = data;
        updateDashboard(data);
      } else {
        console.error('Error loading dashboard:', data.error);
      }
    } catch (error) {
      console.error('Error:', error);
    }
  }

  function updateDashboard(data) {
    // Update stat cards
    updateStatCards(data.stats);
    
    // Update role distribution
    updateRoleDistribution(data.roleDistribution);
    
    // Draw charts
    setTimeout(() => {
      animateNumbers(data.stats);
      drawSparklines();
      drawGrowthChart(data.growthData);
      drawRoleDonut(data.roleDistribution);
    }, 100);
  }

  function updateStatCards(stats) {
    document.getElementById('totalUsers').textContent = stats.totalUsers || 0;
    document.getElementById('usersGrowth').textContent = stats.usersGrowth || '+0%';
    document.getElementById('activeUsersToday').textContent = stats.activeUsersToday || 0;
    document.getElementById('pendingRequests').textContent = stats.pendingRequests || 0;
    document.getElementById('totalClinics').textContent = stats.totalClinics || 0;
    
    // Update info cards
    document.getElementById('todayAppointments').textContent = stats.todayAppointments || 0;
    document.getElementById('newUsersToday').textContent = stats.newUsersToday || 0;
    document.getElementById('activeClinicsInfo').textContent = stats.totalClinics || 0;
    document.getElementById('totalSystemUsers').textContent = stats.totalUsers || 0;
    
    // Update pending card status
    const pendingCard = document.getElementById('pendingCard');
    const pendingStatus = document.getElementById('pendingStatus');
    const reviewBtn = document.getElementById('reviewBtn');
    
    if (stats.pendingRequests > 0) {
      pendingCard.classList.add('has-pending');
      pendingStatus.innerHTML = '<span class="pulse-dot"></span> Awaiting approval';
      reviewBtn.style.display = 'block';
    } else {
      pendingCard.classList.remove('has-pending');
      pendingStatus.innerHTML = 'All caught up!';
      reviewBtn.style.display = 'none';
    }
  }

  function updateRoleDistribution(roleDistribution) {
    const legendList = document.getElementById('legendList');
    const colors = {
      'pet_owner': '#3b82f6',
      'sitter': '#60a5fa',
      'trainer': '#10b981',
      'groomer': '#f59e0b',
      'vet': '#ef4444',
      'breeder': '#8b5cf6'
    };
    
    legendList.innerHTML = roleDistribution.map(role => {
      const color = colors[role.role_name] || '#6b7280';
      return `
        <div class="legend-item">
          <span class="legend-dot" style="background:${color}"></span>
          <span class="legend-label">${role.role_display_name}</span>
          <span class="legend-value">${role.count}</span>
          <span class="legend-percent">${role.percentage}%</span>
        </div>
      `;
    }).join('');
  }

  // ============================================
  // ANIMATED NUMBER COUNTERS
  // ============================================
  function animateNumbers(stats) {
    // Set data-target attributes from real data
    document.getElementById('totalUsers').setAttribute('data-target', stats.totalUsers);
    document.getElementById('activeUsersToday').setAttribute('data-target', stats.activeUsersToday);
    document.getElementById('pendingRequests').setAttribute('data-target', stats.pendingRequests);
    document.getElementById('totalClinics').setAttribute('data-target', stats.totalClinics);
    
    document.querySelectorAll('.animated-number').forEach(elem => {
      const target = parseInt(elem.getAttribute('data-target')) || 0;
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
  function drawGrowthChart(growthData) {
    const growth = document.getElementById('growthChart');
    if (!growth || !growthData || growthData.length === 0) return;

    const width = growth.offsetWidth || 800;
    const height = 300;
    const padding = { top: 20, right: 30, bottom: 50, left: 60 };
    const chartWidth = width - padding.left - padding.right;
    const chartHeight = height - padding.top - padding.bottom;

    // Format month data
    const months = growthData.map(d => {
      const date = new Date(d.month + '-01');
      return date.toLocaleDateString('en-US', { month: 'short' });
    });
    
    const data = growthData.map(d => parseInt(d.count));
    const max = Math.max(...data);
    const min = Math.min(...data);
    const range = max - min;

    // Calculate points with cumulative totals
    let cumulative = 0;
    const points = data.map((val, i) => {
      cumulative += val;
      const x = padding.left + (i / (data.length - 1)) * chartWidth;
      const y = padding.top + chartHeight - ((cumulative - min) / (range || 1)) * chartHeight;
      return { x, y, val: cumulative };
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
  function drawRoleDonut(roleDistribution) {
    const donut = document.getElementById('roleDonut');
    if (!donut || !roleDistribution || roleDistribution.length === 0) return;

    const colors = {
      'pet_owner': '#3b82f6',
      'sitter': '#60a5fa',
      'trainer': '#10b981',
      'groomer': '#f59e0b',
      'vet': '#ef4444',
      'breeder': '#8b5cf6'
    };

    const data = roleDistribution.map(role => ({
      label: role.role_display_name,
      value: parseInt(role.count),
      color: colors[role.role_name] || '#6b7280'
    }));

    const total = data.reduce((sum, d) => sum + d.value, 0);
    if (total === 0) {
      donut.innerHTML = '<div style="text-align:center;padding:40px;color:#9ca3af;">No data available</div>';
      return;
    }
    
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
        if (legendItems[index]) {
          legendItems[index].style.background = '#f8fafc';
          legendItems[index].style.transform = 'translateX(4px)';
        }
      });
      
      segment.addEventListener('mouseleave', function() {
        this.style.strokeWidth = '4';
        
        // Reset legend item
        const legendItems = document.querySelectorAll('.legend-item');
        if (legendItems[index]) {
          legendItems[index].style.background = '';
          legendItems[index].style.transform = '';
        }
      });
    });
  }

})();
</script>
