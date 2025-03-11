<?php
session_start();
$isAdmin = isset($_SESSION['admin_id']);
$isDoctor = isset($_SESSION['tutor_id']);

if (!$isAdmin && !$isTutor) {
    header("Location: login.php");
    exit();
}

require 'db_connect.php';

$type = $_GET['type'];
$student_id = $_GET['student_id'];
$time = $_GET['time'];

$doctor_id = $isTutor ? $_SESSION['tutor_id'] : null;

switch ($type) {
    case 'surgery':
        $stmt = $pdo->prepare(
            $isTutor ? "DELETE FROM Surgery WHERE StudentID = :student_id AND SurgeryTime = :time AND TutorID = :tutor_id" :
                        "DELETE FROM Surgery WHERE StudentID = :student_id AND SurgeryTime = :time"
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
