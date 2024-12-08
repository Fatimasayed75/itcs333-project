-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 08, 2024 at 10:40 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+03:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rbs`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `bookingID` int(10) NOT NULL,
  `userID` int(10) NOT NULL,
  `roomID` varchar(10) NOT NULL,
  `bookingTime` timestamp NOT NULL DEFAULT current_timestamp(),
  `startTime` datetime NOT NULL,
  `endTime` datetime NOT NULL,
  `status` enum('active','pending','expired','rejected') NOT NULL DEFAULT 'active',
  `feedback` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `commentID` int(10) NOT NULL,
  `userID` int(10) NOT NULL,
  `roomID` varchar(10) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `content` text DEFAULT NULL,
  `isRead` int(11) DEFAULT 0,
  `bookingID` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comment_reply`
--

CREATE TABLE `comment_reply` (
  `replyID` int(10) NOT NULL,
  `commentID` int(10) NOT NULL,
  `userID` int(10) NOT NULL,
  `replyContent` text NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `equipmentID` int(11) NOT NULL,
  `equipmentName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`equipmentID`, `equipmentName`) VALUES
(1, 'Studying Chair'),
(2, 'Doctor Table'),
(3, 'Doctor PC'),
(4, 'Student PC'),
(5, 'Board'),
(6, 'Data Show Projector'),
(7, 'PC Chair'),
(8, 'Doctor Chair');

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `roomID` varchar(10) NOT NULL,
  `type` enum('lab','class') DEFAULT 'class',
  `capacity` int(10) UNSIGNED NOT NULL,
  `isAvailable` tinyint(4) NOT NULL DEFAULT 0,
  `floor` tinyint(4) NOT NULL,
  `department` enum('IS','CS','CE') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`roomID`, `type`, `capacity`, `isAvailable`, `floor`, `department`) VALUES
('S40-021', 'lab', 30, 1, 0, 'IS'),
('S40-023', 'lab', 30, 1, 0, 'IS'),
('S40-028', 'class', 30, 1, 0, 'IS'),
('S40-029', 'class', 30, 1, 0, 'IS'),
('S40-030', 'lab', 30, 1, 0, 'IS'),
('S40-032', 'class', 30, 1, 0, 'IS'),
('S40-049', 'class', 30, 1, 0, 'CS'),
('S40-051', 'lab', 30, 1, 0, 'CS'),
('S40-056', 'class', 30, 1, 0, 'CS'),
('S40-057', 'class', 30, 1, 0, 'CS'),
('S40-058', 'lab', 30, 1, 0, 'CS'),
('S40-060', 'class', 30, 1, 0, 'CS'),
('S40-077', 'class', 30, 1, 0, 'CE'),
('S40-079', 'lab', 30, 1, 0, 'CE'),
('S40-084', 'class', 30, 1, 0, 'CE'),
('S40-085', 'class', 30, 1, 0, 'CE'),
('S40-086', 'lab', 30, 1, 0, 'CE'),
('S40-088', 'class', 30, 1, 0, 'CE'),
('S40-1002', 'lab', 100, 0, 1, 'IS'),
('S40-1006', 'lab', 30, 1, 1, 'IS'),
('S40-1008', 'lab', 30, 1, 1, 'IS'),
('S40-1009', 'lab', 30, 1, 1, 'IS'),
('S40-1010', 'lab', 30, 1, 1, 'IS'),
('S40-1012', 'lab', 30, 1, 1, 'IS'),
('S40-1014', 'lab', 30, 1, 1, 'IS'),
('S40-1043', 'lab', 30, 1, 1, 'CS'),
('S40-1045', 'lab', 30, 1, 1, 'CS'),
('S40-1047', 'class', 30, 1, 1, 'CS'),
('S40-1048', 'class', 30, 1, 1, 'CS'),
('S40-1050', 'lab', 30, 1, 1, 'CS'),
('S40-1052', 'lab', 30, 1, 1, 'CS'),
('S40-1081', 'lab', 30, 1, 1, 'CE'),
('S40-1083', 'lab', 30, 1, 1, 'CE'),
('S40-1085', 'class', 30, 1, 1, 'CE'),
('S40-1086', 'class', 30, 1, 1, 'CE'),
('S40-1087', 'lab', 30, 1, 1, 'CE'),
('S40-1089', 'lab', 30, 1, 1, 'CE'),
('S40-2001', 'lab', 100, 0, 2, 'IS'),
('S40-2005', 'lab', 30, 1, 2, 'IS'),
('S40-2007', 'lab', 30, 1, 2, 'IS'),
('S40-2008', 'class', 30, 1, 2, 'IS'),
('S40-2010', 'class', 30, 1, 2, 'IS'),
('S40-2011', 'class', 30, 1, 2, 'IS'),
('S40-2012', 'class', 30, 1, 2, 'IS'),
('S40-2013', 'lab', 30, 1, 2, 'IS'),
('S40-2015', 'lab', 30, 1, 2, 'IS'),
('S40-2043', 'lab', 30, 1, 2, 'CS'),
('S40-2045', 'lab', 30, 1, 2, 'CS'),
('S40-2046', 'class', 30, 1, 2, 'CS'),
('S40-2048', 'class', 30, 1, 2, 'CS'),
('S40-2049', 'class', 30, 1, 2, 'CS'),
('S40-2050', 'class', 30, 1, 2, 'CS'),
('S40-2051', 'lab', 30, 1, 2, 'CS'),
('S40-2053', 'lab', 30, 1, 2, 'CS'),
('S40-2081', 'lab', 30, 1, 2, 'CE'),
('S40-2083', 'lab', 30, 1, 2, 'CE'),
('S40-2084', 'class', 30, 1, 2, 'CE'),
('S40-2086', 'class', 30, 1, 2, 'CE'),
('S40-2087', 'class', 30, 1, 2, 'CE'),
('S40-2088', 'class', 30, 1, 2, 'CE'),
('S40-2089', 'lab', 30, 1, 2, 'CE'),
('S40-2091', 'lab', 30, 1, 2, 'CE');

-- --------------------------------------------------------

--
-- Table structure for table `roomequipments`
--

CREATE TABLE `roomequipments` (
  `roomID` varchar(10) NOT NULL,
  `equipmentID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roomequipments`
--

INSERT INTO `roomequipments` (`roomID`, `equipmentID`, `Quantity`) VALUES
('S40-021', 2, 1),
('S40-021', 3, 1),
('S40-021', 4, 30),
('S40-021', 5, 1),
('S40-021', 6, 1),
('S40-021', 7, 30),
('S40-021', 8, 1),
('S40-023', 2, 1),
('S40-023', 3, 1),
('S40-023', 4, 30),
('S40-023', 5, 1),
('S40-023', 6, 1),
('S40-023', 7, 30),
('S40-023', 8, 1),
('S40-028', 1, 30),
('S40-028', 2, 1),
('S40-028', 3, 1),
('S40-028', 5, 1),
('S40-028', 6, 1),
('S40-028', 8, 1),
('S40-029', 1, 30),
('S40-029', 2, 1),
('S40-029', 3, 1),
('S40-029', 5, 1),
('S40-029', 6, 1),
('S40-029', 8, 1),
('S40-030', 2, 1),
('S40-030', 3, 1),
('S40-030', 4, 30),
('S40-030', 5, 1),
('S40-030', 6, 1),
('S40-030', 7, 30),
('S40-030', 8, 1),
('S40-032', 1, 30),
('S40-032', 2, 1),
('S40-032', 3, 1),
('S40-032', 5, 1),
('S40-032', 6, 1),
('S40-032', 8, 1),
('S40-049', 1, 30),
('S40-049', 2, 1),
('S40-049', 3, 1),
('S40-049', 5, 1),
('S40-049', 6, 1),
('S40-049', 8, 1),
('S40-051', 2, 1),
('S40-051', 3, 1),
('S40-051', 4, 30),
('S40-051', 5, 1),
('S40-051', 6, 1),
('S40-051', 7, 0),
('S40-051', 8, 1),
('S40-056', 1, 30),
('S40-056', 2, 1),
('S40-056', 3, 1),
('S40-056', 5, 1),
('S40-056', 6, 1),
('S40-056', 8, 1),
('S40-057', 1, 30),
('S40-057', 2, 1),
('S40-057', 3, 1),
('S40-057', 5, 1),
('S40-057', 6, 1),
('S40-057', 8, 1),
('S40-058', 2, 1),
('S40-058', 3, 1),
('S40-058', 4, 30),
('S40-058', 5, 1),
('S40-058', 6, 1),
('S40-058', 7, 30),
('S40-058', 8, 1),
('S40-060', 1, 30),
('S40-060', 2, 1),
('S40-060', 3, 1),
('S40-060', 5, 1),
('S40-060', 6, 1),
('S40-060', 8, 1),
('S40-077', 1, 30),
('S40-077', 2, 1),
('S40-077', 3, 1),
('S40-077', 5, 1),
('S40-077', 6, 1),
('S40-077', 8, 1),
('S40-079', 2, 1),
('S40-079', 3, 1),
('S40-079', 4, 30),
('S40-079', 5, 1),
('S40-079', 6, 1),
('S40-079', 7, 30),
('S40-079', 8, 1),
('S40-084', 1, 30),
('S40-084', 2, 1),
('S40-084', 3, 1),
('S40-084', 5, 1),
('S40-084', 6, 1),
('S40-084', 8, 1),
('S40-085', 1, 30),
('S40-085', 2, 1),
('S40-085', 3, 1),
('S40-085', 5, 1),
('S40-085', 6, 1),
('S40-085', 8, 1),
('S40-086', 2, 1),
('S40-086', 3, 1),
('S40-086', 4, 30),
('S40-086', 5, 1),
('S40-086', 6, 1),
('S40-086', 7, 30),
('S40-086', 8, 1),
('S40-088', 1, 30),
('S40-088', 2, 1),
('S40-088', 3, 1),
('S40-088', 5, 1),
('S40-088', 6, 1),
('S40-088', 8, 1),
('S40-1002', 3, 2),
('S40-1002', 4, 50),
('S40-1002', 5, 2),
('S40-1002', 6, 2),
('S40-1002', 7, 100),
('S40-1006', 2, 1),
('S40-1006', 3, 1),
('S40-1006', 4, 30),
('S40-1006', 5, 1),
('S40-1006', 6, 1),
('S40-1006', 7, 30),
('S40-1006', 8, 1),
('S40-1008', 2, 1),
('S40-1008', 3, 1),
('S40-1008', 4, 30),
('S40-1008', 5, 1),
('S40-1008', 6, 1),
('S40-1008', 7, 30),
('S40-1008', 8, 1),
('S40-1009', 2, 1),
('S40-1009', 3, 1),
('S40-1009', 4, 30),
('S40-1009', 5, 1),
('S40-1009', 6, 1),
('S40-1009', 7, 30),
('S40-1009', 8, 1),
('S40-1010', 2, 1),
('S40-1010', 3, 1),
('S40-1010', 4, 30),
('S40-1010', 5, 1),
('S40-1010', 6, 1),
('S40-1010', 7, 30),
('S40-1010', 8, 1),
('S40-1012', 2, 1),
('S40-1012', 3, 1),
('S40-1012', 4, 30),
('S40-1012', 5, 1),
('S40-1012', 6, 1),
('S40-1012', 7, 30),
('S40-1012', 8, 1),
('S40-1014', 2, 1),
('S40-1014', 3, 1),
('S40-1014', 4, 30),
('S40-1014', 5, 1),
('S40-1014', 6, 1),
('S40-1014', 7, 30),
('S40-1014', 8, 1),
('S40-1043', 2, 1),
('S40-1043', 3, 1),
('S40-1043', 4, 30),
('S40-1043', 5, 1),
('S40-1043', 6, 1),
('S40-1043', 7, 30),
('S40-1043', 8, 1),
('S40-1045', 2, 1),
('S40-1045', 3, 1),
('S40-1045', 4, 30),
('S40-1045', 5, 1),
('S40-1045', 6, 1),
('S40-1045', 7, 30),
('S40-1045', 8, 1),
('S40-1047', 1, 30),
('S40-1047', 2, 1),
('S40-1047', 3, 1),
('S40-1047', 5, 1),
('S40-1047', 6, 1),
('S40-1047', 8, 1),
('S40-1048', 1, 30),
('S40-1048', 2, 1),
('S40-1048', 3, 1),
('S40-1048', 5, 1),
('S40-1048', 6, 1),
('S40-1048', 8, 1),
('S40-1050', 2, 1),
('S40-1050', 3, 1),
('S40-1050', 4, 30),
('S40-1050', 5, 1),
('S40-1050', 6, 1),
('S40-1050', 7, 30),
('S40-1050', 8, 1),
('S40-1052', 2, 1),
('S40-1052', 3, 1),
('S40-1052', 4, 30),
('S40-1052', 5, 1),
('S40-1052', 6, 1),
('S40-1052', 7, 30),
('S40-1052', 8, 1),
('S40-1081', 2, 1),
('S40-1081', 3, 1),
('S40-1081', 4, 30),
('S40-1081', 5, 1),
('S40-1081', 6, 1),
('S40-1081', 7, 30),
('S40-1081', 8, 1),
('S40-1083', 2, 1),
('S40-1083', 3, 1),
('S40-1083', 4, 30),
('S40-1083', 5, 1),
('S40-1083', 6, 1),
('S40-1083', 7, 30),
('S40-1083', 8, 1),
('S40-1085', 1, 30),
('S40-1085', 2, 1),
('S40-1085', 3, 1),
('S40-1085', 5, 1),
('S40-1085', 6, 1),
('S40-1085', 8, 1),
('S40-1086', 1, 30),
('S40-1086', 2, 1),
('S40-1086', 3, 1),
('S40-1086', 5, 1),
('S40-1086', 6, 1),
('S40-1086', 8, 1),
('S40-1087', 2, 1),
('S40-1087', 3, 1),
('S40-1087', 4, 30),
('S40-1087', 5, 1),
('S40-1087', 6, 1),
('S40-1087', 7, 30),
('S40-1087', 8, 1),
('S40-1089', 2, 1),
('S40-1089', 3, 1),
('S40-1089', 4, 30),
('S40-1089', 5, 1),
('S40-1089', 6, 1),
('S40-1089', 7, 30),
('S40-1089', 8, 1),
('S40-2001', 3, 2),
('S40-2001', 4, 50),
('S40-2001', 5, 2),
('S40-2001', 6, 2),
('S40-2001', 7, 100),
('S40-2005', 2, 1),
('S40-2005', 3, 1),
('S40-2005', 4, 30),
('S40-2005', 5, 1),
('S40-2005', 6, 1),
('S40-2005', 7, 30),
('S40-2005', 8, 1),
('S40-2007', 2, 1),
('S40-2007', 3, 1),
('S40-2007', 4, 30),
('S40-2007', 5, 1),
('S40-2007', 6, 1),
('S40-2007', 7, 30),
('S40-2007', 8, 1),
('S40-2008', 1, 30),
('S40-2008', 2, 1),
('S40-2008', 3, 1),
('S40-2008', 5, 1),
('S40-2008', 6, 1),
('S40-2008', 8, 1),
('S40-2010', 1, 30),
('S40-2010', 2, 1),
('S40-2010', 3, 1),
('S40-2010', 5, 1),
('S40-2010', 6, 1),
('S40-2010', 8, 1),
('S40-2011', 1, 30),
('S40-2011', 2, 1),
('S40-2011', 3, 1),
('S40-2011', 5, 1),
('S40-2011', 6, 1),
('S40-2011', 8, 1),
('S40-2012', 1, 30),
('S40-2012', 2, 1),
('S40-2012', 3, 1),
('S40-2012', 5, 1),
('S40-2012', 6, 1),
('S40-2012', 8, 1),
('S40-2013', 2, 1),
('S40-2013', 3, 1),
('S40-2013', 4, 30),
('S40-2013', 5, 1),
('S40-2013', 6, 1),
('S40-2013', 7, 30),
('S40-2013', 8, 1),
('S40-2015', 2, 1),
('S40-2015', 3, 1),
('S40-2015', 4, 30),
('S40-2015', 5, 1),
('S40-2015', 6, 1),
('S40-2015', 7, 30),
('S40-2015', 8, 1),
('S40-2043', 2, 1),
('S40-2043', 3, 1),
('S40-2043', 4, 30),
('S40-2043', 5, 1),
('S40-2043', 6, 1),
('S40-2043', 7, 30),
('S40-2043', 8, 1),
('S40-2045', 2, 1),
('S40-2045', 3, 1),
('S40-2045', 4, 30),
('S40-2045', 5, 1),
('S40-2045', 6, 1),
('S40-2045', 7, 30),
('S40-2045', 8, 1),
('S40-2046', 1, 30),
('S40-2046', 2, 1),
('S40-2046', 3, 1),
('S40-2046', 5, 1),
('S40-2046', 6, 1),
('S40-2046', 8, 1),
('S40-2048', 1, 30),
('S40-2048', 2, 1),
('S40-2048', 3, 1),
('S40-2048', 5, 1),
('S40-2048', 6, 1),
('S40-2048', 8, 1),
('S40-2049', 1, 30),
('S40-2049', 2, 1),
('S40-2049', 3, 1),
('S40-2049', 5, 1),
('S40-2049', 6, 1),
('S40-2049', 8, 1),
('S40-2050', 1, 30),
('S40-2050', 2, 1),
('S40-2050', 3, 1),
('S40-2050', 5, 1),
('S40-2050', 6, 1),
('S40-2050', 8, 1),
('S40-2051', 2, 1),
('S40-2051', 3, 1),
('S40-2051', 4, 30),
('S40-2051', 5, 1),
('S40-2051', 6, 1),
('S40-2051', 7, 30),
('S40-2051', 8, 1),
('S40-2053', 2, 1),
('S40-2053', 3, 1),
('S40-2053', 4, 30),
('S40-2053', 5, 1),
('S40-2053', 6, 1),
('S40-2053', 7, 30),
('S40-2053', 8, 1),
('S40-2081', 2, 1),
('S40-2081', 3, 1),
('S40-2081', 4, 30),
('S40-2081', 5, 1),
('S40-2081', 6, 1),
('S40-2081', 7, 30),
('S40-2081', 8, 1),
('S40-2083', 2, 1),
('S40-2083', 3, 1),
('S40-2083', 4, 30),
('S40-2083', 5, 1),
('S40-2083', 6, 1),
('S40-2083', 7, 30),
('S40-2083', 8, 1),
('S40-2084', 1, 30),
('S40-2084', 2, 1),
('S40-2084', 3, 1),
('S40-2084', 5, 1),
('S40-2084', 6, 1),
('S40-2084', 8, 1),
('S40-2086', 1, 30),
('S40-2086', 2, 1),
('S40-2086', 3, 1),
('S40-2086', 5, 1),
('S40-2086', 6, 1),
('S40-2086', 8, 1),
('S40-2087', 1, 30),
('S40-2087', 2, 1),
('S40-2087', 3, 1),
('S40-2087', 5, 1),
('S40-2087', 6, 1),
('S40-2087', 8, 1),
('S40-2088', 1, 30),
('S40-2088', 2, 1),
('S40-2088', 3, 1),
('S40-2088', 5, 1),
('S40-2088', 6, 1),
('S40-2088', 8, 1),
('S40-2089', 2, 1),
('S40-2089', 3, 1),
('S40-2089', 4, 30),
('S40-2089', 5, 1),
('S40-2089', 6, 1),
('S40-2089', 7, 30),
('S40-2089', 8, 1),
('S40-2091', 2, 1),
('S40-2091', 3, 1),
('S40-2091', 4, 30),
('S40-2091', 5, 1),
('S40-2091', 6, 1),
('S40-2091', 7, 30),
('S40-2091', 8, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userID` int(10) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `firstName` text NOT NULL,
  `lastName` text NOT NULL,
  `role` enum('admin','student','instructor') NOT NULL DEFAULT 'student',
  `profilePic` blob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `email`, `password`, `firstName`, `lastName`, `role`, `profilePic`) VALUES
