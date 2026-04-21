# Breeder Section: Breeding Pet Form Enhancement

## Overview
This document outlines the required changes to add new fields and functionality to the **Breeding Pets Management** section. The goal is to create a comprehensive breeding pet form that captures all necessary breeding information.

---

## Current State

### Current Form Fields
The existing breeding pet form (`/views/breeder/breeding-pets.php`) currently captures:
- ✓ Pet Photo
- ✓ Pet Name
- ✓ Breed
- ✓ Gender
- ✓ Date of Birth
- ✓ Description
- ✓ Active Status (checkbox)

### Current Database Table
**Table:** `breeder_pets`
```
Columns: id, breeder_id, name, breed, gender, date_of_birth, photo, description, is_active, created_at, updated_at
```

### Current Files
| File | Purpose |
|------|---------|
| `views/breeder/breeding-pets.php` | Main UI view with form modal |
| `api/breeder/manage-breeding-pets.php` | Backend API for CRUD operations |
| `public/js/breeder/breeding-pets.js` | Frontend JavaScript for form handling |
| `models/Breeder/PetsModel.php` | Database model (minimal) |

---

## Proposed Enhancement: Breeding Health & Certification Form

### New Fields to Add

#### 1. **Health Information Section**

| Field Name | Type | DB Column | Required | Notes |
|------------|------|-----------|----------|-------|
| Health Certifications | Multi-select | `health_certifications` | No | Vaccination, DNA Tested, Health Screened, etc. |
| Vaccination Date | Date | `vaccination_date` | No | Last vaccination date |
| Vaccination Valid Until | Date | `vaccination_valid_until` | No | Auto-calculated from vaccination date |
| Last Vet Checkup | Date | `last_vet_checkup` | No | Date of most recent veterinary examination |
| Known Genetic Conditions | Textarea | `genetic_conditions` | No | Any known hereditary conditions |
| OFA/PennHIP Certification Number | Text | `vet_certification_number` | No | Orthopedic Foundation for Animals certification |

#### 2. **Breeding History Section**

| Field Name | Type | DB Column | Required | Notes |
|------------|------|-----------|----------|-------|
| Number of Litters | Number | `num_litters` | No | Total litters produced |
| Last Litter Date | Date | `last_litter_date` | No | Date of most recent litter |
| Breeding Breaks Needed | Checkbox | `needs_breeding_break` | No | Flag if pet needs rest before next breeding |
| Breeding Break Until | Date | `breeding_break_until` | No | Date when breeding can resume |
| Average Litter Size | Number | `avg_litter_size` | No | Typical number of offspring |

#### 3. **Genetics & Bloodline Section**

| Field Name | Type | DB Column | Required | Notes |
|------------|------|-----------|----------|-------|
| Bloodline/Pedigree | Textarea | `pedigree_info` | No | Notable ancestors or bloodline info |
| DNA Test Results | File Upload | `dna_test_file` | No | PDF/image of genetic testing results |
| Color Genetics | Text | `color_genetics` | No | Genetic color information (e.g., Aa, BB) |
| Champion Titles | Textarea | `champion_titles` | No | Any show titles or achievements |

#### 4. **Breeding Availability Section**

| Field Name | Type | DB Column | Required | Notes |
|------------|------|-----------|----------|-------|
| Breeding Fee | Decimal(10,2) | `breeding_fee` | No | Cost for breeding services |
| Geographic Availability | Multi-select | `geographic_service_areas` | No | Regions/cities where breeding available |
| Service Type | Radio | `service_type` | No | Options: Natural, Artificial Insemination, Both |
| Requires Deposits | Checkbox | `requires_deposit` | No | Whether deposit is required |
| Deposit Amount | Decimal(10,2) | `deposit_amount` | No | Deposit amount if required |
| Breeding Restrictions | Textarea | `breeding_restrictions` | No | Any specific breeding requirements |

#### 5. **Contact & Preferences Section**

| Field Name | Type | DB Column | Required | Notes |
|------------|------|-----------|----------|-------|
| Breeding Contact Person | Text | `breeding_contact` | No | Name of person handling breeding arrangements |
| Phone Number | Tel | `breeding_phone` | No | Contact number for breeding inquiries |
| Email | Email | `breeding_email` | No | Email for breeding inquiries |
| Website/Social Media | URL | `breeding_website` | No | Link to breeder's website or social profile |
| Notes for Breeders | Textarea | `breeder_notes` | No | Special notes or requirements |

---

## Database Changes Required

### New Migration File
Create: `database/migrations/add_breeding_pet_details.php`

