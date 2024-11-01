<?php
session_start();
if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

include 'header.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Success</title>
    <link rel="stylesheet" href="patient_management_style.css">
</head>
<body>
    <div class = "container">
    <h2 class="header">Appointment Scheduled Successfully!</h2>
    <p class="message">Your appointment has been created successfully.</p>
    <div class="button-container">
        <a href="schedule_appointment.php" class="button-link">
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
