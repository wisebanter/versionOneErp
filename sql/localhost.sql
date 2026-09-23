-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 09, 2026 at 08:00 PM
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
-- Database: `erp_api`
--

DROP DATABASE IF EXISTS `erp_api` ;

CREATE DATABASE IF NOT EXISTS `erp_api` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `erp_api`;

-- --------------------------------------------------------

--
-- Table structure for table `academic_years`
--

CREATE TABLE `academic_years` (
  `_id` int(11) NOT NULL,
  `name` int(11) DEFAULT NULL,
  `active` enum('T','F') DEFAULT 'F'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `academic_years`
--

INSERT INTO `academic_years` (`_id`, `name`, `active`) VALUES
(1, 2021, 'F'),
(2, 2022, 'T'),
(3, 2023, 'F');

-- --------------------------------------------------------

--
-- Table structure for table `academic_years_terms`
--

CREATE TABLE `academic_years_terms` (
  `_id` int(11) NOT NULL,
  `yearidn` int(11) NOT NULL,
  `intakeid` int(11) NOT NULL,
  `sectionid` int(11) NOT NULL,
  `secnnumber` int(11) DEFAULT 1,
  `secnactive` enum('T','F') DEFAULT 'F',
  `regdeadline` datetime DEFAULT NULL,
  `startsdate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `academic_years_terms`
--

INSERT INTO `academic_years_terms` (`_id`, `yearidn`, `intakeid`, `sectionid`, `secnnumber`, `secnactive`, `regdeadline`, `startsdate`) VALUES
(1, 1, 1, 1, 1, 'F', '2025-12-22 21:38:09', NULL),
(2, 1, 1, 1, 2, 'F', '2025-12-31 21:39:06', NULL),
(3, 2, 1, 1, 1, 'F', '2025-12-31 21:58:36', NULL),
(4, 2, 1, 1, 2, 'T', '2025-12-01 22:18:54', NULL),
(5, 2, 2, 1, 1, 'T', NULL, NULL),
(6, 3, 1, 1, 2, 'F', NULL, NULL);

-- --------------------------------------------------------


--
-- Table structure for table `academic_years_terms_release`
--

CREATE TABLE `academic_years_terms_release` (
  `_id` int(11) NOT NULL,
  `termid` int(11) DEFAULT NULL,
  `course` int(11) DEFAULT NULL,
  `submitedby` int(11) DEFAULT NULL,
  `releasedby` int(11) DEFAULT NULL,
  `submiteddate` datetime DEFAULT NULL,
  `releaseddate` datetime DEFAULT NULL,
  `releasedstate` enum('Pending','Released') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `academic_years_terms_release`
--

INSERT INTO `academic_years_terms_release` (`_id`, `termid`, `course`, `submitedby`, `releasedby`, `submiteddate`, `releaseddate`, `releasedstate`) VALUES
(1, 1, 1, 1, 1, NULL, NULL, 'Pending'),
(2, 2, 1, 1, NULL, NULL, NULL, 'Pending'),
(3, 3, 1, 1, NULL, NULL, NULL, 'Pending'),
(4, 4, 1, 1, NULL, NULL, NULL, 'Pending'),
(5, 5, 1, 1, NULL, NULL, NULL, 'Pending'),
(6, 6, 1, 1, NULL, NULL, NULL, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `curriculums`
--

CREATE TABLE `curriculums` (
  `_id` int(11) NOT NULL,
  `systemid` int(11) NOT NULL,
  `name` varchar(90) DEFAULT NULL,
  `active` enum('T','F') DEFAULT 'T'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `curriculums`
--

INSERT INTO `curriculums` (`_id`, `systemid`, `name`, `active`) VALUES
(1, 1, 'Main Curriculum', 'T'),
(2, 1, 'yyt4', 'T'),
(3, 1, 'gggy', 'T');

-- --------------------------------------------------------

--
-- Table structure for table `curriculum_settings`
--

CREATE TABLE `curriculum_settings` (
  `_id` int(11) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `curriculum` int(11) DEFAULT NULL,
  `programid` int(11) DEFAULT NULL,
  `courseid` int(11) DEFAULT NULL,
  `cosunitid` int(11) DEFAULT NULL,
  `sectionid` int(11) DEFAULT NULL,
  `studyyear` int(11) DEFAULT NULL,
  `studyterm` int(11) DEFAULT NULL,
  `creditunits` float(2,1) DEFAULT NULL,
  `duration` float(3,2) DEFAULT NULL,
  `iscore` enum('T','F') DEFAULT 'T',
  `issupervised` enum('T','F') NOT NULL DEFAULT 'F',
  `payamount` int(11) DEFAULT NULL,
  `active` enum('T','F') DEFAULT 'T'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `curriculum_settings`
--

INSERT INTO `curriculum_settings` (`_id`, `code`, `curriculum`, `programid`, `courseid`, `cosunitid`, `sectionid`, `studyyear`, `studyterm`, `creditunits`, `duration`, `iscore`, `issupervised`, `payamount`, `active`) VALUES
(1, 'BIT 1103', 1, 1, 1, 1, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(2, 'BIT 1101', 1, 1, 1, 5, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(3, 'ICU 1101', 1, 1, 1, 9, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(4, 'ELS 1101', 1, 1, 1, 10, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(5, 'ICU 1101', 1, 1, 2, 9, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(6, 'ELS 1101', 1, 1, 2, 10, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(7, 'CSC 1101', 1, 1, 2, 5, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(8, 'BIT 2101', 1, 1, 1, 18, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(9, 'BIT 2102', 1, 1, 1, 19, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(10, 'BIT 2103', 1, 1, 1, 20, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(11, 'BIT 2104', 1, 1, 1, 21, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(12, 'CSC 2101', 1, 1, 2, 18, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(13, 'CSC 2102', 1, 1, 2, 19, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(14, 'CSC 2105', 1, 1, 2, 37, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(15, 'CSC 2106', 1, 1, 2, 38, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(16, 'CSC 3101', 1, 1, 2, 27, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(17, 'CSC 3102', 1, 1, 2, 32, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(18, 'CSC 3104', 1, 1, 2, 31, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(19, 'CSC 3105', 1, 1, 2, 30, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(20, 'CSC 3106', 1, 1, 2, 29, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(21, 'CSC 3108', 1, 1, 2, 41, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(22, 'BIT 3101', 1, 1, 1, 27, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(23, 'BIT 3106', 1, 1, 1, 29, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(24, 'BIT 3105', 1, 1, 1, 30, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(25, 'BIT 3104', 1, 1, 1, 31, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(26, 'BIT 3108', 1, 1, 1, 41, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(27, 'BIT 3102', 1, 1, 1, 32, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(28, 'DIT 1101', 1, 2, 1, 5, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(29, 'DIT 1103', 1, 2, 1, 44, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(30, 'DIT 1104', 1, 2, 1, 7, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(31, 'ELS 1101', 1, 2, 1, 10, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(32, 'ICU 1101', 1, 2, 1, 9, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(33, 'DIT 1201', 1, 2, 1, 14, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(34, 'DIT 1202', 1, 2, 1, 45, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(35, 'DIT 1203', 1, 2, 1, 46, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(36, 'DIT 1204', 1, 2, 1, 48, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(37, 'DIT 1205', 1, 2, 1, 47, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(38, 'FOS 1201', 1, 2, 1, 26, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(39, 'DIT 2101', 1, 2, 1, 49, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(40, 'DIT 2102', 1, 2, 1, 50, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(41, 'DIT 2103', 1, 2, 1, 22, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(42, 'DIT 2104', 1, 2, 1, 51, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(43, 'DIT 2105', 1, 2, 1, 52, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(44, 'DIT 2203', 1, 2, 1, 54, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(46, 'DIT 2201', 1, 2, 1, 57, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(47, 'DIT 2202', 1, 2, 1, 53, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(48, 'DCS 1101', 1, 2, 2, 5, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(49, 'DCS 1102', 1, 2, 2, 12, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(50, 'DCS 1104', 1, 2, 2, 7, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(51, 'ICU 1101', 1, 2, 2, 9, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(52, 'ELS 1101', 1, 2, 2, 10, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(53, 'DCS 1202', 1, 2, 2, 45, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(54, 'DCS 1203', 1, 2, 2, 46, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(55, 'DCS 1204', 1, 2, 2, 48, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(56, 'DCS 1205', 1, 2, 2, 39, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(57, 'DCS 1103', 1, 2, 2, 44, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(58, 'DCS 1201', 1, 2, 2, 14, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(59, 'FOS 1201', 1, 2, 2, 26, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(60, 'DCS 2101', 1, 2, 2, 49, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(61, 'DCS 2102', 1, 2, 2, 50, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(62, 'DCS 2103', 1, 2, 2, 22, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(63, 'DCS 2105', 1, 2, 2, 52, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(64, 'DCS 2106', 1, 2, 2, 55, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(65, 'DCS 2201', 1, 2, 2, 57, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(66, 'DCS 2203', 1, 2, 2, 54, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(67, 'FOS 2204', 1, 2, 2, 36, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'F'),
(68, 'DCS 2205', 1, 2, 2, 53, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(69, 'DCS 2104', 1, 2, 2, 51, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(70, 'FST 1101', 1, 2, 11, 58, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(71, 'DEC 1101', 1, 2, 11, 59, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(72, 'ICU 1101', 1, 2, 11, 9, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(73, 'DEC 1102', 1, 2, 11, 60, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(74, 'DEC 1103', 1, 2, 11, 61, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(75, 'DEC 1104', 1, 2, 11, 62, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(76, 'DEC 1105', 1, 2, 11, 63, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(77, 'DEC 1201', 1, 2, 11, 64, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(78, 'DEC 1203', 1, 2, 11, 66, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(79, 'DEC 1204', 1, 2, 11, 67, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(80, 'DEC 1205', 1, 2, 11, 73, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(81, 'DEC 1206', 1, 2, 11, 81, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(82, 'DEC 1207', 1, 2, 11, 69, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(83, 'DEC 1202', 1, 2, 11, 84, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(84, 'DEC 2101', 1, 2, 11, 82, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(85, 'DEC 2106', 1, 2, 11, 75, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(86, 'DEC 2105', 1, 2, 11, 85, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(87, 'DEC 2201', 1, 2, 11, 76, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(88, 'DEC 2203', 1, 2, 11, 83, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(89, 'DEC 2204', 1, 2, 11, 79, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(90, 'DEC 2206', 1, 2, 11, 80, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(91, 'DEC 2205', 1, 2, 11, 86, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(92, 'FST 1101', 1, 3, 11, 58, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(93, 'ICU 1101', 1, 3, 11, 9, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(94, 'CEC 1101', 1, 3, 11, 59, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(95, 'CEC 1102', 1, 3, 11, 60, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(96, 'CEC 1103', 1, 3, 11, 61, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(97, 'CEC 1104', 1, 3, 11, 62, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(98, 'CEC 1105', 1, 3, 11, 63, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(99, 'CEC 1201', 1, 3, 11, 64, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(100, 'CEC 1202', 1, 3, 11, 65, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(101, 'CEC 1204', 1, 3, 11, 67, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(102, 'CEC 1205', 1, 3, 11, 68, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(103, 'CEC 1206', 1, 3, 11, 69, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(104, 'CEC 1203', 1, 3, 11, 66, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(105, 'CEC 2101', 1, 3, 11, 70, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(106, 'CEC 2102', 1, 3, 11, 71, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(107, 'CEC 2103', 1, 3, 11, 72, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(108, 'CEC 2104', 1, 3, 11, 73, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(109, 'CEC 2105', 1, 3, 11, 74, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(110, 'CEC 2106', 1, 3, 11, 75, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(111, 'CEC 2201', 1, 3, 11, 76, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(112, 'CEC 2202', 1, 3, 11, 77, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(113, 'CEC 2203', 1, 3, 11, 78, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(114, 'CEC 2205', 1, 3, 11, 80, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(115, 'DEC 2102', 1, 2, 11, 88, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(116, 'DEC 2104', 1, 2, 11, 87, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(117, 'DEC 2103', 1, 2, 11, 89, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(118, 'DEC 2202', 1, 2, 11, 90, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(119, 'CEC 2204', 1, 3, 11, 91, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(120, 'BCR 1103', 1, 1, 12, 95, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(121, 'BCR 1104', 1, 1, 12, 94, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(122, 'BCR 1106', 1, 1, 12, 93, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(123, 'BCR 1107', 1, 1, 12, 92, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(124, 'BCR 1201', 1, 1, 12, 108, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(125, 'BCR 1202', 1, 1, 12, 109, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(126, 'BCR 1203', 1, 1, 12, 110, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(127, 'BCR 1204', 1, 1, 12, 111, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(128, 'BCR 1205', 1, 1, 12, 112, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(129, 'BCR 1206', 1, 1, 12, 113, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(130, 'BCR 1207', 1, 1, 12, 114, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(131, 'BCR 2101', 1, 1, 12, 96, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(132, 'BCR 2102', 1, 1, 12, 97, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(133, 'BCR 2103', 1, 1, 12, 98, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(134, 'BCR 2104', 1, 1, 12, 99, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(135, 'BCR 2105', 1, 1, 12, 100, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(136, 'BCR 2106', 1, 1, 12, 101, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(137, 'BCR 2201', 1, 1, 12, 115, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(138, 'BCR 2202', 1, 1, 12, 116, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(139, 'BCR 2203', 1, 1, 12, 117, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(140, 'BCR 2204', 1, 1, 12, 118, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(141, 'BCR 2205', 1, 1, 12, 119, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(142, 'BCR 2206', 1, 1, 12, 120, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(143, 'BCR 3101', 1, 1, 12, 102, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(144, 'BCR 3102', 1, 1, 12, 103, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(145, 'BCR3104', 1, 1, 12, 105, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'F'),
(146, 'BCR 3105', 1, 1, 12, 106, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(147, 'BCR 3106', 1, 1, 12, 107, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(148, 'BCR 3103', 1, 1, 12, 104, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(149, 'DIT 1102', 1, 2, 1, 43, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(150, 'CSC 3107', 1, 1, 2, 56, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(151, 'CSC 3202', 1, 1, 2, 34, 1, 3, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(152, 'CSC 3204', 1, 1, 2, 42, 1, 3, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(153, 'CSC 3203', 1, 1, 2, 35, 1, 3, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(154, 'FST 3201', 1, 1, 2, 36, 1, 3, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(155, 'CSC 2203', 1, 1, 2, 2, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(156, 'CSC 2207', 1, 1, 2, 40, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(157, 'CSC 2201', 1, 1, 2, 23, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(158, 'CSC 2204', 1, 1, 2, 22, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(159, 'CSC 2202', 1, 1, 2, 24, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(160, 'CSC 2206', 1, 1, 2, 25, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(161, 'CSC 1203', 1, 1, 2, 14, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(162, 'BIT 1203', 1, 1, 1, 14, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(163, 'BIT 2201', 1, 1, 1, 23, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(164, 'BIT 2202', 1, 1, 1, 24, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(165, 'BIT 2203', 1, 1, 1, 2, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(166, 'BIT 2204', 1, 1, 1, 22, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(167, 'BIT 3202', 1, 1, 1, 34, 1, 3, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(168, 'BIT 3203', 1, 1, 1, 35, 1, 3, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(169, 'FST 3201', 1, 1, 1, 36, 1, 3, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(170, 'BBA 1104', 1, 1, 4, 241, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(171, 'BBA 1105', 1, 1, 4, 242, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(172, 'BBA 1101', 1, 1, 4, 238, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(173, 'BBA 1102', 1, 1, 4, 239, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(174, 'BBA 1103', 1, 1, 4, 240, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(175, 'ICU 1101', 1, 1, 4, 9, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(176, 'FST 1101', 1, 1, 4, 58, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(177, 'BPA 1101', 1, 1, 3, 243, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(178, 'BBA 1101', 1, 1, 3, 238, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(179, 'BBA 1104', 1, 1, 3, 241, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(180, 'BBA 1105', 1, 1, 3, 242, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(181, 'FST 1101', 1, 1, 3, 58, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(182, 'ICU 1101', 1, 1, 3, 9, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(183, 'FST 1101', 1, 1, 5, 58, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(184, 'ICU 1101', 1, 1, 5, 9, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(185, 'BBA 1104', 1, 1, 5, 241, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(186, 'BBA 1105', 1, 1, 5, 242, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(187, 'BBA 1101', 1, 1, 5, 238, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(188, 'BBA 1102', 1, 1, 5, 239, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(189, 'BBA 1103', 1, 1, 5, 240, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(190, 'ICU 1101', 1, 1, 6, 9, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(191, 'FST 1101', 1, 1, 6, 58, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(192, 'BBA 1103', 1, 1, 6, 240, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(193, 'BBA 1102', 1, 1, 6, 239, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(194, 'BBA 1101', 1, 1, 6, 238, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(195, 'BBA 1105', 1, 1, 6, 242, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(196, 'BBA 1104', 1, 1, 6, 241, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(197, 'ICU 1101', 1, 1, 7, 9, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(198, 'FST 1101', 1, 1, 7, 58, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(199, 'BBA 1104', 1, 1, 7, 241, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(200, 'BBA 1105', 1, 1, 7, 242, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(201, 'BBA 1101', 1, 1, 7, 238, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(202, 'BBA 1102', 1, 1, 7, 239, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(203, 'BPA 1201', 1, 1, 3, 252, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(204, 'BBA 1204', 1, 1, 3, 247, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(205, 'BPA 1203', 1, 1, 3, 253, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(206, 'BBA 1201', 1, 1, 4, 244, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(207, 'BBA 1205', 1, 1, 4, 248, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(208, 'BBA 1201', 1, 1, 5, 244, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(209, 'BBA 1204', 1, 1, 5, 247, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(210, 'BBA 1205', 1, 1, 5, 248, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(211, 'BBA 1201', 1, 1, 7, 244, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(212, 'BBA 1205', 1, 1, 7, 248, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(213, 'BBA 1201', 1, 1, 6, 244, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(214, 'BBA 1204', 1, 1, 6, 247, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(215, 'BBA 1205', 1, 1, 6, 248, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(216, 'BHR 1201', 1, 1, 6, 249, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(217, 'BIT 3103', 1, 1, 1, 51, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'F'),
(218, 'CSC 3103', 1, 1, 2, 28, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(219, 'FST 1101', 1, 1, 12, 58, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(220, 'ICU 1101', 1, 1, 12, 9, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(221, 'ELS 1101', 1, 1, 12, 10, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(222, 'BBA 1206', 1, 1, 1, 255, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(223, 'BBA 1206', 1, 1, 2, 255, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(224, 'DBA 1206', 1, 2, 2, 255, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(226, 'DBA 1206', 1, 2, 1, 255, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(227, 'BIT 1105', 1, 1, 1, 46, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'F'),
(228, 'BIT 1106', 1, 1, 1, 44, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'F'),
(229, 'BIT 1107', 1, 1, 1, 257, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'F'),
(230, 'BIT 1206', 1, 1, 1, 259, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'F'),
(231, 'BIT 1208', 1, 1, 1, 260, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'F'),
(232, 'BIT 1205', 1, 1, 1, 258, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'F'),
(233, 'BIT 1207', 1, 1, 1, 47, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'F'),
(234, 'BIT 2107', 1, 1, 1, 4, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'F'),
(235, 'BIT 2207', 1, 1, 1, 28, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'F'),
(236, 'CSC 1102', 1, 1, 2, 262, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'F'),
(237, 'CSC 1105', 1, 1, 2, 56, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'F'),
(238, 'CSC 1106', 1, 1, 2, 44, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'F'),
(239, 'CSC 1101', 1, 1, 2, 48, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'F'),
(240, 'CSC 1202', 1, 1, 2, 21, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'F'),
(241, 'CSC 1205', 1, 1, 2, 39, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'F'),
(242, 'CSC 1201', 1, 1, 2, 12, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'F'),
(243, 'CSC 1204', 1, 1, 2, 257, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'F'),
(244, 'CSC 2108', 1, 1, 2, 263, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'F'),
(245, 'CSC 2107', 1, 1, 2, 4, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'F'),
(246, 'BIT 1105', 1, 1, 1, 8, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(247, 'BIT 1104', 1, 1, 1, 7, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(248, 'BIT 1102', 1, 1, 1, 6, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(249, 'BIT 1202', 1, 1, 1, 3, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(250, 'BIT 1204', 1, 1, 1, 15, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(251, 'ACC 1201', 1, 1, 1, 256, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(252, 'BIT 1201', 1, 1, 1, 13, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(253, 'CSC 1106', 1, 1, 2, 11, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(254, 'CSC 1104', 1, 1, 2, 7, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(255, 'CSC 1103', 1, 1, 2, 1, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(256, 'CSC 1105', 1, 1, 2, 12, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(257, 'CSC 1102', 1, 1, 2, 6, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(258, 'ACC 1201', 1, 1, 2, 256, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(259, 'CSC 1202', 1, 1, 2, 3, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(260, 'CSC 1204', 1, 1, 2, 15, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(261, 'CSC 1201', 1, 1, 2, 13, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(262, 'BBA 1206', 1, 1, 4, 264, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(263, 'BHR 1203', 1, 1, 4, 265, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(264, 'BHR 1202', 1, 1, 4, 266, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(265, 'BBA 1204', 1, 1, 4, 247, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(266, 'BPA 1103', 1, 1, 3, 267, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(267, 'BBA 1203', 1, 1, 3, 240, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(268, 'BBA 1206', 1, 1, 3, 264, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(269, 'BHR 1202', 1, 1, 3, 266, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(270, 'BPA 1204', 1, 1, 3, 268, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(271, 'BHR 1204', 1, 1, 5, 269, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(272, 'BHR 1203', 1, 1, 5, 265, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(273, 'BHR 1202', 1, 1, 5, 266, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(274, 'BBA 1206', 1, 1, 6, 264, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(275, 'IBF 1201', 1, 1, 6, 270, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(276, 'BBA 1207', 1, 1, 6, 271, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(277, 'BHR 1203', 1, 1, 7, 265, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(278, 'BHR 1202', 1, 1, 7, 266, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(279, 'BPL 1202', 1, 1, 7, 272, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(280, 'BBA 2107', 1, 1, 4, 278, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(281, 'BBA 2108', 1, 1, 4, 284, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(282, 'BBA 2109', 1, 1, 4, 245, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(283, 'BBA 2106', 1, 1, 4, 277, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(284, 'BBA 2104', 1, 1, 4, 275, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(285, 'BBA 2101', 1, 1, 4, 273, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(286, 'BBA 2106', 1, 1, 5, 277, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(287, 'BBA 2109', 1, 1, 5, 245, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(288, 'BHR 2103', 1, 1, 5, 287, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(289, 'BHR 2105', 1, 1, 5, 289, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(290, 'BHR 2101', 1, 1, 5, 291, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(291, 'BBA 2109', 1, 1, 6, 245, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(292, 'BBA 2104', 1, 1, 6, 275, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(293, 'BBA 2106', 1, 1, 6, 277, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(294, 'BBA 2108', 1, 1, 6, 284, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(295, 'IBF 2102', 1, 1, 6, 292, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(296, 'IBF 2104', 1, 1, 6, 251, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(297, 'IBF 2101', 1, 1, 6, 293, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(298, 'BBA 2109', 1, 1, 7, 245, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(299, 'BBA 2108', 1, 1, 7, 284, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(300, 'BBA 2106', 1, 1, 7, 277, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(301, 'BBA 2104', 1, 1, 7, 275, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(302, 'BBA 2112', 1, 1, 7, 294, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(303, 'BBA 2111', 1, 1, 7, 295, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(304, 'BPL 2104', 1, 1, 7, 250, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(305, 'BPA 2101', 1, 1, 3, 297, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(306, 'BPL 2104', 1, 1, 3, 250, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(307, 'BBA 2110', 1, 1, 3, 239, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(308, 'BBA 2106', 1, 1, 3, 277, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(309, 'BBA 2102', 1, 1, 3, 273, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(310, 'BPA 2101', 1, 1, 3, 296, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(311, 'ISL 1101', 1, 1, 8, 127, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(312, 'ISL 1102', 1, 1, 8, 128, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(313, 'ISL 1103', 1, 1, 8, 129, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(314, 'ISL 1104', 1, 1, 8, 130, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(315, 'ISL 1105', 1, 1, 8, 131, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(316, 'ISL 1106', 1, 1, 8, 132, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(317, 'ARB 1101', 1, 1, 8, 121, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(318, 'ARB 1102', 1, 1, 8, 122, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(319, 'ARB 1103', 1, 1, 8, 123, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(320, 'ARB 1104', 1, 1, 8, 124, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(321, 'SHA 1101', 1, 1, 8, 133, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(322, 'FOS 1101', 1, 1, 8, 5, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(323, 'ARB 1106', 1, 1, 8, 125, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(324, 'ELS 1101', 1, 1, 8, 298, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(325, 'ISL 1201', 1, 1, 8, 187, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(326, 'ISL 1202', 1, 1, 8, 188, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(327, 'ISL 1203', 1, 1, 8, 189, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(328, 'ISL 1204', 1, 1, 8, 190, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(329, 'ISL 1205', 1, 1, 8, 191, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(330, 'ISL 1206', 1, 1, 8, 192, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(331, 'ARB 1201', 1, 1, 8, 181, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(332, 'ARB 1202', 1, 1, 8, 182, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(333, 'ARB 1203', 1, 1, 8, 183, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(334, 'SHA 1201', 1, 1, 8, 142, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(335, 'ELS 1201', 1, 1, 8, 299, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(336, 'FST 1202', 1, 1, 8, 300, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(337, 'ISL 1208', 1, 1, 8, 194, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(338, 'ISL 1101', 1, 1, 9, 127, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(339, 'ISL 1102', 1, 1, 9, 128, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(340, 'ISL 1103', 1, 1, 9, 129, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(341, 'ISL 1106', 1, 1, 9, 132, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(342, 'ARB 1101', 1, 1, 9, 121, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(343, 'ARB 1104', 1, 1, 9, 124, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(344, 'SHA 1101', 1, 1, 9, 133, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(345, 'SHA 1102', 1, 1, 9, 135, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(346, 'FST 1101', 1, 1, 9, 5, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(347, 'ELS 1101', 1, 1, 9, 298, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(348, 'ARB 1106', 1, 1, 9, 125, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(349, 'SHA 1107', 1, 1, 9, 140, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(350, 'LAW 1101', 1, 1, 9, 134, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(351, 'ISL 1201', 1, 1, 9, 187, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(352, 'ISL 1202', 1, 1, 9, 188, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(353, 'ISL 1206', 1, 1, 9, 192, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(354, 'ARB 1201', 1, 1, 9, 181, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(355, 'ELS 1201', 1, 1, 9, 299, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(356, 'FST 1202', 1, 1, 9, 300, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(357, 'LAW 1201', 1, 1, 9, 141, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(358, 'SHA 1201', 1, 1, 9, 142, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(359, 'SHA 1202', 1, 1, 9, 143, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(360, 'ISL 1208', 1, 1, 9, 194, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(361, 'SHA 1206', 1, 1, 9, 136, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(362, 'ISL 1203', 1, 1, 9, 189, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(363, 'ISL 2101', 1, 1, 8, 199, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(364, 'ISL 2102', 1, 1, 8, 200, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(365, 'ISL 2104', 1, 1, 8, 202, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(366, 'ARB 2101', 1, 1, 8, 195, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(367, 'ARB 2102', 1, 1, 8, 196, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(368, 'ARB 2103', 1, 1, 8, 197, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(369, 'DAW 2101', 1, 1, 8, 198, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(370, 'RSH 2101', 1, 1, 8, 204, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(371, 'SHA 2101', 1, 1, 8, 149, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(372, 'ELS 2101', 1, 1, 8, 301, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(373, 'ISL 2105', 1, 1, 8, 215, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(374, 'ISL 2201', 1, 1, 8, 209, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(375, 'ISL 2202', 1, 1, 8, 210, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(376, 'ISL 2204', 1, 1, 8, 212, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(377, 'ISL 2205', 1, 1, 8, 193, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(378, 'ARB 2201', 1, 1, 8, 205, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(379, 'ARB 2202', 1, 1, 8, 206, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(380, 'ARB 2203', 1, 1, 8, 207, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(381, 'ELS 2201', 1, 1, 8, 302, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(382, 'SHA 2207', 1, 1, 8, 160, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(383, 'HRM 2201', 1, 1, 8, 153, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(384, 'ISL 2206', 1, 1, 8, 234, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(385, 'ARB 1204', 1, 1, 8, 184, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(386, 'EFD 1101', 1, 1, 10, 303, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(387, 'EPY 1101', 1, 1, 10, 304, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(388, 'EFD 1102', 1, 1, 10, 305, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(389, 'FST 1101', 1, 1, 10, 58, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(390, 'ICU 1101', 1, 1, 10, 9, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(391, 'ELS 1102', 1, 1, 10, 10, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(392, 'EPY 1201', 1, 1, 10, 306, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(393, 'ECI 1201', 1, 1, 10, 307, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(394, 'EMA 1201', 1, 1, 10, 308, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(395, 'EPY 2101', 1, 1, 10, 309, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(396, 'EMA 2101', 1, 1, 10, 310, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(397, 'ECI 2101', 1, 1, 10, 311, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(398, NULL, 1, 1, 10, 312, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(399, NULL, 1, 1, 10, 313, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(400, NULL, 1, 1, 10, 314, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(401, NULL, 1, 1, 10, 315, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(402, NULL, 1, 1, 10, 316, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(403, NULL, 1, 1, 10, 317, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(404, NULL, 1, 1, 10, 318, 1, 3, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(405, NULL, 1, 1, 10, 319, 1, 3, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(406, NULL, 1, 1, 10, 320, 1, 3, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(407, NULL, 1, 1, 10, 321, 1, 3, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(408, 'GEO 1101', 1, 1, 10, 322, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(409, 'GEO 1102', 1, 1, 10, 323, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(410, 'GEO 1103', 1, 1, 10, 324, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(411, 'GEO 1201', 1, 1, 10, 325, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(412, 'GEO 1202', 1, 1, 10, 326, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(413, 'GEO 1203', 1, 1, 10, 327, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(414, 'IED 1101', 1, 1, 10, 348, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(415, 'IED 1102', 1, 1, 10, 349, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(416, 'IED 1103', 1, 1, 10, 350, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(417, 'IED 1201', 1, 1, 10, 351, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(418, 'IED 1202', 1, 1, 10, 352, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(419, 'IED 1203', 1, 1, 10, 353, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(420, 'HIS 1101', 1, 1, 10, 366, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(421, 'HIS 1102', 1, 1, 10, 367, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(422, 'HIS 1103', 1, 1, 10, 368, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(423, 'HIS 1201', 1, 1, 10, 369, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(424, 'HIS 1202', 1, 1, 10, 370, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(425, 'HIS 1203', 1, 1, 10, 371, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(426, 'ECO 1101', 1, 1, 10, 384, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(427, 'ECO 1102', 1, 1, 10, 385, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(428, 'ECO 1103', 1, 1, 10, 386, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(429, 'ECO 1201', 1, 1, 10, 387, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(430, 'ECO 1202', 1, 1, 10, 388, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(431, 'ECO 1203', 1, 1, 10, 389, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(432, 'LUG 1101', 1, 1, 10, 402, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(433, 'LUG 1102', 1, 1, 10, 403, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(434, 'LUG 1201', 1, 1, 10, 404, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(435, 'LUG 1202', 1, 1, 10, 405, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(436, 'AED 1101', 1, 1, 10, 418, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(437, 'AED 1102', 1, 1, 10, 419, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(438, 'AED 1103', 1, 1, 10, 420, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(439, 'AED 1104', 1, 1, 10, 421, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(440, 'AED 1201', 1, 1, 10, 422, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(441, 'AED 1202', 1, 1, 10, 423, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(442, 'AED 1203', 1, 1, 10, 426, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(443, 'LIT 1101', 1, 1, 10, 435, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(444, 'LIT 1102', 1, 1, 10, 436, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(445, 'LIT 1201', 1, 1, 10, 437, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(446, 'LIT 1202', 1, 1, 10, 438, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(447, 'LIT 1203', 1, 1, 10, 439, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(448, 'ENG 1101', 1, 1, 10, 450, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(449, 'ENL 1102', 1, 1, 10, 451, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(450, 'ENL 1201', 1, 1, 10, 452, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(451, 'ENL 1202', 1, 1, 10, 453, 1, 1, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(452, 'ISL 2101', 1, 1, 9, 199, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(453, 'ISL 2102', 1, 1, 9, 200, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(454, 'ISL 2105', 1, 1, 9, 215, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(455, 'ARB 2101', 1, 1, 9, 195, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(456, 'SHA 2102', 1, 1, 9, 150, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(457, 'SHA 2101', 1, 1, 9, 149, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(458, 'SHA 2103', 1, 1, 9, 140, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(459, 'RSH 2101', 1, 1, 9, 164, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(460, 'ELS 2101', 1, 1, 9, 301, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(461, 'SHA 2104', 1, 1, 9, 151, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(462, 'SHA 2105', 1, 1, 9, 152, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(463, 'DAW 2101', 1, 1, 9, 198, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(464, 'CSC 2103', 1, 1, 2, 20, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(465, 'CSC 2104', 1, 1, 2, 21, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(466, 'BIT 2106', 1, 1, 1, 16, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(467, 'BIT 2105', 1, 1, 1, 17, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(468, 'IBF 3104', 1, 1, 6, 477, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(469, 'IBF 3103', 1, 1, 6, 488, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(470, 'IBF 3102', 1, 1, 6, 270, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(471, 'IBF 3101', 1, 1, 6, 471, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(472, 'BBA 3105', 1, 1, 6, 480, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(473, 'BBA 3104', 1, 1, 6, 472, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(474, 'BBA 3104', 1, 1, 5, 472, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(475, 'BBA 3105', 1, 1, 5, 480, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(476, 'BHR 3104', 1, 1, 5, 483, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(477, 'BHR 3106', 1, 1, 5, 469, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(478, 'BHR 3101', 1, 1, 5, 481, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(479, 'BHR 3102', 1, 1, 5, 492, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(480, 'BHR 3105', 1, 1, 5, 491, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(481, 'BHR 3106', 1, 1, 5, 475, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(482, 'BBA 3104', 1, 1, 7, 472, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(483, 'BPL 3102', 1, 1, 7, 476, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(484, 'BBA 3105', 1, 1, 7, 480, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(485, 'BBA 3103', 1, 1, 7, 482, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(486, 'BPL 3106', 1, 1, 7, 485, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(487, 'BPL 3104', 1, 1, 7, 487, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(488, 'BPL 3101', 1, 1, 7, 470, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(489, 'DPA 2101', 1, 2, 3, 297, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(490, 'DPA 2103', 1, 2, 3, 277, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(491, 'DPA 2104', 1, 2, 3, 493, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(492, 'DPA 2102', 1, 2, 3, 296, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(493, 'DHR 2101', 1, 2, 3, 266, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(494, 'DBA 2105', 1, 2, 3, 265, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(495, 'BBA 3101', 1, 1, 4, 467, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(496, 'MKT 3101', 1, 1, 4, 468, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(497, 'BBA 3104', 1, 1, 4, 472, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(498, 'MKT 3106', 1, 1, 4, 473, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(499, 'BBA 3102', 1, 1, 4, 474, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(500, 'BBA 3105', 1, 1, 4, 480, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(501, 'BBA 3103', 1, 1, 4, 482, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(502, 'BBA 3102', 1, 1, 4, 486, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(503, 'GEO 2101', 1, 1, 10, 328, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(504, 'GEO 2102', 1, 1, 10, 329, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(505, 'GEO 2103', 1, 1, 10, 330, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(506, 'GEO 2104', 1, 1, 10, 331, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(507, 'GEO 2105', 1, 1, 10, 332, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(508, 'IED 2101', 1, 1, 10, 354, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(509, 'IED 2102', 1, 1, 10, 355, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(510, 'IED 2103', 1, 1, 10, 356, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(511, 'HIS 2101', 1, 1, 10, 372, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(512, 'HIS 2102', 1, 1, 10, 373, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(513, 'HIS 2103', 1, 1, 10, 374, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(514, 'ECO 2101', 1, 1, 10, 390, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(515, 'ECO 2103', 1, 1, 10, 391, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(516, 'ECO 2104', 1, 1, 10, 392, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(517, 'LUG 2101', 1, 1, 10, 406, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(518, 'LUG 2102', 1, 1, 10, 407, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(519, 'LUG 2103', 1, 1, 10, 408, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(520, 'AED 2101', 1, 1, 10, 425, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(521, 'AED 2102', 1, 1, 10, 424, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(522, 'AED 2103', 1, 1, 10, 427, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(523, 'East Africa Poetry and Drama', 1, 1, 10, 440, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(524, 'LIT 2102', 1, 1, 10, 441, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(525, 'LIT 2103', 1, 1, 10, 442, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(526, 'ENL 2101', 1, 1, 10, 454, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(527, 'ENL 2102', 1, 1, 10, 455, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(528, 'ENL 2103', 1, 1, 10, 456, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(529, NULL, 1, 1, 10, 333, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(530, NULL, 1, 1, 10, 334, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(531, NULL, 1, 1, 10, 335, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(532, NULL, 1, 1, 10, 336, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(533, NULL, 1, 1, 10, 337, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(534, NULL, 1, 1, 10, 357, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(535, NULL, 1, 1, 10, 358, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(536, NULL, 1, 1, 10, 359, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(537, NULL, 1, 1, 10, 375, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(538, NULL, 1, 1, 10, 376, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(539, NULL, 1, 1, 10, 377, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(540, NULL, 1, 1, 10, 393, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(541, NULL, 1, 1, 10, 394, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(542, NULL, 1, 1, 10, 395, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(543, NULL, 1, 1, 10, 409, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(544, NULL, 1, 1, 10, 410, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(545, NULL, 1, 1, 10, 411, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(546, NULL, 1, 1, 10, 428, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(547, NULL, 1, 1, 10, 429, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(548, NULL, 1, 1, 10, 432, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(549, NULL, 1, 1, 10, 443, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(550, NULL, 1, 1, 10, 444, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(551, NULL, 1, 1, 10, 457, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(552, NULL, 1, 1, 10, 458, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(553, 'BBA 2105', 1, 1, 3, 265, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(554, 'BHR 2101', 1, 1, 3, 266, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(555, 'BBA 2102', 1, 1, 4, 276, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(556, 'BBA 2102', 1, 1, 6, 276, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(557, 'BBA 2101', 1, 1, 6, 273, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(558, 'BPL 2102', 1, 1, 7, 272, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(559, 'BPA 2102', 1, 1, 3, 493, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(560, 'BPA 2104', 1, 1, 5, 286, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(561, 'BPL 2103', 1, 1, 7, 495, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(562, 'BPL 2106', 1, 1, 7, 496, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(563, 'BHR 2101', 1, 1, 4, 266, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(564, 'BHR 2101', 1, 1, 6, 266, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(565, 'BHR 2101', 1, 1, 7, 266, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(566, 'BPL 2107', 1, 1, 7, 485, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(567, 'BBA 2105', 1, 1, 4, 265, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(568, 'BBA 2105', 1, 1, 7, 265, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(569, 'BBA 2105', 1, 1, 6, 265, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(570, 'BBA 2105', 1, 1, 5, 265, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(571, 'BPA 3101', 1, 1, 3, 478, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(572, 'BPA 3102', 1, 1, 3, 466, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(573, 'BPA 3103', 1, 1, 3, 489, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(574, 'BPA 3104', 1, 1, 3, 465, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(575, 'BBA 3105', 1, 1, 3, 480, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(576, 'BPA 3101', 1, 1, 3, 479, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(577, 'BPA 3103', 1, 1, 3, 490, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(578, 'BPA 3106', 1, 1, 3, 484, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(579, 'BHR 3101', 1, 1, 3, 481, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(580, 'BHR 3102', 1, 1, 3, 475, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(581, 'BHR 3103', 1, 1, 3, 491, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(582, 'BHR 3104', 1, 1, 3, 483, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(583, 'ECO 2102', 1, 1, 10, 389, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(584, 'ECO 2103', 1, 1, 10, 386, 1, 2, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(585, NULL, 1, 1, 8, 220, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(586, NULL, 1, 1, 8, 456, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(587, NULL, 1, 1, 8, 217, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(588, NULL, 1, 1, 8, 216, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(589, NULL, 1, 1, 8, 218, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(590, NULL, 1, 1, 8, 211, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(591, NULL, 1, 1, 8, 219, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(592, NULL, 1, 1, 8, 223, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(593, NULL, 1, 1, 8, 222, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(594, 'BCR 3107', 1, 1, 12, 497, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(595, 'BBA 1102', 1, 1, 3, 239, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(596, 'BBA 1101', 1, 1, 3, 240, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(597, 'SHA 1104', 1, 1, 9, 137, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(598, 'SHA 1103', 1, 1, 9, 136, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(599, 'SHA 1101', 1, 1, 9, 155, 1, 1, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(600, 'BIT 2206', 1, 1, 1, 25, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(601, 'BIT 2205', 1, 1, 1, 4, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(602, 'FST 2201', 1, 1, 1, 26, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(603, 'BIT 3103', 1, 1, 1, 28, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(604, 'CSC 2205', 1, 1, 2, 4, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(605, 'CSC 2208', 1, 1, 2, 39, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(606, 'FST 2205', 1, 1, 2, 26, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(607, 'DCS 2202', 1, 2, 2, 56, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(608, 'FST 2201', 1, 2, 1, 499, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(609, 'FST 2201', 1, 2, 2, 499, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(617, 'BCR 3201', 1, 1, 12, 500, 1, 3, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(618, 'BCR 3202', 1, 1, 12, 501, 1, 3, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(619, 'BCR 3203', 1, 1, 12, 502, 1, 3, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(620, 'BCR 3204', 1, 1, 12, 503, 1, 3, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(621, 'BCR 3205', 1, 1, 12, 504, 1, 3, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(622, 'BCR 3206', 1, 1, 12, 505, 1, 3, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(623, 'BCR 3207', 1, 1, 12, 506, 1, 3, 2, NULL, NULL, 'T', 'F', NULL, 'T'),
(624, 'BCR 3104', 1, 1, 12, 510, 1, 3, 1, NULL, NULL, 'T', 'F', NULL, 'T'),
(625, 'BCR 2207', 1, 1, 12, 505, 1, 2, 2, NULL, NULL, 'T', 'F', NULL, 'T');

-- --------------------------------------------------------

--
-- Table structure for table `curriculum_settings_lecturer`
--

CREATE TABLE `curriculum_settings_lecturer` (
  `_id` int(11) NOT NULL,
  `currsetting` int(11) DEFAULT NULL,
  `academterm` int(11) DEFAULT NULL,
  `lecturerid` int(11) DEFAULT NULL,
  `testsubmited` enum('T','F') NOT NULL DEFAULT 'F',
  `coursesubmited` enum('T','F') NOT NULL DEFAULT 'F',
  `examsubmited` enum('T','F') NOT NULL DEFAULT 'F',
  `active` enum('T','F') DEFAULT 'T',
  `qn_1` int(11) DEFAULT NULL,
  `qn_2` int(11) DEFAULT NULL,
  `qn_3` int(11) DEFAULT NULL,
  `qn_4` int(11) DEFAULT NULL,
  `qn_5` int(11) DEFAULT NULL,
  `qn_6` int(11) DEFAULT NULL,
  `qn_7` int(11) DEFAULT NULL,
  `qn_8` int(11) DEFAULT NULL,
  `qn_9` int(11) DEFAULT NULL,
  `qn_10` int(11) DEFAULT NULL,
  `qn_11` int(11) DEFAULT NULL,
  `qn_12` int(11) DEFAULT NULL,
  `qn_13` int(11) DEFAULT NULL,
  `qn_14` int(11) DEFAULT NULL,
  `qn_15` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `curriculum_settings_lecturer`
--

INSERT INTO `curriculum_settings_lecturer` (`_id`, `currsetting`, `academterm`, `lecturerid`, `testsubmited`, `coursesubmited`, `examsubmited`, `active`, `qn_1`, `qn_2`, `qn_3`, `qn_4`, `qn_5`, `qn_6`, `qn_7`, `qn_8`, `qn_9`, `qn_10`, `qn_11`, `qn_12`, `qn_13`, `qn_14`, `qn_15`) VALUES
(1, 2, 4, 2, 'F', 'F', 'F', 'T', 20, 33, 40, 21, NULL, 52, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 1, 4, 2, 'F', 'F', 'F', 'T', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 248, 4, 2, 'F', 'F', 'F', 'T', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 247, 4, 2, 'F', 'F', 'F', 'T', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 227, 4, 2, 'F', 'F', 'F', 'T', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 246, 4, 2, 'F', 'F', 'F', 'T', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 228, 4, 2, 'F', 'F', 'F', 'T', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 229, 4, 2, 'F', 'F', 'F', 'T', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `curriculum_settings_lecturer_assigned_supervision`
--

CREATE TABLE `curriculum_settings_lecturer_assigned_supervision` (
  `_id` int(11) NOT NULL,
  `lecturer_assid` int(11) DEFAULT NULL,
  `student_markid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `curriculum_settings_lecturer_content`
--

CREATE TABLE `curriculum_settings_lecturer_content` (
  `_id` int(11) NOT NULL,
  `lecturer_assid` int(11) DEFAULT NULL,
  `content_title` varchar(25) DEFAULT NULL,
  `content_filename` varchar(100) DEFAULT NULL,
  `content_details` varchar(100) DEFAULT NULL,
  `uploaded_date` datetime NOT NULL DEFAULT current_timestamp(),
  `active` enum('T','F') DEFAULT 'T'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `curriculum_settings_lecturer_content`
--

INSERT INTO `curriculum_settings_lecturer_content` (`_id`, `lecturer_assid`, `content_title`, `content_filename`, `content_details`, `uploaded_date`, `active`) VALUES
(1, 1, 'hfkdhfakjdfa', 'fasdhgfkajsdhgkjsd', 'fadgfkasdhgfkajsdh', '2025-12-29 06:44:44', 'T');

-- --------------------------------------------------------

--
-- Table structure for table `curriculum_settings_lecturer_session`
--

CREATE TABLE `curriculum_settings_lecturer_session` (
  `_id` int(11) NOT NULL,
  `lecturer_assid` int(11) DEFAULT NULL,
  `sessionidn` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `curriculum_settings_lecturer_session`
--

INSERT INTO `curriculum_settings_lecturer_session` (`_id`, `lecturer_assid`, `sessionidn`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 2, 3);

-- --------------------------------------------------------

--
-- Table structure for table `curriculum_settings_lecturer_session_attendance`
--

CREATE TABLE `curriculum_settings_lecturer_session_attendance` (
  `_id` int(11) NOT NULL,
  `lect_session` int(11) DEFAULT NULL,
  `attendcode` varchar(20) NOT NULL,
  `attenddate` datetime NOT NULL DEFAULT current_timestamp(),
  `createddate` datetime NOT NULL DEFAULT current_timestamp(),
  `active` enum('T','F') DEFAULT 'T'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `curriculum_settings_lecturer_session_attendance`
--

INSERT INTO `curriculum_settings_lecturer_session_attendance` (`_id`, `lect_session`, `attendcode`, `attenddate`, `createddate`, `active`) VALUES
(1, 1, '666frt', '2025-12-29 06:54:46', '2025-12-29 06:54:46', 'T'),
(2, 1, '666f322', '2025-12-30 07:41:58', '2025-12-29 07:42:29', 'T'),
(3, 1, 'hfdgjasd7', '2025-12-31 07:41:58', '2025-12-29 07:42:29', 'T');

-- --------------------------------------------------------

--
-- Table structure for table `curriculum_settings_lecturer_session_attendance_list`
--

CREATE TABLE `curriculum_settings_lecturer_session_attendance_list` (
  `_id` int(11) NOT NULL,
  `attendidn` int(11) DEFAULT NULL,
  `markidnum` int(11) DEFAULT NULL,
  `attendstate` enum('Present','Absent','Reason') DEFAULT 'Absent',
  `attendreason` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `curriculum_settings_lecturer_session_attendance_list`
--

INSERT INTO `curriculum_settings_lecturer_session_attendance_list` (`_id`, `attendidn`, `markidnum`, `attendstate`, `attendreason`) VALUES
(1, 1, 1, 'Present', ''),
(2, 2, 1, 'Reason', 'Was sick');

-- --------------------------------------------------------

--
-- Table structure for table `curriculum_settings_lecturer_session_coursework`
--

CREATE TABLE `curriculum_settings_lecturer_session_coursework` (
  `_id` int(11) NOT NULL,
  `lect_session` int(11) DEFAULT NULL,
  `markedoutof` int(11) DEFAULT NULL,
  `contributes` int(11) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `instructions` varchar(90) DEFAULT NULL,
  `foruploading` enum('T','F') DEFAULT 'F',
  `submitdeadline` datetime NOT NULL DEFAULT current_timestamp(),
  `createddate` datetime NOT NULL DEFAULT current_timestamp(),
  `active` enum('T','F') DEFAULT 'T'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `curriculum_settings_lecturer_session_coursework`
--

INSERT INTO `curriculum_settings_lecturer_session_coursework` (`_id`, `lect_session`, `markedoutof`, `contributes`, `title`, `instructions`, `foruploading`, `submitdeadline`, `createddate`, `active`) VALUES
(1, 1, 20, 20, 'hsdgfu', 'akjdsgfkadhgfkasdjhgfkasjdhfgkjsadfasdfkasdhgfajksdhgfakjdsgfakjsdfhkasd', 'T', '2025-12-29 06:45:18', '2025-12-29 06:45:18', 'T');

-- --------------------------------------------------------

--
-- Table structure for table `curriculum_settings_lecturer_session_coursework_submit`
--

CREATE TABLE `curriculum_settings_lecturer_session_coursework_submit` (
  `_id` int(11) NOT NULL,
  `courseworkid` int(11) DEFAULT NULL,
  `markidnum` int(11) DEFAULT NULL,
  `marksgot` int(10) DEFAULT NULL,
  `submitfile` varchar(100) DEFAULT NULL,
  `submitedon` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `curriculum_settings_lecturer_session_coursework_submit`
--

INSERT INTO `curriculum_settings_lecturer_session_coursework_submit` (`_id`, `courseworkid`, `markidnum`, `marksgot`, `submitfile`, `submitedon`) VALUES
(2, 1, 1, NULL, 'dae55a079ca7456e528b0c9926383c713fd7493b.pdf', '2026-01-05 15:29:10');

-- --------------------------------------------------------

--
-- Table structure for table `curriculum_settings_student`
--

CREATE TABLE `curriculum_settings_student` (
  `_id` int(11) NOT NULL,
  `studentidn` int(11) DEFAULT NULL,
  `academterm` int(11) DEFAULT NULL,
  `regstdyear` enum('1','2','3','4','5') NOT NULL DEFAULT '1',
  `regdate` date DEFAULT NULL,
  `regstatus` enum('Pending','Complete') NOT NULL DEFAULT 'Pending',
  `acstatus` enum('Normal','Late') DEFAULT NULL,
  `active` enum('T','F') DEFAULT 'T'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `curriculum_settings_student`
--

INSERT INTO `curriculum_settings_student` (`_id`, `studentidn`, `academterm`, `regstdyear`, `regdate`, `regstatus`, `acstatus`, `active`) VALUES
(1, 1, 1, '1', '2025-12-29', 'Complete', 'Late', 'T'),
(2, 1, 2, '1', '2025-12-29', 'Complete', 'Normal', 'T'),
(4, 1, 3, '2', '2025-12-29', 'Complete', 'Normal', 'T'),
(5, 1, 4, '2', '2026-01-06', 'Complete', 'Late', 'T');

-- --------------------------------------------------------

--
-- Table structure for table `curriculum_settings_student_marks`
--

CREATE TABLE `curriculum_settings_student_marks` (
  `_id` int(11) NOT NULL,
  `cursetting` int(11) DEFAULT NULL,
  `registered` int(11) DEFAULT NULL,
  `credits` float(5,2) DEFAULT NULL,
  `testmarks` int(11) DEFAULT NULL,
  `corsework` int(11) DEFAULT NULL,
  `finalmark` int(11) DEFAULT NULL,
  `totals` int(11) DEFAULT NULL,
  `gradelabel` varchar(3) DEFAULT NULL,
  `gradepoint` float(5,2) DEFAULT NULL,
  `status` enum('Pending','Passed','Retake') DEFAULT 'Pending',
  `states` enum('Pending','Registered','Done') DEFAULT 'Pending',
  `active` enum('T','F') DEFAULT 'T',
  `qn_1` int(11) DEFAULT NULL,
  `qn_2` int(11) DEFAULT NULL,
  `qn_3` int(11) DEFAULT NULL,
  `qn_4` int(11) DEFAULT NULL,
  `qn_5` int(11) DEFAULT NULL,
  `qn_6` int(11) DEFAULT NULL,
  `qn_7` int(11) DEFAULT NULL,
  `qn_8` int(11) DEFAULT NULL,
  `qn_9` int(11) DEFAULT NULL,
  `qn_10` int(11) DEFAULT NULL,
  `qn_11` int(11) DEFAULT NULL,
  `qn_12` int(11) DEFAULT NULL,
  `qn_13` int(11) DEFAULT NULL,
  `qn_14` int(11) DEFAULT NULL,
  `qn_15` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `curriculum_settings_student_marks`
--

INSERT INTO `curriculum_settings_student_marks` (`_id`, `cursetting`, `registered`, `credits`, `testmarks`, `corsework`, `finalmark`, `totals`, `gradelabel`, `gradepoint`, `status`, `states`, `active`, `qn_1`, `qn_2`, `qn_3`, `qn_4`, `qn_5`, `qn_6`, `qn_7`, `qn_8`, `qn_9`, `qn_10`, `qn_11`, `qn_12`, `qn_13`, `qn_14`, `qn_15`) VALUES
(1, 2, 1, 4.00, NULL, NULL, NULL, 82, 'A', 5.00, 'Retake', 'Registered', 'T', 30, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 248, 1, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 'Pending', 'Pending', 'T', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 1, 1, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 'Pending', 'Pending', 'T', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 247, 1, 4.00, NULL, NULL, NULL, 74, 'B', 4.00, 'Pending', 'Pending', 'T', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 246, 1, 3.00, NULL, NULL, NULL, 63, 'C', 3.00, 'Pending', 'Pending', 'T', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 4, 1, 4.00, NULL, NULL, NULL, 71, 'B', 4.00, 'Pending', 'Pending', 'T', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 3, 1, 4.00, NULL, NULL, NULL, 77, 'B+', 4.50, 'Pending', 'Pending', 'T', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 251, 2, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 'Pending', 'Pending', 'T', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(10, 222, 2, 3.00, NULL, NULL, NULL, 70, 'B', 4.00, 'Pending', 'Pending', 'T', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(11, 252, 2, 3.00, NULL, NULL, NULL, 65, 'C+', 3.50, 'Pending', 'Pending', 'T', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(12, 249, 2, 5.00, NULL, NULL, NULL, 67, 'C+', 3.50, 'Pending', 'Pending', 'T', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(13, 162, 2, 4.00, NULL, NULL, NULL, 83, 'A', 5.00, 'Pending', 'Pending', 'T', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 250, 2, 4.00, NULL, NULL, NULL, 78, 'B+', 4.50, 'Pending', 'Pending', 'T', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 8, 4, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 'Pending', 'Pending', 'T', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(23, 9, 4, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 'Pending', 'Pending', 'T', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(24, 10, 4, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 'Pending', 'Pending', 'T', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(25, 11, 4, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 'Pending', 'Pending', 'T', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(26, 467, 4, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 'Pending', 'Pending', 'T', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(27, 466, 4, 4.00, NULL, NULL, NULL, 81, 'A', 5.00, 'Pending', 'Pending', 'T', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(32, 601, 5, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 'Pending', 'Pending', 'T', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(33, 600, 5, 6.00, NULL, NULL, NULL, 70, 'B', 4.00, 'Pending', 'Pending', 'T', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(34, 602, 5, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 'Pending', 'Pending', 'T', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(38, 166, 5, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 'Pending', 'Pending', 'T', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(39, 163, 5, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 'Pending', 'Pending', 'T', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(40, 164, 5, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 'Pending', 'Pending', 'T', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(41, 165, 5, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 'Pending', 'Pending', 'T', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `curriculum_settings_timetable`
--

CREATE TABLE `curriculum_settings_timetable` (
  `_id` int(11) NOT NULL,
  `currsetting` int(11) DEFAULT NULL,
  `academterm` int(11) DEFAULT NULL,
  `sessionid` int(11) DEFAULT NULL,
  `clasroomid` int(11) DEFAULT NULL,
  `testroomid` int(11) DEFAULT NULL,
  `examroomid` int(11) DEFAULT NULL,
  `timetable` enum('classes','exams') DEFAULT 'classes',
  `weekdayid` int(11) DEFAULT NULL,
  `classtime` timestamp NULL DEFAULT NULL,
  `testsdate` datetime DEFAULT NULL,
  `examsdate` datetime DEFAULT NULL,
  `active` enum('T','F') DEFAULT 'T'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `curriculum_settings_timetable`
--

INSERT INTO `curriculum_settings_timetable` (`_id`, `currsetting`, `academterm`, `sessionid`, `clasroomid`, `testroomid`, `examroomid`, `timetable`, `weekdayid`, `classtime`, `testsdate`, `examsdate`, `active`) VALUES
(1, 466, 4, 1, 3, 2, 3, 'exams', 13, '2025-12-03 11:00:00', '2025-12-22 16:52:44', '2025-12-31 16:52:50', 'T');

-- --------------------------------------------------------

--
-- Table structure for table `financial_transactions`
--

CREATE TABLE `financial_transactions` (
  `_id` int(11) NOT NULL,
  `usertype` enum('Student','Staff') DEFAULT 'Student',
  `userid` int(11) DEFAULT NULL,
  `tran_parent_ref` int(11) DEFAULT NULL,
  `tran_internal_ref` varchar(90) NOT NULL,
  `tran_external_ref` varchar(90) DEFAULT NULL,
  `tran_amount` float(10,5) DEFAULT 0.00000,
  `tran_type` enum('Credit','Debit','Charge') DEFAULT 'Credit',
  `tran_status` enum('Pending','Succeeded','Failed','Cancelled') DEFAULT 'Pending',
  `tran_mode` enum('Bank','Cash','Momo') DEFAULT 'Bank',
  `tran_non_bank_account` varchar(50) DEFAULT NULL,
  `tran_date` datetime NOT NULL DEFAULT current_timestamp(),
  `tran_details` varchar(90) DEFAULT NULL,
  `tran_status_reason` varchar(90) DEFAULT NULL,
  `active` enum('T','F') DEFAULT 'T'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `financial_transactions_items`
--

CREATE TABLE `financial_transactions_items` (
  `_id` int(11) NOT NULL,
  `tranidnum` int(11) DEFAULT NULL,
  `tranamount` int(11) DEFAULT NULL,
  `trandetails` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `registered_course`
--

CREATE TABLE `registered_course` (
  `_id` int(11) NOT NULL,
  `faculty` int(11) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `active` enum('T','F') DEFAULT 'T'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registered_course`
--

INSERT INTO `registered_course` (`_id`, `faculty`, `name`, `active`) VALUES
(1, 1, 'Information Technology', 'T'),
(2, 1, 'Computer Science', 'T'),
(3, 2, 'Public Administration', 'T'),
(4, 2, 'Business Administration', 'T'),
(5, 2, 'Human Resource Management', 'T'),
(6, 2, 'Islamic Banking and Finance', 'T'),
(7, 2, 'Procurement Supply and Logistics Management', 'T'),
(8, 3, 'Islamic Studies and Arabic Language', 'T'),
(9, 3, 'Arts in Sharia', 'T'),
(10, 4, 'Education', 'T'),
(11, 4, 'Early Childhood', 'T'),
(12, 5, 'Arts in Conflict Resolution and Peace Building', 'T');

-- --------------------------------------------------------

--
-- Table structure for table `registered_course_assignments`
--

CREATE TABLE `registered_course_assignments` (
  `_id` int(11) NOT NULL,
  `course` int(11) DEFAULT NULL,
  `nametag` enum('Prog','Secn','Sess','Intk') DEFAULT NULL,
  `assignid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registered_course_assignments`
--

INSERT INTO `registered_course_assignments` (`_id`, `course`, `nametag`, `assignid`) VALUES
(6, 1, 'Prog', 1),
(7, 1, 'Prog', 2),
(8, 1, 'Secn', 1),
(9, 1, 'Sess', 1),
(10, 1, 'Sess', 2),
(11, 1, 'Intk', 1),
(12, 1, 'Intk', 2),
(71, 2, 'Prog', 1),
(72, 2, 'Prog', 2),
(1, 2, 'Secn', 1),
(2, 2, 'Sess', 1),
(3, 2, 'Sess', 2),
(4, 2, 'Intk', 1),
(5, 2, 'Intk', 2),
(36, 3, 'Prog', 1),
(37, 3, 'Prog', 2),
(38, 3, 'Secn', 1),
(39, 3, 'Sess', 1),
(40, 3, 'Sess', 2),
(41, 3, 'Intk', 1),
(42, 3, 'Intk', 2),
(13, 4, 'Prog', 1),
(14, 4, 'Prog', 2),
(15, 4, 'Secn', 1),
(16, 4, 'Sess', 1),
(17, 4, 'Sess', 2),
(18, 4, 'Intk', 1),
(19, 4, 'Intk', 2),
(20, 5, 'Prog', 1),
(21, 5, 'Prog', 2),
(57, 5, 'Secn', 1),
(58, 5, 'Sess', 1),
(59, 5, 'Sess', 2),
(60, 5, 'Intk', 1),
(61, 5, 'Intk', 2),
(22, 6, 'Prog', 1),
(23, 6, 'Prog', 2),
(24, 6, 'Secn', 1),
(25, 6, 'Sess', 1),
(26, 6, 'Sess', 2),
(27, 6, 'Intk', 1),
(28, 6, 'Intk', 2),
(29, 7, 'Prog', 1),
(30, 7, 'Prog', 2),
(31, 7, 'Secn', 1),
(32, 7, 'Sess', 1),
(33, 7, 'Sess', 2),
(34, 7, 'Intk', 1),
(35, 7, 'Intk', 2),
(75, 8, 'Prog', 1),
(76, 8, 'Prog', 3),
(48, 8, 'Secn', 1),
(49, 8, 'Sess', 2),
(50, 8, 'Intk', 1),
(51, 8, 'Intk', 2),
(43, 9, 'Prog', 1),
(44, 9, 'Secn', 1),
(45, 9, 'Sess', 2),
(46, 9, 'Intk', 1),
(47, 9, 'Intk', 2),
(66, 10, 'Prog', 1),
(67, 10, 'Secn', 1),
(68, 10, 'Sess', 2),
(69, 10, 'Intk', 1),
(70, 10, 'Intk', 2),
(73, 11, 'Prog', 2),
(74, 11, 'Prog', 3),
(62, 11, 'Secn', 1),
(63, 11, 'Sess', 2),
(64, 11, 'Intk', 1),
(65, 11, 'Intk', 2),
(52, 12, 'Prog', 1),
(53, 12, 'Secn', 1),
(54, 12, 'Sess', 2),
(55, 12, 'Intk', 1),
(56, 12, 'Intk', 2);

-- --------------------------------------------------------

--
-- Table structure for table `registered_course_units`
--

CREATE TABLE `registered_course_units` (
  `_id` int(11) NOT NULL,
  `course` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `active` enum('T','F') DEFAULT 'T'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registered_course_units`
--

INSERT INTO `registered_course_units` (`_id`, `course`, `name`, `active`) VALUES
(1, 1, 'Communication Technology and Internet', 'T'),
(2, 1, 'Research Methods', 'T'),
(3, 1, 'Basic Statistics', 'T'),
(4, 1, 'IT Project Management', 'T'),
(5, 1, 'Computer Applications', 'T'),
(6, 1, 'Introduction to Programming and Problem Solving', 'T'),
(7, 1, 'Computer Systems', 'T'),
(8, 1, 'Mathematics for IT', 'T'),
(9, 8, 'Islamic Ethics &amp; Thought', 'T'),
(10, 10, 'Communication Skills', 'T'),
(11, 2, 'Digital Logic', 'T'),
(12, 2, 'Discrete Mathematics', 'T'),
(13, 1, 'Structured Programming', 'T'),
(14, 1, 'Hardware Repair and Maintenance', 'T'),
(15, 1, 'Electrical Installation', 'T'),
(16, 1, 'E-Commerce and Website Design', 'T'),
(17, 1, 'Fundamentals of Information Systems', 'T'),
(18, 1, 'Operating System Concepts', 'T'),
(19, 2, 'Object Oriented Programming', 'T'),
(20, 2, 'Database Systems', 'T'),
(21, 2, 'System Analysis and Design', 'T'),
(22, 2, 'Database Programming', 'T'),
(23, 1, 'Computer and Information Security', 'T'),
(24, 1, 'Computer Graphics and Multimedia', 'T'),
(25, 1, 'Computer Networks and Data Communication', 'T'),
(26, 1, 'Industrial Training', 'T'),
(27, 1, 'Web Programming', 'T'),
(28, 1, 'Software Engineering', 'T'),
(29, 1, 'IT Ethics and Professionalism', 'T'),
(30, 1, 'Business Intelligence and Data Warehousing', 'T'),
(31, 2, 'Human Computer Interaction', 'T'),
(32, 2, 'Business Application Programming', 'T'),
(33, 1, 'Technopreneurship', 'T'),
(34, 2, 'Concepts of Cloud Computing', 'T'),
(35, 1, 'Systems and Network Administration', 'T'),
(36, 1, 'Research Project', 'T'),
(37, 2, 'Numerical Methods', 'T'),
(38, 2, 'Language Theory and Automata', 'T'),
(39, 2, 'Data Structures and Algorithms', 'T'),
(40, 2, 'Signals and Communication Systems', 'T'),
(41, 2, 'Mobile Application Development', 'T'),
(42, 2, 'Artificial Intelligence and Expert Systems', 'T'),
(43, 1, 'Basic Mathematics', 'T'),
(44, 2, 'Fundamentals of Programming', 'T'),
(45, 2, 'Visual Programming', 'T'),
(46, 1, 'Electronic Commerce', 'T'),
(47, 1, 'Information Systems Management', 'T'),
(48, 2, 'Fundamentals of Database Design', 'T'),
(49, 1, 'Technical Report Writing', 'T'),
(50, 1, 'Website Development', 'T'),
(51, 1, 'Introduction to Software Engineering', 'T'),
(52, 1, 'Fundamentals of Operating Systems', 'T'),
(53, 1, 'Ethics in IT Environment', 'T'),
(54, 1, 'Network Administration', 'T'),
(55, 2, 'Fundamentals of Digital Logic', 'T'),
(56, 2, 'Computer Organisation and Architecture', 'T'),
(57, 1, 'Multimedia Design', 'T'),
(58, 1, 'Fundamentals of Computer', 'T'),
(59, 11, 'Methods and Approaches in Teaching', 'T'),
(60, 11, 'Introduction to Early Childhood Development I', 'T'),
(61, 11, 'Foundations of Education', 'T'),
(62, 11, 'Language Education', 'T'),
(63, 11, 'Education Technology I', 'T'),
(64, 11, 'Cultural Education', 'T'),
(65, 11, 'Pre-Primary Curriculum', 'T'),
(66, 11, 'Education Technology II', 'T'),
(67, 11, 'Field Work and Child Study', 'T'),
(68, 11, 'Child Growth and Development', 'T'),
(69, 11, 'School Practice I', 'T'),
(70, 11, 'Mathematics Education', 'T'),
(71, 11, 'Child Health, Nutrition, Safety and Security', 'T'),
(72, 11, 'Development Studies', 'T'),
(73, 11, 'Educational Psychology', 'T'),
(74, 11, 'Basic Entrepreneurship Development Skills', 'T'),
(75, 11, 'School Administration &amp; Management', 'T'),
(76, 11, 'Special Needs Education', 'T'),
(77, 11, 'Business Kiswahili', 'T'),
(78, 11, 'Home Management', 'T'),
(79, 11, 'Real Life Project', 'T'),
(80, 11, 'School Practice II', 'T'),
(81, 11, 'Assessment and Evaluation', 'T'),
(82, 11, 'Research Methodology', 'T'),
(83, 11, 'Early Childhood Development', 'T'),
(84, 11, 'Curriculum Education', 'T'),
(85, 11, 'Entrepreneurship Development Skills', 'T'),
(86, 11, 'Research Project', 'T'),
(87, 11, 'Mathematics Education', 'T'),
(88, 11, 'Child Health, Nutrition, Safety and Security', 'T'),
(89, 11, 'Development Studies', 'T'),
(90, 11, 'Business Kiswahili', 'T'),
(91, 11, 'Real Life Project', 'T'),
(92, 12, 'Introduction to Peace Studies', 'T'),
(93, 12, 'Conflict Theory', 'T'),
(94, 12, 'International Relations', 'T'),
(95, 12, 'Democratization and Governance', 'T'),
(96, 12, 'Research Methods', 'T'),
(97, 12, 'Introduction to Human Rights in Uganda', 'T'),
(98, 12, 'Migration and Refugee Studies', 'T'),
(99, 12, 'Diplomacy and Conflict Resolution', 'T'),
(100, 12, 'International, Security, Terrorism and Peace Keeping', 'T'),
(101, 12, 'Government, Peace and Politics in Uganda', 'T'),
(102, 12, 'Peacekeeping &amp; Conflict Resolution in Africa', 'T'),
(103, 12, 'Psycho-Social Counselling &amp; Trauma', 'T'),
(104, 12, 'Ethics &amp; African Development', 'T'),
(105, 12, 'Program and Project Management', 'T'),
(106, 12, 'Government and Politics in Sub-Saharan Africa', 'T'),
(107, 12, 'Negotiation and Conflict Resolution', 'T'),
(108, 12, 'Social Analysis for Peace and Development', 'T'),
(109, 12, 'Religion, Conflict Prevention and Peace Building', 'T'),
(110, 12, 'Behavioral Science', 'T'),
(111, 12, 'Peace and Modern Practices', 'T'),
(112, 12, 'Media and Conflict Resolution', 'T'),
(113, 12, 'Conflict Management', 'T'),
(114, 12, 'Arms Control, Proliferation and Disarmament in Africa', 'T'),
(115, 12, 'New Trends in African Security Studies', 'T'),
(116, 12, 'Gender, Development and Conflict', 'T'),
(117, 12, 'Local Government Administration', 'T'),
(118, 12, 'Public Policy Formulation and Practice', 'T'),
(119, 12, 'Indigenous Approaches to Conflict Prevention and Peace building', 'T'),
(120, 12, 'International Humanitarian Law', 'T'),
(121, 8, 'Grammar &amp; Morpholgy I', 'T'),
(122, 8, 'Arabic Literature and Texts I', 'T'),
(123, 8, 'Eloquence and Rhetoric I', 'T'),
(124, 8, 'Practices of Arabic Language I', 'T'),
(125, 8, 'General Linguistics', 'T'),
(126, 8, 'General Social Psychology I', 'T'),
(127, 8, 'Holy Quran I', 'T'),
(128, 8, 'Quran Exegesis I', 'T'),
(129, 8, 'Sciences of the Holy Quran I', 'T'),
(130, 8, 'Sciences of the Prophetic Traditions I', 'T'),
(131, 8, 'Life History of the Prophet I', 'T'),
(132, 8, 'Islamic Belief &amp; Faith I', 'T'),
(133, 9, 'Jurisprudence of Rituals I', 'T'),
(134, 9, 'Introduction to Law', 'T'),
(135, 9, 'Sources of Jurisprudence I', 'T'),
(136, 9, 'History of Islamic Legislation', 'T'),
(137, 9, 'Jurisprudence of Sunnnah', 'T'),
(138, 9, 'Jurisprudence of Sunnnah xxxx', 'T'),
(139, 9, 'Islamic Law of Contracts', 'T'),
(140, 9, 'Jurisprudence of Zakat', 'T'),
(141, 9, 'Introduction to Common Law', 'T'),
(142, 9, 'Jurisprudence of Rituals II', 'T'),
(143, 9, 'Sources of Jurisprudence II', 'T'),
(144, 9, 'Islamic Law of Contracts', 'T'),
(145, 9, 'Jurisprudence of Zakat II', 'T'),
(146, 9, 'Introduction to Tafsir', 'T'),
(147, 9, 'History of Islamic Legislation', 'T'),
(148, 9, 'Jurisprudence of Zakat', 'T'),
(149, 9, 'Islamic Family Law', 'T'),
(150, 9, 'Sources of Jurisprudence III', 'T'),
(151, 9, 'Private Islamic International Law', 'T'),
(152, 9, 'Islamic Economics', 'T'),
(153, 9, 'Research Methods', 'T'),
(154, 9, 'Islamic Family Law', 'T'),
(155, 9, 'Jurisprudence of Rituals', 'T'),
(156, 9, 'Theories of Islamic Finance', 'T'),
(157, 9, 'Bequest &amp; Endowment', 'T'),
(158, 9, 'Inheritance', 'T'),
(159, 9, 'Jurisprudence of the Quran', 'T'),
(160, 9, 'Wills &amp; Endowment', 'T'),
(161, 9, 'Sources of Jurisprudence IV', 'T'),
(162, 9, 'Islamic Public International Law', 'T'),
(163, 9, 'Bequest &amp; Endowment', 'T'),
(164, 9, 'Research Methods and Verification', 'T'),
(165, 9, 'Islamic Law of Contracts', 'T'),
(166, 9, 'Islamic Judical System', 'T'),
(167, 9, 'Islamic Law of Evidence', 'T'),
(168, 9, 'Islamic Humanitarian Law', 'T'),
(169, 9, 'Inheritance', 'T'),
(170, 9, 'Sources of Jurisprudence V', 'T'),
(171, 9, 'Islamic Law of Procedures', 'T'),
(172, 9, 'Wills and Endowment', 'T'),
(173, 9, 'Islamic Law of Inheritance', 'T'),
(174, 9, 'Sources of Jurisprudence VI', 'T'),
(175, 9, 'Islamic Criminal Law', 'T'),
(176, 9, 'Principles of Islamic Jurisprudence', 'T'),
(177, 9, 'Islamic Law of Companies', 'T'),
(178, 9, 'Islamic Consitutional Law', 'T'),
(179, 9, 'Islamic Systems', 'T'),
(180, 9, 'Islamic Economics', 'T'),
(181, 8, 'Grammar &amp; Morpholgy II', 'T'),
(182, 8, 'Arabic Literature &amp; Texts II', 'T'),
(183, 8, 'Eloquence &amp; Rhetoric II', 'T'),
(184, 8, 'Practices of Arabic Language II', 'T'),
(185, 8, 'Arabic Language Exercises', 'T'),
(186, 8, 'General Social Psychology II', 'T'),
(187, 8, 'Holy Quran II', 'T'),
(188, 8, 'Quran Exegesis II', 'T'),
(189, 8, 'Sciences of the Holy Quran II', 'T'),
(190, 8, 'Sciences of the Prophetic Traditions II', 'T'),
(191, 8, 'Life History of the Prophet II', 'T'),
(192, 8, 'Islamic Belief &amp; Faith II', 'T'),
(193, 8, 'Methods of Hadith Extraction', 'T'),
(194, 8, 'Islamic Education', 'T'),
(195, 8, 'Grammar &amp; Morpholgy III', 'T'),
(196, 8, 'Arabic Literature &amp; Texts III', 'T'),
(197, 8, 'Prosody', 'T'),
(198, 8, 'Fundamentals of Islamic Daawa', 'T'),
(199, 8, 'Holy Quran III', 'T'),
(200, 8, 'Quran Exegesis III', 'T'),
(201, 8, 'Hadith Jurisprudence', 'T'),
(202, 8, 'Muslim Sects', 'T'),
(203, 8, 'Sciences of the Holy Quran', 'T'),
(204, 8, 'Research Methods &amp; Verification', 'T'),
(205, 8, 'Grammar &amp; Morpholgy IV', 'T'),
(206, 8, 'Arabic Literature &amp; Texts IV', 'T'),
(207, 8, 'Islamic Specific Teaching Methods', 'T'),
(208, 8, 'Fundamentals of Islamic Daawa', 'T'),
(209, 8, 'Holy Quran IV', 'T'),
(210, 8, 'Quranic Miracles of Modern Sciences', 'T'),
(211, 8, 'Islam in East Africa', 'T'),
(212, 8, 'Scientific Miracles of Hadith', 'T'),
(213, 8, 'Methods of Hadith Extraction', 'T'),
(214, 8, 'Islamic Belief and Faith', 'T'),
(215, 8, 'Sciences of the Holy Quran III', 'T'),
(216, 8, 'Grammar &amp; Morpholgy V', 'T'),
(217, 8, 'Comparative Literature I', 'T'),
(218, 8, 'Arabic Specific Teaching Methods', 'T'),
(219, 8, 'Lexicology &amp; Semantics I', 'T'),
(220, 8, 'Holy Quran V', 'T'),
(221, 8, 'Hadith Jurisprudence', 'T'),
(222, 8, 'Principles of Islamic Debate', 'T'),
(223, 8, 'Contemporary Muslim World', 'T'),
(224, 8, 'Grammar &amp; Morpholgy VI', 'T'),
(225, 8, 'Comparative Literature I xxx', 'T'),
(226, 8, 'Arabic Language Exercises', 'T'),
(227, 8, 'Lexicology &amp; Semantics II', 'T'),
(228, 8, 'General Linguistics', 'T'),
(229, 8, 'Holy Quran VI', 'T'),
(230, 8, 'Quranic Miracles in Modern Sciences II', 'T'),
(231, 8, 'Introduction to Islamic Banking', 'T'),
(232, 8, 'Scientific Miracles of Hadith', 'T'),
(233, 8, 'Islamic Law of Inheritance', 'T'),
(234, 8, 'Islamic Belief &amp; Faith III', 'T'),
(235, 8, 'Strategy', 'T'),
(236, 8, 'Islam in East Africa xx', 'T'),
(237, 8, 'Research Report Project', 'T'),
(238, 4, 'Fundamentals of Accounting', 'T'),
(239, 4, 'Micro Economics', 'T'),
(240, 4, 'Principles of Business Management', 'T'),
(241, 4, 'Business Communication Skills', 'T'),
(242, 4, 'Business Mathematics', 'T'),
(243, 3, 'Introduction to Political Science', 'T'),
(244, 4, 'Macro Economics', 'T'),
(245, 4, 'Principles of Marketing', 'T'),
(246, 4, 'Business Information Technology', 'T'),
(247, 4, 'Principles of Entrepreneurship', 'T'),
(248, 4, 'Business Law', 'T'),
(249, 5, 'Introduction to Human Resource Management', 'T'),
(250, 7, 'Introduction to Procurement', 'T'),
(251, 6, 'Islamic Banking System I', 'T'),
(252, 3, 'Principles of Local Government', 'T'),
(253, 3, 'Public Administration Practice', 'T'),
(254, 5, 'Principles of Human Resource Management', 'T'),
(255, 4, 'Principles of Management', 'T'),
(256, 4, 'Fundamentals of Computerized Accounting', 'T'),
(257, 1, 'Entrepreneuship And IT', 'T'),
(258, 2, 'Programming with C', 'T'),
(259, 1, 'Computer Networks', 'T'),
(260, 1, 'Gender and ICT', 'T'),
(261, 2, 'Computer Organisation and Architecture', 'T'),
(262, 2, 'Computational Mathematics', 'T'),
(263, 2, 'Cryptography and Network Security', 'T'),
(264, 4, 'Fundamentals of Accounting II', 'T'),
(265, 5, 'Organisational Behaviour', 'T'),
(266, 5, 'Principles of Human Resource', 'T'),
(267, 3, 'Theories of Public Administration', 'T'),
(268, 3, 'New Public Administration', 'T'),
(269, 5, 'Industrial Relations and Labour Laws', 'T'),
(270, 6, 'Principles of Islamic Economics', 'T'),
(271, 4, 'Insurance Management', 'T'),
(272, 7, 'International Trade Theory', 'T'),
(273, 4, 'Intermediate Accounting', 'T'),
(274, 4, 'Business Statistics', 'T'),
(275, 4, 'Quantitative Methods', 'T'),
(276, 4, 'Principles of Insurance Management', 'T'),
(277, 4, 'Project Planning and Management', 'T'),
(278, 4, 'Company Law', 'T'),
(279, 4, 'Research Methods', 'T'),
(280, 4, 'Internship Report', 'T'),
(281, 4, 'Customer Care and Public Relations', 'T'),
(282, 4, 'Cost and Management Accounting', 'T'),
(283, 4, 'Production and Operation Management', 'T'),
(284, 4, 'Financial Management', 'T'),
(285, 5, 'Performance Management', 'T'),
(286, 5, 'Employee Guidance and Couselling', 'T'),
(287, 5, 'Occupational Health and Safety', 'T'),
(288, 5, 'Production and Operation Management', 'T'),
(289, 5, 'Human Resource Management Information System', 'T'),
(290, 5, 'Customer Care and Public Relations', 'T'),
(291, 5, 'Human Resource Planning', 'T'),
(292, 6, 'Economics in the Qur’an and Sunah', 'T'),
(293, 6, 'Sharia Economics and Society', 'T'),
(294, 7, 'Negotiation Skills', 'T'),
(295, 7, 'Supply Chain Management', 'T'),
(296, 3, 'Leadership Skills Development', 'T'),
(297, 3, 'Administrative Law', 'T'),
(298, 10, 'English Language I', 'T'),
(299, 10, 'English Language II', 'T'),
(300, 1, 'Computer Application II', 'T'),
(301, 10, 'English Language III', 'T'),
(302, 10, 'Intermediate English Language', 'T'),
(303, 10, 'History and Comparative Education', 'T'),
(304, 10, 'Introductory and Developmental Psychology', 'T'),
(305, 10, 'Philosophy and Sociology of Education', 'T'),
(306, 10, 'Psychology of Learning and Instruction', 'T'),
(307, 10, 'General Methods of Teaching', 'T'),
(308, 10, 'Educational Management &amp; Administration I', 'T'),
(309, 10, 'Research Methods in Education', 'T'),
(310, 10, 'Educational Management &amp; Administration II', 'T'),
(311, 10, 'Instructional Technology', 'T'),
(312, 10, 'Guidance and Counselling', 'T'),
(313, 10, 'Educational Measurement and Evaluation', 'T'),
(314, 10, 'Microteaching and production of Instructional Materials', 'T'),
(315, 10, 'School Practice I', 'T'),
(316, 10, 'Curriculum Studies', 'T'),
(317, 10, 'Special Needs Education', 'T'),
(318, 10, 'Microteaching', 'T'),
(319, 10, 'School Practice II', 'T'),
(320, 10, 'History and Philosophy of Muslim Education', 'T'),
(321, 10, 'Research Report', 'T'),
(322, 10, 'World Environment', 'T'),
(323, 10, 'Evolution of Geography Thought', 'T'),
(324, 10, 'Regional Geography of Africa', 'T'),
(325, 10, 'Practical Geography', 'T'),
(326, 10, 'Biogeography', 'T'),
(327, 10, 'Elements of  Physical Geography', 'T'),
(328, 10, 'Geomorphology', 'T'),
(329, 10, 'Geography of East Africa', 'T'),
(330, 10, 'Geography of Natural Disasters', 'T'),
(331, 10, 'Agricultural Geography', 'T'),
(332, 10, 'Resources &amp; Development', 'T'),
(333, 10, 'Climatology', 'T'),
(334, 10, 'Geography Teaching Methods', 'T'),
(335, 10, 'Spatial Organization of Human Environment', 'T'),
(336, 10, 'Urban Geography', 'T'),
(337, 10, 'Hydrology', 'T'),
(338, 10, 'Geography of World Development', 'T'),
(339, 10, 'Applied Geomorphology', 'T'),
(340, 10, 'Principles of Soil Science', 'T'),
(341, 10, 'Settlement of Geography', 'T'),
(342, 10, 'Geographic Information Systems and Remote  Sensing', 'T'),
(343, 10, 'Population Geography (CORE)', 'T'),
(344, 10, 'Applied Climatology(CORE)', 'T'),
(345, 10, 'Environmental Remote Sensing', 'T'),
(346, 10, 'Transport Geography', 'T'),
(347, 10, 'Economic Geography', 'T'),
(348, 10, 'Islamic History I (Seerah)', 'T'),
(349, 10, 'Hadith &amp; its Sciences I', 'T'),
(350, 10, 'Fiqh Ibadat (Rituals)', 'T'),
(351, 10, 'Islam In  East Africa', 'T'),
(352, 10, 'Aqeedah', 'T'),
(353, 10, 'Sources of Islamic  Law', 'T'),
(354, 10, 'Ulum Al–Quran', 'T'),
(355, 10, 'Islamic systems', 'T'),
(356, 10, 'Islam in West Africa', 'T'),
(357, 10, 'Islamic History II', 'T'),
(358, 10, 'I.R.E Teaching methods', 'T'),
(359, 10, 'Islamic Law of Contracts', 'T'),
(360, 10, 'Contemporary Muslim World', 'T'),
(361, 10, 'Religion and Gender', 'T'),
(362, 10, 'Islamic Family Law', 'T'),
(363, 10, 'Islamic  Philosophy', 'T'),
(364, 10, 'World Religions', 'T'),
(365, 10, 'Modern Islamic Reform Movement', 'T'),
(366, 10, 'Themes in African History from the earliest times to 1871', 'T'),
(367, 10, 'Historiography and Research Methodology', 'T'),
(368, 10, 'Historicism and Islamic Civilization', 'T'),
(369, 10, 'History of Uganda from 1800', 'T'),
(370, 10, 'Themes in African History from 1871 to present', 'T'),
(371, 10, 'History of World Revolutions since 1750', 'T'),
(372, 10, 'Themes in European History from 1789 to 1914', 'T'),
(373, 10, 'African Nationalism since 1900', 'T'),
(374, 10, 'History of Imperialism and neo-colonialism in Africa since 1800', 'T'),
(375, 10, 'Themes in European History since 1914', 'T'),
(376, 10, 'Methods in teaching History', 'T'),
(377, 10, 'History of Southern African since 1800 to date', 'T'),
(378, 10, 'Themes in History of West Africa from 1800 to date', 'T'),
(379, 10, 'Themes in History of North Africa from 1000AD to present.', 'T'),
(380, 10, 'History of Human Rights in African from 1800 to present', 'T'),
(381, 10, 'History of USA since the 15th Century to present', 'T'),
(382, 10, 'History of World Affairs since 1939', 'T'),
(383, 10, 'History of Contemporary Islamic reform movements', 'T'),
(384, 10, 'Introduction to Microeconomics', 'T'),
(385, 10, 'Introduction to Mathematical Economics', 'T'),
(386, 10, 'History of Economic Thought', 'T'),
(387, 10, 'Introduction to Macroeconomics', 'T'),
(388, 10, 'Agriculture Economics', 'T'),
(389, 10, 'Industrial and Labor Economics', 'T'),
(390, 10, 'Intermediate Microeconomics', 'T'),
(391, 10, 'Quantitative Methods', 'T'),
(392, 10, 'Business Statistics', 'T'),
(393, 10, 'Intermediate Macroeconomics', 'T'),
(394, 10, 'Economics Teaching Methods', 'T'),
(395, 10, 'Structure of African Economics', 'T'),
(396, 10, 'Econometrics', 'T'),
(397, 10, 'Advanced Microeconomics', 'T'),
(398, 10, 'Development Economics', 'T'),
(399, 10, 'Economic Planning and Policy', 'T'),
(400, 10, 'Advanced Macroeconomics', 'T'),
(401, 10, 'Monetary Economics', 'T'),
(402, 10, 'Introduction to the study of Luganda Language', 'T'),
(403, 10, 'Introduction to the Principles of Translation', 'T'),
(404, 10, 'The Phonology of Luganda Language', 'T'),
(405, 10, 'Prominent contributors to Luganda', 'T'),
(406, 10, 'Transformational Grammar/ Luganda syntax', 'T'),
(407, 10, 'The Morphology of Luganda Language', 'T'),
(408, 10, 'Creative Writing in Luganda', 'T'),
(409, 10, 'The Luganda Novel', 'T'),
(410, 10, 'Luganda Teaching Methods', 'T'),
(411, 10, 'Comparative study of Luganda and other related  languages', 'T'),
(412, 10, 'Luganda Stylistics', 'T'),
(413, 10, 'Philosophical Interpretation of Proverbs in Luganda', 'T'),
(414, 10, 'Kiganga Poetry', 'T'),
(415, 10, 'Drama in Luganda', 'T'),
(416, 10, 'Children’s Literature in Luganda', 'T'),
(417, 10, 'Contemporary studies in Luganda', 'T'),
(418, 10, 'Grammar and ( Exchange) Morphology 1', 'T'),
(419, 10, 'Rhetoric 1', 'T'),
(420, 10, 'Arabic Language Exercises I', 'T'),
(421, 10, 'Principles of Translation', 'T'),
(422, 10, 'Grammar and Morphology 11', 'T'),
(423, 10, 'Literature and Texts 1', 'T'),
(424, 10, 'Arabic Language Exercises III', 'T'),
(425, 10, 'Grammar and Morphology 111', 'T'),
(426, 10, 'Arabic Language Exercises II', 'T'),
(427, 10, 'Albalagah (Rhetoric 11)', 'T'),
(428, 10, 'Grammar and Morphology 1V', 'T'),
(429, 10, 'Arabic Teaching Methods', 'T'),
(430, 10, 'Literature and Texts', 'T'),
(431, 10, 'Grammar and Morphology v', 'T'),
(432, 10, 'Literature and Texts II', 'T'),
(433, 10, 'Grammar and Morphology VI', 'T'),
(434, 10, 'Rhetoric  111', 'T'),
(435, 10, 'Introduction to literacy Genres', 'T'),
(436, 10, 'Introduction to East African Oral Literature', 'T'),
(437, 10, 'Drama', 'T'),
(438, 10, 'East Africa Prose Fiction', 'T'),
(439, 10, 'Critical Reading and Response', 'T'),
(440, 10, 'East Africa Poetry and Drama', 'T'),
(441, 10, 'The Novel', 'T'),
(442, 10, 'Theory and History of Literature', 'T'),
(443, 10, 'Poetry', 'T'),
(444, 10, 'Methods of Teaching Literature', 'T'),
(445, 10, 'Literature Aesthetics', 'T'),
(446, 10, 'African Literature', 'T'),
(447, 10, 'Literary Language and Presentation', 'T'),
(448, 10, 'Literary Criticism', 'T'),
(449, 10, 'Drama in Education', 'T'),
(450, 10, 'Introduction to the study of English', 'T'),
(451, 10, 'Introduction to the Grammar of English', 'T'),
(452, 10, 'Teaching the Productive and Receptive skills of communication', 'T'),
(453, 10, 'Introduction to English Phonetics and Phonology', 'T'),
(454, 10, 'Morphology of English', 'T'),
(455, 10, 'Teaching Grammar and Vocabulary', 'T'),
(456, 10, 'Advanced English Grammar', 'T'),
(457, 10, 'English Syntax', 'T'),
(458, 10, 'Second Language Acquisition', 'T'),
(459, 10, 'English Semantics and Pragmatics', 'T'),
(460, 10, 'Varieties of English', 'T'),
(461, 10, 'Applied Linguistics', 'T'),
(462, 10, 'Discourse Analysis', 'T'),
(463, 10, 'Sociolinguistics', 'T'),
(464, 10, 'English Teaching: Testing and Evaluation', 'T'),
(465, 3, 'Public Policy Formulation and Strategy', 'T'),
(466, 3, 'Public Sector Accounting', 'T'),
(467, 4, 'Advanced Accounting', 'T'),
(468, 4, 'Advanced Marketing', 'T'),
(469, 5, 'Industrial Relations', 'T'),
(470, 7, 'Public Procurement and Disposal of Assets', 'T'),
(471, 6, 'Islamic Capital Markets', 'T'),
(472, 4, 'Business Ethics', 'T'),
(473, 4, 'Electronic Marketing', 'T'),
(474, 4, 'Auditing and Investigation', 'T'),
(475, 5, 'Industrial Psychology', 'T'),
(476, 7, 'Retail and Merchandize Management', 'T'),
(477, 6, 'Auditing and Governance for Islamic Finance', 'T'),
(478, 3, 'Resource Mobilization and Strategy', 'T'),
(479, 3, 'Community Development', 'T'),
(480, 4, 'Strategic Management', 'T'),
(481, 5, 'Labour Laws', 'T'),
(482, 4, 'Managerial Economics', 'T'),
(483, 5, 'Employee Training and Management', 'T'),
(484, 3, 'Knowledge Management for Organization', 'T'),
(485, 7, 'Project and Contract Management', 'T'),
(486, 4, 'Consumer Behaviour', 'T'),
(487, 7, 'International Sourcing and Customs Clearance', 'T'),
(488, 6, 'Principles and Practices of Takaful And Re-Takaful (Islamic Insurance)', 'T'),
(489, 3, 'Local Government Finance', 'T'),
(490, 3, 'Innovative Public Management', 'T'),
(491, 5, 'Reward Management', 'T'),
(492, 5, 'Labour Economics', 'T'),
(493, 3, 'Development Theory and Practice', 'T'),
(494, 10, 'xxxx', 'T'),
(495, 7, 'Procurement Environment', 'T'),
(496, 7, 'Storage and Distribution in Supply Chain', 'T'),
(497, 12, 'Post-War Reconstruction and Sustainable Development', 'T'),
(498, 1, 'Computer Application I', 'T'),
(499, 1, 'Research Project', 'T'),
(500, 12, 'Disaster Preparedness and Management', 'T'),
(501, 12, 'Post-Conflict Security Sector Reform in Africa', 'T'),
(502, 12, 'Peace and Security in the Great Lakes Region', 'T'),
(503, 12, 'Globalization Culture and Identity', 'T'),
(504, 12, 'Human Security and Development', 'T'),
(505, 12, 'Internship', 'T'),
(506, 12, 'Dissertation', 'T'),
(507, 12, 'Peace Keeping and Conflict Resolution in Africa', 'T'),
(508, 12, 'Psycho-Social Counselling and Trauma Healing', 'T'),
(509, 12, 'xxxx', 'F'),
(510, 12, 'Project Planning and Management', 'T'),
(511, 12, 'xxxx', 'T'),
(512, 12, 'xxxxx', 'T'),
(513, 12, 'x', 'F');

-- --------------------------------------------------------

--
-- Table structure for table `registered_faculty`
--

CREATE TABLE `registered_faculty` (
  `_id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `active` enum('T','F') DEFAULT 'T'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registered_faculty`
--

INSERT INTO `registered_faculty` (`_id`, `name`, `active`) VALUES
(1, 'Faculty of Science and Technology', 'T'),
(2, 'Faculty of Business and Management', 'T'),
(3, 'Faculty of Islamic Studies and Arabic Language', 'T'),
(4, 'Faculty of Education', 'T'),
(5, 'Faculty of Humanities', 'T');

-- --------------------------------------------------------

--
-- Table structure for table `setting_collections`
--

CREATE TABLE `setting_collections` (
  `_id` int(11) NOT NULL,
  `names` varchar(65) DEFAULT NULL,
  `label` enum('Mon','day','Hall','Nash','Relgn') DEFAULT NULL,
  `active` enum('T','F') DEFAULT 'T'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting_collections`
--

INSERT INTO `setting_collections` (`_id`, `names`, `label`, `active`) VALUES
(1, 'January', 'Mon', 'T'),
(2, 'Febrary', 'Mon', 'T'),
(3, 'March', 'Mon', 'T'),
(4, 'April', 'Mon', 'T'),
(5, 'May', 'Mon', 'T'),
(6, 'June', 'Mon', 'T'),
(7, 'July', 'Mon', 'T'),
(8, 'August', 'Mon', 'T'),
(9, 'September', 'Mon', 'T'),
(10, 'October', 'Mon', 'T'),
(11, 'November', 'Mon', 'T'),
(12, 'December', 'Mon', 'T'),
(13, 'Monday', 'day', 'T'),
(14, 'Tuesday', 'day', 'T'),
(15, 'Wednsday', 'day', 'T'),
(16, 'Thursday', 'day', 'T'),
(17, 'Friday', 'day', 'T'),
(18, 'Saturday', 'day', 'T'),
(19, 'Sunday', 'day', 'T'),
(20, 'Minnah', 'Hall', 'T'),
(21, 'Muzidalifah', 'Hall', 'T'),
(22, 'ABC', 'Hall', 'T'),
(23, 'DEF', 'Hall', 'T'),
(24, 'Muslim', 'Relgn', 'T'),
(25, 'Catholic', 'Relgn', 'T'),
(26, 'Protestant', 'Relgn', 'T'),
(27, 'Seventhday', 'Relgn', 'T'),
(28, 'Ugandan', 'Nash', 'T'),
(29, 'Kenyan', 'Nash', 'T'),
(30, 'Tanzanian', 'Nash', 'T'),
(31, 'Rwandise', 'Nash', 'T'),
(32, 'Burundi', 'Nash', 'T'),
(33, 'Sudanise', 'Nash', 'T'),
(34, 'Somali', 'Nash', 'T'),
(35, 'Libian', 'Nash', 'T'),
(36, 'Congolise', 'Nash', 'T');

-- --------------------------------------------------------

--
-- Table structure for table `setting_exams_system`
--

CREATE TABLE `setting_exams_system` (
  `sysid` int(11) NOT NULL,
  `sysname` varchar(65) DEFAULT NULL,
  `sysactive` enum('T','F') DEFAULT 'T'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting_exams_system`
--

INSERT INTO `setting_exams_system` (`sysid`, `sysname`, `sysactive`) VALUES
(1, 'First Grade Seting - OLD', 'T'),
(2, 'Second Grade Seting - IUIU', 'T'),
(3, 'Second Grade Seting - NCHE', 'T');

-- --------------------------------------------------------

--
-- Table structure for table `setting_exams_system_award`
--

CREATE TABLE `setting_exams_system_award` (
  `id` int(11) NOT NULL,
  `systemid` int(11) NOT NULL,
  `programid` int(11) NOT NULL,
  `name` varchar(65) DEFAULT NULL,
  `cls_min` float(3,1) DEFAULT NULL,
  `cls_max` float(3,1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting_exams_system_award`
--

INSERT INTO `setting_exams_system_award` (`id`, `systemid`, `programid`, `name`, `cls_min`, `cls_max`) VALUES
(1, 1, 1, 'First Class', 4.4, 5.0),
(2, 1, 1, 'Second class (upper division)', 4.0, 4.3),
(3, 1, 1, 'Second class (lower division)', 3.0, 3.9),
(4, 1, 1, 'Pass', 2.0, 2.9),
(5, 1, 1, '-', 0.0, 1.9),
(6, 1, 2, 'First Class', 4.4, 5.0),
(7, 1, 2, 'Second class (upper division)', 4.0, 4.3),
(8, 1, 2, 'Second class (lower division)', 3.0, 3.9),
(9, 1, 2, 'Pass', 2.0, 2.9),
(10, 1, 2, '-', 0.0, 1.9),
(11, 1, 3, 'First Class', 4.4, 5.0),
(12, 1, 3, 'Second class (upper division)', 4.0, 4.3),
(13, 1, 3, 'Second class (lower division)', 3.0, 3.9),
(14, 1, 3, 'Pass', 2.0, 2.9),
(15, 1, 3, '-', 0.0, 1.9);

-- --------------------------------------------------------

--
-- Table structure for table `setting_exams_system_grading`
--

CREATE TABLE `setting_exams_system_grading` (
  `id` int(11) NOT NULL,
  `systemid` int(11) NOT NULL,
  `grade` varchar(65) DEFAULT NULL,
  `grd_max` int(11) DEFAULT NULL,
  `grd_min` int(11) DEFAULT NULL,
  `points` float(2,1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting_exams_system_grading`
--

INSERT INTO `setting_exams_system_grading` (`id`, `systemid`, `grade`, `grd_max`, `grd_min`, `points`) VALUES
(1, 1, 'A', 100, 80, 5.0),
(2, 1, 'B+', 79, 75, 4.5),
(3, 1, 'B', 74, 70, 4.0),
(4, 1, 'C+', 69, 65, 3.5),
(5, 1, 'C', 64, 60, 3.0),
(6, 1, 'D+', 59, 55, 2.5),
(7, 1, 'D', 54, 50, 2.0),
(8, 1, 'F', 49, 0, 0.0);

-- --------------------------------------------------------

--
-- Table structure for table `setting_intakes`
--

CREATE TABLE `setting_intakes` (
  `_id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `canapply` enum('T','F') DEFAULT 'T',
  `active` enum('T','F') DEFAULT 'T'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting_intakes`
--

INSERT INTO `setting_intakes` (`_id`, `name`, `canapply`, `active`) VALUES
(1, 'January', 'F', 'T'),
(2, 'August', 'T', 'T');

-- --------------------------------------------------------

--
-- Table structure for table `setting_programes`
--

CREATE TABLE `setting_programes` (
  `_id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `duration` int(11) DEFAULT 0,
  `retake` int(11) DEFAULT 0,
  `active` enum('T','F') DEFAULT 'T'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting_programes`
--

INSERT INTO `setting_programes` (`_id`, `name`, `duration`, `retake`, `active`) VALUES
(1, 'Bachelor of', 3, 50, 'T'),
(2, 'Diploma in', 2, 40, 'T'),
(3, 'Certificate in', 2, 30, 'T');

-- --------------------------------------------------------

--
-- Table structure for table `setting_programes_exams`
--

CREATE TABLE `setting_programes_exams` (
  `_id` int(11) NOT NULL,
  `programid` int(11) NOT NULL,
  `name` varchar(65) DEFAULT NULL,
  `contributes` int(11) DEFAULT NULL,
  `active` enum('T','F') DEFAULT 'T'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting_programes_exams`
--

INSERT INTO `setting_programes_exams` (`_id`, `programid`, `name`, `contributes`, `active`) VALUES
(1, 1, 'Final Examinations', 60, 'T'),
(2, 1, 'Coursework', 20, 'T'),
(3, 1, 'Tests', 20, 'T'),
(4, 2, 'Final Examinations', 60, 'T'),
(5, 2, 'Coursework', 20, 'T'),
(6, 2, 'Tests', 20, 'T'),
(7, 3, 'Final Examinations', 60, 'T'),
(8, 3, 'Coursework', 20, 'T'),
(9, 3, 'Tests', 20, 'T');

-- --------------------------------------------------------

--
-- Table structure for table `setting_rooms`
--

CREATE TABLE `setting_rooms` (
  `_id` int(11) NOT NULL,
  `roomname` varchar(50) DEFAULT NULL,
  `roomtype` enum('Normal','Computer','Laboratory') DEFAULT 'Normal',
  `capacity` int(11) DEFAULT NULL,
  `active` enum('T','F') DEFAULT 'T'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting_rooms`
--

INSERT INTO `setting_rooms` (`_id`, `roomname`, `roomtype`, `capacity`, `active`) VALUES
(1, 'Comp Lab 1', 'Computer', 20, 'T'),
(2, 'Comp Lab 2', 'Computer', 40, 'T'),
(3, 'N.B 306', 'Normal', 80, 'T'),
(4, 'N.B 200', 'Normal', 500, 'T');

-- --------------------------------------------------------

--
-- Table structure for table `setting_sections`
--

CREATE TABLE `setting_sections` (
  `_id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `duration` int(11) DEFAULT 3,
  `active` enum('T','F') DEFAULT 'T'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting_sections`
--

INSERT INTO `setting_sections` (`_id`, `name`, `duration`, `active`) VALUES
(1, 'Semester', 2, 'T');

-- --------------------------------------------------------

--
-- Table structure for table `setting_sessions`
--

CREATE TABLE `setting_sessions` (
  `_id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `active` enum('T','F') DEFAULT 'T'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting_sessions`
--

INSERT INTO `setting_sessions` (`_id`, `name`, `active`) VALUES
(1, 'Day', 'T'),
(2, 'Evening', 'T'),
(3, 'Weekend', 'T');

-- --------------------------------------------------------

--
-- Table structure for table `system_branches`
--

CREATE TABLE `system_branches` (
  `id` int(11) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `website` text DEFAULT NULL,
  `details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_branches`
--

INSERT INTO `system_branches` (`id`, `title`, `phone`, `email`, `website`, `details`) VALUES
(1, 'Main Campus', NULL, NULL, NULL, 'The Main Campus is situated in Kansanga, Ggaba Road, in the vibrant capital city of Kampala. T'),
(2, 'Western Campus', NULL, NULL, NULL, 'KIU-Western Campus is a unique campus, located in Ishaka, Bushenyi District. It is the hub of health sciences as a University. We are proud of our extensive involvement in teaching, research, and innovation in all our programs.'),
(3, 'Tanzania Campus', NULL, NULL, NULL, 'Kampala International University in Tanzania (KIUT) stands today as one of the most dynamic and rapidly growing private universities in the country, a symbol of academic excellence, resilience, and visionary leadership.');

-- --------------------------------------------------------

--
-- Table structure for table `system_charges`
--

CREATE TABLE `system_charges` (
  `_hid` int(11) NOT NULL,
  `_hcode` varchar(50) DEFAULT NULL,
  `_hnames` varchar(50) DEFAULT NULL,
  `_hcharge` float(10,2) DEFAULT 0.00,
  `_foradmin` enum('T','F') DEFAULT 'T',
  `_hactive` enum('T','F') DEFAULT 'T'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_charges`
--

INSERT INTO `system_charges` (`_hid`, `_hcode`, `_hnames`, `_hcharge`, `_foradmin`, `_hactive`) VALUES
(1, 'REG001', 'Admission Fee', 0.00, 'T', 'T'),
(2, 'REG002', 'Registration Percentage', 0.00, 'T', 'T'),
(3, 'REG003', 'Late Registration Fee', 50000.00, 'T', 'T'),
(4, 'REG004', 'Retake Registration Fee', 50000.00, 'T', 'T'),
(5, 'REG005', 'Accommodation Fee', 0.00, 'T', 'T');

-- --------------------------------------------------------

--
-- Table structure for table `system_credentials`
--

CREATE TABLE `system_credentials` (
  `_cid` int(11) NOT NULL,
  `reff_number` varchar(10) DEFAULT NULL,
  `descriptins` varchar(80) DEFAULT NULL,
  `credentials` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_credentials`
--

INSERT INTO `system_credentials` (`_cid`, `reff_number`, `descriptins`, `credentials`) VALUES
(1, 'REF001', 'contact information', '==Qfi0kaVJjT\n6dGNOR1Z18ERFRjI6ISWXJFdhdVNwN2MSlXWYJldjdWP9ICIsISTqVlMOpXQ49EVrNjT6FENiojI\nhdkVzN2R4BnYtVVPiACLiE2V10mYwIEbidlR1N2MkBnWuFVdZJTO0JiOiE2V10mYyEDahd1d9Iye'),
(2, 'REF002', 'Email configuration', '9JSWXJFdhdVN\nBplMxgWYXdXdZJTO0JiOiMmMWVnWHZVeSdVMoF2V31jIgwiIZdlU0F2V1A3YzIVeZhlU2N2Z90jI\n6IyYyYVdadkV5RVbGRnWR1TPiACLio1MkFXZXZEel1Gd2Q2MGtmWIxWNkFUP9IiOiMmMWp2YtZFM\nhJjV1ICIsIiYXZEcihkTsJWbSx2YqNWMOBjQuJ2VGBnYDVjaiJDM9IiOik1VOpmWY5keTJjV1Iye'),
(3, 'REF003', 'SMS configuration', '9JiVywmeadlSoJmbSx2YrFUeNdWP9IiOiMmMWp2YtZFM\nhJjV1ICIsIyYHZFdZ1mVtl1VohmWEFFNOBjQuJ2VGBnYDVjaiJDM9IiOik1VOpmWY5keTJjV1Iye'),
(4, 'REF004', 'LivePay configuration', '9JCVWJEVadlTMxEVklWTq1keZpmUqllajdnTyk0MZpXQ3lla\nGt2TEV0dNJjSrlFVrpnTXlEMMZ1Z9IiOiMmMWp2YtZFMhJjV1ICIsICVWJUUkdlSMxEVjRzTEpEb\napWV55UbRFjTEV0MadUW35ERKt2TEFEMNRkQrp1ValWWqVkMMZ1Z9IiOik1VOpmWY5keTJjV1Iye');

-- --------------------------------------------------------

--
-- Table structure for table `system_logs`
--

CREATE TABLE `system_logs` (
  `_id` int(11) NOT NULL,
  `loginid` int(11) DEFAULT NULL,
  `uroleid` int(11) DEFAULT NULL,
  `userpid` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `tbaction` enum('SELECT','INSERT','UPDATE','DELETE') DEFAULT 'SELECT',
  `tblname` text DEFAULT NULL,
  `tblwhere` text DEFAULT NULL,
  `act_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_logs`
--

INSERT INTO `system_logs` (`_id`, `loginid`, `uroleid`, `userpid`, `details`, `tbaction`, `tblname`, `tblwhere`, `act_date`) VALUES
(1, 1, 1, 1, NULL, 'INSERT', 'setting_programes', '[]', '2026-05-01 20:26:44'),
(2, 1, 1, 1, NULL, 'INSERT', 'setting_programes_exams', '[]', '2026-05-01 20:26:44'),
(3, 1, 1, 1, NULL, 'UPDATE', 'setting_programes', '{\"_id\":\"5\"}', '2026-05-01 20:37:51'),
(4, 1, 1, 1, NULL, 'INSERT', 'setting_programes', '[]', '2026-05-01 20:41:25'),
(5, 1, 1, 1, NULL, 'INSERT', 'setting_programes_exams', '[]', '2026-05-01 20:41:25'),
(6, 1, 1, 1, NULL, 'UPDATE', 'setting_programes', '{\"_id\":\"6\"}', '2026-05-01 20:41:45'),
(7, 1, 1, 1, NULL, 'UPDATE', 'setting_programes', '{\"_id\":\"3\"}', '2026-05-01 20:42:44'),
(8, 1, 1, 1, NULL, 'UPDATE', 'setting_programes', '{\"_id\":\"3\"}', '2026-05-01 20:42:49'),
(9, 1, 1, 1, NULL, 'UPDATE', 'setting_programes', '{\"_id\":\"3\"}', '2026-05-01 20:42:54'),
(10, 1, 1, 1, NULL, 'UPDATE', 'setting_programes', '{\"_id\":\"3\"}', '2026-05-01 20:47:01'),
(11, 1, 1, 1, NULL, 'UPDATE', 'setting_programes_exams', '{\"programid\":1}', '2026-05-01 22:08:27'),
(12, 1, 1, 1, NULL, 'UPDATE', 'setting_programes_exams', '{\"programid\":1}', '2026-05-01 22:11:31'),
(13, 1, 1, 1, NULL, 'UPDATE', 'setting_programes_exams', '{\"programid\":1}', '2026-05-01 22:11:33'),
(14, 1, 1, 1, NULL, 'UPDATE', 'setting_programes_exams', '{\"programid\":1}', '2026-05-01 22:11:52'),
(15, 1, 1, 1, NULL, 'UPDATE', 'setting_programes_exams', '{\"programid\":3}', '2026-05-01 22:12:03'),
(16, 1, 1, 1, NULL, 'UPDATE', 'setting_programes_exams', '{\"programid\":1}', '2026-05-01 22:13:13'),
(17, 1, 1, 1, NULL, 'UPDATE', 'setting_programes_exams', '{\"programid\":1}', '2026-05-01 22:19:24'),
(18, 1, 1, 1, NULL, 'UPDATE', 'setting_programes_exams', '{\"programid\":3}', '2026-05-01 22:20:38'),
(19, 1, 1, 1, NULL, 'UPDATE', 'setting_programes_exams', '{\"programid\":2}', '2026-05-01 22:21:03'),
(20, 1, 1, 1, NULL, 'UPDATE', 'setting_programes_exams', '{\"programid\":1}', '2026-05-02 17:50:41'),
(21, 1, 1, 1, NULL, 'UPDATE', 'setting_intakes', '{\"_id\":\"1\"}', '2026-05-02 17:50:55'),
(22, 1, 1, 1, NULL, 'UPDATE', 'setting_intakes', '{\"_id\":\"1\"}', '2026-05-02 17:51:03'),
(23, 1, 1, 1, NULL, 'UPDATE', 'setting_intakes', '{\"_id\":\"1\"}', '2026-05-02 17:51:10'),
(24, 1, 1, 1, NULL, 'UPDATE', 'setting_intakes', '{\"_id\":\"1\"}', '2026-05-02 17:51:19'),
(25, 1, 1, 1, NULL, 'UPDATE', 'setting_programes_exams', '{\"programid\":2}', '2026-05-02 18:27:30'),
(26, 1, 1, 1, NULL, 'UPDATE', 'setting_programes_exams', '{\"programid\":1}', '2026-05-02 18:41:22'),
(27, 1, 1, 1, NULL, 'UPDATE', 'registered_faculty', '{\"_id\":\"1\"}', '2026-05-03 13:42:53'),
(28, 1, 1, 1, NULL, 'UPDATE', 'registered_faculty', '{\"_id\":\"1\"}', '2026-05-03 13:43:01'),
(29, 1, 1, 1, NULL, 'UPDATE', 'registered_faculty', '{\"_id\":\"1\"}', '2026-05-03 13:43:09'),
(30, 1, 1, 1, NULL, 'UPDATE', 'registered_faculty', '{\"_id\":\"5\"}', '2026-05-04 01:44:20'),
(31, 1, 1, 1, NULL, 'UPDATE', 'setting_sections', '{\"_id\":\"1\"}', '2026-05-04 01:45:40'),
(32, 1, 1, 1, NULL, 'UPDATE', 'registered_faculty', '{\"_id\":\"1\"}', '2026-05-09 09:30:19'),
(33, 1, 1, 1, NULL, 'UPDATE', 'registered_course', '{\"_id\":\"1\"}', '2026-05-09 13:45:40'),
(34, 1, 1, 1, NULL, 'UPDATE', 'registered_course', '{\"_id\":\"2\"}', '2026-05-09 13:45:48'),
(35, 1, 1, 1, NULL, 'UPDATE', 'registered_course', '{\"_id\":\"2\"}', '2026-05-09 13:45:59'),
(36, 1, 1, 1, NULL, 'UPDATE', 'registered_course', '{\"_id\":\"2\"}', '2026-05-09 13:46:06'),
(37, 1, 1, 1, NULL, 'UPDATE', 'registered_course', '{\"_id\":\"1\"}', '2026-05-15 12:15:34'),
(38, 1, 1, 1, NULL, 'UPDATE', 'registered_course', '{\"_id\":\"2\"}', '2026-05-15 12:16:46'),
(39, 1, 1, 1, NULL, 'UPDATE', 'registered_course', '{\"_id\":\"2\"}', '2026-05-15 12:16:52');

-- --------------------------------------------------------

--
-- Table structure for table `users_application`
--

CREATE TABLE `users_application` (
  `_aid` int(11) NOT NULL,
  `astates` enum('Pending','Paid','Admitted','Returned') DEFAULT 'Pending',
  `admissionno` varchar(65) DEFAULT NULL,
  `receiptnumb` varchar(65) DEFAULT NULL,
  `referee` int(11) DEFAULT NULL,
  `fnames` varchar(65) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(65) DEFAULT NULL,
  `intake` int(11) DEFAULT NULL,
  `religion` int(11) DEFAULT NULL,
  `nationality` int(11) DEFAULT NULL,
  `gender` enum('M','F') DEFAULT 'M',
  `applicationDate` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_application_courses`
--

CREATE TABLE `users_application_courses` (
  `_acid` int(11) NOT NULL,
  `applied` int(11) DEFAULT NULL,
  `course` int(11) DEFAULT NULL,
  `program` int(11) DEFAULT NULL,
  `section` int(11) DEFAULT NULL,
  `session` int(11) DEFAULT NULL,
  `priotized` enum('T','F') DEFAULT 'T'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_auth`
--

CREATE TABLE `users_auth` (
  `_id` int(11) NOT NULL,
  `userrole` int(11) DEFAULT NULL,
  `utype` enum('MAIN','ASST') DEFAULT 'ASST',
  `useridno` int(11) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(65) DEFAULT NULL,
  `isactive` enum('T','F') DEFAULT 'F',
  `passchange` enum('T','F') DEFAULT 'F',
  `tempcode` varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_auth`
--

INSERT INTO `users_auth` (`_id`, `userrole`, `utype`, `useridno`, `username`, `password`, `isactive`, `passchange`, `tempcode`) VALUES
(1, 1, 'MAIN', 1, 'admin', '96f61d4318a36d2347fef75a82eafc88', 'T', 'T', NULL),
(2, 5, 'MAIN', 2, 'U-0001', '96f61d4318a36d2347fef75a82eafc88', 'T', 'T', NULL),
(3, 6, 'MAIN', 1, '212-063012-04362', '2843206c6e480422905238c4ce44a8ce', 'T', 'T', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_role`
--

CREATE TABLE `users_role` (
  `_rid` int(11) NOT NULL,
  `_rname` varchar(65) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_role`
--

INSERT INTO `users_role` (`_rid`, `_rname`) VALUES
(1, 'Administrator'),
(2, 'Accountant'),
(3, 'Academic Registrar'),
(4, 'Dean'),
(5, 'Lecturer'),
(6, 'Student'),
(7, 'Alumin');

-- --------------------------------------------------------

--
-- Table structure for table `users_staff`
--

CREATE TABLE `users_staff` (
  `_uid` int(11) NOT NULL,
  `admited` int(11) DEFAULT NULL,
  `faculty` int(11) DEFAULT NULL,
  `registerno` varchar(65) DEFAULT NULL,
  `fnames` varchar(65) DEFAULT NULL,
  `email` varchar(65) DEFAULT NULL,
  `phone` varchar(12) DEFAULT NULL,
  `gender` enum('M','F') DEFAULT 'M',
  `religion` int(11) DEFAULT NULL,
  `nationality` int(11) DEFAULT NULL,
  `pactive` enum('T','F') DEFAULT 'T'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_staff`
--

INSERT INTO `users_staff` (`_uid`, `admited`, `faculty`, `registerno`, `fnames`, `email`, `phone`, `gender`, `religion`, `nationality`, `pactive`) VALUES
(1, 1, 1, 'admin', 'System Administrator', 'wisebanter5@gmail.com', '0788589818', 'M', NULL, NULL, 'T'),
(2, 1, 1, 'U-0001', 'Sample Lecture', 'sample@gmail.com', '0788000111', 'M', 24, 28, 'T');

-- --------------------------------------------------------

--
-- Table structure for table `users_student`
--

CREATE TABLE `users_student` (
  `_uid` int(11) NOT NULL,
  `reffNumb` varchar(65) DEFAULT NULL,
  `registerno` varchar(65) DEFAULT NULL,
  `branchid` int(11) NOT NULL,
  `admited` int(11) DEFAULT NULL,
  `curriculum` int(11) NOT NULL,
  `course` int(11) DEFAULT NULL,
  `program` int(11) DEFAULT NULL,
  `section` int(11) DEFAULT NULL,
  `session` int(11) DEFAULT NULL,
  `intake` int(11) DEFAULT NULL,
  `stdyear` enum('1','2','3','4','5') NOT NULL DEFAULT '1',
  `religion` int(11) DEFAULT NULL,
  `uhall` int(11) DEFAULT NULL,
  `nationality` int(11) DEFAULT NULL,
  `fnames` varchar(65) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(65) DEFAULT NULL,
  `birthDate` datetime NOT NULL DEFAULT current_timestamp(),
  `gender` enum('M','F') DEFAULT 'M',
  `inhostel` enum('T','F') DEFAULT 'F',
  `ishalted` enum('T','F') DEFAULT 'F',
  `deadyear` enum('T','F') DEFAULT 'F',
  `graduated` enum('T','F') DEFAULT 'F',
  `pactive` enum('T','F') DEFAULT 'T'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_student`
--

INSERT INTO `users_student` (`_uid`, `reffNumb`, `registerno`, `branchid`, `admited`, `curriculum`, `course`, `program`, `section`, `session`, `intake`, `stdyear`, `religion`, `uhall`, `nationality`, `fnames`, `phone`, `email`, `birthDate`, `gender`, `inhostel`, `ishalted`, `deadyear`, `graduated`, `pactive`) VALUES
(1, '212-063012-04362', '212-063012-04362', 1, 3, 1, 1, 1, 1, 1, 1, '2', 24, 21, 28, 'Matovu Jahazi Kasim', '0701998877', 'matove@gmail.com', '2025-12-29 04:02:33', 'M', 'F', 'F', 'F', 'F', 'T');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_years_terms_release`
--
ALTER TABLE `academic_years_terms_release`
  ADD PRIMARY KEY (`_id`),
  ADD KEY `termid` (`termid`),
  ADD KEY `course` (`course`),
  ADD KEY `submitedby` (`submitedby`),
  ADD KEY `releasedby` (`releasedby`);

--
-- Indexes for table `academic_years`
--
ALTER TABLE `academic_years`
  ADD PRIMARY KEY (`_id`);

--
-- Indexes for table `academic_years_terms`
--
ALTER TABLE `academic_years_terms`
  ADD PRIMARY KEY (`_id`),
  ADD UNIQUE KEY `year_section_number` (`yearidn`,`intakeid`,`sectionid`,`secnnumber`),
  ADD KEY `intakeid` (`intakeid`),
  ADD KEY `sectionid` (`sectionid`);

--
-- Indexes for table `curriculums`
--
ALTER TABLE `curriculums`
  ADD PRIMARY KEY (`_id`),
  ADD KEY `systemid` (`systemid`);

--
-- Indexes for table `curriculum_settings`
--
ALTER TABLE `curriculum_settings`
  ADD PRIMARY KEY (`_id`),
  ADD KEY `curriculum` (`curriculum`),
  ADD KEY `programid` (`programid`),
  ADD KEY `courseid` (`courseid`),
  ADD KEY `cosunitid` (`cosunitid`),
  ADD KEY `sectionid` (`sectionid`);

--
-- Indexes for table `curriculum_settings_lecturer`
--
ALTER TABLE `curriculum_settings_lecturer`
  ADD PRIMARY KEY (`_id`),
  ADD UNIQUE KEY `cur_reg_lect` (`currsetting`,`academterm`,`lecturerid`),
  ADD KEY `academterm` (`academterm`),
  ADD KEY `lecturerid` (`lecturerid`);

--
-- Indexes for table `curriculum_settings_lecturer_assigned_supervision`
--
ALTER TABLE `curriculum_settings_lecturer_assigned_supervision`
  ADD PRIMARY KEY (`_id`),
  ADD UNIQUE KEY `cur_reg_lec_std` (`lecturer_assid`,`student_markid`),
  ADD KEY `student_markid` (`student_markid`);

--
-- Indexes for table `curriculum_settings_lecturer_content`
--
ALTER TABLE `curriculum_settings_lecturer_content`
  ADD PRIMARY KEY (`_id`),
  ADD KEY `lecturer_assid` (`lecturer_assid`);

--
-- Indexes for table `curriculum_settings_lecturer_session`
--
ALTER TABLE `curriculum_settings_lecturer_session`
  ADD PRIMARY KEY (`_id`),
  ADD UNIQUE KEY `cur_reg_lec_cwk` (`lecturer_assid`,`sessionidn`),
  ADD KEY `sessionidn` (`sessionidn`);

--
-- Indexes for table `curriculum_settings_lecturer_session_attendance`
--
ALTER TABLE `curriculum_settings_lecturer_session_attendance`
  ADD PRIMARY KEY (`_id`),
  ADD UNIQUE KEY `attendcode` (`attendcode`),
  ADD KEY `lect_session` (`lect_session`);

--
-- Indexes for table `curriculum_settings_lecturer_session_attendance_list`
--
ALTER TABLE `curriculum_settings_lecturer_session_attendance_list`
  ADD PRIMARY KEY (`_id`),
  ADD UNIQUE KEY `cur_reg_lec_attend_list` (`attendidn`,`markidnum`),
  ADD KEY `markidnum` (`markidnum`);

--
-- Indexes for table `curriculum_settings_lecturer_session_coursework`
--
ALTER TABLE `curriculum_settings_lecturer_session_coursework`
  ADD PRIMARY KEY (`_id`),
  ADD KEY `lect_session` (`lect_session`);

--
-- Indexes for table `curriculum_settings_lecturer_session_coursework_submit`
--
ALTER TABLE `curriculum_settings_lecturer_session_coursework_submit`
  ADD PRIMARY KEY (`_id`),
  ADD UNIQUE KEY `cur_reg_lec_cwk_sub` (`courseworkid`,`markidnum`),
  ADD KEY `markidnum` (`markidnum`);

--
-- Indexes for table `curriculum_settings_student`
--
ALTER TABLE `curriculum_settings_student`
  ADD PRIMARY KEY (`_id`),
  ADD UNIQUE KEY `std_term` (`studentidn`,`academterm`),
  ADD KEY `academterm` (`academterm`);

--
-- Indexes for table `curriculum_settings_student_marks`
--
ALTER TABLE `curriculum_settings_student_marks`
  ADD PRIMARY KEY (`_id`),
  ADD UNIQUE KEY `cur_reg` (`cursetting`,`registered`),
  ADD KEY `registered` (`registered`);

--
-- Indexes for table `curriculum_settings_timetable`
--
ALTER TABLE `curriculum_settings_timetable`
  ADD PRIMARY KEY (`_id`),
  ADD KEY `currsetting` (`currsetting`),
  ADD KEY `academterm` (`academterm`),
  ADD KEY `weekdayid` (`weekdayid`),
  ADD KEY `sessionid` (`sessionid`),
  ADD KEY `clasroomid` (`clasroomid`),
  ADD KEY `testroomid` (`testroomid`),
  ADD KEY `examroomid` (`examroomid`);

--
-- Indexes for table `financial_transactions`
--
ALTER TABLE `financial_transactions`
  ADD PRIMARY KEY (`_id`),
  ADD UNIQUE KEY `tran_internal_ref` (`tran_internal_ref`),
  ADD UNIQUE KEY `tran_external_ref` (`tran_external_ref`);

--
-- Indexes for table `financial_transactions_items`
--
ALTER TABLE `financial_transactions_items`
  ADD PRIMARY KEY (`_id`);

--
-- Indexes for table `registered_course`
--
ALTER TABLE `registered_course`
  ADD PRIMARY KEY (`_id`),
  ADD KEY `faculty` (`faculty`);

--
-- Indexes for table `registered_course_assignments`
--
ALTER TABLE `registered_course_assignments`
  ADD PRIMARY KEY (`_id`),
  ADD UNIQUE KEY `course` (`course`,`nametag`,`assignid`);

--
-- Indexes for table `registered_course_units`
--
ALTER TABLE `registered_course_units`
  ADD PRIMARY KEY (`_id`),
  ADD KEY `course` (`course`);

--
-- Indexes for table `registered_faculty`
--
ALTER TABLE `registered_faculty`
  ADD PRIMARY KEY (`_id`);

--
-- Indexes for table `setting_collections`
--
ALTER TABLE `setting_collections`
  ADD PRIMARY KEY (`_id`);

--
-- Indexes for table `setting_exams_system`
--
ALTER TABLE `setting_exams_system`
  ADD PRIMARY KEY (`sysid`),
  ADD UNIQUE KEY `sysname` (`sysname`);

--
-- Indexes for table `setting_exams_system_award`
--
ALTER TABLE `setting_exams_system_award`
  ADD PRIMARY KEY (`id`),
  ADD KEY `systemid` (`systemid`),
  ADD KEY `programid` (`programid`);

--
-- Indexes for table `setting_exams_system_grading`
--
ALTER TABLE `setting_exams_system_grading`
  ADD PRIMARY KEY (`id`),
  ADD KEY `systemid` (`systemid`);

--
-- Indexes for table `setting_intakes`
--
ALTER TABLE `setting_intakes`
  ADD PRIMARY KEY (`_id`);

--
-- Indexes for table `setting_programes`
--
ALTER TABLE `setting_programes`
  ADD PRIMARY KEY (`_id`);

--
-- Indexes for table `setting_programes_exams`
--
ALTER TABLE `setting_programes_exams`
  ADD PRIMARY KEY (`_id`),
  ADD KEY `program_exam_ibfk_1` (`programid`);

--
-- Indexes for table `setting_rooms`
--
ALTER TABLE `setting_rooms`
  ADD PRIMARY KEY (`_id`);

--
-- Indexes for table `setting_sections`
--
ALTER TABLE `setting_sections`
  ADD PRIMARY KEY (`_id`);

--
-- Indexes for table `setting_sessions`
--
ALTER TABLE `setting_sessions`
  ADD PRIMARY KEY (`_id`);

--
-- Indexes for table `system_branches`
--
ALTER TABLE `system_branches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `title` (`title`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `system_charges`
--
ALTER TABLE `system_charges`
  ADD PRIMARY KEY (`_hid`);

--
-- Indexes for table `system_credentials`
--
ALTER TABLE `system_credentials`
  ADD PRIMARY KEY (`_cid`),
  ADD UNIQUE KEY `reff_number` (`reff_number`);

--
-- Indexes for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`_id`),
  ADD KEY `loginid` (`loginid`),
  ADD KEY `uroleid` (`uroleid`);

--
-- Indexes for table `users_application`
--
ALTER TABLE `users_application`
  ADD PRIMARY KEY (`_aid`),
  ADD UNIQUE KEY `admissionno` (`admissionno`),
  ADD UNIQUE KEY `receiptnumb` (`receiptnumb`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `referee` (`referee`),
  ADD KEY `intake` (`intake`),
  ADD KEY `religion` (`religion`),
  ADD KEY `nationality` (`nationality`);

--
-- Indexes for table `users_application_courses`
--
ALTER TABLE `users_application_courses`
  ADD PRIMARY KEY (`_acid`),
  ADD KEY `course` (`course`),
  ADD KEY `program` (`program`),
  ADD KEY `section` (`section`),
  ADD KEY `session` (`session`);

--
-- Indexes for table `users_auth`
--
ALTER TABLE `users_auth`
  ADD PRIMARY KEY (`_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `userrole` (`userrole`);

--
-- Indexes for table `users_role`
--
ALTER TABLE `users_role`
  ADD PRIMARY KEY (`_rid`);

--
-- Indexes for table `users_staff`
--
ALTER TABLE `users_staff`
  ADD PRIMARY KEY (`_uid`),
  ADD UNIQUE KEY `registerno` (`registerno`),
  ADD KEY `admited` (`admited`),
  ADD KEY `faculty` (`faculty`),
  ADD KEY `religion` (`religion`),
  ADD KEY `nationality` (`nationality`);

--
-- Indexes for table `users_student`
--
ALTER TABLE `users_student`
  ADD PRIMARY KEY (`_uid`),
  ADD UNIQUE KEY `reffNumb` (`reffNumb`),
  ADD UNIQUE KEY `registerno` (`registerno`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `admited` (`admited`),
  ADD KEY `course` (`course`),
  ADD KEY `program` (`program`),
  ADD KEY `section` (`section`),
  ADD KEY `session` (`session`),
  ADD KEY `intake` (`intake`),
  ADD KEY `religion` (`religion`),
  ADD KEY `uhall` (`uhall`),
  ADD KEY `nationality` (`nationality`),
  ADD KEY `users_student_ibfk_10` (`curriculum`),
  ADD KEY `users_student_ibfk_11` (`branchid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_years_terms_release`
--
ALTER TABLE `academic_years_terms_release`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `academic_years`
--
ALTER TABLE `academic_years`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `academic_years_terms`
--
ALTER TABLE `academic_years_terms`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `curriculums`
--
ALTER TABLE `curriculums`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `curriculum_settings`
--
ALTER TABLE `curriculum_settings`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=626;

--
-- AUTO_INCREMENT for table `curriculum_settings_lecturer`
--
ALTER TABLE `curriculum_settings_lecturer`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `curriculum_settings_lecturer_assigned_supervision`
--
ALTER TABLE `curriculum_settings_lecturer_assigned_supervision`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `curriculum_settings_lecturer_content`
--
ALTER TABLE `curriculum_settings_lecturer_content`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `curriculum_settings_lecturer_session`
--
ALTER TABLE `curriculum_settings_lecturer_session`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `curriculum_settings_lecturer_session_attendance`
--
ALTER TABLE `curriculum_settings_lecturer_session_attendance`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `curriculum_settings_lecturer_session_attendance_list`
--
ALTER TABLE `curriculum_settings_lecturer_session_attendance_list`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `curriculum_settings_lecturer_session_coursework`
--
ALTER TABLE `curriculum_settings_lecturer_session_coursework`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `curriculum_settings_lecturer_session_coursework_submit`
--
ALTER TABLE `curriculum_settings_lecturer_session_coursework_submit`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `curriculum_settings_student`
--
ALTER TABLE `curriculum_settings_student`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `curriculum_settings_student_marks`
--
ALTER TABLE `curriculum_settings_student_marks`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `curriculum_settings_timetable`
--
ALTER TABLE `curriculum_settings_timetable`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `financial_transactions`
--
ALTER TABLE `financial_transactions`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `financial_transactions_items`
--
ALTER TABLE `financial_transactions_items`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `registered_course`
--
ALTER TABLE `registered_course`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `registered_course_assignments`
--
ALTER TABLE `registered_course_assignments`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `registered_course_units`
--
ALTER TABLE `registered_course_units`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=514;

--
-- AUTO_INCREMENT for table `registered_faculty`
--
ALTER TABLE `registered_faculty`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `setting_collections`
--
ALTER TABLE `setting_collections`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `setting_exams_system`
--
ALTER TABLE `setting_exams_system`
  MODIFY `sysid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `setting_exams_system_award`
--
ALTER TABLE `setting_exams_system_award`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `setting_exams_system_grading`
--
ALTER TABLE `setting_exams_system_grading`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `setting_intakes`
--
ALTER TABLE `setting_intakes`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `setting_programes`
--
ALTER TABLE `setting_programes`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `setting_programes_exams`
--
ALTER TABLE `setting_programes_exams`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `setting_rooms`
--
ALTER TABLE `setting_rooms`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `setting_sections`
--
ALTER TABLE `setting_sections`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `setting_sessions`
--
ALTER TABLE `setting_sessions`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `system_branches`
--
ALTER TABLE `system_branches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `system_charges`
--
ALTER TABLE `system_charges`
  MODIFY `_hid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `system_credentials`
--
ALTER TABLE `system_credentials`
  MODIFY `_cid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `users_application`
--
ALTER TABLE `users_application`
  MODIFY `_aid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_application_courses`
--
ALTER TABLE `users_application_courses`
  MODIFY `_acid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_auth`
--
ALTER TABLE `users_auth`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users_role`
--
ALTER TABLE `users_role`
  MODIFY `_rid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users_staff`
--
ALTER TABLE `users_staff`
  MODIFY `_uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users_student`
--
ALTER TABLE `users_student`
  MODIFY `_uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `academic_years_terms_release`
--
ALTER TABLE `academic_years_terms_release`
  ADD CONSTRAINT `academic_years_terms_release_ibfk_1` FOREIGN KEY (`termid`) REFERENCES `academic_years_terms` (`_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `academic_years_terms_release_ibfk_2` FOREIGN KEY (`course`) REFERENCES `registered_course` (`_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `academic_years_terms_release_ibfk_3` FOREIGN KEY (`submitedby`) REFERENCES `users_staff` (`_uid`) ON DELETE CASCADE,
  ADD CONSTRAINT `academic_years_terms_release_ibfk_4` FOREIGN KEY (`releasedby`) REFERENCES `users_staff` (`_uid`) ON DELETE CASCADE;

--
-- Constraints for table `academic_years_terms`
--
ALTER TABLE `academic_years_terms`
  ADD CONSTRAINT `academic_years_terms_ibfk_1` FOREIGN KEY (`yearidn`) REFERENCES `academic_years` (`_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `academic_years_terms_ibfk_2` FOREIGN KEY (`intakeid`) REFERENCES `setting_intakes` (`_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `academic_years_terms_ibfk_3` FOREIGN KEY (`sectionid`) REFERENCES `setting_sections` (`_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `curriculums`
--
ALTER TABLE `curriculums`
  ADD CONSTRAINT `curriculums_ibfk_1` FOREIGN KEY (`systemid`) REFERENCES `setting_exams_system` (`sysid`) ON DELETE CASCADE;

--
-- Constraints for table `curriculum_settings`
--
ALTER TABLE `curriculum_settings`
  ADD CONSTRAINT `curriculum_settings_ibfk_1` FOREIGN KEY (`curriculum`) REFERENCES `curriculums` (`_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `curriculum_settings_ibfk_2` FOREIGN KEY (`programid`) REFERENCES `setting_programes` (`_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `curriculum_settings_ibfk_3` FOREIGN KEY (`courseid`) REFERENCES `registered_course` (`_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `curriculum_settings_ibfk_4` FOREIGN KEY (`cosunitid`) REFERENCES `registered_course_units` (`_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `curriculum_settings_ibfk_5` FOREIGN KEY (`sectionid`) REFERENCES `setting_sections` (`_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `curriculum_settings_lecturer`
--
ALTER TABLE `curriculum_settings_lecturer`
  ADD CONSTRAINT `curriculum_settings_lecturer_ibfk_1` FOREIGN KEY (`currsetting`) REFERENCES `curriculum_settings` (`_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `curriculum_settings_lecturer_ibfk_2` FOREIGN KEY (`academterm`) REFERENCES `academic_years_terms` (`_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `curriculum_settings_lecturer_ibfk_3` FOREIGN KEY (`lecturerid`) REFERENCES `users_staff` (`_uid`) ON DELETE CASCADE;

--
-- Constraints for table `curriculum_settings_lecturer_assigned_supervision`
--
ALTER TABLE `curriculum_settings_lecturer_assigned_supervision`
  ADD CONSTRAINT `curriculum_settings_lecturer_assigned_supervision_ibfk_1` FOREIGN KEY (`lecturer_assid`) REFERENCES `curriculum_settings_lecturer` (`_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `curriculum_settings_lecturer_assigned_supervision_ibfk_2` FOREIGN KEY (`student_markid`) REFERENCES `curriculum_settings_student_marks` (`_id`) ON DELETE CASCADE;

--
-- Constraints for table `curriculum_settings_lecturer_content`
--
ALTER TABLE `curriculum_settings_lecturer_content`
  ADD CONSTRAINT `curriculum_settings_lecturer_content_ibfk_1` FOREIGN KEY (`lecturer_assid`) REFERENCES `curriculum_settings_lecturer` (`_id`) ON DELETE CASCADE;

--
-- Constraints for table `curriculum_settings_lecturer_session`
--
ALTER TABLE `curriculum_settings_lecturer_session`
  ADD CONSTRAINT `curriculum_settings_lecturer_session_ibfk_1` FOREIGN KEY (`lecturer_assid`) REFERENCES `curriculum_settings_lecturer` (`_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `curriculum_settings_lecturer_session_ibfk_2` FOREIGN KEY (`sessionidn`) REFERENCES `setting_sessions` (`_id`) ON DELETE CASCADE;

--
-- Constraints for table `curriculum_settings_lecturer_session_attendance`
--
ALTER TABLE `curriculum_settings_lecturer_session_attendance`
  ADD CONSTRAINT `curriculum_settings_lecturer_session_attendance_ibfk_1` FOREIGN KEY (`lect_session`) REFERENCES `curriculum_settings_lecturer_session` (`_id`) ON DELETE CASCADE;

--
-- Constraints for table `curriculum_settings_lecturer_session_attendance_list`
--
ALTER TABLE `curriculum_settings_lecturer_session_attendance_list`
  ADD CONSTRAINT `curriculum_settings_lecturer_session_attendance_list_ibfk_1` FOREIGN KEY (`attendidn`) REFERENCES `curriculum_settings_lecturer_session_attendance` (`_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `curriculum_settings_lecturer_session_attendance_list_ibfk_2` FOREIGN KEY (`markidnum`) REFERENCES `curriculum_settings_student_marks` (`_id`) ON DELETE CASCADE;

--
-- Constraints for table `curriculum_settings_lecturer_session_coursework`
--
ALTER TABLE `curriculum_settings_lecturer_session_coursework`
  ADD CONSTRAINT `curriculum_settings_lecturer_session_coursework_ibfk_1` FOREIGN KEY (`lect_session`) REFERENCES `curriculum_settings_lecturer_session` (`_id`) ON DELETE CASCADE;

--
-- Constraints for table `curriculum_settings_lecturer_session_coursework_submit`
--
ALTER TABLE `curriculum_settings_lecturer_session_coursework_submit`
  ADD CONSTRAINT `curriculum_settings_lecturer_session_coursework_submit_ibfk_1` FOREIGN KEY (`courseworkid`) REFERENCES `curriculum_settings_lecturer_session_coursework` (`_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `curriculum_settings_lecturer_session_coursework_submit_ibfk_2` FOREIGN KEY (`markidnum`) REFERENCES `curriculum_settings_student_marks` (`_id`) ON DELETE CASCADE;

--
-- Constraints for table `curriculum_settings_student`
--
ALTER TABLE `curriculum_settings_student`
  ADD CONSTRAINT `curriculum_settings_student_ibfk_1` FOREIGN KEY (`studentidn`) REFERENCES `users_student` (`_uid`) ON DELETE CASCADE,
  ADD CONSTRAINT `curriculum_settings_student_ibfk_2` FOREIGN KEY (`academterm`) REFERENCES `academic_years_terms` (`_id`) ON DELETE CASCADE;

--
-- Constraints for table `curriculum_settings_student_marks`
--
ALTER TABLE `curriculum_settings_student_marks`
  ADD CONSTRAINT `curriculum_settings_student_marks_ibfk_1` FOREIGN KEY (`cursetting`) REFERENCES `curriculum_settings` (`_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `curriculum_settings_student_marks_ibfk_2` FOREIGN KEY (`registered`) REFERENCES `curriculum_settings_student` (`_id`) ON DELETE CASCADE;

--
-- Constraints for table `curriculum_settings_timetable`
--
ALTER TABLE `curriculum_settings_timetable`
  ADD CONSTRAINT `curriculum_settings_timetable_ibfk_1` FOREIGN KEY (`currsetting`) REFERENCES `curriculum_settings` (`_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `curriculum_settings_timetable_ibfk_2` FOREIGN KEY (`academterm`) REFERENCES `academic_years_terms` (`_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `curriculum_settings_timetable_ibfk_3` FOREIGN KEY (`weekdayid`) REFERENCES `setting_collections` (`_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `curriculum_settings_timetable_ibfk_4` FOREIGN KEY (`sessionid`) REFERENCES `setting_sessions` (`_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `curriculum_settings_timetable_ibfk_5` FOREIGN KEY (`clasroomid`) REFERENCES `setting_rooms` (`_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `curriculum_settings_timetable_ibfk_6` FOREIGN KEY (`testroomid`) REFERENCES `setting_rooms` (`_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `curriculum_settings_timetable_ibfk_7` FOREIGN KEY (`examroomid`) REFERENCES `setting_rooms` (`_id`) ON DELETE CASCADE;

--
-- Constraints for table `registered_course`
--
ALTER TABLE `registered_course`
  ADD CONSTRAINT `registered_course_ibfk_1` FOREIGN KEY (`faculty`) REFERENCES `registered_faculty` (`_id`) ON DELETE CASCADE;

--
-- Constraints for table `registered_course_assignments`
--
ALTER TABLE `registered_course_assignments`
  ADD CONSTRAINT `registered_course_assignments_ibfk_1` FOREIGN KEY (`course`) REFERENCES `registered_course` (`_id`) ON DELETE CASCADE;

--
-- Constraints for table `registered_course_units`
--
ALTER TABLE `registered_course_units`
  ADD CONSTRAINT `registered_course_units_ibfk_1` FOREIGN KEY (`course`) REFERENCES `registered_course` (`_id`) ON DELETE CASCADE;

--
-- Constraints for table `setting_exams_system_award`
--
ALTER TABLE `setting_exams_system_award`
  ADD CONSTRAINT `setting_exams_system_award_ibfk_1` FOREIGN KEY (`systemid`) REFERENCES `setting_exams_system` (`sysid`) ON DELETE CASCADE,
  ADD CONSTRAINT `setting_exams_system_award_ibfk_2` FOREIGN KEY (`programid`) REFERENCES `setting_programes` (`_id`) ON DELETE CASCADE;

--
-- Constraints for table `setting_exams_system_grading`
--
ALTER TABLE `setting_exams_system_grading`
  ADD CONSTRAINT `setting_exams_system_grading_ibfk_1` FOREIGN KEY (`systemid`) REFERENCES `setting_exams_system` (`sysid`) ON DELETE CASCADE;

--
-- Constraints for table `setting_programes_exams`
--
ALTER TABLE `setting_programes_exams`
  ADD CONSTRAINT `program_exam_ibfk_1` FOREIGN KEY (`programid`) REFERENCES `setting_programes` (`_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD CONSTRAINT `system_logs_ibfk_1` FOREIGN KEY (`loginid`) REFERENCES `users_auth` (`_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `system_logs_ibfk_2` FOREIGN KEY (`uroleid`) REFERENCES `users_role` (`_rid`) ON DELETE CASCADE;

--
-- Constraints for table `users_application`
--
ALTER TABLE `users_application`
  ADD CONSTRAINT `users_application_ibfk_1` FOREIGN KEY (`referee`) REFERENCES `users_student` (`_uid`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_application_ibfk_2` FOREIGN KEY (`intake`) REFERENCES `setting_intakes` (`_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_application_ibfk_3` FOREIGN KEY (`religion`) REFERENCES `setting_collections` (`_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_application_ibfk_4` FOREIGN KEY (`nationality`) REFERENCES `setting_collections` (`_id`) ON DELETE CASCADE;

--
-- Constraints for table `users_application_courses`
--
ALTER TABLE `users_application_courses`
  ADD CONSTRAINT `users_application_courses_ibfk_1` FOREIGN KEY (`course`) REFERENCES `registered_course` (`_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_application_courses_ibfk_2` FOREIGN KEY (`program`) REFERENCES `setting_programes` (`_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_application_courses_ibfk_3` FOREIGN KEY (`section`) REFERENCES `setting_sections` (`_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_application_courses_ibfk_4` FOREIGN KEY (`session`) REFERENCES `setting_sessions` (`_id`) ON DELETE CASCADE;

--
-- Constraints for table `users_auth`
--
ALTER TABLE `users_auth`
  ADD CONSTRAINT `users_auth_ibfk_1` FOREIGN KEY (`userrole`) REFERENCES `users_role` (`_rid`) ON DELETE CASCADE;

--
-- Constraints for table `users_staff`
--
ALTER TABLE `users_staff`
  ADD CONSTRAINT `users_staff_ibfk_1` FOREIGN KEY (`admited`) REFERENCES `academic_years` (`_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_staff_ibfk_2` FOREIGN KEY (`faculty`) REFERENCES `registered_faculty` (`_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_staff_ibfk_3` FOREIGN KEY (`religion`) REFERENCES `setting_collections` (`_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_staff_ibfk_4` FOREIGN KEY (`nationality`) REFERENCES `setting_collections` (`_id`) ON DELETE CASCADE;

--
-- Constraints for table `users_student`
--
ALTER TABLE `users_student`
  ADD CONSTRAINT `users_student_ibfk_1` FOREIGN KEY (`admited`) REFERENCES `academic_years` (`_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_student_ibfk_10` FOREIGN KEY (`curriculum`) REFERENCES `curriculums` (`_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_student_ibfk_11` FOREIGN KEY (`branchid`) REFERENCES `system_branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_student_ibfk_2` FOREIGN KEY (`course`) REFERENCES `registered_course` (`_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_student_ibfk_3` FOREIGN KEY (`program`) REFERENCES `setting_programes` (`_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_student_ibfk_4` FOREIGN KEY (`section`) REFERENCES `setting_sections` (`_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_student_ibfk_5` FOREIGN KEY (`session`) REFERENCES `setting_sessions` (`_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_student_ibfk_6` FOREIGN KEY (`intake`) REFERENCES `setting_intakes` (`_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_student_ibfk_7` FOREIGN KEY (`religion`) REFERENCES `setting_collections` (`_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_student_ibfk_8` FOREIGN KEY (`uhall`) REFERENCES `setting_collections` (`_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_student_ibfk_9` FOREIGN KEY (`nationality`) REFERENCES `setting_collections` (`_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
