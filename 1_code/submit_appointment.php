<?php
session_start();
if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

require 'db_connect.php';
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_id = $_POST['patient_id'];
    $appointment_type = $_POST['appointment_type'];
    $doctor_id = $_SESSION['doctor_id'];

    try {
        if ($appointment_type === 'surgery') {
            $surgery_time = $_POST['surgery_time'];
            $surgery_type = $_POST['surgery_type'];

            // Insert Surgery Appointment
            $stmt = $pdo->prepare("INSERT INTO Surgery (PatientID, SurgeryTime, SurgeryType, DoctorID) VALUES (?, ?, ?, ?)");
            $stmt->execute([$patient_id, $surgery_time, $surgery_type, $doctor_id]);
        } elseif ($appointment_type === 'lab') {
            $lab_time = $_POST['lab_time'];
            $lab_type = $_POST['lab_type'];
            $clinic_location = $_POST['clinic_location'];
            
            // Insert Lab Appointment
            $stmt = $pdo->prepare("INSERT INTO Labs (PatientID, LabTime, LabType, ClinicLocation) VALUES (?, ?, ?, ?)");
            $stmt->execute([$patient_id, $lab_time, $lab_type, $clinic_location]);
        } elseif ($appointment_type === 'checkup') {
            $checkup_time = $_POST['checkup_time'];
            $checkup_reason = $_POST['checkup_reason'];

            // Insert Checkup Appointment
            $stmt = $pdo->prepare("INSERT INTO CheckUp (PatientID, CheckTime, CheckReason, DoctorID) VALUES (?, ?, ?, ?)");
            $stmt->execute([$patient_id, $checkup_time, $checkup_reason, $doctor_id]);
        }

        // Redirect or show success message
        header("Location: success.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
