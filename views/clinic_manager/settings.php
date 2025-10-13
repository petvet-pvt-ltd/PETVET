<?php
// ==== Simulated DB data (arrays) ====
$manager = [
  'name' => 'Kasun Perera',
  'email' => 'kasun.perera@petvet.lk',
  'phone' => '+94 77 123 4567',
  'avatar' => 'https://media.istockphoto.com/photos/headshot-portrait-of-smiling-ethnic-businessman-in-office-picture-id1300512215?b=1&k=20&m=1300512215&s=170667a&w=0&h=LsZL_-vvAHB2A2sNLHu9Vpoib_3aLLkRamveVW3AGeQ='
];

$clinic = [
  'name' => 'PetVet Animal Clinic',
  'reg_no' => 'PV-CLN-2021-045',
  'logo' => 'https://static.vecteezy.com/system/resources/previews/005/601/780/non_2x/veterinary-clinic-logo-vector.jpg',

  'cover' => 'https://img.freepik.com/free-vector/veterinary-clinic-social-media-cover-template_23-2149716789.jpg?w=1060&t=st=1704177217~exp=1704177817~hmac=197572bb68251ba0fc40f21b0c57a1e6099f7fa4dedc067b41e398190d17b804',

  'description' => 'Trusted pet healthcare and wellness. Experienced vets, modern facilities, and friendly service.',
  'address' => '123 Main St, Colombo 03',
  'map_pin' => '6.9271, 79.8612',
  'phone' => '+94 11 234 5678',
  'email' => 'contact@petvet.lk',
  'website' => 'https://petvet.lk',
  'facebook' => 'https://facebook.com/petvetlk',
  'instagram' => 'https://instagram.com/petvetlk'
];

$hours = [
  ['day' => 'Monday',    'open' => true,  'start' => '09:00', 'end' => '18:00'],
  ['day' => 'Tuesday',   'open' => true,  'start' => '09:00', 'end' => '18:00'],
  ['day' => 'Wednesday', 'open' => true,  'start' => '09:00', 'end' => '18:00'],
  ['day' => 'Thursday',  'open' => true,  'start' => '09:00', 'end' => '18:00'],
  ['day' => 'Friday',    'open' => true,  'start' => '09:00', 'end' => '18:00'],
  ['day' => 'Saturday',  'open' => true,  'start' => '10:00', 'end' => '16:00'],
  ['day' => 'Sunday',    'open' => false, 'start' => '09:00', 'end' => '13:00'],
];

$holidays = ['2025-12-25', '2026-01-01'];

