<?php
// Session is already started in index.php
$_SESSION['current_role'] = 'trainer';
$module = 'trainer';
$currentPage = 'availability.php';
$GLOBALS['module'] = $module;
$GLOBALS['currentPage'] = $currentPage;
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once dirname(__DIR__, 2) . '/config/connect.php';

// Make database connection available
global $conn;

// Load weekly schedule from database
$weeklySchedule = [];
$user_id = $_SESSION['user_id'];
$role_type = 'trainer';

$stmt = $conn->prepare("
    SELECT day_of_week, is_available, start_time, end_time
    FROM service_provider_weekly_schedule
    WHERE user_id = ? AND role_type = ?
    ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')
");
$stmt->bind_param('is', $user_id, $role_type);
$stmt->execute();
$result = $stmt->get_result();

$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
$existing = [];
while ($row = $result->fetch_assoc()) {
    $existing[$row['day_of_week']] = [
        'day' => $row['day_of_week'],
        'enabled' => (bool)$row['is_available'],
        'start' => substr($row['start_time'], 0, 5),
        'end' => substr($row['end_time'], 0, 5)
    ];
}

// Return all days with defaults for missing days
foreach ($days as $day) {
    if (isset($existing[$day])) {
        $weeklySchedule[] = $existing[$day];
    } else {
        // Default schedule
        $weeklySchedule[] = [
            'day' => $day,
            'enabled' => in_array($day, ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']),
            'start' => $day === 'Saturday' ? '10:00' : '09:00',
            'end' => $day === 'Saturday' ? '16:00' : '18:00'
        ];
    }
}

// Load blocked dates from database
$unavailableDates = [];
$stmt = $conn->prepare("
    SELECT blocked_date, block_type, block_time, reason
    FROM service_provider_blocked_dates
    WHERE user_id = ? AND role_type = ?
    ORDER BY blocked_date ASC
");
$stmt->bind_param('is', $user_id, $role_type);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $unavailableDates[] = [
        'date' => $row['blocked_date'],
        'type' => $row['block_type'],
        'time' => $row['block_time'] ? substr($row['block_time'], 0, 5) : null,
        'reason' => $row['reason']
    ];
}

$pageTitle = "Availability";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - PetVet Trainer</title>
    <link rel="stylesheet" href="<?= asset('css/trainer/availability.css') ?>">
</head>
<body>
    <?php include __DIR__ . '/../shared/sidebar/sidebar.php'; ?>

    <main class="main-content">
        <div class="page-wrap">
            <!-- Header -->
            <div class="availability-header">
                <div>
                    <h1>Availability Management</h1>
                    <p class="muted">Configure your training schedule and time-off dates</p>
                </div>
                <nav class="quick-nav">
                    <a href="#section-schedule" class="active">Weekly Schedule</a>
                    <a href="#section-blocked">Blocked Dates</a>
                </nav>
            </div>

            <!-- Weekly Schedule Section -->
            <section class="card" id="section-schedule">
                <div class="card-head">
                    <h2>🕒 Weekly Schedule</h2>
                    <button class="btn primary small" id="applyToAll">Apply to All Days</button>
                </div>
                <div class="card-body">
                    <p class="section-desc">Set your default working hours for each day of the week</p>
                    <div class="schedule-grid">
                        <?php foreach ($weeklySchedule as $index => $day): ?>
                        <div class="schedule-row" data-day="<?= $day['day'] ?>">
                            <div class="day-toggle">
                                <label class="toggle">
                                    <input type="checkbox" <?= $day['enabled'] ? 'checked' : '' ?> 
                                           data-day-index="<?= $index ?>" 
                                           class="day-checkbox">
                                    <span class="toggle-track">
                                        <span class="toggle-handle"></span>
                                    </span>
                                    <span class="day-name"><?= $day['day'] ?></span>
                                </label>
                            </div>
                            <div class="time-controls <?= !$day['enabled'] ? 'disabled' : '' ?>">
                                <div class="time-input-group">
                                    <label>Start Time</label>
                                    <input type="time" 
                                           value="<?= $day['start'] ?>" 
                                           class="time-input start-time"
                                           data-day-index="<?= $index ?>"
                                           <?= !$day['enabled'] ? 'disabled' : '' ?>>
                                </div>
                                <span class="time-separator">to</span>
                                <div class="time-input-group">
                                    <label>End Time</label>
                                    <input type="time" 
                                           value="<?= $day['end'] ?>" 
                                           class="time-input end-time"
                                           data-day-index="<?= $index ?>"
                                           <?= !$day['enabled'] ? 'disabled' : '' ?>>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="actions">
                        <button class="btn outline" id="resetSchedule">Reset to Default</button>
                        <button class="btn primary" id="saveSchedule">Save Schedule</button>
                    </div>
                </div>
            </section>

            <!-- Blocked Dates Section -->
            <section class="card" id="section-blocked">
                <div class="card-head">
                    <h2>🚫 Blocked Dates</h2>
                </div>
                <div class="card-body">
                    <p class="section-desc">Manage specific dates when you're unavailable (vacations, holidays, etc.)</p>
                    
                    <div class="blocked-dates-container">
                        <div class="add-blocked-date">
                            <div class="input-group">
                                <label>Select Date to Block</label>
                                <input type="date" id="newBlockedDate" min="<?= date('Y-m-d') ?>">
                            </div>
                            
                            <div class="input-group">
                                <label>Block Type</label>
                                <select id="blockType">
                                    <option value="full-day">Full Day Unavailable</option>
                                    <option value="before">Available Only Before This Time</option>
                                    <option value="after">Available Only After This Time</option>
                                </select>
                            </div>

                            <div class="input-group" id="timeInputGroup" style="display: none;">
                                <label id="timeLabel">Time</label>
                                <input type="time" id="blockTime">
                            </div>

                            <div class="input-group">
                                <label>Reason (Optional)</label>
                                <input type="text" id="blockReason" placeholder="e.g., Vacation, Doctor Appointment">
                            </div>
                            
                            <button class="btn primary" id="addBlockedDate">
                                <span>+ Block Date</span>
                            </button>
                        </div>

                        <div class="blocked-dates-list" id="blockedDatesList">
                            <h3>Currently Blocked Dates</h3>
                            <?php if (empty($unavailableDates)): ?>
                                <div class="empty-state">
                                    <p>No blocked dates yet. Add dates when you're unavailable.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($unavailableDates as $blockedDate): ?>
                                <?php
                                    $typeText = '🚫 Full Day Unavailable';
                                    if ($blockedDate['type'] === 'before') {
                                        $typeText = '⏰ Available Before ' . date('g:i A', strtotime($blockedDate['time']));
                                    } elseif ($blockedDate['type'] === 'after') {
                                        $typeText = '⏰ Available After ' . date('g:i A', strtotime($blockedDate['time']));
                                    }
                                ?>
                                <div class="blocked-date-item" data-date="<?= $blockedDate['date'] ?>" data-type="<?= $blockedDate['type'] ?>" <?= $blockedDate['time'] ? 'data-time="'.$blockedDate['time'].'"' : '' ?>>
                                    <div class="blocked-date-info">
                                        <span class="blocked-date-value"><?= date('F j, Y', strtotime($blockedDate['date'])) ?></span>
                                        <span class="blocked-date-type"><?= $typeText ?></span>
                                        <span class="blocked-date-reason"><?= htmlspecialchars($blockedDate['reason'] ?? 'Personal') ?></span>
                                    </div>
                                    <button class="btn-remove" onclick="removeBlockedDate('<?= $blockedDate['date'] ?>')">
                                        <span>✕</span>
                                    </button>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>


        </div>
    </main>

    <!-- Toast Notification -->
    <div class="toast" id="toast"></div>

    <script src="<?= asset('js/trainer/availability.js') ?>"></script>
</body>
</html>