(57, '201245123@stu.uob.edu.bh', '$2y$10$qKdWaSKr77cZP9TFpeqMHeMYUGOOOdm35GX9sLbGuFdTaeBnERtv6', 'Test', 'Test', 'student', 0x64656661756c742e6a7067),
(58, '202211111@stu.uob.edu.bh', '$2y$10$AFRdEi2pWCGoqG7vjJOcgOpA1qwTiZJ6RQhHq9bebfekk2LSiy/r2', 'first', 'last', 'student', 0x64656661756c742e6a7067),
(62, 'admin@uob.edu.bh', '$2y$10$oTI5fQMqPQTaaca6MkZfzueNjAfKjL5N9Qep/xBLUfRS2khmpYTGW', 'Admin1', 'Admin1', 'admin', 0x64656661756c742e6a7067),
(63, 'instructor@uob.edu.bh', '$2y$10$HsL1UloZGJGB37IpX9xm3.a0Ooil20eVzuuEIhC.DyFV7mfu144WO', 'Instructor1', 'Instructor1', 'instructor', 0x64656661756c742e6a7067),
(64, '201745123@stu.uob.edu.bh', '$2y$10$LUg3JSU1vLY.Qx4L2QrdmuKvUY43m8nf4Ofm7quMIU92F8MoXsKGq', 'E', 'E', 'student', 0x64656661756c742e6a7067),
(65, '202145123@stu.uob.edu.bh', '$2y$10$tM7km2EmbmAqUepEWvjpUuuBfttxCr.48nHa5kMn9XpjTqM9DqU8i', 'D', 'D', 'student', 0x64656661756c742e6a7067),
(66, '202233333@stu.uob.edu.bh', '$2y$10$nxzMW.ww0rC2oL9rmLXBoOuhfyY0IWSAJVQcO8AOGTT0X/5uDtxVq', 'R', 'R', 'student', 0x64656661756c742e6a7067),
(67, '202012333@stu.uob.edu.bh', '$2y$10$FZaaYngQ1c7MLohQZY7.Nu1Uc28c/CVzUzgKx7aoMw2KLjkOAS/qK', 'Q', 'Q', 'student', 0x64656661756c742e6a7067),
(69, '202207777@stu.uob.edu.bh', '$2y$10$6xHbqYCmxNdp3Q.TTZUFMOcSVbwp2Iy3yx/5r3/0.8CrdVqy0cTfG', 'Fatima', 'Sayed', 'student', 0x64656661756c742e6a7067);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`bookingID`),
  ADD KEY `fk_bookings_userID` (`userID`),
  ADD KEY `fk_bookings_roomID` (`roomID`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`commentID`),
  ADD KEY `fk_comments_userID` (`userID`),
  ADD KEY `fk_comments_roomID` (`roomID`);

