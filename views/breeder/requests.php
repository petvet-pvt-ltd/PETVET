<?php
// This view expects data from BreederController::requests()
// Variables available: $pendingRequests, $approvedRequests, $completedRequests

if (!isset($pendingRequests)) $pendingRequests = [];
if (!isset($approvedRequests)) $approvedRequests = [];
if (!isset($completedRequests)) $completedRequests = [];

$module = 'breeder';
$currentPage = 'requests.php';
$GLOBALS['module'] = $module;
$GLOBALS['currentPage'] = $currentPage;
require_once dirname(__DIR__, 2) . '/config/config.php';
$pageTitle = "Breeding Requests";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - PetVet Breeder</title>
    <link rel="stylesheet" href="<?= asset('css/shared/bookings.css') ?>">
    <style>
        /* Breeder-specific theme colors only */
        .page-header {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        }

        .filter-btn.active {
            background: #f59e0b;
            border-color: #f59e0b;
        }

        .booking-card {
            border-left: 4px solid #f59e0b;
        }

        .booking-date {
            color: #f59e0b;
        }

        .btn-primary {
            background: #f59e0b;
        }

        .btn-primary:hover {
            background: #d97706;
        }

        .btn-outline {
            color: #f59e0b;
            border-color: #f59e0b;
        }

        .btn-outline:hover {
            background: #f59e0b;
            color: white;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
            border: none;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-approved {
            background: #d1fae5;
            color: #065f46;
        }

        .status-completed {
            background: #e0e7ff;
            color: #3730a3;
        }

        /* Contact modal - breeder orange theme */
        .call-btn {
            background: #f59e0b;
            color: white;
        }

        .call-btn:hover {
            background: #d97706;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../shared/sidebar/sidebar.php'; ?>

    <main class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h1>Breeding Requests</h1>
            <p>Manage breeding requests from pet owners</p>
        </div>

        <!-- Filter Tabs -->
        <div class="booking-filters">
            <div class="filter-btn active" data-filter="pending" onclick="filterRequests('pending')">
                Pending (<?php echo count($pendingRequests); ?>)
            </div>
            <div class="filter-btn" data-filter="approved" onclick="filterRequests('approved')">
                Approved (<?php echo count($approvedRequests); ?>)
            </div>
            <div class="filter-btn" data-filter="completed" onclick="filterRequests('completed')">
                Completed (<?php echo count($completedRequests); ?>)
            </div>
        </div>

        <!-- Requests Container -->
        <div class="bookings-container">
            <!-- Pending Requests -->
            <?php foreach ($pendingRequests as $request): ?>
            <div class="booking-card" data-status="pending">
                <div class="booking-header">
                    <div>
                        <div class="booking-title"><?php echo htmlspecialchars($request['pet_name']); ?> - <?php echo htmlspecialchars($request['breed']); ?></div>
                        <div class="booking-date">Requested: <?php echo date('M d, Y', strtotime($request['requested_date'])); ?></div>
                    </div>
                    <div class="booking-status status-pending">Pending</div>
                </div>
                <div class="booking-details">
                    <div class="detail-item">
                        <span class="detail-icon">👤</span>
                        <span><?php echo htmlspecialchars($request['owner_name']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">🐕</span>
                        <span><?php echo htmlspecialchars($request['pet_breed']); ?> (<?php echo htmlspecialchars($request['gender']); ?>)</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">📅</span>
                        <span>Preferred Date: <?php echo date('M d, Y', strtotime($request['preferred_date'])); ?></span>
                    </div>
                    <?php if (!empty($request['phone'])): ?>
                    <div class="detail-item">
                        <span class="detail-icon">📞</span>
                        <span><?php echo htmlspecialchars($request['phone']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                <?php if (!empty($request['message'])): ?>
                <div class="booking-description">
                    <strong>Message / Notes:</strong> <?php echo htmlspecialchars($request['message']); ?>
                </div>
                <?php endif; ?>
                <div class="booking-actions">
                    <button class="btn btn-primary" onclick="showAcceptModal(<?php echo $request['id']; ?>, '<?php echo addslashes($request['pet_name']); ?>', '<?php echo addslashes($request['owner_name']); ?>')">Accept</button>
                    <button class="btn btn-danger" onclick="showDeclineModal(<?php echo $request['id']; ?>, '<?php echo addslashes($request['pet_name']); ?>', '<?php echo addslashes($request['owner_name']); ?>')">Decline</button>
                    <button class="btn btn-outline" 
                        data-owner-name="<?php echo htmlspecialchars($request['owner_name'] ?? ''); ?>" 
                        data-owner-phone="<?php echo htmlspecialchars($request['phone'] ?? ''); ?>"
                        data-owner-phone2="<?php echo htmlspecialchars($request['phone_2'] ?? ''); ?>"
                        onclick="showContactModal(this.dataset.ownerName, this.dataset.ownerPhone, this.dataset.ownerPhone2)">Contact Owner</button>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- Approved Requests -->
            <?php foreach ($approvedRequests as $request): ?>
            <div class="booking-card" data-status="approved" style="display: none;">
                <div class="booking-header">
                    <div>
                        <div class="booking-title"><?php echo htmlspecialchars($request['pet_name']); ?> - <?php echo htmlspecialchars($request['breed']); ?></div>
                        <div class="booking-date">Breeding Date: <?php echo date('M d, Y', strtotime($request['breeding_date'])); ?></div>
                    </div>
                    <div class="booking-status status-approved">Approved</div>
                </div>
                <div class="booking-details">
                    <div class="detail-item">
                        <span class="detail-icon">👤</span>
                        <span><?php echo htmlspecialchars($request['owner_name']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">🐕</span>
                        <span><?php echo htmlspecialchars($request['pet_breed']); ?> (<?php echo htmlspecialchars($request['gender']); ?>)</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">❤️</span>
                        <span>Breeding Pet: <?php echo htmlspecialchars($request['breeder_pet_name']); ?></span>
                    </div>
                    <?php if (!empty($request['phone'])): ?>
                    <div class="detail-item">
                        <span class="detail-icon">📞</span>
                        <span><?php echo htmlspecialchars($request['phone']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                <?php if (!empty($request['notes'])): ?>
                <div class="booking-description">
                    <strong>Notes:</strong> <?php echo htmlspecialchars($request['notes']); ?>
                </div>
                <?php endif; ?>
                <div class="booking-actions">
                    <button class="btn btn-primary" onclick="showCompleteModal(<?php echo $request['id']; ?>, '<?php echo addslashes($request['pet_name']); ?>', '<?php echo addslashes($request['owner_name']); ?>')">Mark Complete</button>
                    <button class="btn btn-outline" 
                        data-owner-name="<?php echo htmlspecialchars($request['owner_name'] ?? ''); ?>" 
                        data-owner-phone="<?php echo htmlspecialchars($request['phone'] ?? ''); ?>"
                        data-owner-phone2="<?php echo htmlspecialchars($request['phone_2'] ?? ''); ?>"
                        onclick="showContactModal(this.dataset.ownerName, this.dataset.ownerPhone, this.dataset.ownerPhone2)">Contact Owner</button>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- Completed Requests -->
            <?php foreach ($completedRequests as $request): ?>
            <div class="booking-card" data-status="completed" style="display: none;">
                <div class="booking-header">
                    <div>
                        <div class="booking-title"><?php echo htmlspecialchars($request['pet_name']); ?> - <?php echo htmlspecialchars($request['breed']); ?></div>
                        <div class="booking-date">Completed: <?php echo date('M d, Y', strtotime($request['completion_date'])); ?></div>
                    </div>
                    <div class="booking-status status-completed">Completed</div>
                </div>
                <div class="booking-details">
                    <div class="detail-item">
                        <span class="detail-icon">👤</span>
                        <span><?php echo htmlspecialchars($request['owner_name']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">🐕</span>
                        <span><?php echo htmlspecialchars($request['pet_breed']); ?> (<?php echo htmlspecialchars($request['gender']); ?>)</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">❤️</span>
                        <span>Breeding Pet: <?php echo htmlspecialchars($request['breeder_pet_name']); ?></span>
                    </div>
                </div>
                <?php if (!empty($request['final_notes'])): ?>
                <div class="booking-description">
                    <strong>Final Notes:</strong> <?php echo htmlspecialchars($request['final_notes']); ?>
                </div>
                <?php endif; ?>
                <div class="booking-actions">
                    <button class="btn btn-outline" 
                        data-owner-name="<?php echo htmlspecialchars($request['owner_name'] ?? ''); ?>" 
                        data-owner-phone="<?php echo htmlspecialchars($request['phone'] ?? ''); ?>"
                        data-owner-phone2="<?php echo htmlspecialchars($request['phone_2'] ?? ''); ?>"
                        onclick="showContactModal(this.dataset.ownerName, this.dataset.ownerPhone, this.dataset.ownerPhone2)">Contact Owner</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Accept Request Modal -->
    <div id="acceptModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Accept Breeding Request</h3>
                <button class="modal-close" onclick="closeAcceptModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p class="modal-message">Accept breeding request from <strong id="acceptOwnerName"></strong> for <strong id="acceptPetName"></strong>?</p>
                
                <div class="form-group">
                    <label for="selectBreedingPet">Select Your Breeding Pet *</label>
                    <select id="selectBreedingPet" class="form-control" required>
                        <option value="">Choose a pet...</option>
                        <!-- This will be populated dynamically -->
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeAcceptModal()">Cancel</button>
                <button class="btn btn-primary" onclick="confirmAcceptRequest()">Confirm Accept</button>
            </div>
        </div>
    </div>

    <!-- Decline Request Modal -->
    <div id="declineModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Decline Breeding Request</h3>
                <button class="modal-close" onclick="closeDeclineModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p class="modal-message">Decline breeding request from <strong id="declineOwnerName"></strong> for <strong id="declinePetName"></strong>?</p>
                
                <div class="form-group">
                    <label for="declineReason">Reason (Optional)</label>
                    <textarea id="declineReason" class="form-control" rows="3" placeholder="Enter reason for declining..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeDeclineModal()">Cancel</button>
                <button class="btn btn-danger" onclick="confirmDeclineRequest()">Confirm Decline</button>
            </div>
        </div>
    </div>

    <!-- Mark Complete Modal -->
    <div id="completeModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Mark Breeding as Complete</h3>
                <button class="modal-close" onclick="closeCompleteModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p class="modal-message">Mark breeding for <strong id="completePetName"></strong> (<strong id="completeOwnerName"></strong>) as completed?</p>
                
                <div class="form-group">
                    <label for="completeNotes">Notes (Optional)</label>
                    <textarea id="completeNotes" class="form-control" rows="3" placeholder="Add any final notes about the breeding..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeCompleteModal()">Cancel</button>
                <button class="btn btn-primary" onclick="confirmCompleteRequest()">Confirm Complete</button>
            </div>
        </div>
    </div>

    <!-- Contact Owner Modal -->
    <div id="contactModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Contact Owner</h3>
                <button class="modal-close" onclick="closeContactModal()">&times;</button>
            </div>
            <div class="owner-name" id="modalOwnerName"></div>
            <div class="phone-list" id="phoneList"></div>
        </div>
    </div>

    <script src="<?= asset('js/breeder/requests.js') ?>"></script>
</body>
</html>
