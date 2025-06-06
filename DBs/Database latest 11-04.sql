-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 11 avr. 2025 à 16:53
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
(1, 1, 'admin', 'Logged in', 'Admin Zouhair logged in', '2025-04-09 14:11:33'),
(2, 2, 'student', 'Viewed course', 'Sara viewed Mechanics Intro', '2025-04-09 14:11:33'),
(3, 3, 'student', 'Logged in', 'Omar logged in', '2025-04-09 14:11:33'),
(4, 1, 'admin', 'Validated student', 'Validated student ID 1 with level ID 2', '2025-04-09 16:08:36'),
(5, 1, 'admin', 'Added course', 'Added course: A for subject ID 4', '2025-04-09 16:09:02'),
(6, 1, 'admin', 'Added course', 'Added course: B for subject ID 4', '2025-04-09 16:16:06'),
(7, 1, 'admin', 'Validated student', 'Validated student ID 1 with level ID 1', '2025-04-09 16:17:22'),
(8, 1, 'admin', 'Added course', 'Added course: C for subject ID 3', '2025-04-09 16:17:38'),
(9, 1, 'admin', 'Added course', 'Added course: D for subject ID 3', '2025-04-09 16:20:38'),
(10, 1, 'admin', 'Edited course', 'Edited course ID 9: D', '2025-04-09 16:28:04'),
(11, 1, 'admin', 'Added course', 'Added course: aze for subject ID 3', '2025-04-09 16:40:52'),
(12, 1, 'student', 'screenshot', 'Screenshot taken on page 2 of aze', '2025-04-10 23:41:01'),
(13, 1, 'admin', 'Validated student', 'Validated student ID 6 with level ID 1', '2025-04-11 00:40:36'),
(14, 1, 'admin', 'Validated student', 'Validated student ID 4 with level ID 1', '2025-04-11 01:39:13'),
(15, 1, 'admin', 'Validated student', 'Validated student ID 8 with level ID 1', '2025-04-11 01:39:55'),
(16, 1, 'admin', 'Validated student', 'Validated student ID 9 with level ID 1', '2025-04-11 02:19:07'),
(17, 1, 'admin', 'Logged out', 'Admin logged out', '2025-04-11 02:19:16'),
(18, 1, 'admin', 'Validated student', 'Validated student ID 11 with level ID 1', '2025-04-11 02:22:21'),
(19, 1, 'admin', 'Logged out', 'Admin logged out', '2025-04-11 02:43:33'),
(20, 1, 'admin', 'Added course', 'Added course: axert for subject ID 3', '2025-04-11 02:46:39'),
(21, 1, 'admin', 'Added course', 'Added course: x for subject ID 1', '2025-04-11 02:47:45'),
(22, 15, 'admin', 'Screenshot taken', 'Captured page 1 of course \'axert\' (ID: 11)', '2025-04-11 03:02:50'),
(23, 1, 'admin', 'Added course', 'Added course: 01 for subject ID 3', '2025-04-11 03:26:00'),
(24, 1, 'admin', 'Added course', 'Added course: axe for subject ID 1', '2025-04-11 11:31:23'),
(25, 1, 'admin', 'Added course', 'Added course: axe for subject ID 3', '2025-04-11 11:31:34'),
(26, 1, 'admin', 'Added course', 'Added course: axe for subject ID 4', '2025-04-11 11:49:15'),
(27, 1, 'admin', 'Added course', 'Added course: axe for subject ID 1', '2025-04-11 11:49:52'),
(28, 1, 'admin', 'Added course', 'Added course: axe for subject ID 3', '2025-04-11 11:50:23'),
(29, 1, 'admin', 'Added course', 'Added course: aze for subject ID 1', '2025-04-11 12:05:50'),
(30, 1, 'admin', 'Added course', 'Added course: aze for subject ID 3', '2025-04-11 12:14:02'),
(31, 1, 'admin', 'Added course', 'Added course: last one added for subject ID 3', '2025-04-11 12:26:03'),
(32, 1, 'admin', 'Added course', 'Added course: x for subject ID 3', '2025-04-11 12:26:43'),
(33, 1, 'admin', 'Added course', 'Added course: last one added maths for subject ID 1', '2025-04-11 12:28:09'),
(34, 1, 'admin', 'Added course', 'Added course: testing video  for subject ID 3', '2025-04-11 12:50:08'),
(35, 1, 'admin', 'Screenshot taken', 'Captured page 3 of course \'last one added maths\' (ID: 23)', '2025-04-11 13:08:46'),
(36, 1, 'admin', 'Added course', 'Added course: both for subject ID 3', '2025-04-11 15:04:58'),
(37, 17, 'admin', 'Screenshot taken', 'Captured page 2 of course \'both\' (ID: 25)', '2025-04-11 15:05:24'),
(38, 1, 'admin', 'Added course', 'Added course: 1only one for subject ID 3', '2025-04-11 15:16:52'),
(39, 1, 'admin', 'Added course', 'Added course: x for subject ID 5', '2025-04-11 15:50:25');

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
(1, 'Algebra Basics', 1, 'Easy', '2025-04-09 14:11:33'),
(2, 'Mechanics Intro', 2, 'Medium', '2025-04-09 14:11:33'),
(3, 'Python Programming', 3, 'Medium', '2025-04-09 14:11:33'),
(4, 'Calculus Advanced', 4, 'Hard', '2025-04-09 14:11:33'),
(5, 'Quantum Physics', 5, 'Hard', '2025-04-09 14:11:33'),
(6, 'A', 4, 'Easy', '2025-04-09 16:09:02'),
(7, 'B', 4, 'Easy', '2025-04-09 16:16:06'),
(8, 'C', 3, 'Easy', '2025-04-09 16:17:38'),
(9, 'D', 3, 'Easy', '2025-04-09 16:20:38'),
(10, 'aze', 3, 'Easy', '2025-04-09 16:40:52'),
(11, 'axert', 3, 'Easy', '2025-04-11 02:46:39'),
(12, 'x', 1, 'Easy', '2025-04-11 02:47:45'),
(13, '01', 3, 'Easy', '2025-04-11 03:26:00'),
(14, 'axe', 1, 'Easy', '2025-04-11 11:31:23'),
(15, 'axe', 3, 'Easy', '2025-04-11 11:31:34'),
(16, 'axe', 4, 'Easy', '2025-04-11 11:49:15'),
(17, 'axe', 1, 'Easy', '2025-04-11 11:49:52'),
(18, 'axe', 3, 'Easy', '2025-04-11 11:50:23'),
(19, 'aze', 1, 'Easy', '2025-04-11 12:05:50'),
(20, 'aze', 3, 'Easy', '2025-04-11 12:14:02'),
(21, 'last one added', 3, 'Easy', '2025-04-11 12:26:03'),
(22, 'x', 3, 'Easy', '2025-04-11 12:26:43'),
(23, 'last one added maths', 1, 'Easy', '2025-04-11 12:28:09'),
(24, 'testing video ', 3, 'Easy', '2025-04-11 12:50:08'),
(25, 'both', 3, 'Easy', '2025-04-11 15:04:58'),
(26, '1only one', 3, 'Easy', '2025-04-11 15:16:52'),
(27, 'x', 5, 'Easy', '2025-04-11 15:50:25');

