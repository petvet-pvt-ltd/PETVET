# Breeder Module Implementation

## Overview
This document describes the implementation of the breeder module database functionality for managing breeding pets and breeding requests.

## Database Tables Created

### 1. `breeder_pets`
Stores breeding pets managed by breeders.

**Columns:**
- `id` - Primary key (auto-increment)
- `breeder_id` - Foreign key to users table (the breeder who owns the pet)
- `name` - Pet name (VARCHAR 100)
- `breed` - Pet breed (VARCHAR 100)
- `gender` - Male or Female (ENUM)
- `date_of_birth` - Pet's date of birth (DATE)
- `photo` - Photo path/URL (VARCHAR 255, nullable)
- `description` - Pet description (TEXT, nullable)
- `is_active` - Active status for breeding availability (TINYINT, default 1)
- `created_at` - Record creation timestamp
- `updated_at` - Record last update timestamp

**Indexes:**
- `idx_breeder_id` - For filtering by breeder
- `idx_is_active` - For filtering active pets

### 2. `breeding_requests`
Stores breeding service requests from pet owners.

**Columns:**
- `id` - Primary key (auto-increment)
- `breeder_id` - Foreign key to users table (the breeder receiving the request)
- `owner_id` - Foreign key to users table (the pet owner making the request)
- `owner_pet_name` - Name of the owner's pet (VARCHAR 100)
- `owner_pet_breed` - Breed of the owner's pet (VARCHAR 100)
- `owner_pet_gender` - Gender of the owner's pet (ENUM: Male/Female)
- `preferred_date` - Preferred breeding date (DATE)
- `message` - Message from the owner (TEXT, nullable)
- `status` - Request status (ENUM: pending, approved, declined, completed)
- `breeder_pet_id` - Foreign key to breeder_pets (nullable, set when approved)
- `breeding_date` - Actual breeding date (DATE, nullable)
- `decline_reason` - Reason for declining (TEXT, nullable)
- `notes` - Notes when approving (TEXT, nullable)
- `final_notes` - Final notes when completing (TEXT, nullable)
- `requested_date` - When the request was created
- `approved_date` - When the request was approved (nullable)
- `declined_date` - When the request was declined (nullable)
- `completed_date` - When the breeding was completed (nullable)
- `created_at` - Record creation timestamp
- `updated_at` - Record last update timestamp

**Indexes:**
- `idx_breeder_id` - For filtering by breeder
- `idx_owner_id` - For filtering by pet owner
- `idx_status` - For filtering by status
- `idx_breeder_pet_id` - For joining with breeder_pets

## API Endpoints Created

### 1. `/api/breeder/manage-breeding-pets.php`

Manages breeding pets CRUD operations.

**Actions:**
- `get_all` - Get all breeding pets for the logged-in breeder
- `add` - Add a new breeding pet (supports photo upload)
- `update` - Update an existing breeding pet (supports photo upload)
- `delete` - Delete a breeding pet
- `toggle_status` - Toggle active/inactive status

**Authentication:** Requires logged-in user session

**Request Format:** POST with FormData (for file uploads)

**Response Format:** JSON
```json
{
  "success": true/false,
  "message": "...",
  "data": {...}
}
```

### 2. `/api/breeder/manage-requests.php`

Manages breeding requests from pet owners.

**Actions:**
- `get_all` - Get all requests (pending, approved, completed, declined)
- `accept` - Accept a pending request (requires breeder_pet_id)
- `decline` - Decline a pending request (optional reason)
- `complete` - Mark an approved request as completed (optional final notes)
- `get_active_pets` - Get active breeding pets for selection

**Authentication:** Requires logged-in user session

**Request Format:** POST with FormData

**Response Format:** JSON
```json
{
  "success": true/false,
  "message": "...",
  "data": {...}
}
```

## Frontend Changes

### Updated JavaScript Files

#### 1. `public/js/breeder/breeding-pets.js`
- Connected `savePet()` function to API endpoint
- Connected `togglePetStatus()` function to API endpoint
- Connected `confirmDelete()` function to API endpoint
- Added proper error handling and user feedback

