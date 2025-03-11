<?php
session_start();
if (!isset($_SESSION['tutor_id'])) {
    header("Location: login.php");
    exit();
}

require 'db_connect.php';
include 'header.php';

$stmt = $pdo->prepare("SELECT Student.* FROM Student 
                        JOIN TutorStudent ON Student.StudentID = TutorStudent.StudentID 
                        WHERE TutorStudent.TutorID = :tutor_id");
$stmt->execute(['doctor_id' => $_SESSION['tutor_id']]);
$patients = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutor Dashboard</title>
    <link rel="stylesheet" href="student_management_style.css">
</head>
<body>
    <div class="container">
        <h2>Welcome, Tutor. <?php echo htmlspecialchars($_SESSION['tutor_name']); ?>!</h2>
        <p class="text">Select an option below:</p>
        <ul class="options">
            <li><a href="schedule_appointment.php">Schedule an Appointment</a></li>
            <li><a href="view_records.php">View Patient Records</a></li>
            <li><a href="view_appointments.php">View Scheduled Appointments</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>

        <h3>Your Students:</h3>
        <ul class="students">
            <?php foreach ($students as $student): ?>
                <li>
                    <?php echo htmlspecialchars($patient['StudentName']); ?> - 
                    <?php echo htmlspecialchars($patient['ContactNumber']); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
