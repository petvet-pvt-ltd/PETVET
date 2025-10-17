<link rel="stylesheet" href="/PETVET/public/css/admin/lost_found.css">

<div class="main-content">
  <div class="lf-header">
    <h1>Lost & Found Management</h1>
  </div>

  <!-- Search and Filters -->
  <div class="lf-controls">
    <div class="lf-search-box">
      <svg class="search-icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
        <path d="M9 17A8 8 0 1 0 9 1a8 8 0 0 0 0 16zM19 19l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      </svg>
      <input type="text" id="lfSearchInput" placeholder="Search by pet name, breed, or location" />
    </div>
    <div class="lf-filters">
      <select id="typeFilter" class="lf-filter-select">
        <option value="all">Type: All</option>
        <option value="Lost">Lost</option>
        <option value="Found">Found</option>
      </select>
      <select id="statusFilter" class="lf-filter-select">
        <option value="all">Status: All</option>
        <option value="Pending">Pending</option>
        <option value="Approved">Approved</option>
        <option value="Resolved">Resolved</option>
      </select>
      <button class="lf-export-btn">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
          <path d="M14 10v2.667A1.333 1.333 0 0112.667 14H3.333A1.333 1.333 0 012 12.667V10M11.333 5.333L8 2m0 0L4.667 5.333M8 2v8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Export Reports
      </button>
    </div>
  </div>

  <!-- Stats Cards -->
  <div class="lf-stats">
    <div class="lf-stat-card lost">
      <div class="stat-icon">🔍</div>
      <div class="stat-details">
        <div class="stat-label">Total Lost Reports</div>
        <div class="stat-value" id="totalLostValue">128</div>
      </div>
    </div>
    <div class="lf-stat-card found">
      <div class="stat-icon">🎉</div>
      <div class="stat-details">
        <div class="stat-label">Total Found Reports</div>
        <div class="stat-value" id="totalFoundValue">92</div>
      </div>
    </div>
    <div class="lf-stat-card resolved">
      <div class="stat-icon">✅</div>
      <div class="stat-details">
        <div class="stat-label">Resolved Cases</div>
        <div class="stat-value" id="resolvedValue">73</div>
      </div>
    </div>
    <div class="lf-stat-card flagged">
      <div class="stat-icon">⚠️</div>
      <div class="stat-details">
        <div class="stat-label">Flagged Reports</div>
        <div class="stat-value" id="flaggedValue">5</div>
      </div>
    </div>
  </div>

  <!-- Reports Table -->
  <div class="lf-table-container">
    <table class="lf-table">
      <thead>
        <tr>
          <th>PET</th>
          <th>TYPE</th>
          <th>LOCATION</th>
          <th>REPORTED BY</th>
          <th>DATE REPORTED</th>
          <th>STATUS</th>
          <th>ACTIONS</th>
        </tr>
      </thead>
      <tbody id="lfTableBody">
        <tr data-type="Lost" data-status="Pending" data-date="2023-10-26" data-contact="555-0101" data-description="Golden Retriever lost near downtown area. Very friendly and responds to name Bella. Last seen wearing a pink collar.">
          <td>
            <div class="pet-info">
              <img src="https://images.unsplash.com/photo-1633722715463-d30f4f325e24?w=80&h=80&fit=crop" alt="Bella" class="pet-avatar">
              <div>
                <div class="pet-name">Bella</div>
                <div class="pet-breed">Golden Retriever</div>
              </div>
            </div>
          </td>
          <td><span class="type-badge lost">Lost</span></td>
          <td>San Francisco, CA</td>
          <td>John Doe</td>
          <td>2023-10-26</td>
          <td><span class="status-badge pending">Pending</span></td>
          <td>
            <div class="action-buttons">
              <button class="action-btn view" title="View Details">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                  <path d="M1 9s3-6 8-6 8 6 8 6-3 6-8 6-8-6-8-6z" stroke="currentColor" stroke-width="1.5"/>
                  <circle cx="9" cy="9" r="2" stroke="currentColor" stroke-width="1.5"/>
                </svg>
              </button>
              <button class="action-btn approve" title="Approve">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                  <path d="M15 5L7 13l-4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </button>
              <button class="action-btn delete" title="Delete">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                  <path d="M2 4h14M6 4V3a1 1 0 011-1h4a1 1 0 011 1v1m2 0v11a2 2 0 01-2 2H6a2 2 0 01-2-2V4h10z" stroke="currentColor" stroke-width="1.5"/>
                </svg>
              </button>
            </div>
          </td>
        </tr>
        <tr data-type="Found" data-status="Approved" data-date="2023-10-25" data-contact="555-0102" data-description="Tabby cat found wandering in residential area. Very calm and appears well-fed. No collar or identification.">
          <td>
            <div class="pet-info">
              <img src="https://images.unsplash.com/photo-1574158622682-e40e69881006?w=80&h=80&fit=crop" alt="Whiskers" class="pet-avatar">
              <div>
                <div class="pet-name">Whiskers</div>
                <div class="pet-breed">Tabby Cat</div>
              </div>
            </div>
          </td>
          <td><span class="type-badge found">Found</span></td>
          <td>New York, NY</td>
          <td>Jane Smith</td>
          <td>2023-10-25</td>
          <td><span class="status-badge approved">Approved</span></td>
          <td>
            <div class="action-buttons">
              <button class="action-btn view" title="View Details">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                  <path d="M1 9s3-6 8-6 8 6 8 6-3 6-8 6-8-6-8-6z" stroke="currentColor" stroke-width="1.5"/>
                  <circle cx="9" cy="9" r="2" stroke="currentColor" stroke-width="1.5"/>
                </svg>
              </button>
              <button class="action-btn approve" title="Approve">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                  <path d="M15 5L7 13l-4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </button>
              <button class="action-btn delete" title="Delete">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                  <path d="M2 4h14M6 4V3a1 1 0 011-1h4a1 1 0 011 1v1m2 0v11a2 2 0 01-2 2H6a2 2 0 01-2-2V4h10z" stroke="currentColor" stroke-width="1.5"/>
                </svg>
              </button>
            </div>
          </td>
        </tr>
        <tr data-type="Lost" data-status="Resolved" data-date="2023-10-24" data-contact="555-0103" data-description="Large husky with blue eyes. Last seen near Central Park wearing a red collar.">
          <td>
            <div class="pet-info">
              <img src="https://images.unsplash.com/photo-1568572933382-74d440642117?w=80&h=80&fit=crop" alt="Max" class="pet-avatar">
              <div>
                <div class="pet-name">Max</div>
                <div class="pet-breed">Siberian Husky</div>
              </div>
            </div>
          </td>
          <td><span class="type-badge lost">Lost</span></td>
          <td>Austin, TX</td>
          <td>Mike Johnson</td>
          <td>2023-10-24</td>
          <td><span class="status-badge resolved">Resolved</span></td>
          <td>
            <div class="action-buttons">
              <button class="action-btn view" title="View Details">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                  <path d="M1 9s3-6 8-6 8 6 8 6-3 6-8 6-8-6-8-6z" stroke="currentColor" stroke-width="1.5"/>
                  <circle cx="9" cy="9" r="2" stroke="currentColor" stroke-width="1.5"/>
                </svg>
              </button>
              <button class="action-btn approve" title="Approve" style="display:none">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                  <path d="M15 5L7 13l-4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </button>
              <button class="action-btn delete" title="Delete">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                  <path d="M2 4h14M6 4V3a1 1 0 011-1h4a1 1 0 011 1v1m2 0v11a2 2 0 01-2 2H6a2 2 0 01-2-2V4h10z" stroke="currentColor" stroke-width="1.5"/>
                </svg>
              </button>
            </div>
          </td>
        </tr>
        <tr data-type="Found" data-status="Pending" data-date="2023-10-23" data-contact="555-0104" data-description="Small Persian cat found wandering near Main Street. Very friendly and well-groomed.">
          <td>
            <div class="pet-info">
              <img src="https://images.unsplash.com/photo-1535930891776-0c2dfb7fda1a?w=80&h=80&fit=crop" alt="Luna" class="pet-avatar">
              <div>
                <div class="pet-name">Luna</div>
                <div class="pet-breed">Persian Cat</div>
              </div>
            </div>
          </td>
          <td><span class="type-badge found">Found</span></td>
          <td>Seattle, WA</td>
          <td>Sarah Williams</td>
          <td>2023-10-23</td>
          <td><span class="status-badge pending">Pending</span></td>
          <td>
            <div class="action-buttons">
              <button class="action-btn view" title="View Details">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                  <path d="M1 9s3-6 8-6 8 6 8 6-3 6-8 6-8-6-8-6z" stroke="currentColor" stroke-width="1.5"/>
                  <circle cx="9" cy="9" r="2" stroke="currentColor" stroke-width="1.5"/>
                </svg>
              </button>
              <button class="action-btn approve" title="Approve">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                  <path d="M15 5L7 13l-4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </button>
              <button class="action-btn delete" title="Delete">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                  <path d="M2 4h14M6 4V3a1 1 0 011-1h4a1 1 0 011 1v1m2 0v11a2 2 0 01-2 2H6a2 2 0 01-2-2V4h10z" stroke="currentColor" stroke-width="1.5"/>
                </svg>
              </button>
            </div>
          </td>
        </tr>
        <tr data-type="Lost" data-status="Approved" data-date="2023-10-22" data-contact="555-0105" data-description="Medium-sized beagle, brown and white markings. Answers to name Rocky. Last seen near the beach.">
          <td>
            <div class="pet-info">
              <img src="https://images.unsplash.com/photo-1505628346881-b72b27e84530?w=80&h=80&fit=crop" alt="Rocky" class="pet-avatar">
              <div>
                <div class="pet-name">Rocky</div>
                <div class="pet-breed">Beagle</div>
              </div>
            </div>
          </td>
          <td><span class="type-badge lost">Lost</span></td>
          <td>Miami, FL</td>
          <td>Robert Brown</td>
          <td>2023-10-22</td>
          <td><span class="status-badge approved">Approved</span></td>
          <td>
            <div class="action-buttons">
              <button class="action-btn view" title="View Details">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                  <path d="M1 9s3-6 8-6 8 6 8 6-3 6-8 6-8-6-8-6z" stroke="currentColor" stroke-width="1.5"/>
                  <circle cx="9" cy="9" r="2" stroke="currentColor" stroke-width="1.5"/>
                </svg>
              </button>
              <button class="action-btn approve" title="Approve">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                  <path d="M15 5L7 13l-4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </button>
              <button class="action-btn delete" title="Delete">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                  <path d="M2 4h14M6 4V3a1 1 0 011-1h4a1 1 0 011 1v1m2 0v11a2 2 0 01-2 2H6a2 2 0 01-2-2V4h10z" stroke="currentColor" stroke-width="1.5"/>
                </svg>
              </button>
            </div>
          </td>
        </tr>
        <tr data-type="Found" data-status="Resolved" data-date="2023-10-21" data-contact="555-0106" data-description="Small Pomeranian found in park. Very energetic and playful. Owner has been reunited.">
          <td>
            <div class="pet-info">
              <img src="https://i.pravatar.cc/40?img=6" alt="Milo" class="pet-avatar">
              <div>
                <div class="pet-name">Milo</div>
                <div class="pet-breed">Pomeranian</div>
              </div>
            </div>
          </td>
          <td><span class="type-badge found">Found</span></td>
          <td>Boston, MA</td>
          <td>Emily Davis</td>
          <td>2023-10-21</td>
          <td><span class="status-badge resolved">Resolved</span></td>
          <td>
            <div class="action-buttons">
              <button class="action-btn view" title="View Details">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                  <path d="M1 9s3-6 8-6 8 6 8 6-3 6-8 6-8-6-8-6z" stroke="currentColor" stroke-width="1.5"/>
                  <circle cx="9" cy="9" r="2" stroke="currentColor" stroke-width="1.5"/>
                </svg>
              </button>
              <button class="action-btn approve" title="Approve" style="display:none">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                  <path d="M15 5L7 13l-4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </button>
              <button class="action-btn delete" title="Delete">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                  <path d="M2 4h14M6 4V3a1 1 0 011-1h4a1 1 0 011 1v1m2 0v11a2 2 0 01-2 2H6a2 2 0 01-2-2V4h10z" stroke="currentColor" stroke-width="1.5"/>
                </svg>
              </button>
            </div>
          </td>
        </tr>
        <tr data-type="Lost" data-status="Pending" data-date="2023-10-20" data-contact="555-0107" data-description="Black Labrador, very friendly. Lost during morning walk. Has microchip.">
          <td>
            <div class="pet-info">
              <img src="https://images.unsplash.com/photo-1587300003388-59208cc962cb?w=80&h=80&fit=crop" alt="Shadow" class="pet-avatar">
              <div>
                <div class="pet-name">Shadow</div>
                <div class="pet-breed">Labrador</div>
              </div>
            </div>
          </td>
          <td><span class="type-badge lost">Lost</span></td>
          <td>Portland, OR</td>
          <td>David Miller</td>
          <td>2023-10-20</td>
          <td><span class="status-badge pending">Pending</span></td>
          <td>
            <div class="action-buttons">
              <button class="action-btn view" title="View Details">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                  <path d="M1 9s3-6 8-6 8 6 8 6-3 6-8 6-8-6-8-6z" stroke="currentColor" stroke-width="1.5"/>
                  <circle cx="9" cy="9" r="2" stroke="currentColor" stroke-width="1.5"/>
                </svg>
              </button>
              <button class="action-btn approve" title="Approve">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                  <path d="M15 5L7 13l-4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </button>
              <button class="action-btn delete" title="Delete">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                  <path d="M2 4h14M6 4V3a1 1 0 011-1h4a1 1 0 011 1v1m2 0v11a2 2 0 01-2 2H6a2 2 0 01-2-2V4h10z" stroke="currentColor" stroke-width="1.5"/>
                </svg>
              </button>
            </div>
          </td>
        </tr>
        <tr data-type="Found" data-status="Approved" data-date="2023-10-19" data-contact="555-0108" data-description="Orange tabby cat found near shopping center. Wearing blue collar with bell.">
          <td>
            <div class="pet-info">
              <img src="https://images.unsplash.com/photo-1611915387288-fd8d2f5f928b?w=80&h=80&fit=crop" alt="Charlie" class="pet-avatar">
              <div>
                <div class="pet-name">Charlie</div>
                <div class="pet-breed">Orange Tabby</div>
              </div>
            </div>
          </td>
          <td><span class="type-badge found">Found</span></td>
          <td>Denver, CO</td>
          <td>Lisa Anderson</td>
          <td>2023-10-19</td>
          <td><span class="status-badge approved">Approved</span></td>
          <td>
            <div class="action-buttons">
              <button class="action-btn view" title="View Details">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                  <path d="M1 9s3-6 8-6 8 6 8 6-3 6-8 6-8-6-8-6z" stroke="currentColor" stroke-width="1.5"/>
                  <circle cx="9" cy="9" r="2" stroke="currentColor" stroke-width="1.5"/>
                </svg>
              </button>
              <button class="action-btn approve" title="Approve">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                  <path d="M15 5L7 13l-4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </button>
              <button class="action-btn delete" title="Delete">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                  <path d="M2 4h14M6 4V3a1 1 0 011-1h4a1 1 0 011 1v1m2 0v11a2 2 0 01-2 2H6a2 2 0 01-2-2V4h10z" stroke="currentColor" stroke-width="1.5"/>
                </svg>
              </button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<!-- View Details Modal -->
