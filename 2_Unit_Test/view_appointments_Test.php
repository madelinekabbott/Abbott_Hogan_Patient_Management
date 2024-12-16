<?php

namespace Zubair\TestProject;

use PHPUnit\Framework\TestCase;

final class view_appointments_Test extends TestCase
{
    


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

$scheduleUrl = $isAdmin ? "admin_schedule_appointment.php" : "schedule_appointment.php";

$view = $_GET['view'] ?? 'upcoming'; 

if ($view === 'past') {
    $timeCondition = "< NOW()";
    $headingSuffix = ": Past";
} else {
    $timeCondition = ">= NOW()";
    $headingSuffix = ": Upcoming";
}

if ($isAdmin) {
    $surgery_stmt = $pdo->query("
        SELECT 
            Surgery.PatientID, 
            Patient.PatientName, 
            Surgery.SurgeryTime, 
            Surgery.SurgeryType,
            Doctor.DoctorName
        FROM Surgery
        JOIN Patient ON Surgery.PatientID = Patient.PatientID
        JOIN Doctor ON Surgery.DoctorID = Doctor.DoctorID
        WHERE Surgery.SurgeryTime $timeCondition
        ORDER BY Surgery.SurgeryTime DESC
    ");
    $surgeries = $surgery_stmt->fetchAll();

    $lab_stmt = $pdo->query("
        SELECT 
            Labs.PatientID, 
            Patient.PatientName, 
            Labs.LabTime, 
            Labs.LabType, 
            Labs.ClinicLocation
        FROM Labs
        JOIN Patient ON Labs.PatientID = Patient.PatientID
        WHERE Labs.LabTime $timeCondition
        ORDER BY Labs.LabTime DESC
    ");
    $labs = $lab_stmt->fetchAll();

    $checkup_stmt = $pdo->query("
        SELECT 
            CheckUp.PatientID, 
            Patient.PatientName, 
            CheckUp.CheckTime, 
            CheckUp.CheckReason,
            Doctor.DoctorName
        FROM CheckUp
        JOIN Patient ON CheckUp.PatientID = Patient.PatientID
        JOIN Doctor ON CheckUp.DoctorID = Doctor.DoctorID
        WHERE CheckUp.CheckTime $timeCondition
        ORDER BY CheckUp.CheckTime DESC
    ");
    $checkups = $checkup_stmt->fetchAll();

} else {
    $doctor_id = $_SESSION['doctor_id'];

    $surgery_stmt = $pdo->prepare("
        SELECT 
            Surgery.PatientID,
            Patient.PatientName,
            Surgery.SurgeryTime,
            Surgery.SurgeryType
        FROM Surgery
        JOIN Patient ON Surgery.PatientID = Patient.PatientID
        WHERE Surgery.DoctorID = :doctor_id
          AND Surgery.SurgeryTime $timeCondition
        ORDER BY Surgery.SurgeryTime DESC
    ");
    $surgery_stmt->execute(['doctor_id' => $doctor_id]);
    $surgeries = $surgery_stmt->fetchAll();

    $lab_stmt = $pdo->prepare("
        SELECT 
            Labs.PatientID,
            Patient.PatientName,
            Labs.LabTime,
            Labs.LabType,
            Labs.ClinicLocation
        FROM Labs
        JOIN Patient ON Labs.PatientID = Patient.PatientID
        JOIN DoctorPatient ON Labs.PatientID = DoctorPatient.PatientID
        WHERE DoctorPatient.DoctorID = :doctor_id
          AND Labs.LabTime $timeCondition
        ORDER BY Labs.LabTime DESC
    ");
    $lab_stmt->execute(['doctor_id' => $doctor_id]);
    $labs = $lab_stmt->fetchAll();

    $checkup_stmt = $pdo->prepare("
        SELECT 
            CheckUp.PatientID,
            Patient.PatientName,
            CheckUp.CheckTime,
            CheckUp.CheckReason
        FROM CheckUp
        JOIN Patient ON CheckUp.PatientID = Patient.PatientID
        WHERE CheckUp.DoctorID = :doctor_id
          AND CheckUp.CheckTime $timeCondition
        ORDER BY CheckUp.CheckTime DESC
    ");
    $checkup_stmt->execute(['doctor_id' => $doctor_id]);
    $checkups = $checkup_stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scheduled Appointments</title>
    <link rel="stylesheet" href="patient_management_style.css">
</head>
<body>
<div class="container">
    <?php if ($isAdmin): ?>
        <h2>All Scheduled Appointments<?php echo $headingSuffix; ?></h2>
    <?php else: ?>
        <h2>Your Scheduled Appointments<?php echo $headingSuffix; ?></h2>
    <?php endif; ?>

    <!-- Toggle links -->
    <div style="margin-bottom: 1rem;">
        <a href="?view=upcoming" class="button">Show Upcoming</a>
        <a href="?view=past" class="button">Show Past</a>
    </div>

    <!-- Surgeries Table -->
    <h3>Surgeries</h3>
    <table class="appt-table">
        <thead>
            <tr>
                <th>Patient Name</th>
                <th>Surgery Time</th>
                <th>Surgery Type</th>
                <?php if ($isAdmin): ?>
                    <th>Assigned Doctor</th>
                <?php endif; ?>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($surgeries)): ?>
                <?php foreach ($surgeries as $surgery): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($surgery['PatientName']); ?></td>
                        <td><?php echo htmlspecialchars($surgery['SurgeryTime']); ?></td>
                        <td><?php echo htmlspecialchars($surgery['SurgeryType']); ?></td>
                        <?php if ($isAdmin): ?>
                            <td><?php echo htmlspecialchars($surgery['DoctorName']); ?></td>
                        <?php endif; ?>
                        <td class="appt-cell">
                            <a href="edit_appointment.php?type=surgery&patient_id=<?php echo $surgery['PatientID']; ?>&time=<?php echo urlencode($surgery['SurgeryTime']); ?>">Edit</a> |
                            <a href="delete_appointment.php?type=surgery&patient_id=<?php echo $surgery['PatientID']; ?>&time=<?php echo urlencode($surgery['SurgeryTime']); ?>" 
                               onclick="return confirm('Are you sure you want to delete this appointment?');">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?php echo $isAdmin ? '5' : '4'; ?>">No scheduled surgeries.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Labs Table -->
    <h3>Lab Appointments</h3>
    <table class="appt-table">
        <thead>
            <tr>
                <th>Patient Name</th>
                <th>Lab Time</th>
                <th>Lab Type</th>
                <th>Clinic Location</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($labs)): ?>
                <?php foreach ($labs as $lab): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($lab['PatientName']); ?></td>
                        <td><?php echo htmlspecialchars($lab['LabTime']); ?></td>
                        <td><?php echo htmlspecialchars($lab['LabType']); ?></td>
                        <td><?php echo htmlspecialchars($lab['ClinicLocation']); ?></td>
                        <td class="appt-cell">
                            <a href="edit_appointment.php?type=lab&patient_id=<?php echo $lab['PatientID']; ?>&time=<?php echo urlencode($lab['LabTime']); ?>">Edit</a> |
                            <a href="delete_appointment.php?type=lab&patient_id=<?php echo $lab['PatientID']; ?>&time=<?php echo urlencode($lab['LabTime']); ?>" 
                               onclick="return confirm('Are you sure you want to delete this appointment?');">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No lab appointments found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Check-Ups Table -->
    <h3>Check-Ups</h3>
    <table class="appt-table">
        <thead>
            <tr>
                <th>Patient Name</th>
                <th>Check-Up Time</th>
                <th>Check-Up Reason</th>
                <?php if ($isAdmin): ?>
                    <th>Assigned Doctor</th>
                <?php endif; ?>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($checkups)): ?>
                <?php foreach ($checkups as $checkup): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($checkup['PatientName']); ?></td>
                        <td><?php echo htmlspecialchars($checkup['CheckTime']); ?></td>
                        <td><?php echo htmlspecialchars($checkup['CheckReason']); ?></td>
                        <?php if ($isAdmin): ?>
                            <td><?php echo htmlspecialchars($checkup['DoctorName']); ?></td>
                        <?php endif; ?>
                        <td class="appt-cell">
                            <a href="edit_appointment.php?type=checkup&patient_id=<?php echo $checkup['PatientID']; ?>&time=<?php echo urlencode($checkup['CheckTime']); ?>">Edit</a> |
                            <a href="delete_appointment.php?type=checkup&patient_id=<?php echo $checkup['PatientID']; ?>&time=<?php echo urlencode($checkup['CheckTime']); ?>" 
                               onclick="return confirm('Are you sure you want to delete this appointment?');">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?php echo $isAdmin ? '5' : '4'; ?>">No scheduled check-ups.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <br>
    <a href="<?php echo $scheduleUrl; ?>" class="button">Schedule a new appointment</a>
    <br><br>
    <a href="logout.php">Logout</a>
</div>
</body>
</html>
}