<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$module = 'trainer';
$GLOBALS['currentPage'] = 'settings.php';
$GLOBALS['module'] = 'trainer';

/** Trainer Settings */
// Data will be loaded via JavaScript from API
$profile = ['avatar' => '/PETVET/public/images/emptyProfPic.png'];
$trainerData = [];
$prefs = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Settings - Trainer - PetVet</title>
<link rel="stylesheet" href="/PETVET/public/css/pet-owner/settings.css" />
<style>
	/* Ensure number inputs match shared settings input styles */
	input[type=number] {
		padding: 12px 14px;
		border: 1px solid var(--border);
		border-radius: 12px;
		background: var(--card);
		color: var(--text);
		outline: none;
		transition: var(--transition);
		box-shadow: 0 1px 0 rgba(0,0,0,.02);
		width: 100%;
		box-sizing: border-box;
	}
	input[type=number]:focus {
		border-color: var(--primary);
		box-shadow: 0 0 0 4px var(--ring);
	}

	/* Training Types Toggle Section */
	.training-types-section {
		margin-top: 1.5rem;
		padding: 1.5rem;
		background: #f9fafb;
		border-radius: 8px;
		border: 1px solid #e5e7eb;
	}

	.training-types-section h4 {
		margin: 0 0 1rem;
		font-size: 1rem;
		font-weight: 600;
		color: #374151;
	}

	.training-type-item {
		display: flex;
		align-items: center;
		gap: 1rem;
		padding: 1rem;
		background: white;
		border-radius: 6px;
		border: 1px solid #e5e7eb;
		margin-bottom: 0.75rem;
	}

	@media (max-width: 640px) {
		.training-type-item {
			flex-direction: column;
			align-items: stretch;
			gap: 12px;
		}
		.training-toggle-wrapper {
			flex: 1 1 auto;
			min-width: 0;
		}
		.training-charge-wrapper input {
			max-width: none;
		}
	}

	.training-type-item:last-child {
		margin-bottom: 0;
	}

	.training-toggle-wrapper {
		display: flex;
		align-items: center;
		flex: 0 0 260px;
		min-width: 260px;
	}

	/* Toggle styles normally rely on .pref-row being position:relative.
	   Make the toggle self-contained here to keep alignment consistent. */
	.training-toggle-wrapper label.toggle {
		position: relative;
		display: flex;
		align-items: center;
		gap: 16px;
		margin: 0;
	}

	.training-toggle-wrapper .toggle-label {
		display: flex;
		flex-direction: column;
		line-height: 1.3;
	}

	.training-charge-wrapper {
		flex: 1;
		display: flex;
		align-items: center;
		gap: 10px;
		min-width: 0;
	}

	.training-charge-wrapper input {
		margin: 0;
		width: 100%;
		min-width: 140px;
		max-width: 420px;
	}

	.training-charge-wrapper input:disabled {
		background: #f3f4f6;
		cursor: not-allowed;
		opacity: 0.6;
	}

	.currency-prefix {
		font-weight: 500;
		color: #6b7280;
		font-size: 0.95rem;
		white-space: nowrap;
	}

	.charge-suffix {
		color: #6b7280;
		white-space: nowrap;
		font-size: 14px;
		font-weight: 500;
	}

	/* Make two-column rows align cleanly even with error text */
	.row.two {
		align-items: start;
	}
	.row.two > label {
		min-width: 0;
	}

	/* Phone validation styles */
	.phone-error {
		color: #ef4444;
		font-size: 12px;
		margin-top: 4px;
		display: none;
	}

	.phone-error.show {
		display: block;
	}

	input.error {
		border-color: #ef4444 !important;
	}

	/* Section spacing */
	.card + .card {
		margin-top: 1.5rem;
	}

	.role-section-badge {
		display: inline-flex;
		align-items: center;
		gap: 0.5rem;
		padding: 0.25rem 0.75rem;
		background: #dbeafe;
		color: #1e40af;
		border-radius: 9999px;
		font-size: 0.75rem;
		font-weight: 600;
		margin-left: 0.5rem;
	}

	.role-section-badge svg {
		width: 14px;
		height: 14px;
	}
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
			<a href="#section-trainer">Trainer</a>
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
								<div class="image-preview-list avatar big" id="trainerAvatarPreview">
									<div class="image-preview-item">
										<img src="<?= htmlspecialchars($profile['avatar'] ?? '/PETVET/public/images/emptyProfPic.png') ?>" alt="avatar" />
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
								<label>First Name
									<input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($profile['first_name'] ?? '') ?>" required />
								</label>
								<label>Last Name
									<input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($profile['last_name'] ?? '') ?>" required />
								</label>
							</div>
							<div class="row one">
								<label>Email (Login Username)
									<input type="email" id="email" name="email" value="<?= htmlspecialchars($profile['email'] ?? '') ?>" readonly style="background: #f0f0f0; cursor: not-allowed;" />
								</label>
							</div>
							<div class="row one">
								<label>Phone
									<input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($profile['phone'] ?? '') ?>" placeholder="07XXXXXXXX" pattern="07[0-9]{8}" title="Phone number must be 10 digits starting with 07" />
									<span id="phoneError" class="phone-error"></span>
								</label>
							</div>
							<div class="row one">
								<label>Address
									<textarea id="address" name="address" rows="3" placeholder="Enter your full address"><?= htmlspecialchars($profile['address'] ?? '') ?></textarea>
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

			<!-- 3. Trainer-Specific Section -->
			<section class="card" id="section-trainer" data-section>
				<div class="card-head">
					<h2>Trainer Settings
						<span class="role-section-badge">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
								<circle cx="9" cy="7" r="4"></circle>
								<path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
								<path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
							</svg>
							Trainer
						</span>
					</h2>
					<p class="muted small">Manage your trainer profile and service details</p>
				</div>
				<form id="formTrainer" class="form">
					<div class="row one">
						<p class="muted small">Public listing: complete required fields + enable at least one training type.</p>
					</div>
					<div class="row one">
						<label>Association Name
							<input type="text" name="association_name" value="<?= htmlspecialchars($trainerData['association_name'] ?? '') ?>" placeholder="e.g., Sri Lanka Pet Trainers Association" required />
						</label>
					</div>
					<div class="row two">
						<label>Working Areas (Max 5)
							<div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
								<select id="trainerWorkAreaSelect" style="min-width: 220px; flex: 1 1 220px;"></select>
								<button type="button" class="btn outline" id="trainerAddWorkAreaBtn" style="white-space:nowrap;">Add Area</button>
							</div>
							<div id="trainerWorkAreaChips" style="display:flex; flex-wrap:wrap; gap:8px; margin-top:10px;"></div>
							<input type="hidden" id="trainerWorkAreasJson" name="work_areas" value="<?= htmlspecialchars($trainerData['work_area'] ?? '') ?>" />
							<small class="muted">Select Sri Lanka districts. You can add up to 5.</small>
						</label>
						<label>Experience (Years)
							<input type="number" name="experience" value="<?= htmlspecialchars($trainerData['experience'] ?? '') ?>" min="0" placeholder="e.g., 5" required />
						</label>
					</div>
					<div class="row one">
						<label>Specialization
							<input type="text" name="specialization" value="<?= htmlspecialchars($trainerData['specialization'] ?? '') ?>" placeholder="e.g., Obedience Training, Behavior Modification" required />
						</label>
					</div>
					
					<!-- Training Types with Toggles and Charges -->
					<div class="training-types-section">
						<h4>Training Types & Charges</h4>
						<p class="muted small">Enable at least one to be visible on Services.</p>
						
						<!-- Basic Training -->
						<div class="training-type-item">
							<div class="training-toggle-wrapper">
								<label class="toggle">
									<input type="checkbox" name="training_basic_enabled" id="trainingBasicToggle" <?= ($trainerData['training_basic_enabled'] ?? false) ? 'checked' : '' ?> />
									<span class="toggle-track"><span class="toggle-handle"></span></span>
									<span class="toggle-label">Basic Training</span>
								</label>
							</div>
							<div class="training-charge-wrapper">
								<span class="currency-prefix">LKR</span>
								<input type="number" name="training_basic_charge" id="trainingBasicCharge" value="<?= htmlspecialchars($trainerData['training_basic_charge'] ?? '') ?>" placeholder="2000" min="0" step="100" <?= !($trainerData['training_basic_enabled'] ?? false) ? 'disabled' : '' ?> />
								<span class="charge-suffix">per session</span>
							</div>
						</div>

						<!-- Intermediate Training -->
						<div class="training-type-item">
							<div class="training-toggle-wrapper">
								<label class="toggle">
									<input type="checkbox" name="training_intermediate_enabled" id="trainingIntermediateToggle" <?= ($trainerData['training_intermediate_enabled'] ?? false) ? 'checked' : '' ?> />
									<span class="toggle-track"><span class="toggle-handle"></span></span>
									<span class="toggle-label">Intermediate Training</span>
								</label>
							</div>
							<div class="training-charge-wrapper">
								<span class="currency-prefix">LKR</span>
								<input type="number" name="training_intermediate_charge" id="trainingIntermediateCharge" value="<?= htmlspecialchars($trainerData['training_intermediate_charge'] ?? '') ?>" placeholder="3500" min="0" step="100" <?= !($trainerData['training_intermediate_enabled'] ?? false) ? 'disabled' : '' ?> />
								<span class="charge-suffix">per session</span>
							</div>
						</div>

						<!-- Advanced Training -->
						<div class="training-type-item">
							<div class="training-toggle-wrapper">
								<label class="toggle">
									<input type="checkbox" name="training_advanced_enabled" id="trainingAdvancedToggle" <?= ($trainerData['training_advanced_enabled'] ?? false) ? 'checked' : '' ?> />
									<span class="toggle-track"><span class="toggle-handle"></span></span>
									<span class="toggle-label">Advanced Training</span>
								</label>
							</div>
							<div class="training-charge-wrapper">
								<span class="currency-prefix">LKR</span>
								<input type="number" name="training_advanced_charge" id="trainingAdvancedCharge" value="<?= htmlspecialchars($trainerData['training_advanced_charge'] ?? '') ?>" placeholder="5000" min="0" step="100" <?= !($trainerData['training_advanced_enabled'] ?? false) ? 'disabled' : '' ?> />
								<span class="charge-suffix">per session</span>
							</div>
						</div>
					</div>

					<div class="row one">
						<label>Certifications <span style="color: #9ca3af; font-weight: 400;">(Optional)</span>
							<textarea name="certifications" rows="2" placeholder="List your certifications"><?= htmlspecialchars($trainerData['certifications'] ?? '') ?></textarea>
						</label>
					</div>

					<div class="row two">
						<label>Primary Phone Number
							<input type="tel" name="phone_primary" id="phonePrimary" value="<?= htmlspecialchars($trainerData['phone_primary'] ?? '') ?>" placeholder="0XXXXXXXXX" pattern="0[0-9]{9}" required />
							<span id="phonePrimaryError" class="phone-error"></span>
						</label>
						<label>Secondary Phone Number
							<input type="tel" name="phone_secondary" id="phoneSecondary" value="<?= htmlspecialchars($trainerData['phone_secondary'] ?? '') ?>" placeholder="0XXXXXXXXX" pattern="0[0-9]{9}" />
							<span id="phoneSecondaryError" class="phone-error"></span>
						</label>
					</div>

					<div class="actions">
						<button class="btn primary" type="submit">Save Trainer Settings</button>
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
								<span class="toggle-label">Email Notifications <small>Appointment confirmations & updates</small></span>
							</label>
						</div>
						<div class="pref-row">
							<label class="select-group">Appointment Reminder
								<select name="reminder_appointments" class="slim-select">
									<option value="24" <?= (($prefs['reminder_appointments'] ?? 24)==24?'selected':''); ?>>24 hours before</option>
									<option value="12" <?= (($prefs['reminder_appointments'] ?? 24)==12?'selected':''); ?>>12 hours before</option>
									<option value="6" <?= (($prefs['reminder_appointments'] ?? 24)==6?'selected':''); ?>>6 hours before</option>
									<option value="1" <?= (($prefs['reminder_appointments'] ?? 24)==1?'selected':''); ?>>1 hour before</option>
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

