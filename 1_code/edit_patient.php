<?php
session_start();
require 'db_connect.php';
include 'header.php';

$isAdmin = isset($_SESSION['admin_id']);
$isDoctor = isset($_SESSION['doctor_id']);

if (!$isAdmin && !$isDoctor) {
    header("Location: login.php");
    exit();
}

$patient_id = $_GET['patient_id'] ?? null;

if (!$patient_id) {
    echo "No patient specified.";
    exit();
}

try {
    if ($isDoctor) {
        $doctor_id = $_SESSION['doctor_id'];
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
    } else {
        $stmt = $pdo->prepare("
            SELECT 
                PatientName, DOB, Address, City, 
                ContactNumber, PatientInformation, 
                Prescriptions, Diagnoses, PreferredPharmacy
            FROM 
                Patient
            WHERE 
                Patient.PatientID = :patient_id
        ");
        $stmt->execute(['patient_id' => $patient_id]);
    }

    $patient = $stmt->fetch();

    if (!$patient) {
        echo "Patient record not found or access is restricted.";
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patientName = $_POST['patient_name'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $contactNumber = $_POST['contact_number'];
    $patientInformation = $_POST['patient_information'];
    $prescriptions = $_POST['prescriptions'];
    $diagnoses = $_POST['diagnoses'];
    $preferredPharmacy = $_POST['preferred_pharmacy'];

    try {
        $updateStmt = $pdo->prepare("
            UPDATE Patient 
            SET PatientName = :patient_name, DOB = :dob, Address = :address, 
                City = :city, ContactNumber = :contact_number, 
                PatientInformation = :patient_information, Prescriptions = :prescriptions, 
                Diagnoses = :diagnoses, PreferredPharmacy = :preferred_pharmacy 
            WHERE PatientID = :patient_id
        ");
        
        $updateStmt->execute([
            'patient_name' => $patientName,
            'dob' => $dob,
            'address' => $address,
            'city' => $city,
            'contact_number' => $contactNumber,
            'patient_information' => $patientInformation,
            'prescriptions' => $prescriptions,
            'diagnoses' => $diagnoses,
            'preferred_pharmacy' => $preferredPharmacy,
            'patient_id' => $patient_id
        ]);

        header("Location: view_patient.php?patient_id=" . $patient_id . "&updated=true");
        exit();
    } catch (PDOException $e) {
        echo "Database Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Patient</title>
    <link rel="stylesheet" href="patient_management_style.css"> 
</head>
<body>
    <div class="container">
        <h2 class="header">Edit Patient Information</h2>
        <form action="" method="post">
            <div class="form-group text">
                <label for="patient_name">Patient Name:</label>
                <input type="text" name="patient_name" id="patient_name" value="<?php echo htmlspecialchars($patient['PatientName']); ?>" required>
            </div>
            <div class="form-group text">
                <label for="dob">Date of Birth:</label>
                <input type="date" name="dob" id="dob" value="<?php echo htmlspecialchars($patient['DOB']); ?>" required>
            </div>
            <div class="form-group text">
                <label for="address">Address:</label>
                <input type="text" name="address" id="address" value="<?php echo htmlspecialchars($patient['Address']); ?>" required>
            </div>
            <div class="form-group text">
                <label for="city">City:</label>
                <input type="text" name="city" id="city" value="<?php echo htmlspecialchars($patient['City']); ?>" required>
            </div>
            <div class="form-group text">
                <label for="contact_number">Contact Number:</label>
                <input type="text" name="contact_number" id="contact_number" value="<?php echo htmlspecialchars($patient['ContactNumber']); ?>" required>
            </div>
            <div class="form-group text">
                <label for="patient_information">Additional Information:</label>
                <textarea name="patient_information" id="patient_information" rows="4"><?php echo htmlspecialchars($patient['PatientInformation']); ?></textarea>
            </div>
            <div class="form-group text">
                <label for="prescriptions">Prescriptions:</label>
                <textarea name="prescriptions" id="prescriptions" rows="4"><?php echo htmlspecialchars($patient['Prescriptions']); ?></textarea>
            </div>
            <div class="form-group text">
                <label for="diagnoses">Diagnoses:</label>
                <textarea name="diagnoses" id="diagnoses" rows="4"><?php echo htmlspecialchars($patient['Diagnoses']); ?></textarea>
            </div>
            <div class="form-group text">
                <label for="preferred_pharmacy">Preferred Pharmacy:</label>
                <input type="text" name="preferred_pharmacy" id="preferred_pharmacy" value="<?php echo htmlspecialchars($patient['PreferredPharmacy']); ?>">
            </div>
            <div class="form-group text">
                <button type="submit" class="button">Update Patient Information</button>
            </div>
        </form>
        <a href="view_patient.php?patient_id=<?php echo $patient_id; ?>" class="button">Cancel</a>
    </div>
</body>
</html>