-- Update all account emails to simple, easy-to-remember usernames
-- All passwords remain: password123

USE petvet;

-- Update existing accounts with simple emails
UPDATE users SET email = 'admin@gmail.com' WHERE email = 'admin@petvet.com';
UPDATE users SET email = 'petowner@gmail.com' WHERE email = 'john.doe@example.com';
UPDATE users SET email = 'vet@gmail.com' WHERE email = 'dr.sarah@happypaws.lk';
UPDATE users SET email = 'manager@gmail.com' WHERE email = 'manager@happypaws.lk';
UPDATE users SET email = 'recep@gmail.com' WHERE email = 'receptionist@gmail.com';

-- Verify the changes
SELECT 
    u.email, 
    u.first_name, 
    u.last_name, 
    r.role_name,
    'password123' as password_hint
FROM users u
LEFT JOIN user_roles ur ON u.id = ur.user_id
LEFT JOIN roles r ON ur.role_id = r.id
WHERE u.email IN (
    'admin@gmail.com',
    'petowner@gmail.com',
    'vet@gmail.com',
    'manager@gmail.com',
    'recep@gmail.com',
    'trainer@gmail.com',
    'sitter@gmail.com',
    'breeder@gmail.com',
    'groomer@gmail.com'
)
ORDER BY u.email;

-- Clear any login attempts for old emails
DELETE FROM login_attempts WHERE email IN (
    'admin@petvet.com',
    'john.doe@example.com',
    'dr.sarah@happypaws.lk',
    'manager@happypaws.lk',
    'receptionist@gmail.com'
);
