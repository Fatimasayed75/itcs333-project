-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 28, 2024 at 04:57 PM
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
  `status` enum('active','pending','expired') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`bookingID`, `userID`, `roomID`, `bookingTime`, `startTime`, `endTime`, `status`) VALUES
(43, 14, 'S40-1112', '2024-10-28 09:33:10', '2024-10-30 10:00:00', '2024-10-30 10:30:00', 'expired'),
(44, 14, 'S40-1112', '2024-10-28 09:44:44', '2024-10-30 10:00:00', '2024-10-30 10:30:00', 'pending'),
(49, 14, 'S40-1112', '2024-10-28 10:03:01', '2024-10-30 09:15:00', '2024-10-30 10:45:00', 'pending'),
(50, 14, 'S40-1112', '2024-10-28 10:03:24', '2024-10-30 08:00:00', '2024-10-30 09:15:00', 'pending'),
(57, 14, 'S40-1002', '2024-10-28 10:31:34', '2024-10-30 10:54:00', '2024-10-30 12:00:00', 'pending'),
(58, 14, 'S40-1003', '2024-10-28 10:31:44', '2024-10-30 10:54:00', '2024-10-30 12:00:00', 'active');

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
  `isRead` tinyint(1) NOT NULL DEFAULT 0
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
  `equipmentID` int(10) NOT NULL,
  `roomID` varchar(10) NOT NULL,
  `equipmentName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `roomID` varchar(10) NOT NULL,
  `type` enum('lab','class') DEFAULT 'class',
  `capacity` int(10) UNSIGNED NOT NULL,
  `isAvailable` tinyint(4) NOT NULL DEFAULT 0,
  `floor` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`roomID`, `type`, `capacity`, `isAvailable`, `floor`) VALUES
('S40-1002', NULL, 10, 1, 1),
('S40-1003', NULL, 10, 1, 1),
('S40-1112', 'class', 20, 1, 1),
('S40-1117', NULL, 20, 1, 1),
('S40-1118', NULL, 20, 1, 1);

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
(13, 'z.fadhel2004@gmail.com', '$2y$10$SVXz4/ZIcig5ETK5wFFVS.W7oGbSiBdO4O/azxFUbxwWtuRckNVRm', '', '', 'student', ''),
(14, 'zf@gmail.com', '$2y$10$If9LNj9T4xOSCkdRJ5tQleYC0O7yxrqQRWtZMnCMTvrMS1xyNLGqC', '', '', 'student', ''),
(15, '22@gmail.com', '$2y$10$hM4ic42IPRFi5yrUZiPGKOJ3G.cNKnFLsR6SyXKFcT7txLxM2QaGq', '', '', 'student', ''),
(17, 'z@gmail.com', '$2y$10$Kr4HBq4DGR90BPPNfIAXdeBPcovK4kyzKYVmIlksHcAKHHUrkwlai', '', '', 'student', ''),
(19, 'eee@gmail.com', '123', 'z', 'f', 'student', ''),
(20, 'work@gmail.com', '123', 'z', 'f', 'student', ''),
(21, 'workPlz@gmail.com', '123', 'z', 'f', 'student', '');

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
  ADD PRIMARY KEY (`equipmentID`),
  ADD KEY `fk_equipment_roomID` (`roomID`);

--
-- Indexes for table `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`roomID`);

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
  MODIFY `bookingID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `commentID` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comment_reply`
--
ALTER TABLE `comment_reply`
  MODIFY `replyID` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

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
-- Constraints for table `equipment`
--
ALTER TABLE `equipment`
  ADD CONSTRAINT `fk_equipment_roomID` FOREIGN KEY (`roomID`) REFERENCES `room` (`roomID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
