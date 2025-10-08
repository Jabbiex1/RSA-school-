-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 08, 2025 at 01:47 AM
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
-- Database: `royal_academy`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(1, 'admin', 'admin@example.com', '0192023a7bbd73250516f069df18b500', '2025-08-28 20:32:01'),
(3, 'admin', 'admin@exampl.com', '$2y$10$Ie2Mwc7zL8lj/yHwdN8.puej2Q1lS6egng5Z1PK1wzDD/Af9pzPhG', '2025-08-28 23:15:27'),
(4, 'santigie', 'santigie@gmail.com', '$2y$10$.4JZ7aSq1nT.6hX1bIp81OVWleqVJFZUtw3ulzZ4WhDog1q7S/9uO', '2025-08-28 23:17:55');

-- --------------------------------------------------------

--
-- Table structure for table `admissions`
--

CREATE TABLE `admissions` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `date_of_birth` date NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `previous_school` varchar(100) NOT NULL,
  `applying_class` varchar(50) NOT NULL,
  `admission_status` enum('Pending','Approved','Denied') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admissions`
--

INSERT INTO `admissions` (`id`, `full_name`, `date_of_birth`, `gender`, `email`, `phone`, `previous_school`, `applying_class`, `admission_status`, `created_at`, `status`) VALUES
(1, 'Esther Fatmata Vandy', '2025-08-29', 'Male', 'tcsvandyesther@gmail.com', '034230188', 'kamara', 'Class 4', 'Pending', '2025-08-16 02:17:13', 'Pending'),
(2, 'Abdulai Jabbie', '2025-08-10', 'Male', 'jabbieabdulai582@gmail.com', '099787878', 'Maranatha', '6', 'Pending', '2025-08-17 17:28:43', 'Pending'),
(3, 'Abdulai Jabbie', '2025-08-10', 'Male', 'jabbieabdulai5@gmail.com', '099787878', 'Maranatha', '6', 'Pending', '2025-08-17 17:36:55', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `averages`
--

CREATE TABLE `averages` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `term` varchar(20) NOT NULL,
  `average` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `averages`
--

INSERT INTO `averages` (`id`, `student_id`, `term`, `average`) VALUES
(1, '7', 'Third Term', 26.52),
(2, '7', 'First Term', 0.00),
(3, '7', 'Second Term', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` varchar(20) NOT NULL,
  `title` varchar(150) NOT NULL,
  `type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `title`, `type`) VALUES
('C4218', 'System', 'International');

-- --------------------------------------------------------

--
-- Table structure for table `course_materials`
--

CREATE TABLE `course_materials` (
  `id` int(11) NOT NULL,
  `course_id` varchar(20) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `filedata` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_materials`
--

INSERT INTO `course_materials` (`id`, `course_id`, `filename`, `filedata`) VALUES
(1, 'C4218', 'Budget explaination.docx', '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');

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) DEFAULT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `assignment` int(11) DEFAULT NULL,
  `test` int(11) DEFAULT NULL,
  `exam` int(11) DEFAULT NULL,
  `total` int(11) DEFAULT NULL,
  `grade` varchar(5) DEFAULT NULL,
  `term` varchar(10) DEFAULT NULL,
  `terms` varchar(20) NOT NULL DEFAULT 'First Term'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`id`, `student_id`, `subject`, `assignment`, `test`, `exam`, `total`, `grade`, `term`, `terms`) VALUES
