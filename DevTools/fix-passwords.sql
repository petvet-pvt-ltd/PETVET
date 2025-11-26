USE petvet;

-- Fix passwords with correct bcrypt hash
UPDATE users SET password = '$2y$10$BINhO08G1d0W.jnwQ/xHDeDz.B9uzRxfgo9.osphNDGYUPYlESdHi' WHERE email = 'trainer@gmail.com';
UPDATE users SET password = '$2y$10$BINhO08G1d0W.jnwQ/xHDeDz.B9uzRxfgo9.osphNDGYUPYlESdHi' WHERE email = 'sitter@gmail.com';
UPDATE users SET password = '$2y$10$BINhO08G1d0W.jnwQ/xHDeDz.B9uzRxfgo9.osphNDGYUPYlESdHi' WHERE email = 'breeder@gmail.com';
UPDATE users SET password = '$2y$10$BINhO08G1d0W.jnwQ/xHDeDz.B9uzRxfgo9.osphNDGYUPYlESdHi' WHERE email = 'groomer@gmail.com';
UPDATE users SET password = '$2y$10$BINhO08G1d0W.jnwQ/xHDeDz.B9uzRxfgo9.osphNDGYUPYlESdHi' WHERE email = 'receptionist@gmail.com';

-- Clear login attempts
DELETE FROM login_attempts WHERE email IN ('trainer@gmail.com', 'sitter@gmail.com', 'breeder@gmail.com', 'groomer@gmail.com', 'receptionist@gmail.com');

-- Verify
SELECT email, first_name, password FROM users WHERE email IN ('trainer@gmail.com', 'sitter@gmail.com', 'breeder@gmail.com', 'groomer@gmail.com', 'receptionist@gmail.com');