```sql
ALTER TABLE breeder_pets ADD COLUMN (
    -- Health Information
    health_certifications JSON,
    vaccination_date DATE,
    vaccination_valid_until DATE,
    last_vet_checkup DATE,
    genetic_conditions TEXT,
    vet_certification_number VARCHAR(100),
    
    -- Breeding History
    num_litters INT DEFAULT 0,
    last_litter_date DATE,
    needs_breeding_break BOOLEAN DEFAULT FALSE,
    breeding_break_until DATE,
    avg_litter_size INT,
    
    -- Genetics & Bloodline
    pedigree_info LONGTEXT,
    dna_test_file VARCHAR(255),
    color_genetics VARCHAR(100),
    champion_titles LONGTEXT,
    
    -- Breeding Availability
    breeding_fee DECIMAL(10, 2),
    geographic_service_areas JSON,
    service_type ENUM('natural', 'artificial', 'both'),
    requires_deposit BOOLEAN DEFAULT FALSE,
    deposit_amount DECIMAL(10, 2),
    breeding_restrictions LONGTEXT,
    
    -- Contact & Preferences
    breeding_contact VARCHAR(100),
    breeding_phone VARCHAR(20),
    breeding_email VARCHAR(100),
    breeding_website VARCHAR(255),
    breeder_notes LONGTEXT
);

CREATE INDEX idx_breeding_fee ON breeder_pets(breeding_fee);
CREATE INDEX idx_service_type ON breeder_pets(service_type);
```

---

## Files to Modify

### 1. **Database & Models**
- [ ] Create migration: `database/migrations/add_breeding_pet_details.php`
- [ ] Update `models/Breeder/PetsModel.php`:
  - Add methods for retrieving detailed breeding information
  - Add validation for new fields
  - Add methods for searching by breeding fee, service type, etc.

### 2. **Backend API**
- [ ] Update `api/breeder/manage-breeding-pets.php`:
  - Modify `addBreedingPet()` function to handle new fields
  - Modify `updateBreedingPet()` function for partial updates
  - Add file upload handling for DNA test results
  - Add JSON encoding for multi-select fields (certifications, service areas)
  - Add validation:
    - Vaccination date cannot be in future
    - Breeding break until date must be after break date
    - Breeding fee must be positive
    - Phone number validation
    - Email validation

### 3. **Frontend Views**
- [ ] Update `views/breeder/breeding-pets.php`:
  - Expand table to show more columns (Breeding Fee, Service Type, Certification Status)
  - Create new or expand modal with tabs/sections:
    - **Basic Info** (existing: name, breed, gender, dob, photo)
    - **Health Information** (vaccinations, vet checkups, certifications)
    - **Breeding History** (litters, breeding breaks)
    - **Genetics** (pedigree, DNA tests, color genetics, titles)
    - **Availability** (breeding fee, service type, geographic areas)
    - **Contact** (breeder contact info and preferences)
  - Add collapsible sections for better UX
  - Add file upload preview for DNA test documents
  - Add date range validation on client side

### 4. **Frontend JavaScript**
- [ ] Update `public/js/breeder/breeding-pets.js`:
  - Add form validation for new fields
  - Add dynamic field visibility (e.g., show breeding break until only if needs break is checked)
  - Add conditional logic:
    - Show deposit amount only if requires deposit is checked
    - Validate breeding break until is after start date
  - Add file upload handling for DNA tests
  - Add JSON serialization for multi-select fields
  - Add tab/section switching in modal
  - Add phone number formatting
  - Add date comparison validations

### 5. **CSS Styling**
- [ ] Update/Create `public/css/breeder/breeding-pets.css`:
  - Style for tabbed modal interface
  - Styling for multi-select checkboxes
  - File upload preview styling
  - Collapsible section styling
  - Responsive layout for expanded form

### 6. **Display/Public Section** (Optional)
- [ ] Create `views/breeder/available-breeding-pets.php`:
  - Public page showing available breeding pets
  - Filter by service type, breeding fee range, geographic area
  - Show certification badges, breeding history highlights
  - Contact form for breeding inquiries

- [ ] Create `api/breeder/search-breeding-pets.php`:
  - Search and filter breeding pets by criteria
  - Apply geographic filters
  - Sort by breeding fee, most recent, rating

---

## Validation Rules

### Client-Side (JavaScript)
```javascript
- Vaccination Date: Not in future, not older than 2 years
- Breeding Fee: Must be >= 0, max 1,000,000
- Number of Litters: >= 0, integer
- Average Litter Size: 1-20
- Breeding Break Until: Must be after today if needs_breeding_break is true
- Phone: Valid 10-digit format
- Email: Valid email format
- Last Litter Date: Not in future
```

### Server-Side (PHP API)
```php
- All client-side validations (don't trust client)
- File upload: Only PDF or image formats for DNA tests, max 5MB
- JSON serialization validation
- Enum validation for service_type
- Geographic area list validation against predefined list
- Breeding break logic: Can't breed if current_date < breeding_break_until
```

---

## Feature Additions

