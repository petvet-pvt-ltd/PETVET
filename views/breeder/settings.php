<?php
/** Breeder Settings (Profile & Preferences) */
$profile = isset($profile) ? $profile : [
	'name' => 'Your Name',
	'email' => 'you@example.com',
	'phone' => '',
	'address' => '',
	'city' => '',
	'postal_code' => '',
	'avatar' => 'https://placehold.co/200x200?text=Avatar',
	'business_name' => '',
	'license_number' => '',
	'experience_years' => 0,
	'specialization' => '',
	'date_joined' => date('Y-m-d'),
	'verified_email' => false,
	'verified_phone' => false
];
$prefs = isset($prefs) ? $prefs : [
	'email_notifications' => true,
	'sms_notifications' => false,
	'inquiry_alerts' => true,
	'newsletter_subscription' => true,
	'marketing_emails' => false,
	'language' => 'en',
	'timezone' => 'Asia/Colombo',
	'theme' => 'light',
	'public_profile' => true,
	'show_pricing' => true
];
?>
<link rel="stylesheet" href="/PETVET/public/css/breeder/settings.css" />
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
								<div class="image-preview-list avatar big" id="breederAvatarPreview">
									<div class="image-preview-item">
										<img src="<?= htmlspecialchars($profile['avatar']) ?>" alt="avatar" />
									</div>
								</div>
								<div class="uploader-actions center">
									<input type="file" id="breederAvatar" accept="image/*" hidden />
									<button type="button" class="btn outline" data-for="breederAvatar">Change</button>
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
								<label>Business Name
									<input type="text" name="business_name" value="<?= htmlspecialchars($profile['business_name']) ?>" />
								</label>
							</div>
							<div class="row two">
								<label>License Number
									<input type="text" name="license_number" value="<?= htmlspecialchars($profile['license_number']) ?>" />
								</label>
								<label>Experience (Years)
									<input type="number" name="experience_years" value="<?= htmlspecialchars($profile['experience_years']) ?>" min="0" />
								</label>
							</div>
							<label>Specialization
								<input type="text" name="specialization" value="<?= htmlspecialchars($profile['specialization']) ?>" placeholder="e.g., Large Breeds, Working Dogs" />
							</label>
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
							<span class="muted small">Receive email alerts for inquiries</span>
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
							<strong>Inquiry Alerts</strong>
							<span class="muted small">Instant alerts for new pet inquiries</span>
						</div>
						<label class="toggle">
							<input type="checkbox" name="inquiry_alerts" <?= $prefs['inquiry_alerts'] ? 'checked' : '' ?> />
							<span class="slider"></span>
						</label>
					</div>
					<div class="pref-row">
						<div class="pref-label">
							<strong>Public Profile</strong>
							<span class="muted small">Show your profile to potential buyers</span>
						</div>
						<label class="toggle">
							<input type="checkbox" name="public_profile" <?= $prefs['public_profile'] ? 'checked' : '' ?> />
							<span class="slider"></span>
						</label>
					</div>
					<div class="pref-row">
						<div class="pref-label">
							<strong>Show Pricing</strong>
							<span class="muted small">Display pet prices publicly</span>
						</div>
						<label class="toggle">
							<input type="checkbox" name="show_pricing" <?= $prefs['show_pricing'] ? 'checked' : '' ?> />
							<span class="slider"></span>
						</label>
					</div>
					<div class="pref-row">
						<div class="pref-label">
							<strong>Newsletter</strong>
							<span class="muted small">Weekly breeding tips and updates</span>
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
					$currentRole = 'breeder';
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
<script src="/PETVET/public/js/breeder/settings.js"></script>
