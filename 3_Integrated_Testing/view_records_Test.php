<?php

namespace Zubair\TestProject;

use PHPUnit\Framework\TestCase;

final class view_records_Test extends TestCase
{
    // Tests will go here
}

<?php
session_start();
require 'db_connect.php';
include 'header.php';

if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];

$stmt = $pdo->prepare("
    SELECT 
        Patient.PatientID, Patient.PatientName, Patient.DOB, Patient.Address, Patient.City 
    FROM 
        Patient 
    JOIN 
        DoctorPatient ON Patient.PatientID = DoctorPatient.PatientID
    WHERE 
        DoctorPatient.DoctorID = :doctor_id
");
$stmt->execute(['doctor_id' => $doctor_id]);
$patients = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Records</title>
    <link rel="stylesheet" href="patient_management_style.css">
</head>
<body>
    <div class="container">
    <h2 class="header">Patient Records</h2>
    <table class="table">
        <thead>
            <tr>
                <th class="table-header">Name</th>
                <th class="table-header">Date of Birth</th>
                <th class="table-header">Address</th>
                <th class="table-header">City</th>
                <th class="table-header"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($patients as $patient): ?>
                <tr>
                    <td class="table-cell"><?php echo htmlspecialchars($patient['PatientName']); ?></td>
                    <td class="table-cell"><?php echo htmlspecialchars($patient['DOB']); ?></td>
                    <td class="table-cell"><?php echo htmlspecialchars($patient['Address']); ?></td>
                    <td class="table-cell"><?php echo htmlspecialchars($patient['City']); ?></td>
                    <td class="table-cell">
                        <a href="view_patient.php?patient_id=<?php echo $patient['PatientID']; ?>" class="button-link">
                            <button class="button">View Details</button>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <br>
    <a href="logout.php" class="link">Logout</a>
</div>
</body>
</html>
