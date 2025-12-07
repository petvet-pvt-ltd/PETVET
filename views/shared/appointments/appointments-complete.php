<?php
/**
 * Complete Shared Appointments View Component
 * Used by both Clinic Manager and Receptionist
 * Contains appointment views and modals (following MVC - no data logic here)
 */

// Ensure required variables are set by the controller
if (!isset($appointments) || !isset($vetNames) || !isset($view) || !isset($moduleName)) {
    throw new Exception('Required appointment data not provided by controller');
}
?>

<!-- Calendar Controls -->
<div class="calendar-controls">
    <div class="view-toggle" role="tablist" aria-label="Calendar View Modes">
        <button class="toggle-pill <?= $view==='today'?'active':'' ?>" id="btn-today" onclick="showCalendarView('today')">Today</button>
        <button class="toggle-pill <?= $view==='week'?'active':'' ?>" id="btn-week" onclick="showCalendarView('week')">Week</button>
        <button class="toggle-pill <?= $view==='month'?'active':'' ?>" id="btn-month" onclick="showCalendarView('month')">Month</button>
    </div>
    
    <form method="get" class="vet-filter" id="vetFilterForm">
        <input type="hidden" name="module" value="<?= htmlspecialchars($moduleName) ?>" />
        <input type="hidden" name="page" value="appointments" />
        <input type="hidden" name="view" id="currentViewInput" value="<?= htmlspecialchars($view) ?>" />
        <label for="vetSelect" style="font-weight: 600; color: var(--gray-700); margin-right: 8px;">Filter by Vet:</label>
        <select id="vetSelect" name="vet" class="select" style="min-width: 160px;" aria-label="Filter by vet">
            <option value="all" <?= $selectedVet==='all'?'selected':''; ?>>All Vets</option>
            <?php foreach($vetNames as $vet): ?>
                <option value="<?= htmlspecialchars($vet['vet_name']); ?>" <?= $selectedVet===$vet['vet_name']?'selected':''; ?>><?= htmlspecialchars($vet['vet_name']); ?></option>
            <?php endforeach; ?>
        </select>
        <?php if($selectedVet !== 'all'): ?>
            <a class="btn btn-ghost btn-sm" href="/PETVET/index.php?module=<?= htmlspecialchars($moduleName) ?>&page=appointments&vet=all" title="Clear filter">Clear</a>
        <?php endif; ?>
    </form>
</div>

<!-- Today View -->
<div class="calendar-view <?= $view==='today'?'active':'' ?>" id="calendar-today">
    <div class="calendar" style="grid-template-columns: 1fr;">
        <div class="day-col today-col">
            <div class="day-date-stripe">
                <?= date('M j - D') ?>
            </div>
            <div style="margin-top: 8px;">
            <?php
                $todayStr = (new DateTime())->format('Y-m-d');
                if (!empty($appointments[$todayStr])):
                    foreach ($appointments[$todayStr] as $appt):
            ?>
                <div class="event"
                     data-id="<?= $appt['id'] ?>"
                     data-pet="<?= htmlspecialchars($appt['pet']) ?>"
                     data-animal="<?= htmlspecialchars($appt['animal']) ?>"
                     data-client="<?= htmlspecialchars($appt['client']) ?>"
                     data-vet="<?= htmlspecialchars($appt['vet']) ?>"
                     data-type="<?= htmlspecialchars($appt['type']) ?>"
                     data-phone="<?= htmlspecialchars($appt['client_phone'] ?? 'N/A') ?>"
                     data-date="<?= $todayStr ?>"
                     data-time="<?= $appt['time'] ?>"
                     onclick="openDetailsFromEl(this)">
                    <span class="evt-time"><?= date('g:i A', strtotime($appt['time'])) ?></span>
                    <span class="evt-client"><?= htmlspecialchars($appt['client']) ?></span>
                    <span class="evt-vet"><?= htmlspecialchars($appt['vet']) ?></span>
                </div>
            <?php
                    endforeach;
                else:
            ?>
                <div style="color:#64748b; font-size:15px; text-align:center; margin-top:18px;">No appointments for today.</div>
            <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Week View -->
