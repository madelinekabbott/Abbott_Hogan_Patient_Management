<?php
session_start(); 
require 'db_connect.php';
include 'header.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}


$stmt = $pdo->query("SELECT * FROM Student ORDER BY StudentName ASC");
$students = $stmt->fetchAll();

$Tutors = [];
    $tutorStmt = $pdo->query("SELECT TutorID, TutorName FROM Tutor ORDER BY TutorName ASC");
    $tutors = $tutorStmt->fetchAll();
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
            const tutorField = document.getElementById("tutor_field");

            // HomeworkHelp Fields
            const homeworkhelpFields = document.getElementById("homeworkhelp_fields");

            // TestPrep Fields
            const testprepFields = document.getElementById("testprep_fields");

            // MeetUp Fields
            const meetupFields = document.getElementById("meetup_fields");

            // Hide all by default
            homeworkhelpFields.style.display = "none";
            testprepFields.style.display = "none";
            meetupFields.style.display = "none";
            tutorField.style.display = "none";

            if (appointmentType === "homeworkhelp") {
                homeworkhelpFields.style.display = "block";
                tutorField.style.display = "block";
            } else if (appointmentType === "testprep") {
                testprepFields.style.display = "block";
            } else if (appointmentType === "meetup") {
                meetupFields.style.display = "block";
                tutorField.style.display = "block";
            }
        }

        document.addEventListener("DOMContentLoaded", () => {
        document.getElementById("doctor_id").addEventListener("change", function() {
            const tutorId = this.value;
            const studentDropdown = document.getElementById("patient_id");

            if (studentId) {
                fetch(`fetch_students.php?doctor_id=${studentId}`)
                    .then(response => response.json())
                    .then(data => {
                        studentDropdown.innerHTML = '<option value="">-- Select a Student --</option>';
                        
                        data.forEach(student => {
                            const option = document.createElement("option");
                            option.value = student.StudentID;
                            option.textContent = student.StudentName;
                            studentDropdown.appendChild(option);
                        });
                    })
                    .catch(error => console.error("Error fetching students:", error));
            } else {
                studentDropdown.innerHTML = '<option value="">-- Select a Student --</option>';
            }
            });
        });
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


            <div id="tutor_field" style="display: none;">
                <label for="tutor_id" class="form-label">Select Tutor:</label>
                <select id="tutor_id" name="tutor_id" class="form-select">
                    <option value="">-- Select a Tutor --</option>
                    <?php foreach ($tutors as $tutor): ?>
                        <option value="<?php echo htmlspecialchars($tutor['TutorID']); ?>">
                            <?php echo htmlspecialchars($doc['TutorName']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <br><br>
            </div>

            <label class="form-label">Appointment Type:</label><br>
            <input type="radio" id="homeworkhelp" name="appointment_type" value="homeworkhelp" onclick="toggleAppointmentType()">
            <label for="homeworkhelp">Homework Help</label><br>
            <input type="radio" id="testprep" name="appointment_type" value="testprep" onclick="toggleAppointmentType()">
            <label for="testprep">Test Prep</label><br>
            <input type="radio" id="meetup" name="appointment_type" value="meetup" onclick="toggleAppointmentType()">
            <label for="meetup">Meet Up</label><br><br>

            <!-- Homework Help Fields -->
            <div id="homeworkhelp_fields" style="display: none;">
                <label for="homeworkhelp_time" class="form-label">Select Date and Time:</label>
                <input type="datetime-local" id="homeworkhelp_time" name="homeworkhelp_time" class="form-input" min="<?php echo date('Y-m-d\TH:i'); ?>">
                <br><br>
                <label for="homeworkhelp_type" class="form-label">Homework Type:</label>
                <input type="text" id="homeworkhelp_type" name="homeworkhelp_type" class="form-input">
                <br><br>
            </div>

            <!-- Test Prep Fields -->
            <div id="testprep_fields" style="display: none;">
                <label for="testprep_time" class="form-label">Select Date and Time:</label>
                <input type="datetime-local" id="testprep_time" name="testprep_time" class="form-input" min="<?php echo date('Y-m-d\TH:i'); ?>">
                <br><br>
                <label for="testprep_type" class="form-label">Test Prep Type:</label>
                <input type="text" id="testprep_type" name="testprep_type" class="form-input">
                <br><br>
                <label for="testprep_location" class="form-label">Test Prep Location:</label>
                <input type="text" id="testprep_location" name="testprep_location" class="form-input">
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

            <input type="submit" value="Schedule Appointment" class="button">
        </form>
    </div>
</body>
</html>