$cfgPath = __DIR__ . '/../../config/clinic_manager.php';
$cfg = file_exists($cfgPath) ? require $cfgPath : ['slot_duration_minutes' => 20];
$policies = [
  'slot_length' => (int)($cfg['slot_duration_minutes'] ?? 20), // minutes
  'lead_time_hours' => 2,
  'cancellation_policy' => "Please cancel appointments at least 2 hours in advance to avoid fees.",
  'no_show_policy' => "No-shows may incur a standard charge and require prepayment for future bookings."
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Settings | Clinic Manager</title>
  <link rel="stylesheet" href="/PETVET/public/css/clinic-manager/enhanced-global.css" />
  <link rel="stylesheet" href="/PETVET/public/css/clinic-manager/settings.css" />
</head>
<body>
  <div class="main-content">
    <div class="page-wrap">
      <div class="settings-header">
        <div>
          <h1>Settings</h1>
          <p class="muted">Manage your profile, clinic details, and policies</p>
        </div>
        <nav class="quick-nav" aria-label="Quick navigation">
          <a href="#section-manager">Manager</a>
          <a href="#section-clinic">Clinic</a>
          <a href="#section-hours">Hours &amp; Policies</a>
        </nav>
      </div>

      <div class="settings-grid">

        <!-- ============== Manager Profile (Avatar left, form right) ============== -->
        <section class="card" id="section-manager">
          <div class="card-head"><h2>Manager Profile</h2></div>
          <form id="formManager" class="form" enctype="multipart/form-data">
            <div class="profile-grid">
              <!-- Left: BIG circular avatar -->
              <div class="profile-left">
                <div class="avatar-frame">
                  <div class="image-preview-list avatar big" id="mgrAvatarPreview">
                    <div class="image-preview-item"><img src="<?=htmlspecialchars($manager['avatar'])?>" alt="avatar"></div>
                  </div>
                  <div class="uploader-actions center">
                    <input type="file" id="mgrAvatar" accept="image/*" hidden />
                    <button type="button" class="btn btn-light" data-for="mgrAvatar">Change</button>
                  </div>
                </div>
              </div>

              <!-- Right: fields -->
              <div class="profile-right">
                <div class="row two">
                  <label>Full Name
                    <input type="text" name="name" value="<?=htmlspecialchars($manager['name'])?>" required />
                  </label>
                  <label>Email
                    <input type="email" name="email" value="<?=htmlspecialchars($manager['email'])?>" required />
                  </label>
                </div>
                <div class="row one">
                  <label>Phone
                    <input type="tel" name="phone" value="<?=htmlspecialchars($manager['phone'])?>" />
                  </label>
                </div>

                <fieldset class="fieldset">
                  <legend>Change Password</legend>
                  <div class="row two">
                    <label>Current Password
                      <input type="password" name="current_password" />
                    </label>
                    <label>New Password
                      <input type="password" name="new_password" />
                    </label>
                  </div>
                  <div class="row one">
                    <label>Confirm New Password
                      <input type="password" name="confirm_password" />
                    </label>
                  </div>
                </fieldset>

                <div class="actions">
                  <button class="btn btn-primary" type="submit">Save Changes</button>
                </div>
              </div>
            </div>
          </form>
        </section>

        <!-- ============== Clinic Profile (Facebook-style cover + logo) ============== -->
        <section class="card" id="section-clinic">
          <div class="card-head"><h2>Clinic Profile</h2></div>
          <form id="formClinic" class="form" enctype="multipart/form-data">

            <!-- Hero block -->
            <div class="brand-hero">
              <div class="cover-wrap">
                <div class="image-preview-list cover hero-cover" id="clinicCoverPreview">
                  <div class="image-preview-item hero"><img src="<?=htmlspecialchars($clinic['cover'])?>" alt="cover"></div>
                </div>
                <input type="file" id="clinicCover" accept="image/*" hidden />
                <button type="button" class="btn btn-light btn-sm change-cover" data-for="clinicCover">Change cover</button>
              </div>

              <div class="logo-wrap">
                <div class="image-preview-list logo hero-logo" id="clinicLogoPreview">
                  <div class="image-preview-item"><img src="<?=htmlspecialchars($clinic['logo'])?>" alt="logo"></div>
                </div>
                <input type="file" id="clinicLogo" accept="image/*" hidden />
                <button type="button" class="btn btn-light btn-sm change-logo" data-for="clinicLogo">Change logo</button>
              </div>
            </div>

            <div class="row one">
              <p class="hero-title"><?=$clinic['name']?></p>
              <p class="hero-sub"><?=$clinic['description']?></p>
            </div>

            <div class="row two">
              <label>Clinic Name
                <input type="text" name="name" value="<?=htmlspecialchars($clinic['name'])?>" required />
              </label>
              <label>Registration No.
                <input type="text" name="reg_no" value="<?=htmlspecialchars($clinic['reg_no'])?>" />
              </label>
            </div>

            <div class="row one">
              <label>Short Description
                <textarea name="description" rows="3" required><?=htmlspecialchars($clinic['description'])?></textarea>
              </label>
            </div>

            <div class="row one">
              <label>Address
                <input type="text" name="address" value="<?=htmlspecialchars($clinic['address'])?>" />
              </label>
            </div>
            <div class="row two">
              <label>Google Map Pin / Plus Code
                <input type="text" name="map_pin" value="<?=htmlspecialchars($clinic['map_pin'])?>" />
              </label>
              <label>Phone
                <input type="tel" name="phone" value="<?=htmlspecialchars($clinic['phone'])?>" />
              </label>
            </div>
            <div class="row two">
              <label>Email
                <input type="email" name="email" value="<?=htmlspecialchars($clinic['email'])?>" />
              </label>
              <label>Website
                <input type="url" name="website" value="<?=htmlspecialchars($clinic['website'])?>" />
              </label>
            </div>
            <div class="row two">
              <label>Facebook
                <input type="url" name="facebook" value="<?=htmlspecialchars($clinic['facebook'])?>" />
              </label>
              <label>Instagram
                <input type="url" name="instagram" value="<?=htmlspecialchars($clinic['instagram'])?>" />
              </label>
            </div>

            <div class="actions">
              <button class="btn btn-primary" type="submit">Save Changes</button>
            </div>
          </form>
        </section>

        <!-- ============== Business Hours & Policies ============== -->
        <section class="card" id="section-hours">
          <div class="card-head"><h2>Business Hours &amp; Policies</h2></div>
          <form id="formHours" class="form">
            <div class="hours-list">
              <?php foreach ($hours as $i => $d): ?>
              <div class="hours-row" data-i="<?=$i?>">
                <span class="day"><?=htmlspecialchars($d['day'])?></span>
                <label class="switch">
                  <input type="checkbox" class="open-toggle" <?=$d['open'] ? 'checked' : ''?> />
                  <span>Open</span>
                </label>
                <div class="time-group">
                  <input type="time" class="time-start" value="<?=htmlspecialchars($d['start'])?>" <?=$d['open']? '' : 'disabled'?> />
                  <span class="sep">to</span>
                  <input type="time" class="time-end" value="<?=htmlspecialchars($d['end'])?>" <?=$d['open']? '' : 'disabled'?> />
                </div>
              </div>
              <?php endforeach; ?>
            </div>

            <div class="row one">
              <label>Holidays / Closures
                <div class="holiday-list" id="holidayList">
                  <?php foreach ($holidays as $h): ?>
                    <div class="holiday-item">
                      <input type="date" value="<?=htmlspecialchars($h)?>" />
                      <button type="button" class="icon-btn remove" aria-label="Remove holiday" title="Remove">×</button>
                    </div>
                  <?php endforeach; ?>
                </div>
                <div class="add-holiday">
                  <input type="date" id="newHoliday" />
                  <button type="button" class="btn" id="btnAddHoliday">Add Holiday</button>
                </div>
              </label>
            </div>

            <div class="row two">
              <label>Appointment Slot Length (minutes)
                <select id="slotLength">
                  <?php foreach ([10,15,20,30,45,60] as $opt): ?>
                    <option value="<?=$opt?>" <?=$opt==$policies['slot_length']?'selected':''?>><?=$opt?></option>
                  <?php endforeach; ?>
                </select>
                <small class="muted">This is applied across Overview and Appointments. Current default: <?=$policies['slot_length']?> minutes.</small>
              </label>
              <label>Lead Time (hours)
                <select id="leadTime">
                  <?php foreach ([1,2,3,4,6,12,24] as $opt): ?>
                    <option value="<?=$opt?>" <?=$opt==$policies['lead_time_hours']?'selected':''?>><?=$opt?></option>
                  <?php endforeach; ?>
                </select>
              </label>
            </div>

            <div class="row one">
              <label>Cancellation Policy
                <textarea id="cancelPolicy" rows="3"><?=htmlspecialchars($policies['cancellation_policy'])?></textarea>
              </label>
            </div>
            <div class="row one">
              <label>No-show Policy
                <textarea id="noShowPolicy" rows="3"><?=htmlspecialchars($policies['no_show_policy'])?></textarea>
              </label>
            </div>

            <div class="actions">
              <button class="btn btn-primary" type="submit">Save Changes</button>
            </div>
          </form>
        </section>

      </div>
    </div>
  </div>

  <div id="toast" class="toast" role="status" aria-live="polite" aria-atomic="true"></div>
  <script src="/PETVET/public/js/clinic-manager/settings.js"></script>
</body>
</html>