<div class="calendar-view <?= $view==='week'?'active':'' ?>" id="calendar-week">
    <div class="calendar">
        <?php foreach ($weekDays as $i => $dateObj):
            $dateStr = $dateObj->format('Y-m-d');
            $isToday = ($i === 0);
        ?>
        <div class="day-col<?= $isToday ? ' today-col' : '' ?>">
            <div class="day-date-stripe">
                <?= $dateObj->format('M j - D') ?>
            </div>
            <div class="day-appointments-container" data-day-title="<?= $dateObj->format('l, F j, Y') ?>">
                <?php if (!empty($appointments[$dateStr])): ?>
                    <?php foreach ($appointments[$dateStr] as $appt): 
                        $vetFirstName = explode(' ', $appt['vet'])[0];
                    ?>
                        <div class="event"
                             data-id="<?= $appt['id'] ?>"
                             data-pet="<?= htmlspecialchars($appt['pet']) ?>"
                             data-animal="<?= htmlspecialchars($appt['animal']) ?>"
                             data-client="<?= htmlspecialchars($appt['client']) ?>"
                             data-vet="<?= htmlspecialchars($appt['vet']) ?>"
                             data-type="<?= htmlspecialchars($appt['type']) ?>"
                             data-phone="<?= htmlspecialchars($appt['client_phone'] ?? 'N/A') ?>"
                             data-date="<?= $dateStr ?>"
                             data-time="<?= $appt['time'] ?>"
                             onclick="openDetailsFromEl(this)">
                            <span class="evt-compact">
                                <span class="evt-time"><?= date('g:i A', strtotime($appt['time'])) ?></span>
                                <span class="evt-vet-short"><?= htmlspecialchars($vetFirstName) ?></span>
                            </span>
                            <div class="evt-expanded">
                                <div class="evt-expanded-row">
                                    <span class="evt-label">Time:</span>
                                    <span class="evt-value"><?= date('g:i A', strtotime($appt['time'])) ?></span>
                                </div>
                                <div class="evt-expanded-row">
                                    <span class="evt-label">Vet:</span>
                                    <span class="evt-value"><?= htmlspecialchars($appt['vet']) ?></span>
                                </div>
                                <div class="evt-expanded-row">
                                    <span class="evt-label">Client:</span>
                                    <span class="evt-value"><?= htmlspecialchars($appt['client']) ?></span>
                                </div>
                                <div class="evt-expanded-row">
                                    <span class="evt-label">Pet:</span>
                                    <span class="evt-value"><?= htmlspecialchars($appt['pet']) ?> (<?= htmlspecialchars($appt['animal']) ?>)</span>
                                </div>
                                <div class="evt-expanded-row">
                                    <span class="evt-label">Type:</span>
                                    <span class="evt-value"><?= htmlspecialchars($appt['type']) ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Month View -->
<div class="calendar-view <?= $view==='month'?'active':'' ?>" id="calendar-month">
    <div style="display: flex; flex-direction: column; gap: 16px;">
        <?php foreach ($monthDays as $week): ?>
            <div class="calendar" style="margin-bottom:0;">
                <?php foreach ($week as $i => $dateObj):
                    $dateStr = $dateObj->format('Y-m-d');
                    $isToday = ($dateStr === (new DateTime())->format('Y-m-d'));
                ?>
                <div class="day-col<?= $isToday ? ' today-col' : '' ?>">
                    <div class="day-date-stripe">
                        <?= $dateObj->format('M j - D') ?>
                    </div>
                    <div class="day-appointments-container" data-day-title="<?= $dateObj->format('l, F j, Y') ?>">
                        <?php if (!empty($appointments[$dateStr])): ?>
                            <?php foreach ($appointments[$dateStr] as $appt): 
                                $vetFirstName = explode(' ', $appt['vet'])[0];
                            ?>
                                <div class="event"
                                     data-id="<?= $appt['id'] ?>"
                                     data-pet="<?= htmlspecialchars($appt['pet']) ?>"
                                     data-animal="<?= htmlspecialchars($appt['animal']) ?>"
                                     data-client="<?= htmlspecialchars($appt['client']) ?>"
                                     data-vet="<?= htmlspecialchars($appt['vet']) ?>"
                                     data-type="<?= htmlspecialchars($appt['type']) ?>"
                                     data-phone="<?= htmlspecialchars($appt['client_phone'] ?? 'N/A') ?>"
                                     data-date="<?= $dateStr ?>"
                                     data-time="<?= $appt['time'] ?>"
                                     onclick="openDetailsFromEl(this)">
                                    <span class="evt-compact">
                                        <span class="evt-time"><?= date('g:i A', strtotime($appt['time'])) ?></span>
                                        <span class="evt-vet-short"><?= htmlspecialchars($vetFirstName) ?></span>
                                    </span>
                                    <div class="evt-expanded">
                                        <div class="evt-expanded-row">
                                            <span class="evt-label">Time:</span>
                                            <span class="evt-value"><?= date('g:i A', strtotime($appt['time'])) ?></span>
                                        </div>
                                        <div class="evt-expanded-row">
                                            <span class="evt-label">Vet:</span>
                                            <span class="evt-value"><?= htmlspecialchars($appt['vet']) ?></span>
                                        </div>
                                        <div class="evt-expanded-row">
                                            <span class="evt-label">Client:</span>
                                            <span class="evt-value"><?= htmlspecialchars($appt['client']) ?></span>
                                        </div>
                                        <div class="evt-expanded-row">
                                            <span class="evt-label">Pet:</span>
                                            <span class="evt-value"><?= htmlspecialchars($appt['pet']) ?> (<?= htmlspecialchars($appt['animal']) ?>)</span>
                                        </div>
                                        <div class="evt-expanded-row">
                                            <span class="evt-label">Type:</span>
                                            <span class="evt-value"><?= htmlspecialchars($appt['type']) ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php
// =====================================================
// APPOINTMENT MODALS
// =====================================================
?>

