# Receptionist Guide: Pet Owner Notifications

## Overview
When you approve, decline, or cancel an appointment booking, the pet owner automatically receives a notification in their pet owner dashboard. They see a notification bell icon in their sidebar that shows their latest messages.

## How It Works

### For Receptionist

#### When Approving an Appointment
1. Review the pending appointment in your dashboard
2. Click **"Approve"** button
3. (Optional) Select or confirm the assigned vet
4. Click **"Confirm"**

**What happens behind the scenes:**
- Appointment status changes to "approved" in the system
- **Notification automatically created** and sent to pet owner
- Pet owner sees in notification: 
  > "Appointment for **[Pet Name]** with **Dr. [Vet Name]** has been confirmed for **[Date], at [Time]**"

#### When Declining an Appointment
1. Review the pending appointment in your dashboard
2. Click **"Decline"** button
3. Enter a reason (e.g., "Vet fully booked", "Pet age incompatible", etc.)
4. Click **"Confirm Decline"**

**What happens behind the scenes:**
- Appointment status changes to "declined" in the system
- **Notification with reason automatically created** and sent to pet owner
- Pet owner sees in notification:
  > "Appointment declined for **[Pet Name]**. Reason: *[Your reason text]*"

#### When Cancelling an Appointment
1. Find the appointment in your dashboard
2. Click **"Cancel"** button
3. Enter a cancellation reason
4. Click **"Confirm Cancellation"**

**What happens behind the scenes:**
- Appointment status changes to "cancelled" in the system
- **Notification automatically created** and sent to pet owner
- Pet owner sees in notification:
  > "Your appointment for **[Pet Name]** has been cancelled. Reason: *[Your reason text]*"

### For Pet Owner

#### Viewing Notifications
1. Pet owner logs into their dashboard
2. Looks for **notification bell icon** 🔔 in the top right corner of sidebar
3. **Red badge** on bell shows number of unread messages
4. Clicks bell to open notification panel
5. Sees all notifications in order (most recent first)

#### Understanding Notification Display

**Each notification shows:**
- **Icon** representing the type (appointment 📅, sitter 👥, trainer ⏰, breeder 💖)
- **Title** (e.g., "Appointment Confirmed", "Appointment Declined")
- **Message** with key details and your clinic name
- **Time** when notification was sent (e.g., "2 hours ago")
- **Status** - blue background indicates unread notification

#### Marking as Read
- **Single notification**: Click on it to mark as read (background changes from blue to white)
- **All at once**: Click "Mark All as Read" button at top of panel

#### Auto-Updates
- Notification panel automatically refreshes every 30 seconds
- Pet owner can leave dashboard open and new notifications appear automatically
- No page refresh needed

## Examples of Notifications Sent

### Example 1: Approval
**Situation:** Receptionist approves grooming appointment for "Buddy" with Dr. Sarah

**Notification shows:**
```
📅 Appointment Confirmed
Appointment for Buddy with Dr. Sarah Johnson has been confirmed 
for December 15, 2024, at 2:30 PM - PawsomeGroomers Clinic
```

### Example 2: Decline with Reason
**Situation:** Receptionist declines vaccination appointment, clinic fully booked

**Notification shows:**
```
📅 Appointment Declined
Appointment declined for Buddy. Reason: Vet fully booked on that date - 
PawsomeGroomers Clinic
```

### Example 3: Cancellation
**Situation:** Staff member cancels appointment due to staff emergency

**Notification shows:**
```
📅 Appointment Cancelled
Your appointment for Buddy has been cancelled. Reason: Staff emergency - 
PawsomeGroomers Clinic
```

## Important Notes

### What Gets Included in Notifications
✅ Pet name
✅ Vet name (if applicable)
✅ Appointment date and time (if approved)
✅ Your reason (if declined or cancelled)
✅ Clinic name where appointment is scheduled
✅ Timestamp when notification was sent

### What Happens if Pet Owner is Offline
- Notifications are saved in the system database
- When pet owner logs back in, they see all waiting notifications
- Can review notifications anytime, even days later
- Bell badge shows all unread notifications

### Clinic Information
- Clinic name is automatically pulled from your clinic settings
- If clinic name is updated later, new notifications show updated name
- Previous notifications keep the clinic name from when they were sent

## Troubleshooting

### Pet Owner Not Seeing Notification
1. **Check if bell icon is visible** - Only shows on pet owner dashboard (not receptionist or other roles)
2. **Ask them to refresh** - They can press F5 or reload the page
3. **Check for unread count** - If bell has red badge, they have notifications waiting
4. **Wait for auto-refresh** - Panel auto-refreshes every 30 seconds

### Notification Shows Wrong Clinic Name
- This is normal if clinic details were updated after booking
- Notifications preserve the clinic name from when appointment was booked

### Pet Owner Can't Click on Notifications
- Make sure they're using a modern browser (Chrome, Firefox, Safari, Edge)
- Notifications automatically mark as read when clicked

## System Details for IT/Admin

### Response Time
- Notifications created **immediately** when you take action
- Appear in pet owner's panel within 30 seconds (at next auto-refresh)
- Or pet owner can refresh manually for instant update

### Data Stored
- Each notification stores: message, reason, date/time, clinic details
- Read status tracked separately (pet owner can see which they've viewed)
- Notifications kept for long-term reference

### Database Tracking
- All notifications logged to `notifications` table
- Read status tracked in `notification_reads` table
- Used for analytics and audit trail

## Future Features (Coming Soon)
- ✉️ Email notifications in addition to in-app
- 📱 Mobile push notifications
- 🔔 Notification preferences (which types to receive)
- 🔍 Search and filter old notifications
- 📋 Archive important messages
