-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 17 avr. 2025 à 16:24
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
(144, 1, 'admin', 'Added course', 'Added course: axerty for subject ID 6', '2025-04-17 12:58:39'),
(145, 1, 'admin', 'Edited course', 'Edited course ID 24: axerty', '2025-04-17 12:59:35'),
(146, 1, 'admin', 'Edited course', 'Edited course ID 24: axerty', '2025-04-17 12:59:54'),
(147, 1, 'admin', 'Edited course', 'Edited course ID 24: axerty', '2025-04-17 13:03:25'),
(148, 1, 'admin', 'Edited course', 'Edited course ID 24: axerty', '2025-04-17 13:04:36'),
(149, 1, 'admin', 'Edited course', 'Edited course ID 24: axerty', '2025-04-17 13:04:52'),
(150, 26, 'student', 'Viewed course', 'Viewed course ID 24: axerty', '2025-04-17 13:07:27'),
(151, 26, 'student', 'Viewed course', 'Viewed course ID 23: x', '2025-04-17 13:07:29'),
(152, 1, 'admin', 'Edited course', 'Edited course ID 24: axerty', '2025-04-17 13:10:24'),
(153, 26, 'student', 'Viewed course', 'Viewed course ID 24: axerty', '2025-04-17 13:10:30'),
(154, 26, 'student', 'Viewed course', 'Viewed course ID 24: axerty', '2025-04-17 13:10:34'),
(155, 26, 'admin', 'Screenshot taken', 'Captured page 0 of course \'axerty\' (ID: 24)', '2025-04-17 13:10:41'),
(156, 26, 'admin', 'Screenshot taken', 'Captured page 0 of course \'axerty\' (ID: 24)', '2025-04-17 13:10:46'),
(157, 26, 'admin', 'Screenshot taken', 'Captured page 0 of course \'axerty\' (ID: 24)', '2025-04-17 13:10:48'),
(158, 1, 'admin', 'Edited course', 'Edited course ID 24: axerty', '2025-04-17 13:11:27'),
(159, 1, 'admin', 'Edited course', 'Edited course ID 24: axerty', '2025-04-17 13:11:55'),
(160, 26, 'student', 'Viewed course', 'Viewed course ID 24: axerty', '2025-04-17 13:12:00'),
(161, 26, 'student', 'Viewed course', 'Viewed course ID 24: axerty', '2025-04-17 13:30:57'),
(162, 26, 'student', 'Viewed course', 'Viewed course ID 24: axerty', '2025-04-17 13:31:02'),
(163, 26, 'admin', 'Screenshot taken', 'Captured page 0 of course \'axerty\' (ID: 24)', '2025-04-17 13:31:08'),
(164, 26, 'student', 'Viewed course', 'Viewed course ID 18: Algebra', '2025-04-17 13:32:08'),
(165, 26, 'student', 'Viewed course', 'Viewed course ID 18: Algebra', '2025-04-17 13:38:57'),
(166, 26, 'student', 'Viewed course', 'Viewed course ID 18: Algebra', '2025-04-17 13:39:04'),
(167, 26, 'student', 'Viewed course', 'Viewed course ID 18: Algebra', '2025-04-17 13:47:41'),
(168, 26, 'student', 'Viewed course', 'Viewed course ID 18: Algebra', '2025-04-17 13:54:33'),
(169, 26, 'admin', 'Screenshot taken', 'Captured page 0 of course \'Algebra\' (ID: 18)', '2025-04-17 13:54:55'),
(170, 26, 'student', 'Viewed course', 'Viewed course ID 18: Algebra', '2025-04-17 13:55:06'),
(171, 20, 'student', 'Viewed course', 'Viewed course ID 18: Algebra', '2025-04-17 14:13:22'),
(172, 20, 'student', 'Viewed course', 'Viewed course ID 21: x', '2025-04-17 14:13:30'),
(173, 26, 'student', 'Viewed course', 'Viewed course ID 24: axerty', '2025-04-17 14:14:49'),
(174, 26, 'student', 'Viewed course', 'Viewed course ID 24: axerty', '2025-04-17 14:15:04'),
(175, 1, 'admin', 'Logged out', 'Admin logged out', '2025-04-17 15:19:13'),
(176, 27, 'student', 'Viewed course', 'Viewed course ID 19: Thermodynamics', '2025-04-17 15:22:06'),
(177, 29, 'student', 'Viewed course', 'Viewed course ID 24: axerty', '2025-04-17 15:23:31'),
(178, 29, 'student', 'Viewed course', 'Viewed course ID 24: axerty', '2025-04-17 15:23:37');

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
(18, 'Algebra', 4, 'Easy', '2025-04-12 17:59:57'),
(19, 'Thermodynamics', 5, 'Easy', '2025-04-12 18:34:32'),
(20, 'algebre2', 4, 'Easy', '2025-04-12 19:07:29'),
(21, 'x', 5, 'Easy', '2025-04-12 19:07:55'),
(22, 'Base de données', 3, 'Easy', '2025-04-13 15:09:56'),
(23, 'x', 6, 'Medium', '2025-04-16 00:54:17'),
(24, 'axerty', 6, 'Easy', '2025-04-17 12:58:39');

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
(25, 18, 31, 'PDF', 'Wiame', '../Uploads/pdfs/1744477678_قرار فتح المباراة (1).pdf', '2025-04-12 18:07:58'),
(26, 18, 31, 'Video', 'intro vid', 'https://www.youtube.com/watch?v=kVpGEuLg5SM&list=RDipRPNPei3z4&index=27', '2025-04-12 18:08:42'),
(27, 18, 31, 'Video', 'intro vid', 'https://www.youtube.com/embed/kVpGEuLg5SM', '2025-04-12 18:17:22'),
(28, 18, 31, 'PDF', 'Wiame22', '../Uploads/pdfs/1744479059_SKM_C364e24091012400.pdf', '2025-04-12 18:30:59'),
(29, 18, 32, 'PDF', 'Wiame Rachade', '../Uploads/pdfs/1744479059_Indemnite_F.C._2024_-_ZBITOU_JAMAL.pdf', '2025-04-12 18:30:59'),
(30, 19, 33, 'PDF', 'Introduction', '../Uploads/pdfs/1744479437_1743297513_قرار_فتح_المباراة.pdf', '2025-04-12 18:37:17'),
(31, 19, 33, 'Video', 'Intro video', 'https://www.youtube.com/embed/1lwkDoremGI', '2025-04-12 18:37:17'),
(32, 19, 34, 'PDF', 'Exemples', '../Uploads/pdfs/1744479437_Calendrier_F.C._2024_-_ZBITOU_JAMAL.pdf', '2025-04-12 18:37:17'),
(33, 19, 34, 'PDF', 'Biographie', '../Uploads/pdfs/1744479437_لائحة_المترشحين_المقبولين_لاجتياز_الاختبار_الكتابي.pdf', '2025-04-12 18:37:17'),
(34, 18, 31, 'PDF', 'Exemples', '../Uploads/pdfs/1744481214_Indemnite_F.C._2024_-_ZBITOU_JAMAL.pdf', '2025-04-12 19:06:54'),
(35, 21, 36, 'PDF', 'n', '../Uploads/pdfs/1744481385_Indemnite_F.C._2024_-_ZBITOU_JAMAL.pdf', '2025-04-12 19:09:45'),
(36, 22, 37, 'PDF', 'Intro', '../Uploads/pdfs/1744553464_oncf-voyages-ismail_haddad.pdf', '2025-04-13 15:11:04'),
(37, 22, 38, 'PDF', 'exemples', '../Uploads/pdfs/1744553464_LISTEDEF_PUB_IE1G.pdf', '2025-04-13 15:11:04'),
(38, 22, 39, 'PDF', 'History', '../Uploads/pdfs/1744553464_Marouane_Haddad_-_CV_-_Final.pdf', '2025-04-13 15:11:04'),
(39, 22, 40, 'PDF', 'Les tables', '../Uploads/pdfs/1744553464_Loi-cadre_06.22_Ar.pdf', '2025-04-13 15:11:04'),
(40, 22, 40, 'PDF', 'Les relations entre les tables', '../Uploads/pdfs/1744554666_DIPLOME_MAROUANE_HADDAD..pdf', '2025-04-13 15:31:06'),
(41, 22, 41, 'PDF', 'Mysql + Apache', '../Uploads/pdfs/1744554746_الملخص.pdf', '2025-04-13 15:32:26'),
(42, 24, 44, 'PDF', 'a', '../Uploads/pdfs/1744891824_قرار_فتح_المباراة_(1).pdf', '2025-04-17 13:10:24'),
(43, 24, 45, 'PDF', 'aze', '../Uploads/pdfs/1744891887_PV__AO_N_1-FST-GAR-2022_FSTT_SEANCE_N_1.pdf', '2025-04-17 13:11:27'),
(44, 24, 46, 'PDF', 'aze', '../Uploads/pdfs/1744891915_BL_LENSA_TANGER_-_Copie.pdf', '2025-04-17 13:11:55');

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
(31, 18, 'les matrices', NULL, '2025-04-12 17:59:57'),
(32, 18, 'les vecteurs', NULL, '2025-04-12 17:59:57'),
(33, 19, 'Introduction', 'il faut bien savoir les basiques', '2025-04-12 18:34:32'),
(34, 19, 'Exemples + Bibliographie', NULL, '2025-04-12 18:34:32'),
(35, 20, 'test', NULL, '2025-04-12 19:07:29'),
(36, 21, 'test', NULL, '2025-04-12 19:07:55'),
(37, 22, 'Intro', NULL, '2025-04-13 15:09:56'),
(38, 22, 'Exemples', NULL, '2025-04-13 15:09:56'),
(39, 22, 'Histoire', NULL, '2025-04-13 15:09:56'),
(40, 22, 'Les tables', NULL, '2025-04-13 15:09:56'),
(41, 22, 'Mysql', NULL, '2025-04-13 15:32:02'),
(42, 23, 'c', NULL, '2025-04-16 00:54:17'),
(43, 23, 'v', NULL, '2025-04-16 00:54:17'),
(44, 24, 'les matrices', NULL, '2025-04-17 12:58:39'),
(45, 24, 'les matrices2', NULL, '2025-04-17 12:58:39'),
(46, 24, 'aze', NULL, '2025-04-17 13:11:27');

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
(1, 24, '16c093bd15fdb2fe0e095ce2c780b3aa', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '::1', 35.735304, -5.8893164, '2025-04-15 00:14:49', 'pending'),
(2, 24, '09da2cd039ab4f7b2d6f3a44ac94275e', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '::1', 35.735304, -5.8893164, '2025-04-15 00:35:10', 'denied'),
(3, 24, '09da2cd039ab4f7b2d6f3a44ac94275e', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '::1', 35.735304, -5.8893164, '2025-04-15 00:35:42', 'pending'),
(4, 20, 'efd0b963bcd740c2d339046c55cbce77', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1', 35.736997, -5.895445, '2025-04-17 14:12:55', 'approved'),
(5, 27, '1c71aca27f7948b0bdaa722b6c4fb6b3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '::1', 35.7386883, -5.8881645, '2025-04-17 15:19:44', 'approved');

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
(2, 'Bac+3', 'Third year post-baccalaureate', '2025-04-09 14:11:33'),
(3, 'Bac+1', '', '2025-04-15 23:29:34');

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
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `quizzes`
--

INSERT INTO `quizzes` (`id`, `title`, `subject_id`, `description`, `pdf_path`, `created_at`, `updated_at`) VALUES
(1, 'Exam base de données', 1, 'Exam base de données', '../uploads/quizzes/67fc19304dd32_Resultat-final-Ingenieur-dEtat.pdf', '2025-04-13 21:06:08', '2025-04-13 21:06:08'),
(2, 'test', 3, 'aze', '../uploads/quizzes/67fc1998d5308_oncf-voyages-ismail haddad.pdf', '2025-04-13 21:07:52', '2025-04-13 21:07:52');

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
(1, 2, 21, '../uploads/quiz_submissions/67fc19a7637a3_Resultat-final-Ingenieur-dEtat.pdf', 18, 'bien', '2025-04-13 21:08:07', '2025-04-13 21:21:22');

-- --------------------------------------------------------

--
-- Structure de la table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('pending','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `device_id` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `device_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `level_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `session_status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'active',
  `education_level` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `current_institution` varchar(255) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `students`
--

INSERT INTO `students` (`id`, `full_name`, `email`, `password`, `status`, `device_id`, `device_name`, `latitude`, `longitude`, `level_id`, `created_at`, `session_status`, `education_level`, `phone`, `city`, `current_institution`, `date_of_birth`, `gender`) VALUES
(1, 'Ali BenAhmed', 'ali@example.com', 'student123', 'pending', '', NULL, NULL, NULL, 1, '2025-04-09 14:11:33', 'active', NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'Sara ElHadi', 'sara@example.com', 'student123', 'pending', '', NULL, NULL, NULL, 1, '2025-04-09 14:11:33', 'active', NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'Omar Kadir', 'omar@example.com', 'student123', 'pending', '', NULL, NULL, NULL, 2, '2025-04-09 14:11:33', 'active', NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'Ali BenAhmedxxx', 'zouhair@t.fr', '$2y$10$4AYc9NFNR3deOwvIEpYZqe2I8Z3bHTYILhP1gcGhe7OEalA2EU3be', 'approved', '753d70d7-1d60-4aa7-a64f-db834d474fa0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 35.73547220, -5.88933500, 1, '2025-04-11 00:08:27', 'active', NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'maro', 'm@m.com', '$2y$10$fWAtt.ey7Lebi/vDVmPhvesvutEmWn40MAdVguROQJbbp45HgnWky', 'approved', 'd612b836-50be-4d3e-8155-7cb178bce63e', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 35.73547220, -5.88933500, 1, '2025-04-11 00:15:17', 'active', NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'maro', 'xm@m.com', '$2y$10$9s48UTZqtfkIE1AK0a97heODFgtoDbHCvmJLgNhmfdot399E6DcXO', 'approved', '2f62cc96-e5ca-4314-b209-391c944461cd', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 35.73547220, -5.88933500, 1, '2025-04-11 00:34:13', 'active', NULL, NULL, NULL, NULL, NULL, NULL),
(8, 'mar1', 't@t.com', '$2y$10$suWT96YsjQS.ENT5z75HN.cR7.QfcmrVshwHIW/Je09LiiFAM9rue', 'approved', 'd3921bd3-7ebc-4f84-ac2b-9cd8f2b8c4d1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 35.73547220, -5.88933500, 1, '2025-04-11 00:41:31', 'active', NULL, NULL, NULL, NULL, NULL, NULL),
(9, 'mar2', 'a@a.com', '$2y$10$5hhGEDqga3S/PfDvVz5F2e3iRNbLM.2lfAUZyUAvn.JS6cSRa5PcS', 'approved', 'd3921bd3-7ebc-4f84-ac2b-9cd8f2b8c4d1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 35.73547220, -5.88933500, 1, '2025-04-11 00:42:35', 'active', NULL, NULL, NULL, NULL, NULL, NULL),
(10, 'michu', 'z@z.com', '$2y$10$HBJnNVfs6vNruoFcu6gmHuoGyIZwusNLuIsJc0BHx7mK1RG1MDCQC', 'pending', '604f2ea3-7488-4c2e-ad6b-4b541e0d56e9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 0.00000000, 0.00000000, NULL, '2025-04-11 00:43:22', 'active', NULL, NULL, NULL, NULL, NULL, NULL),
(11, 'michu2xx', 'e@e.com', '$2y$10$NzO7gmj1eHz9V4jlswQJAemhtgglpJtQw3HUu5pHvAsJxMqAWHXqq', 'approved', 'd3921bd3-7ebc-4f84-ac2b-9cd8f2b8c4d1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 35.73547220, -5.88933500, 1, '2025-04-11 00:44:09', 'active', NULL, NULL, NULL, NULL, NULL, NULL),
(12, 'mar3', 'r@r.com', '$2y$10$R.2RWfwqEwbvQQalHgHpGO98VFQxQaN6Oqsy2c5MRwHc2kK09WK8a', 'approved', 'd3921bd3-7ebc-4f84-ac2b-9cd8f2b8c4d1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 35.73547220, -5.88933500, NULL, '2025-04-11 01:03:58', 'active', NULL, NULL, NULL, NULL, NULL, NULL),
(15, 'MAROUANE HADDAD', 'marouanehaddad08@gmail.com', '$2y$10$7s70Iv31utajASfMtSBf0u0XuKFMkjF.3WL8S1Mj2Y7d5WlWYRSdG', 'approved', 'd3921bd3-7ebc-4f84-ac2b-9cd8f2b8c4d1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 35.73548510, -5.88934810, 1, '2025-04-11 02:45:16', 'active', NULL, NULL, NULL, NULL, NULL, NULL),
(16, 'MAROUANE HADDAD', 'marouanehaddad09@gmail.com', '$2y$10$IcJNMIXlXllII1p90A9mcesWVNTgtlzTCuG9x/mcpyIUN1gQ34Vvq', 'approved', '604f2ea3-7488-4c2e-ad6b-4b541e0d56e9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 35.73549920, -5.88935180, 1, '2025-04-11 03:23:54', 'active', NULL, NULL, NULL, NULL, NULL, NULL),
(17, 'Marouane Sami', 'm@m.fr', '$2y$10$lEXoCLx8NTxkTPfuTJA/0.hoFXDSNuBq27mRxdqjpEhPjvO6rlPMS', 'approved', '275f8639-96f8-4db7-b9db-4d61d10d95e2', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 35.73270000, -5.89160000, 1, '2025-04-11 11:27:49', 'active', NULL, NULL, NULL, NULL, NULL, NULL),
(18, 'Wiame Rachade', 'w@w.com', '$2y$10$obIdwu7T9uwB4v0KGWDwLOfaMvMR0ijM2PSV63Jey90QF6NRhFxNq', 'approved', 'd1c73fa8-f52c-4f8a-b182-4045853c9ccb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 35.73747000, -5.89478800, 2, '2025-04-11 15:48:48', 'active', NULL, NULL, NULL, NULL, NULL, NULL),
(19, 'michu haddad', 'x@x.x', '$2y$10$zZaeqYT4xf6u1xNEbRwA2.4cQFRe2tIwWmfaf0cE.dq3SDxp92hS.', 'approved', '9bb8c812-974b-4ca7-b6d4-f420700508c3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 35.75644160, -5.77044480, 2, '2025-04-12 16:05:04', 'active', NULL, NULL, NULL, NULL, NULL, NULL),
(20, 'Wiame Rachade', 'v@v.v', '$2y$10$L77ck8f2bargustfnCjJGeWW9.Q7z4BcU649c5oQChQjSeKLJQvba', 'approved', 'd1c73fa8-f52c-4f8a-b182-4045853c9ccb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 35.73408900, -5.88688100, 3, '2025-04-12 18:31:53', 'active', NULL, NULL, NULL, NULL, NULL, NULL),
(21, 'michu weldi', 'michu@michu.com', '$2y$10$jYM9ybvteIwjDOiPFfaZVe/6L01C7Mb3/cM7oXJDs2GFZ5mCywNq6', 'approved', 'd3921bd3-7ebc-4f84-ac2b-9cd8f2b8c4d1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 35.73552950, -5.88930730, 1, '2025-04-13 15:13:11', 'active', NULL, NULL, NULL, NULL, NULL, NULL),
(22, 'ghazali', 'g@g.g', '$2y$10$EU6vZTFMTbt4M17Y8lBjNOAIoi5Vd2zYIRcm3nF5u6drn/KoBKN8y', 'approved', '', NULL, NULL, NULL, 2, '2025-04-14 23:49:17', 'active', NULL, NULL, NULL, NULL, NULL, NULL),
(23, 'mar mar', '1@1.1', '$2y$10$WPn0GLRViHHNbZc/JNHDLurutwx6KxwHvGZu5YQklJ71AxaTcUHhK', 'approved', '', NULL, NULL, NULL, NULL, '2025-04-15 00:11:29', 'active', NULL, NULL, NULL, NULL, NULL, NULL),
(24, 'mar mar 55', 'm@m.mx', '$2y$10$dbZHI2AjwotZHAxbMrB7L.zLgyooKwppBIRyYbYaWGr.Sf6WNXxWq', 'approved', '', NULL, NULL, NULL, 1, '2025-04-15 00:12:13', 'active', NULL, NULL, NULL, NULL, NULL, NULL),
(25, 'MAROUANE HADDAD', 'marouanehaddad08+1@gmail.com', '$2y$10$5fqFcsiJzOuwgPOoxdlbgOtQ7UBUosL4cZCTvTOsm4z3BVzLT5kIa', 'approved', '', NULL, NULL, NULL, NULL, '2025-04-15 01:48:37', 'active', NULL, NULL, NULL, NULL, NULL, NULL),
(26, 'michu haddad', 'x@x.xx', '$2y$10$wqEwr1tYOEoayyWV4l84Oer/zWjPO0pqjnAKijT7map92vAzgWiQK', 'approved', '', NULL, NULL, NULL, 2, '2025-04-17 13:06:42', 'active', NULL, NULL, NULL, NULL, NULL, NULL),
(27, 'Marouane Haddad', '9@9.9', '$2y$10$8e26M.pTNuTXk.9IEJrDZuRMlNQuZ1YpwrajuZVSNwJfo7k.Xmzkm', 'approved', '', NULL, NULL, NULL, 2, '2025-04-17 15:04:09', 'active', NULL, NULL, NULL, NULL, NULL, NULL),
(28, 'Marouane Haddad', 'aminxa@example.com', '$2y$10$aDx4/Z.CcWLe0JpPJTiYpO.RzCQQMSeeiI60fmebUiYe2bTy.hm3G', 'approved', 'efd0b963bcd740c2d339046c55cbce77', '0', 35.73360000, -5.89210000, 3, '2025-04-17 15:16:58', 'active', 'Bac+2', '0613508702', 'Kénitra', 'Ibn tofail', '2025-04-17', 'Male'),
(29, 'malak', 'test@test.com', '$2y$10$XErsXOYq5VP9tFyVi9k66OHvIVjkEdDIVzmZ8NE07b7bdL5b53lRy', 'approved', '', NULL, NULL, NULL, 3, '2025-04-17 15:22:46', 'active', NULL, NULL, NULL, NULL, NULL, NULL);

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
(5, 20, 18, '2025-04-12 19:05:45'),
(6, 21, 22, '2025-04-13 15:19:47'),
(7, 22, 18, '2025-04-14 23:51:14'),
(8, 22, 20, '2025-04-14 23:51:14'),
(10, 24, 18, '2025-04-15 00:36:31'),
(11, 24, 20, '2025-04-15 00:36:31'),
(13, 24, 22, '2025-04-15 00:36:43'),
(14, 26, 23, '2025-04-17 13:07:21'),
(15, 26, 24, '2025-04-17 13:07:21'),
(17, 26, 18, '2025-04-17 13:31:59'),
(18, 20, 24, '2025-04-17 14:14:25'),
(19, 27, 19, '2025-04-17 15:19:07'),
(20, 29, 24, '2025-04-17 15:23:19');

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
(1, 23, '9e471bbddb32cf5bc453ea260583c215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1', 35.735371, -5.889729, '2025-04-15 00:11:29', 'approved'),
(2, 24, '9e471bbddb32cf5bc453ea260583c215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1', 35.735371, -5.889729, '2025-04-15 00:12:13', 'approved'),
(3, 24, '16c093bd15fdb2fe0e095ce2c780b3aa', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '::1', 35.735304, -5.8893164, '2025-04-15 00:15:05', 'approved'),
(4, 25, '9e471bbddb32cf5bc453ea260583c215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1', 35.735371, -5.889729, '2025-04-15 01:48:37', 'approved'),
(5, 26, '1c71aca27f7948b0bdaa722b6c4fb6b3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '::1', 35.736972, -5.8955627, '2025-04-17 13:06:42', 'approved'),
(6, 20, 'efd0b963bcd740c2d339046c55cbce77', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1', 35.736997, -5.895445, '2025-04-17 14:13:04', 'approved'),
(7, 27, 'efd0b963bcd740c2d339046c55cbce77', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1', 35.736997, -5.895445, '2025-04-17 15:04:09', 'approved'),
(8, 27, '1c71aca27f7948b0bdaa722b6c4fb6b3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '::1', 35.7386883, -5.8881645, '2025-04-17 15:19:55', 'approved'),
(9, 29, 'efd0b963bcd740c2d339046c55cbce77', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1', 35.736997, -5.895445, '2025-04-17 15:22:46', 'approved');

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
(25, 18, 5, '2025-04-11 15:50:02', 0),
(26, 19, 4, '2025-04-12 16:07:27', 0),
(28, 20, 5, '2025-04-12 18:33:28', 1),
(29, 20, 4, '2025-04-12 19:05:45', 0),
(30, 21, 3, '2025-04-13 15:19:47', 1),
(31, 22, 4, '2025-04-14 23:51:14', 1),
(32, 24, 4, '2025-04-15 00:36:31', 1),
(33, 24, 3, '2025-04-15 00:36:43', 0),
(34, 26, 6, '2025-04-17 13:07:21', 1),
(35, 26, 4, '2025-04-17 13:31:59', 0),
(36, 20, 6, '2025-04-17 14:14:25', 0),
(37, 27, 5, '2025-04-17 15:19:07', 0),
(38, 29, 6, '2025-04-17 15:23:19', 0);

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
(5, 'Physics', 2, '2025-04-09 14:11:33'),
(6, 'fitlife', 3, '2025-04-15 23:39:34');

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
(1, 24, '2025-04-15 00:29:39', '2025-04-15 00:30:14', 35.735371, -5.889729, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(2, 24, '2025-04-15 00:34:56', '2025-04-15 01:47:56', 35.735371, -5.889729, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(3, 24, '2025-04-16 01:08:42', '2025-04-16 21:42:02', 35.735371, -5.889729, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(4, 24, '2025-04-16 21:53:49', '2025-04-16 21:53:57', 35.734463, -5.889782, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(5, 24, '2025-04-16 21:56:45', '2025-04-16 23:47:19', 35.734463, -5.889782, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(6, 24, '2025-04-16 21:56:49', '2025-04-16 23:47:19', 35.734463, -5.889782, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(7, 24, '2025-04-16 21:56:53', '2025-04-16 23:47:19', 35.734463, -5.889782, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(8, 24, '2025-04-16 21:57:02', '2025-04-16 22:20:32', 35.734463, -5.889782, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(9, 24, '2025-04-16 22:20:42', '2025-04-16 23:44:40', 35.734463, -5.889782, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(10, 24, '2025-04-16 23:44:45', '2025-04-16 23:46:58', 35.73526, -5.88975, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(11, 24, '2025-04-16 23:47:02', '2025-04-16 23:50:39', 35.73526, -5.88975, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(12, 24, '2025-04-16 23:50:42', '2025-04-16 23:50:58', 35.73526, -5.88975, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(13, 24, '2025-04-16 23:51:05', '2025-04-16 23:54:50', 35.73526, -5.88975, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(14, 24, '2025-04-16 23:51:09', '2025-04-16 23:54:50', 35.73526, -5.88975, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(15, 24, '2025-04-16 23:51:13', '2025-04-16 23:54:50', 35.73526, -5.88975, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(16, 24, '2025-04-16 23:54:28', '2025-04-16 23:54:50', 35.73526, -5.88975, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(17, 24, '2025-04-16 23:55:06', '2025-04-16 23:56:28', 35.73526, -5.88975, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(18, 24, '2025-04-16 23:58:34', NULL, 35.73526, -5.88975, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(19, 26, '2025-04-17 13:07:14', '2025-04-17 15:19:29', 35.736972, -5.8955627, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '::1'),
(20, 20, '2025-04-17 14:13:11', '2025-04-17 14:23:31', 35.736997, -5.895445, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(21, 27, '2025-04-17 15:04:26', '2025-04-17 15:10:38', 35.7336, -5.8921, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(22, 27, '2025-04-17 15:18:26', NULL, 35.7336, -5.8921, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(23, 27, '2025-04-17 15:20:02', NULL, 35.7386883, -5.8881645, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '::1'),
(24, 27, '2025-04-17 15:22:04', '2025-04-17 15:22:30', 35.7336, -5.8921, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1'),
(25, 29, '2025-04-17 15:23:27', NULL, 35.736997, -5.895445, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '::1');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=179;

--
-- AUTO_INCREMENT pour la table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT pour la table `course_contents`
--
ALTER TABLE `course_contents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT pour la table `course_folders`
--
ALTER TABLE `course_folders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT pour la table `device_attempts`
--
ALTER TABLE `device_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `levels`
--
ALTER TABLE `levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `quiz_submissions`
--
ALTER TABLE `quiz_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT pour la table `student_courses`
--
ALTER TABLE `student_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `student_devices`
--
ALTER TABLE `student_devices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `student_subjects`
--
ALTER TABLE `student_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT pour la table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

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
