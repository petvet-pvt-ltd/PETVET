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

function format_sitter_date_range(?string $start, ?string $end): string {
    $start = trim((string)$start);
    $end = trim((string)$end);
    if ($start === '') return '';
    if ($end === '' || $end === $start) {
        return date('M j, Y', strtotime($start));
    }

    $sd = new DateTime($start);
    $ed = new DateTime($end);

    // Inclusive day count for multi-day bookings
    $dayCount = null;
    try {
        $diffDays = (int)$sd->diff($ed)->days;
        $dayCount = $diffDays + 1;
    } catch (Throwable $e) {
        $dayCount = null;
    }

    $suffix = ($dayCount && $dayCount > 1) ? ' (' . $dayCount . ' days)' : '';

    if ($sd->format('Y') === $ed->format('Y') && $sd->format('m') === $ed->format('m')) {
        // Same month/year: "Feb 2–5, 2026"
        return $sd->format('M j') . '–' . $ed->format('j, Y') . $suffix;
    }
    if ($sd->format('Y') === $ed->format('Y')) {
        // Same year: "Feb 28 – Mar 2, 2026"
        return $sd->format('M j') . ' – ' . $ed->format('M j, Y') . $suffix;
    }
    // Different years
    return $sd->format('M j, Y') . ' – ' . $ed->format('M j, Y') . $suffix;
}

