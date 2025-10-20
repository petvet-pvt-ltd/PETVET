<?php /* Lost & Found - now consolidated without partials */ ?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost & Found | PetVet</title>
    <link rel="stylesheet" href="/PETVET/public/css/guest/navbar.css">
    <link rel="stylesheet" href="/PETVET/public/css/guest/lost-found.css">
</head>
<body>

<?php require_once 'navbar.php' ?>

<div class="main-content">
<?php
// Data comes from the controller via LostFoundModel
$reports = $reports ?? [];
$lostReports = $lostReports ?? [];
$foundReports = $foundReports ?? [];

// Helper functions for display formatting
function lf_esc($s){ 
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); 
}

function lf_fmtDate($ymd){ 
    $t=strtotime($ymd); 
    return $t? date('M j, Y',$t): lf_esc($ymd); 
}
?>
<header class="lf-header">
	<div>
		<h2>Lost &amp; Found</h2>
		<p class="lf-sub">Report missing pets or browse found pets in your community.</p>
	</div>
	<div style="display:none;gap:12px;">
		<button type="button" class="btn secondary" id="myListingsBtn">My Listings</button>
		<button type="button" class="btn primary" id="openReport">+ Report Pet</button>
	</div>
</header>

<section class="lf-controls">
	<div class="segmented" role="tablist" aria-label="Lost or Found">
		<button type="button" role="tab" aria-selected="true" class="seg-btn is-active" data-view="lost">Lost</button>
		<button type="button" role="tab" aria-selected="false" class="seg-btn" data-view="found">Found</button>
	</div>
	<div class="filters">
		<label class="input-wrap">
			<input type="text" id="q" placeholder="Search by name, breed, color, location...">
			<span class="icon">&#128269;</span>
		</label>
		<select id="species">
			<option value="">All species</option>
			<option>Dog</option><option>Cat</option><option>Bird</option>
		</select>
		<select id="sortBy">
			<option value="new">Newest first</option>
			<option value="old">Oldest first</option>
		</select>
	</div>
</section>

<section id="lostList" class="cards-grid" aria-live="polite">
	<?php if(empty($lostReports)): ?>
		<div class="empty">
			<img src="/PETVET/public/img/illustrations/empty-lost.svg" alt="" />
			<h3>No lost pets reported.</h3>
			<p>Great news! If you're missing a pet, post a report so the community can help.</p>
			<button type="button" class="btn outline" id="emptyReportLost">+ Report Pet</button>
		</div>
	<?php else: ?>
		<?php foreach ($lostReports as $r): ?>
			<article class="card" data-species="<?php echo lf_esc($r['species']); ?>" data-date="<?php echo lf_esc($r['date']); ?>" data-color="<?php echo lf_esc($r['color']); ?>">
				<div class="card-media">
				<?php 
				$photos = !empty($r['photo']) ? (is_array($r['photo']) ? $r['photo'] : [$r['photo']]) : [];
				if(!empty($photos)): ?>
					<img src="<?php echo lf_esc($photos[0]); ?>" alt="<?php echo lf_esc($r['name'] ?: ($r['species'].' (unknown name)')); ?>" class="carousel-image" data-index="0">
					<?php if(count($photos) > 1): ?>
						<?php foreach(array_slice($photos, 1) as $idx => $photo): ?>
							<img src="<?php echo lf_esc($photo); ?>" alt="Photo <?php echo $idx + 2; ?>" class="carousel-image" style="display:none;" data-index="<?php echo $idx + 1; ?>">
						<?php endforeach; ?>
						<button class="carousel-nav prev" data-direction="prev">
						</button>
						<button class="carousel-nav next" data-direction="next">
						</button>
						<div class="carousel-indicators">
							<?php foreach($photos as $idx => $photo): ?>
								<button class="carousel-indicator <?php echo $idx === 0 ? 'active' : ''; ?>" data-index="<?php echo $idx; ?>"></button>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				<?php else: ?>
					<div class="photo-fallback"><?php echo strtoupper(substr($r['species'],0,1)); ?></div>
				<?php endif; ?>
					<span class="badge badge-lost">Lost</span>
				</div>
				<div class="card-body">
					<h4 class="title">
						<?php echo lf_esc($r['name'] ?: 'Unknown Name'); ?>
						<span class="muted">• <?php echo lf_esc($r['species']); ?><?php echo $r['breed']? ' · '.lf_esc($r['breed']) : ''; ?><?php echo $r['age']? ' · '.lf_esc($r['age']) : ''; ?></span>
					</h4>
					<p class="meta"><strong>Last seen:</strong> <?php echo lf_esc($r['last_seen']); ?> — <?php echo lf_fmtDate($r['date']); ?></p>
					<?php if(!empty($r['notes'])): ?><p class="notes"><?php echo lf_esc($r['notes']); ?></p><?php endif; ?>
					<div class="actions">
						<button class="btn outline contact-owner-btn" 
							data-name="<?php echo lf_esc($r['name'] ?: 'Pet Owner'); ?>"
							data-phone="<?php echo lf_esc($r['contact']['phone']); ?>"
							data-phone2="<?php echo lf_esc($r['contact']['phone2'] ?? ''); ?>"
							data-email="<?php echo lf_esc($r['contact']['email']); ?>">
							Contact Owner
						</button>
					</div>
				</div>
			</article>
		<?php endforeach; ?>
	<?php endif; ?>
