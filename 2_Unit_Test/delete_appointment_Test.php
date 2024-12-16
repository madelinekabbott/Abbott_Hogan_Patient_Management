<?php

namespace Zubair\TestProject;

use PHPUnit\Framework\TestCase;

final class delete_appointment_Test extends TestCase
{


<?php
session_start();
$isAdmin = isset($_SESSION['admin_id']);
$isDoctor = isset($_SESSION['doctor_id']);

if (!$isAdmin && !$isDoctor) {
    header("Location: login.php");
    exit();
}

require 'db_connect.php';

$type = $_GET['type'];
$patient_id = $_GET['patient_id'];
$time = $_GET['time'];

$doctor_id = $isDoctor ? $_SESSION['doctor_id'] : null;

switch ($type) {
    case 'surgery':
        $stmt = $pdo->prepare(
            $isDoctor ? "DELETE FROM Surgery WHERE PatientID = :patient_id AND SurgeryTime = :time AND DoctorID = :doctor_id" :
                        "DELETE FROM Surgery WHERE PatientID = :patient_id AND SurgeryTime = :time"
        );
        break;
    case 'lab':
        $stmt = $pdo->prepare(
            $isDoctor ? "DELETE FROM Labs WHERE PatientID = :patient_id AND LabTime = :time AND EXISTS (
                            SELECT 1 FROM DoctorPatient WHERE DoctorID = :doctor_id AND PatientID = :patient_id
                        )" :
                        "DELETE FROM Labs WHERE PatientID = :patient_id AND LabTime = :time"
        );
        break;
    case 'checkup':
        $stmt = $pdo->prepare(
            $isDoctor ? "DELETE FROM CheckUp WHERE PatientID = :patient_id AND CheckTime = :time AND DoctorID = :doctor_id" :
                        "DELETE FROM CheckUp WHERE PatientID = :patient_id AND CheckTime = :time"
        );
        break;
    default:
        echo "Invalid appointment type.";
        exit();
}

$params = [
    ':patient_id' => $patient_id,
    ':time' => $time
];
if ($isDoctor) {
    $params[':doctor_id'] = $doctor_id;
}
$stmt->execute($params);

header("Location: view_appointments.php");
exit();
}