#### 2. `public/js/breeder/requests.js`
- Connected `loadBreedingPets()` to fetch active pets from API
- Connected `confirmAcceptRequest()` to API endpoint
- Connected `confirmDeclineRequest()` to API endpoint
- Connected `confirmCompleteRequest()` to API endpoint
- Added proper error handling and user feedback

### Updated PHP Models

#### 1. `models/Breeder/PetsModel.php`
- Updated `getBreedingPets()` to fetch from database instead of mock data
- Calculates pet age from date of birth
- Returns actual database records

#### 2. `models/Breeder/DashboardModel.php`
- Updated `getStats()` to calculate from database
- Updated `getPendingRequests()` to fetch from database
- Updated `getApprovedRequests()` to fetch from database
- Updated `getCompletedRequests()` to fetch from database
- Updated `getUpcomingBreedingDates()` to fetch from database
- All methods now join with users table to get owner information

#### 3. `controllers/BreederController.php`
- Updated to use actual user ID from session instead of mock ID
- Now uses `$_SESSION['user_id']` for all operations

## Setup Instructions

### 1. Create Database Tables
The tables have already been created in the TiDB database using:
```bash
C:\xampp\mysql\bin\mysql.exe -h gateway01.ap-southeast-1.prod.aws.tidbcloud.com -P 4000 -u 2iYmekB7i4tHWm7.root -pPo3TdFdOuAqvbtCn --ssl petvetDB
```

SQL file: `database/create_breeder_tables.sql`

### 2. Insert Sample Data (Optional)
To test the functionality, you can insert sample data:
```bash
Get-Content "database/sample_breeder_data.sql" | mysql.exe -h ... --ssl petvetDB
```

**Important:** Before inserting sample data, update the user IDs in the SQL file to match actual breeder and pet owner IDs in your database.

### 3. Verify Installation
Check that tables were created:
```sql
SHOW TABLES LIKE 'breeder%';
DESCRIBE breeder_pets;
DESCRIBE breeding_requests;
```

## Features Implemented

### Breeding Pets Management
✅ Add new breeding pets with photo upload
✅ Edit existing breeding pets
✅ Delete breeding pets
✅ Toggle active/inactive status
✅ Display pet age calculated from date of birth
✅ Store pet descriptions and details

### Breeding Requests Management
✅ View pending requests from pet owners
✅ Accept requests and assign a breeding pet
✅ Decline requests with optional reason
✅ Mark approved requests as completed
✅ View approved requests with breeding dates
✅ View completed breeding history
✅ Contact pet owners via phone

### Dashboard
✅ Display statistics (pending, approved, completed, active pets)
✅ Show upcoming breeding dates
✅ Real-time data from database

## Security Features
- Session-based authentication
- User authorization (breeders can only manage their own pets/requests)
- Input validation on all API endpoints
- Prepared statements to prevent SQL injection
- File upload validation for pet photos

## File Structure
```
PETVET/
├── api/
│   └── breeder/
│       ├── manage-breeding-pets.php
│       └── manage-requests.php
├── database/
│   ├── create_breeder_tables.sql
│   └── sample_breeder_data.sql
├── public/
│   └── js/
│       └── breeder/
│           ├── breeding-pets.js (updated)
│           └── requests.js (updated)
├── models/
│   └── Breeder/
│       ├── PetsModel.php (updated)
│       └── DashboardModel.php (updated)
└── controllers/
    └── BreederController.php (updated)
```

## Testing Checklist

1. ✅ Database tables created successfully
2. ⏳ Add a new breeding pet
3. ⏳ Edit an existing breeding pet
4. ⏳ Delete a breeding pet
5. ⏳ Toggle pet active/inactive status
6. ⏳ View pending requests
7. ⏳ Accept a request
8. ⏳ Decline a request
9. ⏳ Mark a request as completed
10. ⏳ Verify dashboard statistics update correctly

## Notes
- The UI was not modified as requested
- All functionality is now connected to the database
- Photo uploads are handled by the existing ImageUploader class
- The system uses the existing user authentication and session management
- All dates use MySQL DATE and TIMESTAMP types for proper date handling
