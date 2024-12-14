<?php
session_start(); 
require 'db_connect.php';
include 'header.php';

// Check if admin or doctor is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}


$stmt = $pdo->query("SELECT * FROM Patient ORDER BY PatientName ASC");
$patients = $stmt->fetchAll();

$doctors = [];
    $doctorStmt = $pdo->query("SELECT DoctorID, DoctorName FROM Doctor ORDER BY DoctorName ASC");
    $doctors = $doctorStmt->fetchAll();
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
            const appointmentType = document.querySelector('input[name="appointment_type"]:checked')?.value;
            const doctorField = document.getElementById("doctor_field");

            // Surgery Fields
            const surgeryFields = document.getElementById("surgery_fields");

            // Lab Fields
            const labFields = document.getElementById("lab_fields");

            // Checkup Fields
            const checkupFields = document.getElementById("checkup_fields");

            // Hide all by default
            surgeryFields.style.display = "none";
            labFields.style.display = "none";
            checkupFields.style.display = "none";
            doctorField.style.display = "none";

            if (appointmentType === "surgery") {
                surgeryFields.style.display = "block";
                doctorField.style.display = "block";
            } else if (appointmentType === "lab") {
                labFields.style.display = "block";
            } else if (appointmentType === "checkup") {
                checkupFields.style.display = "block";
                doctorField.style.display = "block";
            }
        }

        document.addEventListener("DOMContentLoaded", () => {
        document.getElementById("doctor_id").addEventListener("change", function() {
            const doctorId = this.value;
            const patientDropdown = document.getElementById("patient_id");

            if (doctorId) {
                fetch(`fetch_patients.php?doctor_id=${doctorId}`)
                    .then(response => response.json())
                    .then(data => {
                        patientDropdown.innerHTML = '<option value="">-- Select a Patient --</option>';
                        
                        data.forEach(patient => {
                            const option = document.createElement("option");
                            option.value = patient.PatientID;
                            option.textContent = patient.PatientName;
                            patientDropdown.appendChild(option);
                        });
                    })
                    .catch(error => console.error("Error fetching patients:", error));
            } else {
                patientDropdown.innerHTML = '<option value="">-- Select a Patient --</option>';
            }
            });
        });
    </script>
</head>
<body onload="toggleAppointmentType();">
    <div class="container">
        <h2>Schedule an Appointment</h2>
        <form action="submit_appointment.php" method="POST" class="form">

        <div id="patient_field">
            <label for="patient_id" class="form-label">Select Patient:</label>
            <select id="patient_id" name="patient_id" required class="form-select">
                <option value="">-- Select a Patient --</option>
                <?php foreach ($patients as $patient): ?>
                    <option value="<?php echo htmlspecialchars($patient['PatientID']); ?>">
                        <?php echo htmlspecialchars($patient['PatientName']); ?>
                    </option>
                 <?php endforeach; ?>
            </select>
        </div>
        <br><br>


            <div id="doctor_field" style="display: none;">
                <label for="doctor_id" class="form-label">Select Doctor:</label>
                <select id="doctor_id" name="doctor_id" class="form-select">
                    <option value="">-- Select a Doctor --</option>
                    <?php foreach ($doctors as $doc): ?>
                        <option value="<?php echo htmlspecialchars($doc['DoctorID']); ?>">
                            <?php echo htmlspecialchars($doc['DoctorName']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <br><br>
            </div>

            <label class="form-label">Appointment Type:</label><br>
            <input type="radio" id="surgery" name="appointment_type" value="surgery" onclick="toggleAppointmentType()">
            <label for="surgery">Surgery</label><br>
            <input type="radio" id="lab" name="appointment_type" value="lab" onclick="toggleAppointmentType()">
            <label for="lab">Lab Test</label><br>
            <input type="radio" id="checkup" name="appointment_type" value="checkup" onclick="toggleAppointmentType()">
            <label for="checkup">Check Up</label><br><br>

            <!-- Surgery Fields -->
            <div id="surgery_fields" style="display: none;">
                <label for="surgery_time" class="form-label">Select Date and Time:</label>
                <input type="datetime-local" id="surgery_time" name="surgery_time" class="form-input" min="<?php echo date('Y-m-d\TH:i'); ?>">
                <br><br>
                <label for="surgery_type" class="form-label">Surgery Type:</label>
                <input type="text" id="surgery_type" name="surgery_type" class="form-input">
                <br><br>
            </div>

            <!-- Lab Fields -->
            <div id="lab_fields" style="display: none;">
                <label for="lab_time" class="form-label">Select Date and Time:</label>
                <input type="datetime-local" id="lab_time" name="lab_time" class="form-input" min="<?php echo date('Y-m-d\TH:i'); ?>">
                <br><br>
                <label for="lab_type" class="form-label">Lab Test Type:</label>
                <input type="text" id="lab_type" name="lab_type" class="form-input">
                <br><br>
                <label for="clinic_location" class="form-label">Clinic Location:</label>
                <input type="text" id="clinic_location" name="clinic_location" class="form-input">
                <br><br>
            </div>

            <!-- Check-up Fields -->
            <div id="checkup_fields" style="display: none;">
                <label for="checkup_time" class="form-label">Select Date and Time:</label>
                <input type="datetime-local" id="checkup_time" name="checkup_time" class="form-input" min="<?php echo date('Y-m-d\TH:i'); ?>">
                <br><br>
                <label for="checkup_reason" class="form-label">Check-up Reason:</label>
                <input type="text" id="checkup_reason" name="checkup_reason" class="form-input">
                <br><br>
            </div>

            <input type="submit" value="Schedule Appointment" class="button">
        </form>
    </div>
</body>
</html>