<!-- Details Modal -->
<div id="detailsModal" class="modal hidden">
    <div class="modal-content">
        <span class="close" onclick="closeModal('detailsModal')">&times;</span>
        <h3>Appointment Details</h3>
        <p><strong>Pet:</strong> <span id="dPet"></span></p>
        <p><strong>Species:</strong> <span id="dSpecies"></span></p>
        <p><strong>Client:</strong> <span id="dClient"></span></p>
        <p><strong>Type:</strong> <span id="dType"></span></p>
        <p><strong>Phone:</strong> <span id="dPhone"></span></p>
        <div class="form-group">
            <label>Veterinarian</label>
            <select id="dVet" class="select">
                <?php foreach($vetNames as $vet): ?>
                    <option value="<?= htmlspecialchars($vet['vet_name']); ?>"><?= htmlspecialchars($vet['vet_name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="input-row">
            <div>
                <label>Date</label>
                <input type="date" id="dDate">
            </div>
            <div>
                <label>Time</label>
                <input type="time" id="dTime">
            </div>
        </div>
        <div class="modal-actions">
            <button class="btn btn-primary" id="rescheduleBtn" onclick="rescheduleAppointment()" disabled style="opacity: 0.5; cursor: not-allowed;">
                Reschedule
            </button>
            <button class="btn btn-danger" onclick="cancelAppointment()">
                Cancel Appointment
            </button>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div id="addModal" class="modal hidden">
    <div class="modal-content receptionist-booking-modal">
        <span class="close" onclick="closeModal('addModal')">&times;</span>
        <h3>Add New Appointment</h3>
        
        <!-- Step 1: Appointment Type & Vet Selection -->
        <div class="form-section" id="receptionistVetSection">
            <h4 class="section-title">Step 1: Appointment Details</h4>
            <div class="form-group">
                <label>Appointment Type</label>
                <select id="newAppointmentType" class="select" required>
                    <option value="">Select type</option>
                    <option value="routine">Routine Check-up</option>
                    <option value="vaccination">Vaccination</option>
                    <option value="dental">Dental Cleaning</option>
                    <option value="illness">Illness/Injury</option>
                    <option value="emergency">Emergency</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label>Veterinarian</label>
                <select id="newVetName" class="select" required>
                    <option value="">Choose a veterinarian</option>
                    <?php foreach($vetNames as $vet): ?>
                        <option value="<?= htmlspecialchars($vet['id']); ?>"><?= htmlspecialchars($vet['vet_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Step 2: Date Selection -->
        <div class="form-section" id="receptionistDateSection" style="display:none;">
            <h4 class="section-title">Step 2: Select Date</h4>
            <div id="receptionistCalendarWidget" class="calendar-widget">
                <!-- Calendar will be generated here -->
            </div>
            <input type="hidden" id="newDate" required>
            <p style="margin:12px 0 0; font-size:11px; color:#64748b;">
                📅 Available dates: Next 30 days (excludes clinic closed days and blocked dates)
            </p>
        </div>

        <!-- Step 3: Time Selection -->
        <div class="form-section" id="receptionistTimeSection" style="display:none;">
            <h4 class="section-title">Step 3: Select Time</h4>
            <div id="receptionistTimeSlotsGrid" class="time-slots-grid">
                <!-- Time slots will be loaded dynamically -->
            </div>
            <input type="hidden" id="newTime" required>
            <p style="margin:12px 0 0; font-size:11px; color:#64748b;">
                ⏱️ Slot duration: 20 minutes | Times show only available slots
            </p>
        </div>

        <!-- Step 4: Pet and Client Details -->
        <div class="form-section" id="receptionistDetailsSection" style="display:none;">
            <h4 class="section-title">Step 4: Pet & Client Details</h4>
            <div class="form-group">
                <label>Pet Name</label>
                <input type="text" id="newPetName" placeholder="Enter pet name" required>
            </div>
            <div class="form-group">
                <label>Client Name</label>
                <input type="text" id="newClientName" placeholder="Enter client name" required>
            </div>
        </div>

        <div class="modal-actions">
            <button class="btn btn-secondary" onclick="closeModal('addModal')">
                Cancel
            </button>
            <button class="btn btn-primary" id="saveAppointmentBtn" onclick="saveAppointment()" disabled>
                Save Appointment
            </button>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmModal" class="modal hidden">
    <div class="modal-content confirmation-modal">
        <div class="confirmation-header">
            <h3 id="confirmTitle">Confirm Action</h3>
        </div>
        <div class="confirmation-body">
            <p id="confirmMessage">Are you sure you want to proceed?</p>
        </div>
        <div class="modal-actions">
            <button class="btn btn-secondary" onclick="closeConfirmation()">
                Cancel
            </button>
            <button class="btn btn-primary" id="confirmButton" onclick="executeConfirmedAction()">
                Confirm
            </button>
        </div>
    </div>
</div>

<!-- Error/Success Modal -->
<div id="notificationModal" class="modal hidden">
    <div class="modal-content notification-modal">
        <div class="notification-header">
            <h3 id="notificationTitle">Notification</h3>
        </div>
        <div class="notification-body">
            <p id="notificationMessage">Action completed successfully</p>
        </div>
        <div class="modal-actions">
            <button class="btn btn-primary" onclick="closeNotification()">
                OK
            </button>
        </div>
    </div>
</div>