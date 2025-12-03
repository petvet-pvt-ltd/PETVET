<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$module = 'clinic_manager';
$GLOBALS['currentPage'] = 'settings.php';
$GLOBALS['module'] = 'clinic_manager';

/** Clinic Manager Settings (Profile & Preferences) */
// Data is provided by controller
if (!isset($profile) || !isset($clinic) || !isset($prefs)) {
    die('Settings data not loaded');
}

// Ensure weeklySchedule and blockedDays are set
$weeklySchedule = $weeklySchedule ?? [];
$blockedDays = $blockedDays ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Settings - Clinic Manager - PetVet</title>
<link rel="stylesheet" href="/PETVET/public/css/pet-owner/settings.css" />
<link rel="stylesheet" href="/PETVET/public/css/clinic-manager/settings.css" />
</head>
<body>
<?php include __DIR__ . '/../shared/sidebar/sidebar.php'; ?>
<main class="main-content" style="padding-top: 0px !important;">
		<div class="settings-header">
			<div>
				<h1>Settings</h1>
				<p class="muted">Manage your profile, clinic details &amp; preferences</p>
			</div>
			<nav class="quick-nav" aria-label="Quick navigation">
				<a href="#section-profile">Profile</a>
				<a href="#section-clinic">Clinic</a>
				<a href="#section-preferences">Preferences</a>
				<a href="#section-availability">Availability</a>
				<a href="#section-password">Password</a>
			</nav>
		</div>
		<div class="page-wrap">
			<div class="settings-grid">
				<!-- Profile Card -->
				<section class="card" id="section-profile" data-section>
					<div class="card-head"><h2>Profile</h2></div>
					<form id="formProfile" class="form" enctype="multipart/form-data">
						<div class="profile-grid">
							<div class="profile-left">
								<div class="avatar-frame">
									<div class="image-preview-list avatar big" id="managerAvatarPreview">
										<div class="image-preview-item">
											<img src="<?= htmlspecialchars($profile['avatar']) ?>" alt="avatar" />
										</div>
									</div>
									<div class="uploader-actions center">
										<input type="file" id="managerAvatar" accept="image/*" hidden />
										<button type="button" class="btn outline" data-for="managerAvatar">Change</button>
									</div>
								</div>
							</div>
							<div class="profile-right">
								<div class="row two">
									<label>First Name
										<input type="text" name="first_name" value="<?= htmlspecialchars($profile['first_name'] ?? '') ?>" required />
									</label>
									<label>Last Name
										<input type="text" name="last_name" value="<?= htmlspecialchars($profile['last_name'] ?? '') ?>" required />
									</label>
								</div>
							<div class="row one">
								<label>Email (Login Username)
									<input type="email" name="email" value="<?= htmlspecialchars($profile['email']) ?>" readonly style="background: #f0f0f0; cursor: not-allowed;" />
								</label>
							</div>
							<div class="row one">
								<label>Phone
									<input type="tel" name="phone" id="phoneInput" value="<?= htmlspecialchars($profile['phone'] ?? '') ?>" placeholder="07XXXXXXXX" pattern="07[0-9]{8}" title="Phone number must be 10 digits starting with 07" />
									<span id="phoneError" style="color: #ef4444; font-size: 12px; margin-top: 4px; display: none;"></span>
								</label>
							</div>
								<div class="actions">
									<button class="btn primary" type="submit">Save Profile</button>
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

            <div class="row one">
              <label>Clinic Name
                <input type="text" name="name" value="<?=htmlspecialchars($clinic['name'])?>" required />
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
            <div class="row one">
              <label>Email
                <input type="email" name="email" value="<?=htmlspecialchars($clinic['email'])?>" />
              </label>
            </div>

            <div class="actions">
              <button class="btn primary" type="submit">Save Changes</button>
            </div>
          </form>
        </section>

				<!-- Preferences Card -->
				<section class="card" id="section-preferences" data-section>
					<div class="card-head"><h2>Preferences</h2></div>
					<form id="formPrefs" class="form">
						<div class="prefs-container">
							<div class="pref-item">
								<div class="pref-info">
									<div class="pref-title">Email Notifications</div>
									<div class="pref-subtitle">Pending requests and shop updates</div>
								</div>
								<label class="toggle-switch">
									<input type="checkbox" name="email_notifications" <?= $prefs['email_notifications'] ? 'checked' : '' ?> />
									<span class="toggle-slider"></span>
								</label>
							</div>

							<div class="pref-item">
								<div class="pref-info">
									<div class="pref-title">Slot Time (minutes)</div>
									<div class="pref-subtitle">Duration for each appointment slot</div>
								</div>
								<input type="number" name="slot_time" value="<?= $prefs['slot_duration_minutes'] ?? 20 ?>" min="5" max="120" step="5" class="slot-input" />
							</div>
						</div>

						<div class="actions">
							<button class="btn btn-primary" type="submit">Save Preferences</button>
						</div>
					</form>
				</section>

        <!-- Availability / Weekly Schedule -->
        <section class="card" id="section-availability" data-section>
          <div class="card-head"><h2>Weekly Schedule</h2></div>
          <form id="formWeeklySchedule" class="form">
            <p class="muted" style="margin-top:0">Configure your availability for each day of the week</p>
            
            <?php foreach ($weeklySchedule as $day): 
            ?>
            <div class="schedule-day <?= $day['active'] ? 'active' : '' ?>" data-day="<?= $day['id'] ?>">
              <div class="day-label"><?= $day['label'] ?></div>
              
              <label class="toggle-switch">
                <input 
                  type="checkbox" 
                  name="<?= $day['id'] ?>_enabled"
                  <?= $day['active'] ? 'checked' : '' ?>
                />
                <span class="toggle-slider"></span>
              </label>
              
              <div class="time-inputs">
                <label>
                  Start
                  <input 
                    type="time" 
                    name="<?= $day['id'] ?>_start" 
                    value="<?= $day['start'] ?>"
                    <?= !$day['active'] ? 'disabled' : '' ?>
                  />
                </label>
                <label>
                  End
                  <input 
                    type="time" 
                    name="<?= $day['id'] ?>_end" 
                    value="<?= $day['end'] ?>"
                    <?= !$day['active'] ? 'disabled' : '' ?>
                  />
                </label>
              </div>
            </div>
            <?php endforeach; ?>

            <div class="actions">
              <button class="btn btn-light" type="reset">Reset</button>
              <button class="btn btn-primary" type="submit">Save Schedule</button>
            </div>
          </form>
        </section>

        <!-- Blocked Days -->
        <section class="card" id="section-blocked-days" data-section>
          <div class="card-head"><h2>Blocked Days</h2></div>
          <div class="form">
            <p class="muted" style="margin-top:0">Block specific dates for holidays, vacations, or personal reasons</p>
            
            <div class="blocked-days-header">
              <input type="date" id="newBlockedDate" placeholder="Select date" />
              <input type="text" id="newBlockedReason" placeholder="Reason (e.g., Holiday, Vacation)" />
              <button type="button" class="btn btn-primary" id="btnAddBlockedDay">Add Date</button>
            </div>

            <div class="blocked-days-list" id="blockedDaysList">
              <?php if (empty($blockedDays)): ?>
                <div class="empty-state">No blocked days yet. Add dates you won't be available.</div>
              <?php else:
                foreach ($blockedDays as $blocked): 
                  $dateObj = new DateTime($blocked['blocked_date']);
                  $displayDate = $dateObj->format('M j, Y');
              ?>
                <div class="blocked-day-item" data-date="<?= htmlspecialchars($blocked['blocked_date']) ?>">
                  <div class="blocked-date"><?= htmlspecialchars($displayDate) ?></div>
                  <div class="blocked-reason"><?= htmlspecialchars($blocked['reason'] ?? 'Holiday') ?></div>
                  <button type="button" class="btn-remove" data-action="remove">Remove</button>
                </div>
              <?php endforeach; 
              endif; ?>
            </div>

            <div class="actions" style="margin-top:var(--space-4)">
              <button class="btn btn-primary" type="button" id="btnSaveBlockedDays">Save Blocked Days</button>
            </div>
          </div>
        </section>

				<!-- Password Card -->
				<section class="card" id="section-password" data-section>
					<div class="card-head"><h2>Change Password</h2></div>
					<form id="formPassword" class="form">
						<div class="row one">
							<label>Current Password
								<input type="password" name="current_password" autocomplete="current-password" />
							</label>
						</div>
						<div class="row one">
							<label>New Password
								<input type="password" name="new_password" autocomplete="new-password" />
							</label>
						</div>
						<div class="row one">
							<label>Confirm New Password
								<input type="password" name="confirm_password" />
							</label>
						</div>
						<div class="actions">
							<button class="btn primary" type="submit">Update Password</button>
						</div>
					</form>
				</section>

			</div>
	</div>
</main>
<div id="toast" class="toast" role="status" aria-live="polite" aria-atomic="true"></div>
<script src="/PETVET/public/js/pet-owner/settings.js"></script>
<script src="/PETVET/public/js/clinic-manager/settings.js"></script>
</body>
</html>
