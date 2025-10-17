// Groomer Availability JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const toast = document.getElementById('toast');
    let scheduleData = [];

    // Initialize schedule data
    document.querySelectorAll('.schedule-row').forEach((row, index) => {
        const dayCheckbox = row.querySelector('.day-checkbox');
        const startTime = row.querySelector('.start-time');
        const endTime = row.querySelector('.end-time');
        
        scheduleData.push({
            day: row.dataset.day,
            enabled: dayCheckbox.checked,
            start: startTime.value,
            end: endTime.value
        });

        // Handle day toggle
        dayCheckbox.addEventListener('change', function() {
            const timeControls = row.querySelector('.time-controls');
            scheduleData[index].enabled = this.checked;
            
            if (this.checked) {
                timeControls.classList.remove('disabled');
                startTime.disabled = false;
                endTime.disabled = false;
            } else {
                timeControls.classList.add('disabled');
                startTime.disabled = true;
                endTime.disabled = true;
            }
        });

        // Handle time changes
        startTime.addEventListener('change', function() {
            scheduleData[index].start = this.value;
        });

        endTime.addEventListener('change', function() {
            scheduleData[index].end = this.value;
        });
    });

    // Apply to All Days
    document.getElementById('applyToAll')?.addEventListener('click', function() {
        const firstRow = document.querySelector('.schedule-row');
        const firstEnabled = firstRow.querySelector('.day-checkbox').checked;
        const firstStart = firstRow.querySelector('.start-time').value;
        const firstEnd = firstRow.querySelector('.end-time').value;

        document.querySelectorAll('.schedule-row').forEach((row, index) => {
            const checkbox = row.querySelector('.day-checkbox');
            const startTime = row.querySelector('.start-time');
            const endTime = row.querySelector('.end-time');
            const timeControls = row.querySelector('.time-controls');

            checkbox.checked = firstEnabled;
            startTime.value = firstStart;
            endTime.value = firstEnd;

            scheduleData[index] = {
                day: row.dataset.day,
                enabled: firstEnabled,
                start: firstStart,
                end: firstEnd
            };

            if (firstEnabled) {
                timeControls.classList.remove('disabled');
                startTime.disabled = false;
                endTime.disabled = false;
            } else {
                timeControls.classList.add('disabled');
                startTime.disabled = true;
                endTime.disabled = true;
            }
        });

        showToast('Schedule applied to all days');
    });

    // Reset Schedule
    document.getElementById('resetSchedule')?.addEventListener('click', function() {
        if (confirm('Reset schedule to default working hours (9 AM - 6 PM, Mon-Sat)?')) {
            document.querySelectorAll('.schedule-row').forEach((row, index) => {
                const checkbox = row.querySelector('.day-checkbox');
                const startTime = row.querySelector('.start-time');
                const endTime = row.querySelector('.end-time');
                const timeControls = row.querySelector('.time-controls');
                const isSunday = row.dataset.day === 'Sunday';

                checkbox.checked = !isSunday;
                startTime.value = isSunday ? '10:00' : '09:00';
                endTime.value = isSunday ? '14:00' : '18:00';

                scheduleData[index] = {
                    day: row.dataset.day,
                    enabled: !isSunday,
                    start: startTime.value,
                    end: endTime.value
                };

                if (!isSunday) {
                    timeControls.classList.remove('disabled');
                    startTime.disabled = false;
                    endTime.disabled = false;
                } else {
                    timeControls.classList.add('disabled');
                    startTime.disabled = true;
                    endTime.disabled = true;
                }
            });

            showToast('Schedule reset to default');
        }
    });

    // Save Schedule
    document.getElementById('saveSchedule')?.addEventListener('click', function() {
        // Validate times
        let isValid = true;
        scheduleData.forEach(day => {
            if (day.enabled && day.start >= day.end) {
                isValid = false;
            }
        });

        if (!isValid) {
            showToast('Error: End time must be after start time!');
            return;
        }

        // In real app, send to server
        console.log('Saving schedule:', scheduleData);
        showToast('Schedule saved successfully');
    });

    // Quick nav active state
    document.querySelectorAll('.quick-nav a').forEach(link => {
        link.addEventListener('click', function(e) {
            document.querySelectorAll('.quick-nav a').forEach(l => l.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Blocked Dates
    const blockType = document.getElementById('blockType');
    const timeInputGroup = document.getElementById('timeInputGroup');
    const timeLabel = document.getElementById('timeLabel');

    blockType?.addEventListener('change', function() {
        if (this.value === 'full-day') {
            timeInputGroup.style.display = 'none';
        } else {
            timeInputGroup.style.display = 'block';
            timeLabel.textContent = this.value === 'before' ? 'Available Until' : 'Available From';
        }
    });

    // Add Blocked Date
    document.getElementById('addBlockedDate')?.addEventListener('click', function() {
        const dateInput = document.getElementById('newBlockedDate');
        const reasonInput = document.getElementById('blockReason');
        const blockTimeInput = document.getElementById('blockTime');
        const blockTypeValue = blockType.value;

        if (!dateInput.value) {
            showToast('Please select a date to block');
            return;
        }

        const date = new Date(dateInput.value);
        const formattedDate = date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });

        let typeText = '🚫 Full Day Unavailable';
        if (blockTypeValue === 'before') {
            typeText = `✅ Available Until ${blockTimeInput.value}`;
        } else if (blockTypeValue === 'after') {
            typeText = `✅ Available From ${blockTimeInput.value}`;
        }

        // Create blocked date item
        const blockedDatesList = document.getElementById('blockedDatesList');
        const emptyState = blockedDatesList.querySelector('.empty-state');
        if (emptyState) {
            emptyState.remove();
        }

        const blockedItem = document.createElement('div');
        blockedItem.className = 'blocked-date-item';
        blockedItem.dataset.date = dateInput.value;
        blockedItem.dataset.type = blockTypeValue;
        blockedItem.innerHTML = `
            <div class="blocked-date-info">
                <span class="blocked-date-value">${formattedDate}</span>
                <span class="blocked-date-type">${typeText}</span>
                <span class="blocked-date-reason">${reasonInput.value || 'Personal'}</span>
            </div>
            <button class="btn-remove" onclick="removeBlockedDate('${dateInput.value}')">
                <span>✕</span>
            </button>
        `;

        blockedDatesList.appendChild(blockedItem);

        // Clear form
        dateInput.value = '';
        reasonInput.value = '';
        blockTimeInput.value = '';
        blockType.value = 'full-day';
        timeInputGroup.style.display = 'none';

        showToast('Date blocked successfully');
    });

    // Remove blocked date (global function)
    window.removeBlockedDate = function(date) {
        const item = document.querySelector(`.blocked-date-item[data-date="${date}"]`);
        if (item) {
            item.style.opacity = '0';
            setTimeout(() => {
                item.remove();
                
                const blockedDatesList = document.getElementById('blockedDatesList');
                const items = blockedDatesList.querySelectorAll('.blocked-date-item');
                
                if (items.length === 0) {
                    const emptyState = document.createElement('div');
                    emptyState.className = 'empty-state';
                    emptyState.innerHTML = '<p>No blocked dates yet. Add dates when you\'re unavailable.</p>';
                    blockedDatesList.appendChild(emptyState);
                }
                
                showToast('Blocked date removed');
            }, 300);
        }
    };

    // Toast notification
    function showToast(message) {
        toast.textContent = message;
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }
});
