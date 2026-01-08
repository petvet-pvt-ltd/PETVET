# Service Provider Availability System Implementation

## Overview
This implementation adds full database functionality to the service provider availability management system for trainers, sitters, breeders, and groomers. Users can now save their weekly schedules and blocked dates, which persist in the database.

## Database Tables Created

### 1. service_provider_weekly_schedule
Stores weekly recurring availability schedules for each service provider.

**Columns:**
- `id` - Auto-increment primary key
- `user_id` - Foreign key to users table
- `role_type` - ENUM('trainer', 'sitter', 'breeder', 'groomer')
- `day_of_week` - ENUM for days Monday-Sunday
- `is_available` - TINYINT(1) - Whether the provider is available on this day
- `start_time` - TIME - Start of availability
- `end_time` - TIME - End of availability
- `created_at` - Timestamp
- `updated_at` - Timestamp (auto-updates)

**Indexes:**
- Unique constraint on (user_id, role_type, day_of_week)
- Index on (user_id, role_type)
- Index on day_of_week

### 2. service_provider_blocked_dates
Stores specific dates when service providers are unavailable.

**Columns:**
- `id` - Auto-increment primary key
- `user_id` - Foreign key to users table
- `role_type` - ENUM('trainer', 'sitter', 'breeder', 'groomer')
- `blocked_date` - DATE - The date to block
- `block_type` - ENUM('full-day', 'before', 'after') - Type of block
- `block_time` - TIME - Time for partial day blocks (nullable)
- `reason` - VARCHAR(255) - Optional reason for blocking
- `created_at` - Timestamp
- `updated_at` - Timestamp (auto-updates)

**Indexes:**
- Unique constraint on (user_id, role_type, blocked_date)
- Index on (user_id, role_type)
- Index on blocked_date

## API Endpoints

### File: `/api/service-provider-availability.php`

All endpoints require authentication and validate that the user has the specified role.

#### 1. Get Weekly Schedule
- **Action:** `get_schedule`
- **Method:** GET
- **Parameters:** `role_type` (trainer|sitter|breeder|groomer)
- **Response:** JSON with schedule array

#### 2. Save Weekly Schedule
- **Action:** `save_schedule`
- **Method:** POST
- **Parameters:** `role_type`, schedule data in request body
- **Response:** Success/failure message

#### 3. Get Blocked Dates
- **Action:** `get_blocked_dates`
- **Method:** GET
- **Parameters:** `role_type`
- **Response:** JSON with blocked dates array

#### 4. Add Blocked Date
- **Action:** `add_blocked_date`
- **Method:** POST
- **Parameters:** `role_type`, date, type, time, reason in request body
- **Response:** Success with new ID

#### 5. Remove Blocked Date
- **Action:** `remove_blocked_date`
- **Method:** POST
- **Parameters:** `role_type`, date in request body
- **Response:** Success/failure message

## Files Modified

### PHP Files (Backend):
1. `/views/trainer/availability.php` - Updated to load data from database
2. `/views/sitter/availability.php` - Updated to load data from database
3. `/views/breeder/availability.php` - Updated to load data from database
4. `/views/groomer/availability.php` - Updated to load data from database

### JavaScript Files (Frontend):
1. `/public/js/trainer/availability.js` - Added API integration
2. `/public/js/sitter/availability.js` - Added API integration
3. `/public/js/breeder/availability.js` - Added API integration (copied from trainer)
4. `/public/js/groomer/availability.js` - Added API integration (copied from trainer)

### New Files Created:
1. `/api/service-provider-availability.php` - Main API endpoint
2. `/database/create_availability_tables.sql` - SQL schema for tables

## Features Implemented

### Weekly Schedule Management
- View current weekly schedule (loads from database)
- Enable/disable specific days
- Set custom start and end times for each day
- Apply Monday's schedule to all days (bulk update)
- Reset to default schedule
- Save changes to database with validation
- Real-time UI updates

### Blocked Dates Management
- View all blocked dates from database
- Add new blocked dates with three types:
  - Full day unavailable
  - Available only before specified time
  - Available only after specified time
- Optional reason for blocking
- Remove blocked dates
- Automatic sorting by date
- Validation to prevent duplicate dates

### Multi-Role Support
- Single user can have multiple service provider roles
- Each role has independent availability settings
- Proper role validation on all API calls
- Separate schedules for trainer, sitter, breeder, and groomer roles

## Security Features
- User authentication required for all API calls
- Role verification before allowing data access/modification
- SQL injection protection using prepared statements
- XSS protection with htmlspecialchars on output
- CSRF protection should be added for production

## UI/UX Features
- No changes to existing UI design
- Toast notifications for all actions
- Smooth animations and transitions
- Form validation before submission
- Confirmation dialogs for destructive actions
- Loading states handled gracefully
- Fallback to default data if API fails

## Testing Recommendations

1. **Database Connection Test:**
   - Verify tables were created successfully
   - Check indexes and foreign keys

2. **Weekly Schedule Tests:**
   - Save a new schedule
   - Modify existing schedule
   - Reset to defaults
   - Apply to all days feature

3. **Blocked Dates Tests:**
   - Add full-day block
   - Add partial-day block (before/after)
   - Remove blocked date
   - Add duplicate date (should fail gracefully)

4. **Multi-Role Tests:**
   - User with multiple roles
   - Switch between roles
   - Verify data isolation between roles

5. **Edge Cases:**
   - Invalid time ranges
   - Past dates for blocking
   - Empty schedule submission
   - Network failures

## Known Considerations

1. **Time Zones:** Currently uses server time. Consider adding timezone support for multi-region deployments.

2. **Recurring Blocks:** System doesn't support recurring blocked dates (e.g., "every Monday"). Each date must be added individually.

3. **Schedule Conflicts:** No validation against existing bookings when changing availability.

4. **Capacity Limits:** No limit on number of blocked dates or schedule changes.

## Future Enhancements

1. Add timezone support
2. Implement recurring blocked dates
3. Add conflict detection with existing bookings
4. Export/import schedule functionality
5. Schedule templates (save and reuse common patterns)
6. Bulk date blocking (date ranges)
7. Activity/audit log for schedule changes
8. Email notifications when schedule changes affect existing bookings

## Deployment Notes

1. Run the SQL migration: `database/create_availability_tables.sql`
2. Ensure database credentials are correct in `config/connect.php`
3. Verify file permissions for API endpoint
4. Test with each service provider role
5. Monitor error logs for any database issues

## Support

For issues or questions:
- Check database connection first
- Verify user has required roles
- Check browser console for JavaScript errors
- Review server error logs for PHP issues
