<?php
session_start();
if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

require 'db_connect.php';

$doctor_id = $_SESSION['doctor_id'];
$type = $_GET['type'];
$patient_id = $_GET['patient_id'];
$time = $_GET['time'];

// Delete the correct appointment based on type
switch ($type) {
    case 'surgery':
        $stmt = $pdo->prepare("DELETE FROM Surgery WHERE PatientID = :patient_id AND SurgeryTime = :time AND DoctorID = :doctor_id");
        break;
    case 'lab':
        $stmt = $pdo->prepare("DELETE FROM Labs WHERE PatientID = :patient_id AND LabTime = :time AND EXISTS (
            SELECT 1 FROM DoctorPatient WHERE DoctorID = :doctor_id AND PatientID = :patient_id
        )");
        break;
    case 'checkup':
        $stmt = $pdo->prepare("DELETE FROM CheckUp WHERE PatientID = :patient_id AND CheckTime = :time AND DoctorID = :doctor_id");
        break;
    default:
        echo "Invalid appointment type.";
        exit();
}

// Execute the delete statement
$stmt->execute([
    ':patient_id' => $patient_id,
    ':time' => $time,
    ':doctor_id' => $doctor_id
]);

header("Location: view_appointments.php");
exit();
?>
