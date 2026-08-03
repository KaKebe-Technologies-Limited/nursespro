-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: nursespro
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

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
-- Table structure for table `classes`
--

DROP TABLE IF EXISTS `classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `tutor_name` varchar(150) NOT NULL,
  `class_date` date NOT NULL,
  `class_time` varchar(100) NOT NULL,
  `status` enum('upcoming','live','completed') NOT NULL DEFAULT 'upcoming',
  `telegram_link` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `module_id` (`module_id`),
  CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `curriculum_modules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `classes`
--

LOCK TABLES `classes` WRITE;
/*!40000 ALTER TABLE `classes` DISABLE KEYS */;
INSERT INTO `classes` VALUES (1,2,'Medical Nursing III – Cardiac Case Review','Dr. Sarah Namukasa','2026-08-05','6:00 PM – 8:00 PM','upcoming','https://t.me/nursespro_dne112','2026-08-02 21:33:29'),(2,3,'Surgical & Paediatric Nursing Masterclass','Ms. Grace Atim','2026-08-03','7:00 PM – 9:00 PM','live','https://t.me/nursespro_dne113','2026-08-02 21:33:29'),(3,4,'Pharmacology II – Drug Calculations Workshop','Mr. Joseph Otim','2026-07-28','5:00 PM – 7:00 PM','completed',NULL,'2026-08-02 21:33:29'),(4,6,'PHC & Community-Based Health Care Seminar','Dr. Peter Waiswa','2026-08-08','6:30 PM – 8:30 PM','upcoming','https://t.me/nursespro_dne121','2026-08-02 21:33:29');
/*!40000 ALTER TABLE `classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `curriculum_courses`
--

DROP TABLE IF EXISTS `curriculum_courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `curriculum_courses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `curriculum_courses`
--

LOCK TABLES `curriculum_courses` WRITE;
/*!40000 ALTER TABLE `curriculum_courses` DISABLE KEYS */;
INSERT INTO `curriculum_courses` VALUES (4,'Diploma in Midwifery (Direct)'),(3,'Diploma in Midwifery (Extension)'),(2,'Diploma in Nursing (Direct)'),(1,'Diploma in Nursing (Extension)');
/*!40000 ALTER TABLE `curriculum_courses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `curriculum_modules`
--

DROP TABLE IF EXISTS `curriculum_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `curriculum_modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL,
  `year` varchar(20) NOT NULL,
  `semester` varchar(20) NOT NULL,
  `code` varchar(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_code` (`course_id`,`code`),
  CONSTRAINT `curriculum_modules_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `curriculum_courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `curriculum_modules`
--

LOCK TABLES `curriculum_modules` WRITE;
/*!40000 ALTER TABLE `curriculum_modules` DISABLE KEYS */;
INSERT INTO `curriculum_modules` VALUES (1,1,'Year 1','Semester 1','DNE 111','Foundations of Nursing III',1,'2026-08-02 21:33:28'),(2,1,'Year 1','Semester 1','DNE 112','Medical Nursing III',2,'2026-08-02 21:33:28'),(3,1,'Year 1','Semester 1','DNE 113','Surgical Nursing III & Paediatric Nursing',3,'2026-08-02 21:33:28'),(4,1,'Year 1','Semester 1','DNE 114','Mental Health Nursing II & Pharmacology II',4,'2026-08-02 21:33:28'),(5,1,'Year 1','Semester 1','DNE 115','Practicals',5,'2026-08-02 21:33:28'),(6,1,'Year 1','Semester 2','DNE 121','Primary Health Care (PHC) & Community Based Health Care (CBHC)',1,'2026-08-02 21:33:28'),(7,1,'Year 1','Semester 2','DNE 122','Applied Research and Teaching Methodology',2,'2026-08-02 21:33:28'),(8,1,'Year 1','Semester 2','DNE 123','Palliative Care Nursing',3,'2026-08-02 21:33:28'),(9,1,'Year 1','Semester 2','DNE 124','Disaster Management & Occupational Health and Safety',4,'2026-08-02 21:33:28'),(10,1,'Year 1','Semester 2','DNE 125','Practicals',5,'2026-08-02 21:33:28'),(11,1,'Year 2','Semester 1','DNE 211','Paediatric Nursing III',1,'2026-08-02 21:33:28'),(12,1,'Year 2','Semester 1','DNE 212','Gynaecology Nursing (II) & Reproductive Health Nursing (II)',2,'2026-08-02 21:33:28'),(13,1,'Year 2','Semester 1','DNE 213','Health Service Management & Entrepreneurship',3,'2026-08-02 21:33:28'),(14,1,'Year 2','Semester 1','DNE 214','Practical',4,'2026-08-02 21:33:28');
/*!40000 ALTER TABLE `curriculum_modules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notes`
--

DROP TABLE IF EXISTS `notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `views` int(11) NOT NULL DEFAULT 0,
  `uploaded_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `module_id` (`module_id`),
  KEY `uploaded_by` (`uploaded_by`),
  CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `curriculum_modules` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notes_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notes`
--

LOCK TABLES `notes` WRITE;
/*!40000 ALTER TABLE `notes` DISABLE KEYS */;
INSERT INTO `notes` VALUES (1,1,'Foundations of Nursing Practice III','Core nursing principles, the nursing process (ADPIE), and professional ethical practice.','dne111_foundations_of_nursing.pdf',345,1,'2026-08-02 21:33:29'),(2,2,'Medical Nursing III – Chronic Disease Management','Nursing management of hypertension, diabetes, cardiovascular and respiratory conditions.','dne112_medical_nursing.pdf',290,1,'2026-08-02 21:33:29'),(3,3,'Surgical Nursing III & Paediatric Nursing','Pre/post-operative care and the principles of paediatric nursing.','dne113_surgical_paediatric_nursing.pdf',176,1,'2026-08-02 21:33:29'),(4,4,'Pharmacology II – Drug Calculations & Dosage','Drug dosage formulas, IV drip rate calculations, and medication safety.','dne114_pharmacology_drug_calculations.pdf',210,1,'2026-08-02 21:33:29'),(5,6,'Primary Health Care & Community-Based Health Care','PHC principles and community-based health care delivery in Uganda.','dne121_phc_cbhc.pdf',154,1,'2026-08-02 21:33:29'),(6,7,'Applied Research Methods for Nursing','Research process, evidence-based practice, and basic teaching methodology.','dne122_applied_research_teaching_methodology.pdf',198,1,'2026-08-02 21:33:29'),(7,8,'Palliative Care Nursing – End of Life Care','Symptom management and holistic care for terminally ill patients.','dne123_palliative_care_nursing.pdf',133,1,'2026-08-02 21:33:29'),(8,11,'Paediatric Nursing III – Advanced Child Care','IMCI assessment, nutrition/growth monitoring, and common childhood illnesses.','dne211_paediatric_nursing_iii.pdf',167,1,'2026-08-02 21:33:29'),(9,12,'Gynaecology & Reproductive Health Nursing','Gynaecological conditions and reproductive health nursing care.','dne212_gynaecology_reproductive_health.pdf',141,1,'2026-08-02 21:33:29'),(10,13,'Health Service Management & Entrepreneurship','Health service leadership, management principles, and entrepreneurship.','dne213_health_service_management.pdf',98,1,'2026-08-02 21:33:29');
/*!40000 ALTER TABLE `notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `method` varchar(20) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `reference` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'paid',
  `paid_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expiry_granted` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (1,2,18500,'Airtel','0701234567','REF7C5F9089F1','paid','2026-08-02 20:33:28','2026-11-02 23:33:28'),(2,4,18500,'MTN','0789012345','REFBE101562E3','paid','2025-10-14 20:33:28','2026-01-14 23:33:28'),(3,5,18500,'Airtel','0700333444','REF87BCABC658','paid','2026-07-02 20:33:28','2026-10-02 23:33:28'),(4,6,18500,'MTN','0701444555','REF666C702C57','paid','2026-08-02 20:33:28','2026-11-02 23:33:28'),(5,7,18500,'Airtel','0702555666','REF956060F102','paid','2026-06-02 20:33:28','2026-09-02 23:33:28'),(6,8,18500,'Airtel','0703666777','REF0BCBBDF108','paid','2026-07-02 20:33:28','2026-10-02 23:33:28'),(7,9,18500,'MTN','0704777888','REFF81009F614','paid','2026-03-18 20:33:28','2026-06-18 23:33:28'),(8,10,18500,'MTN','0705888999','REF0CE3D0AC54','paid','2026-08-02 20:33:28','2026-11-02 23:33:28'),(9,11,18500,'MTN','0706999000','REF06053222CE','paid','2026-06-02 20:33:28','2026-09-02 23:33:28'),(10,2,18500,'MTN','0701234567','REF2EA9814AB7','paid','2026-03-15 20:33:29','2026-06-13 23:33:29');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_notes`
--

DROP TABLE IF EXISTS `student_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student_notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL DEFAULT 'General',
  `content` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `student_notes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_notes`
--

LOCK TABLES `student_notes` WRITE;
/*!40000 ALTER TABLE `student_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('student','tutor','superadmin') NOT NULL DEFAULT 'student',
  `course` varchar(150) DEFAULT NULL,
  `year` varchar(20) DEFAULT NULL,
  `semester` varchar(20) DEFAULT NULL,
  `institution` varchar(150) DEFAULT NULL,
  `reg_number` varchar(50) DEFAULT NULL,
  `access_expiry` datetime DEFAULT NULL,
  `notes_viewed` int(11) NOT NULL DEFAULT 0,
  `classes_attended` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin User','admin@nursespro.ac.ug','$2y$10$IWOcUlzGjqDr2LZIiN1XAecUGgu7jDrKbKrNKTCS1ZBKxSkNx31mC','0392972444','superadmin','Diploma in Nursing (Direct)','Year 2','Semester 1','Mulago School of Nursing','ADM001','2027-02-02 23:33:28',42,18,'2026-08-02 21:33:28'),(2,'Sarah Nakato','sarah@demo.com','$2y$10$m7YcG2axrPiQrUJL5wVXT.QXOXIxYpuuqdujEWcUCTqsmsj7wqzFe','0701234567','student','Diploma in Nursing (Extension)','Year 1','Semester 1','Mulago School of Nursing','MID22001','2026-11-02 23:33:28',19,7,'2026-08-02 21:33:28'),(3,'John Okello','john@demo.com','$2y$10$NgNbIoTNVO6m3LdWGZ2afOQwS04tDM4GeAWwNi5ke5CtI0JUtOeD.','0712345678','tutor','Diploma in Nursing (Extension)','Year 3','Semester 1','Kampala International','NUR21009','2027-02-02 23:33:28',28,12,'2026-08-02 21:33:28'),(4,'Grace Apio','grace@demo.com','$2y$10$KmqchfM9FuiQPW/4UJ6YD.dzkBUlSdejIoCOXcjLmbo/ShVcc5QwO','0789012345','student','Diploma in Nursing (Direct)','Year 2','Semester 2','Makerere University','NUR23007','2026-01-14 23:33:28',5,2,'2026-08-02 21:33:29'),(5,'Peter Musoke','peter@demo.com','$2y$10$x4lN8iwjWn/SQDIyKw9TaurJfu9.nf3fwSJxnpLZYjWLXNlFsFt66','0700333444','student','Diploma in Nursing (Extension)','Year 1','Semester 1','Mulago School of Nursing','DNE24001','2026-10-02 23:33:28',9,4,'2026-08-02 21:33:29'),(6,'Immaculate Nabirye','immaculate@demo.com','$2y$10$V3uDvBK7rvJIkdxsBOeok.9IWDKF4G1XoJJxoOK0BtjmfBKGqpr.S','0701444555','student','Diploma in Nursing (Extension)','Year 1','Semester 1','Mulago School of Nursing','DNE24002','2026-11-02 23:33:28',11,6,'2026-08-02 21:33:29'),(7,'Brian Ssekandi','brian@demo.com','$2y$10$b65ow02eudC2byBSWbqg7uii10rN5O0JZji2e6Q5Gh0aDczhwcVtq','0702555666','student','Diploma in Nursing (Extension)','Year 1','Semester 2','Kampala International','DNE24003','2026-11-03 08:43:39',6,3,'2026-08-02 21:33:29'),(8,'Diana Achieng','diana@demo.com','$2y$10$1fEmd/4oQDEa4qEOOJJSJ.v/ZVubvLSzwraoiGs3LgnHLAYacQQhO','0703666777','student','Diploma in Nursing (Extension)','Year 2','Semester 1','Mulago School of Nursing','DNE23004','2026-10-02 23:33:28',17,9,'2026-08-02 21:33:29'),(9,'Moses Kato','moses@demo.com','$2y$10$dK3B8JLtsaH8zZ8sNn7PmeSR9fCg6T9zmZtIcV/zCZnnIOS1hEI6a','0704777888','student','Diploma in Nursing (Extension)','Year 2','Semester 1','Makerere University','DNE23005','2026-06-18 23:33:28',3,1,'2026-08-02 21:33:29'),(10,'Ritah Nansubuga','ritah@demo.com','$2y$10$cqpBjqahVgJUSvwLvCmZl.DtA8/JbyBlTEd2d4ohTx/mqor6gHbym','0705888999','student','Diploma in Nursing (Extension)','Year 1','Semester 2','Mulago School of Nursing','DNE24006','2026-11-02 23:33:28',8,5,'2026-08-02 21:33:29'),(11,'Emmanuel Byaruhanga','emmanuel@demo.com','$2y$10$K3XhLbnT2lnRMF..AZkdAOPE64/iTSyfFmvMLZ/8M4LyueulUxuNi','0706999000','student','Diploma in Nursing (Direct)','Year 1','Semester 1','Kampala International','NUR24010','2026-09-02 23:33:28',4,2,'2026-08-02 21:33:29');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-03 11:24:36
