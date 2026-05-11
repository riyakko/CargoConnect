-- CargoConnect Admin Account
-- Run this in phpMyAdmin → Database: 4746260_cargoconnecct
-- Email: admin@cargoconnect.com | Password: Admin@1234

INSERT IGNORE INTO users (first_name, last_name, email, password, role, contact_number, status)
VALUES ('Admin', 'CargoConnect', 'admin@cargoconnect.com', '$2y$10$Tvnt3urblH2yGodfkxHPseuJxEl80wzkcfDFiyTf1bXyXTm8hQGoO', 'admin', '', 'active');

-- If admin already exists, update the password
UPDATE users SET password='$2y$10$Tvnt3urblH2yGodfkxHPseuJxEl80wzkcfDFiyTf1bXyXTm8hQGoO', role='admin', status='active'
WHERE email='admin@cargoconnect.com';
