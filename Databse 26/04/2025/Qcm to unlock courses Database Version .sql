-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 26 avr. 2025 à 19:34
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `zouhair_elearning`
--

-- --------------------------------------------------------

--
-- Structure de la table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_type` enum('admin','student') NOT NULL,
  `action` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `user_type`, `action`, `details`, `timestamp`) VALUES
(322, 1, 'admin', 'Added level', 'Added level: Bac+2', '2025-04-24 01:24:30'),
(323, 1, 'admin', 'Added level', 'Added level: Bac+3', '2025-04-24 01:24:35'),
(324, 1, 'admin', 'Added level', 'Added level: Autres', '2025-04-24 01:24:42'),
(325, 1, 'admin', 'Edited level', 'Edited level ID 11: Bac+4', '2025-04-24 01:25:14'),
(326, 1, 'admin', 'Deleted level', 'Deleted level ID 11: Bac+4', '2025-04-24 01:25:31'),
(327, 1, 'admin', 'Added subject', 'Added subject: Mathématiques for level ID 9', '2025-04-24 01:27:08'),
(328, 1, 'admin', 'Added subject', 'Added subject: Physiques for level ID 9', '2025-04-24 01:27:15'),
(329, 1, 'admin', 'Added subject', 'Added subject: Informatiques for level ID 9', '2025-04-24 01:27:21'),
(330, 1, 'admin', 'Added course', 'Added course: Algebre for subject ID 13', '2025-04-24 01:29:45'),
(331, 1, 'admin', 'Edited course', 'Edited course ID 39: Algebre', '2025-04-24 01:31:51'),
(332, 42, 'student', 'Registered', 'Étudiant Marouane Haddad (ID 42) inscrit avec email marouanehaddad08@gmail.com', '2025-04-24 01:39:38'),
(333, 42, 'student', 'Viewed course', 'Viewed course ID 39: Algebre', '2025-04-24 01:49:50'),
(334, 42, 'student', 'Viewed quiz', 'Viewed quiz ID 19: Examen Algebre', '2025-04-24 01:51:51'),
(335, 42, 'student', 'Viewed quiz', 'Viewed quiz ID 19: Examen Algebre', '2025-04-24 01:53:05'),
(336, 42, 'student', 'Viewed quiz', 'Viewed quiz ID 19: Examen Algebre', '2025-04-24 01:54:19'),
(337, 42, 'student', 'Viewed quiz', 'Viewed quiz ID 19: Examen Algebre', '2025-04-24 01:54:19'),
(338, 42, 'student', 'Viewed quiz', 'Viewed quiz ID 19: Examen Algebre', '2025-04-24 01:57:13'),
(339, 1, 'admin', 'Added course', 'Added course: Analyse for subject ID 13', '2025-04-24 02:09:26'),
(340, 43, 'student', 'Registered', 'Étudiant Wiame Rachade (ID 43) inscrit avec email marouanehaddad08+@gmail.com', '2025-04-24 21:50:45'),
(341, 44, 'student', 'Registered', 'Étudiant Marouane Haddad 2 (ID 44) inscrit avec email marouanehaddad08+1@gmail.com', '2025-04-24 22:06:24'),
(342, 45, 'student', 'Registered', 'Étudiant Marouane Haddad 3 (ID 45) inscrit avec email marouanehaddad08+2@gmail.com', '2025-04-24 22:07:22'),
(343, 46, 'student', 'Registered', 'Étudiant Marouane Haddad 4 (ID 46) inscrit avec email marouanehaddad08+3@gmail.com', '2025-04-24 22:56:49'),
(344, 46, 'student', 'auto_logout', 'Déconnexion automatique due à inactivité ou activité sur onglets non-app', '2025-04-25 01:35:13'),
(345, 46, 'student', 'auto_logout', 'Déconnexion automatique due à inactivité ou activité sur onglets non-app', '2025-04-25 01:35:15'),
(346, 46, 'student', 'auto_logout', 'Déconnexion automatique due à inactivité ou activité sur onglets non-app', '2025-04-25 01:35:18'),
(347, 46, 'student', 'auto_logout', 'Déconnexion automatique due à inactivité ou activité sur onglets non-app', '2025-04-25 01:35:19'),
(348, 46, 'student', 'auto_logout', 'Déconnexion automatique due à inactivité ou activité sur onglets non-app', '2025-04-25 01:35:34'),
(349, 46, 'student', 'auto_logout', 'Déconnexion automatique due à inactivité ou activité sur onglets non-app', '2025-04-25 01:35:36'),
(350, 46, 'student', 'Viewed course', 'Viewed course ID 39: Algebre', '2025-04-25 22:19:30'),
(351, 1, 'admin', 'Added subject', 'Added subject: Data for level ID 10', '2025-04-26 16:03:28'),
(352, 45, 'student', 'Viewed course', 'Viewed course ID 40: Analyse', '2025-04-26 16:21:02'),
(353, 45, 'student', 'Viewed course', 'Viewed course ID 39: Algebre', '2025-04-26 16:21:11'),
(354, 44, 'student', 'Viewed course', 'Viewed course ID 40: Analyse', '2025-04-26 16:35:12'),
(355, 1, 'admin', 'Added course', 'Added course: thermo for subject ID 14', '2025-04-26 16:40:53'),
(356, 1, 'admin', 'Added course', 'Added course: thermo 2 for subject ID 14', '2025-04-26 16:41:06'),
(357, 1, 'admin', 'Added course', 'Added course: thermo 3 for subject ID 14', '2025-04-26 16:41:25'),
(358, 43, 'student', 'Viewed course', 'Viewed course ID 41: thermo', '2025-04-26 16:45:30'),
(359, 43, 'student', 'Viewed course', 'Viewed course ID 42: thermo 2', '2025-04-26 16:45:55'),
(360, 47, 'student', 'Registered', 'Étudiant Marouane Haddad 1 (ID 47) inscrit avec email marouanehaddad08+1@gmail.com', '2025-04-26 17:25:05'),
(361, 1, 'admin', 'Added course', 'Added course: Proba for subject ID 13', '2025-04-26 17:26:10'),
(362, 1, 'admin', 'Edited course', 'Edited course ID 40: Analyse', '2025-04-26 17:26:28'),
(363, 1, 'admin', 'Edited course', 'Edited course ID 40: Analyse', '2025-04-26 17:27:21'),
(364, 1, 'admin', 'Edited course', 'Edited course ID 44: Proba', '2025-04-26 17:27:37'),
(365, 47, 'student', 'Viewed course', 'Viewed course ID 39: Algebre', '2025-04-26 17:34:52'),
(366, 47, 'student', 'Viewed course', 'Viewed course ID 40: Analyse', '2025-04-26 17:36:20'),
(367, 48, 'student', 'Registered', 'Étudiant Marouane Haddad 2 (ID 48) inscrit avec email marouanehaddad08+2@gmail.com', '2025-04-26 17:46:14'),
(368, 49, 'student', 'Registered', 'Étudiant Marouane Haddad 3 (ID 49) inscrit avec email marouanehaddad08+3@gmail.com', '2025-04-26 18:15:01'),
(369, 49, 'student', 'Viewed course', 'Viewed course ID 40: Analyse', '2025-04-26 18:16:13'),
(370, 50, 'student', 'Registered', 'Étudiant Marouane Haddad 4 (ID 50) inscrit avec email marouanehaddad08+4@gmail.com', '2025-04-26 18:29:46'),
(371, 50, 'student', 'Viewed course', 'Viewed course ID 44: Proba', '2025-04-26 18:33:25');

-- --------------------------------------------------------

--
-- Structure de la table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'zouhair', 'admin123', '2025-04-09 14:11:32');

-- --------------------------------------------------------

--
-- Structure de la table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `difficulty` enum('Easy','Medium','Hard') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `courses`
--

INSERT INTO `courses` (`id`, `title`, `subject_id`, `difficulty`, `created_at`) VALUES
(39, 'Algebre', 13, 'Easy', '2025-04-24 01:29:45'),
(40, 'Analyse', 13, 'Easy', '2025-04-24 02:09:26'),
(41, 'thermo', 14, 'Easy', '2025-04-26 16:40:53'),
(42, 'thermo 2', 14, 'Easy', '2025-04-26 16:41:06'),
(43, 'thermo 3', 14, 'Easy', '2025-04-26 16:41:25'),
(44, 'Proba', 13, 'Easy', '2025-04-26 17:26:10');

-- --------------------------------------------------------

--
-- Structure de la table `course_contents`
--

CREATE TABLE `course_contents` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `content_type` enum('PDF','Video') NOT NULL,
  `content_name` varchar(255) NOT NULL,
  `content_path` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `course_contents`
--

INSERT INTO `course_contents` (`id`, `course_id`, `folder_id`, `content_type`, `content_name`, `content_path`, `created_at`) VALUES
(52, 39, 78, 'PDF', 'Intro', '../Uploads/pdfs/1745454711_17439544931.pdf', '2025-04-24 01:31:51'),
(53, 39, 78, 'PDF', 'Exemples', '../Uploads/pdfs/1745454711_1743955932الملخص.pdf', '2025-04-24 01:31:51'),
(54, 39, 78, 'Video', 'Introduction', 'https://www.youtube.com/embed/D7C6NysH3t8', '2025-04-24 01:31:51'),
(55, 39, 79, 'PDF', 'Exos', '../Uploads/pdfs/1745454711_ListedesCandidatsconvoqusCINmasqu1.pdf', '2025-04-24 01:31:51'),
(56, 39, 80, 'PDF', 'Introduction', '../Uploads/pdfs/1745454711_document8.pdf', '2025-04-24 01:31:51'),
(57, 40, 81, 'PDF', 'Introduction', '../Uploads/pdfs/1745684841_17439544931.pdf', '2025-04-26 17:27:21'),
(58, 40, 86, 'PDF', 'Computer Phone', '../Uploads/pdfs/1745684841_ListedesCandidatsconvoqusCINmasqu12.pdf', '2025-04-26 17:27:21'),
(59, 44, 85, 'PDF', 'test', '../Uploads/pdfs/1745684857_protected.pdf', '2025-04-26 17:27:37');

-- --------------------------------------------------------

--
-- Structure de la table `course_folders`
--

CREATE TABLE `course_folders` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `course_folders`
--

INSERT INTO `course_folders` (`id`, `course_id`, `name`, `description`, `created_at`) VALUES
(78, 39, 'Matrices', NULL, '2025-04-24 01:29:45'),
(79, 39, 'Vecteurs', NULL, '2025-04-24 01:29:45'),
(80, 39, 'Espaces', NULL, '2025-04-24 01:29:45'),
(81, 40, 'Test', NULL, '2025-04-24 02:09:26'),
(82, 41, 'intro', NULL, '2025-04-26 16:40:53'),
(83, 42, 'exo', NULL, '2025-04-26 16:41:06'),
(84, 43, 'problemes', NULL, '2025-04-26 16:41:25'),
(85, 44, 'cours1', NULL, '2025-04-26 17:26:10'),
(86, 40, 'Seance 2', NULL, '2025-04-26 17:26:28');

-- --------------------------------------------------------

--
-- Structure de la table `device_attempts`
--

CREATE TABLE `device_attempts` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `device_fingerprint` varchar(255) NOT NULL,
  `device_info` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `attempted_at` datetime NOT NULL,
  `status` enum('pending','approved','denied') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `levels`
--

CREATE TABLE `levels` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `levels`
--

INSERT INTO `levels` (`id`, `name`, `description`, `created_at`) VALUES
(9, 'Bac+2', 'Niveau bac+2', '2025-04-24 01:24:30'),
(10, 'Bac+3', '', '2025-04-24 01:24:35');

-- --------------------------------------------------------

--
-- Structure de la table `qcm`
--

CREATE TABLE `qcm` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `course_before_id` int(11) NOT NULL,
  `course_after_id` int(11) NOT NULL,
  `threshold` float NOT NULL COMMENT 'Score minimum pour passer (ex: 70%)',
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `qcm`
--

INSERT INTO `qcm` (`id`, `title`, `subject_id`, `course_before_id`, `course_after_id`, `threshold`, `description`, `created_at`, `updated_at`) VALUES
(11, 'Qcm to unlock Analyse', 13, 39, 40, 80, '', '2025-04-26 17:31:35', '2025-04-26 17:31:53'),
(12, 'Qcm to unlock Proba', 13, 40, 44, 80, '', '2025-04-26 17:34:15', '2025-04-26 17:34:15');

-- --------------------------------------------------------

--
-- Structure de la table `qcm_choices`
--

CREATE TABLE `qcm_choices` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `choice_text` varchar(255) NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `qcm_choices`
--

INSERT INTO `qcm_choices` (`id`, `question_id`, `choice_text`, `is_correct`, `created_at`) VALUES
(63, 17, 'a', 1, '2025-04-26 17:31:53'),
(64, 17, 'b', 0, '2025-04-26 17:31:53'),
(65, 17, 'c', 0, '2025-04-26 17:31:53'),
(66, 17, 'd', 0, '2025-04-26 17:31:53'),
(67, 18, 'e', 0, '2025-04-26 17:31:53'),
(68, 18, 'f', 0, '2025-04-26 17:31:53'),
(69, 18, 'g', 1, '2025-04-26 17:31:53'),
(70, 18, 'h', 0, '2025-04-26 17:31:53'),
(71, 19, 'i', 0, '2025-04-26 17:31:53'),
(72, 19, 'j', 1, '2025-04-26 17:31:53'),
(73, 19, 'k', 1, '2025-04-26 17:31:53'),
(74, 19, 'l', 0, '2025-04-26 17:31:53'),
(75, 20, 'm', 1, '2025-04-26 17:31:53'),
(76, 20, 'n', 1, '2025-04-26 17:31:53'),
(77, 20, 'o', 1, '2025-04-26 17:31:53'),
(78, 20, 'p', 1, '2025-04-26 17:31:53'),
(79, 21, 'q', 1, '2025-04-26 17:31:53'),
(80, 21, 'r', 0, '2025-04-26 17:31:53'),
(81, 21, 's', 0, '2025-04-26 17:31:53'),
(82, 21, 't', 0, '2025-04-26 17:31:53'),
(83, 22, 'a', 0, '2025-04-26 17:34:15'),
(84, 22, 'b', 0, '2025-04-26 17:34:15'),
(85, 22, 'c', 0, '2025-04-26 17:34:15'),
(86, 22, 'd', 1, '2025-04-26 17:34:15'),
(87, 23, 'e', 1, '2025-04-26 17:34:15'),
(88, 23, 'f', 1, '2025-04-26 17:34:15'),
(89, 23, 'g', 0, '2025-04-26 17:34:15'),
(90, 23, 'h', 0, '2025-04-26 17:34:15'),
(91, 24, 'i', 0, '2025-04-26 17:34:15'),
(92, 24, 'j', 1, '2025-04-26 17:34:15'),
(93, 24, 'k', 1, '2025-04-26 17:34:15'),
(94, 24, 'l', 1, '2025-04-26 17:34:15'),
(95, 25, 'm', 1, '2025-04-26 17:34:15'),
(96, 25, 'n', 0, '2025-04-26 17:34:15'),
(97, 25, 'o', 0, '2025-04-26 17:34:15'),
(98, 25, 'p', 0, '2025-04-26 17:34:15'),
(99, 26, 'q', 0, '2025-04-26 17:34:15'),
(100, 26, 'r', 0, '2025-04-26 17:34:15'),
(101, 26, 's', 1, '2025-04-26 17:34:15'),
(102, 26, 't', 0, '2025-04-26 17:34:15');

-- --------------------------------------------------------

--
-- Structure de la table `qcm_questions`
--

CREATE TABLE `qcm_questions` (
  `id` int(11) NOT NULL,
  `qcm_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `order` int(11) NOT NULL DEFAULT 1 COMMENT 'Pour trier les questions',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `qcm_questions`
