-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 24 avr. 2025 à 23:51
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
(342, 45, 'student', 'Registered', 'Étudiant Marouane Haddad 3 (ID 45) inscrit avec email marouanehaddad08+2@gmail.com', '2025-04-24 22:07:22');

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
(40, 'Analyse', 13, 'Easy', '2025-04-24 02:09:26');

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
(56, 39, 80, 'PDF', 'Introduction', '../Uploads/pdfs/1745454711_document8.pdf', '2025-04-24 01:31:51');

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
(81, 40, 'Test', NULL, '2025-04-24 02:09:26');

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

--
-- Déchargement des données de la table `device_attempts`
--

INSERT INTO `device_attempts` (`id`, `student_id`, `device_fingerprint`, `device_info`, `ip_address`, `latitude`, `longitude`, `attempted_at`, `status`) VALUES
(7, 42, '16c093bd15fdb2fe0e095ce2c780b3aa', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '::1', 35.7354719, -5.8892765, '2025-04-24 01:46:10', 'approved');

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

--
-- Déchargement des données de la table `quiz_submissions`
--

INSERT INTO `quiz_submissions` (`id`, `quiz_id`, `student_id`, `response_path`, `grade`, `feedback`, `submitted_at`, `graded_at`) VALUES
(21, 19, 42, '../Uploads/quiz_submissions/Marouane Haddad - Examen Algebre v1.pdf', 18, 'T.bien', '2025-04-24 01:54:19', '2025-04-24 01:55:54');

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
  `device_id` varchar(36) NOT NULL,
  `device_name` varchar(255) DEFAULT NULL,
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

INSERT INTO `students` (`id`, `full_name`, `email`, `phone`, `dob`, `password`, `status`, `device_id`, `device_name`, `latitude`, `longitude`, `level_id`, `gender`, `city`, `university`, `filiere`, `created_at`, `session_status`) VALUES
(42, 'Marouane Haddad', 'marouanehaddad08@gmail.com', '0613508702', '2009-04-24', '$2y$10$wUwKZTvkHvM7QXEfC1/GJehsrhhfgrcaVUwWDFM9KOq15Tre5zJ/2', 'approved', '9e471bbddb32cf5bc453ea260583c215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 35.73487500, -5.88978700, 9, 'Male', 'Tanger', NULL, NULL, '2025-04-24 01:39:38', 'active'),
(43, 'Wiame Rachade', 'marouanehaddad08+@gmail.com', '0613508702', '2001-02-12', '$2y$10$EuxPvTICOL0Ucq8qmxvmxuah6oqYsR19X9X.iJrErBFHDfTcA1T0e', 'pending', '9e471bbddb32cf5bc453ea260583c215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 35.73490500, -5.88978000, NULL, 'Male', 'Paris', 'Ibn Tofail', 'Math Informatique', '2025-04-24 21:50:45', 'active'),
(44, 'Marouane Haddad 2', 'marouanehaddad08+1@gmail.com', '0613508702', '2002-02-12', '$2y$10$X1O1MczFE5hJNXWGjmKiDOdYtB74maorpaCB1Un3CRj5EqAKCHC.i', 'pending', '9e471bbddb32cf5bc453ea260583c215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 35.73490500, -5.88978000, NULL, 'Female', 'Tanger', 'Abdelmalek Essaadi University', 'Informatique', '2025-04-24 22:06:24', 'active'),
(45, 'Marouane Haddad 3', 'marouanehaddad08+2@gmail.com', '0613508702', '2002-02-12', '$2y$10$Yg43agFl9tHO89e5FjaGne9O5D4TdwrEOsu71EoUfgOSMUv0IPufG', 'pending', '9e471bbddb32cf5bc453ea260583c215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 35.73490500, -5.88978000, NULL, 'Male', 'Tanger', 'ESI', 'Data', '2025-04-24 22:07:22', 'active');

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
(59, 42, 39, '2025-04-24 01:49:23');

-- --------------------------------------------------------

--
-- Structure de la table `student_devices`
--

CREATE TABLE `student_devices` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `device_fingerprint` varchar(255) NOT NULL,
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

INSERT INTO `student_devices` (`id`, `student_id`, `device_fingerprint`, `device_info`, `ip_address`, `latitude`, `longitude`, `created_at`, `status`) VALUES
(24, 42, '9e471bbddb32cf5bc453ea260583c215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1', 35.734875, -5.889787, '2025-04-24 01:39:38', 'approved'),
(25, 42, '16c093bd15fdb2fe0e095ce2c780b3aa', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '::1', 35.7354719, -5.8892765, '2025-04-24 01:47:27', 'approved'),
(26, 43, '9e471bbddb32cf5bc453ea260583c215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1', 35.734905, -5.88978, '2025-04-24 21:50:45', 'approved'),
(27, 44, '9e471bbddb32cf5bc453ea260583c215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1', 35.734905, -5.88978, '2025-04-24 22:06:24', 'approved'),
(28, 45, '9e471bbddb32cf5bc453ea260583c215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1', 35.734905, -5.88978, '2025-04-24 22:07:22', 'approved');

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
(54, 42, 13, '2025-04-24 01:49:23', 1);

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
(15, 'Informatiques', 9, '2025-04-24 01:27:21');

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
(36, 42, '2025-04-24 01:41:01', '2025-04-24 01:43:58', 35.734875, -5.889787, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(37, 42, '2025-04-24 01:44:14', '2025-04-24 01:44:39', 35.734875, -5.889787, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(38, 42, '2025-04-24 01:44:50', '2025-04-24 21:49:45', 35.734875, -5.889787, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(39, 42, '2025-04-24 01:47:45', NULL, 35.7354719, -5.8892765, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '::1');

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
  ADD UNIQUE KEY `student_id` (`student_id`,`device_fingerprint`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=343;

--
-- AUTO_INCREMENT pour la table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT pour la table `course_contents`
--
ALTER TABLE `course_contents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT pour la table `course_folders`
--
ALTER TABLE `course_folders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT pour la table `device_attempts`
--
ALTER TABLE `device_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `levels`
--
ALTER TABLE `levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT pour la table `student_courses`
--
ALTER TABLE `student_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT pour la table `student_devices`
--
ALTER TABLE `student_devices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT pour la table `student_subjects`
--
ALTER TABLE `student_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT pour la table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

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
-- Contraintes pour la table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_ibfk_1` FOREIGN KEY (`level_id`) REFERENCES `levels` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
