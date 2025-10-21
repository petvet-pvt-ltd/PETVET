-- Fix all passwords to use the correct hash for password123
-- Hash: $2y$10$BINhO08G1d0W.jnwQ/xHDeDz.B9uzRxfgo9.osphNDGYUPYlESdHi

USE petvet;

-- Update all account passwords
UPDATE users SET password = '$2y$10$BINhO08G1d0W.jnwQ/xHDeDz.B9uzRxfgo9.osphNDGYUPYlESdHi' WHERE email IN (
    'admin@gmail.com',
    'petowner@gmail.com',
    'vet@gmail.com',
    'manager@gmail.com',
    'recep@gmail.com',
    'trainer@gmail.com',
    'sitter@gmail.com',
    'breeder@gmail.com',
    'groomer@gmail.com'
);

-- Clear all login attempts
DELETE FROM login_attempts WHERE email IN (
    'admin@gmail.com',
    'petowner@gmail.com',
    'vet@gmail.com',
    'manager@gmail.com',
    'recep@gmail.com',
    'trainer@gmail.com',
    'sitter@gmail.com',
    'breeder@gmail.com',
    'groomer@gmail.com'
);

-- Verify
SELECT email, first_name, LEFT(password, 30) as pwd_check, 'password123' as hint
FROM users 
WHERE email IN (
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
ORDER BY email;
