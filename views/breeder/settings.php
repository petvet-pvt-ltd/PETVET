<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$module = 'breeder';
$GLOBALS['currentPage'] = 'settings.php';
$GLOBALS['module'] = 'breeder';
/** Breeder Settings */
// Data will be loaded via JavaScript from API
$profile = ['avatar' => '/PETVET/public/images/emptyProfPic.png'];
$breederData = [];
$prefs = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Settings - Breeder - PetVet</title>
<link rel="stylesheet" href="/PETVET/public/css/pet-owner/settings.css" />
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
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
			<a href="#section-breeder">Breeder</a>
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
								<div class="image-preview-list avatar big" id="breederAvatarPreview">
									<div class="image-preview-item">
										<img src="<?= htmlspecialchars($profile['avatar'] ?? '/PETVET/public/images/emptyProfPic.png') ?>" alt="avatar" />
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

			<!-- 3. Breeder-Specific Section -->
			<section class="card" id="section-breeder" data-section>
				<div class="card-head">
					<h2>Breeder Settings
						<span class="role-section-badge">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
								<circle cx="12" cy="7" r="4" />
								<path d="M7 21v-1a7 7 0 0 1 7-7" />
							</svg>
							Breeder
						</span>
					</h2>
					<p class="muted small">Manage your breeder profile and service details</p>
				</div>
				<form id="formBreeder" class="form">
					<div class="row one">
						<label>Business Name
						<input type="text" name="business_name" value="<?= htmlspecialchars($breederData['business_name'] ?? '') ?>" placeholder="e.g., Golden Paws Kennel" />
						</label>
					</div>
					<div class="row two">
						<label>License Number
						<input type="text" name="license_number" value="<?= htmlspecialchars($breederData['license_number'] ?? '') ?>" placeholder="e.g., BRD-12345" />
					</label>
					<label>Experience (Years)
						<input type="number" name="experience" value="<?= htmlspecialchars($breederData['experience'] ?? '') ?>" min="0" placeholder="e.g., 6" />
						</label>
					</div>
					<div class="row two">
						<label>Work Area (District)
							<input type="text" id="work_area_display" readonly value="<?= htmlspecialchars($breederData['service_area'] ?? 'Not set - select location on map') ?>" style="background: #f9f9f9; cursor: not-allowed;" placeholder="Auto-detected from map location" />
							<input type="hidden" name="work_area" id="work_area" value="<?= htmlspecialchars($breederData['service_area'] ?? '') ?>" />
							<small class="muted" style="display: block; margin-top: 4px;">District is automatically detected from your map location</small>
						</label>
						<label>Specialization
							<input type="text" name="specialization" value="<?= htmlspecialchars($breederData['specialization'] ?? '') ?>" placeholder="e.g., Large breeds, Working dogs" />
						</label>
					</div>
					<div class="row one">
						<label>Breeding Services & Pricing
						<textarea name="services_description" rows="5" placeholder="Describe your breeding services, breeds, and rates... (UI only)"><?= htmlspecialchars($breederData['services_description'] ?? '') ?></textarea>
						</label>
					</div>
					<div class="row one">
						<div style="display:flex; flex-direction:column; gap:8px; font-weight:600; font-size:14px;">
							<label for="location_display" style="font-weight:600;">Map Location</label>
							<small class="muted" style="display:block; margin-top:-4px;">Click on the map to set your business location. This helps customers find breeders near them.</small>
							<button type="button" class="btn outline" id="useMyLocationBtn" style="margin-top: 6px; width: fit-content;">
								<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 4px;">
									<circle cx="12" cy="12" r="10"/>
									<circle cx="12" cy="12" r="3"/>
								</svg>
								Use My Current Location
							</button>
							<div id="breederMapContainer" style="height: 400px; width: 100%; border-radius: 8px; border: 1px solid var(--line, #e0e0e0); display: none;"></div>
							<input type="hidden" id="location_latitude" name="location_latitude" value="<?= htmlspecialchars($breederData['location_latitude'] ?? '') ?>" />
							<input type="hidden" id="location_longitude" name="location_longitude" value="<?= htmlspecialchars($breederData['location_longitude'] ?? '') ?>" />
							<input type="text" id="location_display" readonly placeholder="Click 'Use My Current Location' or click on map" style="cursor: pointer; background: #f9f9f9;" />
						</div>
					</div>
					<div class="row two">
						<label>Primary Phone Number
							<input type="tel" name="phone_primary" id="phonePrimary" value="<?= htmlspecialchars($breederData['phone_primary'] ?? '') ?>" placeholder="0XXXXXXXXX" pattern="0[0-9]{9}" required />
							<span id="phonePrimaryError" class="phone-error"></span>
						</label>
						<label>Secondary Phone Number
							<input type="tel" name="phone_secondary" id="phoneSecondary" value="<?= htmlspecialchars($breederData['phone_secondary'] ?? '') ?>" placeholder="0XXXXXXXXX" pattern="0[0-9]{9}" />
							<span id="phoneSecondaryError" class="phone-error"></span>
						</label>
					</div>
					<div class="actions">
						<button class="btn primary" type="submit">Save Breeder Settings</button>
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
								<span class="toggle-label">Email Notifications <small>Inquiries & updates</small></span>
							</label>
						</div>
						<div class="pref-row">
							<label class="toggle">
								<input type="checkbox" name="inquiry_alerts" <?= ($prefs['inquiry_alerts'] ?? true) ? 'checked' : '' ?> />
								<span class="toggle-track"><span class="toggle-handle"></span></span>
								<span class="toggle-label">Inquiry Alerts <small>Notify me when a customer contacts me</small></span>
							</label>
						</div>
						<div class="pref-row">
							<label class="toggle">
							<input type="checkbox" name="public_profile" <?= ($prefs['public_profile'] ?? true) ? 'checked' : '' ?> />
							<span class="toggle-track"><span class="toggle-handle"></span></span>
							<span class="toggle-label">Public Profile <small>Allow customers to view my profile</small></span>
						</label>
					</div>
					<div class="pref-row">
						<label class="toggle">
							<input type="checkbox" name="show_pricing" <?= ($prefs['show_pricing'] ?? true) ? 'checked' : '' ?> />
								<span class="toggle-track"><span class="toggle-handle"></span></span>
								<span class="toggle-label">Show Pricing <small>Display my rates publicly</small></span>
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
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>
<script src="/PETVET/public/js/breeder/settings.js?v=<?php echo time(); ?>"></script>
</body>
</html>
