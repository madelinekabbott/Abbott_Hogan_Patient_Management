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

            // Homework Help Fields
            const homeworkhelpFields = document.getElementById("homework_help_fields");

            // Test Prep Fields
            const testprepFields = document.getElementById("test_prep_fields");

            // MeetUp Fields
            const meetupFields = document.getElementById("meetUp_fields");

            // Hide all by default
            homeworkhelpFields.style.display = "none";
            testprepFields.style.display = "none";
            meetupFields.style.display = "none";

            if (appointmentType === "homeworkhelp") {
                homeworkhelpFields.style.display = "block";
            } else if (appointmentType === "testprep") {
                testprepFields.style.display = "block";
            } else if (appointmentType === "meetup") {
                meetupFields.style.display = "block";
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
            <input type="radio" id="homeworkhelp" name="appointment_type" value="homeworkhelp" onclick="toggleAppointmentType()">
            <label for="homeworkhelp">Homework Help</label><br>
            <input type="radio" id="testprep" name="appointment_type" value="testprep" onclick="toggleAppointmentType()">
            <label for="testprep">Test Prep</label><br>
            <input type="radio" id="meetup" name="appointment_type" value="meetup" onclick="toggleAppointmentType()">
            <label for="meetup">Meet Up</label><br><br>

            <!-- Homework Help Fields -->
            <div id="homework_help_fields" style="display: none;">
                <label for="Hw_time" class="form-label">Select Date and Time:</label>
                <input type="datetime-local" id="Hw_time" name="Hw_time" class="form-input" min="<?php echo date('Y-m-d\TH:i'); ?>">
                <br><br>
                <label for="Hw_type" class="form-label">Homework Type:</label>
                <input type="text" id="Hw_type" name="Hw_type" class="form-input">
                <br><br>
            </div>

            <!-- Test Prep Fields -->
            <div id="test_prep_fields" style="display: none;">
                <label for="testprep_time" class="form-label">Select Date and Time:</label>
                <input type="datetime-local" id="testprep_time" name="testprep_time" class="form-input" min="<?php echo date('Y-m-d\TH:i'); ?>">
                <br><br>
                <label for="testprep_type" class="form-label">Test Prep Type:</label>
                <input type="text" id="testprep_type" name="testprep_type" class="form-input">
                <br><br>
                <label for="tutor_location" class="form-label">Tutor Location:</label>
                <input type="text" id="tutor_location" name="tutor_location" class="form-input">
                <br><br>
            </div>

            <!-- Meet-up Fields -->
            <div id="meetup_fields" style="display: none;">
                <label for="meetup_time" class="form-label">Select Date and Time:</label>
                <input type="datetime-local" id="meetup_time" name="meetup_time" class="form-input" min="<?php echo date('Y-m-d\TH:i'); ?>">
                <br><br>
                <label for="meetup_reason" class="form-label">Meet-up Reason:</label>
                <input type="text" id="meetup_reason" name="meetup_reason" class="form-input">
                <br><br>
            </div>

            <!-- Hidden field to pass the logged-in tutor ID -->
            <input type="hidden" name="tutor_id" value="<?php echo htmlspecialchars($tutor_id); ?>">

            <input type="submit" value="Schedule Appointment" class="button">
        </form>
    </div>
</body>
</html>