(97, '20', 'Civics', 15, 10, 45, NULL, NULL, NULL, 'First Term'),
(98, '20', 'Creative and Innovation Studies', 16, 33, 46, NULL, NULL, NULL, 'First Term'),
(99, '20', 'Dictation and Spelling', 9, 15, 33, NULL, NULL, NULL, 'First Term'),
(100, '20', 'English II', 5, 30, 46, NULL, NULL, NULL, 'First Term'),
(101, '20', 'English Sentence Pattern & Structure (ESP & S)', 14, 24, 29, NULL, NULL, NULL, 'First Term'),
(102, '20', 'Group Reading', 17, 12, 50, NULL, NULL, NULL, 'First Term'),
(103, '20', 'Information and Communication Technology (ICT)', 11, 15, 20, NULL, NULL, NULL, 'First Term'),
(104, '20', 'Literature', 13, 23, 44, NULL, NULL, NULL, 'First Term'),
(105, '20', 'Mathematics', 12, 17, 35, NULL, NULL, NULL, 'First Term'),
(106, '20', 'Oral and Writing', 11, 28, 36, NULL, NULL, NULL, 'First Term'),
(107, '20', 'Physical Health Education', 10, 29, 37, NULL, NULL, NULL, 'First Term'),
(108, '20', 'Prevocational Studies', 9, 28, 38, NULL, NULL, NULL, 'First Term'),
(109, '20', 'Quantitative Aptitude', 8, 28, 39, NULL, NULL, NULL, 'First Term'),
(110, '20', 'Reading', 6, 27, 40, NULL, NULL, NULL, 'First Term'),
(111, '20', 'Religious Moral Education', 7, 26, 41, NULL, NULL, NULL, 'First Term'),
(112, '20', 'Science', 9, 26, 42, NULL, NULL, NULL, 'First Term'),
(113, '20', 'Social Studies', 9, 25, 43, NULL, NULL, NULL, 'First Term'),
(114, '20', 'Spelling and Writing', 10, 24, 44, NULL, NULL, NULL, 'First Term'),
(115, '20', 'Verbal Aptitude', 12, 24, 45, NULL, NULL, NULL, 'First Term'),
(116, '20', 'Civics', 27, 10, 45, NULL, NULL, NULL, 'Second Term'),
(117, '20', 'Creative and Innovation Studies', 16, 33, 46, NULL, NULL, NULL, 'Second Term'),
(118, '20', 'Dictation and Spelling', 9, 15, 33, NULL, NULL, NULL, 'Second Term'),
(119, '20', 'English II', 5, 30, 41, NULL, NULL, NULL, 'Second Term'),
(120, '20', 'English Sentence Pattern & Structure (ESP & S)', 14, 24, 29, NULL, NULL, NULL, 'Second Term'),
(121, '20', 'Group Reading', 17, 12, 50, NULL, NULL, NULL, 'Second Term'),
(122, '20', 'Information and Communication Technology (ICT)', 11, 15, 20, NULL, NULL, NULL, 'Second Term'),
(123, '20', 'Literature', 13, 23, 44, NULL, NULL, NULL, 'Second Term'),
(124, '20', 'Mathematics', 12, 17, 35, NULL, NULL, NULL, 'Second Term'),
(125, '20', 'Oral and Writing', 11, 28, 36, NULL, NULL, NULL, 'Second Term'),
(126, '20', 'Physical Health Education', 10, 29, 37, NULL, NULL, NULL, 'Second Term'),
(127, '20', 'Prevocational Studies', 9, 28, 38, NULL, NULL, NULL, 'Second Term'),
(128, '20', 'Quantitative Aptitude', 8, 28, 39, NULL, NULL, NULL, 'Second Term'),
(129, '20', 'Reading', 6, 27, 40, NULL, NULL, NULL, 'Second Term'),
(130, '20', 'Religious Moral Education', 7, 26, 41, NULL, NULL, NULL, 'Second Term'),
(131, '20', 'Science', 9, 26, 42, NULL, NULL, NULL, 'Second Term'),
(132, '20', 'Social Studies', 9, 25, 43, NULL, NULL, NULL, 'Second Term'),
(133, '20', 'Spelling and Writing', 10, 24, 44, NULL, NULL, NULL, 'Second Term'),
(134, '20', 'Verbal Aptitude', 16, 24, 45, NULL, NULL, NULL, 'Second Term'),
(164, '19', 'Civics', 15, 30, 45, NULL, NULL, NULL, 'Third Term'),
(165, '19', 'Creative and Innovation Studies', 16, 33, 46, NULL, NULL, NULL, 'Third Term'),
(166, '19', 'Drama', 9, 15, 33, NULL, NULL, NULL, 'Third Term'),
(167, '19', 'Environmental Studies', 5, 30, 41, NULL, NULL, NULL, 'Third Term'),
(168, '19', 'Information and Communication Technology (ICT)', 14, 24, 29, NULL, NULL, NULL, 'Third Term'),
(169, '19', 'Music', 17, 12, 50, NULL, NULL, NULL, 'Third Term'),
(170, '19', 'Numeracy', 11, 15, 20, NULL, NULL, NULL, 'Third Term'),
(171, '19', 'Oral Expression', 13, 23, 44, NULL, NULL, NULL, 'Third Term'),
(172, '19', 'Oral News', 12, 17, 35, NULL, NULL, NULL, 'Third Term'),
(173, '19', 'Physical Health Education', 11, 28, 36, NULL, NULL, NULL, 'Third Term'),
(174, '19', 'Pre-reading', 10, 29, 37, NULL, NULL, NULL, 'Third Term'),
(175, '19', 'Pre-writing', 9, 28, 38, NULL, NULL, NULL, 'Third Term'),
(176, '19', 'Quantitative Aptitude', 8, 28, 39, NULL, NULL, NULL, 'Third Term'),
(177, '19', 'Civics', 15, 10, 50, NULL, NULL, NULL, 'First Term'),
(178, '19', 'Creative and Innovation Studies', 16, 25, 46, NULL, NULL, NULL, 'First Term'),
(179, '19', 'Story Telling', 20, 12, 50, NULL, NULL, NULL, 'First Term'),
(180, '19', 'Verbal Aptitude', 22, 24, 45, NULL, NULL, NULL, 'Third Term'),
(181, '19', 'Drama', 9, 15, 33, NULL, NULL, NULL, 'First Term'),
(182, '19', 'Environmental Studies', 5, 30, 41, NULL, NULL, NULL, 'First Term'),
(183, '19', 'Information and Communication Technology (ICT)', 12, 0, 0, NULL, NULL, NULL, 'First Term'),
(184, '7', 'Agricultural Science', 0, 0, 0, NULL, NULL, NULL, 'First Term'),
(185, '7', 'Civics', 0, 0, 0, NULL, NULL, NULL, 'First Term'),
(186, '7', 'Creative and Innovation Studies', 0, 0, 0, NULL, NULL, NULL, 'First Term'),
(187, '7', 'English I', 0, 0, 0, NULL, NULL, NULL, 'First Term'),
(188, '7', 'English II', 0, 0, 0, NULL, NULL, NULL, 'First Term'),
(189, '7', 'Home Economics', 0, 0, 0, NULL, NULL, NULL, 'First Term'),
(190, '7', 'Information and Communication Technology (ICT)', 0, 0, 0, NULL, NULL, NULL, 'First Term'),
(191, '7', 'Literature', 0, 0, 0, NULL, NULL, NULL, 'First Term'),
(192, '7', 'Mathematics', 0, 0, 0, NULL, NULL, NULL, 'First Term'),
(193, '7', 'Physical Health Education', 0, 0, 0, NULL, NULL, NULL, 'First Term'),
(194, '7', 'Quantitative Aptitude', 0, 0, 0, NULL, NULL, NULL, 'First Term'),
(195, '7', 'Reading', 0, 0, 0, NULL, NULL, NULL, 'First Term'),
(196, '7', 'Religious Moral Education', 0, 0, 0, NULL, NULL, NULL, 'First Term'),
(197, '7', 'Science', 0, 0, 0, NULL, NULL, NULL, 'First Term'),
(198, '7', 'Social Studies', 0, 0, 0, NULL, NULL, NULL, 'First Term'),
(199, '7', 'Verbal Aptitude', 0, 0, 0, NULL, NULL, NULL, 'First Term'),
(200, '7', 'Agricultural Science', 8, 35, 45, NULL, NULL, NULL, 'Third Term'),
(201, '7', 'Civics', 9, 33, 45, NULL, NULL, NULL, 'Third Term'),
(202, '7', 'Creative and Innovation Studies', 8, 34, 46, NULL, NULL, NULL, 'Third Term'),
(203, '7', 'English I', 7, 33, 48, NULL, NULL, NULL, 'Third Term'),
(204, '7', 'English II', 9, 36, 49, NULL, NULL, NULL, 'Third Term'),
(205, '7', 'Home Economics', 5, 25, 30, NULL, NULL, NULL, 'Third Term'),
(206, '7', 'Information and Communication Technology (ICT)', 7, 29, 35, NULL, NULL, NULL, 'Third Term'),
(207, '7', 'Literature', 8, 29, 45, NULL, NULL, NULL, 'Third Term'),
(208, '7', 'Mathematics', 10, 30, 43, NULL, NULL, NULL, 'Third Term'),
(209, '7', 'Physical Health Education', 8, 34, 44, NULL, NULL, NULL, 'Third Term'),
(210, '7', 'Quantitative Aptitude', 8, 28, 39, NULL, NULL, NULL, 'Third Term'),
(211, '7', 'Reading', 6, 27, 40, NULL, NULL, NULL, 'Third Term'),
(212, '7', 'Religious Moral Education', 7, 26, 30, NULL, NULL, NULL, 'Third Term'),
(213, '7', 'Science', 9, 26, 42, NULL, NULL, NULL, 'Third Term'),
(214, '7', 'Social Studies', 9, 25, 43, NULL, NULL, NULL, 'Third Term'),
(215, '7', 'Verbal Aptitude', 9, 33, 39, NULL, NULL, NULL, 'Third Term'),
(216, '7', 'Agricultural Science', 0, 0, 0, NULL, NULL, NULL, 'Second Term'),
(217, '7', 'Civics', 0, 0, 0, NULL, NULL, NULL, 'Second Term'),
(218, '7', 'Creative and Innovation Studies', 0, 0, 0, NULL, NULL, NULL, 'Second Term'),
(219, '7', 'English I', 0, 0, 0, NULL, NULL, NULL, 'Second Term'),
(220, '7', 'English II', 0, 0, 0, NULL, NULL, NULL, 'Second Term'),
(221, '7', 'Home Economics', 0, 0, 0, NULL, NULL, NULL, 'Second Term'),
(222, '7', 'Information and Communication Technology (ICT)', 0, 0, 0, NULL, NULL, NULL, 'Second Term'),
(223, '7', 'Literature', 0, 0, 0, NULL, NULL, NULL, 'Second Term'),
(224, '7', 'Mathematics', 0, 0, 0, NULL, NULL, NULL, 'Second Term'),
(225, '7', 'Physical Health Education', 0, 0, 0, NULL, NULL, NULL, 'Second Term'),
(226, '7', 'Quantitative Aptitude', 0, 0, 0, NULL, NULL, NULL, 'Second Term'),
(227, '7', 'Reading', 0, 0, 0, NULL, NULL, NULL, 'Second Term'),
(228, '7', 'Religious Moral Education', 0, 0, 0, NULL, NULL, NULL, 'Second Term'),
(229, '7', 'Science', 0, 0, 0, NULL, NULL, NULL, 'Second Term'),
(230, '7', 'Social Studies', 0, 0, 0, NULL, NULL, NULL, 'Second Term'),
(231, '7', 'Verbal Aptitude', 0, 0, 0, NULL, NULL, NULL, 'Second Term');