<script>
	// Phone validation for all phone inputs
	function validatePhone(input, errorSpan) {
		const value = input.value.trim();
		const pattern = /^0[0-9]{9}$/;
		
		if (value && !pattern.test(value)) {
			input.classList.add('error');
			errorSpan.textContent = 'Phone must be 10 digits starting with 0';
			errorSpan.classList.add('show');
			return false;
		} else {
			input.classList.remove('error');
			errorSpan.textContent = '';
			errorSpan.classList.remove('show');
			return true;
		}
	}

	// Setup phone validation
	const phoneInputs = [
		{ input: document.getElementById('phoneInput'), error: document.getElementById('phoneError') },
		{ input: document.getElementById('phonePrimary'), error: document.getElementById('phonePrimaryError') },
		{ input: document.getElementById('phoneSecondary'), error: document.getElementById('phoneSecondaryError') }
	];

	phoneInputs.forEach(({ input, error }) => {
		if (input && error) {
			input.addEventListener('input', () => validatePhone(input, error));
			input.addEventListener('blur', () => validatePhone(input, error));
		}
	});

	// Training type toggle handlers
	const trainingToggles = [
		{ toggle: 'trainingBasicToggle', charge: 'trainingBasicCharge' },
		{ toggle: 'trainingIntermediateToggle', charge: 'trainingIntermediateCharge' },
		{ toggle: 'trainingAdvancedToggle', charge: 'trainingAdvancedCharge' }
	];

	trainingToggles.forEach(({ toggle, charge }) => {
		const toggleEl = document.getElementById(toggle);
		const chargeEl = document.getElementById(charge);
		
		if (toggleEl && chargeEl) {
			toggleEl.addEventListener('change', function() {
				chargeEl.disabled = !this.checked;
				if (!this.checked) {
					chargeEl.value = '';
				}
			});
		}
	});

	// Avatar preview handler
	const avatarInput = document.getElementById('trainerAvatar');
	const avatarPreview = document.querySelector('#trainerAvatarPreview img');
	
	document.querySelectorAll('[data-for="trainerAvatar"]').forEach(btn => {
		btn.addEventListener('click', () => avatarInput.click());
	});

	if (avatarInput) {
		avatarInput.addEventListener('change', function(e) {
			const file = e.target.files[0];
			if (file) {
				const reader = new FileReader();
				reader.onload = function(e) {
					if (avatarPreview) {
						avatarPreview.src = e.target.result;
					}
				};
				reader.readAsDataURL(file);
			}
		});
	}
</script>

<script src="/PETVET/public/js/trainer/settings.js?v=<?php echo time(); ?>"></script>
</body>
</html>
