// Sitter Availability Management JavaScript

// State management
let weeklySchedule = [];
let blockedDates = [];

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeWeeklySchedule();
    initializeBlockedDates();
    setupQuickNav();
});

// ========== Weekly Schedule Functions ==========

function initializeWeeklySchedule() {
    // Load schedule from DOM
    const scheduleRows = document.querySelectorAll('.schedule-row');
    scheduleRows.forEach((row, index) => {
        const dayName = row.getAttribute('data-day');
        const checkbox = row.querySelector('.day-checkbox');
        const startTime = row.querySelector('.start-time');
        const endTime = row.querySelector('.end-time');

        weeklySchedule.push({
            day: dayName,
            enabled: checkbox.checked,
            start: startTime.value,
            end: endTime.value
        });

        // Event listeners
        checkbox.addEventListener('change', (e) => {
            weeklySchedule[index].enabled = e.target.checked;
            const timeControls = row.querySelector('.time-controls');
            const timeInputs = timeControls.querySelectorAll('.time-input');
            
            if (e.target.checked) {
                timeControls.classList.remove('disabled');
                timeInputs.forEach(input => input.disabled = false);
            } else {
                timeControls.classList.add('disabled');
                timeInputs.forEach(input => input.disabled = true);
            }
            
            renderCalendar(); // Refresh calendar to reflect changes
        });

        startTime.addEventListener('change', (e) => {
            weeklySchedule[index].start = e.target.value;
        });

        endTime.addEventListener('change', (e) => {
            weeklySchedule[index].end = e.target.value;
        });
    });

    // Apply to all days button
    document.getElementById('applyToAll').addEventListener('click', applyToAllDays);

    // Save schedule button
    document.getElementById('saveSchedule').addEventListener('click', saveSchedule);

    // Reset schedule button
    document.getElementById('resetSchedule').addEventListener('click', resetSchedule);
}

function getScheduleForDay(dayOfWeek) {
    const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    const dayName = days[dayOfWeek];
    return weeklySchedule.find(s => s.day === dayName);
}

function applyToAllDays() {
    // Get Monday's schedule as template
    const mondaySchedule = weeklySchedule.find(s => s.day === 'Monday');
    if (!mondaySchedule) return;

    const confirmed = confirm(`Apply Monday's schedule (${mondaySchedule.start} - ${mondaySchedule.end}) to all days?`);
    if (!confirmed) return;

    weeklySchedule.forEach((schedule, index) => {
        schedule.enabled = mondaySchedule.enabled;
        schedule.start = mondaySchedule.start;
        schedule.end = mondaySchedule.end;

        // Update UI
        const row = document.querySelectorAll('.schedule-row')[index];
        const checkbox = row.querySelector('.day-checkbox');
        const startTime = row.querySelector('.start-time');
        const endTime = row.querySelector('.end-time');
        const timeControls = row.querySelector('.time-controls');

        checkbox.checked = schedule.enabled;
        startTime.value = schedule.start;
        endTime.value = schedule.end;

        if (schedule.enabled) {
            timeControls.classList.remove('disabled');
            startTime.disabled = false;
            endTime.disabled = false;
        } else {
            timeControls.classList.add('disabled');
            startTime.disabled = true;
            endTime.disabled = true;
        }
    });

    showToast('Monday schedule applied to all days', 'success');
}

function saveSchedule() {
    // Validate times
    for (let schedule of weeklySchedule) {
        if (schedule.enabled) {
            if (!schedule.start || !schedule.end) {
                showToast('Please set start and end times for all enabled days', 'error');
                return;
            }
            if (schedule.start >= schedule.end) {
                showToast(`Invalid time range for ${schedule.day}`, 'error');
                return;
            }
        }
    }

    // Here you would send to backend
    console.log('Saving schedule:', weeklySchedule);
    
    // Simulate API call
    setTimeout(() => {
        showToast('Weekly schedule saved successfully!', 'success');
    }, 300);
}

function resetSchedule() {
    const confirmed = confirm('Reset schedule to default working hours (9 AM - 6 PM, Monday-Friday)?');
    if (!confirmed) return;

    const defaultSchedule = {
        enabled: true,
        start: '09:00',
        end: '18:00'
    };

    weeklySchedule.forEach((schedule, index) => {
        if (schedule.day === 'Saturday') {
            schedule.enabled = true;
            schedule.start = '10:00';
            schedule.end = '16:00';
        } else if (schedule.day === 'Sunday') {
            schedule.enabled = false;
            schedule.start = '10:00';
            schedule.end = '14:00';
        } else {
            schedule.enabled = defaultSchedule.enabled;
            schedule.start = defaultSchedule.start;
            schedule.end = defaultSchedule.end;
        }

        // Update UI
        const row = document.querySelectorAll('.schedule-row')[index];
        const checkbox = row.querySelector('.day-checkbox');
        const startTime = row.querySelector('.start-time');
        const endTime = row.querySelector('.end-time');
        const timeControls = row.querySelector('.time-controls');

        checkbox.checked = schedule.enabled;
        startTime.value = schedule.start;
        endTime.value = schedule.end;

        if (schedule.enabled) {
            timeControls.classList.remove('disabled');
            startTime.disabled = false;
            endTime.disabled = false;
        } else {
            timeControls.classList.add('disabled');
            startTime.disabled = true;
            endTime.disabled = true;
        }
    });

    showToast('Schedule reset to default', 'success');
}

// ========== Blocked Dates Functions ==========

