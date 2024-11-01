<?php
session_start();
if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

require 'db_connect.php';
include 'header.php';

// Get the list of patients for the logged-in doctor
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
    <title>Schedule Appointment</title>
    <link rel="stylesheet" href="patient_management_style.css">
    <script>
        function toggleAppointmentType() {
            const appointmentType = document.getElementById("appointment_type").value;
            document.getElementById("surgery_fields").style.display = appointmentType === "surgery" ? "block" : "none";
            document.getElementById("lab_fields").style.display = appointmentType === "lab" ? "block" : "none";
            document.getElementById("checkup_fields").style.display = appointmentType === "checkup" ? "block" : "none";
        }
    </script>
</head>
<body>
    <div class="container">
    <h2>Schedule an Appointment</h2>
    <form action="submit_appointment.php" method="POST" class="form">
        <label for="patient_id" class="form-label">Select Patient:</label>
        <select id="patient_id" name="patient_id" required class="form-select">
            <?php foreach ($patients as $patient): ?>
                <option value="<?php echo htmlspecialchars($patient['PatientID']); ?>">
                    <?php echo htmlspecialchars($patient['PatientName']); ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="appointment_type" class="form-label">Select Appointment Type:</label>
        <select id="appointment_type" name="appointment_type" onchange="toggleAppointmentType()" required class="form-select">
            <option value="">--Select--</option>
            <option value="surgery">Surgery</option>
            <option value="lab">Lab Test</option>
            <option value="checkup">Check Up</option>
        </select><br><br>

        <!-- Surgery Fields -->
        <div id="surgery_fields" class="appointment-fields" style="display: none;">
            <label for="surgery_time" class="form-label">Select Date and Time:</label>
            <input type="datetime-local" id="surgery_time" name="surgery_time" class="form-input"><br><br>
            <label for="surgery_type" class="form-label">Surgery Type:</label>
            <input type="text" id="surgery_type" name="surgery_type" class="form-input"><br><br>
        </div>

        <!-- Lab Fields -->
        <div id="lab_fields" class="appointment-fields" style="display: none;">
            <label for="lab_time" class="form-label">Select Date and Time:</label>
            <input type="datetime-local" id="lab_time" name="lab_time" class="form-input"><br><br>
            <label for="lab_type" class="form-label">Lab Test Type:</label>
            <input type="text" id="lab_type" name="lab_type" class="form-input"><br><br>
            <label for="clinic_location" class="form-label">Clinic Location:</label>
            <input type="text" id="clinic_location" name="clinic_location" class="form-input"><br><br>
        </div>

        <!-- Check-up Fields -->
        <div id="checkup_fields" class="appointment-fields" style="display: none;">
            <label for="checkup_time" class="form-label">Select Date and Time:</label>
            <input type="datetime-local" id="checkup_time" name="checkup_time" class="form-input"><br><br>
            <label for="checkup_reason" class="form-label">Check-up Reason:</label>
            <input type="text" id="checkup_reason" name="checkup_reason" class="form-input"><br><br>
        </div>

        <input type="submit" value="Schedule Appointment" class="button">
    </form>
            </div>
</body>
</html>
