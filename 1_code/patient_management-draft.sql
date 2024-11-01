-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 29, 2024 at 04:48 PM
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
-- Database: `patient_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `CheckUp`
--

CREATE TABLE `CheckUp` (
  `CheckTime` datetime DEFAULT NULL,
  `CheckReason` varchar(255) DEFAULT NULL,
  `PatientID` int(11) DEFAULT NULL,
  `DoctorID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `CheckUp`
--

INSERT INTO `CheckUp` (`CheckTime`, `CheckReason`, `PatientID`, `DoctorID`) VALUES
('2024-01-01 13:00:00', 'Trouble Sleeping', 2, 2214),
('2025-12-18 12:00:00', 'Migraine', 1, 2214);

-- --------------------------------------------------------

--
-- Table structure for table `Doctor`
--

CREATE TABLE `Doctor` (
  `DoctorID` int(11) NOT NULL,
  `DoctorName` varchar(255) DEFAULT NULL,
  `Department` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Doctor`
--

INSERT INTO `Doctor` (`DoctorID`, `DoctorName`, `Department`, `password`) VALUES
(2214, 'Allan House', 'Check Ups', 'securepass'),
(3211, 'Paul Bergfelder', 'Surgery', 'password123');

-- --------------------------------------------------------

--
-- Table structure for table `DoctorPatient`
--

CREATE TABLE `DoctorPatient` (
  `DoctorID` int(11) NOT NULL,
  `PatientID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `DoctorPatient`
--

INSERT INTO `DoctorPatient` (`DoctorID`, `PatientID`) VALUES
(2214, 1),
(2214, 2),
(3211, 1),
(3211, 2);

-- --------------------------------------------------------

--
-- Table structure for table `Emergency`
--

CREATE TABLE `Emergency` (
  `RoomNum` int(11) DEFAULT NULL,
  `Diagnosis` varchar(255) DEFAULT NULL,
  `PatientID` int(11) DEFAULT NULL,
  `DoctorID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Emergency`
--

INSERT INTO `Emergency` (`RoomNum`, `Diagnosis`, `PatientID`, `DoctorID`) VALUES
(2, 'Broken Leg', 1, 3211),
(5, 'Stroke Symptoms', 2, 3211);

-- --------------------------------------------------------

--
-- Table structure for table `Labs`
--

CREATE TABLE `Labs` (
  `LabTime` datetime DEFAULT NULL,
  `LabType` varchar(255) DEFAULT NULL,
  `LabResult` varchar(255) DEFAULT NULL,
  `PatientID` int(11) DEFAULT NULL,
  `ClinicLocation` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Labs`
--

INSERT INTO `Labs` (`LabTime`, `LabType`, `LabResult`, `PatientID`, `ClinicLocation`) VALUES
('2024-01-01 09:00:00', 'Full Blood Panel', 'Normal', 1, 'Quest Diagnostics in IU Health, Indianapolis'),
('2024-01-01 22:00:00', 'Blood Sugar', '<100', 2, 'Quest Diagnostics at Community Hospital North, Indianapolis');

-- --------------------------------------------------------

--
-- Table structure for table `Patient`
--

CREATE TABLE `Patient` (
  `PatientID` int(11) NOT NULL,
  `PatientName` varchar(255) DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `City` varchar(255) DEFAULT NULL,
  `ContactNumber` varchar(15) DEFAULT NULL,
  `PatientInformation` varchar(255) DEFAULT NULL,
  `Prescriptions` varchar(255) DEFAULT NULL,
  `Diagnoses` varchar(255) DEFAULT NULL,
  `PreferredPharmacy` varchar(255) DEFAULT NULL,
  `DOB` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Patient`
--

INSERT INTO `Patient` (`PatientID`, `PatientName`, `Address`, `City`, `ContactNumber`, `PatientInformation`, `Prescriptions`, `Diagnoses`, `PreferredPharmacy`, `DOB`) VALUES
(1, 'Bobby Mckee', '432 North 20th', 'Richmond', '7190098876', 'broke leg in summer of 2021', 'propranolol, 20mg daily by mouth, extended release', 'heart arrhythmia', '', '1990-12-16'),
(2, 'Stacy Lively', '123 North 22nd', 'Lafayette', '3176679900', '', '', '', '', '1980-01-18');

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
('2024-01-01 11:00:00', 'Femoral Bypass', 1, 3211),
('2024-01-01 15:00:00', 'Coronary Stent Placement', 2, 3211),
('2024-11-23 06:00:00', 'Broken Ankle', 2, 2214);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `CheckUp`
--
ALTER TABLE `CheckUp`
  ADD KEY `PatientID` (`PatientID`),
  ADD KEY `DoctorID` (`DoctorID`);

--
-- Indexes for table `Doctor`
--
ALTER TABLE `Doctor`
  ADD PRIMARY KEY (`DoctorID`);

--
-- Indexes for table `DoctorPatient`
--
ALTER TABLE `DoctorPatient`
  ADD PRIMARY KEY (`DoctorID`,`PatientID`),
  ADD KEY `PatientID` (`PatientID`);

--
-- Indexes for table `Emergency`
--
ALTER TABLE `Emergency`
  ADD KEY `PatientID` (`PatientID`),
  ADD KEY `DoctorID` (`DoctorID`);

--
-- Indexes for table `Labs`
--
ALTER TABLE `Labs`
  ADD KEY `PatientID` (`PatientID`);

--
-- Indexes for table `Patient`
--
ALTER TABLE `Patient`
  ADD PRIMARY KEY (`PatientID`);

--
-- Indexes for table `Surgery`
--
ALTER TABLE `Surgery`
  ADD KEY `PatientID` (`PatientID`),
  ADD KEY `DoctorID` (`DoctorID`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `CheckUp`
--
ALTER TABLE `CheckUp`
  ADD CONSTRAINT `checkup_ibfk_1` FOREIGN KEY (`PatientID`) REFERENCES `Patient` (`PatientID`),
  ADD CONSTRAINT `checkup_ibfk_2` FOREIGN KEY (`DoctorID`) REFERENCES `Doctor` (`DoctorID`);

--
-- Constraints for table `DoctorPatient`
--
ALTER TABLE `DoctorPatient`
  ADD CONSTRAINT `doctorpatient_ibfk_1` FOREIGN KEY (`DoctorID`) REFERENCES `Doctor` (`DoctorID`),
  ADD CONSTRAINT `doctorpatient_ibfk_2` FOREIGN KEY (`PatientID`) REFERENCES `Patient` (`PatientID`);

--
-- Constraints for table `Emergency`
--
ALTER TABLE `Emergency`
  ADD CONSTRAINT `emergency_ibfk_1` FOREIGN KEY (`PatientID`) REFERENCES `Patient` (`PatientID`),
  ADD CONSTRAINT `emergency_ibfk_2` FOREIGN KEY (`DoctorID`) REFERENCES `Doctor` (`DoctorID`);

--
-- Constraints for table `Labs`
--
ALTER TABLE `Labs`
  ADD CONSTRAINT `labs_ibfk_1` FOREIGN KEY (`PatientID`) REFERENCES `Patient` (`PatientID`);

--
-- Constraints for table `Surgery`
--
ALTER TABLE `Surgery`
  ADD CONSTRAINT `surgery_ibfk_1` FOREIGN KEY (`PatientID`) REFERENCES `Patient` (`PatientID`),
  ADD CONSTRAINT `surgery_ibfk_2` FOREIGN KEY (`DoctorID`) REFERENCES `Doctor` (`DoctorID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
