<?php
session_start();
require 'db_connect.php';
include 'header.php';

$isAdmin = isset($_SESSION['admin_id']);
$isDoctor = isset($_SESSION['doctor_id']);

if (!$isAdmin && !$isDoctor) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_id = $_POST['patient_id'];
    $appointment_type = $_POST['appointment_type'];
    $doctor_id = isset($_SESSION['doctor_id']) ? $_SESSION['doctor_id'] : null;
    $current_time = date('Y-m-d H:i:s'); 

    try {
        function isScheduleConflict($pdo, $patient_id, $doctor_id, $appointment_time, $duration) {
            $end_time = date('Y-m-d H:i:s', strtotime("$appointment_time + $duration minutes"));

            $stmt = $pdo->prepare(
                "SELECT * FROM (
                    SELECT PatientID, DoctorID, CheckTime AS StartTime, DATE_ADD(CheckTime, INTERVAL 30 MINUTE) AS EndTime FROM CheckUp
                    UNION
                    SELECT PatientID, DoctorID, SurgeryTime AS StartTime, DATE_ADD(SurgeryTime, INTERVAL 120 MINUTE) AS EndTime FROM Surgery
                    UNION
                    SELECT PatientID, NULL AS DoctorID, LabTime AS StartTime, DATE_ADD(LabTime, INTERVAL 30 MINUTE) AS EndTime FROM Labs
                ) AS Appointments
                WHERE (PatientID = ? OR DoctorID = ?) AND StartTime < ? AND EndTime > ?"
            );

            $stmt->execute([$patient_id, $doctor_id, $end_time, $appointment_time]);
            return $stmt->rowCount() > 0;
        }

        if ($appointment_type === 'surgery') {
            $surgery_time = $_POST['surgery_time'];
            $surgery_type = $_POST['surgery_type'];

            if ($surgery_time < $current_time) {
                throw new Exception("Cannot schedule a surgery in the past.");
            }

            if (isScheduleConflict($pdo, $patient_id, $doctor_id, $surgery_time, 120)) {
                header("Location: scheduling_conflict.php");
                exit();
            }

            $stmt = $pdo->prepare("INSERT INTO Surgery (PatientID, SurgeryTime, SurgeryType, DoctorID) VALUES (?, ?, ?, ?)");
            $stmt->execute([$patient_id, $surgery_time, $surgery_type, $doctor_id]);
        } elseif ($appointment_type === 'lab') {
            $lab_time = $_POST['lab_time'];
            $lab_type = $_POST['lab_type'];
            $clinic_location = $_POST['clinic_location'];

            if ($lab_time < $current_time) {
                throw new Exception("Cannot schedule a lab in the past.");
            }

            if (isScheduleConflict($pdo, $patient_id, null, $lab_time, 30)) {
                header("Location: scheduling_conflict.php");
                exit();
            }

            $stmt = $pdo->prepare("INSERT INTO Labs (PatientID, LabTime, LabType, ClinicLocation) VALUES (?, ?, ?, ?)");
            $stmt->execute([$patient_id, $lab_time, $lab_type, $clinic_location]);
        } elseif ($appointment_type === 'checkup') {
            $checkup_time = $_POST['checkup_time'];
            $checkup_reason = $_POST['checkup_reason'];

            if ($checkup_time < $current_time) {
                throw new Exception("Cannot schedule a checkup in the past.");
            }

            if (isScheduleConflict($pdo, $patient_id, $doctor_id, $checkup_time, 30)) {
                header("Location: scheduling_conflict.php");
                exit();
            }

            $stmt = $pdo->prepare("INSERT INTO CheckUp (PatientID, CheckTime, CheckReason, DoctorID) VALUES (?, ?, ?, ?)");
            $stmt->execute([$patient_id, $checkup_time, $checkup_reason, $doctor_id]);
        }

        header("Location: success.php");
        exit();
    } catch (PDOException $e) {
        echo "Database Error: " . $e->getMessage();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
