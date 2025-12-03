<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$module = 'pet-owner';
$GLOBALS['currentPage'] = 'settings.php';
$GLOBALS['module'] = 'pet-owner';
/** Pet Owner Settings (Profile & Preferences) */
// Provide safe defaults if controller didn't pass data.
$profile = isset($profile) ? $profile : [
	'first_name' => 'Your',
	'last_name' => 'Name',
	'email' => 'you@example.com',
	'phone' => '',
	'address' => '',
	'avatar' => '/PETVET/public/images/emptyProfPic.png',
	'date_joined' => date('Y-m-d'),
	'email_verified' => false
];
$prefs = isset($prefs) ? $prefs : [
	'email_notifications' => true,
	'sms_notifications' => false,
	'reminder_appointments' => 24,
	'reminder_vaccinations' => 168,
	'newsletter_subscription' => true,
	'marketing_emails' => false,
	'language' => 'en',
	'timezone' => 'Asia/Colombo',
	'theme' => 'light',
	'privacy_profile' => 'public'
];
$accountStats = isset($accountStats) ? $accountStats : [
	'total_pets' => 0,
	'active_appointments' => 0,
	'total_medical_records' => 0,
	'account_age_days' => 0,
	'last_login' => date('Y-m-d H:i:s'),
	'profile_completion' => 50
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Settings - Pet Owner - PetVet</title>
<link rel="stylesheet" href="/PETVET/public/css/pet-owner/settings.css" />
</head>
<body>
<?php include __DIR__ . '/../shared/sidebar/sidebar.php'; ?>
<main class="main-content" style="padding-top: 0px !important;">
		<div class="settings-header">
			<div>
				<h1>Settings</h1>
				<p class="muted">Manage your profile &amp; preferences</p>
			</div>
			<nav class="quick-nav" aria-label="Quick navigation">
				<a href="#section-profile">Profile</a>
				<a href="#section-password">Password</a>
				<a href="#section-preferences">Preferences</a>
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
									<div class="image-preview-list avatar big" id="ownerAvatarPreview">
										<div class="image-preview-item">
											<img src="<?= htmlspecialchars($profile['avatar']) ?>" alt="avatar" />
										</div>
									</div>
									<div class="uploader-actions center">
										<input type="file" id="ownerAvatar" accept="image/*" hidden />
										<button type="button" class="btn outline" data-for="ownerAvatar">Change</button>
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
							<div class="row one">
								<label>Address
									<textarea name="address" rows="3" placeholder="Enter your full address"><?= htmlspecialchars($profile['address'] ?? '') ?></textarea>
								</label>
							</div>
								<div class="actions">
									<button class="btn primary" type="submit">Save Profile</button>
								</div>
							</div>
						</div>
					</form>
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

				<!-- Preferences Card -->
				<section class="card" id="section-preferences" data-section>
					<div class="card-head"><h2>Preferences</h2></div>
					<form id="formPrefs" class="form">
						<div class="pref-simplified">
							<div class="pref-row">
								<label class="toggle">
									<input type="checkbox" name="email_notifications" <?= $prefs['email_notifications'] ? 'checked' : '' ?> />
									<span class="toggle-track"><span class="toggle-handle"></span></span>
									<span class="toggle-label">Email Notifications <small>Appointment confirmations & updates</small></span>
								</label>
							</div>
							<div class="pref-row">
								<label class="select-group">Appointment Reminder
									<select name="reminder_appointments" class="slim-select" value="<?= (int)$prefs['reminder_appointments']; ?>">
										<option value="24" <?= ($prefs['reminder_appointments']==24?'selected':''); ?>>24 hours before</option>
										<option value="12" <?= ($prefs['reminder_appointments']==12?'selected':''); ?>>12 hours before</option>
										<option value="6" <?= ($prefs['reminder_appointments']==6?'selected':''); ?>>6 hours before</option>
										<option value="1" <?= ($prefs['reminder_appointments']==1?'selected':''); ?>>1 hour before</option>
									</select>
								</label>
							</div>
						</div>
						<div class="actions">
							<button class="btn primary" type="submit">Save Preferences</button>
						</div>
					</form>
				</section>

				<!-- Role Management Card -->
				<?php include __DIR__ . '/../shared/components/role-switcher.php'; ?>
			</div>
	</div>
</main>
<div id="toast" class="toast" role="status" aria-live="polite" aria-atomic="true"></div>
<script src="/PETVET/public/js/pet-owner/settings.js"></script>
</body>
</html>