--

INSERT INTO `qcm_questions` (`id`, `qcm_id`, `question_text`, `order`, `created_at`) VALUES
(17, 11, 'q1', 1, '2025-04-26 17:31:53'),
(18, 11, 'q2', 2, '2025-04-26 17:31:53'),
(19, 11, 'q3', 3, '2025-04-26 17:31:53'),
(20, 11, 'q4', 4, '2025-04-26 17:31:53'),
(21, 11, 'q5', 5, '2025-04-26 17:31:53'),
(22, 12, 'Q1', 1, '2025-04-26 17:34:15'),
(23, 12, 'Q2', 2, '2025-04-26 17:34:15'),
(24, 12, 'Q3', 3, '2025-04-26 17:34:15'),
(25, 12, 'Q4', 4, '2025-04-26 17:34:15'),
(26, 12, 'Q5', 5, '2025-04-26 17:34:15');

-- --------------------------------------------------------

--
-- Structure de la table `qcm_submissions`
--

CREATE TABLE `qcm_submissions` (
  `id` int(11) NOT NULL,
  `qcm_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `score` float DEFAULT NULL COMMENT 'Score obtenu (pourcentage)',
  `submitted_at` datetime DEFAULT current_timestamp(),
  `passed` tinyint(1) DEFAULT 0 COMMENT '1 si seuil atteint, 0 sinon'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `qcm_submissions`
--

INSERT INTO `qcm_submissions` (`id`, `qcm_id`, `student_id`, `score`, `submitted_at`, `passed`) VALUES
(8, 11, 47, 100, '2025-04-26 17:36:06', 1),
(9, 12, 47, 80, '2025-04-26 17:36:44', 1),
(12, 11, 48, 100, '2025-04-26 17:52:57', 1),
(13, 12, 48, 60, '2025-04-26 17:53:26', 0),
(14, 12, 48, 60, '2025-04-26 18:08:36', 0),
(15, 12, 48, 20, '2025-04-26 18:10:07', 0),
(16, 12, 48, 20, '2025-04-26 18:11:08', 0),
(17, 12, 48, 80, '2025-04-26 18:12:01', 1),
(18, 11, 49, 100, '2025-04-26 18:16:12', 1),
(19, 11, 50, 100, '2025-04-26 18:32:58', 1),
(20, 12, 50, 80, '2025-04-26 18:33:17', 1);

-- --------------------------------------------------------

--
-- Structure de la table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `pdf_path` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `start_datetime` datetime NOT NULL DEFAULT '2025-01-01 00:00:00',
  `duration_hours` decimal(4,2) NOT NULL DEFAULT 1.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `quizzes`
--

INSERT INTO `quizzes` (`id`, `title`, `subject_id`, `description`, `pdf_path`, `created_at`, `updated_at`, `start_datetime`, `duration_hours`) VALUES
(19, 'Examen Algebre', 13, '', '../uploads/quizzes/68098b0410919_1743954493_1.pdf', '2025-04-24 01:51:16', '2025-04-24 01:51:16', '2025-04-24 01:53:00', 1.00);

-- --------------------------------------------------------

--
-- Structure de la table `quiz_submissions`
--

CREATE TABLE `quiz_submissions` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `response_path` varchar(255) NOT NULL,
  `grade` float DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `submitted_at` datetime DEFAULT current_timestamp(),
  `graded_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `level_id` int(11) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `university` varchar(100) DEFAULT NULL,
  `filiere` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `session_status` varchar(20) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `students`
--

INSERT INTO `students` (`id`, `full_name`, `email`, `phone`, `dob`, `password`, `status`, `latitude`, `longitude`, `level_id`, `gender`, `city`, `university`, `filiere`, `created_at`, `session_status`) VALUES
(47, 'Marouane Haddad 1', 'marouanehaddad08+1@gmail.com', '0613508702', '2002-02-12', '$2y$10$XtlGwNQeqm5LMztmVATD8ujE960dIhRB4Y5ZI19GFSwHqbpo1k.VS', 'approved', 35.73408900, -5.88688100, 9, 'Male', 'Tanger', 'Abdelmalek Essaadi University', 'Informatique', '2025-04-26 17:25:05', 'active'),
(48, 'Marouane Haddad 2', 'marouanehaddad08+2@gmail.com', '0613508702', '2002-02-12', '$2y$10$J8xXqYkkC37122a4i42ECO4lX12UPAqsxmV7tVskN.vIvmnUFnEey', 'approved', 35.73408900, -5.88688100, 9, 'Female', 'Tanger', 'Abdelmalek Essaadi University', 'Biologie', '2025-04-26 17:46:14', 'active'),
(49, 'Marouane Haddad 3', 'marouanehaddad08+3@gmail.com', '0613508702', '2002-02-12', '$2y$10$kpn/ejXZO4FUiwx066nSb.X5Uyt7Xr/6TZnDQxh2SG5.TLdtTJ9hi', 'approved', 35.73406200, -5.88668000, 9, 'Male', 'Tanger', 'Al Akhawayn University', 'Mathématiques', '2025-04-26 18:15:01', 'active'),
(50, 'Marouane Haddad 4', 'marouanehaddad08+4@gmail.com', '0613508702', '2002-02-12', '$2y$10$rnvIC6xd/At9B0rwjA9pEOSbh93h/eW/QPs7vxq2yCigssnloAQyu', 'approved', 35.73406200, -5.88668000, 9, 'Male', 'Tanger', 'Moulay Ismail University', 'Physique', '2025-04-26 18:29:46', 'active');

-- --------------------------------------------------------

--
-- Structure de la table `student_courses`
--

CREATE TABLE `student_courses` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `assigned_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `student_courses`
--

INSERT INTO `student_courses` (`id`, `student_id`, `course_id`, `assigned_at`) VALUES
(70, 47, 39, '2025-04-26 17:34:42'),
(71, 47, 40, '2025-04-26 17:36:06'),
(72, 47, 44, '2025-04-26 17:36:44'),
(73, 48, 39, '2025-04-26 17:46:36'),
(74, 48, 40, '2025-04-26 17:52:57'),
(75, 48, 44, '2025-04-26 18:12:01'),
(82, 50, 39, '2025-04-26 18:32:30'),
(83, 50, 40, '2025-04-26 18:32:58'),
(84, 50, 44, '2025-04-26 18:33:17');

-- --------------------------------------------------------

--
-- Structure de la table `student_devices`
--

CREATE TABLE `student_devices` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `device_fingerprint` varchar(255) NOT NULL,
  `device_name` varchar(50) DEFAULT NULL,
  `device_info` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `status` enum('approved','denied') DEFAULT 'approved'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `student_devices`
--

INSERT INTO `student_devices` (`id`, `student_id`, `device_fingerprint`, `device_name`, `device_info`, `ip_address`, `latitude`, `longitude`, `created_at`, `status`) VALUES
(32, 47, '9e471bbddb32cf5bc453ea260583c215', 'Device Computer', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1', 35.734089, -5.886881, '2025-04-26 17:25:05', 'approved'),
(33, 48, '9e471bbddb32cf5bc453ea260583c215', 'Device 1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1', 35.734089, -5.886881, '2025-04-26 17:46:14', 'approved'),
(34, 49, '9e471bbddb32cf5bc453ea260583c215', 'Device 1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1', 35.734062, -5.88668, '2025-04-26 18:15:01', 'approved'),
(35, 50, '9e471bbddb32cf5bc453ea260583c215', 'Device 1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1', 35.734062, -5.88668, '2025-04-26 18:29:46', 'approved');

-- --------------------------------------------------------

--
-- Structure de la table `student_subjects`
--

CREATE TABLE `student_subjects` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `assigned_at` datetime DEFAULT current_timestamp(),
  `all_courses` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `student_subjects`
--

INSERT INTO `student_subjects` (`id`, `student_id`, `subject_id`, `assigned_at`, `all_courses`) VALUES
(59, 47, 13, '2025-04-26 17:34:42', 1),
(60, 48, 13, '2025-04-26 17:46:36', 1),
(69, 49, 13, '2025-04-26 18:28:31', 0),
(71, 50, 13, '2025-04-26 18:32:21', 1);

-- --------------------------------------------------------

--
-- Structure de la table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `level_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `subjects`
--

INSERT INTO `subjects` (`id`, `name`, `level_id`, `created_at`) VALUES
(10, 'Mathématiques', 6, '2025-04-24 00:54:03'),
(11, 'Physiques', 7, '2025-04-24 00:54:11'),
(12, 'Informatiques', 8, '2025-04-24 00:54:18'),
(13, 'Mathématiques', 9, '2025-04-24 01:27:08'),
(14, 'Physiques', 9, '2025-04-24 01:27:15'),
(15, 'Informatiques', 9, '2025-04-24 01:27:21'),
(16, 'Data', 10, '2025-04-26 16:03:28');

-- --------------------------------------------------------

--
-- Structure de la table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `login_time` datetime NOT NULL,
  `logout_time` datetime DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `device_info` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user_sessions`
--

INSERT INTO `user_sessions` (`id`, `student_id`, `login_time`, `logout_time`, `latitude`, `longitude`, `device_info`, `ip_address`) VALUES
(61, 47, '2025-04-26 17:25:12', '2025-04-26 17:45:50', 35.734089, -5.886881, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(62, 48, '2025-04-26 17:46:19', '2025-04-26 18:14:12', 35.734089, -5.886881, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(63, 49, '2025-04-26 18:15:09', '2025-04-26 18:29:18', 35.734062, -5.88668, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(64, 50, '2025-04-26 18:29:52', NULL, 35.734062, -5.88668, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Index pour la table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Index pour la table `course_contents`
--
ALTER TABLE `course_contents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `folder_id` (`folder_id`);

--
-- Index pour la table `course_folders`
--
ALTER TABLE `course_folders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Index pour la table `device_attempts`
--
ALTER TABLE `device_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Index pour la table `levels`
--
ALTER TABLE `levels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Index pour la table `qcm`
--
ALTER TABLE `qcm`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `course_before_id` (`course_before_id`),
  ADD KEY `course_after_id` (`course_after_id`);

--
-- Index pour la table `qcm_choices`
--
ALTER TABLE `qcm_choices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Index pour la table `qcm_questions`
--
ALTER TABLE `qcm_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `qcm_id` (`qcm_id`);

--
-- Index pour la table `qcm_submissions`
--
ALTER TABLE `qcm_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `qcm_id` (`qcm_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Index pour la table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Index pour la table `quiz_submissions`
--
ALTER TABLE `quiz_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Index pour la table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `level_id` (`level_id`);

--
-- Index pour la table `student_courses`
--
ALTER TABLE `student_courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`,`course_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Index pour la table `student_devices`
--
ALTER TABLE `student_devices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`,`device_fingerprint`),
  ADD UNIQUE KEY `student_id_2` (`student_id`,`device_fingerprint`);

--
-- Index pour la table `student_subjects`
--
ALTER TABLE `student_subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`,`subject_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Index pour la table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`,`level_id`),
  ADD KEY `level_id` (`level_id`);

--
-- Index pour la table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=372;

--
-- AUTO_INCREMENT pour la table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT pour la table `course_contents`
--
ALTER TABLE `course_contents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT pour la table `course_folders`
--
ALTER TABLE `course_folders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT pour la table `device_attempts`
--
ALTER TABLE `device_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `levels`
--
ALTER TABLE `levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `qcm`
--
ALTER TABLE `qcm`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `qcm_choices`
--
ALTER TABLE `qcm_choices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT pour la table `qcm_questions`
--
ALTER TABLE `qcm_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT pour la table `qcm_submissions`
--
ALTER TABLE `qcm_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT pour la table `quiz_submissions`
--
ALTER TABLE `quiz_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT pour la table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT pour la table `student_courses`
--
ALTER TABLE `student_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT pour la table `student_devices`
--
ALTER TABLE `student_devices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT pour la table `student_subjects`
--
ALTER TABLE `student_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT pour la table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `course_contents`
--
ALTER TABLE `course_contents`
  ADD CONSTRAINT `course_contents_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_contents_ibfk_2` FOREIGN KEY (`folder_id`) REFERENCES `course_folders` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `course_folders`
--
ALTER TABLE `course_folders`
  ADD CONSTRAINT `course_folders_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `device_attempts`
--
ALTER TABLE `device_attempts`
  ADD CONSTRAINT `device_attempts_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `qcm`
--
ALTER TABLE `qcm`
  ADD CONSTRAINT `qcm_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `qcm_ibfk_2` FOREIGN KEY (`course_before_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `qcm_ibfk_3` FOREIGN KEY (`course_after_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `qcm_choices`
--
ALTER TABLE `qcm_choices`
  ADD CONSTRAINT `qcm_choices_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `qcm_questions` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `qcm_questions`
--
ALTER TABLE `qcm_questions`
  ADD CONSTRAINT `qcm_questions_ibfk_1` FOREIGN KEY (`qcm_id`) REFERENCES `qcm` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `qcm_submissions`
--
ALTER TABLE `qcm_submissions`
  ADD CONSTRAINT `qcm_submissions_ibfk_1` FOREIGN KEY (`qcm_id`) REFERENCES `qcm` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `qcm_submissions_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `quiz_submissions`
--
ALTER TABLE `quiz_submissions`
  ADD CONSTRAINT `quiz_submissions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_submissions_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`level_id`) REFERENCES `levels` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `student_courses`
--
ALTER TABLE `student_courses`
  ADD CONSTRAINT `student_courses_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_courses_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `student_devices`
--
ALTER TABLE `student_devices`
  ADD CONSTRAINT `student_devices_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `student_subjects`
--
ALTER TABLE `student_subjects`
  ADD CONSTRAINT `student_subjects_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_subjects_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