function format_sitter_time_range($startTime, $endTime): string {
    $startTime = trim((string)$startTime);
    $endTime = trim((string)$endTime);

    if ($startTime === '' && $endTime === '') return '';

    // If start_time already contains a range (e.g., "9:00 AM - 6:00 PM"), don't duplicate.
    if ($startTime !== '' && (strpos($startTime, '-') !== false || ($endTime !== '' && stripos($startTime, $endTime) !== false))) {
        return $startTime;
    }

    if ($startTime !== '' && $endTime !== '') {
        return $startTime . ' - ' . $endTime;
    }

    return $startTime !== '' ? $startTime : $endTime;
}
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

        .confirm-btn-cancel {
            background: #fff;
            color: #17a2b8;
            border: 2px solid #17a2b8;
        }

        .confirm-btn-cancel:hover {
            background: #138496;
            border-color: #138496;
            color: #fff;
        }

        .confirm-reason-group {
            margin-top: 12px;
            text-align: left;
        }

        .confirm-reason-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 6px;
            color: #374151;
        }

        .confirm-reason-input {
            width: 100%;
            min-height: 70px;
            padding: 10px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            resize: vertical;
            outline: none;
            background: #fff;
        }

        .confirm-reason-input:focus {
            border-color: rgba(23, 162, 184, 0.7);
            box-shadow: 0 0 0 3px rgba(23, 162, 184, 0.15);
        }

        #confirmModal .confirm-modal-actions {
            margin-top: 16px;
        }

        /* Distance badge (same visual style used on Services page) */
        .booking-status-wrap {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .clinic-item-distance{
            display:inline-flex;
            align-items:center;
            gap:0.35rem;
            font-size:0.8rem;
            color:#3b82f6;
            font-weight:600;
            background:#eff6ff;
            padding:0.35rem 0.7rem;
            border-radius:6px;
            border:1px solid #bfdbfe;
            box-shadow:0 1px 2px rgba(59, 130, 246, 0.1);
            white-space:nowrap
        }

        .clinic-item-distance svg{
            color:#3b82f6;
            flex-shrink:0;
        }

        .map-nav-btn{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            width:28px;
            height:28px;
            border-radius:6px;
            border:1px solid #d1d5db;
            background:#fff;
            margin-left:0.5rem;
            text-decoration:none;
        }

        .map-nav-btn:hover{
            background:#f9fafb;
        }

        .map-nav-btn svg{
            width:16px;
            height:16px;
            color:#374151;
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
            <div class="booking-card" data-status="pending" data-booking-id="<?php echo (int)$booking['id']; ?>">
                <div class="booking-header">
                    <div>
                        <div class="booking-title"><?php echo htmlspecialchars($booking['pet_name']); ?> - <?php echo htmlspecialchars($booking['service_type']); ?></div>
                        <div class="booking-date"><?php echo htmlspecialchars(format_sitter_date_range($booking['start_date'] ?? '', $booking['end_date'] ?? '')); ?></div>
                    </div>
                    <div class="booking-status-wrap">
                        <?php if (!empty($booking['distance_km'])): ?>
                            <span class="clinic-item-distance" title="Distance from your location">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M21 10c0 6-9 13-9 13S3 16 3 10a9 9 0 0 1 18 0Z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                <?php echo htmlspecialchars($booking['distance_km']); ?> km
                            </span>
                        <?php endif; ?>
                        <div class="booking-status status-pending">Pending</div>
                    </div>
                </div>
                <div class="booking-details">
                    <div class="detail-item">
                        <span class="detail-icon">👤</span>
                        <span><?php echo htmlspecialchars($booking['owner_name']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">⏰</span>
                        <span><?php echo htmlspecialchars(format_sitter_time_range($booking['start_time'] ?? '', $booking['end_time'] ?? '')); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon"><?php echo $booking['pet_type'] == 'Dog' ? '🐕' : '🐱'; ?></span>
                        <span><?php echo htmlspecialchars($booking['pet_breed']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">📍</span>
                        <span>
                            <?php echo htmlspecialchars($booking['location']); ?>
                            <?php if (!empty($booking['location_lat']) && !empty($booking['location_lng'])): ?>
                                <a class="map-nav-btn" target="_blank" rel="noopener"
                                   href="https://www.google.com/maps?q=<?php echo urlencode($booking['location_lat'] . ',' . $booking['location_lng']); ?>"
                                   title="Open in Google Maps">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M21 10c0 6-9 13-9 13S3 16 3 10a9 9 0 0 1 18 0Z"></path>
                                        <circle cx="12" cy="10" r="3"></circle>
                                    </svg>
                                </a>
                            <?php endif; ?>
                        </span>
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
            <div class="booking-card" data-status="confirmed" data-booking-id="<?php echo (int)$booking['id']; ?>" style="display: none;">
                <div class="booking-header">
                    <div>
                        <div class="booking-title"><?php echo htmlspecialchars($booking['pet_name']); ?> - <?php echo htmlspecialchars($booking['service_type']); ?></div>
                        <div class="booking-date"><?php echo htmlspecialchars(format_sitter_date_range($booking['start_date'] ?? '', $booking['end_date'] ?? '')); ?></div>
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
                        <span><?php echo htmlspecialchars(format_sitter_time_range($booking['start_time'] ?? '', $booking['end_time'] ?? '')); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon"><?php echo $booking['pet_type'] == 'Dog' ? '🐕' : '🐱'; ?></span>
                        <span><?php echo htmlspecialchars($booking['pet_breed']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">📍</span>
                        <span>
                            <?php echo htmlspecialchars($booking['location']); ?>
                            <?php if (!empty($booking['location_lat']) && !empty($booking['location_lng'])): ?>
                                <a class="map-nav-btn" target="_blank" rel="noopener"
                                   href="https://www.google.com/maps?q=<?php echo urlencode($booking['location_lat'] . ',' . $booking['location_lng']); ?>"
                                   title="Open in Google Maps">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M21 10c0 6-9 13-9 13S3 16 3 10a9 9 0 0 1 18 0Z"></path>
                                        <circle cx="12" cy="10" r="3"></circle>
                                    </svg>
                                </a>
                            <?php endif; ?>
                        </span>
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
            <div class="booking-card" data-status="completed" data-booking-id="<?php echo (int)$booking['id']; ?>" style="display: none;">
                <div class="booking-header">
                    <div>
                        <div class="booking-title"><?php echo htmlspecialchars($booking['pet_name']); ?> - <?php echo htmlspecialchars($booking['service_type']); ?></div>
                        <div class="booking-date"><?php echo htmlspecialchars(format_sitter_date_range($booking['start_date'] ?? '', $booking['end_date'] ?? '')); ?></div>
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
                        <span><?php echo htmlspecialchars(format_sitter_time_range($booking['start_time'] ?? '', $booking['end_time'] ?? '')); ?></span>
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
                    <button class="btn btn-outline" 
                        data-owner-name="<?php echo htmlspecialchars($booking['owner_name'] ?? ''); ?>" 
                        data-owner-phone="<?php echo htmlspecialchars($booking['owner_phone'] ?? ''); ?>"
                        data-owner-phone2="<?php echo htmlspecialchars($booking['owner_phone_2'] ?? ''); ?>"
                        onclick="showContactModal(this.dataset.ownerName, this.dataset.ownerPhone, this.dataset.ownerPhone2)">Contact Owner</button>
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
            <div id="declineReasonGroup" class="confirm-reason-group" style="display:none;">
                <label for="declineReasonInput" class="confirm-reason-label">Reason (Optional)</label>
                <textarea id="declineReasonInput" class="confirm-reason-input" rows="3" placeholder="Enter reason for declining..."></textarea>
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