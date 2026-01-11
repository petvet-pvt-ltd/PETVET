<?php
// DevTools: Debug city filter - see what getCities returns vs what trainers have
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/PetOwner/ServicesModel.php';

header('Content-Type: text/html; charset=utf-8');

$pdo = db();
$model = new ServicesModel();

// Get cities from the model
$cities = $model->getCities('trainers');

// Get all trainer service_areas directly from DB
$stmt = $pdo->prepare("
    SELECT 
        u.id,
        CONCAT(u.first_name, ' ', u.last_name) as name,
        spp.service_area,
        ur.verification_status,
        ur.is_active
    FROM users u
    INNER JOIN service_provider_profiles spp ON u.id = spp.user_id
    INNER JOIN user_roles ur ON u.id = ur.user_id
    INNER JOIN roles r ON ur.role_id = r.id
    WHERE r.role_name = 'trainer' 
    AND spp.role_type = 'trainer'
    ORDER BY u.first_name
");
$stmt->execute();
$trainers = $stmt->fetchAll(PDO::FETCH_ASSOC);

?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>City Filter Debug</title>
  <style>
    body{font-family:system-ui,sans-serif;padding:18px;background:#0b1220;color:#e5e7eb;line-height:1.6}
    .card{background:#111a2e;border:1px solid rgba(255,255,255,.08);border-radius:12px;padding:16px;margin:16px 0}
    h2,h3{margin-top:0}
    pre{background:#0b1220;border:1px solid rgba(255,255,255,.08);border-radius:8px;padding:12px;overflow:auto;font-size:13px}
    .pill{display:inline-block;padding:4px 10px;margin:3px;border-radius:999px;background:#1f2a44;border:1px solid rgba(255,255,255,.12);font-size:12px}
    .status-approved{background:#065f46;border-color:#059669}
    .status-pending{background:#7c2d12;border-color:#ea580c}
    table{width:100%;border-collapse:collapse;margin-top:10px}
    th,td{text-align:left;padding:8px;border-bottom:1px solid rgba(255,255,255,.08)}
    th{background:#1f2a44;font-weight:600}
  </style>
</head>
<body>
  <h2>City Filter Debug</h2>

  <div class="card">
    <h3>Cities returned by getCities('trainers')</h3>
    <p>Count: <strong><?= count($cities) ?></strong></p>
    <div>
      <?php foreach ($cities as $city): ?>
        <span class="pill"><?= htmlspecialchars($city) ?></span>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="card">
    <h3>All Trainers in Database</h3>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Status</th>
          <th>Active</th>
          <th>service_area (raw)</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($trainers as $t): ?>
          <tr>
            <td><?= htmlspecialchars($t['id']) ?></td>
            <td><?= htmlspecialchars($t['name']) ?></td>
            <td><span class="pill status-<?= htmlspecialchars($t['verification_status']) ?>"><?= htmlspecialchars($t['verification_status']) ?></span></td>
            <td><?= $t['is_active'] ? '✅' : '❌' ?></td>
            <td><pre><?= htmlspecialchars($t['service_area'] ?? 'NULL') ?></pre></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="card">
    <h3>Query Logic</h3>
    <p><code>getCities()</code> extracts all districts from <code>service_provider_profiles.service_area</code> where <code>role_type = 'trainer'</code>.</p>
    <p>It includes <strong>ALL trainers</strong> (even unapproved/inactive), but the public services page only shows approved + active trainers.</p>
    <p><strong>Solution:</strong> Filter getCities to only include approved + active trainers so dropdown matches visible providers.</p>
  </div>

</body>
</html>