</section>

<section id="foundList" class="cards-grid hidden" aria-live="polite">
	<?php if(empty($foundReports)): ?>
		<div class="empty">
			<img src="/PETVET/public/img/illustrations/empty-found.svg" alt="" />
			<h3>No found pets reported yet.</h3>
			<p>If you've found a pet, post a report so the owner can reach you.</p>
			<button type="button" class="btn outline" id="emptyReportFound">+ Report Pet</button>
		</div>
	<?php else: ?>
		<?php foreach ($foundReports as $r): ?>
			<article class="card" data-species="<?php echo lf_esc($r['species']); ?>" data-date="<?php echo lf_esc($r['date']); ?>" data-color="<?php echo lf_esc($r['color']); ?>">
				<div class="card-media">
				<?php 
				$photos = !empty($r['photo']) ? (is_array($r['photo']) ? $r['photo'] : [$r['photo']]) : [];
				if(!empty($photos)): ?>
					<img src="<?php echo lf_esc($photos[0]); ?>" alt="<?php echo lf_esc($r['species'].' found'); ?>" class="carousel-image" data-index="0">
					<?php if(count($photos) > 1): ?>
						<?php foreach(array_slice($photos, 1) as $idx => $photo): ?>
							<img src="<?php echo lf_esc($photo); ?>" alt="Photo <?php echo $idx + 2; ?>" class="carousel-image" style="display:none;" data-index="<?php echo $idx + 1; ?>">
						<?php endforeach; ?>
						<button class="carousel-nav prev" data-direction="prev">
						</button>
						<button class="carousel-nav next" data-direction="next">
						</button>
						<div class="carousel-indicators">
							<?php foreach($photos as $idx => $photo): ?>
								<button class="carousel-indicator <?php echo $idx === 0 ? 'active' : ''; ?>" data-index="<?php echo $idx; ?>"></button>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				<?php else: ?>
					<div class="photo-fallback"><?php echo strtoupper(substr($r['species'],0,1)); ?></div>
				<?php endif; ?>
				<span class="badge badge-found">Found</span>
				</div>
				<div class="card-body">
					<h4 class="title">
						<?php echo lf_esc($r['name'] ?: 'Unknown Name'); ?>
						<span class="muted">• <?php echo lf_esc($r['species']); ?><?php echo $r['breed']? ' · '.lf_esc($r['breed']) : ''; ?><?php echo $r['age']? ' · '.lf_esc($r['age']) : ''; ?></span>
					</h4>
					<p class="meta"><strong>Found at:</strong> <?php echo lf_esc($r['last_seen']); ?> — <?php echo lf_fmtDate($r['date']); ?></p>
					<?php if(!empty($r['notes'])): ?><p class="notes"><?php echo lf_esc($r['notes']); ?></p><?php endif; ?>
					<div class="actions">
						<button class="btn outline contact-owner-btn" 
							data-name="<?php echo lf_esc($r['name'] ?: 'Pet Finder'); ?>"
							data-phone="<?php echo lf_esc($r['contact']['phone']); ?>"
							data-phone2="<?php echo lf_esc($r['contact']['phone2'] ?? ''); ?>"
							data-email="<?php echo lf_esc($r['contact']['email']); ?>">
							Contact Finder
						</button>
					</div>
				</div>
			</article>
		<?php endforeach; ?>
	<?php endif; ?>
</section>

<div class="modal-overlay" id="reportModal" hidden>
	<div class="modal" role="dialog" aria-modal="true" aria-labelledby="reportTitle">
		<h3 id="reportTitle">Report Pet</h3>
		<form id="reportForm" autocomplete="off">
			<div class="row">
				<label class="field">Type
					<select id="rType" required>
						<option value="lost">Lost</option>
						<option value="found">Found</option>
					</select>
				</label>
				<label class="field">Species
					<select id="rSpecies" required>
						<option>Dog</option><option>Cat</option><option>Bird</option>
					</select>
				</label>
			</div>
			<div class="row">
				<label class="field flex-2">Name (optional)
					<input type="text" id="rName" placeholder="Rocky / Unknown">
				</label>
				<label class="field">Color
					<input type="text" id="rColor" placeholder="Golden / Black">
				</label>
			</div>
			<div class="row">
				<label class="field flex-2">Last seen location
					<input type="text" id="rLocation" required placeholder="Street, Area">
				</label>
				<label class="field">Date
					<input type="date" id="rDate" required>
				</label>
			</div>
			<label class="field">Notes
				<textarea id="rNotes" rows="3" placeholder="Collar info, temperament, special needs..."></textarea>
			</label>
			<div class="row">
				<label class="field flex-2">Primary Phone
					<input type="tel" id="rPhone" required placeholder="+94 77 123 4567">
				</label>
				<label class="field">Secondary Phone (Optional)
					<input type="tel" id="rPhone2" placeholder="+94 76 555 1212">
				</label>
			</div>
			<label class="field">Email (Optional)
				<input type="email" id="rEmail" placeholder="your.email@example.com">
			</label>
			<label class="field">Photos (Optional, Max 3)
				<input type="file" id="rPhoto" accept="image/*" multiple data-max-files="3">
				<small class="muted">Upload up to 3 photos to help identify the pet.</small>
				<div id="photoPreview" style="margin-top:8px;display:none;display:flex;gap:8px;flex-wrap:wrap;">
				</div>
			</label>
			<div class="modal-actions">
				<button type="button" class="btn ghost" id="cancelReport">Cancel</button>
				<button type="submit" class="btn primary">Submit</button>
			</div>
		</form>
	</div>
