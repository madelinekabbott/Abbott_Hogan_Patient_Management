<?php
session_start();
if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

require 'db_connect.php';
include 'header.php';

$doctor_id = $_SESSION['doctor_id'];

// Fetch appointments
$surgery_stmt = $pdo->prepare("
    SELECT Surgery.PatientID, Patient.PatientName, Surgery.SurgeryTime, Surgery.SurgeryType 
    FROM Surgery 
    JOIN Patient ON Surgery.PatientID = Patient.PatientID 
    WHERE Surgery.DoctorID = :doctor_id
");
$surgery_stmt->execute(['doctor_id' => $doctor_id]);
$surgeries = $surgery_stmt->fetchAll();

$lab_stmt = $pdo->prepare("
    SELECT Labs.PatientID, Patient.PatientName, Labs.LabTime, Labs.LabType, Labs.ClinicLocation
    FROM Labs 
    JOIN Patient ON Labs.PatientID = Patient.PatientID 
    JOIN DoctorPatient ON Labs.PatientID = DoctorPatient.PatientID 
    WHERE DoctorPatient.DoctorID = :doctor_id
");
$lab_stmt->execute(['doctor_id' => $doctor_id]);
$labs = $lab_stmt->fetchAll();

$checkup_stmt = $pdo->prepare("
    SELECT CheckUp.PatientID, Patient.PatientName, CheckUp.CheckTime, CheckUp.CheckReason 
    FROM CheckUp 
    JOIN Patient ON CheckUp.PatientID = Patient.PatientID 
    WHERE CheckUp.DoctorID = :doctor_id
");
$checkup_stmt->execute(['doctor_id' => $doctor_id]);
$checkups = $checkup_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Appointments</title>
    <link rel="stylesheet" href="patient_management_style.css">
</head>
<body>
    <div class = "container">
    <h2>Your Scheduled Appointments</h2>

    <!-- Surgeries Table -->
    <h3>Surgeries</h3>
    <table class="appt-table">
        <thead>
            <tr>
                <th>Patient Name</th>
                <th>Surgery Time</th>
                <th>Surgery Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($surgeries) > 0): ?>
                <?php foreach ($surgeries as $surgery): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($surgery['PatientName']); ?></td>
                        <td><?php echo htmlspecialchars($surgery['SurgeryTime']); ?></td>
                        <td><?php echo htmlspecialchars($surgery['SurgeryType']); ?></td>
                        <td class="appt-cell">
                            <a href="edit_appointment.php?type=surgery&patient_id=<?php echo $surgery['PatientID']; ?>&time=<?php echo urlencode($surgery['SurgeryTime']); ?>">Edit</a> |
                            <a href="delete_appointment.php?type=surgery&patient_id=<?php echo $surgery['PatientID']; ?>&time=<?php echo urlencode($surgery['SurgeryTime']); ?>" onclick="return confirm('Are you sure you want to delete this appointment?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No scheduled surgeries.</td>
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
            <?php if (count($labs) > 0): ?>
                <?php foreach ($labs as $lab): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($lab['PatientName']); ?></td>
                        <td><?php echo htmlspecialchars($lab['LabTime']); ?></td>
                        <td><?php echo htmlspecialchars($lab['LabType']); ?></td>
                        <td><?php echo htmlspecialchars($lab['ClinicLocation']); ?></td>
                        <td class="appt-cell">
                            <a href="edit_appointment.php?type=lab&patient_id=<?php echo $lab['PatientID']; ?>&time=<?php echo urlencode($lab['LabTime']); ?>">Edit</a> |
                            <a href="delete_appointment.php?type=lab&patient_id=<?php echo $lab['PatientID']; ?>&time=<?php echo urlencode($lab['LabTime']); ?>" onclick="return confirm('Are you sure you want to delete this appointment?');">Delete</a>
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
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($checkups) > 0): ?>
                <?php foreach ($checkups as $checkup): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($checkup['PatientName']); ?></td>
                        <td><?php echo htmlspecialchars($checkup['CheckTime']); ?></td>
                        <td><?php echo htmlspecialchars($checkup['CheckReason']); ?></td>
                        <td class="appt-cell">
                            <a href="edit_appointment.php?type=checkup&patient_id=<?php echo $checkup['PatientID']; ?>&time=<?php echo urlencode($checkup['CheckTime']); ?>">Edit</a> |
                            <a href="delete_appointment.php?type=checkup&patient_id=<?php echo $checkup['PatientID']; ?>&time=<?php echo urlencode($checkup['CheckTime']); ?>" onclick="return confirm('Are you sure you want to delete this appointment?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No scheduled check-ups.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <br>
    <a href="schedule_appointment.php" class="button">Schedule another appointment</a>
    <br>
    <a href="logout.php">Logout</a>
</div>
</body>
</html>
