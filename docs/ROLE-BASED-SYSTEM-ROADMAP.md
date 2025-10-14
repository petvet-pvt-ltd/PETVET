# Role-Based System Implementation Roadmap

## Overview
Implementation of a flexible role-based system allowing users to have multiple overlapping roles: Pet Owner, Trainer, Groomer, Sitter, and Breeder.

## Database Schema

### Core Tables
```sql
-- Users table (main user entity)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Roles table
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- User roles junction table
CREATE TABLE user_roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    role_id INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_role (user_id, role_id)
);

-- Insert default roles
INSERT INTO roles (name, description) VALUES 
('pet_owner', 'Pet Owner'),
('trainer', 'Pet Trainer'),
('groomer', 'Pet Groomer'),
('sitter', 'Pet Sitter'),
('breeder', 'Pet Breeder'),
('receptionist', 'Clinic Receptionist');
```

### Role-Specific Profile Tables
```sql
-- Service provider profiles (common data)
CREATE TABLE service_provider_profiles (
    user_id INT PRIMARY KEY,
    business_name VARCHAR(255),
    description TEXT,
    years_experience INT,
    service_area TEXT,
    is_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Clinic staff profiles (for receptionists and clinic employees)
CREATE TABLE clinic_staff_profiles (
    user_id INT PRIMARY KEY,
    clinic_id INT NOT NULL,
    position VARCHAR(100),
    hire_date DATE,
    shift_preference ENUM('morning', 'afternoon', 'evening', 'flexible'),
    skills TEXT,
    software_experience TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    -- Note: clinic_id would reference a clinics table when implemented
);

-- Trainer specific data
CREATE TABLE trainer_profiles (
    user_id INT PRIMARY KEY,
    specialization VARCHAR(255),
    training_methods TEXT,
    hourly_rate DECIMAL(10,2),
    license_document VARCHAR(255), -- PDF file path (optional)
    certifications TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Groomer specific data
CREATE TABLE groomer_profiles (
    user_id INT PRIMARY KEY,
    services_offered TEXT,
    equipment_list TEXT,
    pricing_structure TEXT,
    business_license VARCHAR(255) NOT NULL, -- PDF file path (required)
    insurance_info TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Sitter specific data
CREATE TABLE sitter_profiles (
    user_id INT PRIMARY KEY,
    pet_types_accepted TEXT,
    home_type VARCHAR(100),
    yard_available BOOLEAN DEFAULT FALSE,
    overnight_care BOOLEAN DEFAULT FALSE,
    daily_rate DECIMAL(10,2),
    max_pets_at_once INT,
    availability_schedule TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Breeder specific data
CREATE TABLE breeder_profiles (
    user_id INT PRIMARY KEY,
    breeds_specialized TEXT,
    breeding_license VARCHAR(255) NOT NULL, -- PDF file path (required)
    kennel_registration VARCHAR(255),
    health_testing_info TEXT,
    breeding_philosophy TEXT,
    available_animals TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## Registration System

### Multi-Step Registration Process

#### Step 1: Basic User Information
- First Name, Last Name
- Email, Phone, Address
- Password, Confirm Password

#### Step 2: Role Selection
Checkbox-based selection allowing multiple roles:
- ☐ I'm a Pet Owner
- ☐ I provide Training Services
- ☐ I provide Grooming Services  
- ☐ I provide Pet Sitting Services
- ☐ I provide Breeding Services

#### Step 3: Role-Specific Setup (Dynamic)
Show forms based on selected roles:

**Pet Owner Setup:**
- Pet information forms (if applicable)

**Trainer Setup:**
- Specialization (obedience, agility, behavioral, etc.)
- Training methods
- Years of experience
- Hourly rate
- Certifications
- License document upload (optional PDF)

**Groomer Setup:**
- Services offered (bath, nail trim, full groom, etc.)
- Equipment available
- Pricing structure
- Years of experience
- Business license upload (required PDF)
- Insurance information

**Sitter Setup:**
- Pet types accepted
- Home type (apartment, house, etc.)
- Yard availability
- Overnight care capability
- Daily rates
- Maximum pets at once
- Availability schedule

**Breeder Setup:**
- Breeds specialized in
- Breeding license upload (required PDF)
- Kennel registration
- Health testing protocols
- Breeding philosophy
- Current available animals

## User Interface

### Role Switcher Component
Location: Sidebar/Header
```
Current Role: Pet Owner ↓
├── 🐾 Pet Owner (current)
├── 👨‍🏫 Trainer
├── ✂️ Groomer
├── 🏠 Sitter
├── 🐕‍🦺 Breeder
├── ─────────────────
├── + Become a Trainer
├── + Become a Groomer
├── + Become a Sitter
├── + Become a Breeder
```

### Dashboard Layouts (Role-Specific)

**Pet Owner Dashboard:**
- My Pets
- Find Services
- My Appointments
- Pet Health Records

**Trainer Dashboard:**
- Training Clients
- Upcoming Sessions
- Earnings Overview
- Client Progress Reports

**Groomer Dashboard:**
- Grooming Appointments
- Service Packages
- Client Pets
- Revenue Reports

**Sitter Dashboard:**
- Sitting Requests
- Current Bookings
- Calendar View
- Client Reviews

**Breeder Dashboard:**
- Available Litters
- Breeding Schedule
- Inquiry Management
- Health Records

**Receptionist Dashboard:**
- Today's Appointments
- Appointment Management (same as clinic manager)
- Client Communication
- Emergency Scheduling

## Shared Components Architecture

### Appointment Management System
To avoid code duplication between Clinic Manager and Receptionist roles, shared components are implemented:

```
views/shared/appointments/
├── calendar-component.php (Shared calendar view)
├── appointment-modals.php (Add/Edit/View modals)
└── appointment-functions.php (Common JavaScript functions)
```

**Benefits:**
- Single source of truth for appointment logic
- Consistent UI/UX across roles
- Easy maintenance and updates
- Reduced code duplication

**Usage:**
```php
// In clinic manager appointments.php
include __DIR__ . '/../shared/appointments/calendar-component.php';
include __DIR__ . '/../shared/appointments/appointment-modals.php';

