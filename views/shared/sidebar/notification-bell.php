<?php
/**
 * Notification Bell Component
 * Displays notification bell icon with badge and dropdown panel
 */

// Only show for pet-owners
$currentModule = isset($module) ? $module : (isset($GLOBALS['module']) ? $GLOBALS['module'] : null);
if ($currentModule !== 'pet-owner') {
    return;
}
?>

<!-- Notification Styles -->
<style>
    .notification-container {
        position: relative;
    }

    .notification-bell {
        position: relative;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: none;
        background: transparent;
        color: #0f172a;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .notification-bell:hover {
        background: #f1f5f9;
    }

    .notification-badge {
        position: absolute;
        top: 4px;
        right: 4px;
        background: #ef4444;
        color: white;
        font-size: 11px;
        font-weight: 600;
        min-width: 18px;
        height: 18px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .notification-badge.hidden {
        display: none;
    }

    .notification-panel {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        width: 420px;
        max-height: 600px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.15);
        border: 1px solid #e5e7eb;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.2s ease;
        z-index: 1000;
        display: flex;
        flex-direction: column;
    }

    .notification-panel.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .notification-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid #e5e7eb;
    }

    .notification-header h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: #0f172a;
    }

    .mark-all-read {
        background: none;
        border: none;
        color: #2563eb;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 4px;
        transition: background 0.2s ease;
    }

    .mark-all-read:hover {
        background: #eff6ff;
    }

    .notification-list {
        overflow-y: auto;
        max-height: 480px;
        flex: 1;
    }

    .notification-item {
        display: flex;
        gap: 12px;
        padding: 14px 20px;
        border-bottom: 1px solid #f3f4f6;
        cursor: pointer;
        transition: background 0.15s ease;
        position: relative;
    }

    .notification-item:hover {
        background: #f9fafb;
    }

    .notification-item.unread {
        background: #eff6ff;
    }

    .notification-item.unread::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: #2563eb;
    }

    .notification-icon {
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .appointment-icon {
        background: #dbeafe;
        color: #2563eb;
    }

    .sitter-icon {
        background: #fef3c7;
        color: #f59e0b;
    }

    .trainer-icon {
        background: #e0e7ff;
        color: #6366f1;
    }

    .breeder-icon {
        background: #fce7f3;
        color: #ec4899;
    }

    .notification-content {
        flex: 1;
        min-width: 0;
    }

    .notification-text {
        margin: 0 0 4px 0;
        font-size: 14px;
        line-height: 1.5;
        color: #374151;
    }

    .notification-text strong {
        font-weight: 600;
        color: #111827;
    }

    .notification-text em {
        font-style: italic;
        color: #6b7280;
    }

    .notification-time {
        font-size: 12px;
        color: #9ca3af;
    }

    .notification-footer {
        padding: 16px 20px;
        text-align: center;
        border-top: 1px solid #e5e7eb;
        background: #fafbfc;
    }

    .notification-footer p {
        margin: 0;
        font-size: 13px;
        color: #6b7280;
    }

    .notification-empty {
        padding: 40px 20px;
        text-align: center;
        color: #9ca3af;
    }

    body.notification-open {
        overflow: hidden;
    }
</style>

<!-- Notification Bell Component -->
<div class="notification-container">
    <button type="button" class="notification-bell" id="notificationBell">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
        </svg>
        <span class="notification-badge hidden" id="notificationBadge">0</span>
    </button>

    <div class="notification-panel" id="notificationPanel">
        <div class="notification-header">
            <h3>Notifications</h3>
            <button class="mark-all-read" id="markAllRead">Mark all as read</button>
        </div>
        <div class="notification-list" id="notificationList">
            <div class="notification-empty">Loading notifications...</div>
        </div>
        <div class="notification-footer">
            <p>You're all caught up!</p>
        </div>
    </div>
</div>

<script>
    // Notification system
    const notificationBell = document.getElementById('notificationBell');
    const notificationPanel = document.getElementById('notificationPanel');
    const notificationBadge = document.getElementById('notificationBadge');
    const notificationList = document.getElementById('notificationList');
    const markAllReadBtn = document.getElementById('markAllRead');

    let currentNotifications = [];

    // Toggle notification panel
    notificationBell.addEventListener('click', function(e) {
        e.stopPropagation();
        notificationPanel.classList.toggle('show');
        
        if (notificationPanel.classList.contains('show')) {
            document.body.classList.add('notification-open');
            loadNotifications();
        } else {
            document.body.classList.remove('notification-open');
        }
    });

    // Close panel when clicking outside
    document.addEventListener('click', function(e) {
        if (!notificationPanel.contains(e.target) && !notificationBell.contains(e.target)) {
            notificationPanel.classList.remove('show');
            document.body.classList.remove('notification-open');
        }
    });

    // Prevent closing when clicking inside panel
    notificationPanel.addEventListener('click', function(e) {
        e.stopPropagation();
    });

    // Mark all as read
    markAllReadBtn.addEventListener('click', function() {
        markAllNotificationsRead();
    });

    // Click on notification to mark as read
    notificationList.addEventListener('click', function(e) {
        const notificationItem = e.target.closest('.notification-item');
        if (notificationItem) {
            const notificationId = notificationItem.dataset.notificationId;
            markNotificationRead(notificationId);
            notificationItem.classList.remove('unread');
            updateBadgeCount();
        }
    });

    // Load notifications via AJAX
    async function loadNotifications() {
        try {
            const response = await fetch('/PETVET/api/pet-owner/get-notifications.php');
            const data = await response.json();
            
            if (data.success) {
                currentNotifications = data.notifications;
                renderNotifications(data.notifications);
                updateBadgeCount();
            }
        } catch (error) {
            console.error('Error loading notifications:', error);
            notificationList.innerHTML = '<div class="notification-empty">Error loading notifications</div>';
        }
    }

    // Render notifications
    function renderNotifications(notifications) {
        if (notifications.length === 0) {
            notificationList.innerHTML = '<div class="notification-empty">No notifications</div>';
            return;
        }

        notificationList.innerHTML = '';
        
        notifications.forEach(notification => {
            const item = document.createElement('div');
            item.className = `notification-item ${!notification.is_read ? 'unread' : ''}`;
            item.dataset.notificationId = notification.id;
            
            const icon = getNotificationIcon(notification.type);
            const timeAgo = getTimeAgo(new Date(notification.created_at));
            
            item.innerHTML = `
                <div class="notification-icon ${notification.type}-icon">
                    ${icon}
                </div>
                <div class="notification-content">
                    <p class="notification-text">${notification.message}</p>
                    ${notification.clinic_name ? `<p class="notification-text"><em>${notification.clinic_name}</em></p>` : ''}
                    <span class="notification-time">${timeAgo}</span>
                </div>
            `;
            
            notificationList.appendChild(item);
        });
    }

    // Get notification icon
    function getNotificationIcon(type) {
        const icons = {
            'appointment': '📅',
            'sitter': '👥',
            'trainer': '⏰',
            'breeder': '💖'
        };
        return icons[type] || '🔔';
    }

    // Get time ago string
    function getTimeAgo(date) {
        const seconds = Math.floor((new Date() - date) / 1000);
        
        let interval = seconds / 31536000;
        if (interval > 1) return Math.floor(interval) + ' years ago';
        
        interval = seconds / 2592000;
        if (interval > 1) return Math.floor(interval) + ' months ago';
        
        interval = seconds / 86400;
        if (interval > 1) return Math.floor(interval) + ' days ago';
        
        interval = seconds / 3600;
        if (interval > 1) return Math.floor(interval) + ' hours ago';
        
        interval = seconds / 60;
        if (interval > 1) return Math.floor(interval) + ' minutes ago';
        
        return Math.floor(seconds) + ' seconds ago';
    }

    // Mark single notification as read
    async function markNotificationRead(notificationId) {
        try {
            const formData = new FormData();
            formData.append('notification_id', notificationId);
            
            await fetch('/PETVET/api/pet-owner/mark-notification-read.php', {
                method: 'POST',
                body: formData
            });
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }

    // Mark all notifications as read
    async function markAllNotificationsRead() {
        try {
            const formData = new FormData();
            formData.append('mark_all', '1');
            
            await fetch('/PETVET/api/pet-owner/mark-notification-read.php', {
                method: 'POST',
                body: formData
            });
            
            // Reload notifications
            loadNotifications();
        } catch (error) {
            console.error('Error marking all as read:', error);
        }
    }

    // Update badge count
    function updateBadgeCount() {
        const unreadCount = currentNotifications.filter(n => !n.is_read).length;
        if (unreadCount > 0) {
            notificationBadge.textContent = unreadCount;
            notificationBadge.classList.remove('hidden');
        } else {
            notificationBadge.classList.add('hidden');
        }
    }

    // Load notifications on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadNotifications();
        // Refresh notifications every 30 seconds
        setInterval(loadNotifications, 30000);
    });
</script>
