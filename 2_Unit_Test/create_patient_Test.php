<?php

namespace Zubair\TestProject;

use PHPUnit\Framework\TestCase;

final class create_patient_Test extends TestCase
{

<?php
session_start();
require 'db_connect.php';
include 'header.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$errorMessage = ""; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patientId          = $_POST['patient_id'];
    $patientName        = $_POST['patient_name'];
    $address            = $_POST['address'];
    $city               = $_POST['city'];
    $contactNumber      = $_POST['contact_number'];
    $dob                = $_POST['dob'];
    $patientInformation = $_POST['patient_information'] ?? null;
    $prescriptions      = $_POST['prescriptions'] ?? null;
    $diagnoses          = $_POST['diagnoses'] ?? null;
    $preferredPharmacy  = $_POST['preferred_pharmacy'] ?? null;

    $stmt = $pdo->prepare("
        INSERT INTO Patient (
            PatientID, 
            PatientName, 
            Address, 
            City, 
            ContactNumber, 
            DOB, 
            PatientInformation, 
            Prescriptions, 
            Diagnoses, 
            PreferredPharmacy
        ) VALUES (
            :patient_id, 
            :patient_name, 
            :address, 
            :city, 
            :contact_number, 
            :dob, 
            :patient_information, 
            :prescriptions, 
            :diagnoses, 
            :preferred_pharmacy
        )
    ");

    try {
        $stmt->execute([
            'patient_id'           => $patientId,
            'patient_name'         => $patientName,
            'address'              => $address,
            'city'                 => $city,
            'contact_number'       => $contactNumber,
            'dob'                  => $dob,
            'patient_information'  => $patientInformation,
            'prescriptions'        => $prescriptions,
            'diagnoses'            => $diagnoses,
            'preferred_pharmacy'   => $preferredPharmacy
        ]);

        header("Location: manage_patients.php?created=true");
        exit();
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $errorMessage = "That PatientID is already in use.";
        } else {
            $errorMessage = "Error inserting patient: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Patient</title>
    <link rel="stylesheet" href="patient_management_style.css">
</head>
<body>
    <?php if (!empty($errorMessage)): ?>
        <script>alert("<?php echo htmlspecialchars($errorMessage); ?>");</script>
    <?php endif; ?>

    <div class="container">
        <h2 class="header">Create New Patient</h2>
        <form action="" method="POST">
            <table class="form-table">
                <!-- Patient ID -->
                <tr>
                    <td><label for="patient_id">Patient ID:</label></td>
                    <td><input type="text" name="patient_id" id="patient_id" style="width: 200px;" required></td>
                </tr>
                <!-- Patient Name -->
                <tr>
                    <td><label for="patient_name">Patient Name:</label></td>
                    <td><input type="text" name="patient_name" id="patient_name" style="width: 200px;" required></td>
                </tr>
                <!-- Address -->
                <tr>
                    <td><label for="address">Address:</label></td>
                    <td><input type="text" name="address" id="address" style="width: 200px;" required></td>
                </tr>
                <!-- City -->
                <tr>
                    <td><label for="city">City:</label></td>
                    <td><input type="text" name="city" id="city" style="width: 200px;" required></td>
                </tr>
                <!-- Contact Number -->
                <tr>
                    <td><label for="contact_number">Contact Number:</label></td>
                    <td><input type="text" name="contact_number" id="contact_number" style="width: 200px;" required></td>
                </tr>
                <!-- Date of Birth -->
                <tr>
                    <td><label for="dob">Date of Birth:</label></td>
                    <td><input type="date" name="dob" id="dob" required></td>
                </tr>
                <!-- Patient Information (optional) -->
                <tr>
                    <td><label for="patient_information">Patient Information:</label></td>
                    <td><textarea name="patient_information" id="patient_information" rows="3"></textarea></td>
                </tr>
                <!-- Prescriptions (optional) -->
                <tr>
                    <td><label for="prescriptions">Prescriptions:</label></td>
                    <td><textarea name="prescriptions" id="prescriptions" rows="3"></textarea></td>
                </tr>
                <!-- Diagnoses (optional) -->
                <tr>
                    <td><label for="diagnoses">Diagnoses:</label></td>
                    <td><textarea name="diagnoses" id="diagnoses" rows="3"></textarea></td>
                </tr>
                <!-- Preferred Pharmacy (optional) -->
                <tr>
                    <td><label for="preferred_pharmacy">Preferred Pharmacy:</label></td>
                    <td><input type="text" name="preferred_pharmacy" id="preferred_pharmacy" style="width: 200px;"></td>
                </tr>
                <!-- Submit / Cancel -->
                <tr>
                    <td colspan="2" style="text-align:center;">
                        <button type="submit" class="button">Create Patient</button>
                        <a href="manage_patients.php" class="button">Cancel</a>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</body>
</html>
}