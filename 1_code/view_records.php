<?php
session_start();
require 'db_connect.php';
include 'header.php';

if (!isset($_SESSION['tutor_id'])) {
    header("Location: login.php");
    exit();
}

$doctor_id = $_SESSION['tutor_id'];

$stmt = $pdo->prepare("
    SELECT 
        Student.StudentID, Student.StudentName, Student.DOB, Student.Address, Student.City 
    FROM 
        Student 
    JOIN 
        TutorStudent ON Student.StudentID = TutorStudent.StudentID
    WHERE 
        TutorStudent.TutorID = :tutor_id
");
$stmt->execute(['tutor_id' => $tutor_id]);
$students = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Records</title>
    <link rel="stylesheet" href="student_management_style.css">
</head>
<body>
    <div class="container">
    <h2 class="header">Student Records</h2>
    <table class="table">
        <thead>
            <tr>
                <th class="table-header">Name</th>
                <th class="table-header">Date of Birth</th>
                <th class="table-header">Address</th>
                <th class="table-header">City</th>
                <th class="table-header"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $student): ?>
                <tr>
                    <td class="table-cell"><?php echo htmlspecialchars($student['StudentName']); ?></td>
                    <td class="table-cell"><?php echo htmlspecialchars($student['DOB']); ?></td>
                    <td class="table-cell"><?php echo htmlspecialchars($student['Address']); ?></td>
                    <td class="table-cell"><?php echo htmlspecialchars($student['City']); ?></td>
                    <td class="table-cell">
                        <a href="view_student.php?student_id=<?php echo $student['StudentID']; ?>" class="button-link">
                            <button class="button">View Details</button>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <br>
    <a href="logout.php" class="link">Logout</a>
</div>
</body>
</html>
