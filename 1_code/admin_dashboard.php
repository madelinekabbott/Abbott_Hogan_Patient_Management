<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require 'db_connect.php';
include 'header.php';

$stmt = $pdo->prepare("SELECT * FROM Patient");
$stmt->execute();
$patients = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="patient_management_style.css">
</head>
<body>
    <div class="container">
        <h2>Welcome, Admin <?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</h2>
        <p class="text">Select an option below:</p>
        <ul class="options">
            <li><a href="manage_doctors.php">Manage Doctors</a></li>
            <li><a href="manage_patients.php">Manage Patients</a></li>
            <li><a href="view_appointments.php">View All Appointments</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>

        <h3>All Patients:</h3>
        <ul class="patients">
            <?php if ($patients): ?>
                <?php foreach ($patients as $patient): ?>
                    <li>
                        <?php echo htmlspecialchars($patient['PatientName']); ?> - 
                        <?php echo htmlspecialchars($patient['ContactNumber']); ?>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No patients found.</li>
            <?php endif; ?>
        </ul>
    </div>
</body>
</html>
