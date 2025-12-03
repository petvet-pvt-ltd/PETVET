-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: btfrleeonbksuwewbmxg-mysql.services.clever-cloud.com    Database: btfrleeonbksuwewbmxg
-- ------------------------------------------------------
-- Server version	8.0.22-13

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `appointments`
--

DROP TABLE IF EXISTS `appointments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appointments`
--

LOCK TABLES `appointments` WRITE;
/*!40000 ALTER TABLE `appointments` DISABLE KEYS */;
INSERT INTO `appointments` VALUES (1,5,2,1,3,'routine','Test appointment - manual creation','2025-11-27','10:00:00',20,'declined','','2025-11-26 06:40:42','2025-11-26 06:41:09',NULL,NULL),(2,5,2,1,3,'routine','Test appointment - manual creation','2025-11-27','10:00:00',20,'declined','','2025-11-26 06:41:37','2025-11-26 07:27:09',NULL,NULL),(3,6,2,1,NULL,'vaccination','adadawdawdawda','2025-11-27','17:19:00',20,'declined','','2025-11-26 06:43:29','2025-11-26 07:27:02',NULL,NULL),(4,6,2,1,NULL,'dental','dadaddadad','2025-11-27','19:32:00',20,'declined','','2025-11-26 07:57:13','2025-11-26 13:23:50',NULL,NULL),(5,6,2,1,NULL,'dental','dentalkdadoakodka','2025-11-27','18:56:00',20,'declined','','2025-11-26 13:26:19','2025-11-26 13:30:04',NULL,NULL),(6,6,2,1,NULL,'routine','jnadwajkdkldkjk','2025-11-27','23:03:00',20,'declined','','2025-11-26 13:30:49','2025-11-26 13:37:11',NULL,NULL),(7,6,2,1,NULL,'dental','dawdawdawd','2025-11-27','10:11:00',20,'declined','','2025-11-26 13:38:36','2025-11-26 13:41:16',NULL,NULL),(8,6,2,1,NULL,'vaccination','dadawdad','2025-11-27','22:14:00',20,'declined','','2025-11-26 13:42:07','2025-11-26 13:45:21',NULL,NULL),(9,6,2,1,NULL,'routine','dawdadawd','2025-11-27','19:21:00',20,'cancelled',NULL,'2025-11-26 13:47:12','2025-11-26 16:10:25',17,'2025-11-26 13:48:21'),(10,6,2,1,NULL,'routine','dadadfrgr','2025-11-27','23:32:00',20,'cancelled',NULL,'2025-11-26 13:58:35','2025-11-26 16:10:11',17,'2025-11-26 13:59:30'),(11,6,2,1,NULL,'vaccination','dawdamdawudda','2025-11-27','13:01:00',20,'cancelled',NULL,'2025-11-26 16:32:01','2025-11-26 16:33:02',17,'2025-11-26 16:32:42'),(12,6,2,1,NULL,'illness','Not eating lately','2025-11-29','12:03:00',20,'cancelled',NULL,'2025-11-28 14:31:07','2025-11-28 15:12:11',17,'2025-11-28 14:49:10'),(13,25,2,1,NULL,'surgery','adawdawda','2025-12-13','18:47:00',20,'pending',NULL,'2025-11-29 10:15:32','2025-11-29 10:15:32',NULL,NULL);
/*!40000 ALTER TABLE `appointments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=355 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Audit trail for security monitoring';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (1,2,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 07:15:33'),(2,2,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 07:22:16'),(3,2,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 07:25:08'),(4,2,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 07:43:12'),(5,2,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 07:53:50'),(6,2,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 07:54:41'),(7,4,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 07:54:53'),(8,4,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 07:58:34'),(9,5,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 07:58:44'),(10,5,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 07:58:47'),(11,5,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 07:59:43'),(12,5,'logout','::1','Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/17.5 Mobile/15A5370a Safari/602.1','[]','2025-10-21 08:01:26'),(13,6,'login','::1','Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/17.5 Mobile/15A5370a Safari/602.1','[]','2025-10-21 08:02:22'),(14,6,'logout','::1','Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/17.5 Mobile/15A5370a Safari/602.1','[]','2025-10-21 08:03:52'),(15,2,'login','::1','Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/17.5 Mobile/15A5370a Safari/602.1','[]','2025-10-21 08:03:59'),(16,2,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 08:10:52'),(17,2,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 08:11:39'),(18,2,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 08:13:32'),(19,6,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 08:13:53'),(20,4,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-21 08:26:01'),(21,4,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-21 08:26:53'),(22,8,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-21 08:27:15'),(23,8,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-21 08:28:02'),(24,9,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-21 08:28:10'),(25,9,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-21 08:28:22'),(26,5,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-21 08:28:36'),(27,5,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-21 08:28:40'),(28,6,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-21 08:28:56'),(29,6,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-21 08:29:09'),(30,7,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-21 08:29:22'),(31,7,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-21 08:29:33'),(32,3,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 08:32:17'),(33,3,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 08:32:26'),(34,1,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 08:32:39'),(35,1,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 08:32:42'),(36,2,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-21 08:32:48'),(37,2,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 08:41:20'),(38,4,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 08:44:09'),(39,4,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 08:44:24'),(40,4,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 08:44:35'),(41,4,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 08:46:02'),(42,6,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 08:46:12'),(43,1,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 08:46:26'),(44,1,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 08:56:20'),(45,2,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 08:58:56'),(46,2,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 08:59:34'),(47,2,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 09:09:26'),(48,2,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 09:09:30'),(49,2,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 09:12:03'),(50,8,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 09:12:23'),(51,8,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 09:13:07'),(52,5,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 09:13:20'),(53,5,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 09:14:01'),(54,3,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 09:14:10'),(55,10,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 09:19:38'),(56,10,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','{\"new_role\":\"trainer\"}','2025-10-21 09:19:48'),(57,10,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','{\"new_role\":\"sitter\"}','2025-10-21 09:26:38'),(58,10,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','{\"new_role\":\"trainer\"}','2025-10-21 09:33:37'),(59,10,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','{\"new_role\":\"trainer\"}','2025-10-21 09:33:46'),(60,10,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','{\"new_role\":\"sitter\"}','2025-10-21 09:33:52'),(61,10,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','{\"new_role\":\"pet_owner\"}','2025-10-21 09:33:59'),(62,10,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','{\"new_role\":\"trainer\"}','2025-10-21 09:34:10'),(63,10,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','{\"new_role\":\"pet_owner\"}','2025-10-21 09:34:16'),(64,10,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','{\"new_role\":\"trainer\"}','2025-10-21 09:34:24'),(65,10,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','{\"new_role\":\"pet_owner\"}','2025-10-21 09:34:30'),(66,3,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 09:35:11'),(67,10,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 09:45:21'),(68,11,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 09:53:52'),(69,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','{\"new_role\":\"trainer\"}','2025-10-21 09:54:00'),(70,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','{\"new_role\":\"sitter\"}','2025-10-21 09:54:05'),(71,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','{\"new_role\":\"breeder\"}','2025-10-21 09:54:11'),(72,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','{\"new_role\":\"pet_owner\"}','2025-10-21 09:54:38'),(73,11,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 09:55:05'),(74,11,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 09:59:48'),(75,12,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 10:03:11'),(76,12,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','{\"new_role\":\"breeder\"}','2025-10-21 10:03:28'),(77,12,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','{\"new_role\":\"groomer\"}','2025-10-21 10:03:43'),(78,12,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 10:04:08'),(79,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','{\"new_role\":\"trainer\"}','2025-10-21 10:06:07'),(80,11,'logout','::1','Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/17.5 Mobile/15A5370a Safari/602.1','[]','2025-10-21 10:41:09'),(81,4,'login','::1','Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/17.5 Mobile/15A5370a Safari/602.1','[]','2025-10-21 10:41:17'),(82,4,'logout','::1','Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/17.5 Mobile/15A5370a Safari/602.1','[]','2025-10-21 12:41:13'),(83,9,'login','::1','Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/17.5 Mobile/15A5370a Safari/602.1','[]','2025-10-21 12:41:22'),(84,9,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 13:23:03'),(85,9,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 13:23:25'),(86,2,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-21 14:04:56'),(87,2,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-21 14:05:21'),(88,2,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-21 14:07:37'),(89,3,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 19:05:07'),(90,2,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 19:05:21'),(91,2,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 19:38:21'),(92,9,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 19:38:58'),(93,9,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 19:40:35'),(94,2,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 19:49:58'),(95,2,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 20:18:15'),(96,1,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-21 20:18:39'),(97,1,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 04:25:21'),(98,2,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 04:25:40'),(99,2,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 04:26:02'),(100,9,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 04:26:12'),(101,9,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 04:28:19'),(102,2,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 04:28:25'),(103,2,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 04:37:38'),(104,9,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 04:37:54'),(105,9,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 04:38:42'),(106,2,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 04:38:58'),(107,2,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 06:27:16'),(108,2,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 07:11:50'),(109,2,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 07:29:39'),(110,13,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 08:18:04'),(111,13,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 08:18:56'),(112,11,'login','::1','Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/17.5 Mobile/15A5370a Safari/602.1','[]','2025-10-22 08:23:45'),(113,11,'logout','::1','Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/17.5 Mobile/15A5370a Safari/602.1','[]','2025-10-22 08:24:29'),(114,15,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 10:13:36'),(115,15,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 10:17:24'),(116,9,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 10:25:02'),(117,1,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 10:28:06'),(118,9,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 10:29:22'),(119,1,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 10:29:42'),(120,1,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 10:33:41'),(121,2,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 10:34:46'),(122,9,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 10:37:35'),(123,2,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 10:38:05'),(124,2,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 10:38:19'),(125,2,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 10:39:06'),(126,3,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 10:39:51'),(127,3,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 10:44:16'),(128,2,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 10:44:52'),(129,1,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 10:45:09'),(130,9,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 10:46:04'),(131,9,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 10:49:43'),(132,8,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 10:50:09'),(133,8,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 10:50:46'),(134,4,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 10:51:12'),(135,4,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 10:54:26'),(136,5,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 10:55:09'),(137,5,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 10:56:18'),(138,6,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 10:56:37'),(139,6,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 10:57:30'),(140,7,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 10:57:55'),(141,7,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 10:59:06'),(142,2,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 11:02:06'),(143,8,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 11:03:06'),(144,8,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 11:03:42'),(145,1,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 12:47:06'),(146,1,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 13:03:02'),(147,1,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 13:03:24'),(148,2,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 13:04:57'),(149,1,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 13:07:24'),(150,1,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 13:23:05'),(151,1,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 14:00:03'),(152,3,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 14:00:53'),(153,3,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 14:01:28'),(154,2,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 14:01:45'),(155,2,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 14:08:33'),(156,11,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 14:14:03'),(157,11,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 14:19:25'),(158,9,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 14:39:38'),(159,9,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 14:40:38'),(160,9,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 OPR/122.0.0.0','[]','2025-10-22 14:41:00'),(161,9,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 14:43:24'),(162,2,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 14:51:07'),(163,9,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 14:51:50'),(164,2,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 14:52:08'),(165,2,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 14:55:06'),(166,9,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 OPR/122.0.0.0','[]','2025-10-22 15:01:27'),(167,2,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 15:12:50'),(168,9,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 15:12:58'),(169,9,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 15:17:25'),(170,11,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 15:17:40'),(171,2,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 15:22:03'),(172,1,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 15:22:25'),(173,3,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 15:22:40'),(174,3,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 15:25:46'),(175,2,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 15:28:35'),(176,11,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 15:28:44'),(177,1,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 15:29:03'),(178,3,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 15:31:21'),(179,3,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 15:32:51'),(180,11,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 15:33:03'),(181,11,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 15:33:08'),(182,3,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 15:34:09'),(183,3,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 15:35:51'),(184,9,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 15:36:08'),(185,1,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 15:36:36'),(186,2,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 15:36:46'),(187,11,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 15:37:52'),(188,9,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 15:38:06'),(189,9,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 15:39:10'),(190,3,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 15:39:17'),(191,9,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 15:40:35'),(192,8,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 15:40:51'),(193,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"new_role\":\"sitter\"}','2025-10-22 15:41:22'),(194,11,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 15:41:40'),(195,3,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 15:43:53'),(196,2,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 15:43:54'),(197,9,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 15:44:03'),(198,1,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 15:44:08'),(199,1,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 15:44:31'),(200,2,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 15:44:40'),(201,8,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 15:44:46'),(202,9,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 15:44:53'),(203,2,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 15:45:20'),(204,1,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 15:45:29'),(205,9,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 15:53:18'),(206,8,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 15:53:23'),(207,8,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 15:54:19'),(208,11,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 16:09:42'),(209,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"new_role\":\"groomer\"}','2025-10-22 16:14:13'),(210,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"new_role\":\"pet_owner\"}','2025-10-22 16:14:24'),(211,9,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 16:18:19'),(212,11,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 16:20:28'),(213,11,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 16:22:07'),(214,11,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 16:22:49'),(215,11,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 16:23:46'),(216,2,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 16:26:22'),(217,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"new_role\":\"trainer\"}','2025-10-22 16:27:09'),(218,1,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 16:27:13'),(219,11,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 16:27:17'),(220,11,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 16:27:29'),(221,2,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 16:27:31'),(222,2,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 16:30:15'),(223,1,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 16:30:27'),(224,1,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 16:31:11'),(225,2,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 16:31:21'),(226,2,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 16:31:47'),(227,1,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 16:32:02'),(228,11,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 16:32:17'),(229,9,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 16:37:58'),(230,8,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 16:38:05'),(231,8,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 16:39:30'),(232,1,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 16:39:36'),(233,11,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 16:41:00'),(234,9,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 16:42:27'),(235,9,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 16:43:11'),(236,9,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 16:45:46'),(237,3,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 16:46:07'),(238,11,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 16:50:20'),(239,11,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 16:51:07'),(240,1,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 16:59:42'),(241,11,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 17:00:12'),(242,2,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 17:00:29'),(243,9,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 17:00:52'),(244,8,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 17:01:10'),(245,11,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 17:04:25'),(246,3,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 17:05:24'),(247,9,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 17:05:44'),(248,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"new_role\":\"pet_owner\"}','2025-10-22 17:06:18'),(249,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"new_role\":\"trainer\"}','2025-10-22 17:06:26'),(250,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"new_role\":\"pet_owner\"}','2025-10-22 17:06:37'),(251,1,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 17:07:19'),(252,2,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 17:07:34'),(253,2,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 17:09:58'),(254,1,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 17:10:10'),(255,1,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 17:11:09'),(256,2,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 17:11:18'),(257,2,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 17:11:58'),(258,1,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 17:12:08'),(259,1,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 17:13:27'),(260,1,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 17:21:31'),(261,9,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 17:21:45'),(262,9,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 17:22:36'),(263,3,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 17:23:25'),(264,2,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 OPR/122.0.0.0','[]','2025-10-22 17:26:53'),(265,1,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 17:27:04'),(266,2,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 OPR/122.0.0.0','[]','2025-10-22 17:36:38'),(267,11,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 OPR/122.0.0.0','[]','2025-10-22 17:36:49'),(268,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 OPR/122.0.0.0','{\"new_role\":\"trainer\"}','2025-10-22 17:36:53'),(269,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 OPR/122.0.0.0','{\"new_role\":\"groomer\"}','2025-10-22 17:38:12'),(270,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 OPR/122.0.0.0','{\"new_role\":\"sitter\"}','2025-10-22 17:38:15'),(271,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 OPR/122.0.0.0','{\"new_role\":\"breeder\"}','2025-10-22 17:38:19'),(272,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 OPR/122.0.0.0','{\"new_role\":\"pet_owner\"}','2025-10-22 17:38:24'),(273,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 OPR/122.0.0.0','{\"new_role\":\"trainer\"}','2025-10-22 17:39:27'),(274,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 OPR/122.0.0.0','{\"new_role\":\"sitter\"}','2025-10-22 17:40:26'),(275,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 OPR/122.0.0.0','{\"new_role\":\"breeder\"}','2025-10-22 17:40:51'),(276,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 OPR/122.0.0.0','{\"new_role\":\"groomer\"}','2025-10-22 17:41:54'),(277,11,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 OPR/122.0.0.0','[]','2025-10-22 17:45:39'),(278,11,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 OPR/122.0.0.0','[]','2025-10-22 17:45:45'),(279,1,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 17:50:08'),(280,2,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 17:50:20'),(281,11,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 OPR/122.0.0.0','[]','2025-10-22 17:54:53'),(282,11,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 OPR/122.0.0.0','[]','2025-10-22 17:54:59'),(283,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 OPR/122.0.0.0','{\"new_role\":\"breeder\"}','2025-10-22 17:57:38'),(284,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 OPR/122.0.0.0','{\"new_role\":\"pet_owner\"}','2025-10-22 17:57:54'),(285,11,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 18:00:15'),(286,11,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 18:01:47'),(287,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"new_role\":\"groomer\"}','2025-10-22 18:04:57'),(288,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"new_role\":\"groomer\"}','2025-10-22 18:05:15'),(289,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"new_role\":\"pet_owner\"}','2025-10-22 18:05:21'),(290,11,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 18:05:55'),(291,11,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 18:06:54'),(292,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"new_role\":\"groomer\"}','2025-10-22 18:10:31'),(293,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"new_role\":\"pet_owner\"}','2025-10-22 18:10:43'),(294,11,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 OPR/122.0.0.0','[]','2025-10-22 18:12:07'),(295,11,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 OPR/122.0.0.0','[]','2025-10-22 18:13:24'),(296,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 OPR/122.0.0.0','{\"new_role\":\"trainer\"}','2025-10-22 18:16:51'),(297,11,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 OPR/122.0.0.0','[]','2025-10-22 18:17:18'),(298,8,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 18:17:33'),(299,11,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 18:18:30'),(300,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','{\"new_role\":\"sitter\"}','2025-10-22 18:20:49'),(301,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','{\"new_role\":\"pet_owner\"}','2025-10-22 18:20:56'),(302,11,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 18:28:44'),(303,1,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-22 18:28:55'),(304,2,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 18:29:26'),(305,1,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 18:29:38'),(306,9,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 18:32:46'),(307,11,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-22 18:35:50'),(308,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"new_role\":\"trainer\"}','2025-10-22 18:38:14'),(309,1,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-23 04:06:00'),(310,1,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-23 04:06:59'),(311,9,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-23 04:08:17'),(312,1,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-23 04:08:40'),(313,1,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-23 04:08:46'),(314,3,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-23 04:09:44'),(315,11,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-23 04:13:11'),(316,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"new_role\":\"breeder\"}','2025-10-23 04:15:27'),(317,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"new_role\":\"breeder\"}','2025-10-23 04:15:35'),(318,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"new_role\":\"breeder\"}','2025-10-23 04:15:44'),(319,9,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-23 04:19:45'),(320,8,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-23 04:19:58'),(321,8,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-23 04:20:35'),(322,9,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-23 04:20:44'),(323,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"new_role\":\"trainer\"}','2025-10-23 04:26:20'),(324,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"new_role\":\"pet_owner\"}','2025-10-23 04:27:04'),(325,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"new_role\":\"trainer\"}','2025-10-23 04:27:15'),(326,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"new_role\":\"pet_owner\"}','2025-10-23 04:29:09'),(327,8,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 OPR/122.0.0.0','[]','2025-10-23 04:29:46'),(328,11,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-23 04:29:56'),(329,11,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-23 04:34:48'),(330,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"new_role\":\"trainer\"}','2025-10-23 04:36:55'),(331,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"new_role\":\"trainer\"}','2025-10-23 04:42:56'),(332,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"new_role\":\"breeder\"}','2025-10-23 04:43:25'),(333,11,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-23 05:04:21'),(334,11,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-23 05:10:25'),(335,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"new_role\":\"trainer\"}','2025-10-23 05:12:28'),(336,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"new_role\":\"pet_owner\"}','2025-10-23 05:20:44'),(337,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"new_role\":\"trainer\"}','2025-10-23 05:20:48'),(338,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"new_role\":\"sitter\"}','2025-10-23 05:27:21'),(339,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"new_role\":\"breeder\"}','2025-10-23 05:28:02'),(340,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"new_role\":\"groomer\"}','2025-10-23 05:28:32'),(341,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"new_role\":\"pet_owner\"}','2025-10-23 05:29:10'),(342,11,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-23 05:37:44'),(343,11,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','[]','2025-10-23 06:10:04'),(344,11,'role_switch','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','{\"new_role\":\"trainer\"}','2025-10-23 06:15:10'),(345,1,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-24 06:19:28'),(346,3,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-24 06:58:17'),(347,3,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-24 06:59:10'),(348,9,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','[]','2025-10-24 06:59:24'),(349,8,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','[]','2025-11-26 06:12:53'),(350,8,'logout','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','[]','2025-11-26 06:27:54'),(351,17,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','[]','2025-11-26 06:33:06'),(352,17,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','[]','2025-11-26 13:23:28'),(353,17,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','[]','2025-11-28 14:30:04'),(354,17,'login','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','[]','2025-11-29 10:05:09');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clinic_manager_profiles`
--

DROP TABLE IF EXISTS `clinic_manager_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clinic_manager_profiles`
--

LOCK TABLES `clinic_manager_profiles` WRITE;
/*!40000 ALTER TABLE `clinic_manager_profiles` DISABLE KEYS */;
INSERT INTO `clinic_manager_profiles` VALUES (1,9,1,'Clinic Manager','2025-10-21 08:17:00','2025-10-21 08:17:00'),(2,14,2,'Manager','2025-10-22 08:52:57','2025-10-22 08:52:57'),(3,15,3,'Manager','2025-10-22 10:13:19','2025-10-22 10:13:19');
/*!40000 ALTER TABLE `clinic_manager_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clinic_staff`
--

DROP TABLE IF EXISTS `clinic_staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Clinic staff members management';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clinic_staff`
--

LOCK TABLES `clinic_staff` WRITE;
/*!40000 ALTER TABLE `clinic_staff` DISABLE KEYS */;
INSERT INTO `clinic_staff` VALUES (1,1,NULL,'Anushka Perera','Veterinary Assistant','anushka.assist@petvet.lk','+94 71 234 5678','Active',NULL,'2025-10-22 10:20:35','2025-10-22 10:20:35'),(3,1,NULL,'Kavinda Fernando','Veterinary Assistant','kavinda.assist@petvet.lk','+94 77 555 1234','Active',NULL,'2025-10-22 10:20:35','2025-10-22 10:20:35'),(6,1,NULL,'MAlan','Veterinary Assistant','tharindu.front@petvet.lk','0771234567','Inactive',NULL,'2025-10-22 10:20:35','2025-10-23 05:19:33'),(20,1,17,'Jane Smith','Receptionist','receptionist@petvet.com','0771234567','Active',NULL,'2025-11-26 06:25:52','2025-11-26 06:25:52'),(21,1,3,'Sarah Johnson','vet','sarah.johnson@petvet.com','0771234567','Active',NULL,'2025-11-29 10:41:41','2025-11-29 10:41:41'),(22,2,13,'Dihindu Hesara','vet','dihindu.hesara@petvet.com','0779876543','Active',NULL,'2025-11-29 10:41:51','2025-11-29 10:41:51'),(24,1,18,'Michael Chen','vet','michael.chen@petvet.com','0771234568','Active',NULL,'2025-11-29 10:51:44','2025-11-29 10:51:44'),(25,1,19,'Emily Rodriguez','vet','emily.rodriguez@petvet.com','0771234569','Active',NULL,'2025-11-29 10:51:44','2025-11-29 10:51:44'),(26,1,20,'James Wilson','vet','james.wilson@petvet.com','0771234570','Active',NULL,'2025-11-29 10:51:44','2025-11-29 10:51:44'),(27,2,21,'Priya Perera','vet','priya.perera@petvet.com','0771234571','Active',NULL,'2025-11-29 10:51:52','2025-11-29 10:51:52'),(28,2,22,'Nuwan Silva','vet','nuwan.silva@petvet.com','0771234572','Active',NULL,'2025-11-29 10:51:52','2025-11-29 10:51:52'),(29,2,23,'Anjali Fernando','vet','anjali.fernando@petvet.com','0771234573','Active',NULL,'2025-11-29 10:51:52','2025-11-29 10:51:52'),(30,3,24,'Rajesh Kumar','vet','rajesh.kumar@petvet.com','0771234574','Active',NULL,'2025-11-29 10:55:44','2025-11-29 10:55:44'),(31,3,25,'Lisa Thompson','vet','lisa.thompson@petvet.com','0771234575','Active',NULL,'2025-11-29 10:55:44','2025-11-29 10:55:44'),(32,3,26,'David Lee','vet','david.lee@petvet.com','0771234576','Active',NULL,'2025-11-29 10:55:44','2025-11-29 10:55:44');
/*!40000 ALTER TABLE `clinic_staff` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clinics`
--

DROP TABLE IF EXISTS `clinics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clinics` (
  `id` int NOT NULL AUTO_INCREMENT,
  `clinic_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `clinic_address` text COLLATE utf8mb4_unicode_ci NOT NULL,
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clinics`
--

LOCK TABLES `clinics` WRITE;
/*!40000 ALTER TABLE `clinics` DISABLE KEYS */;
INSERT INTO `clinics` VALUES (1,'Happy Paws Veterinary Clinic','123 Main Street, Colombo','Colombo','Colombo 07','0112345678','info@happypaws.lk',NULL,NULL,'approved',1,'2025-10-21 06:59:16','2025-10-21 06:59:16'),(2,'Peter PETVET','145/2/1','Anuradhapura',NULL,'0775983002','allinone@gmail.com',NULL,NULL,'approved',1,'2025-10-22 08:52:57','2025-10-22 08:52:57'),(3,'Pet Bros','145/2/1','Badulla',NULL,'0775983002','gklnkler@gmail.com',NULL,NULL,'approved',1,'2025-10-22 10:13:19','2025-10-22 10:13:19');
/*!40000 ALTER TABLE `clinics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_attempts`
--

DROP TABLE IF EXISTS `login_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login_attempts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `success` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_email_ip` (`email`,`ip_address`),
  KEY `idx_attempted` (`attempted_at`)
) ENGINE=InnoDB AUTO_INCREMENT=198 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Track login attempts for security';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_attempts`
--

LOCK TABLES `login_attempts` WRITE;
/*!40000 ALTER TABLE `login_attempts` DISABLE KEYS */;
INSERT INTO `login_attempts` VALUES (196,'receptionist@petvet.com','::1','2025-11-28 14:30:03',1),(197,'receptionist@petvet.com','::1','2025-11-29 10:05:08',1);
/*!40000 ALTER TABLE `login_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pet_owner_profiles`
--

DROP TABLE IF EXISTS `pet_owner_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pet_owner_profiles`
--

LOCK TABLES `pet_owner_profiles` WRITE;
/*!40000 ALTER TABLE `pet_owner_profiles` DISABLE KEYS */;
INSERT INTO `pet_owner_profiles` VALUES (1,10,NULL,NULL,NULL,NULL,'2025-10-21 09:18:56','2025-10-21 09:18:56'),(2,11,NULL,NULL,NULL,NULL,'2025-10-21 09:53:06','2025-10-21 09:53:06'),(3,12,NULL,NULL,NULL,NULL,'2025-10-21 10:02:35','2025-10-21 10:02:35'),(4,16,NULL,NULL,NULL,NULL,'2025-10-22 16:09:16','2025-10-22 16:09:16');
/*!40000 ALTER TABLE `pet_owner_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pets`
--

DROP TABLE IF EXISTS `pets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pets`
--

LOCK TABLES `pets` WRITE;
/*!40000 ALTER TABLE `pets` DISABLE KEYS */;
INSERT INTO `pets` VALUES (5,2,'Tuffey','Dog','Golden Retriever','Male','2021-01-22',30.00,'Gold color','None','Friendly & Playful','/PETVET/public/images/pets/pet_2_1761138857_68f8d8a9b9cfb.jpg',1,'2025-10-22 13:14:17','2025-10-22 13:14:17'),(6,2,'Kitty','Cat','American Curl','Female','2023-06-27',8.00,'Black, Gray & White','Allergic to Prawns','Lazy & Eats a lot','/PETVET/public/images/pets/pet_2_1761139151_68f8d9cfeefbe.jpg',1,'2025-10-22 13:19:11','2025-10-22 13:19:11'),(12,11,'Rocky','Dog','Rotteriller','Female','2021-02-17',25.00,'Black. Brown and Orange','Allergy to Humans','Very Aggressive against people','/PETVET/public/images/pets/pet_11_1761155352_68f91918afef0.jpg',1,'2025-10-22 17:49:12','2025-10-22 17:49:12'),(13,11,'Buddy','Dog','Poodle','Male','2019-02-13',18.00,'White','None','Playful, Friendly','/PETVET/public/images/pets/pet_11_1761155595_68f91a0b97a67.jpg',1,'2025-10-22 17:53:15','2025-10-22 17:53:15'),(25,2,'Peter','Turtle','','Male','2012-06-12',1.00,'black, green','','','/PETVET/public/images/pets/pet_2_1764411174_692ac726760a0.jpg',1,'2025-11-29 10:12:55','2025-11-29 10:12:55');
/*!40000 ALTER TABLE `pets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_images`
--

DROP TABLE IF EXISTS `product_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_images`
--

LOCK TABLES `product_images` WRITE;
/*!40000 ALTER TABLE `product_images` DISABLE KEYS */;
INSERT INTO `product_images` VALUES (1,1,'/PETVET/views/shared/images/fproduct1.png',0,'2025-10-21 13:42:05'),(2,2,'/PETVET/views/shared/images/fproduct2.png',0,'2025-10-21 13:42:05'),(3,3,'/PETVET/views/shared/images/fproduct3.png',0,'2025-10-21 13:42:05'),(4,4,'/PETVET/views/shared/images/fproduct4.png',0,'2025-10-21 13:42:05');
/*!40000 ALTER TABLE `product_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'Denta Fun Veggie Jaw Bone','A healthy, delicious treat for your dog. Made from natural ingredients to support dental health while satisfying chewing needs. Composition sweet potato meal, pea starch, vegetable by-products, minerals, yeast, cellulose, oils and fats, rosemary | gluten-free formula | vegetarian | no added sugar',500.00,'food','/PETVET/views/shared/images/fproduct1.png',25,'PetVet Official Store',340,1,'2025-10-21 12:48:40','2025-10-21 12:48:40'),(2,'Trixie Litter Scoop','High-quality litter scoop made from durable materials. Perfect for easy and hygienic litter box maintenance. Features comfortable grip handle and efficient scooping design.',900.00,'litter','/PETVET/views/shared/images/fproduct2.png',10,'PetVet Store',185,1,'2025-10-21 12:48:40','2025-10-22 04:38:14'),(3,'Dog Toy Tug Rope','Interactive rope toy perfect for playing tug-of-war with your dog. Made from durable cotton fibers that help clean teeth during play. Great for bonding and exercise.',2100.00,'toys','/PETVET/views/shared/images/fproduct3.png',8,'PlayTime Pets',95,1,'2025-10-21 12:48:40','2025-10-21 12:48:40'),(4,'Trixie Aloe Vera Shampoo','Gentle pet shampoo enriched with Aloe Vera for sensitive skin. Cleanses thoroughly while moisturizing and soothing your pet\'s coat. Suitable for regular use.',1900.00,'grooming','/PETVET/views/shared/images/fproduct4.png',10,'PetVet Store',220,1,'2025-10-21 12:48:40','2025-10-23 04:19:04');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_verification_documents`
--

DROP TABLE IF EXISTS `role_verification_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_verification_documents`
--

LOCK TABLES `role_verification_documents` WRITE;
/*!40000 ALTER TABLE `role_verification_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_verification_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'pet_owner','Pet Owner','Regular pet owner who can book appointments and manage pets',0,'2025-10-21 06:59:16'),(2,'vet','Veterinarian','Licensed veterinarian providing medical services',1,'2025-10-21 06:59:16'),(3,'clinic_manager','Clinic Manager','Manages clinic operations and staff',1,'2025-10-21 06:59:16'),(4,'admin','Administrator','System administrator with full access',0,'2025-10-21 06:59:16'),(5,'receptionist','Receptionist','Front desk staff managing appointments',0,'2025-10-21 06:59:16'),(6,'trainer','Pet Trainer','Professional pet trainer offering training services',1,'2025-10-21 06:59:16'),(7,'sitter','Pet Sitter','Professional pet sitter offering sitting services',1,'2025-10-21 06:59:16'),(8,'breeder','Pet Breeder','Professional breeder managing breeding operations',1,'2025-10-21 06:59:16'),(9,'groomer','Pet Groomer','Professional groomer offering grooming services',1,'2025-10-21 06:59:16');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sell_pet_listing_badges`
--

DROP TABLE IF EXISTS `sell_pet_listing_badges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sell_pet_listing_badges` (
  `id` int NOT NULL AUTO_INCREMENT,
  `listing_id` int NOT NULL,
  `badge` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_listing_id` (`listing_id`),
  CONSTRAINT `sell_pet_listing_badges_ibfk_1` FOREIGN KEY (`listing_id`) REFERENCES `sell_pet_listings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sell_pet_listing_badges`
--

LOCK TABLES `sell_pet_listing_badges` WRITE;
/*!40000 ALTER TABLE `sell_pet_listing_badges` DISABLE KEYS */;
/*!40000 ALTER TABLE `sell_pet_listing_badges` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sell_pet_listing_images`
--

DROP TABLE IF EXISTS `sell_pet_listing_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sell_pet_listing_images`
--

LOCK TABLES `sell_pet_listing_images` WRITE;
/*!40000 ALTER TABLE `sell_pet_listing_images` DISABLE KEYS */;
INSERT INTO `sell_pet_listing_images` VALUES (8,5,'/PETVET/public/images/uploads/pet-listings/pet_5_1761139334_0.jpg',0,'2025-10-22 13:22:14'),(9,6,'/PETVET/public/images/uploads/pet-listings/pet_6_1761140156_0.jpg',0,'2025-10-22 13:35:56'),(10,6,'/PETVET/public/images/uploads/pet-listings/pet_6_1761140182_0.jpg',1,'2025-10-22 13:36:22'),(11,7,'/PETVET/public/images/uploads/pet-listings/pet_7_1761140618_0.jpg',0,'2025-10-22 13:43:38');
/*!40000 ALTER TABLE `sell_pet_listing_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sell_pet_listings`
--

DROP TABLE IF EXISTS `sell_pet_listings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sell_pet_listings`
--

LOCK TABLES `sell_pet_listings` WRITE;
/*!40000 ALTER TABLE `sell_pet_listings` DISABLE KEYS */;
INSERT INTO `sell_pet_listings` VALUES (5,2,'Shadow','Dog','Shiba Inu','2','Male',60000.00,'Kadawatha','Very friendly','0715645789','','','approved','2025-10-22 13:22:14','2025-10-22 13:23:11'),(6,2,'Garfield','Cat','Orange Tabby','4','Male',40000.00,'Maharagama','Eats a lot. Very Aggressive.','0725649798','01124656789','test@gmail.com','approved','2025-10-22 13:35:56','2025-10-22 13:38:15'),(7,2,'Sparrow','Bird','Thick billed','1','Female',25000.00,'Kelaniya','Friendly & Talkative','0789634524','','','approved','2025-10-22 13:43:38','2025-10-22 13:44:23');
/*!40000 ALTER TABLE `sell_pet_listings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_provider_profiles`
--

DROP TABLE IF EXISTS `service_provider_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_provider_profiles`
--

LOCK TABLES `service_provider_profiles` WRITE;
/*!40000 ALTER TABLE `service_provider_profiles` DISABLE KEYS */;
INSERT INTO `service_provider_profiles` VALUES (1,4,'trainer','Pro Pet Training','Colombo, Kandy',8,'Certified Professional Dog Trainer (CPDT)',NULL,NULL,NULL,4.80,45,'Professional dog training services for all breeds',1,'2025-10-21 07:42:28','2025-10-21 07:42:28'),(2,5,'sitter','Caring Pet Sitters','Kandy, Peradeniya',5,'Pet First Aid Certified',NULL,NULL,NULL,4.90,67,'Trusted pet sitting and boarding services',1,'2025-10-21 07:42:28','2025-10-21 07:42:28'),(3,6,'breeder','Premium Breeders','Galle, Matara',12,'Registered Breeder - Kennel Club',NULL,NULL,NULL,4.70,23,'Ethical breeding of purebred dogs and cats',1,'2025-10-21 07:42:28','2025-10-21 07:42:28'),(4,7,'groomer','Pawfect Grooming','Negombo, Colombo',6,'Certified Master Groomer',NULL,NULL,NULL,5.00,89,'Professional grooming services for all pets',1,'2025-10-21 07:42:28','2025-10-21 07:42:28'),(5,10,'trainer','Obey','Kadawatha',10,'adpadlaldp',NULL,NULL,NULL,0.00,0,'Specialization: Obey',1,'2025-10-21 09:18:56','2025-10-21 09:18:56'),(6,10,'sitter',NULL,'',15,NULL,NULL,NULL,NULL,0.00,0,'Home Type: house_with_yard\nPet Types: Cats, Dogs\nMax Pets: 5\nOvernight: Yes',1,'2025-10-21 09:18:56','2025-10-21 09:18:56'),(7,11,'trainer','Obey','Kaduwela',10,'',NULL,NULL,NULL,0.00,0,'Specialization: Obey',1,'2025-10-21 09:53:06','2025-10-21 09:53:06'),(8,11,'groomer','Aminda Groomers','',10,NULL,NULL,NULL,NULL,0.00,0,'Services: nope\nPricing: 1500',1,'2025-10-21 09:53:06','2025-10-21 09:53:06'),(9,11,'sitter',NULL,'',10,NULL,NULL,NULL,NULL,0.00,0,'Home Type: apartment\nPet Types: Cats, Dogs\nMax Pets: 1\nOvernight: No',1,'2025-10-21 09:53:06','2025-10-21 09:53:06'),(10,11,'breeder','Breeding: Germen Shepherd',NULL,10,'156a15544d',NULL,NULL,NULL,0.00,0,'Breeds: Germen Shepherd\nPhilosophy: ',1,'2025-10-21 09:53:06','2025-10-21 09:53:06'),(11,12,'groomer','ba','',1,NULL,NULL,NULL,NULL,0.00,0,'Services: okkoma\nPricing: ba',1,'2025-10-21 10:02:35','2025-10-21 10:02:35'),(12,12,'breeder','Breeding: okkoma',NULL,18,'123',NULL,NULL,NULL,0.00,0,'Breeds: okkoma\nPhilosophy: danne na',1,'2025-10-21 10:02:35','2025-10-21 10:02:35'),(13,16,'trainer','dgdg','dggg',4,'dfddg',NULL,NULL,NULL,0.00,0,'Specialization: dgdg',1,'2025-10-22 16:09:16','2025-10-22 16:09:16'),(14,16,'groomer','dfddg','',5,NULL,NULL,NULL,NULL,0.00,0,'Services: dfgdfg\nPricing: dgdg',1,'2025-10-22 16:09:16','2025-10-22 16:09:16'),(15,16,'sitter',NULL,'',10,NULL,NULL,NULL,NULL,0.00,0,'Home Type: apartment\nPet Types: dgfgfg\nMax Pets: 1\nOvernight: Yes',1,'2025-10-22 16:09:16','2025-10-22 16:09:16'),(16,16,'breeder','Breeding: dfgfhfhfh',NULL,12,'13',NULL,NULL,NULL,0.00,0,'Breeds: dfgfhfhfh\nPhilosophy: sffdf',1,'2025-10-22 16:09:16','2025-10-22 16:09:16');
/*!40000 ALTER TABLE `service_provider_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_roles`
--

DROP TABLE IF EXISTS `user_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_roles`
--

LOCK TABLES `user_roles` WRITE;
/*!40000 ALTER TABLE `user_roles` DISABLE KEYS */;
INSERT INTO `user_roles` VALUES (1,1,4,1,1,'approved',NULL,NULL,'2025-10-21 06:59:16','2025-10-21 06:59:16'),(2,2,1,1,1,'approved',NULL,NULL,'2025-10-21 06:59:16','2025-10-21 06:59:16'),(3,3,2,1,1,'approved',NULL,NULL,'2025-10-21 06:59:16','2025-10-21 06:59:16'),(4,4,6,1,1,'approved',NULL,NULL,NULL,'2025-10-21 07:42:28'),(5,5,7,1,1,'approved',NULL,NULL,NULL,'2025-10-21 07:42:28'),(6,6,8,1,1,'approved',NULL,NULL,NULL,'2025-10-21 07:42:28'),(7,7,9,1,1,'approved',NULL,NULL,NULL,'2025-10-21 07:42:28'),(8,8,5,1,1,'approved',NULL,NULL,NULL,'2025-10-21 07:42:28'),(9,9,3,1,1,'approved',NULL,NULL,NULL,'2025-10-21 08:17:00'),(10,10,1,1,1,'approved',NULL,NULL,NULL,'2025-10-21 09:18:56'),(11,10,6,0,1,'approved',NULL,NULL,NULL,'2025-10-21 09:18:56'),(12,10,7,0,1,'approved',NULL,NULL,NULL,'2025-10-21 09:18:56'),(13,11,1,1,1,'approved',NULL,NULL,NULL,'2025-10-21 09:53:06'),(14,11,6,0,1,'approved',NULL,NULL,NULL,'2025-10-21 09:53:06'),(15,11,9,0,1,'approved',NULL,NULL,NULL,'2025-10-21 09:53:06'),(16,11,7,0,1,'approved',NULL,NULL,NULL,'2025-10-21 09:53:06'),(17,11,8,0,1,'approved',NULL,NULL,NULL,'2025-10-21 09:53:06'),(18,12,1,1,1,'approved',NULL,NULL,NULL,'2025-10-21 10:02:35'),(19,12,9,0,1,'approved',NULL,NULL,NULL,'2025-10-21 10:02:35'),(20,12,8,0,1,'approved',NULL,NULL,NULL,'2025-10-21 10:02:35'),(21,13,2,1,1,'approved',NULL,NULL,NULL,'2025-10-22 08:17:28'),(22,14,3,1,1,'approved',NULL,NULL,NULL,'2025-10-22 08:52:57'),(23,15,3,1,1,'approved',NULL,NULL,NULL,'2025-10-22 10:13:19'),(24,16,1,1,1,'approved',NULL,NULL,NULL,'2025-10-22 16:09:16'),(25,16,6,0,1,'approved',NULL,NULL,NULL,'2025-10-22 16:09:16'),(26,16,9,0,1,'approved',NULL,NULL,NULL,'2025-10-22 16:09:16'),(27,16,7,0,1,'approved',NULL,NULL,NULL,'2025-10-22 16:09:16'),(28,16,8,0,1,'approved',NULL,NULL,NULL,'2025-10-22 16:09:16'),(29,17,5,0,1,'approved',NULL,NULL,NULL,'2025-11-26 06:25:45'),(30,18,2,0,1,'pending',NULL,NULL,NULL,'2025-11-29 10:51:33'),(31,19,2,0,1,'pending',NULL,NULL,NULL,'2025-11-29 10:51:33'),(32,20,2,0,1,'pending',NULL,NULL,NULL,'2025-11-29 10:51:33'),(33,21,2,0,1,'pending',NULL,NULL,NULL,'2025-11-29 10:51:33'),(34,22,2,0,1,'pending',NULL,NULL,NULL,'2025-11-29 10:51:33'),(35,23,2,0,1,'pending',NULL,NULL,NULL,'2025-11-29 10:51:33'),(36,24,2,0,1,'pending',NULL,NULL,NULL,'2025-11-29 10:51:33'),(37,25,2,0,1,'pending',NULL,NULL,NULL,'2025-11-29 10:55:35'),(38,26,2,0,1,'pending',NULL,NULL,NULL,'2025-11-29 10:55:35');
/*!40000 ALTER TABLE `user_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin@gmail.com','$2y$10$BINhO08G1d0W.jnwQ/xHDeDz.B9uzRxfgo9.osphNDGYUPYlESdHi','System','Administrator',NULL,NULL,NULL,1,NULL,1,0,'2025-10-23 04:08:40','2025-10-21 06:59:16','2025-10-23 04:08:40'),(2,'petowner@gmail.com','$2y$10$BINhO08G1d0W.jnwQ/xHDeDz.B9uzRxfgo9.osphNDGYUPYlESdHi','John','Doe','0771234567',NULL,NULL,1,NULL,1,0,'2025-10-22 17:50:20','2025-10-21 06:59:16','2025-10-22 17:50:20'),(3,'vet@gmail.com','$2y$10$BINhO08G1d0W.jnwQ/xHDeDz.B9uzRxfgo9.osphNDGYUPYlESdHi','Sarah','Johnson','0777654321',NULL,NULL,1,NULL,1,0,'2025-10-24 06:58:17','2025-10-21 06:59:16','2025-10-24 06:58:17'),(4,'trainer@gmail.com','$2y$10$BINhO08G1d0W.jnwQ/xHDeDz.B9uzRxfgo9.osphNDGYUPYlESdHi','Tom','Trainer','0771234567','123 Trainer St, Colombo',NULL,1,NULL,1,0,'2025-10-22 10:51:12','2025-10-21 07:42:28','2025-10-22 10:51:12'),(5,'sitter@gmail.com','$2y$10$BINhO08G1d0W.jnwQ/xHDeDz.B9uzRxfgo9.osphNDGYUPYlESdHi','Sam','Sitter','0772234567','456 Sitter Ave, Kandy',NULL,1,NULL,1,0,'2025-10-22 10:55:09','2025-10-21 07:42:28','2025-10-22 10:55:09'),(6,'breeder@gmail.com','$2y$10$BINhO08G1d0W.jnwQ/xHDeDz.B9uzRxfgo9.osphNDGYUPYlESdHi','Bob','Breeder','0773234567','789 Breeder Rd, Galle',NULL,1,NULL,1,0,'2025-10-22 10:56:37','2025-10-21 07:42:28','2025-10-22 10:56:37'),(7,'groomer@gmail.com','$2y$10$BINhO08G1d0W.jnwQ/xHDeDz.B9uzRxfgo9.osphNDGYUPYlESdHi','Grace','Groomer','0774234567','321 Groomer Lane, Negombo',NULL,1,NULL,1,0,'2025-10-22 10:57:55','2025-10-21 07:42:28','2025-10-22 10:57:55'),(8,'recep@gmail.com','$2y$10$BINhO08G1d0W.jnwQ/xHDeDz.B9uzRxfgo9.osphNDGYUPYlESdHi','Rita','Receptionist','0775234567','654 Clinic St, Colombo 7',NULL,1,NULL,1,0,'2025-11-26 06:12:53','2025-10-21 07:42:28','2025-11-26 06:12:53'),(9,'manager@gmail.com','$2y$10$BINhO08G1d0W.jnwQ/xHDeDz.B9uzRxfgo9.osphNDGYUPYlESdHi','Mike','Manager','0776234567','888 Clinic Ave, Colombo',NULL,1,NULL,1,0,'2025-10-24 06:59:24','2025-10-21 08:17:00','2025-10-24 06:59:24'),(10,'peterpoker@gmail.com','$2y$10$oul5xyrLiqPgJdW1AfdYdu0hdvnu6BDzPSVtT7hacTk9W8v4Kwm5e','Hesara','Liyanage','0775983002','145/2/1',NULL,1,NULL,1,0,'2025-10-21 09:19:38','2025-10-21 09:18:56','2025-10-21 09:19:38'),(11,'allinone@gmail.com','$2y$10$B.T98na4LBqC/JOSsTVqPOP64wmqXUVCDk1fd5hxVczwC9HPDJGCq','Hesara','Liyanage','0775983002','145/2/1',NULL,1,NULL,1,0,'2025-10-23 06:10:04','2025-10-21 09:53:06','2025-10-23 06:10:04'),(12,'amindasithummal@gmail.com','$2y$10$1Rsugbt18Haag0Ctp9U91.OGY2.VCDiXnde4h5/Zd8yiDL27rjnhi','aminda','sithummal','0701101519','addlsdksl',NULL,1,NULL,1,0,'2025-10-21 10:03:11','2025-10-21 10:02:35','2025-10-21 10:03:11'),(13,'amindavet@gmail.com','$2y$10$gGkycyLU.By6q4pB7oq8nOC45XJVRNvGyVY4XyGrBz9aDjWuUAsyO','Dihindu','Hesara','0775983002','145/2/1 Gonahena Kadawatha',NULL,1,NULL,1,0,'2025-10-22 08:18:04','2025-10-22 08:17:28','2025-10-22 08:18:04'),(14,'clinica@gmail.com','$2y$10$JBzMnwuUXSho5xGzWmrxc.PSQcsvs47JHls5kLK59Ql5jWSoDEOVS','Hesara','Liyanage','0775983002','',NULL,1,NULL,1,0,NULL,'2025-10-22 08:52:57','2025-10-22 08:52:57'),(15,'pokerpeter474@gmail.com','$2y$10$.WzOwjbd/yTG2Pzh/OKyhOGBDBtfCOximkddp5p.Y4mF/04CPjc4u','Peter','Parker','0775983002','',NULL,1,NULL,1,0,'2025-10-22 10:13:36','2025-10-22 10:13:19','2025-10-22 10:13:36'),(16,'ddfgdfdgdg@gmail.com','$2y$10$V4aOFQDrcvBg4dbTzMYkqO5m8wa2HKGGO.x7VvrPLRpE1b1rNYUIy','aad','sddgdgdg','0701294656','fhfhthh',NULL,1,NULL,1,0,NULL,'2025-10-22 16:09:16','2025-10-22 16:09:16'),(17,'receptionist@petvet.com','$2y$10$4lwVpDlRIKo0HVZ8rciaZ.5I9z0T61JsmGzsaFCVozSpJXUJo38i.','Jane','Smith','0771234567',NULL,NULL,1,NULL,1,0,'2025-11-29 10:05:09','2025-11-26 06:25:37','2025-11-29 10:05:09'),(18,'michael.chen@petvet.com','hashed_password','Michael','Chen','0771234568',NULL,NULL,0,NULL,1,0,NULL,'2025-11-29 10:51:09','2025-11-29 10:51:09'),(19,'emily.rodriguez@petvet.com','hashed_password','Emily','Rodriguez','0771234569',NULL,NULL,0,NULL,1,0,NULL,'2025-11-29 10:51:09','2025-11-29 10:51:09'),(20,'james.wilson@petvet.com','hashed_password','James','Wilson','0771234570',NULL,NULL,0,NULL,1,0,NULL,'2025-11-29 10:51:09','2025-11-29 10:51:09'),(21,'priya.perera@petvet.com','hashed_password','Priya','Perera','0771234571',NULL,NULL,0,NULL,1,0,NULL,'2025-11-29 10:51:09','2025-11-29 10:51:09'),(22,'nuwan.silva@petvet.com','hashed_password','Nuwan','Silva','0771234572',NULL,NULL,0,NULL,1,0,NULL,'2025-11-29 10:51:09','2025-11-29 10:51:09'),(23,'anjali.fernando@petvet.com','hashed_password','Anjali','Fernando','0771234573',NULL,NULL,0,NULL,1,0,NULL,'2025-11-29 10:51:09','2025-11-29 10:51:09'),(24,'rajesh.kumar@petvet.com','hashed_password','Rajesh','Kumar','0771234574',NULL,NULL,0,NULL,1,0,NULL,'2025-11-29 10:51:09','2025-11-29 10:51:09'),(25,'lisa.thompson@petvet.com','hashed_password','Lisa','Thompson','0771234575',NULL,NULL,0,NULL,1,0,NULL,'2025-11-29 10:55:12','2025-11-29 10:55:12'),(26,'david.lee@petvet.com','hashed_password','David','Lee','0771234576',NULL,NULL,0,NULL,1,0,NULL,'2025-11-29 10:55:12','2025-11-29 10:55:12');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vet_profiles`
--

DROP TABLE IF EXISTS `vet_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vet_profiles`
--

LOCK TABLES `vet_profiles` WRITE;
/*!40000 ALTER TABLE `vet_profiles` DISABLE KEYS */;
INSERT INTO `vet_profiles` VALUES (1,3,1,'VET-LK-2020-001234','General Practice, Surgery',8,NULL,0.00,0.00,0,'Experienced veterinarian specializing in small animals with a passion for preventive care.',1,'2025-10-21 06:59:16','2025-10-21 06:59:16'),(2,13,1,'DFSD4561863','Dental',10,NULL,0.00,0.00,0,NULL,1,'2025-10-22 08:17:28','2025-10-22 08:17:28');
/*!40000 ALTER TABLE `vet_profiles` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-30 12:11:25