</div>

<!-- Contact Modal -->
<div class="modal-overlay" id="contactModal" hidden>
	<div class="modal" role="dialog" aria-modal="true" aria-labelledby="contactTitle">
		<h3 id="contactTitle">Contact Information</h3>
		<div class="contact-modal-content" id="contactContent">
			<!-- Contact items will be inserted here by JavaScript -->
		</div>
		<div class="modal-actions">
			<button type="button" class="btn ghost" id="closeContact">Close</button>
		</div>
	</div>
</div>

<!-- My Listings Modal -->
<div class="modal-overlay" id="myListingsModal" hidden>
	<div class="modal" role="dialog" aria-modal="true" aria-labelledby="myListingsTitle">
		<h3 id="myListingsTitle">My Listings</h3>
		<div class="listings-grid" id="myListingsContent">
			<!-- Listings will be inserted here by JavaScript -->
			<div class="empty" style="border:none;padding:32px;">
				<h3>No listings yet</h3>
				<p>You haven't reported any lost or found pets.</p>
			</div>
		</div>
		<div class="modal-actions">
			<button type="button" class="btn ghost" id="closeMyListings">Close</button>
		</div>
	</div>
</div>

<!-- Edit Listing Modal -->
<div class="modal-overlay" id="editListingModal" hidden>
	<div class="modal" role="dialog" aria-modal="true" aria-labelledby="editListingTitle">
		<h3 id="editListingTitle">Edit Report</h3>
		<form id="editListingForm" autocomplete="off">
			<input type="hidden" id="editId">
			<div class="row">
				<label class="field">Type
					<select id="editType" required>
						<option value="lost">Lost</option>
						<option value="found">Found</option>
					</select>
				</label>
				<label class="field">Species
					<select id="editSpecies" required>
						<option>Dog</option><option>Cat</option><option>Bird</option>
					</select>
				</label>
			</div>
			<div class="row">
				<label class="field flex-2">Name (optional)
					<input type="text" id="editName" placeholder="Rocky / Unknown">
				</label>
				<label class="field">Color
					<input type="text" id="editColor" placeholder="Golden / Black">
				</label>
			</div>
			<div class="row">
				<label class="field flex-2">Last seen location
					<input type="text" id="editLocation" required placeholder="Street, Area">
				</label>
				<label class="field">Date
					<input type="date" id="editDate" required>
				</label>
			</div>
			<label class="field">Notes
				<textarea id="editNotes" rows="3" placeholder="Collar info, temperament, special needs..."></textarea>
			</label>
			<div class="row">
				<label class="field flex-2">Primary Phone
					<input type="tel" id="editPhone" required placeholder="+94 77 123 4567">
				</label>
				<label class="field">Secondary Phone (Optional)
					<input type="tel" id="editPhone2" placeholder="+94 76 555 1212">
				</label>
			</div>
			<label class="field">Email (Optional)
				<input type="email" id="editEmail" placeholder="your.email@example.com">
			</label>
			<div class="field">
				<label>Current Photos</label>
				<div id="editPhotoPreview" style="display:flex;gap:12px;flex-wrap:wrap;margin-top:8px;"></div>
			</div>
			<div class="modal-actions">
				<button type="button" class="btn ghost" id="cancelEditListing">Cancel</button>
				<button type="submit" class="btn primary">Save Changes</button>
			</div>
		</form>
	</div>
</div>

<!-- Confirmation Dialog -->
<div class="modal-overlay" id="confirmDialog" hidden>
	<div class="modal confirm-dialog" role="dialog" aria-modal="true" aria-labelledby="confirmTitle">
		<h3 id="confirmTitle">Confirm Delete</h3>
		<p class="confirm-message" id="confirmMessage">
			Are you sure you want to delete the report for <span class="confirm-highlight" id="confirmPetName"></span>? This action cannot be undone.
		</p>
		<div class="confirm-actions">
			<button type="button" class="btn ghost" id="cancelConfirm">Cancel</button>
			<button type="button" class="btn danger" id="confirmDelete">Delete</button>
		</div>
	</div>
</div>

</div>
<script src="/PETVET/public/js/pet-owner/lost-found.js"></script>

</body>
</html>
