-- ============================================
-- PETVET Database Migration - Clinic Staff Management
-- Version: 1.0.0
-- Created: October 22, 2025
-- Description: Create clinic_staff table for managing clinic staff members
-- ============================================

USE petvet;

-- ============================================
-- CLINIC_STAFF TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS clinic_staff (
    id INT PRIMARY KEY AUTO_INCREMENT,
    clinic_id INT NOT NULL COMMENT 'Reference to the clinic this staff belongs to',
    user_id INT DEFAULT NULL COMMENT 'Link to users table if staff has system account (e.g., receptionist)',
    name VARCHAR(255) NOT NULL,
    role VARCHAR(100) NOT NULL COMMENT 'Veterinary Assistant, Front Desk, Support Staff, etc.',
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    next_shift VARCHAR(255) DEFAULT NULL COMMENT 'Next scheduled shift information',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_clinic_id (clinic_id),
    INDEX idx_user_id (user_id),
    INDEX idx_role (role),
    INDEX idx_status (status),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Clinic staff members management';

-- ============================================
-- SEED DATA (Sample staff for clinic_id = 1)
-- ============================================
INSERT INTO clinic_staff (clinic_id, user_id, name, role, email, phone, status, next_shift) VALUES
-- Veterinary Assistants
(1, NULL, 'Anushka Perera', 'Veterinary Assistant', 'anushka.assist@petvet.lk', '+94 71 234 5678', 'Active', NULL),
(1, NULL, 'Nimasha De Silva', 'Veterinary Assistant', 'nimasha.assist@petvet.lk', '+94 71 444 8899', 'Active', NULL),
(1, NULL, 'Kavinda Fernando', 'Veterinary Assistant', 'kavinda.assist@petvet.lk', '+94 77 555 1234', 'Active', NULL),
(1, NULL, 'Sachini Wijesinghe', 'Veterinary Assistant', 'sachini.assist@petvet.lk', '+94 76 888 9999', 'Active', NULL),

-- Front Desk Staff
(1, NULL, 'Malini Silva', 'Front Desk', 'malini.front@petvet.lk', '+94 77 987 6543', 'Active', NULL),
(1, NULL, 'Tharindu Gamage', 'Front Desk', 'tharindu.front@petvet.lk', '+94 71 222 3333', 'Active', NULL),

-- Support Staff
(1, NULL, 'Ruwan Jayasuriya', 'Support Staff', 'ruwan.support@petvet.lk', '+94 76 111 2244', 'Active', NULL),
(1, NULL, 'Dilani Rathnayake', 'Support Staff', 'dilani.support@petvet.lk', '+94 77 444 5555', 'Active', NULL),
(1, NULL, 'Kasun Bandara', 'Support Staff', 'kasun.support@petvet.lk', '+94 71 666 7777', 'Active', NULL),
(1, NULL, 'Chamika Herath', 'Support Staff', 'chamika.support@petvet.lk', '+94 76 999 1111', 'Active', NULL);

-- Verify table creation and data
SELECT 
    id, 
    name, 
    role, 
    email, 
    phone, 
    status,
    created_at 
FROM clinic_staff 
ORDER BY role, name;

SELECT 
    'clinic_staff' as table_name,
    COUNT(*) as total_records
FROM clinic_staff;
