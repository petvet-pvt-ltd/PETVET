-- Add test accounts for all roles
-- Password for all accounts: password123
-- (bcrypt hash of 'password123')

-- Insert Users
INSERT INTO `users` (`email`, `password`, `first_name`, `last_name`, `phone`, `address`, `avatar`, `is_active`, `email_verified`, `created_at`) VALUES
('trainer@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tom', 'Trainer', '0771234567', '123 Trainer St, Colombo', NULL, 1, 1, NOW()),
('sitter@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sam', 'Sitter', '0772234567', '456 Sitter Ave, Kandy', NULL, 1, 1, NOW()),
('breeder@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Bob', 'Breeder', '0773234567', '789 Breeder Rd, Galle', NULL, 1, 1, NOW()),
('groomer@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Grace', 'Groomer', '0774234567', '321 Groomer Lane, Negombo', NULL, 1, 1, NOW()),
('receptionist@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Rita', 'Receptionist', '0775234567', '654 Clinic St, Colombo 7', NULL, 1, 1, NOW());

-- Assign roles to users
-- Get the user IDs first (they will be auto-incremented)
SET @trainer_id = (SELECT id FROM users WHERE email = 'trainer@gmail.com');
SET @sitter_id = (SELECT id FROM users WHERE email = 'sitter@gmail.com');
SET @breeder_id = (SELECT id FROM users WHERE email = 'breeder@gmail.com');
SET @groomer_id = (SELECT id FROM users WHERE email = 'groomer@gmail.com');
SET @receptionist_id = (SELECT id FROM users WHERE email = 'receptionist@gmail.com');

-- Get role IDs
SET @role_trainer = (SELECT id FROM roles WHERE role_name = 'trainer');
SET @role_sitter = (SELECT id FROM roles WHERE role_name = 'sitter');
SET @role_breeder = (SELECT id FROM roles WHERE role_name = 'breeder');
SET @role_groomer = (SELECT id FROM roles WHERE role_name = 'groomer');
SET @role_receptionist = (SELECT id FROM roles WHERE role_name = 'receptionist');

-- Assign roles (all approved and active)
INSERT INTO `user_roles` (`user_id`, `role_id`, `is_primary`, `verification_status`, `applied_at`) VALUES
(@trainer_id, @role_trainer, 1, 'approved', NOW()),
(@sitter_id, @role_sitter, 1, 'approved', NOW()),
(@breeder_id, @role_breeder, 1, 'approved', NOW()),
(@groomer_id, @role_groomer, 1, 'approved', NOW()),
(@receptionist_id, @role_receptionist, 1, 'approved', NOW());

-- Create service provider profiles for trainer, sitter, breeder, and groomer
INSERT INTO `service_provider_profiles` (`user_id`, `role_type`, `business_name`, `service_area`, `experience_years`, `certifications`, `rating`, `total_reviews`, `bio`, `available`, `created_at`) VALUES
(@trainer_id, 'trainer', 'Pro Pet Training', 'Colombo, Kandy', 8, 'Certified Professional Dog Trainer (CPDT)', 4.8, 45, 'Professional dog training services for all breeds', 1, NOW()),
(@sitter_id, 'sitter', 'Caring Pet Sitters', 'Kandy, Peradeniya', 5, 'Pet First Aid Certified', 4.9, 67, 'Trusted pet sitting and boarding services', 1, NOW()),
(@breeder_id, 'breeder', 'Premium Breeders', 'Galle, Matara', 12, 'Registered Breeder - Kennel Club', 4.7, 23, 'Ethical breeding of purebred dogs and cats', 1, NOW()),
(@groomer_id, 'groomer', 'Pawfect Grooming', 'Negombo, Colombo', 6, 'Certified Master Groomer', 5.0, 89, 'Professional grooming services for all pets', 1, NOW());

-- Display success message
SELECT 'Test accounts created successfully!' AS message;
SELECT 
    u.email, 
    u.first_name, 
    u.last_name, 
    r.role_name,
    ur.verification_status
FROM users u
JOIN user_roles ur ON u.id = ur.user_id
JOIN roles r ON ur.role_id = r.id
WHERE u.email IN (
    'trainer@gmail.com',
    'sitter@gmail.com', 
    'breeder@gmail.com',
    'groomer@gmail.com',
    'receptionist@gmail.com'
)
ORDER BY u.email;
