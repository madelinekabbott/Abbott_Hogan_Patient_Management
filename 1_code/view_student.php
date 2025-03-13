<?php
session_start();
require 'db_connect.php';
include 'header.php';

// Determine if user is an admin or a doctor
$isAdmin = isset($_SESSION['admin_id']);
$isTutor = isset($_SESSION['tutor_id']);

// If neither is logged in, redirect to login
if (!$isAdmin && !$isTutor) {
    header("Location: login.php");
    exit();
}

$student_id = $_GET['student_id'] ?? null;
$viewUrl = $isAdmin ? "manage_students.php" : "view_records.php";

if (!$student_id) {
    echo "No student specified.";
    exit();
}

if ($isTutor) {
    $tutor_id = $_SESSION['tutor_id'];

    $stmt = $pdo->prepare("
        SELECT 
            StudentName, DOB, Address, City, 
            ContactNumber, StudentInformation, 
            Class, Grade, PreferredStudyMethod
        FROM 
            Student
        JOIN 
            TutorStudent ON Student.StudentID = TutorStudent.StudentID
        WHERE 
            Student.StudentID = :student_id 
            AND TutorStudent.TutorID = :tutor_id
    ");
    $stmt->execute(['student_id' => $student_id, 'tutor_id' => $tutor_id]);
    $student = $stmt->fetch();

} else {
    $stmt = $pdo->prepare("
        SELECT 
            StudentName, DOB, Address, City, 
            ContactNumber, StudentInformation, 
            Class, Grade, PreferredStudyMethod
        FROM 
            Student
        WHERE Student.StudentID = :student_id
    ");
    $stmt->execute(['student_id' => $student_id]);
    $student = $stmt->fetch();
}

if (!$student) {
    echo "Student record not found or access is restricted.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student</title>
    <link rel="stylesheet" href="student_management_style.css"> 
</head>
<body>
    <div class="container">
        <h2>Patient Information</h2>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($student['PatientName']); ?></p>
        <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($student['DOB']); ?></p>
        <p><strong>Address:</strong> <?php echo htmlspecialchars($student['Address']); ?></p>
        <p><strong>City:</strong> <?php echo htmlspecialchars($student['City']); ?></p>
        <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($student['ContactNumber']); ?></p>
        <p><strong>Student Information:</strong> <?php echo htmlspecialchars($student['StudentInformation']); ?></p>
        <p><strong>Class:</strong> <?php echo htmlspecialchars($student['Grade']); ?></p>
        <p><strong>Grade:</strong> <?php echo htmlspecialchars($student['Class']); ?></p>
        <p><strong>Preferred Study Method:</strong> <?php echo htmlspecialchars($student['PreferredStudyMethod']); ?></p>

        <a href="edit_student.php?student_id=<?php echo $student_id; ?>" class="button">Edit Student Information</a>
        <br>
        <a href="<?php echo $viewUrl; ?>" class="button">Back to Student List</a>
    </div>
</body>
</html>
