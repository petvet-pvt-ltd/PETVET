<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$module = 'groomer';
$GLOBALS['currentPage'] = 'settings.php';
$GLOBALS['module'] = 'groomer';
/** Groomer Settings */
// Data will be loaded via JavaScript from API
$profile = ['avatar' => '/PETVET/public/images/emptyProfPic.png'];
$groomerData = ['business_logo' => '/PETVET/public/images/emptyProfPic.png'];
$prefs = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Settings - Groomer - PetVet</title>
<link rel="stylesheet" href="/PETVET/public/css/pet-owner/settings.css" />
<style>
	.phone-error{
		color:#ef4444;
		font-size:12px;
		margin-top:4px;
		display:none;
	}
	.phone-error.show{display:block;}
	input.error{border-color:#ef4444 !important;}

	.card + .card{margin-top:1.5rem;}

	.role-section-badge{
		display:inline-flex;
		align-items:center;
		gap:.5rem;
		padding:.25rem .75rem;
		background:#dbeafe;
		color:#1e40af;
		border-radius:9999px;
		font-size:.75rem;
		font-weight:600;
		margin-left:.5rem;
	}
	.role-section-badge svg{width:14px;height:14px;}

	/* Make two-column rows align cleanly even with error text */
	.row.two{align-items:start;}
	.row.two > label{min-width:0;}
</style>
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
			<a href="#section-groomer">Groomer</a>
			<a href="#section-preferences">Preferences</a>
			<a href="#section-role">Active Role</a>
		</nav>
	</div>

	<div class="page-wrap">
		<div class="settings-grid">
			<!-- 1. Profile Section (Same as Pet Owner) -->
			<section class="card" id="section-profile" data-section>
				<div class="card-head"><h2>Profile</h2></div>
				<form id="formProfile" class="form" enctype="multipart/form-data">
					<div class="profile-grid">
						<div class="profile-left">
							<div class="avatar-frame">
								<div class="image-preview-list avatar big" id="groomerAvatarPreview">
									<div class="image-preview-item">
										<img src="<?= htmlspecialchars($profile['avatar'] ?? '/PETVET/public/images/emptyProfPic.png') ?>" alt="avatar" />
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
								<label>First Name
									<input type="text" name="first_name" id="first_name" value="<?= htmlspecialchars($profile['first_name'] ?? '') ?>" required />
								</label>
								<label>Last Name
									<input type="text" name="last_name" id="last_name" value="<?= htmlspecialchars($profile['last_name'] ?? '') ?>" required />
								</label>
							</div>
							<div class="row one">
								<label>Email (Login Username)
								<input type="email" name="email" id="email" value="<?= htmlspecialchars($profile['email'] ?? '') ?>" readonly style="background: #f0f0f0; cursor: not-allowed;" />
								</label>
							</div>
							<div class="row one">
								<label>Phone
									<input type="tel" name="phone" id="phone" value="<?= htmlspecialchars($profile['phone'] ?? '') ?>" placeholder="07XXXXXXXX" pattern="07[0-9]{8}" title="Phone number must be 10 digits starting with 07" />
									<span id="phoneError" class="phone-error"></span>
								</label>
							</div>
							<div class="row one">
								<label>Address
									<textarea name="address" id="address" rows="3" placeholder="Enter your full address"><?= htmlspecialchars($profile['address'] ?? '') ?></textarea>
								</label>
							</div>
							<div class="actions">
								<button class="btn primary" type="submit">Save Profile</button>
							</div>
						</div>
					</div>
				</form>
			</section>

			<!-- 2. Change Password Section -->
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

			<!-- 3. Groomer-Specific Section -->
			<section class="card" id="section-groomer" data-section>
				<div class="card-head">
					<h2>Groomer Settings
						<span class="role-section-badge">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<path d="M12 2a5 5 0 0 1 5 5v1a5 5 0 0 1-10 0V7a5 5 0 0 1 5-5Z" />
								<path d="M20 21v-1a7 7 0 0 0-7-7h-2a7 7 0 0 0-7 7v1" />
								<path d="M16 8l2 2" />
								<path d="M8 8l-2 2" />
							</svg>
							Groomer
						</span>
					</h2>
					<p class="muted small">Manage your groomer profile and service details</p>
				</div>
				<form id="formGroomer" class="form" enctype="multipart/form-data">
					<div class="row one">
						<label>Business Name
							<input type="text" name="business_name" value="<?= htmlspecialchars($groomerData['business_name'] ?? '') ?>" placeholder="e.g., Paws &amp; Claws Grooming Salon" />
						</label>
					</div>
					<div class="row one">
						<label style="display: block; margin-bottom: 0.5rem;">Business Logo
							<small class="muted" style="display: block; margin-top: 4px;">This logo will be shown to customers instead of your personal profile picture</small>
						</label>
						<div class="avatar-frame">
							<div class="image-preview-list avatar big" id="businessLogoPreview">
								<div class="image-preview-item">
									<img src="<?= htmlspecialchars($groomerData['business_logo'] ?? '/PETVET/public/images/emptyProfPic.png') ?>" alt="Business Logo" />
								</div>
							</div>
							<div class="uploader-actions center">
								<input type="file" id="businessLogoInput" name="business_logo" accept="image/*" hidden />
								<button type="button" class="btn outline" onclick="document.getElementById('businessLogoInput').click()">Change Logo</button>
							</div>
						</div>
					</div>
					<div class="row two">
						<label>Work Area
							<input type="text" name="work_area" value="<?= htmlspecialchars($groomerData['work_area'] ?? '') ?>" placeholder="e.g., Colombo, Gampaha" />
						</label>
						<label>Experience (Years)
							<input type="number" name="experience" value="<?= htmlspecialchars($groomerData['experience'] ?? '') ?>" min="0" placeholder="e.g., 4" />
						</label>
					</div>
					<div class="row two">
						<label>Specializations
							<input type="text" name="specializations" value="<?= htmlspecialchars($groomerData['specializations'] ?? '') ?>" placeholder="e.g., Dogs, Cats, Show grooming" />
						</label>
						<label>Certifications
							<input type="text" name="certifications" value="<?= htmlspecialchars($groomerData['certifications'] ?? '') ?>" placeholder="e.g., Certified Professional Groomer" />
						</label>
					</div>
					<div class="row one">
						<label>Bio
							<textarea name="bio" rows="3" placeholder="Tell clients about your grooming experience... (UI only)"><?= htmlspecialchars($groomerData['bio'] ?? '') ?></textarea>
						</label>
					</div>
					<div class="row one">
						<label>Google Maps Location Link
							<input type="url" name="google_maps_link" value="<?= htmlspecialchars($groomerData['google_maps_link'] ?? '') ?>" placeholder="https://maps.google.com/..." />
						</label>
					</div>
					<div class="row two">
						<label>Primary Phone Number
							<input type="tel" name="phone_primary" id="phonePrimary" value="<?= htmlspecialchars($groomerData['phone_primary'] ?? '') ?>" placeholder="0XXXXXXXXX" pattern="0[0-9]{9}" required />
							<span id="phonePrimaryError" class="phone-error"></span>
						</label>
						<label>Secondary Phone Number
							<input type="tel" name="phone_secondary" id="phoneSecondary" value="<?= htmlspecialchars($groomerData['phone_secondary'] ?? '') ?>" placeholder="0XXXXXXXXX" pattern="0[0-9]{9}" />
							<span id="phoneSecondaryError" class="phone-error"></span>
						</label>
					</div>
					<div class="actions">
						<button class="btn primary" type="submit">Save Groomer Settings</button>
					</div>
				</form>
			</section>

			<!-- 4. Preferences Section -->
			<section class="card" id="section-preferences" data-section>
				<div class="card-head"><h2>Preferences</h2></div>
				<form id="formPrefs" class="form">
					<div class="pref-simplified">
						<div class="pref-row">
							<label class="toggle">
								<input type="checkbox" name="email_notifications" <?= ($prefs['email_notifications'] ?? true) ? 'checked' : '' ?> />
								<span class="toggle-track"><span class="toggle-handle"></span></span>
								<span class="toggle-label">Email Notifications <small>Bookings & updates</small></span>
							</label>
						</div>
						<div class="pref-row">
							<label class="toggle">
								<input type="checkbox" name="auto_accept_bookings" <?= ($prefs['auto_accept_bookings'] ?? false) ? 'checked' : '' ?> />
								<span class="toggle-track"><span class="toggle-handle"></span></span>
								<span class="toggle-label">Auto Accept Bookings <small>Accept new requests automatically</small></span>
							</label>
						</div>
						<div class="pref-row">
							<label class="select-group">Max Bookings Per Day
								<select name="max_bookings_per_day">
									<option value="2" <?= (($prefs['max_bookings_per_day'] ?? 4)==2?'selected':''); ?>>2</option>
									<option value="4" <?= (($prefs['max_bookings_per_day'] ?? 4)==4?'selected':''); ?>>4</option>
									<option value="6" <?= (($prefs['max_bookings_per_day'] ?? 4)==6?'selected':''); ?>>6</option>
									<option value="8" <?= (($prefs['max_bookings_per_day'] ?? 4)==8?'selected':''); ?>>8</option>
									<option value="10" <?= (($prefs['max_bookings_per_day'] ?? 4)==10?'selected':''); ?>>10</option>
								</select>
							</label>
						</div>
					</div>
					<div class="actions">
						<button class="btn primary" type="submit">Save Preferences</button>
					</div>
				</form>
			</section>

			<!-- 5. Role Management Card -->
			<?php include __DIR__ . '/../shared/components/role-switcher.php'; ?>
		</div>
	</div>
</main>

<div id="toast" class="toast" role="status" aria-live="polite" aria-atomic="true"></div>
<script src="/PETVET/public/js/groomer/settings.js?v=<?php echo time(); ?>"></script>
</body>
</html>