### 1. **Breeding Status Badge System**
Show on table/cards:
- ✓ Vaccination Status (Green/Yellow/Red based on expiry)
- ✓ Certified/Uncertified badge
- ✓ Available/On Break status
- ✓ Service type indicator

### 2. **Breeding Availability Calendar**
- [ ] Visual calendar showing breeding availability
- [ ] Shows when pet is on breeding break
- [ ] Shows last breeding date
- [ ] Toggle availability by date range

### 3. **Breeding Inquiry Management**
- [ ] Create inquiry form for public users
- [ ] Store inquiries in database
- [ ] Notify breeder of new inquiries
- [ ] Track inquiry status (new, contacted, confirmed, declined)

### 4. **Reports & Analytics**
- [ ] Breeding history report (litter dates, sizes, outcomes)
- [ ] Revenue tracking (breeding fees)
- [ ] Most requested pets/bloodlines
- [ ] Breeding success rate (if litter outcome tracking added)

---

## Form UI Structure (Recommended)

### Modal Layout with Tabs
```
┌─────────────────────────────────────────────┐
│ Add/Edit Breeding Pet                    [✕] │
├─────────────────────────────────────────────┤
│ [Basic] [Health] [History] [Genetics] ...   │
├─────────────────────────────────────────────┤
│                                             │
│  Tab Content (Dynamic)                      │
│                                             │
│                                             │
├─────────────────────────────────────────────┤
│              [Cancel]  [Save Pet]           │
└─────────────────────────────────────────────┘
```

### Or Collapsible Sections
```
┌─────────────────────────────────────────────┐
│ Add/Edit Breeding Pet                    [✕] │
├─────────────────────────────────────────────┤
│ ▼ Basic Information                         │
│   [Name] [Breed] [Gender] [DOB]            │
│                                             │
│ ▼ Health Information                        │
│   [Vaccinations] [Vet Checkup] [Certs]    │
│                                             │
│ ▼ Breeding Details                          │
│   [Fee] [Service Type] [Availability]      │
│                                             │
│ ▼ Advanced (Genetics, History, etc.)       │
│   [Pedigree] [DNA] [Litters] [Titles]     │
├─────────────────────────────────────────────┤
│              [Cancel]  [Save Pet]           │
└─────────────────────────────────────────────┘
```

---

## Implementation Priority

### Phase 1 (Core)
1. Add database columns
2. Update API to handle new fields
3. Update form HTML with basic health and breeding fee fields
4. Update JavaScript validation
5. Test basic CRUD operations

### Phase 2 (Enhanced)
1. Add genetics and pedigree fields
2. Implement file upload for DNA tests
3. Add collapsible/tabbed sections
4. Add more complex validations
5. Add search/filter functionality

### Phase 3 (Public Features)
1. Create public breeding pets directory
2. Add search and filter
3. Implement inquiry system
4. Add public-facing information display

---

## Testing Checklist

- [ ] Add new breeding pet with all fields
- [ ] Edit existing breeding pet and update select fields
- [ ] Validate client-side validation works
- [ ] Validate server-side validation works
- [ ] Test file upload for DNA results
- [ ] Test JSON serialization for multi-select fields
- [ ] Test breeding break date logic
- [ ] Test deposit conditional display
- [ ] Verify data persists in database
- [ ] Test responsive design on mobile
- [ ] Test with special characters in text fields
- [ ] Test with very large descriptions/notes
- [ ] Test date range validations
- [ ] Test phone number formatting

---

## Additional Considerations

### Security
- Sanitize all text inputs
- Validate file uploads (type, size, content)
- Implement file virus scanning for uploaded documents
- Rate limit breeding inquiry submissions

### Performance
- Index common search fields (breeding_fee, service_type)
- Consider pagination for large breeding pet lists
- Cache breeding pet listings if public directory created

### Compliance
- Ensure HIPAA compliance if storing health records
- Add data export functionality for record-keeping
- Implement audit logging for changes

---

## Dependencies & Libraries

- No new dependencies required
- Uses existing form validation patterns
- Uses existing file upload infrastructure
- Compatible with existing ImageUploader class
- Works with existing modal system

---

## Estimated Effort

| Component | Effort | Notes |
|-----------|--------|-------|
| Database migration | 1-2 hrs | SQL schema creation |
| API updates | 3-4 hrs | CRUD, validation, file handling |
| Frontend form | 4-5 hrs | HTML, form fields, conditional display |
| JavaScript | 3-4 hrs | Validation, AJAX, event handling |
| CSS styling | 2-3 hrs | Modal tabs/sections, responsiveness |
| Testing | 2-3 hrs | Manual and edge cases |
| **Total** | **15-21 hrs** | |

---

## References & Related Features

- Existing breeding pets management system
- My Pets management (similar form patterns)
- Lost & Found reward system (similar field types)
- Service provider availability system (geographic filtering)
