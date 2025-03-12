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
-- Table structure for table `CheckUp`
--

CREATE TABLE `CheckUp` (
  `CheckTime` datetime DEFAULT NULL,
  `CheckReason` varchar(255) DEFAULT NULL,
  `StudentID` int(11) DEFAULT NULL,
  `TutorID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `CheckUp`
--

INSERT INTO `CheckUp` (`CheckTime`, `CheckReason`, `StudentID`, `TutorID`) VALUES
('2024-01-01 13:00:00', 'Trouble Sleeping', 2, 2214),
('2025-12-18 12:00:00', 'Migraine', 1, 2214),
('2024-12-15 12:00:00', 'General Wellness Check', 2, 2214),
('2026-12-20 12:00:00', 'General Wellness Check', 2, 2214);

-- --------------------------------------------------------

--
-- Table structure for table `Doctor`
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
(3211, 'Paul Bergfelder', 'Math', 'password123', '1972-05-09', '456 Oak Avenue, Shelbyville', 'pbergfelder@email.com', '3176125555');

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
-- Table structure for table `Labs`
--

CREATE TABLE `Labs` (
  `LabTime` datetime DEFAULT NULL,
  `LabType` varchar(255) DEFAULT NULL,
  `LabResult` varchar(255) DEFAULT NULL,
  `StudentID` int(11) DEFAULT NULL,
  `TutorLocation` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Labs`
--

INSERT INTO `Labs` (`LabTime`, `LabType`, `LabResult`, `PatientID`, `ClinicLocation`) VALUES
('2024-01-01 09:00:00', 'Full Blood Panel', 'Normal', 1, 'Quest Diagnostics in IU Health, Indianapolis'),
('2024-01-01 22:00:00', 'Blood Sugar', '<100', 2, 'Quest Diagnostics at Community Hospital North, Indianapolis'),
('2024-12-01 12:00:00', 'Full Blood Panel', NULL, 2, 'Quest Diagnostics at Northpoint Pediatrics, Noblesville, IN');

-- --------------------------------------------------------

--
-- Table structure for table `Patient`
--

CREATE TABLE `Student` (
  `StudentID` int(11) NOT NULL,
  `StudentName` varchar(255) DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `City` varchar(255) DEFAULT NULL,
  `ContactNumber` varchar(15) DEFAULT NULL,
  `StudentInformation` varchar(255) DEFAULT NULL,
  `Class` varchar(255) DEFAULT NULL,
  `Diagnoses` varchar(255) DEFAULT NULL,
  `PreferredPharmacy` varchar(255) DEFAULT NULL,
  `DOB` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Student`
--

INSERT INTO `Student` (`StudentID`, `StudentName`, `Address`, `City`, `ContactNumber`, `StudentInformation`, `Prescriptions`, `Diagnoses`, `PreferredPharmacy`, `DOB`) VALUES
(1, 'Bobby Mckee', '432 North 31st', 'Richmond', '3179980999', 'broke leg in summer of 2021', 'propranolol, 20mg daily by mouth, extended release', 'heart arrhythmia', '', '1980-12-16'),
(2, 'Stacy Lively', '123 North 22nd', 'Lafayette', '3176679900', '', 'Kepra, 20 mg, twice daily', '', '', '1980-01-18'),
(3, 'John Doe', '123 Maple Ave', 'Indianapolis', '555-1234', 'Allergic to peanuts', 'Ibuprofen 200mg PRN', NULL, NULL, '1980-06-15'),
(4, 'Jane Smith', '456 Pine Rd', 'Bloomington', '555-5678', 'Family history of diabetes', NULL, 'Type 2 Diabetes', NULL, '1990-12-01'),
(5, 'Mike Johnson', '789 Oak St', 'Fort Wayne', '555-9999', NULL, NULL, 'Hypertension', 'CVS Pharmacy', '1975-03-25'),
(6, 'Emily Davis', '321 Spruce Ln', 'Muncie', '555-2468', NULL, 'Atorvastatin 20mg daily', NULL, 'Walgreens', '1988-09-07'),
(7, 'Robert Wilson', '654 Cedar Blvd', 'Evansville', '555-1357', 'History of mild asthma', NULL, 'Chronic bronchitis', NULL, '1965-11-20');

-- --------------------------------------------------------

--
-- Table structure for table `Surgery`
--

CREATE TABLE `Surgery` (
  `SurgeryTime` datetime DEFAULT NULL,
  `SurgeryType` varchar(255) DEFAULT NULL,
  `PatientID` int(11) DEFAULT NULL,
  `DoctorID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Surgery`
--

INSERT INTO `Surgery` (`SurgeryTime`, `SurgeryType`, `PatientID`, `DoctorID`) VALUES
('2024-01-01 15:00:00', 'Coronary Stent Placement', 2, 3211),
('2024-11-23 06:00:00', 'Broken Ankle', 2, 2214),
('2026-12-20 12:00:00', 'Appendectomy', 1, 2214);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Admin`
--
ALTER TABLE `Admin`
  ADD PRIMARY KEY (`AdminID`);

--
-- Indexes for table `CheckUp`
--
ALTER TABLE `CheckUp`
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
-- Indexes for table `Labs`
--
ALTER TABLE `Labs`
  ADD KEY `StudentID` (`StudentID`);

--
-- Indexes for table `Student`
--
ALTER TABLE `Student`
  ADD PRIMARY KEY (`StudentID`);

--
-- Indexes for table `Surgery`
--
ALTER TABLE `Surgery`
  ADD KEY `StudentID` (`StudentID`),
  ADD KEY `TutorID` (`TutorID`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `CheckUp`
--
ALTER TABLE `CheckUp`
  ADD CONSTRAINT `checkup_ibfk_1` FOREIGN KEY (`StudentID`) REFERENCES `Student` (`StudentID`),
  ADD CONSTRAINT `checkup_ibfk_2` FOREIGN KEY (`TutorID`) REFERENCES `Tutor` (`TutorID`);

--
-- Constraints for table `TutorStudent`
--
ALTER TABLE `TutorStudent`
  ADD CONSTRAINT `tutorstudent_ibfk_1` FOREIGN KEY (`TutorID`) REFERENCES `Tutor` (`TutorID`),
  ADD CONSTRAINT `tutorstudent_ibfk_2` FOREIGN KEY (`StudentID`) REFERENCES `Student` (`StudentID`);

--
-- Constraints for table `Labs`
--
ALTER TABLE `Labs`
  ADD CONSTRAINT `labs_ibfk_1` FOREIGN KEY (`StudentID`) REFERENCES `Student` (`StudentID`);

--
-- Constraints for table `Surgery`
--
ALTER TABLE `Surgery`
  ADD CONSTRAINT `surgery_ibfk_1` FOREIGN KEY (`StudentID`) REFERENCES `Student` (`StudentID`),
  ADD CONSTRAINT `surgery_ibfk_2` FOREIGN KEY (`TutorID`) REFERENCES `Tutor` (`TutorID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
