<?php
// DevTools debug page: verify trainer working areas storage + parsing
// Usage:
//   /PETVET/DevTools/test-working-areas.php?name=Kavindu%20Gune
//   /PETVET/DevTools/test-working-areas.php?user_id=123

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/PetOwner/ServicesModel.php';

header('Content-Type: text/html; charset=utf-8');

function h($v) {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function parse_service_areas($value) {
    if ($value === null) return [];
    $str = trim((string)$value);
    if ($str === '') return [];

    if (str_starts_with($str, '[')) {
        $decoded = json_decode($str, true);
        if (is_array($decoded)) {
            $areas = [];
            foreach ($decoded as $v) {
                $v = trim((string)$v);
                if ($v !== '') $areas[] = $v;
            }
            return $areas;
        }
    }

    $parts = array_map('trim', explode(',', $str));
    return array_values(array_filter($parts, fn($p) => $p !== ''));
}

$name = isset($_GET['name']) ? trim((string)$_GET['name']) : '';
$userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

$pdo = db();

$user = null;
if ($userId > 0) {
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, email FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} elseif ($name !== '') {
    // match by full name (case-insensitive)
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, email FROM users WHERE CONCAT(first_name, ' ', last_name) LIKE ? LIMIT 10");
    $stmt->execute(['%' . $name . '%']);
    $candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($candidates) === 1) {
        $user = $candidates[0];
    }
}

?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Working Areas Debug</title>
  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;padding:18px;background:#0b1220;color:#e5e7eb}
    a{color:#93c5fd}
    .card{background:#111a2e;border:1px solid rgba(255,255,255,.08);border-radius:12px;padding:14px;margin:12px 0}
    pre{background:#0b1220;border:1px solid rgba(255,255,255,.08);border-radius:10px;padding:12px;overflow:auto}
    code{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,monospace;font-size:13px}
    .muted{color:#9ca3af}
    .row{display:flex;gap:10px;flex-wrap:wrap}
    .pill{display:inline-flex;align-items:center;padding:3px 10px;border-radius:999px;background:#1f2a44;border:1px solid rgba(255,255,255,.12);font-size:12px}
    input{padding:10px;border-radius:10px;border:1px solid rgba(255,255,255,.12);background:#0b1220;color:#e5e7eb;min-width:260px}
    button{padding:10px 14px;border-radius:10px;border:1px solid rgba(255,255,255,.14);background:#1f2a44;color:#e5e7eb;cursor:pointer}
  </style>
</head>
<body>
  <h2>Trainer Working Areas Debug</h2>
  <p class="muted">Checks what is stored in <code>service_provider_profiles.service_area</code> and what <code>ServicesModel</code> returns.</p>

  <div class="card">
    <form method="get" class="row">
      <input name="name" placeholder="Search by trainer name (e.g., Kavindu Gune)" value="<?= h($name) ?>" />
      <input name="user_id" placeholder="or user_id" value="<?= $userId ? h($userId) : '' ?>" />
      <button type="submit">Search</button>
    </form>
    <p class="muted" style="margin-top:10px;">Tip: try <code>?name=Kavindu%20Gune</code></p>
  </div>

<?php
if ($name !== '' && empty($user) && isset($candidates) && count($candidates) > 1) {
    echo '<div class="card"><div class="muted">Multiple users matched. Pick a user_id:</div><div style="margin-top:8px">';
    foreach ($candidates as $c) {
        echo '<div class="pill">' . h($c['id']) . ' — ' . h($c['first_name'] . ' ' . $c['last_name']) . ' (' . h($c['email']) . ')</div> ';
    }
    echo '</div></div>';
}

if ($user) {
    $uid = (int)$user['id'];

    $stmt = $pdo->prepare("SELECT role_type, service_area, updated_at FROM service_provider_profiles WHERE user_id = ? AND role_type = 'trainer'");
    $stmt->execute([$uid]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    $raw = $profile['service_area'] ?? '';
    $parsed = parse_service_areas($raw);

    echo '<div class="card">';
    echo '<h3>User</h3>';
    echo '<div class="pill">id=' . h($uid) . '</div> ';
    echo '<div class="pill">name=' . h($user['first_name'] . ' ' . $user['last_name']) . '</div> ';
    echo '<div class="pill">email=' . h($user['email']) . '</div>';
    echo '</div>';

    echo '<div class="card">';
    echo '<h3>DB: service_provider_profiles (trainer)</h3>';
    echo '<div class="muted">Raw service_area:</div>';
    echo '<pre><code>' . h($raw) . '</code></pre>';
    echo '<div class="muted">Parsed areas:</div>';
    echo '<pre><code>' . h(json_encode($parsed, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . '</code></pre>';
    echo '</div>';

    // ServicesModel output (what the public page uses)
    $m = new ServicesModel();
    $providers = $m->getServiceProviders('trainers', []);
    $found = null;
    foreach ($providers as $p) {
        if ((int)($p['id'] ?? 0) === $uid) {
            $found = $p;
            break;
        }
    }

    echo '<div class="card">';
    echo '<h3>ServicesModel provider entry</h3>';
    if ($found) {
        echo '<div class="muted">Relevant fields:</div>';
        $slice = [
            'id' => $found['id'] ?? null,
            'name' => $found['name'] ?? null,
            'service_area' => $found['service_area'] ?? null,
            'service_areas' => $found['service_areas'] ?? null,
            'city' => $found['city'] ?? null,
        ];
        echo '<pre><code>' . h(json_encode($slice, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . '</code></pre>';
    } else {
        echo '<div class="muted">Trainer not found in ServicesModel output. They may be filtered out by public visibility rules.</div>';
    }
    echo '</div>';
}
?>

</body>
</html>
