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

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="student_management_style.css">
</head>
<body>
    <div class="container">
        <h2>Scheduling Conflict</h2>
        <p class="text">The appointment cannot be scheduled due to a scheduling conflict. Ensure that neither the tutor nor student have appointments closely before or after the appointment you're scheduling.</p>
        <div class="buttons">
            <a href="<?php echo $scheduleUrl; ?>" class="button">Try Again</a>
        </div>
    </div>
</body>
</html>