-- --------------------------------------------------------

--
-- Structure de la table `course_contents`
--

CREATE TABLE `course_contents` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `content_type` enum('PDF','Video') NOT NULL,
  `content_path` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `course_contents`
--

INSERT INTO `course_contents` (`id`, `course_id`, `content_type`, `content_path`, `created_at`) VALUES
(1, 1, 'PDF', '/uploads/pdfs/algebra_basics.pdf', '2025-04-09 14:11:33'),
(2, 2, 'Video', 'https://youtube.com/watch?v=mech101', '2025-04-09 14:11:33'),
(3, 3, 'PDF', '/uploads/pdfs/python_intro.pdf', '2025-04-09 14:11:33'),
(4, 4, 'PDF', '/uploads/pdfs/calculus_advanced.pdf', '2025-04-09 14:11:33'),
(5, 5, 'Video', 'https://youtube.com/watch?v=quantum101', '2025-04-09 14:11:33'),
(6, 6, 'PDF', '../uploads/pdfs/قرار فتح المباراة (1).pdf', '2025-04-09 16:09:02'),
(7, 7, 'PDF', '../uploads/pdfs/BL L\'ENSA TANGER - Copie.pdf', '2025-04-09 16:16:06'),
(8, 8, 'PDF', '../uploads/pdfs/قرار فتح المباراة (1).pdf', '2025-04-09 16:17:38'),
(9, 9, 'Video', 'https://www.youtube.com/watch?v=LAIL6aHua-U', '2025-04-09 16:20:38'),
(10, 10, 'PDF', '../uploads/pdfs/قرار فتح المباراة (1).pdf', '2025-04-09 16:40:52'),
(11, 11, 'PDF', '../uploads/pdfs/oncf-voyages-ismail haddad.pdf', '2025-04-11 02:46:39'),
(12, 12, 'PDF', '../uploads/pdfs/CIN MAROUANE HADDAD.pdf', '2025-04-11 02:47:45'),
(13, 13, 'PDF', '../uploads/pdfs/oncf-voyages-ismail haddad.pdf', '2025-04-11 03:26:00'),
(14, 14, 'PDF', '../uploads/pdfs/قرار فتح المباراة (1).pdf', '2025-04-11 11:31:23'),
(15, 15, 'PDF', '../uploads/pdfs/قرار فتح المباراة (1).pdf', '2025-04-11 11:31:34'),
(16, 16, 'PDF', '../uploads/pdfs/قرار فتح المباراة (1).pdf', '2025-04-11 11:49:15'),
(17, 17, 'PDF', '../uploads/pdfs/قرار فتح المباراة (1).pdf', '2025-04-11 11:49:52'),
(18, 18, 'PDF', '../uploads/pdfs/قرار فتح المباراة (1).pdf', '2025-04-11 11:50:23'),
(19, 19, 'PDF', '../uploads/pdfs/قرار فتح المباراة (1).pdf', '2025-04-11 12:05:50'),
(20, 20, 'PDF', '../uploads/pdfs/Detail_consultation.pdf', '2025-04-11 12:14:02'),
(21, 21, 'PDF', '../uploads/pdfs/Detail_consultation.pdf', '2025-04-11 12:26:03'),
(22, 22, 'PDF', '../uploads/pdfs/قرار فتح المباراة (1).pdf', '2025-04-11 12:26:43'),
(23, 23, 'PDF', '../uploads/pdfs/قرار فتح المباراة (1).pdf', '2025-04-11 12:28:09'),
(24, 24, 'Video', 'https://www.youtube.com/watch?v=rXxLCMf2CXY', '2025-04-11 12:50:08'),
(32, 25, 'PDF', '../uploads/pdfs/قرار فتح المباراة (1).pdf', '2025-04-11 15:04:58'),
(33, 25, 'Video', 'https://www.youtube.com/watch?v=rXxLCMf2CXY', '2025-04-11 15:04:58'),
(34, 26, 'PDF', '../uploads/pdfs/قرار فتح المباراة (1).pdf', '2025-04-11 15:16:52'),
(35, 27, 'PDF', '../uploads/pdfs/قرار فتح المباراة (1).pdf', '2025-04-11 15:50:25');

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
(1, 'Bac+25', 'Second year post-baccalaureate', '2025-04-09 14:11:33'),
(2, 'Bac+3', 'Third year post-baccalaureate', '2025-04-09 14:11:33');

