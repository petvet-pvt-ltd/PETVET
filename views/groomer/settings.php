<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$module = 'groomer';
$GLOBALS['currentPage'] = 'settings.php';
$GLOBALS['module'] = 'groomer';
/** Groomer Settings (Profile & Preferences) */
$profile = isset($profile) ? $profile : [
	'name' => 'Your Name',
	'email' => 'you@example.com',
	'phone' => '',
	'address' => '',
	'city' => '',
	'postal_code' => '',
	'avatar' => 'https://placehold.co/200x200?text=Avatar',
	'cover_photo' => 'https://placehold.co/1200x300?text=Cover',
	'experience_years' => 0,
	'specializations' => '',
	'certifications' => '',
	'bio' => '',
	'google_maps_link' => '',
	'date_joined' => date('Y-m-d'),
	'verified_email' => false,
	'verified_phone' => false
];
$prefs = isset($prefs) ? $prefs : [
	'email_notifications' => true,
	'sms_notifications' => false,
	'booking_reminders' => 24,
	'newsletter_subscription' => true,
	'marketing_emails' => false,
	'language' => 'en',
	'timezone' => 'Asia/Colombo',
	'theme' => 'light',
	'auto_accept_bookings' => false,
	'max_bookings_per_day' => 6
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Settings - Groomer - PetVet</title>
<link rel="stylesheet" href="/PETVET/public/css/groomer/settings.css" />
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
				<a href="#section-role">Role</a>
			</nav>
		</div>

		<div class="settings-grid">
			<!-- Profile Card -->
			<section class="card" id="section-profile" data-section>
				<div class="card-head"><h2>Profile</h2></div>
				<form id="formProfile" class="form" enctype="multipart/form-data">
					<!-- Cover Photo Section -->
					<div class="cover-photo-section">
						<div class="cover-photo-preview" id="coverPhotoPreview">
							<img src="<?= htmlspecialchars($profile['cover_photo']) ?>" alt="Cover Photo" />
							<div class="cover-photo-overlay">
								<button type="button" class="btn outline small" data-for="coverPhoto">Change Cover Photo</button>
							</div>
						</div>
						<input type="file" id="coverPhoto" accept="image/*" hidden />
					</div>

					<div class="profile-grid">
						<div class="profile-left">
							<div class="avatar-frame">
								<div class="image-preview-list avatar big" id="groomerAvatarPreview">
									<div class="image-preview-item">
										<img src="<?= htmlspecialchars($profile['avatar']) ?>" alt="avatar" />
									</div>
								</div>
								<div class="uploader-actions center">
									<input type="file" id="groomerAvatar" accept="image/*" hidden />
									<button type="button" class="btn outline" data-for="groomerAvatar">Change</button>
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
								<label>Specializations
									<input type="text" name="specializations" value="<?= htmlspecialchars($profile['specializations']) ?>" placeholder="e.g., Dogs, Cats, Show Grooming" />
								</label>
								<label>Certifications
									<input type="text" name="certifications" value="<?= htmlspecialchars($profile['certifications']) ?>" placeholder="e.g., Certified Professional Groomer" />
								</label>
							</div>
							<label>Bio
								<textarea name="bio" rows="3" placeholder="Tell clients about your grooming experience..."><?= htmlspecialchars($profile['bio']) ?></textarea>
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
							<label>Google Maps Location Link
								<input type="url" name="google_maps_link" value="<?= htmlspecialchars($profile['google_maps_link']) ?>" placeholder="https://maps.google.com/..." />
								<small class="field-hint">Share your location to help clients find you</small>
							</label>
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
						'breeder' => ['name' => 'Breeder', 'desc' => 'Manage breeding operations'],
						'groomer' => ['name' => 'Groomer', 'desc' => 'Provide grooming services']
					];
					$currentRole = 'groomer';
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
<script src="/PETVET/public/js/groomer/settings.js"></script>
</body>
</html>
