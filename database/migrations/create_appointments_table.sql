-- Create appointments table for booking system
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pet_id INT NOT NULL,
    pet_owner_id INT NOT NULL,
    clinic_id INT NOT NULL,
    vet_id INT NULL,  -- NULL means "any available vet"
    appointment_type VARCHAR(50) NOT NULL,
    symptoms TEXT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    duration_minutes INT DEFAULT 20,
    status ENUM('pending', 'approved', 'declined', 'completed', 'cancelled', 'no_show') DEFAULT 'pending',
    decline_reason TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    approved_by INT NULL,  -- receptionist who approved
    approved_at TIMESTAMP NULL,
    
    -- Foreign keys
    CONSTRAINT fk_appointment_pet FOREIGN KEY (pet_id) REFERENCES pets(id) ON DELETE CASCADE,
    CONSTRAINT fk_appointment_owner FOREIGN KEY (pet_owner_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_appointment_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE CASCADE,
    CONSTRAINT fk_appointment_vet FOREIGN KEY (vet_id) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_appointment_approver FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    
    -- Indexes for performance
    INDEX idx_appointment_date (appointment_date),
    INDEX idx_appointment_status (status),
    INDEX idx_clinic_date (clinic_id, appointment_date),
    INDEX idx_vet_date (vet_id, appointment_date),
    INDEX idx_owner (pet_owner_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