-- --------------------------------------------------------

--
-- Structure de la table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `device_id` varchar(36) NOT NULL,
  `device_name` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `level_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `students`
--

INSERT INTO `students` (`id`, `full_name`, `email`, `password`, `status`, `device_id`, `device_name`, `latitude`, `longitude`, `level_id`, `created_at`) VALUES
(1, 'Ali BenAhmed', 'ali@example.com', 'student123', 'pending', '', NULL, NULL, NULL, 1, '2025-04-09 14:11:33'),
(2, 'Sara ElHadi', 'sara@example.com', 'student123', 'pending', '', NULL, NULL, NULL, 1, '2025-04-09 14:11:33'),
(3, 'Omar Kadir', 'omar@example.com', 'student123', 'pending', '', NULL, NULL, NULL, 2, '2025-04-09 14:11:33'),
(4, 'Ali BenAhmedxxx', 'zouhair@t.fr', '$2y$10$4AYc9NFNR3deOwvIEpYZqe2I8Z3bHTYILhP1gcGhe7OEalA2EU3be', 'approved', '753d70d7-1d60-4aa7-a64f-db834d474fa0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 35.73547220, -5.88933500, 1, '2025-04-11 00:08:27'),
(5, 'maro', 'm@m.com', '$2y$10$fWAtt.ey7Lebi/vDVmPhvesvutEmWn40MAdVguROQJbbp45HgnWky', 'approved', 'd612b836-50be-4d3e-8155-7cb178bce63e', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 35.73547220, -5.88933500, 1, '2025-04-11 00:15:17'),
(6, 'maro', 'xm@m.com', '$2y$10$9s48UTZqtfkIE1AK0a97heODFgtoDbHCvmJLgNhmfdot399E6DcXO', 'approved', '2f62cc96-e5ca-4314-b209-391c944461cd', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 35.73547220, -5.88933500, 1, '2025-04-11 00:34:13'),
(8, 'mar1', 't@t.com', '$2y$10$suWT96YsjQS.ENT5z75HN.cR7.QfcmrVshwHIW/Je09LiiFAM9rue', 'approved', 'd3921bd3-7ebc-4f84-ac2b-9cd8f2b8c4d1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 35.73547220, -5.88933500, 1, '2025-04-11 00:41:31'),
(9, 'mar2', 'a@a.com', '$2y$10$5hhGEDqga3S/PfDvVz5F2e3iRNbLM.2lfAUZyUAvn.JS6cSRa5PcS', 'approved', 'd3921bd3-7ebc-4f84-ac2b-9cd8f2b8c4d1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 35.73547220, -5.88933500, 1, '2025-04-11 00:42:35'),
(10, 'michu', 'z@z.com', '$2y$10$HBJnNVfs6vNruoFcu6gmHuoGyIZwusNLuIsJc0BHx7mK1RG1MDCQC', 'pending', '604f2ea3-7488-4c2e-ad6b-4b541e0d56e9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 0.00000000, 0.00000000, NULL, '2025-04-11 00:43:22'),
(11, 'michu2xx', 'e@e.com', '$2y$10$NzO7gmj1eHz9V4jlswQJAemhtgglpJtQw3HUu5pHvAsJxMqAWHXqq', 'approved', 'd3921bd3-7ebc-4f84-ac2b-9cd8f2b8c4d1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 35.73547220, -5.88933500, 1, '2025-04-11 00:44:09'),
(12, 'mar3', 'r@r.com', '$2y$10$R.2RWfwqEwbvQQalHgHpGO98VFQxQaN6Oqsy2c5MRwHc2kK09WK8a', 'approved', 'd3921bd3-7ebc-4f84-ac2b-9cd8f2b8c4d1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 35.73547220, -5.88933500, NULL, '2025-04-11 01:03:58'),
(15, 'MAROUANE HADDAD', 'marouanehaddad08@gmail.com', '$2y$10$7s70Iv31utajASfMtSBf0u0XuKFMkjF.3WL8S1Mj2Y7d5WlWYRSdG', 'approved', 'd3921bd3-7ebc-4f84-ac2b-9cd8f2b8c4d1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 35.73548510, -5.88934810, 1, '2025-04-11 02:45:16'),
(16, 'MAROUANE HADDAD', 'marouanehaddad09@gmail.com', '$2y$10$IcJNMIXlXllII1p90A9mcesWVNTgtlzTCuG9x/mcpyIUN1gQ34Vvq', 'approved', '604f2ea3-7488-4c2e-ad6b-4b541e0d56e9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 35.73549920, -5.88935180, 1, '2025-04-11 03:23:54'),
(17, 'Marouane Sami', 'm@m.fr', '$2y$10$lEXoCLx8NTxkTPfuTJA/0.hoFXDSNuBq27mRxdqjpEhPjvO6rlPMS', 'approved', '275f8639-96f8-4db7-b9db-4d61d10d95e2', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 35.73270000, -5.89160000, 1, '2025-04-11 11:27:49'),
(18, 'Wiame Rachade', 'w@w.com', '$2y$10$obIdwu7T9uwB4v0KGWDwLOfaMvMR0ijM2PSV63Jey90QF6NRhFxNq', 'approved', 'd1c73fa8-f52c-4f8a-b182-4045853c9ccb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 35.73747000, -5.89478800, 2, '2025-04-11 15:48:48');

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
(1, 2, 1, '2025-04-09 14:11:33'),
(2, 2, 2, '2025-04-09 14:11:33'),
(3, 3, 4, '2025-04-09 14:11:33'),
(4, 2, 3, '2025-04-09 14:11:33'),
(5, 1, 4, '2025-04-09 16:08:36'),
(6, 1, 5, '2025-04-09 16:08:36'),
(7, 3, 6, '2025-04-09 16:09:02'),
(8, 1, 6, '2025-04-09 16:09:02'),
(10, 3, 7, '2025-04-09 16:16:06'),
(11, 1, 7, '2025-04-09 16:16:06'),
(13, 1, 3, '2025-04-09 16:17:22'),
(14, 6, 3, '2025-04-11 00:40:36'),
(15, 6, 8, '2025-04-11 00:40:36'),
(16, 6, 9, '2025-04-11 00:40:36'),
(17, 6, 10, '2025-04-11 00:40:36'),
(21, 4, 3, '2025-04-11 01:39:13'),
(22, 4, 8, '2025-04-11 01:39:13'),
(23, 4, 9, '2025-04-11 01:39:13'),
(24, 4, 10, '2025-04-11 01:39:13'),
(28, 8, 3, '2025-04-11 01:39:55'),
(29, 8, 8, '2025-04-11 01:39:55'),
(30, 8, 9, '2025-04-11 01:39:55'),
(31, 8, 10, '2025-04-11 01:39:55'),
(35, 9, 3, '2025-04-11 02:19:07'),
(36, 9, 8, '2025-04-11 02:19:07'),
(37, 9, 9, '2025-04-11 02:19:07'),
(38, 9, 10, '2025-04-11 02:19:07'),
(42, 9, 1, '2025-04-11 02:19:07'),
(43, 11, 3, '2025-04-11 02:22:21'),
(44, 11, 8, '2025-04-11 02:22:21'),
(45, 11, 9, '2025-04-11 02:22:21'),
(46, 11, 10, '2025-04-11 02:22:21'),
(50, 11, 1, '2025-04-11 02:22:21'),
(51, 15, 3, '2025-04-11 02:46:06'),
(52, 15, 8, '2025-04-11 02:46:06'),
(53, 15, 9, '2025-04-11 02:46:06'),
(54, 15, 10, '2025-04-11 02:46:06'),
(58, 15, 1, '2025-04-11 02:46:06'),
(59, 5, 11, '2025-04-11 02:46:39'),
(60, 6, 11, '2025-04-11 02:46:39'),
(61, 4, 11, '2025-04-11 02:46:39'),
(62, 8, 11, '2025-04-11 02:46:39'),
(63, 9, 11, '2025-04-11 02:46:39'),
(64, 11, 11, '2025-04-11 02:46:39'),
(65, 15, 11, '2025-04-11 02:46:39'),
(66, 2, 12, '2025-04-11 02:47:45'),
(67, 9, 12, '2025-04-11 02:47:45'),
(68, 11, 12, '2025-04-11 02:47:45'),
(69, 15, 12, '2025-04-11 02:47:45'),
(73, 16, 3, '2025-04-11 03:25:07'),
(74, 16, 8, '2025-04-11 03:25:07'),
(75, 16, 9, '2025-04-11 03:25:07'),
(76, 16, 10, '2025-04-11 03:25:07'),
(77, 16, 11, '2025-04-11 03:25:07'),
(78, 5, 13, '2025-04-11 03:26:00'),
(79, 6, 13, '2025-04-11 03:26:00'),
(80, 4, 13, '2025-04-11 03:26:00'),
(81, 8, 13, '2025-04-11 03:26:00'),
(82, 9, 13, '2025-04-11 03:26:00'),
(83, 11, 13, '2025-04-11 03:26:00'),
(84, 15, 13, '2025-04-11 03:26:00'),
(85, 16, 13, '2025-04-11 03:26:00'),
(96, 2, 14, '2025-04-11 11:31:23'),
(97, 9, 14, '2025-04-11 11:31:23'),
(98, 11, 14, '2025-04-11 11:31:23'),
(99, 15, 14, '2025-04-11 11:31:23'),
(103, 5, 15, '2025-04-11 11:31:34'),
(104, 6, 15, '2025-04-11 11:31:34'),
(105, 4, 15, '2025-04-11 11:31:34'),
(106, 8, 15, '2025-04-11 11:31:34'),
(107, 9, 15, '2025-04-11 11:31:34'),
(108, 11, 15, '2025-04-11 11:31:34'),
(109, 15, 15, '2025-04-11 11:31:34'),
(110, 16, 15, '2025-04-11 11:31:34'),
(124, 3, 16, '2025-04-11 11:49:15'),
(125, 1, 16, '2025-04-11 11:49:15'),
(127, 2, 17, '2025-04-11 11:49:52'),
(128, 9, 17, '2025-04-11 11:49:52'),
(129, 11, 17, '2025-04-11 11:49:52'),
(130, 15, 17, '2025-04-11 11:49:52'),
(134, 5, 18, '2025-04-11 11:50:23'),
(135, 6, 18, '2025-04-11 11:50:23'),
(136, 4, 18, '2025-04-11 11:50:23'),
(137, 8, 18, '2025-04-11 11:50:23'),
(138, 9, 18, '2025-04-11 11:50:23'),
(139, 11, 18, '2025-04-11 11:50:23'),
(140, 15, 18, '2025-04-11 11:50:23'),
(141, 16, 18, '2025-04-11 11:50:23'),
(149, 17, 1, '2025-04-11 12:05:37'),
(150, 17, 12, '2025-04-11 12:05:37'),
(151, 17, 14, '2025-04-11 12:05:37'),
(152, 17, 17, '2025-04-11 12:05:37'),
(153, 17, 3, '2025-04-11 12:13:49'),
(154, 17, 8, '2025-04-11 12:13:49'),
(155, 17, 9, '2025-04-11 12:13:49'),
(156, 17, 10, '2025-04-11 12:13:49'),
(157, 17, 11, '2025-04-11 12:13:49'),
(158, 17, 13, '2025-04-11 12:13:49'),
(159, 17, 15, '2025-04-11 12:13:49'),
(160, 17, 18, '2025-04-11 12:13:49'),
(168, 17, 23, '2025-04-11 12:48:07'),
(169, 18, 4, '2025-04-11 15:49:25'),
(170, 18, 6, '2025-04-11 15:49:25'),
(171, 18, 7, '2025-04-11 15:49:25'),
(172, 18, 16, '2025-04-11 15:49:25'),
(176, 18, 5, '2025-04-11 15:50:02');

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
(1, 2, 1, '2025-04-09 14:11:33', 0),
(2, 2, 2, '2025-04-09 14:11:33', 0),
(3, 3, 4, '2025-04-09 14:11:33', 0),
(4, 1, 4, '2025-04-09 16:08:36', 0),
(5, 1, 5, '2025-04-09 16:08:36', 0),
(6, 5, 3, '2025-04-11 00:29:23', 0),
(7, 6, 3, '2025-04-11 00:40:36', 0),
(8, 4, 3, '2025-04-11 01:39:13', 0),
(9, 8, 3, '2025-04-11 01:39:55', 0),
(10, 9, 3, '2025-04-11 02:19:07', 0),
(11, 9, 1, '2025-04-11 02:19:07', 0),
(12, 11, 3, '2025-04-11 02:22:21', 0),
(13, 11, 1, '2025-04-11 02:22:21', 0),
(14, 15, 3, '2025-04-11 02:46:06', 1),
(15, 15, 1, '2025-04-11 02:46:06', 0),
(16, 16, 3, '2025-04-11 03:25:07', 0),
(21, 17, 1, '2025-04-11 12:05:37', 0),
(22, 17, 3, '2025-04-11 12:13:49', 1),
(24, 18, 4, '2025-04-11 15:49:25', 1),
(25, 18, 5, '2025-04-11 15:50:02', 0);

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
(1, 'Maths', 1, '2025-04-09 14:11:33'),
(2, 'Physics', 1, '2025-04-09 14:11:33'),
(3, 'Informatics', 1, '2025-04-09 14:11:33'),
(4, 'Maths', 2, '2025-04-09 14:11:33'),
(5, 'Physics', 2, '2025-04-09 14:11:33');

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
  ADD KEY `course_id` (`course_id`);

--
-- Index pour la table `levels`
--
ALTER TABLE `levels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

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
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT pour la table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT pour la table `course_contents`
--
ALTER TABLE `course_contents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT pour la table `levels`
--
ALTER TABLE `levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `student_courses`
--
ALTER TABLE `student_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=177;

--
-- AUTO_INCREMENT pour la table `student_subjects`
--
ALTER TABLE `student_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT pour la table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
  ADD CONSTRAINT `course_contents_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
