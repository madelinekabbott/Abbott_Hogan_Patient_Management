<?php
session_start();
$isAdmin = isset($_SESSION['admin_id']);
$isTutor = isset($_SESSION['tutor_id']);

if (!$isAdmin && !$isTutor) {
    header("Location: login.php");
    exit();
}

require 'db_connect.php';

$type = $_GET['type'];
$student_id = $_GET['student_id'];
$time = $_GET['time'];

$tutor_id = $isTutor ? $_SESSION['tutor_id'] : null;

switch ($type) {
    case 'homeworkhelp':
        $stmt = $pdo->prepare(
            $isTutor ? "DELETE FROM HomeworkHelp WHERE StudentID = :student_id AND HwTime = :time AND TutorID = :tutor_id" :
                        "DELETE FROM HomeworkHelp WHERE StudentID = :student_id AND HwTime = :time"
        );
        break;
    case 'lab':
        $stmt = $pdo->prepare(
            $isTutor ? "DELETE FROM Labs WHERE StudentID = :student_id AND LabTime = :time AND EXISTS (
                            SELECT 1 FROM DoctorPatient WHERE TutorID = :tutor_id AND StudentID = :student_id
                        )" :
                        "DELETE FROM Labs WHERE StudentID = :student_id AND LabTime = :time"
        );
        break;
    case 'checkup':
        $stmt = $pdo->prepare(
            $isTutor ? "DELETE FROM CheckUp WHERE StudentID = :student_id AND CheckTime = :time AND TutorID = :tutor_id" :
                        "DELETE FROM CheckUp WHERE StudentID = :student_id AND CheckTime = :time"
        );
        break;
    default:
        echo "Invalid appointment type.";
        exit();
}

$params = [
    ':patient_id' => $student_id,
    ':time' => $time
];
if ($isTutor) {
    $params[':tutor_id'] = $tutor_id;
}
$stmt->execute($params);

header("Location: view_appointments.php");
exit();
