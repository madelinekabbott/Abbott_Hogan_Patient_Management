<?php
session_start();
require 'db_connect.php';
include 'header.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

    $stmt = $pdo->prepare("DELETE FROM TutorStudent WHERE StudentID = :student_id");
    $stmt->execute(['student_id' => $delete_id]);

    $stmt = $pdo->prepare("DELETE FROM Student WHERE StudentID = :student_id");
    $stmt->execute(['student_id' => $delete_id]);

    header("Location: manage_students.php?deleted=true");
    exit();
}

$stmt = $pdo->query("
    SELECT 
        StudentID, StudentName, DOB, Address, City
    FROM 
        Student
    ORDER BY 
        StudentName ASC
");
$students = $stmt->fetchAll();

$deleted = isset($_GET['deleted']) && $_GET['deleted'] === 'true';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
    <link rel="stylesheet" href="student_management_style.css">
    <script>
        function confirmDelete(studentName) {
            return confirm("Are you sure you want to delete " + studentName + "?");
        }
    </script>
</head>
<body>
    <div class="container">
        <h2 class="header">Manage Students</h2>
        <?php if ($deleted): ?>
            <p class="success"></p>
        <?php endif; ?>
        <table class="table">
            <thead>
                <tr>
                    <th class="table-header">Name</th>
                    <th class="table-header">Date of Birth</th>
                    <th class="table-header">Address</th>
                    <th class="table-header">City</th>
                    <th class="table-header">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($students)): ?>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td class="table-cell"><?php echo htmlspecialchars($student['PatientName']); ?></td>
                            <td class="table-cell"><?php echo htmlspecialchars($student['DOB']); ?></td>
                            <td class="table-cell"><?php echo htmlspecialchars($student['Address']); ?></td>
                            <td class="table-cell"><?php echo htmlspecialchars($student['City']); ?></td>
                            <td class="table-cell">
                                <a href="view_student.php?student_id=<?php echo $student['StudentID']; ?>" class="button-link">
                                    <button class="button">View Details</button>
                                </a>
                                <a href="manage_students.php?delete=<?php echo $student['StudentID']; ?>"
                                   class="button-link"
                                   onclick="return confirmDelete('<?php echo htmlspecialchars($student['StudentName']); ?>');">
                                    <button class="button button-delete">Delete Record</button>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="table-cell" colspan="5">No students found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <br>
        <a href="create_student.php" class="button">Create New Student</a>
        <br><br>
        <a href="logout.php" class="link">Logout</a>
    </div>
</body>
</html>