<div id="viewModal" class="lf-modal" style="display: none;">
  <div class="lf-modal-overlay" onclick="closeViewModal()"></div>
  <div class="lf-modal-content">
    <div class="lf-modal-header">
      <h2 id="modalTitle">Pet Report Details</h2>
      <button class="lf-modal-close" onclick="closeViewModal()">&times;</button>
    </div>
    <div class="lf-modal-body">
      <div class="detail-grid">
        <div class="detail-item">
          <label>Pet Name:</label>
          <span id="detailPetName"></span>
        </div>
        <div class="detail-item">
          <label>Breed:</label>
          <span id="detailBreed"></span>
        </div>
        <div class="detail-item">
          <label>Type:</label>
          <span id="detailType"></span>
        </div>
        <div class="detail-item">
          <label>Status:</label>
          <span id="detailStatus"></span>
        </div>
        <div class="detail-item">
          <label>Location:</label>
          <span id="detailLocation"></span>
        </div>
        <div class="detail-item">
          <label>Date Reported:</label>
          <span id="detailDate"></span>
        </div>
        <div class="detail-item">
          <label>Reported By:</label>
          <span id="detailReportedBy"></span>
        </div>
        <div class="detail-item">
          <label>Contact:</label>
          <span id="detailContact"></span>
        </div>
        <div class="detail-item full-width">
          <label>Description:</label>
          <p id="detailDescription"></p>
        </div>
      </div>
    </div>
    <div class="lf-modal-footer">
      <button class="lf-btn secondary" onclick="closeViewModal()">Close</button>
    </div>
  </div>
