<?php
// View receives $data from the controller
extract($data ?? []); // exposes: $rangeStart, $rangeEnd, $apptStatus, $workload, $productTotals,
// $appointmentsRevenue, $shopRevenue, $grossRevenue, $netIncome,
// $labels, $bars, $shopBars, $maxBar, $clinicPct, $shopPct, $rangeMode
$rangeMode = $rangeMode ?? ($_GET['range'] ?? 'week'); // week|month|year|custom
function isActive($cur,$mode){ return $cur === $mode ? ' active' : ''; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Reports & Analytics | Clinic Manager</title>
  <link rel="stylesheet" href="/PETVET/public/css/clinic-manager/enhanced-global.css">
  <link rel="stylesheet" href="/PETVET/public/css/clinic-manager/reports.css">
  <style>
    .reports-header {
      background: var(--gradient-card);
      border: 1px solid var(--gray-200);
      border-radius: var(--border-radius);
      padding: 24px;
      margin-bottom: 24px;
      box-shadow: var(--shadow-sm);
    }
    
    .range-toggles {
      display: flex;
      gap: 4px;
      background: var(--gray-100);
      padding: 4px;
      border-radius: var(--border-radius-sm);
      margin-bottom: 16px;
    }
    
    .range-toggle {
      padding: 8px 16px;
      border: none;
      background: transparent;
      color: var(--gray-600);
      font-weight: 600;
      border-radius: 6px;
      cursor: pointer;
      transition: var(--transition);
      text-decoration: none;
      font-size: 14px;
    }
    
    .range-toggle.active {
      background: var(--primary);
      color: white;
      box-shadow: var(--shadow-sm);
    }
    
    .range-toggle:hover:not(.active) {
      background: var(--gray-200);
      color: var(--gray-700);
    }
    
    .export-buttons {
      display: flex;
      gap: 8px;
    }
    
    .reports-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
      gap: 24px;
    }
    
    .report-card {
      background: var(--gradient-card);
      border: 1px solid var(--gray-200);
      border-radius: var(--border-radius);
      box-shadow: var(--shadow-sm);
      overflow: hidden;
      transition: var(--transition);
    }
    
    .report-card:hover {
      box-shadow: var(--shadow-md);
      transform: translateY(-2px);
    }
    
    .report-card-header {
      background: var(--gradient-header);
      padding: 20px 24px;
      border-bottom: 1px solid var(--gray-200);
    }
    
    .report-card-title {
      font-size: 18px;
      font-weight: 700;
      color: var(--gray-900);
      margin: 0;
    }
    
    .report-card-body {
      padding: 24px;
    }
    
    .status-chips {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-bottom: 20px;
    }
    
    .status-chip {
      padding: 6px 12px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .chip-confirmed {
      background: #dcfce7;
      color: #166534;
    }
    
    .chip-completed {
      background: #dbeafe;
      color: #1e40af;
    }
    
    .chip-cancelled {
      background: #fee2e2;
      color: #991b1b;
    }
    
    .chip-noshow {
      background: #fef3c7;
      color: #92400e;
    }
    
    .chart-container {
      background: var(--gray-50);
      border-radius: var(--border-radius-sm);
      padding: 16px;
      margin: 16px 0;
      overflow-x: auto;
    }
    
    .revenue-highlight {
      background: var(--gradient-primary);
      color: white;
      padding: 16px;
      border-radius: var(--border-radius-sm);
      text-align: center;
      margin-top: 16px;
    }
    
    .revenue-amount {
      font-size: 24px;
      font-weight: 800;
      margin-bottom: 4px;
    }
    
    .revenue-label {
      font-size: 14px;
      opacity: 0.9;
    }
    
    .custom-range-form {
      background: white;
      border: 1px solid var(--gray-200);
      border-radius: var(--border-radius);
      padding: 20px;
      margin-bottom: 24px;
      box-shadow: var(--shadow-sm);
    }
    
    .range-inputs {
      display: flex;
      gap: 16px;
      align-items: end;
    }
    
    .range-inputs .field {
      min-width: 140px;
    }
    
    @media (max-width: 768px) {
      .range-inputs {
        flex-direction: column;
        align-items: stretch;
      }
      
      .reports-grid {
        grid-template-columns: 1fr;
      }
      
      .export-buttons {
        flex-direction: column;
        width: 100%;
      }
    }
  </style>
</head>
<body>
  <div class="main-content">
    <div class="page-header">
      <div>
        <h1>Reports & Analytics</h1>
        <p class="muted">Range: <?=htmlspecialchars($rangeStart)?> → <?=htmlspecialchars($rangeEnd)?></p>
      </div>

      <!-- Export buttons -->
      <div class="actions screen-only">
        <div class="export-group">
          <button class="btn compact" onclick="printReports()">Export (PDF)</button>
          <a class="btn compact" href="#" onclick="downloadCSV();return false;">Export CSV</a>
        </div>
      </div>
    </div>

    <section class="filters screen-only">
      <!-- Custom range always posts back with range=custom -->
      <form class="range" method="get" action="">
    <input type="hidden" name="module" value="clinic-manager">
    <input type="hidden" name="page" value="reports">
        <input type="hidden" name="range" value="custom">
        <label>From <input type="date" name="from" value="<?=htmlspecialchars($rangeStart)?>"></label>
        <label>To <input type="date" name="to" value="<?=htmlspecialchars($rangeEnd)?>"></label>
        <button class="btn compact apply" type="submit">Apply</button>
    <a class="btn compact ghost" href="?module=clinic-manager&page=reports&range=week">Clear</a>
      </form>
      
      <!-- Range toggles moved here on right -->
      <div class="toggle-group">
        <a class="btn compact toggle<?=isActive('week',$rangeMode)?>"
          href="?module=clinic-manager&page=reports&range=week">This Week</a>
        <a class="btn compact toggle<?=isActive('month',$rangeMode)?>"
          href="?module=clinic-manager&page=reports&range=month">This Month</a>
        <a class="btn compact toggle<?=isActive('year',$rangeMode)?>"
          href="?module=clinic-manager&page=reports&range=year">This Year</a>
      </div>
    </section>

    <section class="grid">
      <?php
        $titles = ['week'=>'Weekly','month'=>'Monthly','year'=>'Yearly','custom'=>'Custom'];
        $apptTitle = $titles[$rangeMode] ?? 'Weekly';

        $labelsSafe = $labels ?? [];
        $barsSafe   = $bars ?? [];
        $maxSafe    = max(1, $maxBar ?? 1);
        $barCount   = count($labelsSafe);
        
        // Calculate which bar represents today/this month for auto-centering
        $currentBarIndex = 0;
        if ($rangeMode === 'week') {
          // Find today's day of week (0=Monday in the week labels)
          $todayDayOfWeek = (int)date('N') - 1; // 0=Mon, 6=Sun
          $currentBarIndex = min($todayDayOfWeek, $barCount - 1);
        } elseif ($rangeMode === 'month') {
          // Find today's date
          $todayDay = (int)date('j'); // 1-31
          $currentBarIndex = min($todayDay - 1, $barCount - 1);
        } elseif ($rangeMode === 'year') {
          // Find current month (0=Jan, 11=Dec)
          $todayMonth = (int)date('n') - 1; // 0-11
          $currentBarIndex = min($todayMonth, $barCount - 1);
        }
        
        // For week mode with 7 or fewer bars, make them flexible to fill width
        $barFlex = ($barCount <= 7) ? '1 1 0' : '0 0 44px';
        
        // For PDF/print: only MONTH should hide the chart (too many columns)
        $printHideClass = ($rangeMode === 'month') ? 'print-hide-chart' : '';
      ?>
      <!-- ================= Appointments ================= -->
      <article class="card">
        <h3>Appointments (<?=htmlspecialchars($apptTitle)?>)</h3>

        <div class="chips">
          <span class="chip c1">Confirmed: <?=$apptStatus["Confirmed"] ?? 0?></span>
          <span class="chip c2">Completed: <?=$apptStatus["Completed"] ?? 0?></span>
          <span class="chip c3">Cancelled: <?=$apptStatus["Cancelled"] ?? 0?></span>
          <span class="chip c4">No-show: <?=$apptStatus["No-show"] ?? 0?></span>
        </div>

        <!-- Scroll wrapper – dashed box spans full width; inner flex can grow -->
        <div class="bars-scroll <?=$printHideClass?>" id="barsScroll" data-center-index="<?=$currentBarIndex?>" style="--bar-count: <?=$barCount?>; --bar-flex: <?=$barFlex?>">
          <div class="bars">
            <div class="bars-inner">
              <?php foreach ($barsSafe as $i => $val):
                $h = round(($val / $maxSafe) * 120); // px column height
                $lbl = $labelsSafe[$i] ?? '';
              ?>
                <div class="bar">
                  <?php if ($val > 0): ?>
                    <div class="value" style="bottom: <?=$h+6?>px;">LKR <?=number_format($val)?></div>
                  <?php endif; ?>
                  <div class="col" style="height:<?=$h?>px" title="<?=$lbl?>: LKR <?=number_format($val)?>"></div>
                  <div class="label"><?=htmlspecialchars($lbl)?></div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <?php if ($rangeMode === 'month'):
          // Print/PDF replacement for monthly chart: full-width calendar grid
          $incomeByDate = [];
          $monthlyTotal = 0.0;
          foreach ($barsSafe as $i => $val) {
            $dateKey = date('Y-m-d', strtotime($rangeStart . ' +' . $i . ' day'));
            $incomeByDate[$dateKey] = (float)$val;
            $monthlyTotal += (float)$val;
          }

          $monthStart = date('Y-m-01', strtotime($rangeStart));
          $daysInMonth = (int)date('t', strtotime($monthStart));
          $firstDow = (int)date('N', strtotime($monthStart)); // 1=Mon..7=Sun
          $leadingEmpty = $firstDow - 1;
          $trailingEmpty = (7 - (($leadingEmpty + $daysInMonth) % 7)) % 7;
        ?>
          <div class="print-only month-calendar" aria-label="Daily income calendar (monthly)">
            <div class="month-calendar-head">
              <div class="title">Daily Income (Calendar)</div>
              <div class="total">Total: LKR <?=number_format($monthlyTotal)?></div>
            </div>
            <table>
              <thead>
                <tr>
                  <th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th><th>Sun</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <?php for ($i = 0; $i < $leadingEmpty; $i++): ?>
                    <td class="empty"></td>
                  <?php endfor; ?>

                  <?php
                    $cell = $leadingEmpty;
                    for ($day = 1; $day <= $daysInMonth; $day++):
                      $curDate = date('Y-m-d', strtotime($monthStart . ' +' . ($day - 1) . ' day'));
                      $income = (float)($incomeByDate[$curDate] ?? 0);
                      $hasIncome = $income > 0.00001;
                  ?>
                      <td class="day<?= $hasIncome ? ' has-income' : '' ?>">
                        <div class="d"><?=$day?></div>
                        <div class="a">LKR <?=number_format($income)?></div>
                      </td>
                  <?php
                      $cell++;
                      if ($cell % 7 === 0 && $day !== $daysInMonth) {
                        echo "</tr><tr>";
                      }
                    endfor;
                  ?>

                  <?php for ($i = 0; $i < $trailingEmpty; $i++): ?>
                    <td class="empty"></td>
                  <?php endfor; ?>
                </tr>
              </tbody>
            </table>
          </div>
        <?php endif; ?>

        <div class="foot-note">
          Completed revenue in range: <strong>LKR <?=number_format($appointmentsRevenue)?></strong>
        </div>
      </article>

      <!-- ================= Vets workload ================= -->
      <article class="card">
        <h3>Veterinarians Workload</h3>
        <ul class="list">
          <?php foreach (($workload ?? []) as $vet => $count): ?>
            <li><span><?=htmlspecialchars($vet)?></span><b><?=intval($count)?></b></li>
          <?php endforeach; ?>
        </ul>
      </article>

      <!-- ================= Top products ================= -->
      <article class="card">
        <h3>Top Selling Products</h3>
        <ul class="list">
          <?php foreach (($productTotals ?? []) as $prod => $qty): ?>
            <li><span><?=htmlspecialchars($prod)?></span><b><?=intval($qty)?> units</b></li>
          <?php endforeach; ?>
        </ul>
        <div class="foot-note">Shop revenue in range: <strong>LKR <?=number_format($shopRevenue)?></strong></div>
      </article>

      <!-- ================= Finance ================= -->
      <article class="card">
        <h3>Financial Overview</h3>

        <div class="kpis two">
          <div class="kpi">
            <small>Clinic Income</small>
            <div class="num ok">LKR <?=number_format($appointmentsRevenue)?></div>
          </div>
          <div class="kpi">
            <small>Shop Income</small>
            <div class="num ok">LKR <?=number_format($shopRevenue)?></div>
          </div>
        </div>

        <div class="split">
          <!-- Screen donut (CSS conic-gradient) -->
          <div class="donut screen-only" style="--clinic: <?=intval($clinicPct)?>"></div>

          <!-- Print donut (SVG – reliable in PDFs) -->
          <svg class="donut-svg print-only" width="132" height="132" viewBox="0 0 44 44" aria-hidden="true">
            <defs>
              <linearGradient id="clinicGrad" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#0ea5e9"/>
                <stop offset="100%" stop-color="#6366f1"/>
              </linearGradient>
            </defs>
            <g transform="rotate(-90 22 22)">
              <!-- base ring -->
              <circle cx="22" cy="22" r="16" fill="none" stroke="#e5e7eb" stroke-width="8"></circle>
              <!-- clinic arc -->
              <circle cx="22" cy="22" r="16" fill="none" stroke="url(#clinicGrad)" stroke-width="8"
                      pathLength="100" stroke-dasharray="<?=intval($clinicPct)?> 100"></circle>
            </g>
            <!-- inner hole -->
            <circle cx="22" cy="22" r="12" fill="#fff" stroke="#eef2ff"/>
          </svg>

          <ul class="legend">
            <li><span class="dot c5"></span>Clinic: <b><?=intval($clinicPct)?>%</b>
              <small>(LKR <?=number_format($appointmentsRevenue)?>)</small></li>
            <li><span class="dot c6"></span>Shop: <b><?=intval(100 - $clinicPct)?>%</b>
              <small>(LKR <?=number_format($shopRevenue)?>)</small></li>
          </ul>
        </div>
      </article>
    </section>
  </div>

  <script>
    // Auto-scroll to center current day/month, but clamp to edges when near left/right.
    document.addEventListener('DOMContentLoaded', function() {
      const scrollContainer = document.getElementById('barsScroll');
      if (!scrollContainer) return;

      const centerIndex = parseInt(scrollContainer.getAttribute('data-center-index') || '0', 10);
      const bars = scrollContainer.querySelectorAll('.bar');

      if (bars.length <= 7) return; // week view (and any short series) should just fit
      const targetBar = bars[centerIndex];
      if (!targetBar) return;

      const containerRect = scrollContainer.getBoundingClientRect();
      const barRect = targetBar.getBoundingClientRect();
      const barCenterInContainer = (barRect.left - containerRect.left) + scrollContainer.scrollLeft + (barRect.width / 2);

      const targetScrollLeft = barCenterInContainer - (scrollContainer.clientWidth / 2);
      const maxScrollLeft = scrollContainer.scrollWidth - scrollContainer.clientWidth;

      scrollContainer.scrollLeft = Math.max(0, Math.min(maxScrollLeft, targetScrollLeft));
    });

    window.reportsData = {
      appointmentsRevenue: "<?=number_format($appointmentsRevenue,2,'.','')?>",
      shopRevenue: "<?=number_format($shopRevenue,2,'.','')?>"
    };
  </script>
  <script src="/PETVET/public/js/clinic-manager/reports.js"></script>
</body>
</html>
