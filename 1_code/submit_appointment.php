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
                    SELECT StudentID, TutorID, MeetUpTime AS StartTime, DATE_ADD(MeetUpTime, INTERVAL 30 MINUTE) AS EndTime FROM MeetUp
                    UNION
                    SELECT StudentID, TutorID, HomeworkHelpTime AS StartTime, DATE_ADD(HomeworkHelpTime, INTERVAL 120 MINUTE) AS EndTime FROM HomeworkHelp
                    UNION
                    SELECT StudentID, NULL AS TutorID, TestPrepTime AS StartTime, DATE_ADD(TestPrepTime, INTERVAL 30 MINUTE) AS EndTime FROM TestPrep
                ) AS Appointments
                WHERE (StudentID = ? OR TutorID = ?) AND StartTime < ? AND EndTime > ?"
            );

            $stmt->execute([$student_id, $tutor_id, $end_time, $appointment_time]);
            return $stmt->rowCount() > 0;
        }

        if ($appointment_type === 'homeworkHelp') {
            $Hw_time = $_POST['Hw_time'];
            $Hw_type = $_POST['Hw_type'];

            if ($Hw_time < $current_time) {
                throw new Exception("Cannot schedule homework help in the past.");
            }

            if (isScheduleConflict($pdo, $student_id, $tutor_id, $Hw_time, 120)) {
                header("Location: scheduling_conflict.php");
                exit();
            }

            $stmt = $pdo->prepare("INSERT INTO HomeworkHelp (StudentID, HwTime, HwType, TutorID) VALUES (?, ?, ?, ?)");
            $stmt->execute([$student_id, $Hw_time, $Hw_type, $tutor_id]);
        } elseif ($appointment_type === 'lab') {
            $testprep_time = $_POST['testprep_time'];
            $testprep_type = $_POST['testprep_type'];
            $tutor_location = $_POST['tutor_location'];

            if ($testprep_time < $current_time) {
                throw new Exception("Cannot schedule test prep in the past.");
            }

            if (isScheduleConflict($pdo, $student_id, null, $testprep_time, 30)) {
                header("Location: scheduling_conflict.php");
                exit();
            }

            $stmt = $pdo->prepare("INSERT INTO Labs (StudentID, TestPrepTime, TestPrepType, TutorLocation) VALUES (?, ?, ?, ?)");
            $stmt->execute([$student_id, $testprep_time, $testprep_type, $tutor_location]);
        } elseif ($appointment_type === 'meetup') {
            $meetup_time = $_POST['meetup_time'];
            $meetup_reason = $_POST['meetup_reason'];

            if ($meetup_time < $current_time) {
                throw new Exception("Cannot schedule a meeting in the past.");
            }

            if (isScheduleConflict($pdo, $student_id, $tutor_id, $meetup_time, 30)) {
                header("Location: scheduling_conflict.php");
                exit();
            }

            $stmt = $pdo->prepare("INSERT INTO CheckUp (StudentID, MeetUpTime, MeetUpReason, TutorID) VALUES (?, ?, ?, ?)");
            $stmt->execute([$student_id, $meetup_time, $meetup_reason, $tutor_id]);
        }

        header("Location: success.php");
        exit();
    } catch (PDOException $e) {
        echo "Database Error: " . $e->getMessage();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
