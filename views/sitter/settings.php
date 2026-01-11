<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$module = 'sitter';
$GLOBALS['currentPage'] = 'settings.php';
$GLOBALS['module'] = 'sitter';
/** Sitter Settings */
// Data will be loaded via JavaScript from API
$profile = ['avatar' => '/PETVET/public/images/emptyProfPic.png'];
$sitterData = [];
$prefs = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Settings - Sitter - PetVet</title>
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

	/* Keep two-column rows aligned cleanly when error text appears */
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
			<a href="#section-sitter">Sitter</a>
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
								<div class="image-preview-list avatar big" id="sitterAvatarPreview">
									<div class="image-preview-item">
										<img src="<?= htmlspecialchars($profile['avatar'] ?? '/PETVET/public/images/emptyProfPic.png') ?>" alt="avatar" />
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

			<!-- 3. Sitter-Specific Section -->
			<section class="card" id="section-sitter" data-section>
				<div class="card-head">
					<h2>Sitter Settings
						<span class="role-section-badge">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<path d="M12 2a5 5 0 0 1 5 5v1a5 5 0 0 1-10 0V7a5 5 0 0 1 5-5Z" />
								<path d="M20 21v-1a7 7 0 0 0-7-7h-2a7 7 0 0 0-7 7v1" />
								<path d="M8 13l-1.5 2.5" />
								<path d="M16 13l1.5 2.5" />
							</svg>
							Sitter
						</span>
					</h2>
					<p class="muted small">Manage your sitter profile and service details</p>
				</div>
				<form id="formSitter" class="form">
					<div class="row two">
						<label>Working Areas (Max 5)
							<div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
								<select id="sitterWorkAreaSelect" style="min-width: 220px; flex: 1 1 220px;"></select>
								<button type="button" class="btn outline" id="sitterAddWorkAreaBtn" style="white-space:nowrap;">Add Area</button>
							</div>
							<div id="sitterWorkAreaChips" style="display:flex; flex-wrap:wrap; gap:8px; margin-top:10px;"></div>
							<input type="hidden" id="sitterWorkAreasJson" name="work_areas" value="<?= htmlspecialchars($sitterData['work_area'] ?? '') ?>" />
							<small class="muted">Select Sri Lanka districts. You can add up to 5.</small>
						</label>
						<label>Experience (Years)
							<input type="number" name="experience" id="experienceYears" value="<?= htmlspecialchars($sitterData['experience'] ?? '') ?>" min="0" placeholder="e.g., 3" />
						</label>
					</div>
					<div class="row two">
						<label>Pet Types
							<input type="text" name="pet_types" id="petTypes" value="<?= htmlspecialchars($sitterData['pet_types'] ?? '') ?>" placeholder="e.g., Dogs, Cats" />
						</label>
						<label>Home Type
							<input type="text" name="home_type" id="homeType" value="<?= htmlspecialchars($sitterData['home_type'] ?? '') ?>" placeholder="e.g., House, Apartment" />
						</label>
					</div>
					<div class="row one">
						<label>Description
							<textarea name="description" id="sitterDescription" rows="3" placeholder="Describe your sitting service (max 50 words)"><?= htmlspecialchars($sitterData['description'] ?? '') ?></textarea>
						</label>
					</div>
					<div class="row two">
						<label>Primary Phone Number
							<input type="tel" name="phone_primary" id="phonePrimary" value="<?= htmlspecialchars($sitterData['phone_primary'] ?? '') ?>" placeholder="0XXXXXXXXX" pattern="0[0-9]{9}" required />
							<span id="phonePrimaryError" class="phone-error"></span>
						</label>
						<label>Secondary Phone Number
							<input type="tel" name="phone_secondary" id="phoneSecondary" value="<?= htmlspecialchars($sitterData['phone_secondary'] ?? '') ?>" placeholder="0XXXXXXXXX" pattern="0[0-9]{9}" />
							<span id="phoneSecondaryError" class="phone-error"></span>
						</label>
					</div>
					<div class="actions">
						<button class="btn primary" type="submit">Save Sitter Settings</button>
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
								<span class="toggle-label">Email Notifications <small>Booking confirmations &amp; updates</small></span>
							</label>
						</div>
						<div class="pref-row">
							<label class="select-group">Booking Reminder
								<select name="booking_reminders">
									<option value="24" <?= (($prefs['booking_reminders'] ?? 24)==24?'selected':''); ?>>24 hours before</option>
									<option value="48" <?= (($prefs['booking_reminders'] ?? 24)==48?'selected':''); ?>>48 hours before</option>
									<option value="168" <?= (($prefs['booking_reminders'] ?? 24)==168?'selected':''); ?>>1 week before</option>
								</select>
							</label>
						</div>
						<div class="pref-row">
							<label class="select-group">Max Pets Per Booking
								<select name="max_pets">
									<option value="1" <?= (($prefs['max_pets'] ?? 3)==1?'selected':''); ?>>1 pet</option>
									<option value="2" <?= (($prefs['max_pets'] ?? 3)==2?'selected':''); ?>>2 pets</option>
									<option value="3" <?= (($prefs['max_pets'] ?? 3)==3?'selected':''); ?>>3 pets</option>
									<option value="5" <?= (($prefs['max_pets'] ?? 3)==5?'selected':''); ?>>5 pets</option>
								</select>
							</label>
						</div>
						<div class="pref-row">
							<label class="select-group">Availability
								<select name="availability">
									<option value="Flexible" <?= (($prefs['availability'] ?? 'Flexible')==='Flexible'?'selected':''); ?>>Flexible</option>
									<option value="Weekdays" <?= (($prefs['availability'] ?? 'Flexible')==='Weekdays'?'selected':''); ?>>Weekdays</option>
									<option value="Weekends" <?= (($prefs['availability'] ?? 'Flexible')==='Weekends'?'selected':''); ?>>Weekends</option>
									<option value="Evenings" <?= (($prefs['availability'] ?? 'Flexible')==='Evenings'?'selected':''); ?>>Evenings</option>
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
<script src="/PETVET/public/js/sitter/settings.js?v=<?php echo time(); ?>"></script>
</body>
</html>
