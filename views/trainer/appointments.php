<?php
// This view expects data from TrainerController::appointments()
// Variables available: $pendingRequests, $confirmedSessions, $completedSessions

if (!isset($pendingRequests)) $pendingRequests = [];
if (!isset($confirmedSessions)) $confirmedSessions = [];
if (!isset($completedSessions)) $completedSessions = [];

$module = 'trainer';
$currentPage = 'appointments.php';
$GLOBALS['module'] = $module;
$GLOBALS['currentPage'] = $currentPage;
require_once dirname(__DIR__, 2) . '/config/config.php';
$pageTitle = "Training Appointments";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - PetVet Trainer</title>
    <!-- sidebar css is injected by the sidebar include; avoid duplicate/incorrect relative links -->
    <link rel="stylesheet" href="<?= asset('css/shared/bookings.css') ?>">
    <style>
        /* Trainer-specific theme colors only */
        .page-header {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
        }

        .filter-btn.active {
            background: #8b5cf6;
            border-color: #8b5cf6;
        }

        .booking-card {
            border-left: 4px solid #8b5cf6;
        }

        .booking-date {
            color: #8b5cf6;
        }

        .btn-primary {
            background: #8b5cf6;
        }

        .btn-outline {
            color: #8b5cf6;
            border-color: #8b5cf6;
        }

        .btn-outline:hover {
            background: rgba(139, 92, 246, 0.1);
        }

        .call-btn {
            background: #8b5cf6;
        }

        .call-btn:hover {
            background: #7c3aed;
        }

        .confirm-btn-confirm {
            background: #8b5cf6;
        }

        .confirm-btn-confirm:hover {
            background: #7c3aed;
        }

        /* Training type badges */
        .training-type-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-left: 0.5rem;
        }

        .training-type-basic {
            background: #d1fae5;
            color: #065f46;
        }

        .training-type-intermediate {
            background: #fed7aa;
            color: #92400e;
        }

        .training-type-advanced {
            background: #fecaca;
            color: #991b1b;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php include __DIR__ . '/../shared/sidebar/sidebar.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <div>
                <h1>Training Appointments</h1>
                <p>Manage your training sessions and requests</p>
            </div>
        </div>

        <div class="booking-filters">
            <div class="filter-btn active" data-filter="pending" onclick="filterBookings('pending')">
                Pending (<?php echo count($pendingRequests); ?>)
            </div>
            <div class="filter-btn" data-filter="confirmed" onclick="filterBookings('confirmed')">
                Confirmed (<?php echo count($confirmedSessions); ?>)
            </div>
            <div class="filter-btn" data-filter="completed" onclick="filterBookings('completed')">
                Completed (<?php echo count($completedSessions); ?>)
            </div>
        </div>

        <div class="bookings-grid" id="bookingsGrid">
            <!-- Pending Requests -->
            <?php foreach ($pendingRequests as $request): ?>
            <div class="booking-card" data-status="pending">
                <div class="booking-header">
                    <div>
                        <div class="booking-title">
                            <?php echo htmlspecialchars($request['pet_name']); ?> - Training
                            <span class="training-type-badge training-type-<?php echo strtolower($request['training_type']); ?>">
                                <?php echo htmlspecialchars($request['training_type']); ?>
                            </span>
                        </div>
                        <div class="booking-date"><?php echo date('M d, Y', strtotime($request['preferred_date'])); ?></div>
                    </div>
                    <div class="booking-status status-pending">Pending</div>
                </div>
                <div class="booking-details">
                    <div class="detail-item">
                        <span class="detail-icon">👤</span>
                        <span><?php echo htmlspecialchars($request['pet_owner_name']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">⏰</span>
                        <span><?php echo htmlspecialchars($request['preferred_time']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">🐕</span>
                        <span><?php echo htmlspecialchars($request['pet_breed']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">📍</span>
                        <span><?php echo htmlspecialchars($request['location']); ?></span>
                    </div>
                </div>
                <?php if (!empty($request['additional_notes'])): ?>
                <div class="booking-description">
                    <?php echo htmlspecialchars($request['additional_notes']); ?>
                </div>
                <?php endif; ?>
                <div class="booking-actions">
                    <button class="btn btn-success" onclick="confirmAction('accept', '<?php echo htmlspecialchars($request['pet_name']); ?>', <?php echo $request['request_id']; ?>)">Accept</button>
                    <button class="btn btn-outline" onclick="confirmAction('decline', '<?php echo htmlspecialchars($request['pet_name']); ?>', <?php echo $request['request_id']; ?>)">Decline</button>
                    <button class="btn btn-outline" onclick="showContactModal('<?php echo htmlspecialchars($request['pet_owner_name']); ?>', '<?php echo htmlspecialchars($request['pet_owner_phone']); ?>', '<?php echo isset($request['pet_owner_email']) ? htmlspecialchars($request['pet_owner_email']) : ''; ?>')">Contact Owner</button>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- Confirmed Sessions -->
            <?php foreach ($confirmedSessions as $session): ?>
            <div class="booking-card" data-status="confirmed" style="display: none;">
                <div class="booking-header">
                    <div>
                        <div class="booking-title">
                            <?php echo htmlspecialchars($session['pet_name']); ?> - Training
                            <span class="training-type-badge training-type-<?php echo strtolower($session['training_type']); ?>">
                                <?php echo htmlspecialchars($session['training_type']); ?>
                            </span>
                        </div>
                        <div class="booking-date"><?php echo date('M d, Y', strtotime($session['next_session_date'])); ?></div>
                    </div>
                    <div class="booking-status status-confirmed">Confirmed</div>
                </div>
                <div class="booking-details">
                    <div class="detail-item">
                        <span class="detail-icon">👤</span>
                        <span><?php echo htmlspecialchars($session['pet_owner_name']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">⏰</span>
                        <span><?php echo htmlspecialchars($session['next_session_time']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">🐕</span>
                        <span><?php echo htmlspecialchars($session['pet_breed']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">📍</span>
                        <span><?php echo htmlspecialchars($session['location']); ?></span>
                    </div>
                </div>
                <div class="booking-description">
                    <strong>Sessions Completed:</strong> <?php echo $session['session_number']; ?>
                    <?php if (!empty($session['next_session_goals'])): ?>
                    <br><strong>Today's Goals:</strong> <?php echo htmlspecialchars($session['next_session_goals']); ?>
                    <?php endif; ?>
                </div>
                <div class="booking-actions">
                    <button class="btn btn-success" onclick="showCompleteSessionModal(<?php echo htmlspecialchars(json_encode($session), ENT_QUOTES); ?>)">Complete Session</button>
                    <button class="btn btn-outline" onclick="showSessionHistoryModal(<?php echo $session['session_id']; ?>, '<?php echo htmlspecialchars($session['pet_name'], ENT_QUOTES); ?>')">View Session History</button>
                    <button class="btn btn-outline" onclick="showContactModal('<?php echo htmlspecialchars($session['pet_owner_name']); ?>', '<?php echo htmlspecialchars($session['pet_owner_phone']); ?>', '<?php echo isset($session['pet_owner_email']) ? htmlspecialchars($session['pet_owner_email']) : ''; ?>')">Contact Owner</button>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- Completed Sessions -->
            <?php foreach ($completedSessions as $session): ?>
            <div class="booking-card" data-status="completed" style="display: none;">
                <div class="booking-header">
                    <div>
                        <div class="booking-title">
                            <?php echo htmlspecialchars($session['pet_name']); ?> - Training
                            <span class="training-type-badge training-type-<?php echo strtolower($session['training_type']); ?>">
                                <?php echo htmlspecialchars($session['training_type']); ?>
                            </span>
                        </div>
                        <div class="booking-date">Completed on <?php echo date('M d, Y', strtotime($session['completed_date'])); ?></div>
                    </div>
                    <div class="booking-status status-completed">Completed</div>
                </div>
                <div class="booking-details">
                    <div class="detail-item">
                        <span class="detail-icon">👤</span>
                        <span><?php echo htmlspecialchars($session['pet_owner_name']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">✓</span>
                        <span>Sessions Completed: <?php echo $session['sessions_completed']; ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">🐕</span>
                        <span><?php echo htmlspecialchars($session['pet_breed']); ?></span>
                    </div>
                </div>
                <?php if (!empty($session['final_notes'])): ?>
                <div class="booking-description">
                    <strong>Final Notes:</strong> <?php echo htmlspecialchars($session['final_notes']); ?>
                </div>
                <?php endif; ?>
                <div class="booking-actions">
                    <button class="btn btn-outline" onclick="showSessionHistoryModal(<?php echo $session['session_id']; ?>, '<?php echo htmlspecialchars($session['pet_name'], ENT_QUOTES); ?>')">View Session History</button>
                    <button class="btn btn-outline" onclick="showContactModal('<?php echo htmlspecialchars($session['pet_owner_name'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($session['pet_owner_phone'], ENT_QUOTES); ?>', '<?php echo isset($session['pet_owner_email']) ? htmlspecialchars($session['pet_owner_email'], ENT_QUOTES) : ''; ?>')">Contact Owner</button>
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

    <!-- Complete Session Modal -->
    <div id="completeSessionModal" class="modal-overlay" style="display: none;">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <h3>Complete Training Session</h3>
                <button class="modal-close" onclick="closeCompleteSessionModal()">&times;</button>
            </div>
            <form id="completeSessionForm" style="padding-top: 1rem;">
                <input type="hidden" id="sessionId" name="session_id">
                
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Session Notes *</label>
                    <textarea id="sessionNotes" name="notes" rows="6" placeholder="Record observations, behavior changes, commands practiced, special notes about the dog, challenges faced, improvements noticed, etc." required style="width: 100%; padding: 0.625rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.875rem; box-sizing: border-box; resize: vertical;"></textarea>
                    <small style="display: block; margin-top: 0.25rem; font-size: 0.75rem; color: #6b7280;">Document anything important about this session that will help you in future training.</small>
                </div>

                <div id="nextSessionSection">
                    <div style="font-weight: 600; color: #374151; margin: 1rem 0 0.75rem 0; padding-bottom: 0.5rem; border-bottom: 2px solid #e5e7eb;">Schedule Next Session (Optional)</div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Next Session Date</label>
                            <input type="date" id="nextSessionDate" name="next_session_date" style="width: 100%; padding: 0.625rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.875rem; box-sizing: border-box;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Time</label>
                            <input type="time" id="nextSessionTime" name="next_session_time" style="width: 100%; padding: 0.625rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.875rem; box-sizing: border-box;">
                        </div>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Next Session Goals</label>
                        <textarea id="nextSessionGoals" name="next_session_goals" rows="3" placeholder="What will you focus on in the next session?" style="width: 100%; padding: 0.625rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.875rem; box-sizing: border-box; resize: vertical;"></textarea>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem; background: rgba(139, 92, 246, 0.1); border-radius: 6px; margin-bottom: 1rem;">
                    <input type="checkbox" id="markProgramComplete" name="mark_program_complete" style="width: 18px; height: 18px;">
                    <label for="markProgramComplete" style="margin: 0; font-weight: 500;">Mark entire training program as complete</label>
                </div>

                <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Save & Complete</button>
                    <button type="button" class="btn btn-outline" onclick="closeCompleteSessionModal()" style="flex: 1;">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Session History Modal -->
    <div id="sessionHistoryModal" class="modal-overlay" style="display: none;">
        <div class="modal-content" style="max-width: 700px; max-height: 90vh; overflow-y: auto;">
            <div class="modal-header">
                <h3 id="historyModalTitle">Session History</h3>
                <button class="modal-close" onclick="closeSessionHistoryModal()">&times;</button>
            </div>
            <div id="sessionHistoryContent" style="padding-top: 1rem;">
                <div style="text-align: center; padding: 2rem; color: #6b7280;">
                    <p>Loading session history...</p>
                </div>
            </div>
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

    <script src="<?= asset('js/trainer/appointments.js') ?>"></script>
</body>
</html>