</div>

<script>
// Search functionality
document.getElementById('lfSearchInput').addEventListener('input', function(e) {
  const searchTerm = e.target.value.toLowerCase();
  filterTable();
});

// Filter functionality
document.getElementById('typeFilter').addEventListener('change', filterTable);
document.getElementById('statusFilter').addEventListener('change', filterTable);

function filterTable() {
  const searchTerm = document.getElementById('lfSearchInput').value.toLowerCase();
  const typeFilter = document.getElementById('typeFilter').value;
  const statusFilter = document.getElementById('statusFilter').value;
  
  const rows = document.querySelectorAll('#lfTableBody tr');
  let visibleCount = 0;
  
  rows.forEach(row => {
    const petInfo = row.querySelector('.pet-info').textContent.toLowerCase();
    const location = row.cells[2].textContent.toLowerCase();
    const rowType = row.getAttribute('data-type');
    const rowStatus = row.getAttribute('data-status');
    
    const matchesSearch = petInfo.includes(searchTerm) || location.includes(searchTerm);
    const matchesType = typeFilter === 'all' || rowType === typeFilter;
    const matchesStatus = statusFilter === 'all' || rowStatus === statusFilter;
    
    if (matchesSearch && matchesType && matchesStatus) {
      row.style.display = '';
      visibleCount++;
    } else {
      row.style.display = 'none';
    }
  });
  
  updateStats();
}

