<?php

namespace Zubair\TestProject;

use PHPUnit\Framework\TestCase;

final class doctor_dashboard_Test extends TestCase
{

<?php
session_start();
if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

require 'db_connect.php';
include 'header.php';

$stmt = $pdo->prepare("SELECT Patient.* FROM Patient 
                        JOIN DoctorPatient ON Patient.PatientID = DoctorPatient.PatientID 
                        WHERE DoctorPatient.DoctorID = :doctor_id");
$stmt->execute(['doctor_id' => $_SESSION['doctor_id']]);
$patients = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
    <link rel="stylesheet" href="patient_management_style.css">
</head>
<body>
    <div class="container">
        <h2>Welcome, Dr. <?php echo htmlspecialchars($_SESSION['doctor_name']); ?>!</h2>
        <p class="text">Select an option below:</p>
        <ul class="options">
            <li><a href="schedule_appointment.php">Schedule an Appointment</a></li>
            <li><a href="view_records.php">View Patient Records</a></li>
            <li><a href="view_appointments.php">View Scheduled Appointments</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>

        <h3>Your Patients:</h3>
        <ul class="patients">
            <?php foreach ($patients as $patient): ?>
                <li>
                    <?php echo htmlspecialchars($patient['PatientName']); ?> - 
                    <?php echo htmlspecialchars($patient['ContactNumber']); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
}