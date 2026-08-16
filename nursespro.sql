-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 16, 2026 at 09:29 PM
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
-- Database: `nursespro`
--

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `tutor_name` varchar(150) NOT NULL,
  `class_date` date NOT NULL,
  `class_time` varchar(100) NOT NULL,
  `status` enum('upcoming','live','completed') NOT NULL DEFAULT 'upcoming',
  `telegram_link` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `module_id`, `title`, `tutor_name`, `class_date`, `class_time`, `status`, `telegram_link`, `created_at`) VALUES
(1, 2, 'Medical Nursing III – Cardiac Case Review', 'Dr. Sarah Namukasa', '2026-08-05', '6:00 PM – 8:00 PM', 'upcoming', 'https://t.me/nursespro_dne112', '2026-08-02 21:33:29'),
(2, 3, 'Surgical & Paediatric Nursing Masterclass', 'Ms. Grace Atim', '2026-08-03', '7:00 PM – 9:00 PM', 'live', 'https://t.me/nursespro_dne113', '2026-08-02 21:33:29'),
(3, 4, 'Pharmacology II – Drug Calculations Workshop', 'Mr. Joseph Otim', '2026-07-28', '5:00 PM – 7:00 PM', 'completed', NULL, '2026-08-02 21:33:29'),
(4, 6, 'PHC & Community-Based Health Care Seminar', 'Dr. Peter Waiswa', '2026-08-08', '6:30 PM – 8:30 PM', 'upcoming', 'https://t.me/nursespro_dne121', '2026-08-02 21:33:29');

-- --------------------------------------------------------

--
-- Table structure for table `curriculum_courses`
--

CREATE TABLE `curriculum_courses` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `curriculum_courses`
--

INSERT INTO `curriculum_courses` (`id`, `name`) VALUES
(4, 'Diploma in Midwifery (Direct)'),
(3, 'Diploma in Midwifery (Extension)'),
(2, 'Diploma in Nursing (Direct)'),
(1, 'Diploma in Nursing (Extension)');

-- --------------------------------------------------------

--
-- Table structure for table `curriculum_modules`
--

