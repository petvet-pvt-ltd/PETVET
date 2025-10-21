-- Update passwords for all test accounts
-- New password for all accounts: password123
-- Hash: $2y$10$BINhO08G1d0W.jnwQ/xHDeDz.B9uzRxfgo9.osphNDGYUPYlESdHi

UPDATE `users` 
SET `password` = '$2y$10$BINhO08G1d0W.jnwQ/xHDeDz.B9uzRxfgo9.osphNDGYUPYlESdHi' 
WHERE `email` IN (
    'trainer@gmail.com',
    'sitter@gmail.com',
    'breeder@gmail.com',
    'groomer@gmail.com',
    'receptionist@gmail.com'
);

-- Verify the update
SELECT email, first_name, last_name, 'password123' as password_hint 
FROM users 
WHERE email IN (
    'trainer@gmail.com',
    'sitter@gmail.com',
    'breeder@gmail.com',
    'groomer@gmail.com',
    'receptionist@gmail.com'
);
