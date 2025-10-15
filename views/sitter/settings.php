<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$module = 'sitter';
$GLOBALS['currentPage'] = 'settings.php';
$GLOBALS['module'] = 'sitter';
/** Sitter Settings (Profile & Preferences) */
$profile = isset($profile) ? $profile : [
	'name' => 'Your Name',
	'email' => 'you@example.com',
	'phone' => '',
	'address' => '',
	'city' => '',
	'postal_code' => '',
	'avatar' => 'https://placehold.co/200x200?text=Avatar',
	'experience_years' => 0,
	'pet_types' => '',
	'home_type' => '',
	'date_joined' => date('Y-m-d'),
	'verified_email' => false,
	'verified_phone' => false
];
$prefs = isset($prefs) ? $prefs : [
	'email_notifications' => true,
	'sms_notifications' => false,
	'booking_reminders' => 48,
	'newsletter_subscription' => true,
	'marketing_emails' => false,
	'language' => 'en',
	'timezone' => 'Asia/Colombo',
	'theme' => 'light',
	'max_pets' => 3,
	'availability' => 'Flexible'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Settings - Sitter - PetVet</title>
<link rel="stylesheet" href="/PETVET/public/css/sitter/settings.css" />
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
					<div class="profile-grid">
						<div class="profile-left">
							<div class="avatar-frame">
								<div class="image-preview-list avatar big" id="sitterAvatarPreview">
									<div class="image-preview-item">
										<img src="<?= htmlspecialchars($profile['avatar']) ?>" alt="avatar" />
									</div>
								</div>
								<div class="uploader-actions center">
									<input type="file" id="sitterAvatar" accept="image/*" hidden />
									<button type="button" class="btn outline" data-for="sitterAvatar">Change</button>
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
								<label>Experience (Years)
									<input type="number" name="experience_years" value="<?= htmlspecialchars($profile['experience_years']) ?>" min="0" />
								</label>
							</div>
							<div class="row two">
								<label>Pet Types
									<input type="text" name="pet_types" value="<?= htmlspecialchars($profile['pet_types']) ?>" placeholder="Dogs, Cats, etc." />
								</label>
								<label>Home Type
									<input type="text" name="home_type" value="<?= htmlspecialchars($profile['home_type']) ?>" placeholder="House, Apartment, etc." />
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
					<p class="muted small">Customize notifications &amp; settings</p>
				</div>
				<form id="formPrefs" class="form">
					<div class="pref-row">
						<div class="pref-label">
							<strong>Email Notifications</strong>
							<span class="muted small">Receive email alerts for new bookings</span>
						</div>
						<label class="toggle">
							<input type="checkbox" name="email_notifications" <?= $prefs['email_notifications'] ? 'checked' : '' ?> />
							<span class="slider"></span>
						</label>
					</div>
					<div class="pref-row">
						<div class="pref-label">
							<strong>SMS Notifications</strong>
							<span class="muted small">Get text messages for urgent updates</span>
						</div>
						<label class="toggle">
							<input type="checkbox" name="sms_notifications" <?= $prefs['sms_notifications'] ? 'checked' : '' ?> />
							<span class="slider"></span>
						</label>
					</div>
					<div class="pref-row">
						<div class="pref-label">
							<strong>Booking Reminders</strong>
							<span class="muted small">When to send booking reminders</span>
						</div>
						<div class="select-group">
							<select name="booking_reminders" value="<?= $prefs['booking_reminders'] ?>">
								<option value="24" <?= $prefs['booking_reminders'] == 24 ? 'selected' : '' ?>>24 hours before</option>
								<option value="48" <?= $prefs['booking_reminders'] == 48 ? 'selected' : '' ?>>48 hours before</option>
								<option value="168" <?= $prefs['booking_reminders'] == 168 ? 'selected' : '' ?>>1 week before</option>
							</select>
						</div>
					</div>
					<div class="pref-row">
						<div class="pref-label">
							<strong>Maximum Pets</strong>
							<span class="muted small">Max pets you can care for at once</span>
						</div>
						<div class="select-group">
							<select name="max_pets" value="<?= $prefs['max_pets'] ?>">
								<option value="1" <?= $prefs['max_pets'] == 1 ? 'selected' : '' ?>>1 pet</option>
								<option value="2" <?= $prefs['max_pets'] == 2 ? 'selected' : '' ?>>2 pets</option>
								<option value="3" <?= $prefs['max_pets'] == 3 ? 'selected' : '' ?>>3 pets</option>
								<option value="5" <?= $prefs['max_pets'] == 5 ? 'selected' : '' ?>>5 pets</option>
							</select>
						</div>
					</div>
					<div class="pref-row">
						<div class="pref-label">
							<strong>Newsletter</strong>
							<span class="muted small">Weekly tips and updates</span>
						</div>
						<label class="toggle">
							<input type="checkbox" name="newsletter_subscription" <?= $prefs['newsletter_subscription'] ? 'checked' : '' ?> />
							<span class="slider"></span>
						</label>
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
					$availableRoles = [
						'pet-owner' => ['name' => 'Pet Owner', 'desc' => 'Manage your pets and appointments'],
						'trainer' => ['name' => 'Trainer', 'desc' => 'Provide training services'],
						'sitter' => ['name' => 'Pet Sitter', 'desc' => 'Offer pet sitting services'],
						'breeder' => ['name' => 'Breeder', 'desc' => 'Manage breeding operations']
					];
					$currentRole = 'sitter';
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
<div id="toast" class="toast"></div>
<script src="/PETVET/public/js/sitter/settings.js"></script>
</body>
</html>
