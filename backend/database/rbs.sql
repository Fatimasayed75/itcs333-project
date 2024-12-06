-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 06, 2024 at 02:08 PM
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
  `floor` tinyint(4) NOT NULL,
  `department` enum('IS','CS','CE') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`roomID`, `type`, `capacity`, `isAvailable`, `floor`, `department`) VALUES
('S40-021', 'lab', 30, 1, 0, 'IS'),
('S40-023', 'lab', 30, 1, 0, 'IS'),
('S40-028', 'lab', 30, 1, 0, 'IS'),
('S40-029', 'class', 30, 1, 0, 'IS'),
('S40-030', 'class', 30, 1, 0, 'IS'),
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
-- Constraints for table `equipment`
--
ALTER TABLE `equipment`
  ADD CONSTRAINT `fk_equipment_roomID` FOREIGN KEY (`roomID`) REFERENCES `room` (`roomID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
