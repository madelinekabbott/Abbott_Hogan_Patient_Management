<?php
session_start();
require 'db_connect.php';
include 'header.php';

$isAdmin = isset($_SESSION['admin_id']);
$isTutor = isset($_SESSION['tutor_id']);
$scheduleUrl = $isAdmin ? "admin_schedule_appointment.php" : "schedule_appointment.php";

if (!$isAdmin && !$isTutor) {
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Success</title>
    <link rel="stylesheet" href="student_management_style.css">
</head>
<body>
    <div class = "container">
    <h2 class="header">Appointment Scheduled Successfully!</h2>
    <p class="message">Your appointment has been created successfully.</p>
    <div class="button-container">
        <a href="<?php echo $scheduleUrl; ?>" class="button-link">
            <button class="button">Schedule Another Appointment</button>
        </a>
        <a href="view_appointments.php" class="button-link">
            <button class="button">View Scheduled Appointments</button>
        </a>
    </div>
    <a href="logout.php" class="link">Logout</a>
</div>
</body>
</html>