--
-- Indexes for table `comment_reply`
--
ALTER TABLE `comment_reply`
  ADD PRIMARY KEY (`replyID`),
  ADD KEY `fk_comment_reply_commentID` (`commentID`),
  ADD KEY `fk_comment_reply_userID` (`userID`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`equipmentID`,`equipmentName`);

--
-- Indexes for table `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`roomID`);

--
-- Indexes for table `roomequipments`
--
ALTER TABLE `roomequipments`
  ADD PRIMARY KEY (`roomID`,`equipmentID`),
  ADD KEY `RoomID` (`roomID`),
  ADD KEY `EquipmentID` (`equipmentID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `bookingID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `commentID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `comment_reply`
--
ALTER TABLE `comment_reply`
  MODIFY `replyID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `equipmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_bookings_roomID` FOREIGN KEY (`roomID`) REFERENCES `room` (`roomID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bookings_userID` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `fk_comments_roomID` FOREIGN KEY (`roomID`) REFERENCES `room` (`roomID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_comments_userID` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `comment_reply`
--
ALTER TABLE `comment_reply`
  ADD CONSTRAINT `fk_comment_reply_commentID` FOREIGN KEY (`commentID`) REFERENCES `comments` (`commentID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_comment_reply_userID` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `roomequipments`
--
ALTER TABLE `roomequipments`
  ADD CONSTRAINT `roomEquipments_equipmentID_fk` FOREIGN KEY (`equipmentID`) REFERENCES `equipment` (`equipmentID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `roomEquipments_roomID_fk` FOREIGN KEY (`roomID`) REFERENCES `room` (`roomID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
