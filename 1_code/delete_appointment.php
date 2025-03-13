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
    case 'testprep':
        $stmt = $pdo->prepare(
            $isTutor ? "DELETE FROM TestPrep WHERE StudentID = :student_id AND TestPrepTime = :time AND EXISTS (
                            SELECT 1 FROM TutorStudent WHERE TutorID = :tutor_id AND StudentID = :student_id
                        )" :
                        "DELETE FROM TestPrep WHERE StudentID = :student_id AND TestPrepTime = :time"
        );
        break;
    case 'meetup':
        $stmt = $pdo->prepare(
            $isTutor ? "DELETE FROM MeetUp WHERE StudentID = :student_id AND MeetUpTime = :time AND TutorID = :tutor_id" :
                        "DELETE FROM MeetUp WHERE StudentID = :student_id AND MeetUpTime = :time"
        );
        break;
    default:
        echo "Invalid appointment type.";
        exit();
}

$params = [
    ':student_id' => $student_id,
    ':time' => $time
];
if ($isTutor) {
    $params[':tutor_id'] = $tutor_id;
}
$stmt->execute($params);

header("Location: view_appointments.php");
exit();
