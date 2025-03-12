<?php
session_start();
require 'db_connect.php';
include 'header.php';

$isAdmin = isset($_SESSION['admin_id']);
$isTutor = isset($_SESSION['tutor_id']);

if (!$isAdmin && !$isTutor) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $appointment_type = $_POST['appointment_type'];
    $tutor_id = $isTutor ? $_SESSION['tutor_id'] : ($_POST['tutor_id'] ?? null); 
    $current_time = date('Y-m-d H:i:s'); 

    try {
        function isScheduleConflict($pdo, $student_id, $tutor_id, $appointment_time, $duration) {
            $end_time = date('Y-m-d H:i:s', strtotime("$appointment_time + $duration minutes"));

            $stmt = $pdo->prepare(
                "SELECT * FROM (
                    SELECT StudentID, TutorID, CheckTime AS StartTime, DATE_ADD(CheckTime, INTERVAL 30 MINUTE) AS EndTime FROM CheckUp
                    UNION
                    SELECT StudentID, TutorID, SurgeryTime AS StartTime, DATE_ADD(SurgeryTime, INTERVAL 120 MINUTE) AS EndTime FROM Surgery
                    UNION
                    SELECT StudentID, NULL AS TutorID, LabTime AS StartTime, DATE_ADD(LabTime, INTERVAL 30 MINUTE) AS EndTime FROM Labs
                ) AS Appointments
                WHERE (StudentID = ? OR TutorID = ?) AND StartTime < ? AND EndTime > ?"
            );

            $stmt->execute([$student_id, $tutor_id, $end_time, $appointment_time]);
            return $stmt->rowCount() > 0;
        }

        if ($appointment_type === 'surgery') {
            $surgery_time = $_POST['surgery_time'];
            $surgery_type = $_POST['surgery_type'];

            if ($surgery_time < $current_time) {
                throw new Exception("Cannot schedule a surgery in the past.");
            }

            if (isScheduleConflict($pdo, $student_id, $tutor_id, $surgery_time, 120)) {
                header("Location: scheduling_conflict.php");
                exit();
            }

            $stmt = $pdo->prepare("INSERT INTO Surgery (StudentID, SurgeryTime, SurgeryType, TutorID) VALUES (?, ?, ?, ?)");
            $stmt->execute([$student_id, $surgery_time, $surgery_type, $tutor_id]);
        } elseif ($appointment_type === 'lab') {
            $lab_time = $_POST['lab_time'];
            $lab_type = $_POST['lab_type'];
            $clinic_location = $_POST['clinic_location'];

            if ($lab_time < $current_time) {
                throw new Exception("Cannot schedule a lab in the past.");
            }

            if (isScheduleConflict($pdo, $student_id, null, $lab_time, 30)) {
                header("Location: scheduling_conflict.php");
                exit();
            }

            $stmt = $pdo->prepare("INSERT INTO Labs (StudentID, LabTime, LabType, ClinicLocation) VALUES (?, ?, ?, ?)");
            $stmt->execute([$student_id, $lab_time, $lab_type, $clinic_location]);
        } elseif ($appointment_type === 'checkup') {
            $checkup_time = $_POST['checkup_time'];
            $checkup_reason = $_POST['checkup_reason'];

            if ($checkup_time < $current_time) {
                throw new Exception("Cannot schedule a meeting in the past.");
            }

            if (isScheduleConflict($pdo, $student_id, $tutor_id, $checkup_time, 30)) {
                header("Location: scheduling_conflict.php");
                exit();
            }

            $stmt = $pdo->prepare("INSERT INTO CheckUp (StudentID, CheckTime, CheckReason, TutorID) VALUES (?, ?, ?, ?)");
            $stmt->execute([$student_id, $checkup_time, $checkup_reason, $tutor_id]);
        }

        header("Location: success.php");
        exit();
    } catch (PDOException $e) {
        echo "Database Error: " . $e->getMessage();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
