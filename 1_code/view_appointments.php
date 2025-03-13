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
            Surgery.StudentID, 
            Student.StudentName, 
            Surgery.SurgeryTime, 
            Surgery.SurgeryType,
            Tutor.TutorName
        FROM Surgery
        JOIN Student ON Surgery.StudentID = Student.StudentID
        JOIN Tutor ON Surgery.TutorID = Tutor.TutorID
        WHERE Surgery.SurgeryTime $timeCondition
        ORDER BY Surgery.SurgeryTime DESC
    ");
    $surgeries = $surgery_stmt->fetchAll();

    $lab_stmt = $pdo->query("
        SELECT 
            Labs.StudentID, 
            Student.StudentName, 
            Labs.LabTime, 
            Labs.LabType, 
            Labs.ClinicLocation
        FROM Labs
        JOIN Student ON Labs.StudentID = Student.StudentID
        WHERE Labs.LabTime $timeCondition
        ORDER BY Labs.LabTime DESC
    ");
    $labs = $lab_stmt->fetchAll();

    $checkup_stmt = $pdo->query("
        SELECT 
            CheckUp.StudentID, 
            Student.StudentName, 
            CheckUp.CheckTime, 
            CheckUp.CheckReason,
            Tutor.TutorName
        FROM CheckUp
        JOIN Student ON CheckUp.StudentID = Student.StudentID
        JOIN Tutor ON CheckUp.TutorID = Tutor.TutorID
        WHERE CheckUp.CheckTime $timeCondition
        ORDER BY CheckUp.CheckTime DESC
    ");
    $checkups = $checkup_stmt->fetchAll();

} else {
    $tutor_id = $_SESSION['tutor_id'];

    $surgery_stmt = $pdo->prepare("
        SELECT 
            Surgery.StudentID,
            Student.StudentName,
            Surgery.SurgeryTime,
            Surgery.SurgeryType
        FROM Surgery
        JOIN Patient ON Surgery.StudentID = Student.StudentID
        WHERE Surgery.TutorID = :tutor_id
          AND Surgery.SurgeryTime $timeCondition
        ORDER BY Surgery.SurgeryTime DESC
    ");
    $surgery_stmt->execute(['tutor_id' => $tutor_id]);
    $surgeries = $surgery_stmt->fetchAll();

    $lab_stmt = $pdo->prepare("
        SELECT 
            Labs.StudentID,
            Student.StudentName,
            Labs.LabTime,
            Labs.LabType,
            Labs.ClinicLocation
        FROM Labs
        JOIN Student ON Labs.StudentID = Student.StudentID
        JOIN TutorStudent ON Labs.StudentID = TutorStudent.StudentID
        WHERE TutorStudent.TutorID = :tutor_id
          AND Labs.LabTime $timeCondition
        ORDER BY Labs.LabTime DESC
    ");
    $lab_stmt->execute(['tutor_id' => $tutor_id]);
    $labs = $lab_stmt->fetchAll();

    $checkup_stmt = $pdo->prepare("
        SELECT 
            CheckUp.StudentID,
            Student.StudentName,
            CheckUp.CheckTime,
            CheckUp.CheckReason
        FROM CheckUp
        JOIN Student ON CheckUp.StudentID = Student.StudentID
        WHERE CheckUp.TutorID = :tutor_id
          AND CheckUp.CheckTime $timeCondition
        ORDER BY CheckUp.CheckTime DESC
    ");
    $checkup_stmt->execute(['tutor_id' => $tutor_id]);
    $checkups = $checkup_stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scheduled Appointments</title>
    <link rel="stylesheet" href="student_management_style.css">
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
                <th>Student Name</th>
                <th>Surgery Time</th>
                <th>Surgery Type</th>
                <?php if ($isAdmin): ?>
                    <th>Assigned Tutor</th>
                <?php endif; ?>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($surgeries)): ?>
                <?php foreach ($surgeries as $surgery): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($surgery['StudentName']); ?></td>
                        <td><?php echo htmlspecialchars($surgery['SurgeryTime']); ?></td>
                        <td><?php echo htmlspecialchars($surgery['SurgeryType']); ?></td>
                        <?php if ($isAdmin): ?>
                            <td><?php echo htmlspecialchars($surgery['TutorName']); ?></td>
                        <?php endif; ?>
                        <td class="appt-cell">
                            <a href="edit_appointment.php?type=surgery&patient_id=<?php echo $surgery['StudentID']; ?>&time=<?php echo urlencode($surgery['SurgeryTime']); ?>">Edit</a> |
                            <a href="delete_appointment.php?type=surgery&patient_id=<?php echo $surgery['StudentID']; ?>&time=<?php echo urlencode($surgery['SurgeryTime']); ?>" 
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
                <th>Student Name</th>
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
                        <td><?php echo htmlspecialchars($lab['StudentName']); ?></td>
                        <td><?php echo htmlspecialchars($lab['LabTime']); ?></td>
                        <td><?php echo htmlspecialchars($lab['LabType']); ?></td>
                        <td><?php echo htmlspecialchars($lab['ClinicLocation']); ?></td>
                        <td class="appt-cell">
                            <a href="edit_appointment.php?type=lab&patient_id=<?php echo $lab['StudentID']; ?>&time=<?php echo urlencode($lab['LabTime']); ?>">Edit</a> |
                            <a href="delete_appointment.php?type=lab&patient_id=<?php echo $lab['StudentID']; ?>&time=<?php echo urlencode($lab['LabTime']); ?>" 
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
                <th>Student Name</th>
                <th>Check-Up Time</th>
                <th>Check-Up Reason</th>
                <?php if ($isAdmin): ?>
                    <th>Assigned Tutor</th>
                <?php endif; ?>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($checkups)): ?>
                <?php foreach ($checkups as $checkup): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($checkup['StudentName']); ?></td>
                        <td><?php echo htmlspecialchars($checkup['CheckTime']); ?></td>
                        <td><?php echo htmlspecialchars($checkup['CheckReason']); ?></td>
                        <?php if ($isAdmin): ?>
                            <td><?php echo htmlspecialchars($checkup['TutorName']); ?></td>
                        <?php endif; ?>
                        <td class="appt-cell">
                            <a href="edit_appointment.php?type=checkup&patient_id=<?php echo $checkup['StudentID']; ?>&time=<?php echo urlencode($checkup['CheckTime']); ?>">Edit</a> |
                            <a href="delete_appointment.php?type=checkup&patient_id=<?php echo $checkup['StudentID']; ?>&time=<?php echo urlencode($checkup['CheckTime']); ?>" 
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