-- --------------------------------------------------------

--
-- Table structure for table `lecturers`
--

CREATE TABLE `lecturers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `class` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecturers`
--

INSERT INTO `lecturers` (`id`, `name`, `subject`, `email`, `password`, `class`) VALUES
(15, 'Mr Alusine', '', 'alusine@gmail.com', '$2y$10$85sCnQ5mIMP6h8pKmCEdA.Ft2fM6F.M9DhLaMgrphMAuhW0BLKEgu', 'Class 1'),
(16, 'Mr Alusine', '', 'teacher@gmail.com', '$2y$10$jHFPedJr3Ht.29XxqV4yYuTJjXMwDIdxjTdGqKLji8kjYQRkzcr3G', 'Class 2'),
(18, 'Mr Alusine', '', 'admin@gmail.com', '$2y$10$sW/0albvAI64F4n8ylmSH.iH65BrzzH6nwmWf47yxlhHs5cD8aVd2', 'Class 2'),
(20, 'Mr Sheriff', '', 'sheriff@gmail.com', '$2y$10$Zd0S4XL.cCsT6sxvm0dqLOeXbcTsv0wdcMI29T/cQT5seer.p3DC6', 'Jss 1'),
(21, 'Mr Vandy', '', 'vandy@gmail.com', '$2y$10$/iKsfO4mhf7h1hPH0nN2.eJP6pUDAy2UAOW8YSH7Xn07alUFE.yIm', 'Class 5');

