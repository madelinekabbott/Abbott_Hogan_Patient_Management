<?php
require 'db_connect.php';

if (isset($_GET['tutor_id'])) {
    $tutor_id = $_GET['tutor_id'];

    $stmt = $pdo->prepare("SELECT Student.* FROM Student JOIN TutorStudent ON Student.StudentID = TutorStudent.StudentID WHERE TutorStudent.TutorID = :tutor_id");
    $stmt->execute(['tutor_id' => $tutor_id]);
    $students = $stmt->fetchAll();

    echo json_encode($students);
    exit();
}
?>
