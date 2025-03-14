-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 14, 2024 at 01:40 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `student_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `Admin`
--

CREATE TABLE `Admin` (
  `AdminID` int(11) NOT NULL,
  `AdminName` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Admin`
--

INSERT INTO `Admin` (`AdminID`, `AdminName`, `password`) VALUES
(6677, 'John Donne', 'password111');

-- --------------------------------------------------------

--
-- Table structure for table `MeetUp`
--

CREATE TABLE `MeetUp` (
  `MeetTime` datetime DEFAULT NULL,
  `MeetReason` varchar(255) DEFAULT NULL,
  `StudentID` int(11) DEFAULT NULL,
  `TutorID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `MeetUp`
--

INSERT INTO `MeetUp` (`MeetTime`, `MeetReason`, `StudentID`, `TutorID`) VALUES
('2024-01-01 13:00:00', 'Math', 2, 3211),
('2025-12-18 12:00:00', 'English', 1, 2214),
('2024-12-15 12:00:00', 'Social Studies', 2, 3211),
('2026-12-20 12:00:00', 'General Prep', 2, 2214);

-- --------------------------------------------------------

--
-- Table structure for table `Tutor`
--

CREATE TABLE `Tutor` (
  `TutorID` int(11) NOT NULL,
  `TutorName` varchar(255) DEFAULT NULL,
  `Department` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `DOB` date DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `PhoneNumber` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Tutor`
--

INSERT INTO `Tutor` (`TutorID`, `TutorName`, `Department`, `password`, `DOB`, `Address`, `Email`, `PhoneNumber`) VALUES
(2214, 'Allan House', 'English', 'securepass', '1988-12-10', '123 Elm Street, Springfield', 'ahouse@email.com', '3175550000'),
(3211, 'Paul Bergfelder', 'Math/Social Studies', 'password123', '1972-05-09', '456 Oak Avenue, Shelbyville', 'pbergfelder@email.com', '3176125555');

-- --------------------------------------------------------

--
-- Table structure for table `TutorStudent`
--

CREATE TABLE `TutorStudent` (
  `TutorID` int(11) NOT NULL,
  `StudentID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `TutorStudent`
--

INSERT INTO `TutorStudent` (`TutorID`, `StudentID`) VALUES
(2214, 1),
(2214, 2),
(2214, 4),
(3211, 1),
(3211, 2),
(3211, 7);

-- --------------------------------------------------------

--
-- Table structure for table `TestPrep`
--

CREATE TABLE `TestPrep` (
  `PrepTime` datetime DEFAULT NULL,
  `PrepType` varchar(255) DEFAULT NULL,
  `PrepLengthMin` varchar(255) DEFAULT NULL,
  `StudentID` int(11) DEFAULT NULL,
  `TutorLocation` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `TestPrep`
--

INSERT INTO `TestPrep` (`TestTime`, `TestType`, `PrepLengthMin`, `StudentID`, `TutorLocation`) VALUES
('2024-01-01 09:00:00', 'Math', '30 Min', 1, 'Room 25'),
('2024-01-01 22:00:00', 'English', '15 Min', 2, 'Room 57'),
('2024-12-01 12:00:00', 'Social Studies', '45 Min', 2, 'Room 8');

-- --------------------------------------------------------

--
-- Table structure for table `Student`
--

CREATE TABLE `Student` (
  `StudentID` int(11) NOT NULL,
  `StudentName` varchar(255) DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `City` varchar(255) DEFAULT NULL,
  `ContactNumber` varchar(15) DEFAULT NULL,
  `StudentInformation` varchar(255) DEFAULT NULL,
  `Class` varchar(255) DEFAULT NULL,
  `Grade` varchar(255) DEFAULT NULL,
  `PreferredStudyMethod` varchar(255) DEFAULT NULL,
  `DOB` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Student`
--

INSERT INTO `Student` (`StudentID`, `StudentName`, `Address`, `City`, `ContactNumber`, `StudentInformation`, `Class`, `Grade`, `PreferredStudyMethod`, `DOB`) VALUES
(1, 'Bobby Mckee', '432 North 31st', 'Richmond', '3179980999', '', 'Math 101', 'Freshman', 'Face to Face', '1980-12-16'),
(2, 'Stacy Lively', '123 North 22nd', 'Lafayette', '3176679900', '', 'English 201', 'Sophmore', 'Zoom', '1980-01-18'),
(3, 'John Doe', '123 Maple Ave', 'Indianapolis', '555-1234', 'Student needs 1 on 1 time', 'Math 202', 'Sophmore', NULL, '1980-06-15'),
(4, 'Jane Smith', '456 Pine Rd', 'Bloomington', '555-5678', '', 'Math 401', 'Senior', NULL, '1990-12-01'),
(5, 'Mike Johnson', '789 Oak St', 'Fort Wayne', '555-9999', NULL, 'English 101', 'Freshman', 'Face to Face', '1975-03-25'),
(6, 'Emily Davis', '321 Spruce Ln', 'Muncie', '555-2468', NULL, 'English 101', 'Freshman', 'Face to Face', '1988-09-07'),
(7, 'Robert Wilson', '654 Cedar Blvd', 'Evansville', '555-1357', 'Trouble Reading', 'Math 101', 'Freshman', NULL, '1965-11-20');

-- --------------------------------------------------------

--
-- Table structure for table `HomeworkHelp`
--

CREATE TABLE `HomeworkHelp` (
  `HwTime` datetime DEFAULT NULL,
  `HwType` varchar(255) DEFAULT NULL,
  `StudentID` int(11) DEFAULT NULL,
  `TutorID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `HomeworkHelp`
--

INSERT INTO `HomeworkHelp` (`HwTime`, `HwType`, `StudentID`, `TutorID`) VALUES
('2024-01-01 15:00:00', 'Project', 2, 3211),
('2024-11-23 06:00:00', 'Essay', 2, 2214),
('2026-12-20 12:00:00', 'Powerpoint', 1, 2214);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Admin`
--
ALTER TABLE `Admin`
  ADD PRIMARY KEY (`AdminID`);

--
-- Indexes for table `MeetUp`
--
ALTER TABLE `MeetUp`
  ADD KEY `StudentID` (`StudentID`),
  ADD KEY `TutorID` (`TutorID`);

--
-- Indexes for table `Tutor`
--
ALTER TABLE `Tutor`
  ADD PRIMARY KEY (`TutorID`);

--
-- Indexes for table `TutorStudent`
--
ALTER TABLE `TutorStudent`
  ADD PRIMARY KEY (`TutorID`,`StudentID`),
  ADD KEY `StudentID` (`StudentID`);

--
-- Indexes for table `TestPrep`
--
ALTER TABLE `TestPrep`
  ADD KEY `StudentID` (`StudentID`);

--
-- Indexes for table `Student`
--
ALTER TABLE `Student`
  ADD PRIMARY KEY (`StudentID`);

--
-- Indexes for table `HomeworkHelp`
--
ALTER TABLE `HomeworkHelp`
  ADD KEY `StudentID` (`StudentID`),
  ADD KEY `TutorID` (`TutorID`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `MeetUp`
--
ALTER TABLE `MeetUp`
  ADD CONSTRAINT `meetup_ibfk_1` FOREIGN KEY (`StudentID`) REFERENCES `Student` (`StudentID`),
  ADD CONSTRAINT `meetup_ibfk_2` FOREIGN KEY (`TutorID`) REFERENCES `Tutor` (`TutorID`);

--
-- Constraints for table `TutorStudent`
--
ALTER TABLE `TutorStudent`
  ADD CONSTRAINT `tutorstudent_ibfk_1` FOREIGN KEY (`TutorID`) REFERENCES `Tutor` (`TutorID`),
  ADD CONSTRAINT `tutorstudent_ibfk_2` FOREIGN KEY (`StudentID`) REFERENCES `Student` (`StudentID`);

--
-- Constraints for table `TestPrep`
--
ALTER TABLE `TestPrep`
  ADD CONSTRAINT `testprep_ibfk_1` FOREIGN KEY (`StudentID`) REFERENCES `Student` (`StudentID`);

--
-- Constraints for table `HomeworkHelp`
--
ALTER TABLE `HomeworkHelp`
  ADD CONSTRAINT `homeworkhelp_ibfk_1` FOREIGN KEY (`StudentID`) REFERENCES `Student` (`StudentID`),
  ADD CONSTRAINT `homeworkhelp_ibfk_2` FOREIGN KEY (`TutorID`) REFERENCES `Tutor` (`TutorID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
