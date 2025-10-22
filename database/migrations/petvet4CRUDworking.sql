-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 22, 2025 at 01:04 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `petvet`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL COMMENT 'login, logout, role_switch, profile_update, etc.',
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Additional context about the action' CHECK (json_valid(`details`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Audit trail for security monitoring';

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `ip_address`, `user_agent`, `details`, `created_at`) VALUES
(1, 2, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 07:15:33'),
(2, 2, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 07:22:16'),
(3, 2, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 07:25:08'),
(4, 2, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 07:43:12'),
(5, 2, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 07:53:50'),
(6, 2, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 07:54:41'),
(7, 4, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 07:54:53'),
(8, 4, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 07:58:34'),
(9, 5, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 07:58:44'),
(10, 5, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 07:58:47'),
(11, 5, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 07:59:43'),
(12, 5, 'logout', '::1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/17.5 Mobile/15A5370a Safari/602.1', '[]', '2025-10-21 08:01:26'),
(13, 6, 'login', '::1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/17.5 Mobile/15A5370a Safari/602.1', '[]', '2025-10-21 08:02:22'),
(14, 6, 'logout', '::1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/17.5 Mobile/15A5370a Safari/602.1', '[]', '2025-10-21 08:03:52'),
(15, 2, 'login', '::1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/17.5 Mobile/15A5370a Safari/602.1', '[]', '2025-10-21 08:03:59'),
(16, 2, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 08:10:52'),
(17, 2, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 08:11:39'),
(18, 2, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 08:13:32'),
(19, 6, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 08:13:53'),
(20, 4, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-21 08:26:01'),
(21, 4, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-21 08:26:53'),
(22, 8, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-21 08:27:15'),
(23, 8, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-21 08:28:02'),
(24, 9, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-21 08:28:10'),
(25, 9, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-21 08:28:22'),
(26, 5, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-21 08:28:36'),
(27, 5, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-21 08:28:40'),
(28, 6, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-21 08:28:56'),
(29, 6, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-21 08:29:09'),
(30, 7, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-21 08:29:22'),
(31, 7, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-21 08:29:33'),
(32, 3, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 08:32:17'),
(33, 3, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 08:32:26'),
(34, 1, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 08:32:39'),
(35, 1, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 08:32:42'),
(36, 2, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-21 08:32:48'),
(37, 2, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 08:41:20'),
(38, 4, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 08:44:09'),
(39, 4, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 08:44:24'),
(40, 4, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 08:44:35'),
(41, 4, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 08:46:02'),
(42, 6, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 08:46:12'),
(43, 1, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 08:46:26'),
(44, 1, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 08:56:20'),
(45, 2, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 08:58:56'),
(46, 2, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 08:59:34'),
(47, 2, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 09:09:26'),
(48, 2, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 09:09:30'),
(49, 2, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 09:12:03'),
(50, 8, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 09:12:23'),
(51, 8, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 09:13:07'),
(52, 5, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 09:13:20'),
(53, 5, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 09:14:01'),
(54, 3, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 09:14:10'),
(55, 10, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 09:19:38'),
(56, 10, 'role_switch', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '{\"new_role\":\"trainer\"}', '2025-10-21 09:19:48'),
(57, 10, 'role_switch', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '{\"new_role\":\"sitter\"}', '2025-10-21 09:26:38'),
(58, 10, 'role_switch', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '{\"new_role\":\"trainer\"}', '2025-10-21 09:33:37'),
(59, 10, 'role_switch', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '{\"new_role\":\"trainer\"}', '2025-10-21 09:33:46'),
(60, 10, 'role_switch', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '{\"new_role\":\"sitter\"}', '2025-10-21 09:33:52'),
(61, 10, 'role_switch', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '{\"new_role\":\"pet_owner\"}', '2025-10-21 09:33:59'),
(62, 10, 'role_switch', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '{\"new_role\":\"trainer\"}', '2025-10-21 09:34:10'),
(63, 10, 'role_switch', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '{\"new_role\":\"pet_owner\"}', '2025-10-21 09:34:16'),
(64, 10, 'role_switch', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '{\"new_role\":\"trainer\"}', '2025-10-21 09:34:24'),
(65, 10, 'role_switch', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '{\"new_role\":\"pet_owner\"}', '2025-10-21 09:34:30'),
(66, 3, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 09:35:11'),
(67, 10, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 09:45:21'),
(68, 11, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 09:53:52'),
(69, 11, 'role_switch', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '{\"new_role\":\"trainer\"}', '2025-10-21 09:54:00'),
(70, 11, 'role_switch', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '{\"new_role\":\"sitter\"}', '2025-10-21 09:54:05'),
(71, 11, 'role_switch', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '{\"new_role\":\"breeder\"}', '2025-10-21 09:54:11'),
(72, 11, 'role_switch', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '{\"new_role\":\"pet_owner\"}', '2025-10-21 09:54:38'),
(73, 11, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 09:55:05'),
(74, 11, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 09:59:48'),
(75, 12, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 10:03:11'),
(76, 12, 'role_switch', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '{\"new_role\":\"breeder\"}', '2025-10-21 10:03:28'),
(77, 12, 'role_switch', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '{\"new_role\":\"groomer\"}', '2025-10-21 10:03:43'),
(78, 12, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 10:04:08'),
(79, 11, 'role_switch', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '{\"new_role\":\"trainer\"}', '2025-10-21 10:06:07'),
(80, 11, 'logout', '::1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/17.5 Mobile/15A5370a Safari/602.1', '[]', '2025-10-21 10:41:09'),
(81, 4, 'login', '::1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/17.5 Mobile/15A5370a Safari/602.1', '[]', '2025-10-21 10:41:17'),
(82, 4, 'logout', '::1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/17.5 Mobile/15A5370a Safari/602.1', '[]', '2025-10-21 12:41:13'),
(83, 9, 'login', '::1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/17.5 Mobile/15A5370a Safari/602.1', '[]', '2025-10-21 12:41:22'),
(84, 9, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 13:23:03'),
(85, 9, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 13:23:25'),
(86, 2, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-21 14:04:56'),
(87, 2, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-21 14:05:21'),
(88, 2, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-21 14:07:37'),
(89, 3, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 19:05:07'),
(90, 2, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 19:05:21'),
(91, 2, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 19:38:21'),
(92, 9, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 19:38:58'),
(93, 9, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 19:40:35'),
(94, 2, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 19:49:58'),
(95, 2, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 20:18:15'),
(96, 1, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-21 20:18:39'),
(97, 1, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-22 04:25:21'),
(98, 2, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-22 04:25:40'),
(99, 2, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-22 04:26:02'),
(100, 9, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-22 04:26:12'),
(101, 9, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-22 04:28:19'),
(102, 2, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-22 04:28:25'),
(103, 2, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-22 04:37:38'),
(104, 9, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-22 04:37:54'),
(105, 9, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-22 04:38:42'),
(106, 2, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-22 04:38:58'),
(107, 2, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-22 06:27:16'),
(108, 2, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-22 07:11:50'),
(109, 2, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-22 07:29:39'),
(110, 13, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-22 08:18:04'),
(111, 13, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-22 08:18:56'),
(112, 11, 'login', '::1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/17.5 Mobile/15A5370a Safari/602.1', '[]', '2025-10-22 08:23:45'),
(113, 11, 'logout', '::1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/17.5 Mobile/15A5370a Safari/602.1', '[]', '2025-10-22 08:24:29'),
(114, 15, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-22 10:13:36'),
(115, 15, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-22 10:17:24'),
(116, 9, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-22 10:25:02'),
(117, 1, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-22 10:28:06'),
(118, 9, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-22 10:29:22'),
(119, 1, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-22 10:29:42'),
(120, 1, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-22 10:33:41'),
(121, 2, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-22 10:34:46'),
(122, 9, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-22 10:37:35'),
(123, 2, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-22 10:38:05'),
(124, 2, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-22 10:38:19'),
(125, 2, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-22 10:39:06'),
(126, 3, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-22 10:39:51'),
(127, 3, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-22 10:44:16'),
(128, 2, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-22 10:44:52'),
(129, 1, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '[]', '2025-10-22 10:45:09'),
(130, 9, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-22 10:46:04'),
(131, 9, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-22 10:49:43'),
(132, 8, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-22 10:50:09'),
(133, 8, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-22 10:50:46'),
(134, 4, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-22 10:51:12'),
(135, 4, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-22 10:54:26'),
(136, 5, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-22 10:55:09'),
(137, 5, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-22 10:56:18'),
(138, 6, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-22 10:56:37'),
(139, 6, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-22 10:57:30'),
(140, 7, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-22 10:57:55'),
(141, 7, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-22 10:59:06'),
(142, 2, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-22 11:02:06'),
(143, 8, 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-22 11:03:06'),
(144, 8, 'logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '[]', '2025-10-22 11:03:42');

-- --------------------------------------------------------

--
-- Table structure for table `clinics`
--

CREATE TABLE `clinics` (
  `id` int(11) NOT NULL,
  `clinic_name` varchar(255) NOT NULL,
  `clinic_address` text NOT NULL,
  `district` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `clinic_phone` varchar(20) DEFAULT NULL,
  `clinic_email` varchar(255) DEFAULT NULL,
  `operating_hours` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Store as {"monday": "9:00-17:00", ...}' CHECK (json_valid(`operating_hours`)),
  `license_document` varchar(500) DEFAULT NULL,
  `verification_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Veterinary clinics';

--
-- Dumping data for table `clinics`
--

INSERT INTO `clinics` (`id`, `clinic_name`, `clinic_address`, `district`, `city`, `clinic_phone`, `clinic_email`, `operating_hours`, `license_document`, `verification_status`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Happy Paws Veterinary Clinic', '123 Main Street, Colombo', 'Colombo', 'Colombo 07', '0112345678', 'info@happypaws.lk', NULL, NULL, 'approved', 1, '2025-10-21 06:59:16', '2025-10-21 06:59:16'),
(2, 'Peter PETVET', '145/2/1', 'Anuradhapura', NULL, '0775983002', 'allinone@gmail.com', NULL, NULL, 'approved', 1, '2025-10-22 08:52:57', '2025-10-22 08:52:57'),
(3, 'Pet Bros', '145/2/1', 'Badulla', NULL, '0775983002', 'gklnkler@gmail.com', NULL, NULL, 'approved', 1, '2025-10-22 10:13:19', '2025-10-22 10:13:19');

-- --------------------------------------------------------

--
-- Table structure for table `clinic_manager_profiles`
--

CREATE TABLE `clinic_manager_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `clinic_id` int(11) NOT NULL,
  `position` varchar(100) DEFAULT 'Manager',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Clinic manager profiles';

--
-- Dumping data for table `clinic_manager_profiles`
--

INSERT INTO `clinic_manager_profiles` (`id`, `user_id`, `clinic_id`, `position`, `created_at`, `updated_at`) VALUES
(1, 9, 1, 'Clinic Manager', '2025-10-21 08:17:00', '2025-10-21 08:17:00'),
(2, 14, 2, 'Manager', '2025-10-22 08:52:57', '2025-10-22 08:52:57'),
(3, 15, 3, 'Manager', '2025-10-22 10:13:19', '2025-10-22 10:13:19');

-- --------------------------------------------------------

--
-- Table structure for table `clinic_staff`
--

CREATE TABLE `clinic_staff` (
  `id` int(11) NOT NULL,
  `clinic_id` int(11) NOT NULL COMMENT 'Reference to the clinic this staff belongs to',
  `user_id` int(11) DEFAULT NULL COMMENT 'Link to users table if staff has system account (e.g., receptionist)',
  `name` varchar(255) NOT NULL,
  `role` varchar(100) NOT NULL COMMENT 'Veterinary Assistant, Front Desk, Support Staff, etc.',
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `next_shift` varchar(255) DEFAULT NULL COMMENT 'Next scheduled shift information',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Clinic staff members management';

--
-- Dumping data for table `clinic_staff`
--

INSERT INTO `clinic_staff` (`id`, `clinic_id`, `user_id`, `name`, `role`, `email`, `phone`, `status`, `next_shift`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'Anushka Perera', 'Veterinary Assistant', 'anushka.assist@petvet.lk', '+94 71 234 5678', 'Active', NULL, '2025-10-22 10:20:35', '2025-10-22 10:20:35'),
(3, 1, NULL, 'Kavinda Fernando', 'Veterinary Assistant', 'kavinda.assist@petvet.lk', '+94 77 555 1234', 'Active', NULL, '2025-10-22 10:20:35', '2025-10-22 10:20:35'),
(6, 1, NULL, 'Aminda Sitummal', 'Veterinary Assistant', 'tharindu.front@petvet.lk', '0771234567', 'Inactive', NULL, '2025-10-22 10:20:35', '2025-10-22 10:31:20'),
(11, 1, NULL, 'Test Employee 122616', 'Support Staff', 'test122616@petvet.lk', '+94 71 2609479', 'Active', NULL, '2025-10-22 10:26:16', '2025-10-22 10:26:16');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `success` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Track login attempts for security';

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `email`, `ip_address`, `attempted_at`, `success`) VALUES
(59, 'manager@gmail.com', '::1', '2025-10-21 12:41:22', 1),
(60, 'manager@gmail.com', '::1', '2025-10-21 13:23:14', 0),
(61, 'manager@gmail.com', '::1', '2025-10-21 13:23:25', 1),
(62, 'petowner@gmail.com', '::1', '2025-10-21 14:04:56', 1),
(63, 'petowner@gmail.com', '::1', '2025-10-21 14:07:37', 1),
(64, 'petowner@gmail.com', '::1', '2025-10-21 19:05:21', 1),
(65, 'manager@gmail.com', '::1', '2025-10-21 19:38:58', 1),
(66, 'petowner@gmail.com', '::1', '2025-10-21 19:49:58', 1),
(67, 'admin@gmail.com', '::1', '2025-10-21 20:18:39', 1),
(68, 'petowner@gmail.com', '::1', '2025-10-22 04:25:40', 1),
(69, 'manager@gmail.com', '::1', '2025-10-22 04:26:12', 1),
(70, 'petowner@gmail.com', '::1', '2025-10-22 04:28:25', 1),
(71, 'manager@gmail.com', '::1', '2025-10-22 04:37:54', 1),
(72, 'petowner@gmail.com', '::1', '2025-10-22 04:38:51', 0),
(73, 'petowner@gmail.com', '::1', '2025-10-22 04:38:58', 1),
(74, 'petowner@gmail.com', '::1', '2025-10-22 07:11:50', 1),
(75, 'amindavet@gmail.com', '::1', '2025-10-22 08:18:04', 1),
(76, 'allinone@gmail.com', '::1', '2025-10-22 08:23:45', 1),
(77, 'pokerpeter474@gmail.com', '::1', '2025-10-22 10:13:36', 1),
(78, 'manager@gmail.com', '::1', '2025-10-22 10:25:02', 1),
(79, 'manager@gmail.com', '::1', '2025-10-22 10:29:22', 1),
(80, 'admin@gmail.com', '::1', '2025-10-22 10:29:42', 1),
(81, 'petowner@gmail.com', '::1', '2025-10-22 10:34:46', 1),
(82, 'petowner@gmail.com', '::1', '2025-10-22 10:37:55', 0),
(83, 'petowner@gmail.com', '::1', '2025-10-22 10:38:00', 0),
(84, 'petowner@gmail.com', '::1', '2025-10-22 10:38:05', 1),
(85, 'petowner@gmail.com', '::1', '2025-10-22 10:38:19', 1),
(86, 'vet@gmail.com', '::1', '2025-10-22 10:39:51', 1),
(87, 'admin@gmail.com', '::1', '2025-10-22 10:45:09', 1),
(88, 'manager@gmail.com', '::1', '2025-10-22 10:46:04', 1),
(89, 'recep@gmail.com', '::1', '2025-10-22 10:50:09', 1),
(90, 'trainer@gmail.com', '::1', '2025-10-22 10:51:12', 1),
(91, 'sitter@gmail.com', '::1', '2025-10-22 10:55:09', 1),
(92, 'breeder@gmail.com', '::1', '2025-10-22 10:56:37', 1),
(93, 'groomer@gmail.com', '::1', '2025-10-22 10:57:55', 1),
(94, 'recep@gmail.com', '::1', '2025-10-22 11:03:06', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reset_token` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Password reset tokens';

-- --------------------------------------------------------

--
-- Table structure for table `pets`
--

CREATE TABLE `pets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `species` varchar(50) NOT NULL,
  `breed` varchar(100) DEFAULT NULL,
  `sex` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `weight` decimal(10,2) DEFAULT NULL,
  `color` text DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `photo_url` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pets`
--

INSERT INTO `pets` (`id`, `user_id`, `name`, `species`, `breed`, `sex`, `date_of_birth`, `weight`, `color`, `allergies`, `notes`, `photo_url`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 2, 'Tuffey', 'Dog', 'Golden Retriever', 'Male', '2025-03-22', 45.00, 'brown color', 'allergy to prawns', 'vaccinated', '/PETVET/public/images/pets/pet_2_1761072870_68f7d6e6b9720.jpg', 1, '2025-10-21 18:54:30', '2025-10-21 19:04:42'),
(2, 2, 'Rocky', 'Dog', 'Indian Paraiah', 'Male', '2023-02-09', 15.00, 'Brown,White', 'Allergy to chicken', 'A friendly playful dog', '/PETVET/public/images/pets/pet_2_1761073795_68f7da83b8d1f.png', 1, '2025-10-21 19:09:55', '2025-10-21 19:09:55'),
(3, 2, 'pissu pusa', 'Cat', 'Persian', 'Male', '2023-01-22', 10.00, 'orange and white', 'fish', 'eats a lot', '/PETVET/public/images/pets/pet_2_1761073915_68f7dafb2316f.png', 1, '2025-10-21 19:11:55', '2025-10-22 04:37:27');

-- --------------------------------------------------------

--
-- Table structure for table `pet_owner_profiles`
--

CREATE TABLE `pet_owner_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `preferred_vet_id` int(11) DEFAULT NULL,
  `emergency_contact_name` varchar(255) DEFAULT NULL,
  `emergency_contact_phone` varchar(20) DEFAULT NULL,
  `notification_preferences` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Email, SMS, push notification settings' CHECK (json_valid(`notification_preferences`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Pet owner specific profile data';

--
-- Dumping data for table `pet_owner_profiles`
--

INSERT INTO `pet_owner_profiles` (`id`, `user_id`, `preferred_vet_id`, `emergency_contact_name`, `emergency_contact_phone`, `notification_preferences`, `created_at`, `updated_at`) VALUES
(1, 10, NULL, NULL, NULL, NULL, '2025-10-21 09:18:56', '2025-10-21 09:18:56'),
(2, 11, NULL, NULL, NULL, NULL, '2025-10-21 09:53:06', '2025-10-21 09:53:06'),
(3, 12, NULL, NULL, NULL, NULL, '2025-10-21 10:02:35', '2025-10-21 10:02:35');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` enum('food','toys','litter','grooming','accessories','medicine') NOT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `seller` varchar(255) DEFAULT 'PetVet Official Store',
  `sold` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `category`, `image_url`, `stock`, `seller`, `sold`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Denta Fun Veggie Jaw Bone', 'A healthy, delicious treat for your dog. Made from natural ingredients to support dental health while satisfying chewing needs. Composition sweet potato meal, pea starch, vegetable by-products, minerals, yeast, cellulose, oils and fats, rosemary | gluten-free formula | vegetarian | no added sugar', 500.00, 'food', '/PETVET/views/shared/images/fproduct1.png', 25, 'PetVet Official Store', 340, 1, '2025-10-21 12:48:40', '2025-10-21 12:48:40'),
(2, 'Trixie Litter Scoop', 'High-quality litter scoop made from durable materials. Perfect for easy and hygienic litter box maintenance. Features comfortable grip handle and efficient scooping design.', 900.00, 'litter', '/PETVET/views/shared/images/fproduct2.png', 10, 'PetVet Store', 185, 1, '2025-10-21 12:48:40', '2025-10-22 04:38:14'),
(3, 'Dog Toy Tug Rope', 'Interactive rope toy perfect for playing tug-of-war with your dog. Made from durable cotton fibers that help clean teeth during play. Great for bonding and exercise.', 2100.00, 'toys', '/PETVET/views/shared/images/fproduct3.png', 8, 'PlayTime Pets', 95, 1, '2025-10-21 12:48:40', '2025-10-21 12:48:40'),
(4, 'Trixie Aloe Vera Shampoo', 'Gentle pet shampoo enriched with Aloe Vera for sensitive skin. Cleanses thoroughly while moisturizing and soothing your pet\'s coat. Suitable for regular use.', 1900.00, 'grooming', '/PETVET/views/shared/images/fproduct4.png', 12, 'Trixie Official', 220, 1, '2025-10-21 12:48:40', '2025-10-21 12:48:40'),
(11, 'Wired Controller', 'xbox 360 wired controller', 3500.00, 'toys', '/PETVET/public/images/products/product_1761054589_68f78f7db824b_0.jpg', 20, 'PetVet Store', 0, 1, '2025-10-21 13:49:49', '2025-10-21 18:08:28'),
(14, 'awdadad', 'dadadadadada', 150.00, 'litter', '/PETVET/public/images/products/product_1761070137_68f7cc39782f5_0.jpg', 156, 'PetVet Store', 0, 1, '2025-10-21 18:08:57', '2025-10-21 18:08:57'),
(15, 'yomal', 'nnnjknjknjknknk', 100.00, 'food', '/PETVET/public/images/products/product_1761075606_68f7e1961389a_0.jpg', 15, 'PetVet Store', 0, 1, '2025-10-21 19:40:06', '2025-10-21 19:40:06');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_url` varchar(500) NOT NULL,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_url`, `display_order`, `created_at`) VALUES
(1, 1, '/PETVET/views/shared/images/fproduct1.png', 0, '2025-10-21 13:42:05'),
(2, 2, '/PETVET/views/shared/images/fproduct2.png', 0, '2025-10-21 13:42:05'),
(3, 3, '/PETVET/views/shared/images/fproduct3.png', 0, '2025-10-21 13:42:05'),
(4, 4, '/PETVET/views/shared/images/fproduct4.png', 0, '2025-10-21 13:42:05'),
(8, 11, '/PETVET/public/images/products/product_1761054589_68f78f7db824b_0.jpg', 0, '2025-10-21 13:49:49'),
(9, 11, '/PETVET/public/images/products/product_1761054589_68f78f7db8625_1.jpg', 1, '2025-10-21 13:49:49'),
(10, 11, '/PETVET/public/images/products/product_1761054589_68f78f7db8814_2.jpg', 2, '2025-10-21 13:49:49'),
(11, 11, '/PETVET/public/images/products/product_1761054589_68f78f7db89c9_3.jpg', 3, '2025-10-21 13:49:49'),
(17, 14, '/PETVET/public/images/products/product_1761070137_68f7cc39782f5_0.jpg', 0, '2025-10-21 18:08:57'),
(18, 15, '/PETVET/public/images/products/product_1761075606_68f7e1961389a_0.jpg', 0, '2025-10-21 19:40:06'),
(19, 15, '/PETVET/public/images/products/product_1761075606_68f7e19613e21_1.jpg', 1, '2025-10-21 19:40:06'),
(20, 15, '/PETVET/public/images/products/product_1761075606_68f7e196140a3_2.jpg', 2, '2025-10-21 19:40:06'),
(21, 15, '/PETVET/public/images/products/product_1761075606_68f7e19614293_3.jpg', 3, '2025-10-21 19:40:06'),
(22, 15, '/PETVET/public/images/products/product_1761075606_68f7e1961444a_4.jpg', 4, '2025-10-21 19:40:06');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL COMMENT 'System identifier (pet_owner, vet, etc.)',
  `role_display_name` varchar(100) NOT NULL COMMENT 'Human-readable name',
  `description` text DEFAULT NULL,
  `requires_verification` tinyint(1) DEFAULT 0 COMMENT 'Needs admin approval',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Available system roles';

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`, `role_display_name`, `description`, `requires_verification`, `created_at`) VALUES
(1, 'pet_owner', 'Pet Owner', 'Regular pet owner who can book appointments and manage pets', 0, '2025-10-21 06:59:16'),
(2, 'vet', 'Veterinarian', 'Licensed veterinarian providing medical services', 1, '2025-10-21 06:59:16'),
(3, 'clinic_manager', 'Clinic Manager', 'Manages clinic operations and staff', 1, '2025-10-21 06:59:16'),
(4, 'admin', 'Administrator', 'System administrator with full access', 0, '2025-10-21 06:59:16'),
(5, 'receptionist', 'Receptionist', 'Front desk staff managing appointments', 0, '2025-10-21 06:59:16'),
(6, 'trainer', 'Pet Trainer', 'Professional pet trainer offering training services', 1, '2025-10-21 06:59:16'),
(7, 'sitter', 'Pet Sitter', 'Professional pet sitter offering sitting services', 1, '2025-10-21 06:59:16'),
(8, 'breeder', 'Pet Breeder', 'Professional breeder managing breeding operations', 1, '2025-10-21 06:59:16'),
(9, 'groomer', 'Pet Groomer', 'Professional groomer offering grooming services', 1, '2025-10-21 06:59:16');

-- --------------------------------------------------------

--
-- Table structure for table `role_verification_documents`
--

CREATE TABLE `role_verification_documents` (
  `id` int(11) NOT NULL,
  `user_role_id` int(11) NOT NULL,
  `document_type` enum('license','certificate','id','business_permit','other') NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL COMMENT 'Relative path from uploads directory',
  `file_size` int(11) NOT NULL COMMENT 'File size in bytes',
  `mime_type` varchar(100) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Verification documents for service providers';

-- --------------------------------------------------------

--
-- Table structure for table `sell_pet_listings`
--

CREATE TABLE `sell_pet_listings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `species` varchar(50) NOT NULL,
  `breed` varchar(100) NOT NULL,
  `age` varchar(50) NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `location` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `phone2` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `status` enum('pending','approved','rejected','sold') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sell_pet_listings`
--

INSERT INTO `sell_pet_listings` (`id`, `user_id`, `name`, `species`, `breed`, `age`, `gender`, `price`, `location`, `description`, `phone`, `phone2`, `email`, `status`, `created_at`, `updated_at`) VALUES
(2, 2, 'Tuffey', 'Dog', 'Golden Retreiver', '5', 'Male', 80000.00, 'Piliyandala', 'kmflmkkfnfjnnfsnfnjnkjfnsknfnsjfnjnf', '0775983002', '', '', 'approved', '2025-10-21 20:17:48', '2025-10-21 20:42:00');

-- --------------------------------------------------------

--
-- Table structure for table `sell_pet_listing_badges`
--

CREATE TABLE `sell_pet_listing_badges` (
  `id` int(11) NOT NULL,
  `listing_id` int(11) NOT NULL,
  `badge` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sell_pet_listing_images`
--

CREATE TABLE `sell_pet_listing_images` (
  `id` int(11) NOT NULL,
  `listing_id` int(11) NOT NULL,
  `image_url` varchar(500) NOT NULL,
  `display_order` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sell_pet_listing_images`
--

INSERT INTO `sell_pet_listing_images` (`id`, `listing_id`, `image_url`, `display_order`, `created_at`) VALUES
(2, 2, '/PETVET/public/images/uploads/pet-listings/pet_2_1761077868_0.png', 0, '2025-10-21 20:17:48'),
(3, 2, '/PETVET/public/images/uploads/pet-listings/pet_2_1761077868_1.png', 1, '2025-10-21 20:17:48');

-- --------------------------------------------------------

--
-- Table structure for table `service_provider_profiles`
--

CREATE TABLE `service_provider_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_type` enum('trainer','sitter','breeder','groomer') NOT NULL,
  `business_name` varchar(255) DEFAULT NULL,
  `service_area` varchar(255) DEFAULT NULL COMMENT 'City/District where services offered',
  `experience_years` int(11) DEFAULT 0,
  `certifications` text DEFAULT NULL COMMENT 'Comma-separated or JSON array',
  `specializations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Array of specialties' CHECK (json_valid(`specializations`)),
  `price_range_min` decimal(10,2) DEFAULT NULL,
  `price_range_max` decimal(10,2) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT 0.00,
  `total_reviews` int(11) DEFAULT 0,
  `bio` text DEFAULT NULL,
  `available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Profiles for trainers, sitters, breeders, groomers';

--
-- Dumping data for table `service_provider_profiles`
--

INSERT INTO `service_provider_profiles` (`id`, `user_id`, `role_type`, `business_name`, `service_area`, `experience_years`, `certifications`, `specializations`, `price_range_min`, `price_range_max`, `rating`, `total_reviews`, `bio`, `available`, `created_at`, `updated_at`) VALUES
(1, 4, 'trainer', 'Pro Pet Training', 'Colombo, Kandy', 8, 'Certified Professional Dog Trainer (CPDT)', NULL, NULL, NULL, 4.80, 45, 'Professional dog training services for all breeds', 1, '2025-10-21 07:42:28', '2025-10-21 07:42:28'),
(2, 5, 'sitter', 'Caring Pet Sitters', 'Kandy, Peradeniya', 5, 'Pet First Aid Certified', NULL, NULL, NULL, 4.90, 67, 'Trusted pet sitting and boarding services', 1, '2025-10-21 07:42:28', '2025-10-21 07:42:28'),
(3, 6, 'breeder', 'Premium Breeders', 'Galle, Matara', 12, 'Registered Breeder - Kennel Club', NULL, NULL, NULL, 4.70, 23, 'Ethical breeding of purebred dogs and cats', 1, '2025-10-21 07:42:28', '2025-10-21 07:42:28'),
(4, 7, 'groomer', 'Pawfect Grooming', 'Negombo, Colombo', 6, 'Certified Master Groomer', NULL, NULL, NULL, 5.00, 89, 'Professional grooming services for all pets', 1, '2025-10-21 07:42:28', '2025-10-21 07:42:28'),
(5, 10, 'trainer', 'Obey', 'Kadawatha', 10, 'adpadlaldp', NULL, NULL, NULL, 0.00, 0, 'Specialization: Obey', 1, '2025-10-21 09:18:56', '2025-10-21 09:18:56'),
(6, 10, 'sitter', NULL, '', 15, NULL, NULL, NULL, NULL, 0.00, 0, 'Home Type: house_with_yard\nPet Types: Cats, Dogs\nMax Pets: 5\nOvernight: Yes', 1, '2025-10-21 09:18:56', '2025-10-21 09:18:56'),
(7, 11, 'trainer', 'Obey', 'Kaduwela', 10, '', NULL, NULL, NULL, 0.00, 0, 'Specialization: Obey', 1, '2025-10-21 09:53:06', '2025-10-21 09:53:06'),
(8, 11, 'groomer', 'Aminda Groomers', '', 10, NULL, NULL, NULL, NULL, 0.00, 0, 'Services: nope\nPricing: 1500', 1, '2025-10-21 09:53:06', '2025-10-21 09:53:06'),
(9, 11, 'sitter', NULL, '', 10, NULL, NULL, NULL, NULL, 0.00, 0, 'Home Type: apartment\nPet Types: Cats, Dogs\nMax Pets: 1\nOvernight: No', 1, '2025-10-21 09:53:06', '2025-10-21 09:53:06'),
(10, 11, 'breeder', 'Breeding: Germen Shepherd', NULL, 10, '156a15544d', NULL, NULL, NULL, 0.00, 0, 'Breeds: Germen Shepherd\nPhilosophy: ', 1, '2025-10-21 09:53:06', '2025-10-21 09:53:06'),
(11, 12, 'groomer', 'ba', '', 1, NULL, NULL, NULL, NULL, 0.00, 0, 'Services: okkoma\nPricing: ba', 1, '2025-10-21 10:02:35', '2025-10-21 10:02:35'),
(12, 12, 'breeder', 'Breeding: okkoma', NULL, 18, '123', NULL, NULL, NULL, 0.00, 0, 'Breeds: okkoma\nPhilosophy: danne na', 1, '2025-10-21 10:02:35', '2025-10-21 10:02:35');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_token` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'IPv4 or IPv6',
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Active user sessions';

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL COMMENT 'bcrypt hashed password',
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL COMMENT 'Path to profile picture',
  `email_verified` tinyint(1) DEFAULT 0,
  `email_verification_token` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1 COMMENT 'Account active status',
  `is_blocked` tinyint(1) DEFAULT 0 COMMENT 'Admin can block users',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Core user accounts table';

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `first_name`, `last_name`, `phone`, `address`, `avatar`, `email_verified`, `email_verification_token`, `is_active`, `is_blocked`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin@gmail.com', '$2y$10$BINhO08G1d0W.jnwQ/xHDeDz.B9uzRxfgo9.osphNDGYUPYlESdHi', 'System', 'Administrator', NULL, NULL, NULL, 1, NULL, 1, 0, '2025-10-22 10:45:09', '2025-10-21 06:59:16', '2025-10-22 10:45:09'),
(2, 'petowner@gmail.com', '$2y$10$BINhO08G1d0W.jnwQ/xHDeDz.B9uzRxfgo9.osphNDGYUPYlESdHi', 'John', 'Doe', '0771234567', NULL, NULL, 1, NULL, 1, 0, '2025-10-22 10:38:19', '2025-10-21 06:59:16', '2025-10-22 10:38:19'),
(3, 'vet@gmail.com', '$2y$10$BINhO08G1d0W.jnwQ/xHDeDz.B9uzRxfgo9.osphNDGYUPYlESdHi', 'Sarah', 'Johnson', '0777654321', NULL, NULL, 1, NULL, 1, 0, '2025-10-22 10:39:51', '2025-10-21 06:59:16', '2025-10-22 10:39:51'),
(4, 'trainer@gmail.com', '$2y$10$BINhO08G1d0W.jnwQ/xHDeDz.B9uzRxfgo9.osphNDGYUPYlESdHi', 'Tom', 'Trainer', '0771234567', '123 Trainer St, Colombo', NULL, 1, NULL, 1, 0, '2025-10-22 10:51:12', '2025-10-21 07:42:28', '2025-10-22 10:51:12'),
(5, 'sitter@gmail.com', '$2y$10$BINhO08G1d0W.jnwQ/xHDeDz.B9uzRxfgo9.osphNDGYUPYlESdHi', 'Sam', 'Sitter', '0772234567', '456 Sitter Ave, Kandy', NULL, 1, NULL, 1, 0, '2025-10-22 10:55:09', '2025-10-21 07:42:28', '2025-10-22 10:55:09'),
(6, 'breeder@gmail.com', '$2y$10$BINhO08G1d0W.jnwQ/xHDeDz.B9uzRxfgo9.osphNDGYUPYlESdHi', 'Bob', 'Breeder', '0773234567', '789 Breeder Rd, Galle', NULL, 1, NULL, 1, 0, '2025-10-22 10:56:37', '2025-10-21 07:42:28', '2025-10-22 10:56:37'),
(7, 'groomer@gmail.com', '$2y$10$BINhO08G1d0W.jnwQ/xHDeDz.B9uzRxfgo9.osphNDGYUPYlESdHi', 'Grace', 'Groomer', '0774234567', '321 Groomer Lane, Negombo', NULL, 1, NULL, 1, 0, '2025-10-22 10:57:55', '2025-10-21 07:42:28', '2025-10-22 10:57:55'),
(8, 'recep@gmail.com', '$2y$10$BINhO08G1d0W.jnwQ/xHDeDz.B9uzRxfgo9.osphNDGYUPYlESdHi', 'Rita', 'Receptionist', '0775234567', '654 Clinic St, Colombo 7', NULL, 1, NULL, 1, 0, '2025-10-22 11:03:06', '2025-10-21 07:42:28', '2025-10-22 11:03:06'),
(9, 'manager@gmail.com', '$2y$10$BINhO08G1d0W.jnwQ/xHDeDz.B9uzRxfgo9.osphNDGYUPYlESdHi', 'Mike', 'Manager', '0776234567', '888 Clinic Ave, Colombo', NULL, 1, NULL, 1, 0, '2025-10-22 10:46:04', '2025-10-21 08:17:00', '2025-10-22 10:46:04'),
(10, 'peterpoker@gmail.com', '$2y$10$oul5xyrLiqPgJdW1AfdYdu0hdvnu6BDzPSVtT7hacTk9W8v4Kwm5e', 'Hesara', 'Liyanage', '0775983002', '145/2/1', NULL, 1, NULL, 1, 0, '2025-10-21 09:19:38', '2025-10-21 09:18:56', '2025-10-21 09:19:38'),
(11, 'allinone@gmail.com', '$2y$10$B.T98na4LBqC/JOSsTVqPOP64wmqXUVCDk1fd5hxVczwC9HPDJGCq', 'Hesara', 'Liyanage', '0775983002', '145/2/1', NULL, 1, NULL, 1, 0, '2025-10-22 08:23:45', '2025-10-21 09:53:06', '2025-10-22 08:23:45'),
(12, 'amindasithummal@gmail.com', '$2y$10$1Rsugbt18Haag0Ctp9U91.OGY2.VCDiXnde4h5/Zd8yiDL27rjnhi', 'aminda', 'sithummal', '0701101519', 'addlsdksl', NULL, 1, NULL, 1, 0, '2025-10-21 10:03:11', '2025-10-21 10:02:35', '2025-10-21 10:03:11'),
(13, 'amindavet@gmail.com', '$2y$10$gGkycyLU.By6q4pB7oq8nOC45XJVRNvGyVY4XyGrBz9aDjWuUAsyO', 'Dihindu', 'Hesara', '0775983002', '145/2/1 Gonahena Kadawatha', NULL, 1, NULL, 1, 0, '2025-10-22 08:18:04', '2025-10-22 08:17:28', '2025-10-22 08:18:04'),
(14, 'clinica@gmail.com', '$2y$10$JBzMnwuUXSho5xGzWmrxc.PSQcsvs47JHls5kLK59Ql5jWSoDEOVS', 'Hesara', 'Liyanage', '0775983002', '', NULL, 1, NULL, 1, 0, NULL, '2025-10-22 08:52:57', '2025-10-22 08:52:57'),
(15, 'pokerpeter474@gmail.com', '$2y$10$.WzOwjbd/yTG2Pzh/OKyhOGBDBtfCOximkddp5p.Y4mF/04CPjc4u', 'Peter', 'Parker', '0775983002', '', NULL, 1, NULL, 1, 0, '2025-10-22 10:13:36', '2025-10-22 10:13:19', '2025-10-22 10:13:36');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0 COMMENT 'Users default landing role',
  `is_active` tinyint(1) DEFAULT 1 COMMENT 'Can temporarily disable a role',
  `verification_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `verification_notes` text DEFAULT NULL COMMENT 'Admin notes on approval/rejection',
  `verified_by` int(11) DEFAULT NULL COMMENT 'Admin user who verified',
  `verified_at` timestamp NULL DEFAULT NULL,
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='User to roles mapping with verification';

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `user_id`, `role_id`, `is_primary`, `is_active`, `verification_status`, `verification_notes`, `verified_by`, `verified_at`, `applied_at`) VALUES
(1, 1, 4, 1, 1, 'approved', NULL, NULL, '2025-10-21 06:59:16', '2025-10-21 06:59:16'),
(2, 2, 1, 1, 1, 'approved', NULL, NULL, '2025-10-21 06:59:16', '2025-10-21 06:59:16'),
(3, 3, 2, 1, 1, 'approved', NULL, NULL, '2025-10-21 06:59:16', '2025-10-21 06:59:16'),
(4, 4, 6, 1, 1, 'approved', NULL, NULL, NULL, '2025-10-21 07:42:28'),
(5, 5, 7, 1, 1, 'approved', NULL, NULL, NULL, '2025-10-21 07:42:28'),
(6, 6, 8, 1, 1, 'approved', NULL, NULL, NULL, '2025-10-21 07:42:28'),
(7, 7, 9, 1, 1, 'approved', NULL, NULL, NULL, '2025-10-21 07:42:28'),
(8, 8, 5, 1, 1, 'approved', NULL, NULL, NULL, '2025-10-21 07:42:28'),
(9, 9, 3, 1, 1, 'approved', NULL, NULL, NULL, '2025-10-21 08:17:00'),
(10, 10, 1, 1, 1, 'approved', NULL, NULL, NULL, '2025-10-21 09:18:56'),
(11, 10, 6, 0, 1, 'approved', NULL, NULL, NULL, '2025-10-21 09:18:56'),
(12, 10, 7, 0, 1, 'approved', NULL, NULL, NULL, '2025-10-21 09:18:56'),
(13, 11, 1, 1, 1, 'approved', NULL, NULL, NULL, '2025-10-21 09:53:06'),
(14, 11, 6, 0, 1, 'approved', NULL, NULL, NULL, '2025-10-21 09:53:06'),
(15, 11, 9, 0, 1, 'approved', NULL, NULL, NULL, '2025-10-21 09:53:06'),
(16, 11, 7, 0, 1, 'approved', NULL, NULL, NULL, '2025-10-21 09:53:06'),
(17, 11, 8, 0, 1, 'approved', NULL, NULL, NULL, '2025-10-21 09:53:06'),
(18, 12, 1, 1, 1, 'approved', NULL, NULL, NULL, '2025-10-21 10:02:35'),
(19, 12, 9, 0, 1, 'approved', NULL, NULL, NULL, '2025-10-21 10:02:35'),
(20, 12, 8, 0, 1, 'approved', NULL, NULL, NULL, '2025-10-21 10:02:35'),
(21, 13, 2, 1, 1, 'approved', NULL, NULL, NULL, '2025-10-22 08:17:28'),
(22, 14, 3, 1, 1, 'approved', NULL, NULL, NULL, '2025-10-22 08:52:57'),
(23, 15, 3, 1, 1, 'approved', NULL, NULL, NULL, '2025-10-22 10:13:19');

-- --------------------------------------------------------

--
-- Table structure for table `vet_profiles`
--

CREATE TABLE `vet_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `clinic_id` int(11) DEFAULT NULL,
  `license_number` varchar(100) NOT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `years_experience` int(11) DEFAULT 0,
  `education` text DEFAULT NULL,
  `consultation_fee` decimal(10,2) DEFAULT 0.00,
  `rating` decimal(3,2) DEFAULT 0.00 COMMENT 'Average rating 0.00-5.00',
  `total_reviews` int(11) DEFAULT 0,
  `bio` text DEFAULT NULL,
  `available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Veterinarian profiles';

--
-- Dumping data for table `vet_profiles`
--

INSERT INTO `vet_profiles` (`id`, `user_id`, `clinic_id`, `license_number`, `specialization`, `years_experience`, `education`, `consultation_fee`, `rating`, `total_reviews`, `bio`, `available`, `created_at`, `updated_at`) VALUES
(1, 3, 1, 'VET-LK-2020-001234', 'General Practice, Surgery', 8, NULL, 0.00, 0.00, 0, 'Experienced veterinarian specializing in small animals with a passion for preventive care.', 1, '2025-10-21 06:59:16', '2025-10-21 06:59:16'),
(2, 13, 1, 'DFSD4561863', 'Dental', 10, NULL, 0.00, 0.00, 0, NULL, 1, '2025-10-22 08:17:28', '2025-10-22 08:17:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `clinics`
--
ALTER TABLE `clinics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_district` (`district`),
  ADD KEY `idx_status` (`verification_status`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `clinic_manager_profiles`
--
ALTER TABLE `clinic_manager_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_clinic` (`clinic_id`);

--
-- Indexes for table `clinic_staff`
--
ALTER TABLE `clinic_staff`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_clinic_id` (`clinic_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email_ip` (`email`,`ip_address`),
  ADD KEY `idx_attempted` (`attempted_at`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reset_token` (`reset_token`),
  ADD KEY `idx_token` (`reset_token`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_expires` (`expires_at`);

--
-- Indexes for table `pets`
--
ALTER TABLE `pets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `pet_owner_profiles`
--
ALTER TABLE `pet_owner_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `idx_user` (`user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `idx_display_order` (`display_order`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_name` (`role_name`),
  ADD KEY `idx_role_name` (`role_name`);

--
-- Indexes for table `role_verification_documents`
--
ALTER TABLE `role_verification_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_role` (`user_role_id`);

--
-- Indexes for table `sell_pet_listings`
--
ALTER TABLE `sell_pet_listings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_species` (`species`);

--
-- Indexes for table `sell_pet_listing_badges`
--
ALTER TABLE `sell_pet_listing_badges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_listing_id` (`listing_id`);

--
-- Indexes for table `sell_pet_listing_images`
--
ALTER TABLE `sell_pet_listing_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_listing_id` (`listing_id`);

--
-- Indexes for table `service_provider_profiles`
--
ALTER TABLE `service_provider_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_role_type` (`user_id`,`role_type`),
  ADD KEY `idx_role_type` (`role_type`),
  ADD KEY `idx_area` (`service_area`),
  ADD KEY `idx_user` (`user_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_token` (`session_token`),
  ADD KEY `idx_user_session` (`user_id`),
  ADD KEY `idx_token` (`session_token`),
  ADD KEY `idx_expires` (`expires_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_role` (`user_id`,`role_id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `verified_by` (`verified_by`),
  ADD KEY `idx_user_role` (`user_id`,`role_id`),
  ADD KEY `idx_verification` (`verification_status`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `vet_profiles`
--
ALTER TABLE `vet_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `license_number` (`license_number`),
  ADD KEY `idx_clinic` (`clinic_id`),
  ADD KEY `idx_available` (`available`),
  ADD KEY `idx_user` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=145;

--
-- AUTO_INCREMENT for table `clinics`
--
ALTER TABLE `clinics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `clinic_manager_profiles`
--
ALTER TABLE `clinic_manager_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `clinic_staff`
--
ALTER TABLE `clinic_staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pets`
--
ALTER TABLE `pets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pet_owner_profiles`
--
ALTER TABLE `pet_owner_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `role_verification_documents`
--
ALTER TABLE `role_verification_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sell_pet_listings`
--
ALTER TABLE `sell_pet_listings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sell_pet_listing_badges`
--
ALTER TABLE `sell_pet_listing_badges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sell_pet_listing_images`
--
ALTER TABLE `sell_pet_listing_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `service_provider_profiles`
--
ALTER TABLE `service_provider_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `vet_profiles`
--
ALTER TABLE `vet_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `clinic_manager_profiles`
--
ALTER TABLE `clinic_manager_profiles`
  ADD CONSTRAINT `clinic_manager_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `clinic_manager_profiles_ibfk_2` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `clinic_staff`
--
ALTER TABLE `clinic_staff`
  ADD CONSTRAINT `clinic_staff_ibfk_1` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `clinic_staff_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pets`
--
ALTER TABLE `pets`
  ADD CONSTRAINT `pets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pet_owner_profiles`
--
ALTER TABLE `pet_owner_profiles`
  ADD CONSTRAINT `pet_owner_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_verification_documents`
--
ALTER TABLE `role_verification_documents`
  ADD CONSTRAINT `role_verification_documents_ibfk_1` FOREIGN KEY (`user_role_id`) REFERENCES `user_roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sell_pet_listings`
--
ALTER TABLE `sell_pet_listings`
  ADD CONSTRAINT `sell_pet_listings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sell_pet_listing_badges`
--
ALTER TABLE `sell_pet_listing_badges`
  ADD CONSTRAINT `sell_pet_listing_badges_ibfk_1` FOREIGN KEY (`listing_id`) REFERENCES `sell_pet_listings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sell_pet_listing_images`
--
ALTER TABLE `sell_pet_listing_images`
  ADD CONSTRAINT `sell_pet_listing_images_ibfk_1` FOREIGN KEY (`listing_id`) REFERENCES `sell_pet_listings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `service_provider_profiles`
--
ALTER TABLE `service_provider_profiles`
  ADD CONSTRAINT `service_provider_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_ibfk_3` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `vet_profiles`
--
ALTER TABLE `vet_profiles`
  ADD CONSTRAINT `vet_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vet_profiles_ibfk_2` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