function updateStats() {
  const rows = document.querySelectorAll('#lfTableBody tr');
  let lostCount = 0, foundCount = 0, resolvedCount = 0, flaggedCount = 0;
  
  rows.forEach(row => {
    if (row.style.display !== 'none') {
      const type = row.getAttribute('data-type');
      const status = row.getAttribute('data-status');
      
      if (type === 'Lost') lostCount++;
      if (type === 'Found') foundCount++;
      if (status === 'Resolved') resolvedCount++;
      // Flagged logic can be added based on your requirements
    }
  });
  
  document.getElementById('totalLostValue').textContent = lostCount;
  document.getElementById('totalFoundValue').textContent = foundCount;
  document.getElementById('resolvedValue').textContent = resolvedCount;
}

// View Details Modal Functions
function openViewModal(row) {
  const petName = row.querySelector('.pet-name').textContent;
  const breed = row.querySelector('.pet-breed').textContent;
  const type = row.getAttribute('data-type');
  const status = row.getAttribute('data-status');
  const location = row.cells[2].textContent;
  const reportedBy = row.cells[3].textContent;
  const date = row.cells[4].textContent;
  const contact = row.getAttribute('data-contact');
  const description = row.getAttribute('data-description');
  
  // Populate modal
  document.getElementById('detailPetName').textContent = petName;
  document.getElementById('detailBreed').textContent = breed;
  document.getElementById('detailType').innerHTML = `<span class="type-badge ${type.toLowerCase()}">${type}</span>`;
  document.getElementById('detailStatus').innerHTML = `<span class="status-badge ${status.toLowerCase()}">${status}</span>`;
  document.getElementById('detailLocation').textContent = location;
  document.getElementById('detailReportedBy').textContent = reportedBy;
  document.getElementById('detailDate').textContent = date;
  document.getElementById('detailContact').textContent = contact;
  document.getElementById('detailDescription').textContent = description;
  
  // Show modal
  document.getElementById('viewModal').style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

function closeViewModal() {
  document.getElementById('viewModal').style.display = 'none';
  document.body.style.overflow = 'auto';
}

// Action buttons
document.addEventListener('click', function(e) {
  if (e.target.closest('.action-btn.view')) {
    const row = e.target.closest('tr');
    openViewModal(row);
  }
  
  if (e.target.closest('.action-btn.approve')) {
    const row = e.target.closest('tr');
    const petName = row.querySelector('.pet-name').textContent;
    if (confirm('Approve report for: ' + petName + '?')) {
      row.setAttribute('data-status', 'Approved');
      const statusBadge = row.querySelector('.status-badge');
      statusBadge.className = 'status-badge approved';
      statusBadge.textContent = 'Approved';
      e.target.closest('.action-btn.approve').style.display = 'none';
      updateStats();
    }
  }
  
  if (e.target.closest('.action-btn.delete')) {
    const row = e.target.closest('tr');
    const petName = row.querySelector('.pet-name').textContent;
    if (confirm('Are you sure you want to delete the report for "' + petName + '"? This action cannot be undone.')) {
      // Add fade out animation
      row.style.transition = 'opacity 0.3s ease';
      row.style.opacity = '0';
      
      setTimeout(() => {
        row.remove();
        updateStats();
      }, 300);
    }
  }
});

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeViewModal();
  }
});

// Export functionality
document.querySelector('.lf-export-btn').addEventListener('click', function() {
  alert('Export functionality will be implemented');
});

// Initialize stats on load
updateStats();
</script>
