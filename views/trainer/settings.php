<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$module = 'trainer';
$GLOBALS['currentPage'] = 'settings.php';
$GLOBALS['module'] = 'trainer';
/** Trainer Settings (Profile & Preferences) */
$profile = isset($profile) ? $profile : [
	'name' => 'Your Name',
	'email' => 'you@example.com',
	'phone' => '',
	'address' => '',
	'city' => '',
	'postal_code' => '',
	'avatar' => 'https://placehold.co/200x200?text=Avatar',
	'specialization' => '',
	'experience_years' => 0,
	'certifications' => '',
	'date_joined' => date('Y-m-d'),
	'verified_email' => false,
	'verified_phone' => false
];
$prefs = isset($prefs) ? $prefs : [
	'email_notifications' => true,
	'sms_notifications' => false,
	'session_reminders' => 24,
	'newsletter_subscription' => true,
	'marketing_emails' => false,
	'language' => 'en',
	'timezone' => 'Asia/Colombo',
	'theme' => 'light',
	'availability' => 'Mon-Fri 9AM-6PM'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Settings - Trainer - PetVet</title>
<link rel="stylesheet" href="/PETVET/public/css/trainer/settings.css" />
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
				<a href="#section-role">Role</a>
			</nav>
		</div>

		<div class="settings-grid">
			<!-- Profile Card -->
			<section class="card" id="section-profile" data-section>
				<div class="card-head"><h2>Profile</h2></div>
				<form id="formProfile" class="form" enctype="multipart/form-data">
					<div class="cover-photo-section">
						<div class="cover-photo-preview" id="coverPhotoPreview">
							<img src="<?= htmlspecialchars($profile['cover_photo'] ?? 'https://placehold.co/1200x300?text=Cover+Photo') ?>" alt="Cover Photo" />
							<div class="cover-photo-overlay">
								<button type="button" class="btn outline small" data-for="coverPhoto">Change Cover Photo</button>
							</div>
						</div>
						<input type="file" id="coverPhoto" accept="image/*" hidden />
					</div>
					<div class="profile-grid">
						<div class="profile-left">
							<div class="avatar-frame">
								<div class="image-preview-list avatar big" id="trainerAvatarPreview">
									<div class="image-preview-item">
										<img src="<?= htmlspecialchars($profile['avatar']) ?>" alt="avatar" />
									</div>
								</div>
								<div class="uploader-actions center">
									<input type="file" id="trainerAvatar" accept="image/*" hidden />
									<button type="button" class="btn outline" data-for="trainerAvatar">Change</button>
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
							<div class="row two">
								<label>Phone
									<input type="tel" name="phone" value="<?= htmlspecialchars($profile['phone']) ?>" />
								</label>
								<label>Specialization
									<input type="text" name="specialization" value="<?= htmlspecialchars($profile['specialization']) ?>" />
								</label>
							</div>
							<div class="row two">
								<label>Experience (Years)
									<input type="number" name="experience_years" value="<?= htmlspecialchars($profile['experience_years']) ?>" min="0" />
								</label>
								<label>Certifications
									<input type="text" name="certifications" value="<?= htmlspecialchars($profile['certifications']) ?>" />
								</label>
							</div>
							<label>Address
								<input type="text" name="address" value="<?= htmlspecialchars($profile['address']) ?>" />
							</label>
							<div class="row two">
								<label>City
									<input type="text" name="city" value="<?= htmlspecialchars($profile['city']) ?>" />
								</label>
								<label>Postal Code
									<input type="text" name="postal_code" value="<?= htmlspecialchars($profile['postal_code']) ?>" />
								</label>
							</div>
						</div>
					</div>
					<div class="actions">
						<button class="btn primary" type="submit">Save Profile</button>
					</div>
				</form>
			</section>

			<!-- Password Card -->
			<section class="card" id="section-password" data-section>
				<div class="card-head">
					<h2>Change Password</h2>
					<p class="muted small">Use a strong password (min 8 characters)</p>
				</div>
				<form id="formPassword" class="form">
					<label>Current Password
						<input type="password" name="current_password" required />
					</label>
					<label>New Password
						<input type="password" name="new_password" required minlength="6" />
					</label>
					<label>Confirm New Password
						<input type="password" name="confirm_password" required minlength="6" />
					</label>
					<div class="actions">
						<button class="btn primary" type="submit">Update Password</button>
					</div>
				</form>
			</section>

			<!-- Preferences Card -->
			<section class="card" id="section-preferences" data-section>
				<div class="card-head">
					<h2>Preferences</h2>
					<p class="muted small">Customize notifications &amp; pricing</p>
				</div>
				<form id="formPrefs" class="form">
					<div class="pref-simplified">
						<div class="pref-row">
							<label class="toggle">
								<input type="checkbox" name="email_notifications" <?= $prefs['email_notifications'] ? 'checked' : '' ?> />
								<span class="toggle-track"><span class="toggle-handle"></span></span>
								<span class="toggle-label">Email Notifications <small>Receive email alerts for new bookings</small></span>
							</label>
						</div>
						<div class="pref-row">
							<label class="select-group">Session Reminders
								<select name="session_reminders" value="<?= $prefs['session_reminders'] ?>">
									<option value="24" <?= $prefs['session_reminders'] == 24 ? 'selected' : '' ?>>24 hours before</option>
									<option value="48" <?= $prefs['session_reminders'] == 48 ? 'selected' : '' ?>>48 hours before</option>
									<option value="168" <?= $prefs['session_reminders'] == 168 ? 'selected' : '' ?>>1 week before</option>
								</select>
							</label>
						</div>
					</div>
					<hr style="margin: 1.5rem 0; border: 0; border-top: 1px solid #e5e5e5;">
					<h3 style="margin-bottom: 1rem; font-size: 1.1rem;">Hourly Training Rates (LKR)</h3>
					<label>Basic Training
						<input type="number" name="rate_basic" value="<?= $prefs['rate_basic'] ?? 2000 ?>" min="0" step="100" placeholder="e.g., 2000" />
						<small class="field-hint">Per hour rate for basic obedience training</small>
					</label>
					<label>Intermediate Training
						<input type="number" name="rate_intermediate" value="<?= $prefs['rate_intermediate'] ?? 3500 ?>" min="0" step="100" placeholder="e.g., 3500" />
						<small class="field-hint">Per hour rate for intermediate training</small>
					</label>
					<label>Advanced Training
						<input type="number" name="rate_advanced" value="<?= $prefs['rate_advanced'] ?? 5000 ?>" min="0" step="100" placeholder="e.g., 5000" />
						<small class="field-hint">Per hour rate for advanced/specialized training</small>
					</label>
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
<div id="toast" class="toast"></div>
<script src="/PETVET/public/js/trainer/settings.js"></script>
</body>
</html>
