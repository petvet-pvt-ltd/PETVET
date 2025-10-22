<?php /* Admin Pet Listings Management */ ?>
<link rel="stylesheet" href="/PETVET/public/css/admin/pet-listings-modern.css">
<div class="main-content">
  <header class="page-header">
    <div class="title-wrap">
      <h2>Pet Listings Management</h2>
      <p class="subtitle">Review and manage pet listing submissions</p>
    </div>
    <div class="stats-badges">
      <span class="stat-badge pending" id="pendingCount">0 Pending</span>
      <span class="stat-badge approved" id="approvedCount">0 Approved</span>
    </div>
  </header>

  <section class="filters">
    <div class="field grow">
      <input type="text" id="searchInput" placeholder="Search by pet name, breed, or owner...">
    </div>
    <div class="field">
      <select id="statusFilter">
        <option value="">All Status</option>
        <option value="pending">Pending</option>
        <option value="approved">Approved</option>
        <option value="rejected">Rejected</option>
        <option value="sold">Sold</option>
      </select>
    </div>
    <div class="field">
      <select id="speciesFilter">
        <option value="">All Species</option>
        <option value="Dog">Dog</option>
        <option value="Cat">Cat</option>
        <option value="Bird">Bird</option>
        <option value="Other">Other</option>
      </select>
    </div>
  </section>

  <section id="listingsGrid" class="listings-grid">
    <div class="loading-state">
      <div class="spinner"></div>
      <p>Loading listings...</p>
    </div>
  </section>

  <!-- View Details Modal -->
  <div class="modal-overlay" id="viewModal" style="display:none;">
    <div class="modal-content large">
      <div class="modal-header">
        <h3>Pet Listing Details</h3>
        <button class="modal-close" onclick="closeViewModal()">&times;</button>
      </div>
      <div class="modal-body" id="viewModalBody">
        <div class="detail-images">
          <img id="mainImage" class="detail-main-image" src="" alt="Pet Image" style="display:none;">
          <div id="thumbnails" class="detail-thumbnails"></div>
        </div>
        
        <div class="detail-info">
          <div class="detail-row"><span class="detail-label">Name:</span><span class="detail-value" id="detailName"></span></div>
          <div class="detail-row"><span class="detail-label">Species:</span><span class="detail-value" id="detailSpecies"></span></div>
          <div class="detail-row"><span class="detail-label">Breed:</span><span class="detail-value" id="detailBreed"></span></div>
          <div class="detail-row"><span class="detail-label">Age:</span><span class="detail-value" id="detailAge"></span></div>
          <div class="detail-row"><span class="detail-label">Gender:</span><span class="detail-value" id="detailGender"></span></div>
          <div class="detail-row"><span class="detail-label">Price:</span><span class="detail-value" id="detailPrice"></span></div>
          <div class="detail-row"><span class="detail-label">Location:</span><span class="detail-value" id="detailLocation"></span></div>
          <div class="detail-row"><span class="detail-label">Description:</span><span class="detail-value" id="detailDescription"></span></div>
          <div class="detail-row"><span class="detail-label">Phone:</span><span class="detail-value" id="detailPhone"></span></div>
          <div class="detail-row"><span class="detail-label">Phone 2:</span><span class="detail-value" id="detailPhone2"></span></div>
          <div class="detail-row"><span class="detail-label">Email:</span><span class="detail-value" id="detailEmail"></span></div>
          <div class="detail-row"><span class="detail-label">Badges:</span><div class="detail-badges" id="detailBadges"></div></div>
          <div class="detail-row"><span class="detail-label">Owner:</span><span class="detail-value" id="detailOwner"></span></div>
          <div class="detail-row"><span class="detail-label">Owner Email:</span><span class="detail-value" id="detailOwnerEmail"></span></div>
          <div class="detail-row"><span class="detail-label">Status:</span><span id="detailStatus" class="status-badge-overlay"></span></div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn outline" onclick="closeViewModal()">Close</button>
      </div>
    </div>
  </div>

  <!-- Confirm Approve Modal -->
  <div class="modal-overlay" id="confirmApproveModal" style="display:none;">
    <div class="confirm-dialog">
      <h3>Confirm Approval</h3>
      <p class="confirm-message">
        Are you sure you want to approve the listing for <strong id="approvePetName"></strong>? 
        This will make it visible to all users in Explore Pets.
      </p>
      <div class="confirm-actions">
        <button class="btn outline" onclick="closeConfirmApprove()">Cancel</button>
        <button class="btn success" id="confirmApproveBtn">Approve</button>
      </div>
    </div>
  </div>

  <!-- Confirm Decline Modal -->
  <div class="modal-overlay" id="confirmDeclineModal" style="display:none;">
    <div class="confirm-dialog">
      <h3>Confirm Decline</h3>
      <p class="confirm-message">
        Are you sure you want to decline the listing for <strong id="declinePetName"></strong>? 
        This will permanently delete the listing and cannot be undone.
      </p>
      <div class="confirm-actions">
        <button class="btn outline" onclick="closeConfirmDecline()">Cancel</button>
        <button class="btn danger" id="confirmDeclineBtn">Decline & Delete</button>
      </div>
    </div>
  </div>
</div>

<script src="/PETVET/public/js/admin/pet-listings-modern.js"></script>
