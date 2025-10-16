<?php
// This view expects data from SitterController::bookings()
// Variables available: $pendingBookings, $activeBookings, $completedBookings

if (!isset($pendingBookings)) $pendingBookings = [];
if (!isset($activeBookings)) $activeBookings = [];
if (!isset($completedBookings)) $completedBookings = [];

$module = 'sitter';
$currentPage = 'bookings.php';
$GLOBALS['module'] = $module;
$GLOBALS['currentPage'] = $currentPage;
require_once dirname(__DIR__, 2) . '/config/config.php';
$pageTitle = "Bookings";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - PetVet Sitter</title>
    <!-- sidebar css is injected by the sidebar include; avoid duplicate/incorrect relative links -->
    <link rel="stylesheet" href="<?= asset('css/shared/bookings.css') ?>">
    <style>
        /* Sitter-specific theme colors only */
        .page-header {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            box-shadow: 0 4px 15px rgba(23, 162, 184, 0.3);
        }

        .filter-btn.active {
            background: #17a2b8;
            border-color: #17a2b8;
        }

        .booking-card {
            border-left: 4px solid #17a2b8;
        }

        .booking-date {
            color: #17a2b8;
        }

        .btn-primary {
            background: #17a2b8;
        }

        .btn-outline {
            color: #17a2b8;
            border-color: #17a2b8;
        }

        .btn-outline:hover {
            background: rgba(23, 162, 184, 0.1);
        }

        .call-btn {
            background: #17a2b8;
        }

        .call-btn:hover {
            background: #138496;
        }

        .confirm-btn-confirm {
            background: #17a2b8;
        }

        .confirm-btn-confirm:hover {
            background: #138496;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php include __DIR__ . '/../shared/sidebar/sidebar.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <div>
                <h1>Bookings</h1>
                <p>Manage your pet sitting appointments and requests</p>
            </div>
        </div>

        <div class="booking-filters">
            <div class="filter-btn active" data-filter="pending" onclick="filterBookings('pending')">
                Pending (<?php echo count($pendingBookings); ?>)
            </div>
            <div class="filter-btn" data-filter="confirmed" onclick="filterBookings('confirmed')">
                Confirmed (<?php echo count($activeBookings); ?>)
            </div>
            <div class="filter-btn" data-filter="completed" onclick="filterBookings('completed')">
                Completed (<?php echo count($completedBookings); ?>)
            </div>
        </div>

        <div class="bookings-grid" id="bookingsGrid">
            <!-- Pending Bookings -->
            <?php foreach ($pendingBookings as $booking): ?>
            <div class="booking-card" data-status="pending">
                <div class="booking-header">
                    <div>
                        <div class="booking-title"><?php echo htmlspecialchars($booking['pet_name']); ?> - <?php echo htmlspecialchars($booking['service_type']); ?></div>
                        <div class="booking-date"><?php echo date('M d, Y', strtotime($booking['start_date'])); ?><?php if ($booking['start_date'] != $booking['end_date']): ?> - <?php echo date('M d, Y', strtotime($booking['end_date'])); ?><?php endif; ?></div>
                    </div>
                    <div class="booking-status status-pending">Pending</div>
                </div>
                <div class="booking-details">
                    <div class="detail-item">
                        <span class="detail-icon">👤</span>
                        <span><?php echo htmlspecialchars($booking['owner_name']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">⏰</span>
                        <span><?php echo htmlspecialchars($booking['start_time']); ?> - <?php echo htmlspecialchars($booking['end_time']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon"><?php echo $booking['pet_type'] == 'Dog' ? '🐕' : '🐱'; ?></span>
                        <span><?php echo htmlspecialchars($booking['pet_breed']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">📍</span>
                        <span><?php echo htmlspecialchars($booking['location']); ?></span>
                    </div>
                </div>
                <div class="booking-description">
                    <?php echo htmlspecialchars($booking['special_notes']); ?>
                </div>
                <div class="booking-actions">
                    <button class="btn btn-success" onclick="confirmAction('accept', '<?php echo htmlspecialchars($booking['pet_name']); ?>', <?php echo $booking['id']; ?>)">Accept</button>
                    <button class="btn btn-outline" onclick="confirmAction('decline', '<?php echo htmlspecialchars($booking['pet_name']); ?>', <?php echo $booking['id']; ?>)">Decline</button>
                    <button class="btn btn-outline" onclick="showContactModal('<?php echo htmlspecialchars($booking['owner_name']); ?>', '<?php echo htmlspecialchars($booking['owner_phone']); ?>', '<?php echo isset($booking['owner_phone_2']) ? htmlspecialchars($booking['owner_phone_2']) : ''; ?>')">Contact Owner</button>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- Confirmed Bookings -->
            <?php foreach ($activeBookings as $booking): ?>
            <div class="booking-card" data-status="confirmed" style="display: none;">
                <div class="booking-header">
                    <div>
                        <div class="booking-title"><?php echo htmlspecialchars($booking['pet_name']); ?> - <?php echo htmlspecialchars($booking['service_type']); ?></div>
                        <div class="booking-date"><?php echo date('M d, Y', strtotime($booking['start_date'])); ?><?php if ($booking['start_date'] != $booking['end_date']): ?> - <?php echo date('M d, Y', strtotime($booking['end_date'])); ?><?php endif; ?></div>
                    </div>
                    <div class="booking-status status-confirmed">Confirmed</div>
                </div>
                <div class="booking-details">
                    <div class="detail-item">
                        <span class="detail-icon">👤</span>
                        <span><?php echo htmlspecialchars($booking['owner_name']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">⏰</span>
                        <span><?php echo htmlspecialchars($booking['start_time']); ?> - <?php echo htmlspecialchars($booking['end_time']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon"><?php echo $booking['pet_type'] == 'Dog' ? '🐕' : '🐱'; ?></span>
                        <span><?php echo htmlspecialchars($booking['pet_breed']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">📍</span>
                        <span><?php echo htmlspecialchars($booking['location']); ?></span>
                    </div>
                </div>
                <div class="booking-description">
                    <?php echo htmlspecialchars($booking['special_notes']); ?>
                </div>
                <div class="booking-actions">
                    <button class="btn btn-success" onclick="confirmAction('complete', '<?php echo htmlspecialchars($booking['pet_name']); ?>', <?php echo $booking['id']; ?>)">Mark Complete</button>
                    <button class="btn btn-outline" onclick="showContactModal('<?php echo htmlspecialchars($booking['owner_name']); ?>', '<?php echo htmlspecialchars($booking['owner_phone']); ?>', '<?php echo isset($booking['owner_phone_2']) ? htmlspecialchars($booking['owner_phone_2']) : ''; ?>')">Contact Owner</button>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- Completed Bookings -->
            <?php foreach ($completedBookings as $booking): ?>
            <div class="booking-card" data-status="completed" style="display: none;">
                <div class="booking-header">
                    <div>
                        <div class="booking-title"><?php echo htmlspecialchars($booking['pet_name']); ?> - <?php echo htmlspecialchars($booking['service_type']); ?></div>
                        <div class="booking-date"><?php echo date('M d, Y', strtotime($booking['start_date'])); ?><?php if ($booking['start_date'] != $booking['end_date']): ?> - <?php echo date('M d, Y', strtotime($booking['end_date'])); ?><?php endif; ?></div>
                    </div>
                    <div class="booking-status status-completed">Completed</div>
                </div>
                <div class="booking-details">
                    <div class="detail-item">
                        <span class="detail-icon">👤</span>
                        <span><?php echo htmlspecialchars($booking['owner_name']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">⏰</span>
                        <span><?php echo htmlspecialchars($booking['start_time']); ?> - <?php echo htmlspecialchars($booking['end_time']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon"><?php echo $booking['pet_type'] == 'Dog' ? '🐕' : '🐱'; ?></span>
                        <span><?php echo htmlspecialchars($booking['pet_breed']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">📍</span>
                        <span><?php echo htmlspecialchars($booking['location']); ?></span>
                    </div>
                </div>
                <div class="booking-description">
                    <?php echo htmlspecialchars($booking['special_notes']); ?>
                </div>
                <div class="booking-actions">
                    <button class="btn btn-outline" onclick="showContactModal('<?php echo htmlspecialchars($booking['owner_name'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($booking['owner_phone'], ENT_QUOTES); ?>', '<?php echo isset($booking['owner_phone_2']) ? htmlspecialchars($booking['owner_phone_2'], ENT_QUOTES) : ''; ?>')">Contact Owner</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Contact Modal -->
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

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="confirm-modal-overlay">
        <div class="confirm-modal-content">
            <div class="confirm-modal-header">
                <h3 id="confirmTitle">Confirm Action</h3>
            </div>
            <div class="confirm-modal-message" id="confirmMessage">
                Are you sure you want to perform this action?
            </div>
            <div class="confirm-modal-actions">
                <button class="confirm-btn confirm-btn-cancel" onclick="closeConfirmModal()">Cancel</button>
                <button id="confirmButton" class="confirm-btn confirm-btn-confirm" onclick="executeAction()">Confirm</button>
            </div>
        </div>
    </div>

    <script src="<?= asset('js/shared/bookings.js') ?>"></script>
</body>
</html>