function initializeBlockedDates() {
    // Load existing blocked dates
    const blockedItems = document.querySelectorAll('.blocked-date-item');
    blockedItems.forEach(item => {
        const date = item.getAttribute('data-date');
        const type = item.getAttribute('data-type') || 'full-day';
        const time = item.getAttribute('data-time') || null;
        if (date) {
            blockedDates.push({
                date: date,
                type: type,
                time: time,
                reason: item.querySelector('.blocked-date-reason')?.textContent || ''
            });
        }
    });

    // Block type selector
    const blockTypeSelect = document.getElementById('blockType');
    const timeInputGroup = document.getElementById('timeInputGroup');
    const timeLabel = document.getElementById('timeLabel');

    blockTypeSelect.addEventListener('change', function() {
        if (this.value === 'full-day') {
            timeInputGroup.style.display = 'none';
        } else {
            timeInputGroup.style.display = 'block';
            if (this.value === 'before') {
                timeLabel.textContent = 'Available Before';
            } else {
                timeLabel.textContent = 'Available After';
            }
        }
    });

    // Add blocked date button
    document.getElementById('addBlockedDate').addEventListener('click', addBlockedDate);
}

function addBlockedDate() {
    const dateInput = document.getElementById('newBlockedDate');
    const reasonInput = document.getElementById('blockReason');
    const blockTypeSelect = document.getElementById('blockType');
    const timeInput = document.getElementById('blockTime');
    
    const date = dateInput.value;
    const reason = reasonInput.value || 'Personal';
    const type = blockTypeSelect.value;
    const time = type !== 'full-day' ? timeInput.value : null;

    if (!date) {
        showToast('Please select a date', 'error');
        return;
    }

    if (type !== 'full-day' && !time) {
        showToast('Please select a time', 'error');
        return;
    }

    // Check if already blocked
    if (blockedDates.some(b => b.date === date)) {
        showToast('This date is already blocked', 'error');
        return;
    }

    // Add to blocked dates
    const blockedDate = { date, type, time, reason };
    blockedDates.push(blockedDate);

    // Update UI
    addBlockedDateToList(blockedDate);

    // Clear inputs
    dateInput.value = '';
    reasonInput.value = '';
    blockTypeSelect.value = 'full-day';
    timeInput.value = '';
    document.getElementById('timeInputGroup').style.display = 'none';

    showToast('Date blocked successfully', 'success');
}

function addBlockedDateToList(blockedDate) {
    const { date, type, time, reason } = blockedDate;
    const list = document.getElementById('blockedDatesList');
    
    // Remove empty state if exists
    const emptyState = list.querySelector('.empty-state');
    if (emptyState) {
        emptyState.remove();
    }

    const dateObj = new Date(date + 'T00:00:00');
    const formattedDate = dateObj.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });

    // Format type text with proper styling
    let typeText = '';
    if (type === 'full-day') {
        typeText = '🚫 Full Day Unavailable';
    } else if (type === 'before') {
        const formattedTime = formatTime(time);
        typeText = `⏰ Available Before ${formattedTime}`;
    } else if (type === 'after') {
        const formattedTime = formatTime(time);
        typeText = `⏰ Available After ${formattedTime}`;
    }

    const item = document.createElement('div');
    item.className = 'blocked-date-item';
    item.setAttribute('data-date', date);
    item.setAttribute('data-type', type);
    if (time) item.setAttribute('data-time', time);
    item.innerHTML = `
        <div class="blocked-date-info">
            <span class="blocked-date-value">${formattedDate}</span>
            <span class="blocked-date-type">${typeText}</span>
            <span class="blocked-date-reason">${reason}</span>
        </div>
        <button class="btn-remove" onclick="removeBlockedDate('${date}')">
            <span>✕</span>
        </button>
    `;

    list.appendChild(item);
}

function formatTime(time24) {
    const [hours, minutes] = time24.split(':');
    const hour = parseInt(hours);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const hour12 = hour % 12 || 12;
    return `${hour12}:${minutes} ${ampm}`;
}

function removeBlockedDate(date) {
    const confirmed = confirm('Remove this blocked date?');
    if (!confirmed) return;

    // Remove from arrays
    blockedDates = blockedDates.filter(b => b.date !== date);
    unavailableDates.delete(date);

    // Remove from UI
    const item = document.querySelector(`.blocked-date-item[data-date="${date}"]`);
    if (item) {
        item.remove();
    }

    // Check if list is empty
    const list = document.getElementById('blockedDatesList');
    const items = list.querySelectorAll('.blocked-date-item');
    if (items.length === 0) {
        list.innerHTML = `
            <h3>Currently Blocked Dates</h3>
            <div class="empty-state">
                <p>No blocked dates yet. Add dates when you're unavailable.</p>
            </div>
        `;
    }

    showToast('Blocked date removed', 'success');
}

// ========== Quick Navigation ==========

function setupQuickNav() {
    const navLinks = document.querySelectorAll('.quick-nav a');
    const sections = document.querySelectorAll('.card');

    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = link.getAttribute('href');
            const targetSection = document.querySelector(targetId);

            if (targetSection) {
                // Update active link
                navLinks.forEach(l => l.classList.remove('active'));
                link.classList.add('active');

                // Smooth scroll to section
                targetSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // Update active link on scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const id = entry.target.getAttribute('id');
                navLinks.forEach(link => {
                    if (link.getAttribute('href') === `#${id}`) {
                        navLinks.forEach(l => l.classList.remove('active'));
                        link.classList.add('active');
                    }
                });
            }
        });
    }, { threshold: 0.5 });

    sections.forEach(section => observer.observe(section));
}

// ========== Toast Notifications ==========

function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.className = `toast ${type}`;
    
    // Show toast
    setTimeout(() => {
        toast.classList.add('show');
    }, 100);

    // Hide after 3 seconds
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3100);
}

// Make removeBlockedDate available globally
window.removeBlockedDate = removeBlockedDate;
