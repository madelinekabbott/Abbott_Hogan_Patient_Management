<!-- view_patient.php -->
<?php
session_start();
require 'db_connect.php';
include 'header.php';

if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];
$patient_id = $_GET['patient_id'] ?? null;

if (!$patient_id) {
    echo "No patient specified.";
    exit();
}

// Fetch patient details
$stmt = $pdo->prepare("
    SELECT 
        PatientName, DOB, Address, City, 
        ContactNumber, PatientInformation, 
        Prescriptions, Diagnoses, PreferredPharmacy
    FROM 
        Patient
    JOIN 
        DoctorPatient ON Patient.PatientID = DoctorPatient.PatientID
    WHERE 
        Patient.PatientID = :patient_id 
        AND DoctorPatient.DoctorID = :doctor_id
");
$stmt->execute(['patient_id' => $patient_id, 'doctor_id' => $doctor_id]);
$patient = $stmt->fetch();

if (!$patient) {
    echo "Patient record not found or access is restricted.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Patient</title>
    <link rel="stylesheet" href="patient_management_style.css"> 
</head>
<body>
    <div class="container">
        <h2>Patient Information</h2>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($patient['PatientName']); ?></p>
        <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($patient['DOB']); ?></p>
        <p><strong>Address:</strong> <?php echo htmlspecialchars($patient['Address']); ?></p>
        <p><strong>City:</strong> <?php echo htmlspecialchars($patient['City']); ?></p>
        <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($patient['ContactNumber']); ?></p>
        <p><strong>Additional Information:</strong> <?php echo htmlspecialchars($patient['PatientInformation']); ?></p>
        <p><strong>Prescriptions:</strong> <?php echo htmlspecialchars($patient['Prescriptions']); ?></p>
        <p><strong>Diagnoses:</strong> <?php echo htmlspecialchars($patient['Diagnoses']); ?></p>
        <p><strong>Preferred Pharmacy:</strong> <?php echo htmlspecialchars($patient['PreferredPharmacy']); ?></p>

        <a href="edit_patient.php?patient_id=<?php echo $patient_id; ?>" class="button">Edit Patient Information</a>
    <br>
    <a href="view_records.php" class="button">Back to Patient List</a>
    </div>
</body>
</html>
