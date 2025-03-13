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
    $homeworkhelp_stmt = $pdo->query("
        SELECT 
            HomeworkHelp.StudentID, 
            Student.StudentName, 
            HomeworkHelp.HwTime, 
            HomeworkHelp.HwType,
            Tutor.TutorName
        FROM HomeworkHelp
        JOIN Student ON HomeworkHelp.StudentID = Student.StudentID
        JOIN Tutor ON HomeworkHelp.TutorID = Tutor.TutorID
        WHERE HomeworkHelp.HwTime $timeCondition
        ORDER BY HomeworkHelp.HwTime DESC
    ");
    $homeworkhelp = $homeworkhelp_stmt->fetchAll();

    $testprep_stmt = $pdo->query("
        SELECT 
            TestPrep.StudentID, 
            Student.StudentName, 
            TestPrep.TestPrepTime, 
            TestPrep.TestPrepType, 
            TestPrep.TutorLocation
        FROM TestPrep
        JOIN Student ON TestPrep.StudentID = Student.StudentID
        WHERE TestPrep.TestPrepTime $timeCondition
        ORDER BY TestPrep.TestPrepTime DESC
    ");
    $tesprep = $testprep_stmt->fetchAll();

    $meetup_stmt = $pdo->query("
        SELECT 
            MeetUp.StudentID, 
            Student.StudentName, 
            MeetUp.MeetUpTime, 
            MeetUp.MeetUpReason,
            Tutor.TutorName
        FROM MeetUp
        JOIN Student ON MeetUp.StudentID = Student.StudentID
        JOIN Tutor ON MeetUp.TutorID = Tutor.TutorID
        WHERE MeetUp.MeetUpTime $timeCondition
        ORDER BY MeetUp.MeetUpTime DESC
    ");
    $meetups = $meetup_stmt->fetchAll();

} else {
    $tutor_id = $_SESSION['tutor_id'];

    $homeworkhelp_stmt = $pdo->prepare("
        SELECT 
            HomeworkHelp.StudentID,
            Student.StudentName,
            HomeworkHelp.HwTime,
            HomeworkHelp.HwType
        FROM HomeworkHelp
        JOIN Student ON HomeworkHelp.StudentID = Student.StudentID
        WHERE HomeworkHelp.TutorID = :tutor_id
          AND HomeworkHelp.HwTime $timeCondition
        ORDER BY HomeworkHelp.HwTime DESC
    ");
    $homeworkhelp_stmt->execute(['tutor_id' => $tutor_id]);
    $homeworkhelp = $homeworkhelp_stmt->fetchAll();

    $testprep_stmt = $pdo->prepare("
        SELECT 
            TestPrep.StudentID,
            Student.StudentName,
            TestPrep.TestPrepTime,
            TestPrep.TestPrepType,
            TestPrep.TutorLocation
        FROM TestPrep
        JOIN Student ON TestPrep.StudentID = Student.StudentID
        JOIN TutorStudent ON TestPrep.StudentID = TutorStudent.StudentID
        WHERE TutorStudent.TutorID = :tutor_id
          AND TestPrep.TestPrepTime $timeCondition
        ORDER BY TestPrep.TestPrepTime DESC
    ");
    $testprep_stmt->execute(['tutor_id' => $tutor_id]);
    $testprep = $testprep_stmt->fetchAll();

    $meetup_stmt = $pdo->prepare("
        SELECT 
            MeetUp.StudentID,
            Student.StudentName,
            MeetUp.MeetUpTime,
            MeetUp.MeetUpReason
        FROM MeetUp
        JOIN Student ON MeetUp.StudentID = Student.StudentID
        WHERE MeetUp.TutorID = :tutor_id
          AND MeetUp.MeetUpTime $timeCondition
        ORDER BY MeetUp.MeetUpTime DESC
    ");
    $meetup_stmt->execute(['tutor_id' => $tutor_id]);
    $meetup = $meetup_stmt->fetchAll();
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

    <!-- Homework Help Table -->
    <h3>Homework Help</h3>
    <table class="appt-table">
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Homework Time</th>
                <th>Homework Type</th>
                <?php if ($isAdmin): ?>
                    <th>Assigned Tutor</th>
                <?php endif; ?>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($homeworkhelp)): ?>
                <?php foreach ($homeworkhelp as $homeworkhelp): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($homeworkhelp['StudentName']); ?></td>
                        <td><?php echo htmlspecialchars($homeworkhelp['HwTime']); ?></td>
                        <td><?php echo htmlspecialchars($homeworkhelp['HwType']); ?></td>
                        <?php if ($isAdmin): ?>
                            <td><?php echo htmlspecialchars($homeworkhelp['TutorName']); ?></td>
                        <?php endif; ?>
                        <td class="appt-cell">
                            <a href="edit_appointment.php?type=homeworkhelp&student_id=<?php echo $homeworkhelp['StudentID']; ?>&time=<?php echo urlencode($homeworkhelp['HwTime']); ?>">Edit</a> |
                            <a href="delete_appointment.php?type=homeworkhelp&student_id=<?php echo $homeworkhelp['StudentID']; ?>&time=<?php echo urlencode($homeworkhelp['HwTime']); ?>" 
                               onclick="return confirm('Are you sure you want to delete this appointment?');">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?php echo $isAdmin ? '5' : '4'; ?>">No scheduled homework help.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Test Prep Table -->
    <h3>Test Prep Appointments</h3>
    <table class="appt-table">
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Test Prep Time</th>
                <th>Test Prep Type</th>
                <th>Tutor Location</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($testprep)): ?>
                <?php foreach ($testprep as $testprep): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($testprep['StudentName']); ?></td>
                        <td><?php echo htmlspecialchars($testprep['TestPrepTime']); ?></td>
                        <td><?php echo htmlspecialchars($testprep['TestPrepType']); ?></td>
                        <td><?php echo htmlspecialchars($testprep['TutorLocation']); ?></td>
                        <td class="appt-cell">
                            <a href="edit_appointment.php?type=testprep&student_id=<?php echo $testprep['StudentID']; ?>&time=<?php echo urlencode($testprep['TestPrepTime']); ?>">Edit</a> |
                            <a href="delete_appointment.php?type=testprep&student_id=<?php echo $testprep['StudentID']; ?>&time=<?php echo urlencode($testprep['TestPrepTime']); ?>" 
                               onclick="return confirm('Are you sure you want to delete this appointment?');">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No Test Prep appointments found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Meet-Ups Table -->
    <h3>Meet-Ups</h3>
    <table class="appt-table">
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Meet-Up Time</th>
                <th>Meet-Up Reason</th>
                <?php if ($isAdmin): ?>
                    <th>Assigned Tutor</th>
                <?php endif; ?>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($meetup)): ?>
                <?php foreach ($meetup as $meetup): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($meetup['StudentName']); ?></td>
                        <td><?php echo htmlspecialchars($meetup['MeetUpTime']); ?></td>
                        <td><?php echo htmlspecialchars($meetup['MeetUpReason']); ?></td>
                        <?php if ($isAdmin): ?>
                            <td><?php echo htmlspecialchars($meetup['TutorName']); ?></td>
                        <?php endif; ?>
                        <td class="appt-cell">
                            <a href="edit_appointment.php?type=meetup&student_id=<?php echo $meetup['StudentID']; ?>&time=<?php echo urlencode($meetup['MeetUpTime']); ?>">Edit</a> |
                            <a href="delete_appointment.php?type=meetup&student_id=<?php echo $meetup['StudentID']; ?>&time=<?php echo urlencode($meetup['MeetUpTime']); ?>" 
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
