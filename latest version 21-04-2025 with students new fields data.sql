-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 21 avr. 2025 à 01:50
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
(7, 1, 'admin', 'Validated student', 'Validated student ID 1 with level ID 1', '2025-04-09 16:17:22'),
(13, 1, 'admin', 'Validated student', 'Validated student ID 6 with level ID 1', '2025-04-11 00:40:36'),
(14, 1, 'admin', 'Validated student', 'Validated student ID 4 with level ID 1', '2025-04-11 01:39:13'),
(15, 1, 'admin', 'Validated student', 'Validated student ID 8 with level ID 1', '2025-04-11 01:39:55'),
(16, 1, 'admin', 'Validated student', 'Validated student ID 9 with level ID 1', '2025-04-11 02:19:07'),
(17, 1, 'admin', 'Logged out', 'Admin logged out', '2025-04-11 02:19:16'),
(18, 1, 'admin', 'Validated student', 'Validated student ID 11 with level ID 1', '2025-04-11 02:22:21'),
(19, 1, 'admin', 'Logged out', 'Admin logged out', '2025-04-11 02:43:33'),
(20, 1, 'admin', 'Added course', 'Added course: Algébre for subject ID 4', '2025-04-12 15:47:02'),
(21, 1, 'admin', 'Deleted course', 'Deleted course ID 1: Algébre', '2025-04-12 15:47:30'),
(22, 1, 'admin', 'Added course', 'Added course: Algèbre for subject ID 4', '2025-04-12 15:48:14'),
(23, 1, 'admin', 'Added course', 'Added course: algèbre for subject ID 4', '2025-04-12 15:48:56'),
(24, 1, 'admin', 'Edited course', 'Edited course ID 2: Algèbre', '2025-04-12 15:55:29'),
(25, 1, 'admin', 'Edited course', 'Edited course ID 2: Algèbre', '2025-04-12 15:55:50'),
(26, 1, 'admin', 'Deleted course', 'Deleted course ID 3: algèbre', '2025-04-12 15:55:59'),
(27, 1, 'admin', 'Edited course', 'Edited course ID 2: Algèbre', '2025-04-12 15:57:02'),
(28, 1, 'admin', 'Edited course', 'Edited course ID 2: Algèbre', '2025-04-12 15:57:17'),
(29, 1, 'admin', 'Added course', 'Added course: test for subject ID 4', '2025-04-12 16:00:59'),
(30, 1, 'admin', 'Edited course', 'Edited course ID 4: test', '2025-04-12 16:01:15'),
(31, 1, 'admin', 'Edited course', 'Edited course ID 4: test', '2025-04-12 16:01:46'),
(32, 19, 'student', 'Viewed course', 'Viewed course ID 2: Algèbre', '2025-04-12 16:08:53'),
(33, 19, 'student', 'Viewed course', 'Viewed course ID 4: test', '2025-04-12 16:09:05'),
(34, 1, 'admin', 'Added course', 'Added course: Testing for subject ID 4', '2025-04-12 16:16:37'),
(35, 1, 'admin', 'Edited course', 'Edited course ID 5: Testing', '2025-04-12 16:17:11'),
(36, 1, 'admin', 'Edited course', 'Edited course ID 5: Testing', '2025-04-12 16:17:31'),
(37, 1, 'admin', 'Edited course', 'Edited course ID 5: Testing', '2025-04-12 16:17:54'),
(38, 1, 'admin', 'Edited course', 'Edited course ID 5: Testing', '2025-04-12 16:19:22'),
(39, 19, 'student', 'Viewed course', 'Viewed course ID 5: Testing', '2025-04-12 16:21:12'),
(40, 19, 'admin', 'Screenshot taken', 'Captured page 0 of course \'Testing\' (ID: 5)', '2025-04-12 16:21:24'),
(41, 19, 'student', 'Viewed course', 'Viewed course ID 5: Testing', '2025-04-12 16:21:52'),
(42, 1, 'admin', 'Added course', 'Added course: testing22 for subject ID 4', '2025-04-12 16:42:44'),
(43, 1, 'admin', 'Added course', 'Added course: TESTING99 for subject ID 4', '2025-04-12 16:52:30'),
(44, 1, 'admin', 'Edited course', 'Edited course ID 7: TESTING99', '2025-04-12 16:53:08'),
(45, 1, 'admin', 'Added course', 'Added course: last one plz for subject ID 3', '2025-04-12 17:00:04'),
(46, 1, 'admin', 'Deleted course', 'Deleted course ID 8: last one plz', '2025-04-12 17:00:22'),
(47, 1, 'admin', 'Added course', 'Added course: algebra test for subject ID 4', '2025-04-12 17:00:46'),
(48, 1, 'admin', 'Added course', 'Added course: t for subject ID 4', '2025-04-12 17:01:36'),
(49, 1, 'admin', 'Added course', 'Added course: test150 for subject ID 3', '2025-04-12 17:06:31'),
(50, 1, 'admin', 'Added course', 'Added course: Algèbre for subject ID 4', '2025-04-12 17:07:53'),
(51, 1, 'admin', 'Edited course', 'Edited course ID 12: Algèbre', '2025-04-12 17:10:29'),
(52, 1, 'admin', 'Added course', 'Added course: Analyse for subject ID 4', '2025-04-12 17:27:21'),
(53, 1, 'admin', 'Added course', 'Added course: pasting for subject ID 3', '2025-04-12 17:36:19'),
(54, 1, 'admin', 'Added course', 'Added course: 33 for subject ID 4', '2025-04-12 17:37:08'),
(55, 1, 'admin', 'Screenshot taken', 'Captured page 2 of course \'Analyse\' (ID: 13)', '2025-04-12 17:45:05'),
(56, 1, 'admin', 'Screenshot taken', 'Captured page 1 of course \'Algèbre\' (ID: 12)', '2025-04-12 17:45:47'),
(57, 1, 'admin', 'Added course', 'Added course: Algebra for subject ID 4', '2025-04-12 17:46:51'),
(58, 1, 'admin', 'Edited course', 'Edited course ID 16: Algebra', '2025-04-12 17:47:15'),
(59, 1, 'admin', 'Screenshot taken', 'Captured page 3 of course \'Algebra\' (ID: 16)', '2025-04-12 17:48:38'),
(60, 1, 'admin', 'Added course', 'Added course: Analyse for subject ID 4', '2025-04-12 17:54:09'),
(61, 1, 'admin', 'Edited course', 'Edited course ID 17: Analyse', '2025-04-12 17:54:33'),
(62, 1, 'admin', 'Screenshot taken', 'Captured page 2 of course \'Analyse\' (ID: 17)', '2025-04-12 17:56:38'),
(63, 1, 'admin', 'Edited course', 'Edited course: Analyse for subject ID 4', '2025-04-12 17:57:17'),
(64, 1, 'admin', 'Deleted course', 'Deleted course ID 17: Analyse', '2025-04-12 17:57:24'),
(65, 1, 'admin', 'Deleted course', 'Deleted course ID 16: Algebra', '2025-04-12 17:57:29'),
(66, 1, 'admin', 'Added course', 'Added course: Algebra for subject ID 4', '2025-04-12 17:59:57'),
(67, 1, 'admin', 'Edited course', 'Edited course: Algebra for subject ID 4', '2025-04-12 18:00:42'),
(68, 1, 'admin', 'Edited course', 'Edited course: Algebra for subject ID 4', '2025-04-12 18:02:01'),
(69, 1, 'admin', 'Edited course', 'Edited course: Algebra for subject ID 4', '2025-04-12 18:02:40'),
(70, 1, 'admin', 'Edited course', 'Edited course ID 18: Algebra', '2025-04-12 18:07:58'),
(71, 1, 'admin', 'Edited course', 'Edited course ID 18: Algebra', '2025-04-12 18:08:42'),
(72, 1, 'admin', 'Edited course', 'Edited course ID 18: Algebra', '2025-04-12 18:17:22'),
(73, 1, 'admin', 'Edited course', 'Edited course ID 18: Algebra', '2025-04-12 18:30:59'),
(74, 1, 'admin', 'Added course', 'Added course: Thermodynamics for subject ID 5', '2025-04-12 18:34:32'),
(75, 20, 'student', 'Viewed course', 'Viewed course ID 19: Thermodynamics', '2025-04-12 18:34:38'),
(76, 20, 'student', 'Viewed course', 'Viewed course ID 19: Thermodynamics', '2025-04-12 18:34:50'),
(77, 20, 'student', 'Viewed course', 'Viewed course ID 19: Thermodynamics', '2025-04-12 18:35:03'),
(78, 20, 'student', 'Viewed course', 'Viewed course ID 19: Thermodynamics', '2025-04-12 18:35:35'),
(79, 1, 'admin', 'Edited course', 'Edited course ID 19: Thermodynamics', '2025-04-12 18:37:17'),
(80, 20, 'student', 'Viewed course', 'Viewed course ID 19: Thermodynamics', '2025-04-12 18:37:20'),
(81, 20, 'student', 'Viewed course', 'Viewed course ID 19: Thermodynamics', '2025-04-12 18:37:29'),
(82, 20, 'student', 'Viewed course', 'Viewed course ID 19: Thermodynamics', '2025-04-12 19:01:14'),
(83, 20, 'admin', 'Screenshot taken', 'Captured page 0 of course \'Thermodynamics\' (ID: 19)', '2025-04-12 19:02:17'),
(84, 20, 'student', 'Viewed course', 'Viewed course ID 18: Algebra', '2025-04-12 19:05:55'),
(85, 20, 'student', 'Viewed course', 'Viewed course ID 18: Algebra', '2025-04-12 19:06:27'),
(86, 20, 'student', 'Viewed course', 'Viewed course ID 18: Algebra', '2025-04-12 19:06:31'),
(87, 1, 'admin', 'Edited course', 'Edited course ID 18: Algebra', '2025-04-12 19:06:54'),
(88, 20, 'student', 'Viewed course', 'Viewed course ID 18: Algebra', '2025-04-12 19:06:56'),
(89, 1, 'admin', 'Added course', 'Added course: algebre2 for subject ID 4', '2025-04-12 19:07:29'),
(90, 1, 'admin', 'Added course', 'Added course: x for subject ID 5', '2025-04-12 19:07:55'),
(91, 20, 'student', 'Viewed course', 'Viewed course ID 21: x', '2025-04-12 19:07:59'),
(92, 20, 'student', 'Viewed course', 'Viewed course ID 19: Thermodynamics', '2025-04-12 19:08:23'),
(93, 20, 'admin', 'Screenshot taken', 'Captured page 0 of course \'Thermodynamics\' (ID: 19)', '2025-04-12 19:08:44'),
(94, 20, 'student', 'Viewed course', 'Viewed course ID 18: Algebra', '2025-04-12 19:09:22'),
(95, 20, 'student', 'Viewed course', 'Viewed course ID 21: x', '2025-04-12 19:09:28'),
(96, 1, 'admin', 'Edited course', 'Edited course ID 21: x', '2025-04-12 19:09:45'),
(97, 20, 'student', 'Viewed course', 'Viewed course ID 21: x', '2025-04-12 19:09:46'),
(98, 1, 'admin', 'Added course', 'Added course: Base de données for subject ID 3', '2025-04-13 15:09:56'),
(99, 1, 'admin', 'Edited course', 'Edited course ID 22: Base de données', '2025-04-13 15:11:04'),
(100, 21, 'student', 'Viewed course', 'Viewed course ID 22: Base de données', '2025-04-13 15:20:11'),
(101, 21, 'student', 'Viewed course', 'Viewed course ID 22: Base de données', '2025-04-13 15:20:39'),
(102, 1, 'admin', 'Edited course', 'Edited course ID 22: Base de données', '2025-04-13 15:31:06'),
(103, 21, 'student', 'Viewed course', 'Viewed course ID 22: Base de données', '2025-04-13 15:31:14'),
(104, 1, 'admin', 'Edited course', 'Edited course ID 22: Base de données', '2025-04-13 15:32:02'),
(105, 21, 'student', 'Viewed course', 'Viewed course ID 22: Base de données', '2025-04-13 15:32:04'),
(106, 1, 'admin', 'Edited course', 'Edited course ID 22: Base de données', '2025-04-13 15:32:26'),
(107, 21, 'student', 'Viewed course', 'Viewed course ID 22: Base de données', '2025-04-13 15:32:30'),
(108, 21, 'student', 'Viewed course', 'Viewed course ID 22: Base de données', '2025-04-13 16:09:46'),
(109, 21, 'student', 'Viewed course', 'Viewed course ID 22: Base de données', '2025-04-13 17:02:28'),
(110, 21, 'student', 'Viewed course', 'Viewed course ID 22: Base de données', '2025-04-13 17:34:12'),
(111, 21, 'student', 'Viewed course', 'Viewed course ID 22: Base de données', '2025-04-13 17:34:13'),
(112, 21, 'student', 'Viewed course', 'Viewed course ID 22: Base de données', '2025-04-13 17:34:13'),
(113, 21, 'student', 'Viewed course', 'Viewed course ID 22: Base de données', '2025-04-13 17:34:13'),
(114, 21, 'student', 'Viewed course', 'Viewed course ID 22: Base de données', '2025-04-13 17:34:13'),
(115, 21, 'student', 'Viewed course', 'Viewed course ID 22: Base de données', '2025-04-13 17:34:14'),
(116, 21, 'student', 'Viewed course', 'Viewed course ID 22: Base de données', '2025-04-13 17:34:14'),
(117, 21, 'student', 'Viewed course', 'Viewed course ID 22: Base de données', '2025-04-13 17:34:14'),
(118, 21, 'student', 'Viewed course', 'Viewed course ID 22: Base de données', '2025-04-13 17:34:14'),
(119, 21, 'student', 'Viewed course', 'Viewed course ID 22: Base de données', '2025-04-13 17:34:15'),
(120, 21, 'student', 'Viewed course', 'Viewed course ID 22: Base de données', '2025-04-13 17:34:15'),
(121, 21, 'student', 'Viewed course', 'Viewed course ID 22: Base de données', '2025-04-13 17:34:15'),
(122, 21, 'student', 'Viewed course', 'Viewed course ID 22: Base de données', '2025-04-13 17:34:15'),
(123, 21, 'student', 'Viewed course', 'Viewed course ID 22: Base de données', '2025-04-13 17:34:20'),
(124, 21, 'student', 'Viewed course', 'Viewed course ID 22: Base de données', '2025-04-13 17:55:46'),
(125, 21, 'student', 'Viewed course', 'Viewed course ID 22: Base de données', '2025-04-13 17:55:47'),
(126, 21, 'student', 'Viewed quiz', 'Viewed quiz ID 2: test', '2025-04-13 21:07:59'),
(127, 21, 'student', 'Viewed quiz', 'Viewed quiz ID 2: test', '2025-04-13 21:08:07'),
(128, 21, 'student', 'Viewed quiz', 'Viewed quiz ID 2: test', '2025-04-13 21:08:07'),
(129, 21, 'student', 'Viewed quiz', 'Viewed quiz ID 2: test', '2025-04-13 21:09:18'),
(130, 21, 'student', 'Viewed quiz', 'Viewed quiz ID 2: test', '2025-04-13 21:21:34'),
(131, 21, 'student', 'Viewed quiz', 'Viewed quiz ID 2: test', '2025-04-13 21:41:28'),
(132, 21, 'student', 'Viewed quiz', 'Viewed quiz ID 2: test', '2025-04-13 21:41:44'),
(133, 21, 'student', 'Viewed quiz', 'Viewed quiz ID 2: test', '2025-04-13 21:42:23'),
(134, 21, 'student', 'Viewed quiz', 'Viewed quiz ID 2: test', '2025-04-13 21:42:32'),
(135, 21, 'student', 'Viewed course', 'Viewed course ID 22: Base de données', '2025-04-13 21:42:41'),
(136, 21, 'student', 'Viewed course', 'Viewed course ID 22: Base de données', '2025-04-13 21:50:20'),
(137, 21, 'student', 'Viewed course', 'Viewed course ID 22: Base de données', '2025-04-13 21:51:09'),
(138, 21, 'student', 'Viewed quiz', 'Viewed quiz ID 2: test', '2025-04-13 21:52:05'),
(139, 21, 'student', 'Viewed quiz', 'Viewed quiz ID 2: test', '2025-04-13 21:52:21'),
(140, 1, 'admin', 'Added level', 'Added level: Bac+1', '2025-04-15 23:29:34'),
(141, 1, 'admin', 'Added subject', 'Added subject: fitlife for level ID 3', '2025-04-15 23:39:34'),
(142, 1, 'admin', 'Added course', 'Added course: x for subject ID 6', '2025-04-16 00:54:17'),
(143, 1, 'admin', 'Logged out student', 'Admin ID 1 logged out student ID 24', '2025-04-16 22:20:53'),
(144, 26, 'student', 'Registered', 'Student Etudiant 1 (ID 26) registered with email b@b.b', '2025-04-19 17:15:29'),
(145, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 3: mathos', '2025-04-19 17:16:27'),
(146, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 4: last', '2025-04-19 17:17:27'),
(147, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 5: PP', '2025-04-19 17:18:30'),
(148, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 5: PP', '2025-04-19 17:20:02'),
(149, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 6: Exam Analyse', '2025-04-19 17:29:45'),
(150, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 6: Exam Analyse', '2025-04-19 17:30:00'),
(151, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 6: Exam Analyse', '2025-04-19 17:30:00'),
(152, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 6: Exam Analyse', '2025-04-19 17:33:02'),
(153, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 6: Exam Analyse', '2025-04-19 17:40:23'),
(154, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 7: solokat', '2025-04-19 17:41:14'),
(155, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 7: solokat', '2025-04-19 17:42:58'),
(156, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 7: solokat', '2025-04-19 17:43:31'),
(157, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 7: solokat', '2025-04-19 17:43:31'),
(158, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 7: solokat', '2025-04-19 17:51:39'),
(159, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 9: last exam to be', '2025-04-19 17:53:51'),
(160, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 9: last exam to be', '2025-04-19 17:56:44'),
(161, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 9: last exam to be', '2025-04-19 17:58:26'),
(162, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 9: last exam to be', '2025-04-19 17:58:26'),
(163, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 9: last exam to be', '2025-04-19 17:59:17'),
(164, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 11: mathosx', '2025-04-19 18:02:08'),
(165, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 11: mathosx', '2025-04-19 18:02:24'),
(166, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 11: mathosx', '2025-04-19 18:02:37'),
(167, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 11: mathosx', '2025-04-19 18:02:56'),
(168, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 11: mathosx', '2025-04-19 18:03:01'),
(169, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 11: mathosx', '2025-04-19 18:03:12'),
(170, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 11: mathosx', '2025-04-19 18:03:12'),
(171, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 11: mathosx', '2025-04-19 18:04:33'),
(172, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 11: mathosx', '2025-04-19 18:04:40'),
(173, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 11: mathosx', '2025-04-19 18:04:40'),
(174, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 11: mathosx', '2025-04-19 18:05:00'),
(175, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 11: mathosx', '2025-04-19 18:05:00'),
(176, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 12: aaaa', '2025-04-19 18:16:28'),
(177, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 12: aaaa', '2025-04-19 18:18:02'),
(178, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 12: aaaa', '2025-04-19 18:18:19'),
(179, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 12: aaaa', '2025-04-19 18:18:19'),
(180, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 13: testing25', '2025-04-19 18:26:28'),
(181, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 13: testing25', '2025-04-19 18:35:36'),
(182, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 13: testing25', '2025-04-19 18:36:14'),
(183, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 13: testing25', '2025-04-19 18:36:14'),
(184, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 13: testing25', '2025-04-19 18:37:11'),
(185, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 13: testing25', '2025-04-19 18:37:33'),
(186, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 3: mathos', '2025-04-19 18:38:01'),
(187, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 3: mathos', '2025-04-19 18:38:12'),
(188, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 3: mathos', '2025-04-19 18:38:12'),
(189, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 3: mathos', '2025-04-19 18:38:39'),
(190, 26, 'student', 'Viewed course', 'Viewed course ID 18: Algebra', '2025-04-19 18:41:05'),
(191, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 13: testing25', '2025-04-19 23:33:57'),
(192, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 13: testing25', '2025-04-19 23:34:03'),
(193, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 8: Dernier Exam', '2025-04-19 23:34:10'),
(194, 26, 'student', 'Viewed quiz', 'Viewed quiz ID 6: Exam Analyse', '2025-04-19 23:34:19'),
(195, 27, 'student', 'Registered', 'Student Marouane Haddad (ID 27) registered with email marouane@marouane.com', '2025-04-20 00:05:00'),
(196, 1, 'admin', 'Added level', 'Added level: Bac+2', '2025-04-20 00:25:37'),
(197, 1, 'admin', 'Added level', 'Added level: Bac+3', '2025-04-20 00:25:47'),
(198, 1, 'admin', 'Added subject', 'Added subject: Mathématiques for level ID 4', '2025-04-20 00:26:03'),
(199, 1, 'admin', 'Added subject', 'Added subject: Physiques for level ID 4', '2025-04-20 00:26:10'),
(200, 1, 'admin', 'Added subject', 'Added subject: Informatiques for level ID 4', '2025-04-20 00:26:17'),
(201, 1, 'admin', 'Added course', 'Added course: Analyse Mathématique for subject ID 7', '2025-04-20 00:29:35'),
(202, 1, 'admin', 'Added course', 'Added course: Probabilités et Statistiques for subject ID 7', '2025-04-20 00:31:01'),
(203, 1, 'admin', 'Added course', 'Added course: Mécanique Classique for subject ID 8', '2025-04-20 00:31:43'),
(204, 1, 'admin', 'Added course', 'Added course: Thermodynamique for subject ID 8', '2025-04-20 00:37:43'),
(205, 1, 'admin', 'Added course', 'Added course: Algorithmique et Structures de Données for subject ID 9', '2025-04-20 00:38:45'),
(206, 1, 'admin', 'Added course', 'Added course: Bases de Données for subject ID 9', '2025-04-20 00:39:20'),
(207, 1, 'admin', 'Edited course', 'Edited course ID 24: Analyse Mathématique', '2025-04-20 01:27:57'),
(208, 27, 'student', 'Viewed quiz', 'Viewed quiz ID 14: Examen Probabilités et Statistiques', '2025-04-20 01:35:36'),
(209, 27, 'student', 'Viewed quiz', 'Viewed quiz ID 14: Examen Probabilités et Statistiques', '2025-04-20 01:45:16'),
(210, 27, 'student', 'Viewed quiz', 'Viewed quiz ID 14: Examen Probabilités et Statistiques', '2025-04-20 01:45:36'),
(211, 27, 'student', 'Viewed quiz', 'Viewed quiz ID 14: Examen Probabilités et Statistiques', '2025-04-20 01:45:36'),
(212, 27, 'student', 'Viewed quiz', 'Viewed quiz ID 14: Examen Probabilités et Statistiques', '2025-04-20 01:46:07'),
(213, 27, 'student', 'Viewed quiz', 'Viewed quiz ID 14: Examen Probabilités et Statistiques', '2025-04-20 01:46:07'),
(214, 27, 'student', 'Viewed quiz', 'Viewed quiz ID 14: Examen Probabilités et Statistiques', '2025-04-20 01:46:55'),
(215, 27, 'student', 'Viewed quiz', 'Viewed quiz ID 14: Examen Probabilités et Statistiques', '2025-04-20 01:47:16'),
(216, 27, 'student', 'Viewed quiz', 'Viewed quiz ID 14: Examen Probabilités et Statistiques', '2025-04-20 02:13:54'),
(217, 27, 'student', 'Viewed quiz', 'Viewed quiz ID 15: Examen Probabilités et Statistiques 2', '2025-04-20 02:14:55'),
(218, 27, 'student', 'Viewed quiz', 'Viewed quiz ID 15: Examen Probabilités et Statistiques 2', '2025-04-20 02:23:15'),
(219, 27, 'student', 'Viewed quiz', 'Viewed quiz ID 15: Examen Probabilités et Statistiques 2', '2025-04-20 02:23:26'),
(220, 27, 'student', 'Viewed quiz', 'Viewed quiz ID 15: Examen Probabilités et Statistiques 2', '2025-04-20 02:23:27'),
(221, 27, 'student', 'Viewed quiz', 'Viewed quiz ID 15: Examen Probabilités et Statistiques 2', '2025-04-20 02:27:03'),
(222, 27, 'student', 'Viewed quiz', 'Viewed quiz ID 15: Examen Probabilités et Statistiques 2', '2025-04-20 02:27:09'),
(223, 27, 'student', 'Viewed quiz', 'Viewed quiz ID 15: Examen Probabilités et Statistiques 2', '2025-04-20 02:27:09'),
(224, 27, 'student', 'Viewed quiz', 'Viewed quiz ID 15: Examen Probabilités et Statistiques 2', '2025-04-20 02:28:02'),
(225, 27, 'student', 'Viewed quiz', 'Viewed quiz ID 15: Examen Probabilités et Statistiques 2', '2025-04-20 02:28:02'),
(226, 27, 'student', 'Viewed quiz', 'Viewed quiz ID 15: Examen Probabilités et Statistiques 2', '2025-04-20 02:59:39'),
(227, 27, 'student', 'Viewed quiz', 'Viewed quiz ID 15: Examen Probabilités et Statistiques 2', '2025-04-20 02:59:44'),
(228, 27, 'student', 'Viewed quiz', 'Viewed quiz ID 15: Examen Probabilités et Statistiques 2', '2025-04-20 02:59:56'),
(229, 27, 'student', 'Viewed quiz', 'Viewed quiz ID 15: Examen Probabilités et Statistiques 2', '2025-04-20 03:17:25'),
(230, 27, 'student', 'Viewed quiz', 'Viewed quiz ID 15: Examen Probabilités et Statistiques 2', '2025-04-20 03:17:31'),
(231, 27, 'student', 'Viewed quiz', 'Viewed quiz ID 15: Examen Probabilités et Statistiques 2', '2025-04-20 03:17:31'),
(232, 27, 'student', 'Viewed quiz', 'Viewed quiz ID 14: Examen Probabilités et Statistiques', '2025-04-20 03:20:19'),
(233, 27, 'student', 'Viewed quiz', 'Viewed quiz ID 14: Examen Probabilités et Statistiques', '2025-04-20 03:20:24'),
(234, 27, 'student', 'Viewed quiz', 'Viewed quiz ID 14: Examen Probabilités et Statistiques', '2025-04-20 03:20:25'),
(235, 27, 'student', 'Viewed quiz', 'Viewed quiz ID 14: Examen Probabilités et Statistiques', '2025-04-20 03:20:33'),
(236, 27, 'student', 'Viewed quiz', 'Viewed quiz ID 15: Examen Probabilités et Statistiques 2', '2025-04-20 03:20:38'),
(237, 27, 'student', 'Viewed quiz', 'Viewed quiz ID 15: Examen Probabilités et Statistiques 2', '2025-04-20 03:20:43'),
(238, 27, 'student', 'Viewed quiz', 'Viewed quiz ID 15: Examen Probabilités et Statistiques 2', '2025-04-20 03:20:43'),
(239, 27, 'student', 'Viewed quiz', 'Viewed quiz ID 14: Examen Probabilités et Statistiques', '2025-04-20 03:21:36'),
(240, 27, 'student', 'Viewed quiz', 'Viewed quiz ID 14: Examen Probabilités et Statistiques', '2025-04-20 03:28:41'),
(241, 27, 'student', 'Viewed quiz', 'Viewed quiz ID 14: Examen Probabilités et Statistiques', '2025-04-20 03:28:47'),
(242, 27, 'student', 'Viewed quiz', 'Viewed quiz ID 14: Examen Probabilités et Statistiques', '2025-04-20 03:28:47'),
(243, 27, 'student', 'Viewed quiz', 'Viewed quiz ID 14: Examen Probabilités et Statistiques', '2025-04-20 03:30:19'),
(244, 27, 'student', 'Viewed quiz', 'Viewed quiz ID 14: Examen Probabilités et Statistiques', '2025-04-20 03:30:19'),
(245, 28, 'student', 'Registered', 'Student Sami Haddad (ID 28) registered with email marouanehaddad08@gmail.com', '2025-04-20 03:43:49'),
(246, 29, 'student', 'Registered', 'Student Sami Haddad (ID 29) registered with email marouanehaddad08@gmail.com', '2025-04-20 03:45:02'),
(247, 30, 'student', 'Registered', 'Student Sami Haddad (ID 30) registered with email marouanehaddad08@gmail.com', '2025-04-20 03:46:17'),
(248, 31, 'student', 'Registered', 'Student Sami Haddad (ID 31) registered with email marouanehaddad08@gmail.com', '2025-04-20 03:46:59'),
(249, 32, 'student', 'Registered', 'Student Sami Haddad (ID 32) registered with email marouanehaddad08@gmail.com', '2025-04-20 03:56:36'),
(250, 33, 'student', 'Registered', 'Student Sami Haddad (ID 33) registered with email marouanehaddad08@gmail.com', '2025-04-20 21:29:24'),
(251, 34, 'student', 'Registered', 'Étudiant Malak Haddad (ID 34) inscrit avec email marouanehaddad08+@gmail.com', '2025-04-20 22:01:48'),
(252, 35, 'student', 'Registered', 'Étudiant Michu Sghir (ID 35) inscrit avec email marouanehaddad08++@gmail.com', '2025-04-20 23:17:32'),
(253, 35, 'student', 'Viewed quiz', 'Viewed quiz ID 14: Examen Probabilités et Statistiques', '2025-04-20 23:48:48'),
(254, 35, 'student', 'Viewed quiz', 'Viewed quiz ID 14: Examen Probabilités et Statistiques', '2025-04-20 23:48:57'),
(255, 35, 'student', 'Viewed quiz', 'Viewed quiz ID 14: Examen Probabilités et Statistiques', '2025-04-20 23:48:57'),
(256, 35, 'student', 'Viewed quiz', 'Viewed quiz ID 14: Examen Probabilités et Statistiques', '2025-04-20 23:49:59'),
(257, 36, 'student', 'Registered', 'Étudiant Michu lbasel (ID 36) inscrit avec email marouanehaddad08+++@gmail.com', '2025-04-21 00:20:21'),
(258, 37, 'student', 'Registered', 'Étudiant Maro Haddad (ID 37) inscrit avec email marouanehaddad08++++@gmail.com', '2025-04-21 00:22:43'),
(259, 38, 'student', 'Registered', 'Étudiant Marouanee Haddad (ID 38) inscrit avec email marouanehaddad+08@gmail.com', '2025-04-21 00:24:48'),
(260, 39, 'student', 'Registered', 'Étudiant Marouanee Haddad x (ID 39) inscrit avec email marouanehaddad08+1@gmail.com', '2025-04-21 00:26:03'),
(261, 40, 'student', 'Registered', 'Étudiant Marouanee Haddad x (ID 40) inscrit avec email marouanehaddad08++++++@gmail.com', '2025-04-21 00:26:44');

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
(24, 'Analyse Mathématique', 7, 'Easy', '2025-04-20 00:29:35'),
(25, 'Probabilités et Statistiques', 7, 'Medium', '2025-04-20 00:31:01'),
(26, 'Mécanique Classique', 8, 'Easy', '2025-04-20 00:31:43'),
(27, 'Thermodynamique', 8, 'Medium', '2025-04-20 00:37:43'),
(28, 'Algorithmique et Structures de Données', 9, 'Easy', '2025-04-20 00:38:45'),
(29, 'Bases de Données', 9, 'Medium', '2025-04-20 00:39:20');

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
(42, 24, 44, 'PDF', 'Introduction', '../Uploads/pdfs/1745108876_860f4edc-eba0-4d5d-bf4b-40e6cb2975402.pdf', '2025-04-20 01:27:56'),
(43, 24, 44, 'PDF', 'Introduction2', '../Uploads/pdfs/1745108876_17439544931.pdf', '2025-04-20 01:27:56'),
(44, 24, 45, 'PDF', 'Taylor', '../Uploads/pdfs/1745108876_1743955932الملخص.pdf', '2025-04-20 01:27:56'),
(45, 24, 45, 'PDF', 'applications', '../Uploads/pdfs/1745108877_1743990099oncf-voyages-ismail_haddad.pdf', '2025-04-20 01:27:57'),
(46, 24, 46, 'Video', 'Intégration', 'https://www.youtube.com/embed/1wBvuZVE7FI', '2025-04-20 01:27:57'),
(47, 24, 46, 'Video', 'fonctions rationnelles.', 'https://www.youtube.com/embed/fcf5yoF_HEE', '2025-04-20 01:27:57'),
(48, 24, 47, 'PDF', 'Application des séries de Fourier', '../Uploads/pdfs/1745108877_recu2.pdf', '2025-04-20 01:27:57'),
(49, 24, 47, 'PDF', 'résolution des équations différentielles.', '../Uploads/pdfs/1745108877_AG_FITNESS_DESIGNS2.pdf', '2025-04-20 01:27:57');

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
(44, 24, 'Calcul des dérivées et des intégrales.', NULL, '2025-04-20 00:29:35'),
(45, 24, 'Théorème de Taylor et applications.', NULL, '2025-04-20 00:29:35'),
(46, 24, 'Intégration des fonctions rationnelles.', NULL, '2025-04-20 00:29:35'),
(47, 24, 'Application des séries de Fourier dans la résolution des équations différentielles.', NULL, '2025-04-20 00:29:35'),
(48, 25, 'Calcul des probabilités conditionnelles.', NULL, '2025-04-20 00:31:01'),
(49, 25, 'Lois de probabilité (binomiale, normale, Poisson).', NULL, '2025-04-20 00:31:01'),
(50, 25, 'Estimation statistique (moyenne, variance).', NULL, '2025-04-20 00:31:01'),
(51, 25, 'Application des tests d’hypothèses.', NULL, '2025-04-20 00:31:01'),
(52, 26, 'Étude des lois de Newton et applications pratiques.', NULL, '2025-04-20 00:31:43'),
(53, 26, 'Mécanique des particules et des corps rigides.', NULL, '2025-04-20 00:31:43'),
(54, 26, 'Travail et énergie, théorème de travail et énergie cinétique.', NULL, '2025-04-20 00:31:43'),
(55, 26, 'Mouvement circulaire et gravitation universelle.', NULL, '2025-04-20 00:31:43'),
(56, 27, 'Équations d\\\'état des gaz parfaits.', NULL, '2025-04-20 00:37:43'),
(57, 27, 'Cycle de Carnot et rendement des moteurs thermiques.', NULL, '2025-04-20 00:37:43'),
(58, 27, 'Entropie et transformations irréversibles.', NULL, '2025-04-20 00:37:43'),
(59, 27, 'Applications industrielles de la thermodynamique.', NULL, '2025-04-20 00:37:43'),
(60, 28, 'Analyse de la complexité des algorithmes.', NULL, '2025-04-20 00:38:45'),
(61, 28, 'Algorithmes de tri (par insertion, par sélection, rapide).', NULL, '2025-04-20 00:38:45'),
(62, 28, 'Structures de données (tableaux, listes, piles, files, arbres binaires).', NULL, '2025-04-20 00:38:45'),
(63, 28, 'Recherche dans des bases de données.', NULL, '2025-04-20 00:38:45'),
(64, 29, 'Création et gestion de bases de données relationnelles.', NULL, '2025-04-20 00:39:20'),
(65, 29, 'Requêtes SQL (SELECT, INSERT, UPDATE, DELETE).', NULL, '2025-04-20 00:39:20'),
(66, 29, 'Normalisation des bases de données.', NULL, '2025-04-20 00:39:20'),
(67, 29, 'Relations et jointures.', NULL, '2025-04-20 00:39:20');

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
(4, 27, '16c093bd15fdb2fe0e095ce2c780b3aa', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '::1', 35.7354693, -5.8893217, '2025-04-20 03:31:17', 'approved'),
(5, 35, '16c093bd15fdb2fe0e095ce2c780b3aa', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '::1', 35.7354116, -5.889319, '2025-04-20 23:43:32', 'approved');

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
(4, 'Bac+2', 'Niveau Bac+2', '2025-04-20 00:25:37'),
(5, 'Bac+3', 'Niveau Bac+3', '2025-04-20 00:25:47');

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
(14, 'Examen Probabilités et Statistiques', 7, '', '../uploads/quizzes/6804414f24ca9_Epreuve e.crite2_ 2023_Cour des comptes.pdf', '2025-04-20 01:35:27', '2025-04-20 01:35:27', '2025-04-20 01:37:00', 2.00),
(15, 'Examen Probabilités et Statistiques 2', 7, '', '../uploads/quizzes/68044a8b85d76_INGDEVINFO_2.pdf', '2025-04-20 02:14:51', '2025-04-20 02:14:51', '2025-04-20 02:16:00', 1.00);

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
(9, 14, 27, '../Uploads/quiz_submissions/Marouane_Haddad_Examen_Probabilit__s_et_Statistiques_v1_1745109936.pdf', 12, 'du mal', '2025-04-20 01:45:36', '2025-04-20 01:47:02'),
(10, 14, 27, '../Uploads/quiz_submissions/Marouane_Haddad_Examen_Probabilit__s_et_Statistiques_v2_1745109967.pdf', 18, 'daba mezian', '2025-04-20 01:46:07', '2025-04-20 01:47:14'),
(11, 15, 27, '../Uploads/quiz_submissions/Marouane_Haddad_Examen_Probabilit__s_et_Statistiques_2_v1_1745112206.pdf', NULL, NULL, '2025-04-20 02:23:26', NULL),
(12, 15, 27, '../Uploads/quiz_submissions/Marouane_Haddad_Examen_Probabilit__s_et_Statistiques_2_v2_1745112429.pdf', NULL, NULL, '2025-04-20 02:27:09', NULL),
(13, 15, 27, '../Uploads/quiz_submissions/Marouane_Haddad_Examen_Probabilit__s_et_Statistiques_2_v3_1745112482.pdf', NULL, NULL, '2025-04-20 02:28:02', NULL),
(14, 15, 27, '../Uploads/quiz_submissions/Marouane_Haddad_Examen_Probabilit__s_et_Statistiques_2_v4_1745115451.pdf', NULL, NULL, '2025-04-20 03:17:31', NULL),
(15, 14, 27, '../Uploads/quiz_submissions/Marouane_Haddad_Examen_Probabilit__s_et_Statistiques_v3_1745115624.pdf', NULL, NULL, '2025-04-20 03:20:24', NULL),
(16, 15, 27, '../Uploads/quiz_submissions/Marouane_Haddad_Examen_Probabilit__s_et_Statistiques_2_v5_1745115643.pdf', NULL, NULL, '2025-04-20 03:20:43', NULL),
(17, 14, 27, '../Uploads/quiz_submissions/Marouane_HaddadExamen_Probabilit__s_et_Statistiquesv4.pdf', NULL, NULL, '2025-04-20 03:28:47', NULL),
(18, 14, 27, '../Uploads/quiz_submissions/Marouane Haddad - Examen Probabilits et Statistiques v5.pdf', NULL, NULL, '2025-04-20 03:30:19', NULL),
(19, 14, 35, '../Uploads/quiz_submissions/Michu Sghir - Examen Probabilits et Statistiques v1.pdf', 18, 'Very good', '2025-04-20 23:48:57', '2025-04-20 23:49:23');

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
  `created_at` datetime DEFAULT current_timestamp(),
  `session_status` varchar(20) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `students`
--

INSERT INTO `students` (`id`, `full_name`, `email`, `phone`, `dob`, `password`, `status`, `device_id`, `device_name`, `latitude`, `longitude`, `level_id`, `gender`, `city`, `created_at`, `session_status`) VALUES
(27, 'Marouane Haddad', 'marouane@marouane.com', NULL, NULL, '$2y$10$xGi/jzOfhwDo35J7pwHFV.EBcUYKXyRxXs1kMNKZmvWcaV4JEriNC', 'approved', '9e471bbddb32cf5bc453ea260583c215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 35.73553800, -5.88974100, 4, NULL, NULL, '2025-04-20 00:04:59', 'active'),
(33, 'Sami Haddad', 'marouanehaddad08@gmail.com', NULL, NULL, '$2y$10$cbVJciCK5Xocgts/UCIZLuQWP7ZrX.F81ynatgLpXHLk9hncljibC', 'approved', '9e471bbddb32cf5bc453ea260583c215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 35.73498500, -5.88968800, NULL, NULL, NULL, '2025-04-20 21:29:24', 'active'),
(34, 'Malak Haddad', 'marouanehaddad08+@gmail.com', '0613508702', '2009-04-20', '$2y$10$cSr4YqZYvDHOqwifmLLoxeKQ3u.IYVi1I6tqOMcfHGCg0YHJ85hai', 'approved', '9e471bbddb32cf5bc453ea260583c215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 35.73525600, -5.88975000, 4, 'Male', 'Tanger', '2025-04-20 22:01:47', 'active'),
(35, 'Michu Sghir', 'marouanehaddad08++@gmail.com', '0613508702', '2002-02-12', '$2y$10$InRLzwZNi02P1nBq/PpljO8APQ5lKk5ZEGKQZenDP4Pl6Qa.clZTa', 'approved', '9e471bbddb32cf5bc453ea260583c215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 35.73523700, -5.88974900, 4, 'Male', 'Kenitra', '2025-04-20 23:17:32', 'active'),
(36, 'Michu lbasel', 'marouanehaddad08+++@gmail.com', '0613508702', '2000-04-21', '$2y$10$.g29lhoB4WZkurkWKp4jjO0Gxb3vaE0TCV/P/q12HOeN57ru5HIqa', 'pending', '9e471bbddb32cf5bc453ea260583c215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 35.73552300, -5.88974400, NULL, 'Male', 'Kenitra', '2025-04-21 00:20:21', 'active'),
(37, 'Maro Haddad', 'marouanehaddad08++++@gmail.com', '0613508702', '2001-04-21', '$2y$10$ykTb328zYZ0ZucxhY0NrY.e5pU2KvFzcLiglZTU6gtsDJepILLy1K', 'pending', '9e471bbddb32cf5bc453ea260583c215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 35.73520700, -5.88976500, NULL, 'Female', 'Kenitra', '2025-04-21 00:22:43', 'active'),
(38, 'Marouanee Haddad', 'marouanehaddad+08@gmail.com', '0613508702', '2001-04-21', '$2y$10$xiNepa0GciNkCEeboU4WhOPldji9zCxtZQJHaOFW8oXMAtd6J8Vp.', 'pending', '9e471bbddb32cf5bc453ea260583c215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 35.73523300, -5.88974400, NULL, 'Male', 'Kenitra', '2025-04-21 00:24:48', 'active'),
(39, 'Marouanee Haddad x', 'marouanehaddad08+1@gmail.com', '0613508702', '2001-04-21', '$2y$10$L9ugu9Q0AnFggrgpbntkCekusEn96e8iAlQsIQN4vmCOCHLRlkzi6', 'pending', '9e471bbddb32cf5bc453ea260583c215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 35.73521800, -5.88976700, NULL, 'Female', 'Kenitra', '2025-04-21 00:26:03', 'active'),
(40, 'Marouanee Haddad x', 'marouanehaddad08++++++@gmail.com', '0613508702', '2000-04-21', '$2y$10$APfcW8COKs1ZhbzrvV4UDu7TsyttMbiGe0Qp9UaNhCQf8FH/7Trei', 'pending', '9e471bbddb32cf5bc453ea260583c215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 35.73522600, -5.88975000, NULL, 'Male', 'Kenitra', '2025-04-21 00:26:44', 'active');

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
(20, 27, 24, '2025-04-20 01:34:14'),
(21, 27, 25, '2025-04-20 01:34:14'),
(23, 27, 26, '2025-04-20 01:34:14'),
(24, 27, 27, '2025-04-20 01:34:14'),
(26, 27, 28, '2025-04-20 01:34:26'),
(31, 34, 24, '2025-04-20 22:58:04'),
(32, 34, 25, '2025-04-20 22:58:04'),
(46, 35, 24, '2025-04-21 00:00:12'),
(47, 35, 28, '2025-04-21 00:00:27'),
(48, 35, 29, '2025-04-21 00:00:27'),
(49, 35, 26, '2025-04-21 00:01:08'),
(50, 35, 27, '2025-04-21 00:01:08');

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
(6, 27, '9e471bbddb32cf5bc453ea260583c215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1', 35.735538, -5.889741, '2025-04-20 00:05:00', 'approved'),
(7, 27, '16c093bd15fdb2fe0e095ce2c780b3aa', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '::1', 35.7354693, -5.8893217, '2025-04-20 03:31:31', 'approved'),
(13, 33, '9e471bbddb32cf5bc453ea260583c215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1', 35.734985, -5.889688, '2025-04-20 21:29:24', 'approved'),
(14, 34, '9e471bbddb32cf5bc453ea260583c215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1', 35.735256, -5.88975, '2025-04-20 22:01:48', 'approved'),
(15, 35, '9e471bbddb32cf5bc453ea260583c215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1', 35.735237, -5.889749, '2025-04-20 23:17:32', 'approved'),
(16, 35, '16c093bd15fdb2fe0e095ce2c780b3aa', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '::1', 35.7354116, -5.889319, '2025-04-20 23:43:42', 'approved'),
(17, 36, '9e471bbddb32cf5bc453ea260583c215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1', 35.735523, -5.889744, '2025-04-21 00:20:21', 'approved'),
(18, 37, '9e471bbddb32cf5bc453ea260583c215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1', 35.735207, -5.889765, '2025-04-21 00:22:43', 'approved'),
(19, 38, '9e471bbddb32cf5bc453ea260583c215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1', 35.735233, -5.889744, '2025-04-21 00:24:48', 'approved'),
(20, 39, '9e471bbddb32cf5bc453ea260583c215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1', 35.735218, -5.889767, '2025-04-21 00:26:03', 'approved'),
(21, 40, '9e471bbddb32cf5bc453ea260583c215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1', 35.735226, -5.88975, '2025-04-21 00:26:44', 'approved');

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
(36, 27, 7, '2025-04-20 01:34:14', 1),
(37, 27, 8, '2025-04-20 01:34:14', 1),
(38, 27, 9, '2025-04-20 01:34:26', 0),
(43, 34, 7, '2025-04-20 22:58:04', 1),
(50, 35, 7, '2025-04-21 00:00:12', 0),
(51, 35, 9, '2025-04-21 00:00:27', 0),
(52, 35, 8, '2025-04-21 00:01:08', 1);

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
(7, 'Mathématiques', 4, '2025-04-20 00:26:03'),
(8, 'Physiques', 4, '2025-04-20 00:26:10'),
(9, 'Informatiques', 4, '2025-04-20 00:26:17');

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
(20, 27, '2025-04-20 00:05:13', '2025-04-20 00:24:51', 35.735538, -5.889741, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(21, 27, '2025-04-20 00:25:00', '2025-04-20 03:16:22', 35.735538, -5.889741, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(22, 27, '2025-04-20 03:17:13', '2025-04-20 03:43:21', 35.735538, -5.889741, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(23, 27, '2025-04-20 03:31:51', '2025-04-20 21:30:26', 35.7354693, -5.8893217, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '::1'),
(24, 33, '2025-04-20 21:29:28', '2025-04-20 21:29:57', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(25, 34, '2025-04-20 22:02:33', '2025-04-20 22:09:03', 35.735256, -5.88975, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(26, 34, '2025-04-20 22:09:11', NULL, 35.735256, -5.88975, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(27, 34, '2025-04-20 22:09:31', '2025-04-20 22:24:12', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(28, 34, '2025-04-20 22:30:11', '2025-04-20 22:30:33', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(29, 34, '2025-04-20 22:30:39', '2025-04-20 23:16:17', NULL, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(30, 35, '2025-04-20 23:17:48', '2025-04-20 23:28:05', 35.735237, -5.889749, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(31, 35, '2025-04-20 23:28:18', '2025-04-20 23:28:20', 35.735237, -5.889749, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(32, 35, '2025-04-20 23:30:25', '2025-04-21 00:19:42', 35.735237, -5.889749, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=262;

--
-- AUTO_INCREMENT pour la table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT pour la table `course_contents`
--
ALTER TABLE `course_contents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT pour la table `course_folders`
--
ALTER TABLE `course_folders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT pour la table `device_attempts`
--
ALTER TABLE `device_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `levels`
--
ALTER TABLE `levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `quiz_submissions`
--
ALTER TABLE `quiz_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT pour la table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT pour la table `student_courses`
--
ALTER TABLE `student_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT pour la table `student_devices`
--
ALTER TABLE `student_devices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT pour la table `student_subjects`
--
ALTER TABLE `student_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT pour la table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

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
