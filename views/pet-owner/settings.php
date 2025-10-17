<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$module = 'pet-owner';
$GLOBALS['currentPage'] = 'settings.php';
$GLOBALS['module'] = 'pet-owner';
/** Pet Owner Settings (Profile & Preferences) */
// Provide safe defaults if controller didn't pass data.
$profile = isset($profile) ? $profile : [
	'name' => 'Your Name',
	'email' => 'you@example.com',
	'phone' => '',
	'address' => '',
	'city' => '',
	'postal_code' => '',
	'avatar' => 'https://placehold.co/200x200?text=Avatar',
	'date_joined' => date('Y-m-d'),
	'verified_email' => false,
	'verified_phone' => false
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
<main class="main-content">
		<div class="page-wrap">
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
									<label>Full Name
										<input type="text" name="name" value="<?= htmlspecialchars($profile['name']) ?>" required />
									</label>
									<label>Email
										<input type="email" name="email" value="<?= htmlspecialchars($profile['email']) ?>" required />
									</label>
								</div>
								<div class="row one">
									<label>Phone
										<input type="tel" name="phone" value="<?= htmlspecialchars($profile['phone']) ?>" />
									</label>
								</div>
								<div class="row one">
									<label>Bio / Notes
										<textarea name="bio" rows="3" placeholder="Tell us about you &amp; your pets"></textarea>
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
						<div class="row two">
							<label>Current Password
								<input type="password" name="current_password" autocomplete="current-password" />
							</label>
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
				<section class="card" id="section-role" data-section>
					<div class="card-head">
						<h2>Active Role</h2>
						<p class="muted small">Switch between your registered service provider roles</p>
					</div>
					<form id="formRole" class="form">
						<?php
						// Define available roles (in real app, this would come from user's registered roles)
						$availableRoles = [
							'pet-owner' => ['name' => 'Pet Owner', 'desc' => 'Manage your pets and appointments'],
							'trainer' => ['name' => 'Trainer', 'desc' => 'Provide training services'],
							'sitter' => ['name' => 'Pet Sitter', 'desc' => 'Offer pet sitting services'],
							'breeder' => ['name' => 'Breeder', 'desc' => 'Manage breeding operations'],
							'groomer' => ['name' => 'Groomer', 'desc' => 'Provide grooming services']
						];
						$currentRole = $_SESSION['current_role'] ?? 'pet-owner';
						?>
						<div class="role-options">
							<?php foreach ($availableRoles as $roleKey => $roleData): ?>
								<label class="role-option <?= $roleKey === $currentRole ? 'active' : '' ?>">
									<input type="radio" name="active_role" value="<?= $roleKey ?>" <?= $roleKey === $currentRole ? 'checked' : '' ?> />
									<div class="role-card">
										<div class="role-header">
											<span class="role-name"><?= htmlspecialchars($roleData['name']) ?></span>
											<?php if ($roleKey === $currentRole): ?>
												<span class="role-badge">Active</span>
											<?php endif; ?>
										</div>
										<p class="role-desc"><?= htmlspecialchars($roleData['desc']) ?></p>
									</div>
								</label>
							<?php endforeach; ?>
						</div>
						<div class="actions">
							<button class="btn primary" type="submit">Switch Role</button>
						</div>
					</form>
				</section>
			</div>
	</div>
</main>
<div id="toast" class="toast" role="status" aria-live="polite" aria-atomic="true"></div>
<script src="/PETVET/public/js/pet-owner/settings.js"></script>
</body>
</html>