-- --------------------------------------------------------

--
-- Table structure for table `parents`
--

CREATE TABLE `parents` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) DEFAULT NULL,
  `parent_email` varchar(100) NOT NULL,
  `parent_phone` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parents`
--

INSERT INTO `parents` (`id`, `student_id`, `parent_email`, `parent_phone`) VALUES
(8, '20', 'jalloh@gmail.com', ''),
(11, '19', 'santigie@gmail.com', ''),
(12, '19', 'parent@gmail.com', ''),
(13, '20', 'tcsvandyesther@gmail.com', ''),
(19, '20', '', '088323232'),
(20, '20', '', '099809070');

-- --------------------------------------------------------

--
-- Table structure for table `pending_students`
--

CREATE TABLE `pending_students` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `dob` date NOT NULL,
  `class` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `previous_school` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `terms` varchar(20) NOT NULL,
  `position` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`id`, `student_id`, `terms`, `position`) VALUES
(1, '20', 'First Term', '9'),
(2, '20', 'Second Term', '9'),
(5, '19', 'Third Term', '3'),
(6, '19', 'First Term', '1'),
(7, '7', 'First Term', '2'),
(8, '7', 'Third Term', '1');

-- --------------------------------------------------------

--
-- Table structure for table `registered_students`
--

CREATE TABLE `registered_students` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `class` varchar(50) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `course` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registered_students`
--

