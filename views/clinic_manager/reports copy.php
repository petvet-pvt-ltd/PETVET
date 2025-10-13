<?php
// View receives $data from the controller
extract($data); // exposes: $rangeStart, $rangeEnd, $apptStatus, $workload, $productTotals,
// $appointmentsRevenue, $shopRevenue, $grossRevenue, $netIncome,
// $labels, $bars, $maxBar, $clinicPct, $shopPct, $rangeMode
$rangeMode = $rangeMode ?? ($_GET['range'] ?? 'week'); // week|month|year|custom
function isActive($cur,$mode){ return $cur === $mode ? ' active' : ''; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Reports</title>
  <link rel="stylesheet" href="/PETVET/public/css/clinic-manager/reports.css">
  <style>
    .main-content { margin-left: 240px; padding: 24px; }
    @media (max-width: 768px){ .main-content { margin-left: 0; width: 100%; } }
  </style>
</head>
<body>
  <div class="main-content">
    <div class="page-header">
      <div>
        <h1>Reports & Analytics</h1>
        <p class="muted">Range: <?=htmlspecialchars($rangeStart)?> → <?=htmlspecialchars($rangeEnd)?></p>
      </div>

      <!-- Range toggles -->
      <div class="actions">
        <a class="btn toggle<?=isActive('week',$rangeMode)?>"
           href="?page=reports&range=week">This Week</a>
        <a class="btn toggle<?=isActive('month',$rangeMode)?>"
           href="?page=reports&range=month">This Month</a>
        <a class="btn toggle<?=isActive('year',$rangeMode)?>"
           href="?page=reports&range=year">This Year</a>

        <button class="btn primary" onclick="window.print()">Export (PDF)</button>
        <a class="btn" href="#" onclick="downloadCSV();return false;">Export CSV</a>
      </div>
    </div>

    <section class="filters">
      <!-- Custom range always posts back with range=custom -->
      <form class="range" method="get" action="">
        <input type="hidden" name="page" value="reports">
        <input type="hidden" name="range" value="custom">
        <label>From <input type="date" name="from" value="<?=htmlspecialchars($rangeStart)?>"></label>
        <label>To <input type="date" name="to" value="<?=htmlspecialchars($rangeEnd)?>"></label>
        <button class="btn apply" type="submit">Apply</button>
        <a class="btn ghost" href="?page=reports&range=week">Clear</a>
      </form>
    </section>

    <section class="grid">
      <?php
        // Map mode to title for header
        $mode = $rangeMode ?? ($mode ?? ($data['mode'] ?? 'week'));
        $titles = ['week'=>'Weekly','month'=>'Monthly','year'=>'Yearly','custom'=>'Custom'];
        $apptTitle = $titles[$mode] ?? 'Weekly';
        // Count bars to size the scroll container
        $barCount = isset($labels) ? count($labels) : 7;
      ?>
      <article class="card">
        <h3>Appointments (<?=htmlspecialchars($apptTitle)?>)</h3>
        <div class="chips">
          <span class="chip c1">Confirmed: <?=$apptStatus["Confirmed"] ?? 0?></span>
          <span class="chip c2">Completed: <?=$apptStatus["Completed"] ?? 0?></span>
          <span class="chip c3">Cancelled: <?=$apptStatus["Cancelled"] ?? 0?></span>
          <span class="chip c4">No-show: <?=$apptStatus["No-show"] ?? 0?></span>
        </div>
        <!-- NEW: horizontal scroll only for the chart area -->
        <div class="bars-scroll">
          <div class="bars" style="--bar-count: <?=$barCount?>">
            <?php
              $labelsSafe = $labels ?? [];
              $barsSafe   = $bars ?? [];
              $maxSafe    = max(1, $maxBar ?? 1);
              foreach ($barsSafe as $i => $val):
                $h = round(($val / $maxSafe) * 120); // px column height
                $lbl = $labelsSafe[$i] ?? '';
            ?>
              <div class="bar">
                <div class="value" style="bottom: <?=$h+6?>px;">LKR <?=number_format($val)?></div>
                <div class="col" style="height:<?=$h?>px" title="<?=$lbl?>: LKR <?=number_format($val)?>"></div>
                <div class="label"><?=htmlspecialchars($lbl)?></div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="foot-note">
          Completed revenue in range: <strong>LKR <?=number_format($appointmentsRevenue)?></strong>
        </div>
      </article>

      <!-- Vets workload -->
      <article class="card">
        <h3>Veterinarians Workload</h3>
        <ul class="list">
          <?php foreach (($workload ?? []) as $vet => $count): ?>
            <li><span><?=htmlspecialchars($vet)?></span><b><?=intval($count)?></b></li>
          <?php endforeach; ?>
        </ul>
      </article>

      <!-- Top selling products -->
      <article class="card">
        <h3>Top Selling Products</h3>
        <div class="bar-list">
          <?php
            $pt = $productTotals ?? [];
            $maxQty = max(1, ($pt ? max($pt) : 1));
            foreach ($pt as $prod => $qty):
              $w = round(($qty / $maxQty) * 100);
          ?>
            <div class="row">
              <span class="name"><?=htmlspecialchars($prod)?></span>
              <div class="track"><span class="fill" style="width:<?=$w?>%"></span></div>
              <b class="qty"><?=intval($qty)?></b>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="foot-note">Shop revenue in range: <strong>LKR <?=number_format($shopRevenue)?></strong></div>
      </article>

      <!-- Finance -->
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
          <div class="donut" style="--clinic: <?=intval($clinicPct)?>"></div>
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
    window.reportsData = {
      appointmentsRevenue: "<?=number_format($appointmentsRevenue,2,'.','')?>",
      shopRevenue: "<?=number_format($shopRevenue,2,'.','')?>"
    };
  </script>
  <script src="/PETVET/public/js/clinic-manager/reports.js"></script>
</body>
</html>
