<?php
session_start(); 
require 'db_connect.php';
include 'header.php';

// Check if a tutor is logged in
if (!isset($_SESSION['tutor_id'])) {
    header("Location: login.php");
    exit();
}

$tutor_id = $_SESSION['tutor_id'];

$stmt = $pdo->prepare("SELECT Student.* FROM Student JOIN TutorStudent ON Student.StudentID = TutorStudent.StudentID WHERE TutorStudent.TutorID = :tutor_id");
$stmt->execute(['tutor_id' => $tutor_id]);
$students = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Appointment</title>
    <link rel="stylesheet" href="student_management_style.css">
    <script>
        function toggleAppointmentType() {
            const appointmentType = document.querySelector('input[name="appointment_type"]:checked')?.value;

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

            if (appointmentType === "surgery") {
                surgeryFields.style.display = "block";
            } else if (appointmentType === "lab") {
                labFields.style.display = "block";
            } else if (appointmentType === "checkup") {
                checkupFields.style.display = "block";
            }
        }
    </script>
</head>
<body onload="toggleAppointmentType();">
    <div class="container">
        <h2>Schedule an Appointment</h2>
        <form action="submit_appointment.php" method="POST" class="form">

        <div id="student_field">
            <label for="student_id" class="form-label">Select Student:</label>
            <select id="student_id" name="student_id" required class="form-select">
                <option value="">-- Select a Student --</option>
                <?php foreach ($students as $student): ?>
                    <option value="<?php echo htmlspecialchars($student['StudentID']); ?>">
                        <?php echo htmlspecialchars($student['StudentName']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <br><br>

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

            <!-- Hidden field to pass the logged-in tutor ID -->
            <input type="hidden" name="tutor_id" value="<?php echo htmlspecialchars($tutor_id); ?>">

            <input type="submit" value="Schedule Appointment" class="button">
        </form>
    </div>
</body>
</html>