INSERT INTO `registered_students` (`id`, `student_id`, `name`, `class`, `dob`, `gender`, `email`, `phone`, `address`, `course`, `created_at`) VALUES
(1, '902003082', 'Nuru', 'semester 1', '2025-07-06', 'Male', 'nuru@gmail.com', '8898989', '7A Haja Sweray Drive\nImatt\nFreetown\nSierra Leone', 'BIT', '2025-07-19 21:41:16');

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

CREATE TABLE `results` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `subjects` varchar(100) NOT NULL,
  `term` varchar(20) NOT NULL,
  `score` int(11) NOT NULL,
  `class` varchar(50) DEFAULT NULL,
  `year` year(4) DEFAULT year(curdate())
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `class` varchar(50) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `parent_email` varchar(100) DEFAULT NULL,
  `source` varchar(20) DEFAULT 'manual',
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `previous_school` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `class`, `dob`, `parent_email`, `source`, `email`, `phone`, `previous_school`) VALUES
('19', 'Abdulai Kamara', 'Class 1', '2022-07-15', NULL, 'manual', NULL, NULL, NULL),
('20', 'jalloh', 'class 4', '2025-07-06', NULL, 'manual', NULL, NULL, NULL),
('34', 'Abdulai Kamara', 'Jss 3', '2022-01-14', 'parent@gmail.com', 'manual', 'jabbie@gmail.com', '+232447788', 'Logos'),
('6', 'Idrissa Sheriff Koroma', 'Jss 1', '2001-12-14', NULL, 'pending', NULL, NULL, NULL),
('7', 'Joseph Vandy', 'Class 5', '2022-02-02', NULL, 'pending', NULL, NULL, NULL),
('80', 'Alusine', 'Class 6', '2025-07-02', NULL, 'manual', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `class_name` varchar(50) NOT NULL,
  `subject_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `class_name`, `subject_name`) VALUES
(1, 'Nursery 1 & 2', 'Phonics'),
(2, 'Nursery 1 & 2', 'Reading'),
(3, 'Nursery 1 & 2', 'Handwriting'),
(4, 'Nursery 1 & 2', 'Speaking'),
(5, 'Nursery 1 & 2', 'Mathematics'),
(6, 'Nursery 1 & 2', 'Environmental Studies'),
(7, 'Nursery 1 & 2', 'Physical Health Education'),
(8, 'Nursery 1 & 2', 'Science'),
(9, 'Nursery 1 & 2', 'Oral Expression'),
(10, 'Nursery 1 & 2', 'Information and Communication Technology (ICT)'),
(11, 'Nursery 1 & 2', 'Religious Moral Education'),
(12, 'Nursery 1 & 2', 'Civics'),
(13, 'Nursery 1 & 2', 'Creative and Innovation Studies'),
(14, 'Class 1', 'Numeracy'),
(15, 'Class 1', 'Oral Expression'),
(16, 'Class 1', 'Pre-reading'),
(17, 'Class 1', 'Pre-writing'),
(18, 'Class 1', 'Civics'),
(19, 'Class 1', 'Story Telling'),
(20, 'Class 1', 'Physical Health Education'),
(21, 'Class 1', 'Drama'),
(22, 'Class 1', 'Religious Moral Education'),
(23, 'Class 1', 'Quantitative Aptitude'),
(24, 'Class 1', 'Verbal Aptitude'),
(25, 'Class 1', 'Information and Communication Technology (ICT)'),
(26, 'Class 1', 'Creative and Innovation Studies'),
(27, 'Class 1', 'Writing Dictation and Spelling'),
(28, 'Class 1', 'Environmental Studies'),
(29, 'Class 1', 'Music'),
(30, 'Class 1', 'Oral News'),
(31, 'Class 2', 'Language Arts'),
(32, 'Class 2', 'Reading and Writing'),
(33, 'Class 2', 'Mathematics'),
(34, 'Class 2', 'Creative and Innovation Studies'),
(35, 'Class 2', 'Civics'),
(36, 'Class 2', 'Reading and Word Building'),
(37, 'Class 2', 'Physical Health Education'),
(38, 'Class 2', 'Environmental Studies'),
(39, 'Class 2', 'Religious Moral Education'),
(40, 'Class 2', 'Quantitative Aptitude'),
(41, 'Class 2', 'Verbal Aptitude'),
(42, 'Class 2', 'Information and Communication Technology (ICT)'),
(43, 'Class 2', 'Reading'),
(44, 'Class 2', 'English Sentence Pattern & Structure (ESP & S)'),
(45, 'Class 2', 'Oral Expression'),
(46, 'Class 3', 'Religious Moral Education'),
(47, 'Class 3', 'Oral and Writing'),
(48, 'Class 3', 'Mathematics'),
(49, 'Class 3', 'Physical Health Education'),
(50, 'Class 3', 'English Sentence Pattern & Structure (ESP & S)'),
(51, 'Class 3', 'Prevocational Studies'),
(52, 'Class 3', 'Spellings Writing'),
(53, 'Class 3', 'Group Reading'),
(54, 'Class 3', 'Social Studies'),
(55, 'Class 3', 'Verbal Aptitude'),
(56, 'Class 3', 'Creative and Innovation Studies'),
(57, 'Class 3', 'Science'),
(58, 'Class 3', 'Information and Communication Technology (ICT)'),
(59, 'Class 3', 'Spelling and Dictation'),
(60, 'Class 3', 'Quantitative Aptitude'),
(61, 'Class 3', 'Reading'),
(62, 'Class 3', 'Literature'),
(63, 'Class 3', 'Civics'),
(64, 'Class 4', 'Religious Moral Education'),
(65, 'Class 4', 'Physical Health Education'),
(66, 'Class 4', 'Mathematics'),
(67, 'Class 4', 'Oral and Writing'),
(68, 'Class 4', 'English II'),
(69, 'Class 4', 'Science'),
(70, 'Class 4', 'Prevocational Studies'),
(71, 'Class 4', 'Reading'),
(72, 'Class 4', 'English Sentence Pattern & Structure (ESP & S)'),
(73, 'Class 4', 'Social Studies'),
(74, 'Class 4', 'Spelling and Writing'),
(75, 'Class 4', 'Information and Communication Technology (ICT)'),
(76, 'Class 4', 'Creative and Innovation Studies'),
(77, 'Class 4', 'Group Reading'),
(78, 'Class 4', 'Quantitative Aptitude'),
(79, 'Class 4', 'Dictation and Spelling'),
(80, 'Class 4', 'Literature'),
(81, 'Class 4', 'Verbal Aptitude'),
(82, 'Class 4', 'Civics'),
(83, 'Class 5', 'Mathematics'),
(84, 'Class 5', 'Religious Moral Education'),
(85, 'Class 5', 'Physical Health Education'),
(86, 'Class 5', 'English I'),
(87, 'Class 5', 'Quantitative Aptitude'),
(88, 'Class 5', 'Social Studies'),
(89, 'Class 5', 'Agricultural Science'),
(90, 'Class 5', 'Literature'),
(91, 'Class 5', 'Reading'),
(92, 'Class 5', 'Science'),
(93, 'Class 5', 'Verbal Aptitude'),
(94, 'Class 5', 'Information and Communication Technology (ICT)'),
(95, 'Class 5', 'Creative and Innovation Studies'),
(96, 'Class 5', 'English II'),
(97, 'Class 5', 'Home Economics'),
(98, 'Class 5', 'Civics'),
(99, 'Class 6', 'Mathematics'),
(100, 'Class 6', 'Physical Health Education'),
(101, 'Class 6', 'Religious Moral Education'),
(102, 'Class 6', 'Home Economics'),
(103, 'Class 6', 'Verbal Aptitude'),
(104, 'Class 6', 'Quantitative Aptitude'),
(105, 'Class 6', 'Agricultural Science'),
(106, 'Class 6', 'Social Studies'),
(107, 'Class 6', 'Science'),
(108, 'Class 6', 'English I'),
(109, 'Class 6', 'English II'),
(110, 'Class 6', 'Information and Communication Technology (ICT)'),
(111, 'Class 6', 'Civics'),
(112, 'JSS 1', 'Mathematics'),
(113, 'JSS 1', 'English Language (Grammar and Literature)'),
(114, 'JSS 1', 'Integrated Science (General Integrated Science and Biology)'),
(115, 'JSS 1', 'Social Studies'),
(116, 'JSS 1', 'Civics'),
(117, 'JSS 1', 'Religious Moral Education'),
(118, 'JSS 1', 'Physical Health Education'),
(119, 'JSS 1', 'Agricultural Science'),
(120, 'JSS 1', 'Business Studies'),
(121, 'JSS 1', 'Information and Communication Technology (ICT)'),
(122, 'JSS 1', 'Home Economics'),
(123, 'JSS 2', 'Mathematics'),
(124, 'JSS 2', 'English Language (Grammar and Literature)'),
(125, 'JSS 2', 'Integrated Science (Chemistry)'),
(126, 'JSS 2', 'Social Studies'),
(127, 'JSS 2', 'Civics'),
(128, 'JSS 2', 'Religious Moral Education'),
(129, 'JSS 2', 'Physical Health Education'),
(130, 'JSS 2', 'Agricultural Science'),
(131, 'JSS 2', 'Business Studies'),
(132, 'JSS 2', 'Information and Communication Technology (ICT)'),
(133, 'JSS 2', 'Home Economics');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `admissions`
--
ALTER TABLE `admissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `averages`
--
ALTER TABLE `averages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_student_avg` (`student_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `course_materials`
--
ALTER TABLE `course_materials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `lecturers`
--
ALTER TABLE `lecturers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `parents`
--
ALTER TABLE `parents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `pending_students`
--
ALTER TABLE `pending_students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_position` (`student_id`,`terms`);

--
-- Indexes for table `registered_students`
--
ALTER TABLE `registered_students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`);

--
-- Indexes for table `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `admissions`
--
ALTER TABLE `admissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `averages`
--
ALTER TABLE `averages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `course_materials`
--
ALTER TABLE `course_materials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=232;

--
-- AUTO_INCREMENT for table `lecturers`
--
ALTER TABLE `lecturers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `parents`
--
ALTER TABLE `parents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `pending_students`
--
ALTER TABLE `pending_students`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `registered_students`
--
ALTER TABLE `registered_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `results`
--
ALTER TABLE `results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `averages`
--
ALTER TABLE `averages`
  ADD CONSTRAINT `fk_student_avg` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `course_materials`
--
ALTER TABLE `course_materials`
  ADD CONSTRAINT `course_materials_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `parents`
--
ALTER TABLE `parents`
  ADD CONSTRAINT `parents_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `positions`
--
ALTER TABLE `positions`
  ADD CONSTRAINT `positions_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
