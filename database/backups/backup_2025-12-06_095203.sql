-- PETVET Database Backup
-- Date: 2025-12-06 09:52:03
-- Database: btfrleeonbksuwewbmxg
-- Generated using PHP

SET FOREIGN_KEY_CHECKS=0;

-- Table: appointments
DROP TABLE IF EXISTS `appointments`;
CREATE TABLE `appointments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pet_id` int NOT NULL,
  `pet_owner_id` int NOT NULL,
  `clinic_id` int NOT NULL,
  `vet_id` int DEFAULT NULL,
  `appointment_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `symptoms` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `duration_minutes` int DEFAULT '20',
  `status` enum('pending','approved','declined','completed','cancelled','no_show') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `decline_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `approved_by` int DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_appointment_pet` (`pet_id`),
  KEY `fk_appointment_approver` (`approved_by`),
  KEY `idx_appointment_date` (`appointment_date`),
  KEY `idx_appointment_status` (`status`),
  KEY `idx_clinic_date` (`clinic_id`,`appointment_date`),
  KEY `idx_vet_date` (`vet_id`,`appointment_date`),
  KEY `idx_owner` (`pet_owner_id`),
  CONSTRAINT `fk_appointment_approver` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_appointment_clinic` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_appointment_owner` FOREIGN KEY (`pet_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_appointment_pet` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_appointment_vet` FOREIGN KEY (`vet_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `appointments` (`id`, `pet_id`, `pet_owner_id`, `clinic_id`, `vet_id`, `appointment_type`, `symptoms`, `appointment_date`, `appointment_time`, `duration_minutes`, `status`, `decline_reason`, `created_at`, `updated_at`, `approved_by`, `approved_at`) VALUES
('1', '5', '2', '1', '3', 'routine', 'Test appointment - manual creation', '2025-11-27', '10:00:00', '20', 'declined', '', '2025-11-26 06:40:42', '2025-11-26 06:41:09', NULL, NULL),
('2', '5', '2', '1', '3', 'routine', 'Test appointment - manual creation', '2025-11-27', '10:00:00', '20', 'declined', '', '2025-11-26 06:41:37', '2025-11-26 07:27:09', NULL, NULL),
('3', '6', '2', '1', NULL, 'vaccination', 'adadawdawdawda', '2025-11-27', '17:19:00', '20', 'declined', '', '2025-11-26 06:43:29', '2025-11-26 07:27:02', NULL, NULL),
('4', '6', '2', '1', NULL, 'dental', 'dadaddadad', '2025-11-27', '19:32:00', '20', 'declined', '', '2025-11-26 07:57:13', '2025-11-26 13:23:50', NULL, NULL),
('5', '6', '2', '1', NULL, 'dental', 'dentalkdadoakodka', '2025-11-27', '18:56:00', '20', 'declined', '', '2025-11-26 13:26:19', '2025-11-26 13:30:04', NULL, NULL),
('6', '6', '2', '1', NULL, 'routine', 'jnadwajkdkldkjk', '2025-11-27', '23:03:00', '20', 'declined', '', '2025-11-26 13:30:49', '2025-11-26 13:37:11', NULL, NULL),
('7', '6', '2', '1', NULL, 'dental', 'dawdawdawd', '2025-11-27', '10:11:00', '20', 'declined', '', '2025-11-26 13:38:36', '2025-11-26 13:41:16', NULL, NULL),
('8', '6', '2', '1', NULL, 'vaccination', 'dadawdad', '2025-11-27', '22:14:00', '20', 'declined', '', '2025-11-26 13:42:07', '2025-11-26 13:45:21', NULL, NULL),
('9', '6', '2', '1', NULL, 'routine', 'dawdadawd', '2025-11-27', '19:21:00', '20', 'cancelled', NULL, '2025-11-26 13:47:12', '2025-11-26 16:10:25', '17', '2025-11-26 13:48:21'),
('10', '6', '2', '1', NULL, 'routine', 'dadadfrgr', '2025-11-27', '23:32:00', '20', 'cancelled', NULL, '2025-11-26 13:58:35', '2025-11-26 16:10:11', '17', '2025-11-26 13:59:30'),
('11', '6', '2', '1', NULL, 'vaccination', 'dawdamdawudda', '2025-11-27', '13:01:00', '20', 'cancelled', NULL, '2025-11-26 16:32:01', '2025-11-26 16:33:02', '17', '2025-11-26 16:32:42'),
('12', '6', '2', '1', NULL, 'illness', 'Not eating lately', '2025-11-29', '12:03:00', '20', 'cancelled', NULL, '2025-11-28 14:31:07', '2025-11-28 15:12:11', '17', '2025-11-28 14:49:10'),
('14', '6', '2', '1', '18', 'routine', 'caugh', '2025-12-01', '10:15:00', '20', 'cancelled', NULL, '2025-11-30 07:47:34', '2025-11-30 12:20:22', '17', '2025-11-30 07:51:23'),
('15', '26', '2', '1', '19', 'routine', 'routine', '2025-12-01', '20:15:00', '20', 'cancelled', NULL, '2025-11-30 14:43:16', '2025-11-30 15:18:57', '17', '2025-11-30 14:43:52'),
('16', '5', '2', '1', '19', 'vaccination', 'fgfgfgf', '2025-12-03', '13:00:00', '20', 'approved', NULL, '2025-11-30 15:22:22', '2025-11-30 15:24:08', '17', '2025-11-30 15:24:08'),
('17', '26', '2', '1', '20', 'routine', 'adadd', '2025-12-03', '13:00:00', '20', 'cancelled', NULL, '2025-12-01 08:31:59', '2025-12-03 14:49:23', '17', '2025-12-01 08:32:08'),
('18', '6', '2', '1', '18', 'illness', 'kanne na', '2025-12-03', '13:00:00', '20', 'approved', NULL, '2025-12-01 09:04:21', '2025-12-01 09:05:02', '17', '2025-12-01 09:05:02'),
('19', '26', '2', '1', '19', 'routine', '', '2025-12-04', '15:00:00', '20', 'cancelled', NULL, '2025-12-04 08:15:30', '2025-12-04 11:06:39', '17', '2025-12-04 08:15:58'),
('20', '6', '2', '1', '19', 'routine', '', '2025-12-04', '17:00:00', '20', 'approved', NULL, '2025-12-04 09:12:54', '2025-12-04 09:13:08', '17', '2025-12-04 09:13:08'),
('21', '5', '2', '1', NULL, 'routine', '', '2025-12-04', '17:00:00', '20', 'declined', '', '2025-12-04 09:58:38', '2025-12-06 08:36:32', NULL, NULL),
('22', '26', '2', '1', '18', 'vaccination', '', '2025-12-07', '10:00:00', '20', 'cancelled', NULL, '2025-12-04 11:11:07', '2025-12-04 11:15:20', '17', '2025-12-04 11:13:19'),
('23', '26', '2', '1', '18', 'dental', '', '2025-12-04', '18:00:00', '20', 'approved', NULL, '2025-12-04 11:18:04', '2025-12-04 11:19:26', '17', '2025-12-04 11:19:26'),
('24', '26', '2', '1', '20', 'routine', '', '2025-12-08', '12:30:00', '20', 'approved', NULL, '2025-12-06 08:37:03', '2025-12-06 08:46:18', '17', '2025-12-06 08:46:18'),
('25', '6', '2', '1', '19', 'dental', '', '2025-12-17', '11:00:00', '20', 'pending', NULL, '2025-12-06 08:42:26', '2025-12-06 08:42:26', NULL, NULL),
('26', '5', '2', '1', NULL, 'vaccination', '', '2025-12-17', '11:00:00', '20', 'pending', NULL, '2025-12-06 08:44:43', '2025-12-06 08:44:43', NULL, NULL);

-- Table: audit_logs
DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'login, logout, role_switch, profile_update, etc.',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'Additional context about the action',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_action` (`action`),
  KEY `idx_created` (`created_at`),
  CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `audit_logs_chk_1` CHECK (json_valid(`details`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Audit trail for security monitoring';

-- Table: clinic_blocked_days
DROP TABLE IF EXISTS `clinic_blocked_days`;
CREATE TABLE `clinic_blocked_days` (
  `id` int NOT NULL AUTO_INCREMENT,
  `clinic_id` int NOT NULL,
  `blocked_date` date NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_clinic_date` (`clinic_id`,`blocked_date`),
  CONSTRAINT `clinic_blocked_days_ibfk_1` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `clinic_blocked_days` (`id`, `clinic_id`, `blocked_date`, `reason`, `created_at`, `updated_at`) VALUES
('1', '1', '2025-12-25', 'wedding', '2025-12-04 08:46:46', '2025-12-04 08:46:46');

-- Table: clinic_manager_profiles
DROP TABLE IF EXISTS `clinic_manager_profiles`;
CREATE TABLE `clinic_manager_profiles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `clinic_id` int NOT NULL,
  `position` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Manager',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_clinic` (`clinic_id`),
  CONSTRAINT `clinic_manager_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `clinic_manager_profiles_ibfk_2` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Clinic manager profiles';

INSERT INTO `clinic_manager_profiles` (`id`, `user_id`, `clinic_id`, `position`, `created_at`, `updated_at`) VALUES
('1', '9', '1', 'Clinic Manager', '2025-10-21 08:17:00', '2025-10-21 08:17:00'),
('2', '14', '2', 'Manager', '2025-10-22 08:52:57', '2025-10-22 08:52:57'),
('3', '15', '3', 'Manager', '2025-10-22 10:13:19', '2025-10-22 10:13:19');

-- Table: clinic_preferences
DROP TABLE IF EXISTS `clinic_preferences`;
CREATE TABLE `clinic_preferences` (
  `id` int NOT NULL AUTO_INCREMENT,
  `clinic_id` int NOT NULL,
  `email_notifications` tinyint(1) DEFAULT '1',
  `slot_duration_minutes` int DEFAULT '20',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_clinic` (`clinic_id`),
  CONSTRAINT `clinic_preferences_ibfk_1` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `clinic_preferences` (`id`, `clinic_id`, `email_notifications`, `slot_duration_minutes`, `created_at`, `updated_at`) VALUES
('1', '1', '1', '20', '2025-12-03 14:40:04', '2025-12-03 14:40:04'),
('2', '2', '1', '20', '2025-12-03 14:40:04', '2025-12-03 14:40:04'),
('3', '3', '1', '20', '2025-12-03 14:40:05', '2025-12-03 14:40:05');

-- Table: clinic_staff
DROP TABLE IF EXISTS `clinic_staff`;
CREATE TABLE `clinic_staff` (
  `id` int NOT NULL AUTO_INCREMENT,
  `clinic_id` int NOT NULL COMMENT 'Reference to the clinic this staff belongs to',
  `user_id` int DEFAULT NULL COMMENT 'Link to users table if staff has system account (e.g., receptionist)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Veterinary Assistant, Front Desk, Support Staff, etc.',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('Active','Inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'Active',
  `next_shift` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Next scheduled shift information',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_clinic_id` (`clinic_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_role` (`role`),
  KEY `idx_status` (`status`),
  KEY `idx_email` (`email`),
  CONSTRAINT `clinic_staff_ibfk_1` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  CONSTRAINT `clinic_staff_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Clinic staff members management';

INSERT INTO `clinic_staff` (`id`, `clinic_id`, `user_id`, `name`, `role`, `email`, `phone`, `status`, `next_shift`, `created_at`, `updated_at`) VALUES
('1', '1', NULL, 'Anushka Perera', 'Veterinary Assistant', 'anushka.assist@petvet.lk', '+94 71 234 5678', 'Active', NULL, '2025-10-22 10:20:35', '2025-10-22 10:20:35'),
('3', '1', NULL, 'Kavinda Fernando', 'Veterinary Assistant', 'kavinda.assist@petvet.lk', '+94 77 555 1234', 'Active', NULL, '2025-10-22 10:20:35', '2025-10-22 10:20:35'),
('6', '1', NULL, 'MAlan', 'Veterinary Assistant', 'tharindu.front@petvet.lk', '0771234567', 'Inactive', NULL, '2025-10-22 10:20:35', '2025-10-23 05:19:33'),
('20', '1', '17', 'Jane Smith', 'Receptionist', 'receptionist@petvet.com', '0771234567', 'Active', NULL, '2025-11-26 06:25:52', '2025-11-26 06:25:52'),
('21', '1', '3', 'Sarah Johnson', 'vet', 'sarah.johnson@petvet.com', '0771234567', 'Active', NULL, '2025-11-29 10:41:41', '2025-11-29 10:41:41'),
('22', '2', '13', 'Dihindu Hesara', 'vet', 'dihindu.hesara@petvet.com', '0779876543', 'Active', NULL, '2025-11-29 10:41:51', '2025-11-29 10:41:51'),
('24', '1', '18', 'Michael Chen', 'vet', 'michael.chen@petvet.com', '0771234568', 'Active', NULL, '2025-11-29 10:51:44', '2025-11-29 10:51:44'),
('25', '1', '19', 'Emily Rodriguez', 'vet', 'emily.rodriguez@petvet.com', '0771234569', 'Active', NULL, '2025-11-29 10:51:44', '2025-11-29 10:51:44'),
('26', '1', '20', 'James Wilson', 'vet', 'james.wilson@petvet.com', '0771234570', 'Active', NULL, '2025-11-29 10:51:44', '2025-11-29 10:51:44'),
('27', '2', '21', 'Priya Perera', 'vet', 'priya.perera@petvet.com', '0771234571', 'Active', NULL, '2025-11-29 10:51:52', '2025-11-29 10:51:52'),
('28', '2', '22', 'Nuwan Silva', 'vet', 'nuwan.silva@petvet.com', '0771234572', 'Active', NULL, '2025-11-29 10:51:52', '2025-11-29 10:51:52'),
('29', '2', '23', 'Anjali Fernando', 'vet', 'anjali.fernando@petvet.com', '0771234573', 'Active', NULL, '2025-11-29 10:51:52', '2025-11-29 10:51:52'),
('30', '3', '24', 'Rajesh Kumar', 'vet', 'rajesh.kumar@petvet.com', '0771234574', 'Active', NULL, '2025-11-29 10:55:44', '2025-11-29 10:55:44'),
('31', '3', '25', 'Lisa Thompson', 'vet', 'lisa.thompson@petvet.com', '0771234575', 'Active', NULL, '2025-11-29 10:55:44', '2025-11-29 10:55:44'),
('32', '3', '26', 'David Lee', 'vet', 'david.lee@petvet.com', '0771234576', 'Active', NULL, '2025-11-29 10:55:44', '2025-11-29 10:55:44'),
('33', '1', '9', 'Mike Manager', 'Clinic Manager', 'manager@gmail.com', '0771234567', 'Active', NULL, '2025-11-30 15:06:04', '2025-11-30 15:06:04');

-- Table: clinic_weekly_schedule
DROP TABLE IF EXISTS `clinic_weekly_schedule`;
CREATE TABLE `clinic_weekly_schedule` (
  `id` int NOT NULL AUTO_INCREMENT,
  `clinic_id` int NOT NULL,
  `day_of_week` enum('monday','tuesday','wednesday','thursday','friday','saturday','sunday') NOT NULL,
  `is_enabled` tinyint(1) DEFAULT '1',
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_clinic_day` (`clinic_id`,`day_of_week`),
  CONSTRAINT `clinic_weekly_schedule_ibfk_1` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `clinic_weekly_schedule` (`id`, `clinic_id`, `day_of_week`, `is_enabled`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES
('1', '1', 'monday', '1', '09:00:00', '17:00:00', '2025-12-03 14:40:05', '2025-12-06 07:48:24'),
('2', '1', 'tuesday', '1', '09:00:00', '17:00:00', '2025-12-03 14:40:05', '2025-12-06 07:48:24'),
('3', '1', 'wednesday', '1', '09:00:00', '17:00:00', '2025-12-03 14:40:05', '2025-12-06 07:48:24'),
('4', '1', 'thursday', '1', '08:00:00', '22:00:00', '2025-12-03 14:40:05', '2025-12-06 07:48:25'),
('5', '1', 'friday', '1', '09:00:00', '17:00:00', '2025-12-03 14:40:05', '2025-12-06 07:48:25'),
('6', '1', 'saturday', '0', '10:00:00', '14:00:00', '2025-12-03 14:40:05', '2025-12-06 07:48:25'),
('7', '1', 'sunday', '0', '09:00:00', '13:00:00', '2025-12-03 14:40:06', '2025-12-06 07:48:25'),
('8', '2', 'monday', '1', '09:00:00', '17:00:00', '2025-12-03 14:40:06', '2025-12-03 14:40:06'),
('9', '2', 'tuesday', '1', '09:00:00', '17:00:00', '2025-12-03 14:40:06', '2025-12-03 14:40:06'),
('10', '2', 'wednesday', '1', '09:00:00', '17:00:00', '2025-12-03 14:40:06', '2025-12-03 14:40:06'),
('11', '2', 'thursday', '1', '09:00:00', '17:00:00', '2025-12-03 14:40:06', '2025-12-03 14:40:06'),
('12', '2', 'friday', '1', '09:00:00', '17:00:00', '2025-12-03 14:40:06', '2025-12-03 14:40:06'),
('13', '2', 'saturday', '1', '10:00:00', '14:00:00', '2025-12-03 14:40:06', '2025-12-03 14:40:06'),
('14', '2', 'sunday', '0', '09:00:00', '13:00:00', '2025-12-03 14:40:07', '2025-12-03 14:40:07'),
('15', '3', 'monday', '1', '09:00:00', '17:00:00', '2025-12-03 14:40:07', '2025-12-03 14:40:07'),
('16', '3', 'tuesday', '1', '09:00:00', '17:00:00', '2025-12-03 14:40:07', '2025-12-03 14:40:07'),
('17', '3', 'wednesday', '1', '09:00:00', '17:00:00', '2025-12-03 14:40:07', '2025-12-03 14:40:07'),
('18', '3', 'thursday', '1', '09:00:00', '17:00:00', '2025-12-03 14:40:07', '2025-12-03 14:40:07'),
('19', '3', 'friday', '1', '09:00:00', '17:00:00', '2025-12-03 14:40:07', '2025-12-03 14:40:07'),
('20', '3', 'saturday', '1', '10:00:00', '14:00:00', '2025-12-03 14:40:08', '2025-12-03 14:40:08'),
('21', '3', 'sunday', '0', '09:00:00', '13:00:00', '2025-12-03 14:40:08', '2025-12-03 14:40:08');

-- Table: clinics
DROP TABLE IF EXISTS `clinics`;
CREATE TABLE `clinics` (
  `id` int NOT NULL AUTO_INCREMENT,
  `clinic_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `clinic_description` text COLLATE utf8mb4_unicode_ci,
  `clinic_logo` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `clinic_cover` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `clinic_address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `map_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `clinic_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `clinic_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `operating_hours` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'Store as {"monday": "9:00-17:00", ...}',
  `license_document` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verification_status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_district` (`district`),
  KEY `idx_status` (`verification_status`),
  KEY `idx_active` (`is_active`),
  CONSTRAINT `clinics_chk_1` CHECK (json_valid(`operating_hours`))
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Veterinary clinics';

INSERT INTO `clinics` (`id`, `clinic_name`, `clinic_description`, `clinic_logo`, `clinic_cover`, `clinic_address`, `map_location`, `district`, `city`, `clinic_phone`, `clinic_email`, `operating_hours`, `license_document`, `verification_status`, `is_active`, `created_at`, `updated_at`) VALUES
('1', 'Happy Paws Veterinaryy Clinic', 'Trusted pet healthcare and wellness. Experienced vets, modern facilities, and friendly service.', 'https://static.vecteezy.com/system/resources/previews/005/601/780/non_2x/veterinary-clinic-logo-vector.jpg', 'https://img.freepik.com/free-vector/veterinary-clinic-social-media-cover-template_23-2149716789.jpg', '123 Main Street, Colombo', '6.9271, 79.8612', 'Colombo', 'Colombo 07', '0112345678', 'info@happypaws.lk', NULL, NULL, 'approved', '1', '2025-10-21 06:59:16', '2025-12-03 14:47:02'),
('2', 'Peter PETVET', 'Professional veterinary care for your beloved pets.', 'https://static.vecteezy.com/system/resources/previews/005/601/780/non_2x/veterinary-clinic-logo-vector.jpg', 'https://img.freepik.com/free-vector/veterinary-clinic-social-media-cover-template_23-2149716789.jpg', '145/2/1', NULL, 'Anuradhapura', NULL, '0775983002', 'allinone@gmail.com', NULL, NULL, 'approved', '1', '2025-10-22 08:52:57', '2025-12-03 14:44:49'),
('3', 'Pet Bros', NULL, NULL, NULL, '145/2/1', NULL, 'Badulla', NULL, '0775983002', 'gklnkler@gmail.com', NULL, NULL, 'approved', '1', '2025-10-22 10:13:19', '2025-10-22 10:13:19');

-- Table: login_attempts
DROP TABLE IF EXISTS `login_attempts`;
CREATE TABLE `login_attempts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `success` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_email_ip` (`email`,`ip_address`),
  KEY `idx_attempted` (`attempted_at`)
) ENGINE=InnoDB AUTO_INCREMENT=235 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Track login attempts for security';

INSERT INTO `login_attempts` (`id`, `email`, `ip_address`, `attempted_at`, `success`) VALUES
('230', 'petowner@gmail.com', '::1', '2025-12-05 08:05:48', '1'),
('231', 'vet@gmail.com', '::1', '2025-12-05 08:08:29', '1'),
('232', 'manager@gmail.com', '::1', '2025-12-06 07:40:45', '0'),
('233', 'manager@gmail.com', '::1', '2025-12-06 07:40:52', '0'),
('234', 'manager@gmail.com', '::1', '2025-12-06 07:41:02', '1');

-- Table: password_resets
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `reset_token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `used` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reset_token` (`reset_token`),
  KEY `idx_token` (`reset_token`),
  KEY `idx_user` (`user_id`),
  KEY `idx_expires` (`expires_at`),
  CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Password reset tokens';

-- Table: pet_owner_profiles
DROP TABLE IF EXISTS `pet_owner_profiles`;
CREATE TABLE `pet_owner_profiles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `preferred_vet_id` int DEFAULT NULL,
  `emergency_contact_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notification_preferences` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'Email, SMS, push notification settings',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `idx_user` (`user_id`),
  CONSTRAINT `pet_owner_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pet_owner_profiles_chk_1` CHECK (json_valid(`notification_preferences`))
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Pet owner specific profile data';

INSERT INTO `pet_owner_profiles` (`id`, `user_id`, `preferred_vet_id`, `emergency_contact_name`, `emergency_contact_phone`, `notification_preferences`, `created_at`, `updated_at`) VALUES
('1', '10', NULL, NULL, NULL, NULL, '2025-10-21 09:18:56', '2025-10-21 09:18:56'),
('2', '11', NULL, NULL, NULL, NULL, '2025-10-21 09:53:06', '2025-10-21 09:53:06'),
('3', '12', NULL, NULL, NULL, NULL, '2025-10-21 10:02:35', '2025-10-21 10:02:35'),
('4', '16', NULL, NULL, NULL, NULL, '2025-10-22 16:09:16', '2025-10-22 16:09:16');

-- Table: pets
DROP TABLE IF EXISTS `pets`;
CREATE TABLE `pets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `species` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `breed` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sex` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `weight` decimal(10,2) DEFAULT NULL,
  `color` text COLLATE utf8mb4_unicode_ci,
  `allergies` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `photo_url` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_is_active` (`is_active`),
  CONSTRAINT `pets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `pets` (`id`, `user_id`, `name`, `species`, `breed`, `sex`, `date_of_birth`, `weight`, `color`, `allergies`, `notes`, `photo_url`, `is_active`, `created_at`, `updated_at`) VALUES
('5', '2', 'Tuffey', 'Dog', 'Golden Retriever', 'Male', '2021-01-22', '30.00', 'Gold color', 'None', 'Friendly & Playful', '/PETVET/public/images/pets/pet_2_1761138857_68f8d8a9b9cfb.jpg', '1', '2025-10-22 13:14:17', '2025-10-22 13:14:17'),
('6', '2', 'Kitty', 'Cat', 'American Curl', 'Female', '2023-06-27', '8.00', 'Black, Gray & White', 'Allergic to Prawns', 'Lazy & Eats a lot', '/PETVET/public/images/pets/pet_2_1761139151_68f8d9cfeefbe.jpg', '1', '2025-10-22 13:19:11', '2025-10-22 13:19:11'),
('12', '11', 'Rocky', 'Dog', 'Rotteriller', 'Female', '2021-02-17', '25.00', 'Black. Brown and Orange', 'Allergy to Humans', 'Very Aggressive against people', '/PETVET/public/images/pets/pet_11_1761155352_68f91918afef0.jpg', '1', '2025-10-22 17:49:12', '2025-10-22 17:49:12'),
('13', '11', 'Buddy', 'Dog', 'Poodle', 'Male', '2019-02-13', '18.00', 'White', 'None', 'Playful, Friendly', '/PETVET/public/images/pets/pet_11_1761155595_68f91a0b97a67.jpg', '1', '2025-10-22 17:53:15', '2025-10-22 17:53:15'),
('26', '2', 'RoMky', 'Dog', 'WildTation', 'Male', '2025-02-04', '5.00', 'White, Brown', 'Cats', 'Playfull, Sleeps', '/PETVET/public/images/pets/pet_2_1764505597_692c37fdc08c9.jpg', '1', '2025-11-30 12:26:39', '2025-11-30 12:29:20');

-- Table: product_images
DROP TABLE IF EXISTS `product_images`;
CREATE TABLE `product_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `image_url` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_order` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_product_id` (`product_id`),
  KEY `idx_display_order` (`display_order`),
  CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `product_images` (`id`, `product_id`, `image_url`, `display_order`, `created_at`) VALUES
('1', '1', '/PETVET/views/shared/images/fproduct1.png', '0', '2025-10-21 13:42:05'),
('2', '2', '/PETVET/views/shared/images/fproduct2.png', '0', '2025-10-21 13:42:05'),
('3', '3', '/PETVET/views/shared/images/fproduct3.png', '0', '2025-10-21 13:42:05'),
('4', '4', '/PETVET/views/shared/images/fproduct4.png', '0', '2025-10-21 13:42:05');

-- Table: products
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL,
  `category` enum('food','toys','litter','grooming','accessories','medicine') COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stock` int DEFAULT '0',
  `seller` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'PetVet Official Store',
  `sold` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `products` (`id`, `name`, `description`, `price`, `category`, `image_url`, `stock`, `seller`, `sold`, `is_active`, `created_at`, `updated_at`) VALUES
('1', 'Denta Fun Veggie Jaw Bone', 'A healthy, delicious treat for your dog. Made from natural ingredients to support dental health while satisfying chewing needs. Composition sweet potato meal, pea starch, vegetable by-products, minerals, yeast, cellulose, oils and fats, rosemary | gluten-free formula | vegetarian | no added sugar', '500.00', 'food', '/PETVET/views/shared/images/fproduct1.png', '25', 'PetVet Official Store', '340', '1', '2025-10-21 12:48:40', '2025-10-21 12:48:40'),
('2', 'Trixie Litter Scoop', 'High-quality litter scoop made from durable materials. Perfect for easy and hygienic litter box maintenance. Features comfortable grip handle and efficient scooping design.', '900.00', 'litter', '/PETVET/views/shared/images/fproduct2.png', '10', 'PetVet Store', '185', '1', '2025-10-21 12:48:40', '2025-10-22 04:38:14'),
('3', 'Dog Toy Tug Rope', 'Interactive rope toy perfect for playing tug-of-war with your dog. Made from durable cotton fibers that help clean teeth during play. Great for bonding and exercise.', '2100.00', 'toys', '/PETVET/views/shared/images/fproduct3.png', '8', 'PlayTime Pets', '95', '1', '2025-10-21 12:48:40', '2025-10-21 12:48:40'),
('4', 'Trixie Aloe Vera Shampoo', 'Gentle pet shampoo enriched with Aloe Vera for sensitive skin. Cleanses thoroughly while moisturizing and soothing your pet\'s coat. Suitable for regular use.', '1900.00', 'grooming', '/PETVET/views/shared/images/fproduct4.png', '10', 'PetVet Store', '220', '1', '2025-10-21 12:48:40', '2025-10-23 04:19:04');

-- Table: role_verification_documents
DROP TABLE IF EXISTS `role_verification_documents`;
CREATE TABLE `role_verification_documents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_role_id` int NOT NULL,
  `document_type` enum('license','certificate','id','business_permit','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Relative path from uploads directory',
  `file_size` int NOT NULL COMMENT 'File size in bytes',
  `mime_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_role` (`user_role_id`),
  CONSTRAINT `role_verification_documents_ibfk_1` FOREIGN KEY (`user_role_id`) REFERENCES `user_roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Verification documents for service providers';

-- Table: roles
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'System identifier (pet_owner, vet, etc.)',
  `role_display_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Human-readable name',
  `description` text COLLATE utf8mb4_unicode_ci,
  `requires_verification` tinyint(1) DEFAULT '0' COMMENT 'Needs admin approval',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_name` (`role_name`),
  KEY `idx_role_name` (`role_name`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Available system roles';

INSERT INTO `roles` (`id`, `role_name`, `role_display_name`, `description`, `requires_verification`, `created_at`) VALUES
('1', 'pet_owner', 'Pet Owner', 'Regular pet owner who can book appointments and manage pets', '0', '2025-10-21 06:59:16'),
('2', 'vet', 'Veterinarian', 'Licensed veterinarian providing medical services', '1', '2025-10-21 06:59:16'),
('3', 'clinic_manager', 'Clinic Manager', 'Manages clinic operations and staff', '1', '2025-10-21 06:59:16'),
('4', 'admin', 'Administrator', 'System administrator with full access', '0', '2025-10-21 06:59:16'),
('5', 'receptionist', 'Receptionist', 'Front desk staff managing appointments', '0', '2025-10-21 06:59:16'),
('6', 'trainer', 'Pet Trainer', 'Professional pet trainer offering training services', '1', '2025-10-21 06:59:16'),
('7', 'sitter', 'Pet Sitter', 'Professional pet sitter offering sitting services', '1', '2025-10-21 06:59:16'),
('8', 'breeder', 'Pet Breeder', 'Professional breeder managing breeding operations', '1', '2025-10-21 06:59:16'),
('9', 'groomer', 'Pet Groomer', 'Professional groomer offering grooming services', '1', '2025-10-21 06:59:16');

-- Table: sell_pet_listing_badges
DROP TABLE IF EXISTS `sell_pet_listing_badges`;
CREATE TABLE `sell_pet_listing_badges` (
  `id` int NOT NULL AUTO_INCREMENT,
  `listing_id` int NOT NULL,
  `badge` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_listing_id` (`listing_id`),
  CONSTRAINT `sell_pet_listing_badges_ibfk_1` FOREIGN KEY (`listing_id`) REFERENCES `sell_pet_listings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: sell_pet_listing_images
DROP TABLE IF EXISTS `sell_pet_listing_images`;
CREATE TABLE `sell_pet_listing_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `listing_id` int NOT NULL,
  `image_url` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_order` tinyint DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_listing_id` (`listing_id`),
  CONSTRAINT `sell_pet_listing_images_ibfk_1` FOREIGN KEY (`listing_id`) REFERENCES `sell_pet_listings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `sell_pet_listing_images` (`id`, `listing_id`, `image_url`, `display_order`, `created_at`) VALUES
('8', '5', '/PETVET/public/images/uploads/pet-listings/pet_5_1761139334_0.jpg', '0', '2025-10-22 13:22:14'),
('9', '6', '/PETVET/public/images/uploads/pet-listings/pet_6_1761140156_0.jpg', '0', '2025-10-22 13:35:56'),
('10', '6', '/PETVET/public/images/uploads/pet-listings/pet_6_1761140182_0.jpg', '1', '2025-10-22 13:36:22'),
('11', '7', '/PETVET/public/images/uploads/pet-listings/pet_7_1761140618_0.jpg', '0', '2025-10-22 13:43:38');

-- Table: sell_pet_listings
DROP TABLE IF EXISTS `sell_pet_listings`;
CREATE TABLE `sell_pet_listings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `species` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `breed` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `age` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` enum('Male','Female') COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone2` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','approved','rejected','sold') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_species` (`species`),
  CONSTRAINT `sell_pet_listings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `sell_pet_listings` (`id`, `user_id`, `name`, `species`, `breed`, `age`, `gender`, `price`, `location`, `description`, `phone`, `phone2`, `email`, `status`, `created_at`, `updated_at`) VALUES
('5', '2', 'Shadow', 'Dog', 'Shiba Inu', '2', 'Male', '60000.00', 'Kadawatha', 'Very friendly', '0715645789', '', '', 'approved', '2025-10-22 13:22:14', '2025-10-22 13:23:11'),
('6', '2', 'Garfield', 'Cat', 'Orange Tabby', '4', 'Male', '40000.00', 'Maharagama', 'Eats a lot. Very Aggressive.', '0725649798', '01124656789', 'test@gmail.com', 'approved', '2025-10-22 13:35:56', '2025-10-22 13:38:15'),
('7', '2', 'Sparrow', 'Bird', 'Thick billed', '1', 'Female', '25000.00', 'Kelaniya', 'Friendly & Talkative', '0789634524', '', '', 'approved', '2025-10-22 13:43:38', '2025-10-22 13:44:23');

-- Table: service_provider_profiles
DROP TABLE IF EXISTS `service_provider_profiles`;
CREATE TABLE `service_provider_profiles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `role_type` enum('trainer','sitter','breeder','groomer') COLLATE utf8mb4_unicode_ci NOT NULL,
  `business_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_area` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'City/District where services offered',
  `experience_years` int DEFAULT '0',
  `certifications` text COLLATE utf8mb4_unicode_ci COMMENT 'Comma-separated or JSON array',
  `specializations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'Array of specialties',
  `price_range_min` decimal(10,2) DEFAULT NULL,
  `price_range_max` decimal(10,2) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT '0.00',
  `total_reviews` int DEFAULT '0',
  `bio` text COLLATE utf8mb4_unicode_ci,
  `available` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_role_type` (`user_id`,`role_type`),
  KEY `idx_role_type` (`role_type`),
  KEY `idx_area` (`service_area`),
  KEY `idx_user` (`user_id`),
  CONSTRAINT `service_provider_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `service_provider_profiles_chk_1` CHECK (json_valid(`specializations`))
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Profiles for trainers, sitters, breeders, groomers';

INSERT INTO `service_provider_profiles` (`id`, `user_id`, `role_type`, `business_name`, `service_area`, `experience_years`, `certifications`, `specializations`, `price_range_min`, `price_range_max`, `rating`, `total_reviews`, `bio`, `available`, `created_at`, `updated_at`) VALUES
('1', '4', 'trainer', 'Pro Pet Training', 'Colombo, Kandy', '8', 'Certified Professional Dog Trainer (CPDT)', NULL, NULL, NULL, '4.80', '45', 'Professional dog training services for all breeds', '1', '2025-10-21 07:42:28', '2025-10-21 07:42:28'),
('2', '5', 'sitter', 'Caring Pet Sitters', 'Kandy, Peradeniya', '5', 'Pet First Aid Certified', NULL, NULL, NULL, '4.90', '67', 'Trusted pet sitting and boarding services', '1', '2025-10-21 07:42:28', '2025-10-21 07:42:28'),
('3', '6', 'breeder', 'Premium Breeders', 'Galle, Matara', '12', 'Registered Breeder - Kennel Club', NULL, NULL, NULL, '4.70', '23', 'Ethical breeding of purebred dogs and cats', '1', '2025-10-21 07:42:28', '2025-10-21 07:42:28'),
('4', '7', 'groomer', 'Pawfect Grooming', 'Negombo, Colombo', '6', 'Certified Master Groomer', NULL, NULL, NULL, '5.00', '89', 'Professional grooming services for all pets', '1', '2025-10-21 07:42:28', '2025-10-21 07:42:28'),
('5', '10', 'trainer', 'Obey', 'Kadawatha', '10', 'adpadlaldp', NULL, NULL, NULL, '0.00', '0', 'Specialization: Obey', '1', '2025-10-21 09:18:56', '2025-10-21 09:18:56'),
('6', '10', 'sitter', NULL, '', '15', NULL, NULL, NULL, NULL, '0.00', '0', 'Home Type: house_with_yard\nPet Types: Cats, Dogs\nMax Pets: 5\nOvernight: Yes', '1', '2025-10-21 09:18:56', '2025-10-21 09:18:56'),
('7', '11', 'trainer', 'Obey', 'Kaduwela', '10', '', NULL, NULL, NULL, '0.00', '0', 'Specialization: Obey', '1', '2025-10-21 09:53:06', '2025-10-21 09:53:06'),
('8', '11', 'groomer', 'Aminda Groomers', '', '10', NULL, NULL, NULL, NULL, '0.00', '0', 'Services: nope\nPricing: 1500', '1', '2025-10-21 09:53:06', '2025-10-21 09:53:06'),
('9', '11', 'sitter', NULL, '', '10', NULL, NULL, NULL, NULL, '0.00', '0', 'Home Type: apartment\nPet Types: Cats, Dogs\nMax Pets: 1\nOvernight: No', '1', '2025-10-21 09:53:06', '2025-10-21 09:53:06'),
('10', '11', 'breeder', 'Breeding: Germen Shepherd', NULL, '10', '156a15544d', NULL, NULL, NULL, '0.00', '0', 'Breeds: Germen Shepherd\nPhilosophy: ', '1', '2025-10-21 09:53:06', '2025-10-21 09:53:06'),
('11', '12', 'groomer', 'ba', '', '1', NULL, NULL, NULL, NULL, '0.00', '0', 'Services: okkoma\nPricing: ba', '1', '2025-10-21 10:02:35', '2025-10-21 10:02:35'),
('12', '12', 'breeder', 'Breeding: okkoma', NULL, '18', '123', NULL, NULL, NULL, '0.00', '0', 'Breeds: okkoma\nPhilosophy: danne na', '1', '2025-10-21 10:02:35', '2025-10-21 10:02:35'),
('13', '16', 'trainer', 'dgdg', 'dggg', '4', 'dfddg', NULL, NULL, NULL, '0.00', '0', 'Specialization: dgdg', '1', '2025-10-22 16:09:16', '2025-10-22 16:09:16'),
('14', '16', 'groomer', 'dfddg', '', '5', NULL, NULL, NULL, NULL, '0.00', '0', 'Services: dfgdfg\nPricing: dgdg', '1', '2025-10-22 16:09:16', '2025-10-22 16:09:16'),
('15', '16', 'sitter', NULL, '', '10', NULL, NULL, NULL, NULL, '0.00', '0', 'Home Type: apartment\nPet Types: dgfgfg\nMax Pets: 1\nOvernight: Yes', '1', '2025-10-22 16:09:16', '2025-10-22 16:09:16'),
('16', '16', 'breeder', 'Breeding: dfgfhfhfh', NULL, '12', '13', NULL, NULL, NULL, '0.00', '0', 'Breeds: dfgfhfhfh\nPhilosophy: sffdf', '1', '2025-10-22 16:09:16', '2025-10-22 16:09:16');

-- Table: sessions
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `session_token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'IPv4 or IPv6',
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_activity` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_token` (`session_token`),
  KEY `idx_user_session` (`user_id`),
  KEY `idx_token` (`session_token`),
  KEY `idx_expires` (`expires_at`),
  CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Active user sessions';

-- Table: user_roles
DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE `user_roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `role_id` int NOT NULL,
  `is_primary` tinyint(1) DEFAULT '0' COMMENT 'Users default landing role',
  `is_active` tinyint(1) DEFAULT '1' COMMENT 'Can temporarily disable a role',
  `verification_status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `verification_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Admin notes on approval/rejection',
  `verified_by` int DEFAULT NULL COMMENT 'Admin user who verified',
  `verified_at` timestamp NULL DEFAULT NULL,
  `applied_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_role` (`user_id`,`role_id`),
  KEY `role_id` (`role_id`),
  KEY `verified_by` (`verified_by`),
  KEY `idx_user_role` (`user_id`,`role_id`),
  KEY `idx_verification` (`verification_status`),
  KEY `idx_user_id` (`user_id`),
  CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_roles_ibfk_3` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='User to roles mapping with verification';

INSERT INTO `user_roles` (`id`, `user_id`, `role_id`, `is_primary`, `is_active`, `verification_status`, `verification_notes`, `verified_by`, `verified_at`, `applied_at`) VALUES
('1', '1', '4', '1', '1', 'approved', NULL, NULL, '2025-10-21 06:59:16', '2025-10-21 06:59:16'),
('2', '2', '1', '1', '1', 'approved', NULL, NULL, '2025-10-21 06:59:16', '2025-10-21 06:59:16'),
('3', '3', '2', '1', '1', 'approved', NULL, NULL, '2025-10-21 06:59:16', '2025-10-21 06:59:16'),
('4', '4', '6', '1', '1', 'approved', NULL, NULL, NULL, '2025-10-21 07:42:28'),
('5', '5', '7', '1', '1', 'approved', NULL, NULL, NULL, '2025-10-21 07:42:28'),
('6', '6', '8', '1', '1', 'approved', NULL, NULL, NULL, '2025-10-21 07:42:28'),
('7', '7', '9', '1', '1', 'approved', NULL, NULL, NULL, '2025-10-21 07:42:28'),
('8', '8', '5', '1', '1', 'approved', NULL, NULL, NULL, '2025-10-21 07:42:28'),
('9', '9', '3', '1', '1', 'approved', NULL, NULL, NULL, '2025-10-21 08:17:00'),
('10', '10', '1', '1', '1', 'approved', NULL, NULL, NULL, '2025-10-21 09:18:56'),
('11', '10', '6', '0', '1', 'approved', NULL, NULL, NULL, '2025-10-21 09:18:56'),
('12', '10', '7', '0', '1', 'approved', NULL, NULL, NULL, '2025-10-21 09:18:56'),
('13', '11', '1', '1', '1', 'approved', NULL, NULL, NULL, '2025-10-21 09:53:06'),
('14', '11', '6', '0', '1', 'approved', NULL, NULL, NULL, '2025-10-21 09:53:06'),
('15', '11', '9', '0', '1', 'approved', NULL, NULL, NULL, '2025-10-21 09:53:06'),
('16', '11', '7', '0', '1', 'approved', NULL, NULL, NULL, '2025-10-21 09:53:06'),
('17', '11', '8', '0', '1', 'approved', NULL, NULL, NULL, '2025-10-21 09:53:06'),
('18', '12', '1', '1', '1', 'approved', NULL, NULL, NULL, '2025-10-21 10:02:35'),
('19', '12', '9', '0', '1', 'approved', NULL, NULL, NULL, '2025-10-21 10:02:35'),
('20', '12', '8', '0', '1', 'approved', NULL, NULL, NULL, '2025-10-21 10:02:35'),
('21', '13', '2', '1', '1', 'approved', NULL, NULL, NULL, '2025-10-22 08:17:28'),
('22', '14', '3', '1', '1', 'approved', NULL, NULL, NULL, '2025-10-22 08:52:57'),
('23', '15', '3', '1', '1', 'approved', NULL, NULL, NULL, '2025-10-22 10:13:19'),
('24', '16', '1', '1', '1', 'approved', NULL, NULL, NULL, '2025-10-22 16:09:16'),
('25', '16', '6', '0', '1', 'approved', NULL, NULL, NULL, '2025-10-22 16:09:16'),
('26', '16', '9', '0', '1', 'approved', NULL, NULL, NULL, '2025-10-22 16:09:16'),
('27', '16', '7', '0', '1', 'approved', NULL, NULL, NULL, '2025-10-22 16:09:16'),
('28', '16', '8', '0', '1', 'approved', NULL, NULL, NULL, '2025-10-22 16:09:16'),
('29', '17', '5', '0', '1', 'approved', NULL, NULL, NULL, '2025-11-26 06:25:45'),
('30', '18', '2', '0', '1', 'pending', NULL, NULL, NULL, '2025-11-29 10:51:33'),
('31', '19', '2', '0', '1', 'pending', NULL, NULL, NULL, '2025-11-29 10:51:33'),
('32', '20', '2', '0', '1', 'pending', NULL, NULL, NULL, '2025-11-29 10:51:33'),
('33', '21', '2', '0', '1', 'pending', NULL, NULL, NULL, '2025-11-29 10:51:33'),
('34', '22', '2', '0', '1', 'pending', NULL, NULL, NULL, '2025-11-29 10:51:33'),
('35', '23', '2', '0', '1', 'pending', NULL, NULL, NULL, '2025-11-29 10:51:33'),
('36', '24', '2', '0', '1', 'pending', NULL, NULL, NULL, '2025-11-29 10:51:33'),
('37', '25', '2', '0', '1', 'pending', NULL, NULL, NULL, '2025-11-29 10:55:35'),
('38', '26', '2', '0', '1', 'pending', NULL, NULL, NULL, '2025-11-29 10:55:35');

-- Table: users
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'bcrypt hashed password',
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Path to profile picture',
  `email_verified` tinyint(1) DEFAULT '0',
  `email_verification_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1' COMMENT 'Account active status',
  `is_blocked` tinyint(1) DEFAULT '0' COMMENT 'Admin can block users',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_active` (`is_active`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Core user accounts table';

INSERT INTO `users` (`id`, `email`, `password`, `first_name`, `last_name`, `phone`, `address`, `avatar`, `email_verified`, `email_verification_token`, `is_active`, `is_blocked`, `last_login`, `created_at`, `updated_at`) VALUES
('1', 'admin@gmail.com', '$2y$10$BINhO08G1d0W.jnwQ/xHDeDz.B9uzRxfgo9.osphNDGYUPYlESdHi', 'System', 'Administrator', NULL, NULL, NULL, '1', NULL, '1', '0', '2025-12-04 10:59:44', '2025-10-21 06:59:16', '2025-12-04 10:59:44'),
('2', 'petowner@gmail.com', '$2y$10$BOoPpJZc7z0avhTFr0zgJ.9ifJI3bFnTZFue/JDvExdM5NeFi9B.2', 'Hesara', 'Liyanage', '0775983002', '145/2/1, Gonahena, Kadawatha', '/PETVET/uploads/avatars/avatar_1764576816_692d4e30557c5.jpg', '1', NULL, '1', '0', '2025-12-05 08:05:48', '2025-10-21 06:59:16', '2025-12-05 08:05:48'),
('3', 'vet@gmail.com', '$2y$10$BINhO08G1d0W.jnwQ/xHDeDz.B9uzRxfgo9.osphNDGYUPYlESdHi', 'Sarah', 'Johnson', '0777654321', NULL, NULL, '1', NULL, '1', '0', '2025-12-05 08:08:29', '2025-10-21 06:59:16', '2025-12-05 08:08:29'),
('4', 'trainer@gmail.com', '$2y$10$BINhO08G1d0W.jnwQ/xHDeDz.B9uzRxfgo9.osphNDGYUPYlESdHi', 'Tom', 'Trainer', '0771234567', '123 Trainer St, Colombo', NULL, '1', NULL, '1', '0', '2025-12-03 12:33:54', '2025-10-21 07:42:28', '2025-12-03 12:33:54'),
('5', 'sitter@gmail.com', '$2y$10$BINhO08G1d0W.jnwQ/xHDeDz.B9uzRxfgo9.osphNDGYUPYlESdHi', 'Sam', 'Sitter', '0772234567', '456 Sitter Ave, Kandy', NULL, '1', NULL, '1', '0', '2025-10-22 10:55:09', '2025-10-21 07:42:28', '2025-10-22 10:55:09'),
('6', 'breeder@gmail.com', '$2y$10$BINhO08G1d0W.jnwQ/xHDeDz.B9uzRxfgo9.osphNDGYUPYlESdHi', 'Bob', 'Breeder', '0773234567', '789 Breeder Rd, Galle', NULL, '1', NULL, '1', '0', '2025-10-22 10:56:37', '2025-10-21 07:42:28', '2025-10-22 10:56:37'),
('7', 'groomer@gmail.com', '$2y$10$BINhO08G1d0W.jnwQ/xHDeDz.B9uzRxfgo9.osphNDGYUPYlESdHi', 'Grace', 'Groomer', '0774234567', '321 Groomer Lane, Negombo', NULL, '1', NULL, '1', '0', '2025-10-22 10:57:55', '2025-10-21 07:42:28', '2025-10-22 10:57:55'),
('8', 'recep@gmail.com', '$2y$10$BINhO08G1d0W.jnwQ/xHDeDz.B9uzRxfgo9.osphNDGYUPYlESdHi', 'Rita', 'Receptionist', '0775234567', '654 Clinic St, Colombo 7', NULL, '1', NULL, '1', '0', '2025-11-26 06:12:53', '2025-10-21 07:42:28', '2025-11-26 06:12:53'),
('9', 'manager@gmail.com', '$2y$10$BINhO08G1d0W.jnwQ/xHDeDz.B9uzRxfgo9.osphNDGYUPYlESdHi', 'Cpt ', 'Dihindu', '0776234567', NULL, '/PETVET/uploads/avatars/avatar_1764764545_69302b8124546.png', '1', NULL, '1', '0', '2025-12-06 07:41:02', '2025-10-21 08:17:00', '2025-12-06 07:57:44'),
('10', 'peterpoker@gmail.com', '$2y$10$oul5xyrLiqPgJdW1AfdYdu0hdvnu6BDzPSVtT7hacTk9W8v4Kwm5e', 'Hesara', 'Liyanage', '0775983002', '145/2/1', NULL, '1', NULL, '1', '0', '2025-10-21 09:19:38', '2025-10-21 09:18:56', '2025-10-21 09:19:38'),
('11', 'allinone@gmail.com', '$2y$10$B.T98na4LBqC/JOSsTVqPOP64wmqXUVCDk1fd5hxVczwC9HPDJGCq', 'Hesara', 'Liyanage', '0775983002', '145/2/1', NULL, '1', NULL, '1', '0', '2025-10-23 06:10:04', '2025-10-21 09:53:06', '2025-10-23 06:10:04'),
('12', 'amindasithummal@gmail.com', '$2y$10$1Rsugbt18Haag0Ctp9U91.OGY2.VCDiXnde4h5/Zd8yiDL27rjnhi', 'aminda', 'sithummal', '0701101519', 'addlsdksl', NULL, '1', NULL, '1', '0', '2025-10-21 10:03:11', '2025-10-21 10:02:35', '2025-10-21 10:03:11'),
('13', 'amindavet@gmail.com', '$2y$10$gGkycyLU.By6q4pB7oq8nOC45XJVRNvGyVY4XyGrBz9aDjWuUAsyO', 'Dihindu', 'Hesara', '0775983002', '145/2/1 Gonahena Kadawatha', NULL, '1', NULL, '1', '0', '2025-10-22 08:18:04', '2025-10-22 08:17:28', '2025-10-22 08:18:04'),
('14', 'clinica@gmail.com', '$2y$10$JBzMnwuUXSho5xGzWmrxc.PSQcsvs47JHls5kLK59Ql5jWSoDEOVS', 'Hesara', 'Liyanage', '0775983002', '', NULL, '1', NULL, '1', '0', NULL, '2025-10-22 08:52:57', '2025-10-22 08:52:57'),
('15', 'pokerpeter474@gmail.com', '$2y$10$.WzOwjbd/yTG2Pzh/OKyhOGBDBtfCOximkddp5p.Y4mF/04CPjc4u', 'Peter', 'Parker', '0775983002', '', NULL, '1', NULL, '1', '0', '2025-10-22 10:13:36', '2025-10-22 10:13:19', '2025-10-22 10:13:36'),
('16', 'ddfgdfdgdg@gmail.com', '$2y$10$V4aOFQDrcvBg4dbTzMYkqO5m8wa2HKGGO.x7VvrPLRpE1b1rNYUIy', 'aad', 'sddgdgdg', '0701294656', 'fhfhthh', NULL, '1', NULL, '1', '0', NULL, '2025-10-22 16:09:16', '2025-10-22 16:09:16'),
('17', 'receptionist@petvet.com', '$2y$10$4lwVpDlRIKo0HVZ8rciaZ.5I9z0T61JsmGzsaFCVozSpJXUJo38i.', 'James', 'Bond', '0771234567', NULL, '/PETVET/uploads/avatars/avatar_1764581626_692d60fa2f9ed.png', '1', NULL, '1', '0', '2025-12-04 11:09:33', '2025-11-26 06:25:37', '2025-12-04 11:09:33'),
('18', 'michael.chen@petvet.com', 'hashed_password', 'Michael', 'Chen', '0771234568', NULL, NULL, '0', NULL, '1', '0', NULL, '2025-11-29 10:51:09', '2025-11-29 10:51:09'),
('19', 'emily.rodriguez@petvet.com', 'hashed_password', 'Emily', 'Rodriguez', '0771234569', NULL, NULL, '0', NULL, '1', '0', NULL, '2025-11-29 10:51:09', '2025-11-29 10:51:09'),
('20', 'james.wilson@petvet.com', 'hashed_password', 'James', 'Wilson', '0771234570', NULL, NULL, '0', NULL, '1', '0', NULL, '2025-11-29 10:51:09', '2025-11-29 10:51:09'),
('21', 'priya.perera@petvet.com', 'hashed_password', 'Priya', 'Perera', '0771234571', NULL, NULL, '0', NULL, '1', '0', NULL, '2025-11-29 10:51:09', '2025-11-29 10:51:09'),
('22', 'nuwan.silva@petvet.com', 'hashed_password', 'Nuwan', 'Silva', '0771234572', NULL, NULL, '0', NULL, '1', '0', NULL, '2025-11-29 10:51:09', '2025-11-29 10:51:09'),
('23', 'anjali.fernando@petvet.com', 'hashed_password', 'Anjali', 'Fernando', '0771234573', NULL, NULL, '0', NULL, '1', '0', NULL, '2025-11-29 10:51:09', '2025-11-29 10:51:09'),
('24', 'rajesh.kumar@petvet.com', 'hashed_password', 'Rajesh', 'Kumar', '0771234574', NULL, NULL, '0', NULL, '1', '0', NULL, '2025-11-29 10:51:09', '2025-11-29 10:51:09'),
('25', 'lisa.thompson@petvet.com', 'hashed_password', 'Lisa', 'Thompson', '0771234575', NULL, NULL, '0', NULL, '1', '0', NULL, '2025-11-29 10:55:12', '2025-11-29 10:55:12'),
('26', 'david.lee@petvet.com', 'hashed_password', 'David', 'Lee', '0771234576', NULL, NULL, '0', NULL, '1', '0', NULL, '2025-11-29 10:55:12', '2025-11-29 10:55:12');

-- Table: vet_profiles
DROP TABLE IF EXISTS `vet_profiles`;
CREATE TABLE `vet_profiles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `clinic_id` int DEFAULT NULL,
  `license_number` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `specialization` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `years_experience` int DEFAULT '0',
  `education` text COLLATE utf8mb4_unicode_ci,
  `consultation_fee` decimal(10,2) DEFAULT '0.00',
  `rating` decimal(3,2) DEFAULT '0.00' COMMENT 'Average rating 0.00-5.00',
  `total_reviews` int DEFAULT '0',
  `bio` text COLLATE utf8mb4_unicode_ci,
  `available` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  UNIQUE KEY `license_number` (`license_number`),
  KEY `idx_clinic` (`clinic_id`),
  KEY `idx_available` (`available`),
  KEY `idx_user` (`user_id`),
  CONSTRAINT `vet_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `vet_profiles_ibfk_2` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Veterinarian profiles';

INSERT INTO `vet_profiles` (`id`, `user_id`, `clinic_id`, `license_number`, `specialization`, `years_experience`, `education`, `consultation_fee`, `rating`, `total_reviews`, `bio`, `available`, `created_at`, `updated_at`) VALUES
('1', '3', '1', 'VET-LK-2020-001234', 'General Practice, Surgery', '8', NULL, '0.00', '0.00', '0', 'Experienced veterinarian specializing in small animals with a passion for preventive care.', '1', '2025-10-21 06:59:16', '2025-10-21 06:59:16'),
('2', '13', '1', 'DFSD4561863', 'Dental', '10', NULL, '0.00', '0.00', '0', NULL, '1', '2025-10-22 08:17:28', '2025-10-22 08:17:28');

SET FOREIGN_KEY_CHECKS=1;
