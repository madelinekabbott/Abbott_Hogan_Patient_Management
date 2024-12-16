<?php

namespace Zubair\TestProject;

use PHPUnit\Framework\TestCase;

final class manage_doctor_patients_Test extends TestCase
{


<?php
session_start();
require 'db_connect.php';
include 'header.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$doctor_id = $_GET['doctor_id'] ?? null;

if (!$doctor_id) {
    echo "No doctor specified.";
    exit();
}

$stmt = $pdo->prepare("SELECT DoctorName FROM Doctor WHERE DoctorID = :doctor_id");
$stmt->execute(['doctor_id' => $doctor_id]);
$doctor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$doctor) {
    echo "No doctor found with the specified ID.";
    exit();
}

$allPatientsStmt = $pdo->query("SELECT PatientID, PatientName FROM Patient ORDER BY PatientName ASC");
$all_patients = $allPatientsStmt->fetchAll(PDO::FETCH_ASSOC);

$assignedPatientsStmt = $pdo->prepare("
    SELECT PatientID 
    FROM DoctorPatient 
    WHERE DoctorID = :doctor_id
");
$assignedPatientsStmt->execute(['doctor_id' => $doctor_id]);
$assigned_patients = $assignedPatientsStmt->fetchAll(PDO::FETCH_COLUMN, 0);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $selected_patients = $_POST['patients'] ?? [];

    $deleteStmt = $pdo->prepare("DELETE FROM DoctorPatient WHERE DoctorID = :doctor_id");
    $deleteStmt->execute(['doctor_id' => $doctor_id]);

    $insertStmt = $pdo->prepare("INSERT INTO DoctorPatient (DoctorID, PatientID) VALUES (:doctor_id, :patient_id)");
    foreach ($selected_patients as $patient_id) {
        $insertStmt->execute(['doctor_id' => $doctor_id, 'patient_id' => $patient_id]);
    }

    header("Location: manage_doctor_patients.php?doctor_id=".$doctor_id."&updated=true");
    exit();
}

$updated = isset($_GET['updated']) && $_GET['updated'] === 'true';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Patients for Dr. <?php echo htmlspecialchars($doctor['DoctorName']); ?></title>
    <link rel="stylesheet" href="patient_management_style.css">
</head>
<body>
    <div class="container">
        <h2>Manage Patients for Dr. <?php echo htmlspecialchars($doctor['DoctorName']); ?></h2>
        <?php if ($updated): ?>
            <p class="success">Patient assignments have been successfully updated!</p>
        <?php endif; ?>
        
        <form method="POST" action="">
            <p>Select the patients you want to assign to Dr. <?php echo htmlspecialchars($doctor['DoctorName']); ?>:</p>
            <div class="checkbox-container">
                <?php foreach ($all_patients as $patient): ?>
                    <div>
                        <input type="checkbox" name="patients[]" id="patient_<?php echo $patient['PatientID']; ?>" 
                            value="<?php echo $patient['PatientID']; ?>" 
                            <?php echo in_array($patient['PatientID'], $assigned_patients) ? 'checked' : ''; ?>>
                        <label for="patient_<?php echo $patient['PatientID']; ?>">
                            <?php echo htmlspecialchars($patient['PatientName']); ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
            <br>
            <button type="submit" class="button">Update Patient Assignments</button>
        </form>
        <br>
        <a href="manage_doctors.php?doctor_id=<?php echo urlencode($doctor_id); ?>" class="button">Back to Manage Doctor</a>
    </div>
</body>
</html>
}