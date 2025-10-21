-- Add clinic manager test account
-- Email: manager@gmail.com
-- Password: password123

USE petvet;

-- Insert clinic manager user
INSERT INTO users (email, password, first_name, last_name, phone, address, avatar, is_active, email_verified, created_at)
VALUES ('manager@gmail.com', '$2y$10$BINhO08G1d0W.jnwQ/xHDeDz.B9uzRxfgo9.osphNDGYUPYlESdHi', 'Mike', 'Manager', '0776234567', '888 Clinic Ave, Colombo', NULL, 1, 1, NOW());

-- Get the user ID
SET @manager_id = (SELECT id FROM users WHERE email = 'manager@gmail.com');

-- Get clinic_manager role ID
SET @role_clinic_manager = (SELECT id FROM roles WHERE role_name = 'clinic_manager');

-- Assign clinic_manager role (approved)
INSERT INTO user_roles (user_id, role_id, is_primary, verification_status, applied_at)
VALUES (@manager_id, @role_clinic_manager, 1, 'approved', NOW());

-- Create clinic manager profile (assuming clinic_id 1 exists)
INSERT INTO clinic_manager_profiles (user_id, clinic_id, position, created_at)
VALUES (@manager_id, 1, 'Clinic Manager', NOW());

-- Verify
SELECT u.email, u.first_name, r.role_name, 'password123' as password_hint
FROM users u
JOIN user_roles ur ON u.id = ur.user_id
JOIN roles r ON ur.role_id = r.id
WHERE u.email = 'manager@gmail.com';