// In receptionist appointments.php  
include __DIR__ . '/../shared/appointments/calendar-component.php';
include __DIR__ . '/../shared/appointments/appointment-modals.php';
```

## File Upload System

### Document Upload Requirements
- **Trainers**: License documents (optional) - PDF only
- **Groomers**: Business license (required) - PDF only  
- **Sitters**: No document requirements
- **Breeders**: Breeding license (required) - PDF only

### File Handling
- Upload directory: `/uploads/documents/[user_id]/`
- File naming: `[role]_[document_type]_[timestamp].pdf`
- File size limit: 5MB per file
- Validation: PDF format only for required documents

## Implementation Phases

### Phase 1: Core System (Priority 1)
- [ ] Database schema creation
- [ ] Multi-step registration form
- [ ] Basic role management
- [ ] Role switcher component
- [ ] File upload system

### Phase 2: Role-Specific Features (Priority 2)
- [ ] Role-specific dashboards
- [ ] Service provider profiles
- [ ] Document verification system
- [ ] Role addition for existing users

### Phase 3: Advanced Features (Priority 3)
- [ ] Admin verification system
- [ ] Rating/review system for service providers
- [ ] Advanced search and filtering
- [ ] Notification system

## Technical Considerations

### Security
- File upload validation (PDF only, size limits)
- User authentication and session management
- Role-based access control
- Document storage security

### Performance
- Efficient role checking queries
- Image/document optimization
- Database indexing on user_roles table

### User Experience
- Progressive form disclosure
- Clear role switching indicators
- Responsive design for all components
- Form validation and error handling

## File Structure Updates

### File Structure Updates

### New Files Needed
```
register/
├── multi-step-registration.php (replaces client-reg.php)
├── role-setup-forms/
│   ├── trainer-setup.php
│   ├── groomer-setup.php
│   ├── sitter-setup.php
│   └── breeder-setup.php
└── process/
    ├── registration-process.php
    └── role-addition-process.php

views/shared/appointments/
├── calendar-component.php (Shared appointment calendar)
├── appointment-modals.php (Add/Edit/View appointment modals)
└── appointment-functions.php (Common JavaScript functions)

views/receptionist/
├── dashboard.php (Receptionist dashboard)
├── appointments.php (Uses shared components)
└── settings.php

controllers/
└── ReceptionistController.php (Handles receptionist functionality)

components/
├── role-switcher.php
└── file-upload.php

uploads/
└── documents/
    └── [user_id]/
        ├── trainer_license_[timestamp].pdf
        ├── groomer_business_license_[timestamp].pdf
        └── breeder_license_[timestamp].pdf
```

### CSS/JS Requirements
- Multi-step form styling
- Role switcher dropdown styling  
- File upload component styling
- Form validation scripts
- Role switching functionality

## Success Metrics
- User registration completion rate
- Role adoption rate (users adding multiple roles)
- Document verification success rate
- User satisfaction with role switching experience

---

**Next Steps:**
1. Implement database schema
2. Create multi-step registration form
3. Build role-specific setup forms
4. Develop role switcher component
5. Test with various role combinations