CREATE TABLE `curriculum_modules` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `year` varchar(20) NOT NULL,
  `semester` varchar(20) NOT NULL,
  `code` varchar(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `curriculum_modules`
--

INSERT INTO `curriculum_modules` (`id`, `course_id`, `year`, `semester`, `code`, `title`, `sort_order`, `created_at`) VALUES
(1, 1, 'Year 1', 'Semester 1', 'DNE 111', 'Foundations of Nursing III', 1, '2026-08-02 21:33:28'),
(2, 1, 'Year 1', 'Semester 1', 'DNE 112', 'Medical Nursing III', 2, '2026-08-02 21:33:28'),
(3, 1, 'Year 1', 'Semester 1', 'DNE 113', 'Surgical Nursing III & Paediatric Nursing', 3, '2026-08-02 21:33:28'),
(4, 1, 'Year 1', 'Semester 1', 'DNE 114', 'Mental Health Nursing II & Pharmacology II', 4, '2026-08-02 21:33:28'),
(5, 1, 'Year 1', 'Semester 1', 'DNE 115', 'Practicals', 5, '2026-08-02 21:33:28'),
(6, 1, 'Year 1', 'Semester 2', 'DNE 121', 'Primary Health Care (PHC) & Community Based Health Care (CBHC)', 1, '2026-08-02 21:33:28'),
(7, 1, 'Year 1', 'Semester 2', 'DNE 122', 'Applied Research and Teaching Methodology', 2, '2026-08-02 21:33:28'),
(8, 1, 'Year 1', 'Semester 2', 'DNE 123', 'Palliative Care Nursing', 3, '2026-08-02 21:33:28'),
(9, 1, 'Year 1', 'Semester 2', 'DNE 124', 'Disaster Management & Occupational Health and Safety', 4, '2026-08-02 21:33:28'),
(10, 1, 'Year 1', 'Semester 2', 'DNE 125', 'Practicals', 5, '2026-08-02 21:33:28'),
(11, 1, 'Year 2', 'Semester 1', 'DNE 211', 'Paediatric Nursing III', 1, '2026-08-02 21:33:28'),
(12, 1, 'Year 2', 'Semester 1', 'DNE 212', 'Gynaecology Nursing (II) & Reproductive Health Nursing (II)', 2, '2026-08-02 21:33:28'),
(13, 1, 'Year 2', 'Semester 1', 'DNE 213', 'Health Service Management & Entrepreneurship', 3, '2026-08-02 21:33:28'),
(14, 1, 'Year 2', 'Semester 1', 'DNE 214', 'Practical', 4, '2026-08-02 21:33:28');

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE `notes` (
  `id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `topic_id` int(11) DEFAULT NULL,
  `subtopic_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `views` int(11) NOT NULL DEFAULT 0,
  `uploaded_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`id`, `module_id`, `topic_id`, `subtopic_id`, `title`, `description`, `file_path`, `views`, `uploaded_by`, `created_at`) VALUES
(1, 1, NULL, NULL, 'Foundations of Nursing Practice III', 'Core nursing principles, the nursing process (ADPIE), and professional ethical practice.', 'dne111_foundations_of_nursing.pdf', 351, 1, '2026-08-02 21:33:29'),
(2, 2, NULL, NULL, 'Medical Nursing III – Chronic Disease Management', 'Nursing management of hypertension, diabetes, cardiovascular and respiratory conditions.', 'dne112_medical_nursing.pdf', 293, 1, '2026-08-02 21:33:29'),
(3, 3, NULL, NULL, 'Surgical Nursing III & Paediatric Nursing', 'Pre/post-operative care and the principles of paediatric nursing.', 'dne113_surgical_paediatric_nursing.pdf', 182, 1, '2026-08-02 21:33:29'),
(4, 4, NULL, NULL, 'Pharmacology II – Drug Calculations & Dosage', 'Drug dosage formulas, IV drip rate calculations, and medication safety.', 'dne114_pharmacology_drug_calculations.pdf', 215, 1, '2026-08-02 21:33:29'),
(5, 6, NULL, NULL, 'Primary Health Care & Community-Based Health Care', 'PHC principles and community-based health care delivery in Uganda.', 'dne121_phc_cbhc.pdf', 155, 1, '2026-08-02 21:33:29'),
(6, 7, NULL, NULL, 'Applied Research Methods for Nursing', 'Research process, evidence-based practice, and basic teaching methodology.', 'dne122_applied_research_teaching_methodology.pdf', 199, 1, '2026-08-02 21:33:29'),
(7, 8, NULL, NULL, 'Palliative Care Nursing – End of Life Care', 'Symptom management and holistic care for terminally ill patients.', 'dne123_palliative_care_nursing.pdf', 134, 1, '2026-08-02 21:33:29'),
(8, 11, NULL, NULL, 'Paediatric Nursing III – Advanced Child Care', 'IMCI assessment, nutrition/growth monitoring, and common childhood illnesses.', 'dne211_paediatric_nursing_iii.pdf', 169, 1, '2026-08-02 21:33:29'),
(9, 12, NULL, NULL, 'Gynaecology & Reproductive Health Nursing', 'Gynaecological conditions and reproductive health nursing care.', 'dne212_gynaecology_reproductive_health.pdf', 142, 1, '2026-08-02 21:33:29'),
(10, 13, NULL, NULL, 'Health Service Management & Entrepreneurship', 'Health service leadership, management principles, and entrepreneurship.', 'dne213_health_service_management.pdf', 100, 1, '2026-08-02 21:33:29');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `method` varchar(20) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `reference` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'paid',
  `paid_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expiry_granted` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `user_id`, `amount`, `method`, `phone`, `reference`, `status`, `paid_at`, `expiry_granted`) VALUES
(1, 2, 18500, 'Airtel', '0701234567', 'REF7C5F9089F1', 'paid', '2026-08-02 20:33:28', '2026-11-02 23:33:28'),
(2, 4, 18500, 'MTN', '0789012345', 'REFBE101562E3', 'paid', '2025-10-14 20:33:28', '2026-01-14 23:33:28'),
(3, 5, 18500, 'Airtel', '0700333444', 'REF87BCABC658', 'paid', '2026-07-02 20:33:28', '2026-10-02 23:33:28'),
(4, 6, 18500, 'MTN', '0701444555', 'REF666C702C57', 'paid', '2026-08-02 20:33:28', '2026-11-02 23:33:28'),
(5, 7, 18500, 'Airtel', '0702555666', 'REF956060F102', 'paid', '2026-06-02 20:33:28', '2026-09-02 23:33:28'),
(6, 8, 18500, 'Airtel', '0703666777', 'REF0BCBBDF108', 'paid', '2026-07-02 20:33:28', '2026-10-02 23:33:28'),
(7, 9, 18500, 'MTN', '0704777888', 'REFF81009F614', 'paid', '2026-03-18 20:33:28', '2026-06-18 23:33:28'),
(8, 10, 18500, 'MTN', '0705888999', 'REF0CE3D0AC54', 'paid', '2026-08-02 20:33:28', '2026-11-02 23:33:28'),
(9, 11, 18500, 'MTN', '0706999000', 'REF06053222CE', 'paid', '2026-06-02 20:33:28', '2026-09-02 23:33:28'),
(10, 2, 18500, 'MTN', '0701234567', 'REF2EA9814AB7', 'paid', '2026-03-15 20:33:29', '2026-06-13 23:33:29'),
(15, 15, 18500, 'Pesapal', '0777676206', '8db53d95-6c47-4f9d-87ac-da1154a2f875', 'pending', '2026-08-03 09:27:10', '2027-02-03 11:27:07');

-- --------------------------------------------------------

--
-- Table structure for table `student_notes`
--

CREATE TABLE `student_notes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL DEFAULT 'General',
  `content` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subtopics`
--

CREATE TABLE `subtopics` (
  `id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `code` varchar(20) DEFAULT NULL,
  `title` varchar(500) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subtopics`
--

INSERT INTO `subtopics` (`id`, `topic_id`, `code`, `title`, `sort_order`, `created_at`) VALUES
(1, 1, NULL, 'Apply nursing process in the management of patients with conditions of the reproductive system', 1, '2026-08-03 08:30:02'),
(2, 1, NULL, 'Apply nursing process in the management of patients with conditions of the endocrine system', 2, '2026-08-03 08:30:02'),
(3, 1, NULL, 'Apply nursing process in the management of patients with conditions of the nervous system', 3, '2026-08-03 08:30:02'),
(4, 1, NULL, 'Apply nursing process in the management of patients with conditions of the renal system', 4, '2026-08-03 08:30:02'),
(5, 1, NULL, 'Apply nursing process in the management of patients with conditions of the musculo-skeletal system', 5, '2026-08-03 08:30:02'),
(6, 1, NULL, 'Apply nursing process in the management of patients with conditions of the lymphatic system', 6, '2026-08-03 08:30:02'),
(7, 1, NULL, 'Apply nursing process in the management of patients with conditions of the digestive system', 7, '2026-08-03 08:30:02'),
(8, 1, NULL, 'Apply nursing process in the management of patients with conditions of the respiratory system', 8, '2026-08-03 08:30:02'),
(9, 1, NULL, 'Apply nursing process in the management of patients with conditions of the cardiovascular system', 9, '2026-08-03 08:30:02'),
(10, 1, NULL, 'Apply nursing process in the management of patients with conditions of the skin', 10, '2026-08-03 08:30:03'),
(11, 1, NULL, 'Apply nursing process in the management of patients with conditions of the eye', 11, '2026-08-03 08:30:03'),
(12, 1, NULL, 'Apply nursing process in the management of patients with conditions of the ear, nose and throat (ENT)', 12, '2026-08-03 08:30:03'),
(13, 2, NULL, 'Perform Shortening and removal of drains', 1, '2026-08-03 08:30:03'),
(14, 2, NULL, 'Perform Colostomy Care', 2, '2026-08-03 08:30:03'),
(15, 2, NULL, 'Prepare for Abdominis Paracentesis (Abdominal Tapping)', 3, '2026-08-03 08:30:03'),
(16, 2, NULL, 'Prepare for Lumbar Puncture', 4, '2026-08-03 08:30:03'),
(17, 2, NULL, 'Perform Gastrostomy Feeding', 5, '2026-08-03 08:30:03'),
(18, 2, NULL, 'Carry out gastric Lavage', 6, '2026-08-03 08:30:03'),
(19, 2, NULL, 'Perform Tracheostomy Care', 7, '2026-08-03 08:30:03'),
(20, 2, NULL, 'Ophthalmology (Eye care, Pre & Post Operative care, Charts)', 8, '2026-08-03 08:30:03'),
(21, 2, NULL, 'Care of the patients\' ears', 9, '2026-08-03 08:30:03'),
(22, 2, NULL, 'Peri-Operative care', 10, '2026-08-03 08:30:03'),
(23, 2, NULL, 'Wound dressing', 11, '2026-08-03 08:30:03'),
(24, 3, '4.8.1', 'Applied anatomy and Physiology of the nervous system', 1, '2026-08-03 08:30:03'),
(25, 3, '4.8.2', 'Trigeminal neuralgia', 2, '2026-08-03 08:30:03'),
(26, 3, '4.8.3', 'Bell\'s palsy', 3, '2026-08-03 08:30:03'),
(27, 3, '4.8.4', 'Parkinson\'s disease', 4, '2026-08-03 08:30:03'),
(28, 3, NULL, 'Spinal cord compression', 5, '2026-08-03 08:30:03'),
(29, 3, NULL, 'Transverse Myelitis', 6, '2026-08-03 08:30:03'),
(30, 3, NULL, 'Sub arachnoid haemorrhage and intra cranial aneurysm', 7, '2026-08-03 08:30:03'),
(31, 3, NULL, 'General Paralysis of Insane', 8, '2026-08-03 08:30:03'),
(32, 4, '4.9.1', 'Applied anatomy and physiology of the endocrine system', 1, '2026-08-03 08:30:03'),
(33, 4, '4.9.2', 'Acromegaly/Gigantism (Hyperpituitarism)', 2, '2026-08-03 08:30:03'),
(34, 4, '4.9.3', 'Dwarfism (Panhypopituitarism)', 3, '2026-08-03 08:30:03'),
(35, 4, '4.9.4', 'Addison\'s disease (Adrenal insufficiency)', 4, '2026-08-03 08:30:03'),
(36, 4, '4.9.5', 'Pheochromocytoma', 5, '2026-08-03 08:30:03'),
(37, 4, '4.9.6', 'Cushing\'s syndrome', 6, '2026-08-03 08:30:03'),
(38, 4, '4.9.7', 'Hyperaldosteronism', 7, '2026-08-03 08:30:03'),
(39, 4, '4.9.8', 'Thyrotoxicosis', 8, '2026-08-03 08:30:03'),
(40, 4, '4.9.8', 'Hyperthyroidism', 9, '2026-08-03 08:30:03'),
(41, 4, '4.9.8', 'Hyperparathyroidism & Hypoparathyroidism', 10, '2026-08-03 08:30:03'),
(42, 4, '4.9.9', 'Diabetes Mellitus Type 1', 11, '2026-08-03 08:30:03'),
(43, 4, '4.9.9', 'Diabetes Mellitus Type 2', 12, '2026-08-03 08:30:03'),
(44, 5, '4.10.0', 'Anatomy and Physiology of the Renal System', 1, '2026-08-03 08:30:03'),
(45, 5, '4.10.1', 'Urinary tract infections', 2, '2026-08-03 08:30:03'),
(46, 5, '4.10.2', 'Polycystic Kidney disease (PKD)', 3, '2026-08-03 08:30:03'),
(47, 5, '4.10.3', 'Kidney stones', 4, '2026-08-03 08:30:03'),
(48, 6, '4.11.1', 'Anatomy and Physiology of the Lymphatic System', 1, '2026-08-03 08:30:03'),
(49, 6, '4.11.2', 'Lymphedema', 2, '2026-08-03 08:30:03'),
(50, 6, '4.11.3', 'Lymphangitis', 3, '2026-08-03 08:30:03'),
(51, 6, '4.11.4', 'Hodgkin\'s disease', 4, '2026-08-03 08:30:03'),
(52, 7, '4.12.0', 'Anatomy and Physiology of the Musculo-skeletal System', 1, '2026-08-03 08:30:03'),
(53, 7, '4.12.1', 'Tendonitis', 2, '2026-08-03 08:30:03'),
(54, 7, '4.12.2', 'Rheumatoid Arthritis', 3, '2026-08-03 08:30:03'),
(55, 7, '4.12.3', 'Osteoarthritis', 4, '2026-08-03 08:30:03'),
(56, 7, '4.12.4', 'Gout', 5, '2026-08-03 08:30:03'),
(57, 7, '4.12.5', 'Bursitis', 6, '2026-08-03 08:30:03'),
(58, 7, '4.12.6', 'Ankylosing Spondylitis', 7, '2026-08-03 08:30:03'),
(59, 7, '4.12.7', 'Systemic Lupus Erythematosus (SLE)', 8, '2026-08-03 08:30:03'),
(60, 7, '4.12.8', 'Osteoporosis', 9, '2026-08-03 08:30:03'),
(61, 7, '4.12.9', 'Paget\'s disease', 10, '2026-08-03 08:30:03'),
(62, 7, '4.12.10', 'Dermatitis', 11, '2026-08-03 08:30:03'),
(63, 7, '4.12.11', 'Acne vulgaris', 12, '2026-08-03 08:30:03'),
(64, 7, '4.12.12', 'Psoriasis', 13, '2026-08-03 08:30:03'),
(65, 7, '4.12.13', 'Herpes zoster', 14, '2026-08-03 08:30:03'),
(66, 7, '4.12.14', 'Onychomycosis', 15, '2026-08-03 08:30:03'),
(67, 8, '4.6.1', 'Common tumors of ear nose and throat (ENT)', 1, '2026-08-03 08:30:03'),
(68, 8, '4.6.2', 'Adenitis', 2, '2026-08-03 08:30:03'),
(69, 8, '4.6.3', 'Nasal Polyps', 3, '2026-08-03 08:30:03'),
(70, 8, '4.6.4', 'Peritonsillar', 4, '2026-08-03 08:30:03'),
(71, 8, '4.6.5', 'Tonsillitis', 5, '2026-08-03 08:30:03'),
(72, 8, '4.6.6', 'Otitis Media', 6, '2026-08-03 08:30:03'),
(73, 8, '4.6.7', 'Adenoid Hypertrophy', 7, '2026-08-03 08:30:03'),
(74, 8, '4.6.8', 'Furunculosis', 8, '2026-08-03 08:30:03'),
(75, 8, '4.6.9', 'Foreign bodies of Ear, Nose', 9, '2026-08-03 08:30:03'),
(76, 8, '4.6.9', 'Foreign bodies of Throat', 10, '2026-08-03 08:30:03'),
(77, 8, '4.6.10', 'Epistaxis (Nose Bleeding)', 11, '2026-08-03 08:30:03'),
(78, 9, NULL, 'Anatomy Of the Eye', 1, '2026-08-03 08:30:03'),
(79, 9, '4.7.1', 'Conjunctivitis', 2, '2026-08-03 08:30:03'),
(80, 9, '4.7.2', 'Trachoma', 3, '2026-08-03 08:30:03'),
(81, 9, '4.7.3', 'Stye', 4, '2026-08-03 08:30:03'),
(82, 9, '4.7.4', 'Foreign body in the Eye', 5, '2026-08-03 08:30:03'),
(83, 9, '4.7.5', 'Eye Trauma', 6, '2026-08-03 08:30:03'),
(84, 9, '4.7.6', 'Exophthalmos / Proptosis', 7, '2026-08-03 08:30:03'),
(85, 9, '4.7.7', 'Glaucoma', 8, '2026-08-03 08:30:03'),
(86, 9, '4.7.8', 'Corneal Ulcers', 9, '2026-08-03 08:30:03'),
(87, 9, '4.7.9', 'Cataract', 10, '2026-08-03 08:30:03'),
(88, 10, '4.17.0', 'Resuscitation', 1, '2026-08-03 08:30:03'),
(89, 10, '4.17.1', 'Respiratory distress syndrome', 2, '2026-08-03 08:30:03'),
(90, 10, '4.17.2', 'Broncho pulmonary dysplasia/ chronic lung disease', 3, '2026-08-03 08:30:03'),
(91, 10, '4.17.3', 'Meconium Aspiration Syndrome', 4, '2026-08-03 08:30:03'),
(92, 10, '4.17.4', 'Pulmonary hemorrhage', 5, '2026-08-03 08:30:03'),
(93, 10, '4.17.5', 'Apnea', 6, '2026-08-03 08:30:03'),
(94, 10, '4.17.6', 'Pneumonia', 7, '2026-08-03 08:30:03'),
(95, 10, '4.17.7', 'Asthma', 8, '2026-08-03 08:30:03'),
(96, 11, '4.18.1', 'Sickle cell disease', 1, '2026-08-03 08:30:03'),
(97, 11, '4.18.2', 'Pericarditis', 2, '2026-08-03 08:30:03'),
(98, 11, '4.18.3', 'Rheumatic heart disease', 3, '2026-08-03 08:30:03'),
(99, 12, '4.19.1', 'Congenital Toxoplasmosis', 1, '2026-08-03 08:30:03'),
(100, 12, '4.19.2', 'Intracranial Hemorrhage', 2, '2026-08-03 08:30:03'),
(101, 12, '4.19.3', 'Hypoxic Ischemic encephalopathy and its classifications', 3, '2026-08-03 08:30:03'),
(102, 13, '4.20.1', 'Acute Glomerulonephritis', 1, '2026-08-03 08:30:03'),
(103, 13, '4.20.2', 'Nephrotic Syndrome', 2, '2026-08-03 08:30:03'),
(104, 13, '4.20.3', 'Nephritic Syndrome', 3, '2026-08-03 08:30:03'),
(105, 13, '4.20.4', 'Hydrocele', 4, '2026-08-03 08:30:03'),
(106, 14, '4.21.1', 'Fractures', 1, '2026-08-03 08:30:03'),
(107, 14, '4.21.2', 'Osteopenia of Prematurity (metabolic bone diseases)', 2, '2026-08-03 08:30:03'),
(108, 14, '4.21.3', 'Osteomyelitis', 3, '2026-08-03 08:30:03'),
(109, 14, '4.21.4', 'Osteogenesis Imperfecta', 4, '2026-08-03 08:30:03'),
(110, 15, '4.22.1', 'Introduction to HIV/AIDs in children', 1, '2026-08-03 08:30:03'),
(111, 15, '4.22.2', 'Clinical manifestation of HIV / AIDS in Children', 2, '2026-08-03 08:30:03'),
(112, 15, '4.22.3', 'Opportunistic Infections in Children', 3, '2026-08-03 08:30:03'),
(113, 15, '4.22.4', 'Diagnostic Measures', 4, '2026-08-03 08:30:03'),
(114, 15, '4.22.4', 'Treatment of HIV/AIDS in Children (ARV therapy)', 5, '2026-08-03 08:30:03'),
(115, 15, '4.22.5', 'Prevention and Control of HIV/AIDS', 6, '2026-08-03 08:30:03'),
(116, 15, '4.22.6', 'Counseling in HIV/AIDS', 7, '2026-08-03 08:30:03'),
(117, 16, NULL, 'Hemophilus influenza', 1, '2026-08-03 08:30:03'),
(118, 16, NULL, 'Meningitis', 2, '2026-08-03 08:30:03'),
(119, 16, NULL, 'Intersexual disabilities', 3, '2026-08-03 08:30:03'),
(120, 16, NULL, 'Seizures disorders', 4, '2026-08-03 08:30:03'),
(121, 16, NULL, 'Cerebral palsy', 5, '2026-08-03 08:30:03'),
(122, 17, NULL, 'Diabetes Mellitus & Diabetic Keto Acidosis', 1, '2026-08-03 08:30:03'),
(123, 17, NULL, 'Thyrotoxicosis', 2, '2026-08-03 08:30:03'),
(124, 17, NULL, 'Precocious Puberty', 3, '2026-08-03 08:30:03'),
(125, 18, NULL, 'Nephrotic syndrome', 1, '2026-08-03 08:30:03'),
(126, 18, NULL, 'Nephritic syndrome', 2, '2026-08-03 08:30:03'),
(127, 18, NULL, 'Hydrocele', 3, '2026-08-03 08:30:03'),
(128, 19, NULL, 'Atopic dermatitis', 1, '2026-08-03 08:30:03'),
(129, 19, NULL, 'Eczema', 2, '2026-08-03 08:30:03'),
(130, 19, NULL, 'Skin allergies', 3, '2026-08-03 08:30:03'),
(131, 19, NULL, 'Plant allergies', 4, '2026-08-03 08:30:03'),
(132, 19, NULL, 'Stings and bites', 5, '2026-08-03 08:30:03'),
(133, 20, NULL, 'Glaucoma', 1, '2026-08-03 08:30:03'),
(134, 20, NULL, 'Visual impairment', 2, '2026-08-03 08:30:03'),
(135, 20, NULL, 'Congenital Cataract', 3, '2026-08-03 08:30:03'),
(136, 20, NULL, 'Strabismus', 4, '2026-08-03 08:30:03'),
(137, 20, NULL, 'Eye injuries in children', 5, '2026-08-03 08:30:03'),
(138, 20, NULL, 'Foreign bodies in the eye', 6, '2026-08-03 08:30:03'),
(139, 20, NULL, 'Eye infections', 7, '2026-08-03 08:30:03'),
(140, 20, NULL, 'Care of a child under-going eye surgery', 8, '2026-08-03 08:30:03'),
(141, 21, NULL, 'Hearing impairment', 1, '2026-08-03 08:30:03'),
(142, 21, NULL, 'Removal of foreign bodies from the ear and nose', 2, '2026-08-03 08:30:03'),
(143, 21, NULL, 'Reyes syndrome', 3, '2026-08-03 08:30:03'),
(144, 22, NULL, 'IMCI strategy in health care', 1, '2026-08-03 08:30:03'),
(145, 22, NULL, 'General danger signs', 2, '2026-08-03 08:30:03'),
(146, 22, NULL, 'Assess and classify a sick child 2 months to 5 years', 3, '2026-08-03 08:30:03'),
(147, 22, NULL, 'Treat the Child', 4, '2026-08-03 08:30:03'),
(148, 22, NULL, 'Assess and classify a sick young infant 0-2 months', 5, '2026-08-03 08:30:03'),
(149, 22, NULL, 'Manage HIV/AIDS using IMCI approach', 6, '2026-08-03 08:30:03'),
(150, 23, NULL, 'Introduction', 1, '2026-08-03 08:30:03'),
(151, 23, NULL, 'Suicide and suicidal behaviour', 2, '2026-08-03 08:30:03'),
(152, 23, NULL, 'Violence and aggression of patients / clients', 3, '2026-08-03 08:30:03'),
(153, 23, NULL, 'Panic attacks/disorders', 4, '2026-08-03 08:30:03'),
(154, 23, NULL, 'Catatonic stupor syndrome in schizophrenic patients', 5, '2026-08-03 08:30:03'),
(155, 23, NULL, 'Status epilepticus', 6, '2026-08-03 08:30:03'),
(156, 23, NULL, 'Epilepsy', 7, '2026-08-03 08:30:03'),
(157, 24, NULL, 'Law and Mental illness', 1, '2026-08-03 08:30:03'),
(158, 24, NULL, 'Patient/client\'s rights', 2, '2026-08-03 08:30:03'),
(159, 24, NULL, 'Standards of Care', 3, '2026-08-03 08:30:03'),
(160, 24, NULL, 'Mental Treatment Act', 4, '2026-08-03 08:30:03'),
(161, 25, NULL, 'Autism', 1, '2026-08-03 08:30:03'),
(162, 25, NULL, 'Attention deficit hyperactive disorders', 2, '2026-08-03 08:30:03'),
(163, 25, NULL, 'Mood disorders', 3, '2026-08-03 08:30:03'),
(164, 25, NULL, 'Bipolar Affective Disorder', 4, '2026-08-03 08:30:03'),
(165, 25, NULL, 'Suicide', 5, '2026-08-03 08:30:03'),
(166, 25, NULL, 'Anxiety Disorders', 6, '2026-08-03 08:30:03'),
(167, 25, NULL, 'Post-traumatic stress disorder', 7, '2026-08-03 08:30:03'),
(168, 25, NULL, 'Substance Abuse', 8, '2026-08-03 08:30:03'),
(169, 25, NULL, 'Eating disorders', 9, '2026-08-03 08:30:03'),
(170, 25, NULL, 'Mental Retardation now Intellectual Disability', 10, '2026-08-03 08:30:03'),
(171, 26, NULL, 'Gonadotropin drugs (Subfertility, Ovulation Induction)', 1, '2026-08-03 08:30:03'),
(172, 26, NULL, 'Infertility drugs', 2, '2026-08-03 08:30:03'),
(173, 26, NULL, 'Androgens, Antiandrogens & Anabolic Steroids', 3, '2026-08-03 08:30:03'),
(174, 26, NULL, 'BPH & BPH Drugs', 4, '2026-08-03 08:30:03'),
(175, 26, NULL, 'Erectile Dysfunction Medications', 5, '2026-08-03 08:30:03'),
(176, 26, NULL, 'Contraceptives', 6, '2026-08-03 08:30:03'),
(177, 26, NULL, 'Pregnancy, Labour, and Puerperium Drugs', 7, '2026-08-03 08:30:03'),
(178, 27, NULL, 'Immunity', 1, '2026-08-03 08:30:03'),
(179, 27, NULL, 'Immunization', 2, '2026-08-03 08:30:03'),
(180, 27, NULL, 'Immunological agents (Immunomodulators)', 3, '2026-08-03 08:30:03'),
(181, 27, NULL, 'Adverse reactions', 4, '2026-08-03 08:30:03'),
(182, 27, NULL, 'Antineoplastic Agents (Anticancer Drugs)', 5, '2026-08-03 08:30:03'),
(183, 28, NULL, 'Anxiolytics', 1, '2026-08-03 08:30:03'),
(184, 28, NULL, 'Hypnotics', 2, '2026-08-03 08:30:03'),
(185, 28, NULL, 'Mood stabilizers', 3, '2026-08-03 08:30:03'),
(186, 28, NULL, 'Anti-depressants', 4, '2026-08-03 08:30:03'),
(187, 28, NULL, 'Anti-psychotics', 5, '2026-08-03 08:30:03'),
(188, 28, NULL, 'Anticonvulsants', 6, '2026-08-03 08:30:03'),
(189, 29, NULL, 'Different types of narcotics', 1, '2026-08-03 08:30:03'),
(190, 29, NULL, 'Prescription, Dispensing & Storage of narcotics', 2, '2026-08-03 08:30:03'),
(191, 29, NULL, 'Seminar Question with Dangers of Narcotics', 3, '2026-08-03 08:30:03'),
(192, 29, NULL, 'Legal implications of Narcotics', 4, '2026-08-03 08:30:03'),
(193, 29, NULL, 'Narcotic drug abuse & Management', 5, '2026-08-03 08:30:03'),
(194, 31, NULL, 'Updated Introduction to Primary Health Care', 1, '2026-08-03 08:30:03'),
(195, 31, NULL, 'Concepts of Primary Health Care (Principles, Pillars, Elements & Strategies)', 2, '2026-08-03 08:30:03'),
(196, 31, NULL, 'Planning, Implementation, Monitoring & Evaluation of PHC Activities', 3, '2026-08-03 08:30:03'),
(197, 31, NULL, 'Concept of the community', 4, '2026-08-03 08:30:03'),
(198, 31, NULL, 'Concept of Health (Determinants & Dimensions)', 5, '2026-08-03 08:30:03'),
(199, 31, NULL, 'Health and Disease (Outbreak, Natural History of a disease, Surveillance & Malnutrition)', 6, '2026-08-03 08:30:03'),
(200, 31, NULL, 'Sustainable Development Goals (SDG\'s)', 7, '2026-08-03 08:30:03'),
(201, 31, NULL, 'Integrated Disease Surveillance', 8, '2026-08-03 08:30:03'),
(202, 32, NULL, 'Introduction to community based health care', 1, '2026-08-03 08:30:03'),
(203, 32, NULL, 'Community Approach', 2, '2026-08-03 08:30:03'),
(204, 32, NULL, 'Community Entry', 3, '2026-08-03 08:30:03'),
(205, 32, NULL, 'Community Survey', 4, '2026-08-03 08:30:03'),
(206, 32, NULL, 'Community Assessment', 5, '2026-08-03 08:30:03'),
(207, 32, NULL, 'Community situation Analysis (Diagnosis)', 6, '2026-08-03 08:30:03'),
(208, 32, NULL, 'Community Mobilization', 7, '2026-08-03 08:30:03'),
(209, 32, NULL, 'Community Participation', 8, '2026-08-03 08:30:03'),
(210, 32, NULL, 'Community Organization', 9, '2026-08-03 08:30:03'),
(211, 32, NULL, 'Community Dialogue', 10, '2026-08-03 08:30:03'),
(212, 32, NULL, 'Community Empowerment', 11, '2026-08-03 08:30:03'),
(213, 32, NULL, 'School Health Program', 12, '2026-08-03 08:30:03'),
(214, 32, NULL, 'Home Visiting', 13, '2026-08-03 08:30:03'),
(215, 32, NULL, 'Community based rehabilitative services for disabled and disadvantaged groups', 14, '2026-08-03 08:30:03'),
(216, 33, NULL, 'Introduction to research', 1, '2026-08-03 08:30:03'),
(217, 33, NULL, 'Terminologies', 2, '2026-08-03 08:30:03'),
(218, 33, NULL, 'Research Ethics', 3, '2026-08-03 08:30:03'),
(219, 33, NULL, 'Purpose of studying research', 4, '2026-08-03 08:30:03'),
(220, 33, NULL, 'Research techniques (Qualitative, quantitative and their approaches)', 5, '2026-08-03 08:30:03'),
(221, 34, NULL, 'Steps & Phases in Research Process', 1, '2026-08-03 08:30:03'),
(222, 34, NULL, 'Formulation of research Problem, Topics, Objectives', 2, '2026-08-03 08:30:03'),
(223, 34, NULL, 'Writing a research proposal & Marking Guide', 3, '2026-08-03 08:30:03'),
(224, 34, NULL, 'Preliminary Pages', 4, '2026-08-03 08:30:03'),
(225, 34, NULL, 'Chapter One: Introduction & Sections', 5, '2026-08-03 08:30:03'),
(226, 34, NULL, 'Chapter Two: Literature review', 6, '2026-08-03 08:30:03'),
(227, 34, NULL, 'Chapter Three: Methodology', 7, '2026-08-03 08:30:03'),
(228, 34, NULL, 'References/Referencing', 8, '2026-08-03 08:30:03'),
(229, 34, NULL, 'Appendices & Consent Form', 9, '2026-08-03 08:30:03'),
(230, 34, NULL, 'Research Proposal Defense', 10, '2026-08-03 08:30:03'),
(231, 34, NULL, 'Chapter Four: Results', 11, '2026-08-03 08:30:03'),
(232, 34, NULL, 'Chapter Five: Discussion, Conclusion and Recommendations', 12, '2026-08-03 08:30:03'),
(233, 34, NULL, 'Research report & Differences', 13, '2026-08-03 08:30:03'),
(234, 35, NULL, 'Teaching and Learning process', 1, '2026-08-03 08:30:03'),
(235, 35, NULL, 'Principles of teaching and learning (Characteristics & Maxims)', 2, '2026-08-03 08:30:03'),
(236, 35, NULL, 'Teaching Learning Methods', 3, '2026-08-03 08:30:03'),
(237, 35, NULL, 'Communication and Human relations', 4, '2026-08-03 08:30:03'),
(238, 35, NULL, 'Teaching technology', 5, '2026-08-03 08:30:03'),
(239, 35, NULL, 'Assessment and Evaluation', 6, '2026-08-03 08:30:03'),
(240, 39, NULL, 'Teaching aids', 1, '2026-08-03 08:30:03'),
(241, 41, NULL, 'Introduction to Palliative Care', 1, '2026-08-03 08:30:03'),
(242, 41, NULL, 'Importance\'s, Roles, Attributes and Components of Palliative Care', 2, '2026-08-03 08:30:03'),
(243, 41, NULL, 'Principles of Palliative Care', 3, '2026-08-03 08:30:03'),
(244, 41, NULL, 'Models of Palliative Care', 4, '2026-08-03 08:30:03'),
(245, 41, NULL, 'Communication – Preparation of family to make important decisions', 5, '2026-08-03 08:30:03'),
(246, 42, NULL, 'Hospice movement', 1, '2026-08-03 08:30:03'),
(247, 42, NULL, 'Philosophy of hospice', 2, '2026-08-03 08:30:03'),
(248, 42, NULL, 'Goals of hospice', 3, '2026-08-03 08:30:03'),
(249, 42, NULL, 'Holistic care approach', 4, '2026-08-03 08:30:03'),
(250, 43, NULL, 'Introduction to Pain', 1, '2026-08-03 08:30:03'),
(251, 43, NULL, 'Pain Assessment', 2, '2026-08-03 08:30:03'),
(252, 43, NULL, 'Pain Management', 3, '2026-08-03 08:30:03'),
(253, 43, NULL, 'Psychosocial support to terminally ill patients', 4, '2026-08-03 08:30:03'),
(254, 44, NULL, 'Introduction to Palliative care emergencies & Severe uncontrolled pain', 1, '2026-08-03 08:30:03'),
(255, 44, NULL, 'Spinal cord compression', 2, '2026-08-03 08:30:03'),
(256, 44, NULL, 'Hypercalcemia', 3, '2026-08-03 08:30:03'),
(257, 44, NULL, 'Hemorrhage', 4, '2026-08-03 08:30:03'),
(258, 44, NULL, 'Superior Vena Cava Obstruction (SVCO)', 5, '2026-08-03 08:30:03'),
(259, 45, NULL, 'Principles', 1, '2026-08-03 08:30:03'),
(260, 45, NULL, 'GIT (Nausea and vomiting, Diarrhea, Anorexia, Constipation, Hiccups)', 2, '2026-08-03 08:30:03'),
(261, 45, NULL, 'Respiratory system (Dyspnea, Cough, Breathlessness, Rattle)', 3, '2026-08-03 08:30:03'),
(262, 45, NULL, 'Nervous system (Delirium, Depression, Insomnia)', 4, '2026-08-03 08:30:03'),
(263, 45, NULL, 'Skin & Integumentary system (Non-healing wound, Pruritis, Wound Care)', 5, '2026-08-03 08:30:03'),
(264, 45, NULL, 'Genitourinary system (Incontinence, Retention)', 6, '2026-08-03 08:30:03'),
(265, 46, NULL, 'Anger', 1, '2026-08-03 08:30:03'),
(266, 46, NULL, 'Spiritual needs and Johari Window', 2, '2026-08-03 08:30:03'),
(267, 46, NULL, 'Bereavement', 3, '2026-08-03 08:30:03'),
(268, 47, NULL, 'Hastened death', 1, '2026-08-03 08:30:03'),
(269, 47, NULL, 'Assisted death', 2, '2026-08-03 08:30:03'),
(270, 47, NULL, 'Advanced directives', 3, '2026-08-03 08:30:03'),
(271, 47, NULL, 'Will Making', 4, '2026-08-03 08:30:03'),
(272, 48, NULL, 'Nearing death awareness', 1, '2026-08-03 08:30:03'),
(273, 48, NULL, 'Euthanasia', 2, '2026-08-03 08:30:03'),
(274, 48, NULL, 'Grief', 3, '2026-08-03 08:30:03'),
(275, 48, NULL, 'Death and dying', 4, '2026-08-03 08:30:03'),
(276, 48, NULL, 'Breaking sad news', 5, '2026-08-03 08:30:03'),
(277, 49, NULL, 'Introduction to Disaster in Nursing', 1, '2026-08-03 08:30:03'),
(278, 49, NULL, 'Natural disaster', 2, '2026-08-03 08:30:03'),
(279, 49, NULL, 'Man made disaster', 3, '2026-08-03 08:30:03'),
(280, 50, NULL, 'The Stages of Disaster Management', 1, '2026-08-03 08:30:03'),
(281, 50, NULL, 'Roles played by each stakeholder in Disaster Management', 2, '2026-08-03 08:30:03'),
(282, 50, NULL, 'Mass Causality Incident & Triage', 3, '2026-08-03 08:30:03'),
(283, 50, NULL, 'Community Participation in Disaster Management', 4, '2026-08-03 08:30:03'),
(284, 50, NULL, 'Requirements for disaster preparedness', 5, '2026-08-03 08:30:03'),
(285, 51, NULL, 'Natural prevention', 1, '2026-08-03 08:30:03'),
(286, 51, NULL, 'Artificial prevention', 2, '2026-08-03 08:30:03'),
(287, 52, NULL, 'Introduction (Aims, Principles, Components, Elements)', 1, '2026-08-03 08:30:03'),
(288, 52, NULL, 'Identification of occupational health hazards in different work places', 2, '2026-08-03 08:30:03'),
(289, 52, NULL, 'Types of occupational health hazards', 3, '2026-08-03 08:30:03'),
(290, 52, NULL, 'Prevention and control of occupational health hazards', 4, '2026-08-03 08:30:03'),
(291, 52, NULL, 'Occupational Hazard Control', 5, '2026-08-03 08:30:03'),
(292, 52, NULL, 'Workers compensation act', 6, '2026-08-03 08:30:03'),
(293, 52, NULL, 'Occupational Health Service Program', 7, '2026-08-03 08:30:03'),
(294, 52, NULL, 'PPE\'s and Fire Extinguishers', 8, '2026-08-03 08:30:03'),
(295, 52, NULL, 'Injection Safety and Disposal', 9, '2026-08-03 08:30:03'),
(296, 52, NULL, 'Waste Management', 10, '2026-08-03 08:30:03'),
(297, 52, NULL, 'Work related injuries and Fatalities', 11, '2026-08-03 08:30:03'),
(298, 52, NULL, 'Psychosocial aspects of work: Job stress and associated conditions', 12, '2026-08-03 08:30:03'),
(299, 53, NULL, 'Introduction to Gynaecology', 1, '2026-08-03 08:30:03'),
(300, 53, NULL, 'History, Examinations and Investigations', 2, '2026-08-03 08:30:03'),
(301, 53, NULL, 'Menstruation & Menstruation Disorders', 3, '2026-08-03 08:30:04'),
(302, 53, NULL, 'Abortions', 4, '2026-08-03 08:30:04'),
(303, 53, NULL, 'Ectopic Pregnancy', 5, '2026-08-03 08:30:04'),
(304, 53, NULL, 'Cervical Erosion, Trauma and Polyps', 6, '2026-08-03 08:30:04'),
(305, 53, NULL, 'Pelvic Inflammatory Diseases', 7, '2026-08-03 08:30:04'),
(306, 53, NULL, 'Infertility', 8, '2026-08-03 08:30:04'),
(307, 53, NULL, 'Vesico-Vaginal Fistula (VVF) and Recto-Vaginal fistula (RVF)', 9, '2026-08-03 08:30:04'),
(308, 53, NULL, 'Cancers of Reproductive Health Organs (Cervix, Breast, Uterus and Ovaries)', 10, '2026-08-03 08:30:04'),
(309, 53, NULL, 'Fibroids', 11, '2026-08-03 08:30:04'),
(310, 53, NULL, 'Congenital abnormalities of the reproductive organs', 12, '2026-08-03 08:30:04'),
(311, 53, NULL, 'Prolapse of the uterus, cervix and bladder', 13, '2026-08-03 08:30:04'),
(312, 53, NULL, 'Ovarian cyst', 14, '2026-08-03 08:30:04'),
(313, 55, NULL, 'Introduction to Reproductive Health', 1, '2026-08-03 08:30:04'),
(314, 55, NULL, 'Integration of Reproductive Health Services', 2, '2026-08-03 08:30:04'),
(315, 55, NULL, 'Adolescent Reproductive Health and Development', 3, '2026-08-03 08:30:04'),
(316, 55, NULL, 'Adolescent friendly health services', 4, '2026-08-03 08:30:04'),
(317, 55, NULL, 'Adolescent Sexuality', 5, '2026-08-03 08:30:04'),
(318, 55, NULL, 'Vulnerable groups', 6, '2026-08-03 08:30:04'),
(319, 55, NULL, 'Community involvement in adolescent reproductive health', 7, '2026-08-03 08:30:04'),
(320, 57, NULL, 'Management of HIV/AIDs', 1, '2026-08-03 08:30:04'),
(321, 57, NULL, 'Opportunistic Infections and Hepatitis', 2, '2026-08-03 08:30:04'),
(322, 57, NULL, 'Post exposure prophylaxis (PEP and ARV\'s)', 3, '2026-08-03 08:30:04'),
(323, 57, NULL, 'PMTCT and Care of Infant', 4, '2026-08-03 08:30:04'),
(324, 59, NULL, 'Introduction to Health Service Management', 1, '2026-08-03 08:30:04'),
(325, 59, NULL, 'Management theories and Styles', 2, '2026-08-03 08:30:04'),
(326, 59, NULL, 'Principles of Management', 3, '2026-08-03 08:30:04'),
(327, 59, NULL, 'Levels and Functions of Management', 4, '2026-08-03 08:30:04'),
(328, 59, NULL, 'Human resource management', 5, '2026-08-03 08:30:04'),
(329, 59, NULL, 'Human Resource Planning', 6, '2026-08-03 08:30:04'),
(330, 59, NULL, 'Staff recruitment process', 7, '2026-08-03 08:30:04'),
(331, 59, NULL, 'Financial management, Budgeting and Accountability', 8, '2026-08-03 08:30:04'),
(332, 59, NULL, 'Management of equipment and supplies', 9, '2026-08-03 08:30:04'),
(333, 59, NULL, 'Transport management', 10, '2026-08-03 08:30:04'),
(334, 59, NULL, 'Management of Infrastructure', 11, '2026-08-03 08:30:04'),
(335, 59, NULL, 'Integrated disease response and surveillance', 12, '2026-08-03 08:30:04'),
(336, 59, NULL, 'Key government policies (Uganda Healthcare System)', 13, '2026-08-03 08:30:04'),
(337, 60, NULL, 'Introduction, Kinds, Power and Authority', 1, '2026-08-03 08:30:04'),
(338, 60, NULL, 'Leadership theories', 2, '2026-08-03 08:30:04'),
(339, 60, NULL, 'Team process', 3, '2026-08-03 08:30:04'),
(340, 60, NULL, 'Styles of leadership', 4, '2026-08-03 08:30:04'),
(341, 60, NULL, 'Staff Delegation', 5, '2026-08-03 08:30:04'),
(342, 60, NULL, 'Conflict and conflict resolution', 6, '2026-08-03 08:30:04'),
(343, 60, NULL, 'Negotiation Skills', 7, '2026-08-03 08:30:04'),
(344, 60, NULL, 'Support Supervision', 8, '2026-08-03 08:30:04'),
(345, 61, NULL, 'Introduction to Entrepreneurship', 1, '2026-08-03 08:30:04'),
(346, 61, NULL, 'Entrepreneur as a Manager and Entrepreneurial Process', 2, '2026-08-03 08:30:04'),
(347, 61, NULL, 'Small business in the economy', 3, '2026-08-03 08:30:04'),
(348, 61, NULL, 'Entrepreneurship Skills', 4, '2026-08-03 08:30:04'),
(349, 62, NULL, 'Business idea and Opportunity', 1, '2026-08-03 08:30:04'),
(350, 62, NULL, 'Types of Business Enterprises', 2, '2026-08-03 08:30:04'),
(351, 62, NULL, 'Business or Business Enterprise', 3, '2026-08-03 08:30:04'),
(352, 62, NULL, 'Business planning', 4, '2026-08-03 08:30:04'),
(353, 62, NULL, 'Successful strategies for small business', 5, '2026-08-03 08:30:04'),
(354, 62, NULL, 'Start-ups and franchises (Permits/license)', 6, '2026-08-03 08:30:04'),
(355, 62, NULL, 'Buying an existing business', 7, '2026-08-03 08:30:04'),
(356, 62, NULL, 'Forming and protecting a business', 8, '2026-08-03 08:30:04'),
(357, 63, NULL, 'Customer Care', 1, '2026-08-03 08:30:04'),
(358, 63, NULL, 'Marketing', 2, '2026-08-03 08:30:04'),
(359, 63, NULL, 'Money matters for small business', 3, '2026-08-03 08:30:04'),
(360, 63, NULL, 'Business exits and realizing value', 4, '2026-08-03 08:30:04');

-- --------------------------------------------------------

--
-- Table structure for table `topics`
--

CREATE TABLE `topics` (
  `id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `topics`
--

INSERT INTO `topics` (`id`, `module_id`, `title`, `sort_order`, `created_at`) VALUES
(1, 1, 'Applying nursing process to the management of patients', 1, '2026-08-03 08:30:02'),
(2, 1, 'Perform specialized nursing care', 2, '2026-08-03 08:30:03'),
(3, 1, 'Conditions affecting the nervous system', 3, '2026-08-03 08:30:03'),
(4, 1, 'Medical conditions affecting the endocrine system', 4, '2026-08-03 08:30:03'),
(5, 1, 'Medical diseases affecting the renal system', 5, '2026-08-03 08:30:03'),
(6, 1, 'Conditions of the lymphatic system', 6, '2026-08-03 08:30:03'),
(7, 1, 'Conditions of the Musculo-skeletal system', 7, '2026-08-03 08:30:03'),
(8, 3, 'Surgical conditions of the Ear, Nose, and Throat (ENT)', 1, '2026-08-03 08:30:03'),
(9, 3, 'Conditions of the eye', 2, '2026-08-03 08:30:03'),
(10, 11, 'Pediatric condition of the respiratory system', 1, '2026-08-03 08:30:03'),
(11, 11, 'Pediatric conditions of the cardio vascular system', 2, '2026-08-03 08:30:03'),
(12, 11, 'Neurological disorders in children', 3, '2026-08-03 08:30:03'),
(13, 11, 'Genital urinary conditions in children', 4, '2026-08-03 08:30:03'),
(14, 11, 'Bone conditions', 5, '2026-08-03 08:30:03'),
(15, 11, 'Managing children living with HIV /AIDS', 6, '2026-08-03 08:30:03'),
(16, 11, 'Medical conditions affecting the nervous system (children)', 7, '2026-08-03 08:30:03'),
(17, 11, 'Endocrine disorders affecting the children', 8, '2026-08-03 08:30:03'),
(18, 11, 'Urinary disorders affecting the children', 9, '2026-08-03 08:30:03'),
(19, 11, 'Integumentary disorders of the skin (children)', 10, '2026-08-03 08:30:03'),
(20, 11, 'Eye conditions (children)', 11, '2026-08-03 08:30:03'),
(21, 11, 'Conditions of the ear and Nose (children)', 12, '2026-08-03 08:30:03'),
(22, 11, 'Integrated Management of Childhood illnesses (IMCI)', 13, '2026-08-03 08:30:03'),
(23, 4, 'Psychiatric emergencies', 1, '2026-08-03 08:30:03'),
(24, 4, 'Legal issues in psychiatry', 2, '2026-08-03 08:30:03'),
(25, 4, 'Mental health disorders in children', 3, '2026-08-03 08:30:03'),
(26, 4, 'Drugs used in the reproductive system', 4, '2026-08-03 08:30:03'),
(27, 4, 'Immunological drugs', 5, '2026-08-03 08:30:03'),
(28, 4, 'Psychopharmacology', 6, '2026-08-03 08:30:03'),
(29, 4, 'Narcotics', 7, '2026-08-03 08:30:03'),
(30, 4, 'Poison and non-medical use of drugs', 8, '2026-08-03 08:30:03'),
(31, 6, 'Introduction to Primary health care', 1, '2026-08-03 08:30:03'),
(32, 6, 'Community Based Health Care (CBHC)', 2, '2026-08-03 08:30:03'),
(33, 7, 'Introduction to nursing research', 1, '2026-08-03 08:30:03'),
(34, 7, 'Writing a research proposal and report', 2, '2026-08-03 08:30:03'),
(35, 7, 'Introduction to teaching methodology and Concept of education', 3, '2026-08-03 08:30:03'),
(36, 7, 'Philosophy and psychology of education', 4, '2026-08-03 08:30:03'),
(37, 7, 'Andrology', 5, '2026-08-03 08:30:03'),
(38, 7, 'Teaching-learning (educational) objectives', 6, '2026-08-03 08:30:03'),
(39, 7, 'Educational technology and Teaching aids', 7, '2026-08-03 08:30:03'),
(40, 7, 'Planning teaching', 8, '2026-08-03 08:30:03'),
(41, 8, 'Palliative Care concepts', 1, '2026-08-03 08:30:03'),
(42, 8, 'The hospice concept', 2, '2026-08-03 08:30:03'),
(43, 8, 'Pain', 3, '2026-08-03 08:30:03'),
(44, 8, 'Palliative care emergencies', 4, '2026-08-03 08:30:03'),
(45, 8, 'Symptoms of terminally ill patients', 5, '2026-08-03 08:30:03'),
(46, 8, 'Common conditions in palliative care', 6, '2026-08-03 08:30:03'),
(47, 8, 'Ethics at the end of life', 7, '2026-08-03 08:30:03'),
(48, 8, 'Terminal care', 8, '2026-08-03 08:30:03'),
(49, 9, 'Disaster', 1, '2026-08-03 08:30:03'),
(50, 9, 'Disaster management', 2, '2026-08-03 08:30:03'),
(51, 9, 'Disaster prevention', 3, '2026-08-03 08:30:03'),
(52, 9, 'Introduction to occupational health hazards', 4, '2026-08-03 08:30:03'),
(53, 12, 'Manage women with gynecological conditions', 1, '2026-08-03 08:30:03'),
(54, 12, 'Applied anatomy of the female and male reproductive organ', 2, '2026-08-03 08:30:04'),
(55, 12, 'Adolescent reproductive health', 3, '2026-08-03 08:30:04'),
(56, 12, 'Family planning', 4, '2026-08-03 08:30:04'),
(57, 12, 'Sexually transmitted infections (STI)', 5, '2026-08-03 08:30:04'),
(58, 12, 'Post abortion care', 6, '2026-08-03 08:30:04'),
(59, 13, 'Management', 1, '2026-08-03 08:30:04'),
(60, 13, 'Leadership', 2, '2026-08-03 08:30:04'),
(61, 13, 'Concept of entrepreneurship', 3, '2026-08-03 08:30:04'),
(62, 13, 'Creating entrepreneurial small business', 4, '2026-08-03 08:30:04'),
(63, 13, 'Managing people and resources', 5, '2026-08-03 08:30:04');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `phone`, `role`, `course`, `year`, `semester`, `institution`, `reg_number`, `access_expiry`, `notes_viewed`, `classes_attended`, `created_at`) VALUES
(1, 'Admin User', 'admin@nursespro.ac.ug', '$2y$10$IWOcUlzGjqDr2LZIiN1XAecUGgu7jDrKbKrNKTCS1ZBKxSkNx31mC', '0392972444', 'superadmin', 'Diploma in Nursing (Direct)', 'Year 2', 'Semester 1', 'Mulago School of Nursing', 'ADM001', '2027-02-02 23:33:28', 42, 18, '2026-08-02 21:33:28'),
(2, 'Sarah Nakato', 'sarah@demo.com', '$2y$10$m7YcG2axrPiQrUJL5wVXT.QXOXIxYpuuqdujEWcUCTqsmsj7wqzFe', '0701234567', 'student', 'Diploma in Nursing (Extension)', 'Year 1', 'Semester 1', 'Mulago School of Nursing', 'MID22001', '2026-11-02 23:33:28', 20, 7, '2026-08-02 21:33:28'),
(3, 'John Okello', 'john@demo.com', '$2y$10$NgNbIoTNVO6m3LdWGZ2afOQwS04tDM4GeAWwNi5ke5CtI0JUtOeD.', '0712345678', 'tutor', 'Diploma in Nursing (Extension)', 'Year 3', 'Semester 1', 'Kampala International', 'NUR21009', '2027-02-02 23:33:28', 28, 12, '2026-08-02 21:33:28'),
(4, 'Grace Apio', 'grace@demo.com', '$2y$10$KmqchfM9FuiQPW/4UJ6YD.dzkBUlSdejIoCOXcjLmbo/ShVcc5QwO', '0789012345', 'student', 'Diploma in Nursing (Direct)', 'Year 2', 'Semester 2', 'Makerere University', 'NUR23007', '2026-01-14 23:33:28', 5, 2, '2026-08-02 21:33:29'),
(5, 'Peter Musoke', 'peter@demo.com', '$2y$10$x4lN8iwjWn/SQDIyKw9TaurJfu9.nf3fwSJxnpLZYjWLXNlFsFt66', '0700333444', 'student', 'Diploma in Nursing (Extension)', 'Year 1', 'Semester 1', 'Mulago School of Nursing', 'DNE24001', '2026-10-02 23:33:28', 9, 4, '2026-08-02 21:33:29'),
(6, 'Immaculate Nabirye', 'immaculate@demo.com', '$2y$10$V3uDvBK7rvJIkdxsBOeok.9IWDKF4G1XoJJxoOK0BtjmfBKGqpr.S', '0701444555', 'student', 'Diploma in Nursing (Extension)', 'Year 1', 'Semester 1', 'Mulago School of Nursing', 'DNE24002', '2026-11-02 23:33:28', 11, 6, '2026-08-02 21:33:29'),
(7, 'Brian Ssekandi', 'brian@demo.com', '$2y$10$b65ow02eudC2byBSWbqg7uii10rN5O0JZji2e6Q5Gh0aDczhwcVtq', '0702555666', 'student', 'Diploma in Nursing (Extension)', 'Year 1', 'Semester 2', 'Kampala International', 'DNE24003', '2026-11-03 08:43:39', 6, 3, '2026-08-02 21:33:29'),
(8, 'Diana Achieng', 'diana@demo.com', '$2y$10$1fEmd/4oQDEa4qEOOJJSJ.v/ZVubvLSzwraoiGs3LgnHLAYacQQhO', '0703666777', 'student', 'Diploma in Nursing (Extension)', 'Year 2', 'Semester 1', 'Mulago School of Nursing', 'DNE23004', '2026-10-02 23:33:28', 17, 9, '2026-08-02 21:33:29'),
(9, 'Moses Kato', 'moses@demo.com', '$2y$10$dK3B8JLtsaH8zZ8sNn7PmeSR9fCg6T9zmZtIcV/zCZnnIOS1hEI6a', '0704777888', 'student', 'Diploma in Nursing (Extension)', 'Year 2', 'Semester 1', 'Makerere University', 'DNE23005', '2026-06-18 23:33:28', 3, 1, '2026-08-02 21:33:29'),
(10, 'Ritah Nansubuga', 'ritah@demo.com', '$2y$10$cqpBjqahVgJUSvwLvCmZl.DtA8/JbyBlTEd2d4ohTx/mqor6gHbym', '0705888999', 'student', 'Diploma in Nursing (Extension)', 'Year 1', 'Semester 2', 'Mulago School of Nursing', 'DNE24006', '2026-11-02 23:33:28', 8, 5, '2026-08-02 21:33:29'),
(11, 'Emmanuel Byaruhanga', 'emmanuel@demo.com', '$2y$10$K3XhLbnT2lnRMF..AZkdAOPE64/iTSyfFmvMLZ/8M4LyueulUxuNi', '0706999000', 'student', 'Diploma in Nursing (Direct)', 'Year 1', 'Semester 1', 'Kampala International', 'NUR24010', '2026-09-02 23:33:28', 4, 2, '2026-08-02 21:33:29'),
(15, 'Sedu Otolo', 'ot.sedrick@gmail.com', '$2y$10$F2bG1mz5jDExAR7VhguGKeIBsCu60WpzABaWOIG.eQbrwIylyTJSq', '0777676206', 'student', 'Certificate in Nursing', 'Year 1', 'Semester 1', 'Mulago School of Nursing', 'NUR11', NULL, 0, 0, '2026-08-03 09:26:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `module_id` (`module_id`);

--
-- Indexes for table `curriculum_courses`
--
ALTER TABLE `curriculum_courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `curriculum_modules`
--
ALTER TABLE `curriculum_modules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_code` (`course_id`,`code`);

--
-- Indexes for table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `module_id` (`module_id`),
  ADD KEY `uploaded_by` (`uploaded_by`),
  ADD KEY `fk_notes_topic` (`topic_id`),
  ADD KEY `fk_notes_subtopic` (`subtopic_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `student_notes`
--
ALTER TABLE `student_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `subtopics`
--
ALTER TABLE `subtopics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `topic_id` (`topic_id`);

--
-- Indexes for table `topics`
--
ALTER TABLE `topics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `module_id` (`module_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `curriculum_courses`
--
ALTER TABLE `curriculum_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `curriculum_modules`
--
ALTER TABLE `curriculum_modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `student_notes`
--
ALTER TABLE `student_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `subtopics`
--
ALTER TABLE `subtopics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=362;

--
-- AUTO_INCREMENT for table `topics`
--
ALTER TABLE `topics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `curriculum_modules` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `curriculum_modules`
--
ALTER TABLE `curriculum_modules`
  ADD CONSTRAINT `curriculum_modules_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `curriculum_courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `fk_notes_subtopic` FOREIGN KEY (`subtopic_id`) REFERENCES `subtopics` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_notes_topic` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `curriculum_modules` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notes_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_notes`
--
ALTER TABLE `student_notes`
  ADD CONSTRAINT `student_notes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subtopics`
--
ALTER TABLE `subtopics`
  ADD CONSTRAINT `subtopics_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `topics`
--
ALTER TABLE `topics`
  ADD CONSTRAINT `topics_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `curriculum_modules` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
