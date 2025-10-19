# Pet Owner Notification System

## Overview
A YouTube-style notification panel has been implemented for the pet owner's "My Pets" page. The notification bell icon appears in the top-right corner of the page header, next to the "Add Pet" button.

## Features

### 1. **Bell Icon with Badge**
- A bell icon button displays in the header
- A red badge shows the count of unread notifications
- The badge disappears when all notifications are marked as read

### 2. **Notification Panel**
- Clicking the bell icon toggles the notification panel
- Panel appears as a dropdown below the bell icon
- YouTube-style design with smooth animations
- Panel closes when clicking outside or clicking the bell again

### 3. **Notification Types**
The system supports 4 types of notifications, each with a unique icon and color:

#### **Appointment Notifications** (Blue)
- Appointment confirmed with or without preferred vet
- Appointment declined with reason

#### **Sitter Notifications** (Amber/Yellow)
- Sitter accepted request
- Sitter declined request

#### **Trainer Notifications** (Indigo)
- Trainer accepted request
- Trainer declined request
- Training session completed
- Training program completed

#### **Breeder Notifications** (Pink)
- Breeder accepted breeding request
- Breeder declined request with reason

### 4. **User Interactions**
- **Click notification**: Marks a single notification as read (removes blue background)
- **Mark all as read**: Button in header marks all notifications as read
- **Unread indicator**: Blue background and left border for unread notifications
- **Timestamp**: Each notification shows relative time (e.g., "2 hours ago")

## UI Details

### Design Elements
- **Colors**: Matches the site's color scheme with appropriate accent colors
- **Icons**: SVG icons for each notification type
- **Typography**: Strong tags for important names/dates, em tags for reasons
- **Scrolling**: Smooth scrolling with custom scrollbar styling
- **Responsive**: Adapts to mobile screens

### Notification Format Examples

```
Appointment for Duke with Dr. Peter has been confirmed for October 20, 2023, at 10:00 AM
Appointment for Duke has been confirmed for October 20, 2023, at 10:00 AM
Appointment declined for Duke. Reason: Fully booked on that day

Sitter John has accepted your request for Max
Sitter John has declined your request for Max

Trainer Alex has accepted your request for Max
Trainer Alex has declined your request for Max
Training Session 5 for Max has been completed. Next session is scheduled for October 20, 2025.
Training program for Max has been successfully completed.

Breeder Peter accepted your request for Duke. Breeder's Pet: Molly
Breeder Peter declined your request. Reason: Not compatible breeds
```

## Backend Integration (TODO)

### Required API Endpoints

#### 1. Get Notifications
```php
// GET /PETVET/api/notifications/get-notifications.php
// Returns: { notifications: [...], unread_count: 3 }

Response format:
{
  "notifications": [
    {
      "id": 1,
      "type": "appointment", // appointment, sitter, trainer, breeder
      "message": "Appointment for <strong>Duke</strong> with <strong>Dr. Peter</strong>...",
      "time_ago": "2 hours ago",
      "is_read": false,
      "created_at": "2023-10-19 14:30:00"
    }
  ],
  "unread_count": 3
}
```

#### 2. Mark Notification as Read
```php
// POST /PETVET/api/notifications/mark-read.php
// Body: { notificationId: 1 }
```

#### 3. Mark All as Read
```php
// POST /PETVET/api/notifications/mark-all-read.php
```

### Database Schema Suggestion

```sql
CREATE TABLE notifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  type ENUM('appointment', 'sitter', 'trainer', 'breeder') NOT NULL,
  message TEXT NOT NULL,
  is_read BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### JavaScript Integration Points

The code includes commented TODO sections where backend integration should be added:

1. **Line ~990**: Mark single notification as read (AJAX call)
2. **Line ~1010**: Mark all notifications as read (AJAX call)
3. **Line ~1030**: `fetchNotifications()` function template
4. **Line ~1100**: `renderNotifications()` function template
5. **Line ~1140**: Polling interval for new notifications

### Implementation Steps

1. **Create Database Table**: Use the schema above
2. **Create API Endpoints**: Implement the 3 endpoints mentioned
3. **Uncomment JS Code**: Uncomment the `fetchNotifications()` and related code
4. **Test**: Verify notifications load and update correctly
5. **Add Triggers**: In backend code, insert notifications when:
   - Receptionist confirms/declines appointment
   - Sitter accepts/declines request
   - Trainer accepts/declines or completes session
   - Breeder accepts/declines breeding request

## Notification Triggers

### From Receptionist
- Appointment confirmation (with/without preferred vet)
- Appointment declined

### From Sitter
- Request accepted
- Request declined

### From Trainer
- Request accepted
- Request declined
- Session completed
- Program completed

### From Breeder
- Breeding request accepted
- Breeding request declined

## Files Modified

- `views/pet-owner/my-pets.php`: Added notification HTML, CSS, and JavaScript

## Browser Compatibility

- Modern browsers (Chrome, Firefox, Safari, Edge)
- Responsive design for mobile devices
- No external libraries or frameworks required

## Testing

To test the UI:
1. Navigate to the My Pets page as a pet owner
2. Click the bell icon to open notifications
3. Click a notification to mark it as read
4. Click "Mark all as read" to clear all
5. Test on mobile devices for responsive behavior

## Future Enhancements

- Push notifications using browser notification API
- Real-time updates using WebSockets
- Sound alerts for new notifications
- Notification preferences/settings
- Filter notifications by type
- Search within notifications
