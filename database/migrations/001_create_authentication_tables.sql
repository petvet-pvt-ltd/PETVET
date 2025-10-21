-- ============================================
-- PETVET Database Migration - Authentication Module
-- Version: 1.0.0
-- Created: October 21, 2025
-- Description: Complete authentication and user management system
-- ============================================

-- Create Database
CREATE DATABASE IF NOT EXISTS petvet CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE petvet;

-- ============================================
-- 1. USERS TABLE (Core User Entity)
-- ============================================
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL COMMENT 'bcrypt hashed password',
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    avatar VARCHAR(255) DEFAULT NULL COMMENT 'Path to profile picture',
    email_verified BOOLEAN DEFAULT FALSE,
    email_verification_token VARCHAR(255) DEFAULT NULL,
    is_active BOOLEAN DEFAULT TRUE COMMENT 'Account active status',
    is_blocked BOOLEAN DEFAULT FALSE COMMENT 'Admin can block users',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_active (is_active),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Core user accounts table';

-- ============================================
-- 2. ROLES TABLE (Available System Roles)
-- ============================================
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    role_name VARCHAR(50) UNIQUE NOT NULL COMMENT 'System identifier (pet_owner, vet, etc.)',
    role_display_name VARCHAR(100) NOT NULL COMMENT 'Human-readable name',
    description TEXT,
    requires_verification BOOLEAN DEFAULT FALSE COMMENT 'Needs admin approval',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_role_name (role_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Available system roles';

-- Insert default roles
INSERT INTO roles (role_name, role_display_name, description, requires_verification) VALUES 
('pet_owner', 'Pet Owner', 'Regular pet owner who can book appointments and manage pets', FALSE),
('vet', 'Veterinarian', 'Licensed veterinarian providing medical services', TRUE),
('clinic_manager', 'Clinic Manager', 'Manages clinic operations and staff', TRUE),
('admin', 'Administrator', 'System administrator with full access', FALSE),
('receptionist', 'Receptionist', 'Front desk staff managing appointments', FALSE),
('trainer', 'Pet Trainer', 'Professional pet trainer offering training services', TRUE),
('sitter', 'Pet Sitter', 'Professional pet sitter offering sitting services', TRUE),
('breeder', 'Pet Breeder', 'Professional breeder managing breeding operations', TRUE),
('groomer', 'Pet Groomer', 'Professional groomer offering grooming services', TRUE);

-- ============================================
-- 3. USER_ROLES TABLE (Users ↔ Roles Junction)
-- ============================================
CREATE TABLE user_roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    role_id INT NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE COMMENT 'Users default landing role',
    is_active BOOLEAN DEFAULT TRUE COMMENT 'Can temporarily disable a role',
    verification_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    verification_notes TEXT COMMENT 'Admin notes on approval/rejection',
    verified_by INT DEFAULT NULL COMMENT 'Admin user who verified',
    verified_at TIMESTAMP NULL,
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_user_role (user_id, role_id),
    INDEX idx_user_role (user_id, role_id),
    INDEX idx_verification (verification_status),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='User to roles mapping with verification';

-- ============================================
-- 4. ROLE_VERIFICATION_DOCUMENTS TABLE
-- ============================================
CREATE TABLE role_verification_documents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_role_id INT NOT NULL,
    document_type ENUM('license', 'certificate', 'id', 'business_permit', 'other') NOT NULL,
    document_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL COMMENT 'Relative path from uploads directory',
    file_size INT NOT NULL COMMENT 'File size in bytes',
    mime_type VARCHAR(100) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_role_id) REFERENCES user_roles(id) ON DELETE CASCADE,
    INDEX idx_user_role (user_role_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Verification documents for service providers';

-- ============================================
-- 5. SESSIONS TABLE (Active User Sessions)
-- ============================================
CREATE TABLE sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    session_token VARCHAR(255) UNIQUE NOT NULL,
    ip_address VARCHAR(45) COMMENT 'IPv4 or IPv6',
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_session (user_id),
    INDEX idx_token (session_token),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Active user sessions';

-- ============================================
-- 6. PASSWORD_RESETS TABLE
-- ============================================
CREATE TABLE password_resets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    reset_token VARCHAR(255) UNIQUE NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    used BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token (reset_token),
    INDEX idx_user (user_id),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Password reset tokens';

-- ============================================
-- 7. LOGIN_ATTEMPTS TABLE (Brute Force Protection)
-- ============================================
CREATE TABLE login_attempts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    success BOOLEAN DEFAULT FALSE,
    INDEX idx_email_ip (email, ip_address),
    INDEX idx_attempted (attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Track login attempts for security';

-- ============================================
-- 8. CLINICS TABLE
-- ============================================
CREATE TABLE clinics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    clinic_name VARCHAR(255) NOT NULL,
    clinic_address TEXT NOT NULL,
    district VARCHAR(100),
    city VARCHAR(100),
    clinic_phone VARCHAR(20),
    clinic_email VARCHAR(255),
    operating_hours JSON COMMENT 'Store as {"monday": "9:00-17:00", ...}',
    license_document VARCHAR(500),
    verification_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_district (district),
    INDEX idx_status (verification_status),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Veterinary clinics';

-- ============================================
-- 9. PET_OWNER_PROFILES TABLE
-- ============================================
CREATE TABLE pet_owner_profiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    preferred_vet_id INT DEFAULT NULL,
    emergency_contact_name VARCHAR(255),
    emergency_contact_phone VARCHAR(20),
    notification_preferences JSON COMMENT 'Email, SMS, push notification settings',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Pet owner specific profile data';

-- ============================================
-- 10. VET_PROFILES TABLE
-- ============================================
CREATE TABLE vet_profiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    clinic_id INT DEFAULT NULL,
    license_number VARCHAR(100) UNIQUE NOT NULL,
    specialization VARCHAR(255),
    years_experience INT DEFAULT 0,
    education TEXT,
    consultation_fee DECIMAL(10,2) DEFAULT 0.00,
    rating DECIMAL(3,2) DEFAULT 0.00 COMMENT 'Average rating 0.00-5.00',
    total_reviews INT DEFAULT 0,
    bio TEXT,
    available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE SET NULL,
    INDEX idx_clinic (clinic_id),
    INDEX idx_available (available),
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Veterinarian profiles';

-- ============================================
-- 11. CLINIC_MANAGER_PROFILES TABLE
-- ============================================
CREATE TABLE clinic_manager_profiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    clinic_id INT NOT NULL,
    position VARCHAR(100) DEFAULT 'Manager',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_clinic (clinic_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Clinic manager profiles';

-- ============================================
-- 12. SERVICE_PROVIDER_PROFILES TABLE
-- ============================================
CREATE TABLE service_provider_profiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    role_type ENUM('trainer', 'sitter', 'breeder', 'groomer') NOT NULL,
    business_name VARCHAR(255),
    service_area VARCHAR(255) COMMENT 'City/District where services offered',
    experience_years INT DEFAULT 0,
    certifications TEXT COMMENT 'Comma-separated or JSON array',
    specializations JSON COMMENT 'Array of specialties',
    price_range_min DECIMAL(10,2),
    price_range_max DECIMAL(10,2),
    rating DECIMAL(3,2) DEFAULT 0.00,
    total_reviews INT DEFAULT 0,
    bio TEXT,
    available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_role_type (user_id, role_type),
    INDEX idx_role_type (role_type),
    INDEX idx_area (service_area),
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Profiles for trainers, sitters, breeders, groomers';

-- ============================================
-- 13. AUDIT_LOGS TABLE (Security & Monitoring)
-- ============================================
CREATE TABLE audit_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT DEFAULT NULL,
    action VARCHAR(100) NOT NULL COMMENT 'login, logout, role_switch, profile_update, etc.',
    ip_address VARCHAR(45),
    user_agent TEXT,
    details JSON COMMENT 'Additional context about the action',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_action (action),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Audit trail for security monitoring';

-- ============================================
-- CREATE DEFAULT ADMIN USER
-- ============================================
-- Password: Admin@123 (hashed with PASSWORD_HASH)
-- IMPORTANT: Change this password after first login!
INSERT INTO users (email, password, first_name, last_name, email_verified, is_active) 
VALUES (
    'admin@petvet.com', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
    'System', 
    'Administrator', 
    TRUE, 
    TRUE
);

-- Assign admin role
INSERT INTO user_roles (user_id, role_id, is_primary, is_active, verification_status, verified_at)
SELECT 
    (SELECT id FROM users WHERE email = 'admin@petvet.com'),
    (SELECT id FROM roles WHERE role_name = 'admin'),
    TRUE,
    TRUE,
    'approved',
    NOW();

-- ============================================
-- SAMPLE DATA FOR TESTING (Optional - Remove in Production)
-- ============================================

-- Sample Pet Owner
INSERT INTO users (email, password, first_name, last_name, phone, email_verified, is_active) 
VALUES (
    'john.doe@example.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- Password: password
    'John',
    'Doe',
    '0771234567',
    TRUE,
    TRUE
);

INSERT INTO user_roles (user_id, role_id, is_primary, is_active, verification_status, verified_at)
SELECT 
    (SELECT id FROM users WHERE email = 'john.doe@example.com'),
    (SELECT id FROM roles WHERE role_name = 'pet_owner'),
    TRUE,
    TRUE,
    'approved',
    NOW();

-- Sample Clinic
INSERT INTO clinics (clinic_name, clinic_address, district, city, clinic_phone, clinic_email, verification_status, is_active)
VALUES (
    'Happy Paws Veterinary Clinic',
    '123 Main Street, Colombo',
    'Colombo',
    'Colombo 07',
    '0112345678',
    'info@happypaws.lk',
    'approved',
    TRUE
);

-- Sample Vet
INSERT INTO users (email, password, first_name, last_name, phone, email_verified, is_active) 
VALUES (
    'dr.sarah@happypaws.lk',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Sarah',
    'Johnson',
    '0777654321',
    TRUE,
    TRUE
);

INSERT INTO user_roles (user_id, role_id, is_primary, is_active, verification_status, verified_at)
SELECT 
    (SELECT id FROM users WHERE email = 'dr.sarah@happypaws.lk'),
    (SELECT id FROM roles WHERE role_name = 'vet'),
    TRUE,
    TRUE,
    'approved',
    NOW();

INSERT INTO vet_profiles (user_id, clinic_id, license_number, specialization, years_experience, bio, available)
SELECT 
    (SELECT id FROM users WHERE email = 'dr.sarah@happypaws.lk'),
    (SELECT id FROM clinics WHERE clinic_name = 'Happy Paws Veterinary Clinic'),
    'VET-LK-2020-001234',
    'General Practice, Surgery',
    8,
    'Experienced veterinarian specializing in small animals with a passion for preventive care.',
    TRUE;

-- ============================================
-- VERIFICATION & CLEANUP QUERIES
-- ============================================

-- View all tables
-- SHOW TABLES;

-- Check users and their roles
-- SELECT 
--     u.id, u.email, u.first_name, u.last_name,
--     r.role_display_name, ur.verification_status, ur.is_primary
-- FROM users u
-- LEFT JOIN user_roles ur ON u.id = ur.user_id
-- LEFT JOIN roles r ON ur.role_id = r.id;

-- Check role distribution
-- SELECT r.role_display_name, COUNT(ur.id) as user_count
-- FROM roles r
-- LEFT JOIN user_roles ur ON r.id = ur.role_id AND ur.verification_status = 'approved'
-- GROUP BY r.id, r.role_display_name;

-- ============================================
-- END OF MIGRATION
-- ============================================
