<?php
session_start();
require 'db_connect.php';
include 'header.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$tutor_id = $_GET['tutor_id'] ?? null;

if (!$tutor_id) {
    echo "No tutor specified.";
    exit();
}

$stmt = $pdo->prepare("SELECT TutorName FROM Tutor WHERE TutorID = :tutor_id");
$stmt->execute(['tutor_id' => $tutor_id]);
$tutor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tutor) {
    echo "No tutor found with the specified ID.";
    exit();
}

$allStudentsStmt = $pdo->query("SELECT StudentID, StudentName FROM Student ORDER BY StudentName ASC");
$all_students = $allStudentsStmt->fetchAll(PDO::FETCH_ASSOC);

$assignedStudentsStmt = $pdo->prepare("
    SELECT StudentID 
    FROM TutorStudent 
    WHERE TutorID = :tutor_id
");
$assignedStudentsStmt->execute(['tutor_id' => $tutor_id]);
$assigned_students = $assignedStudentsStmt->fetchAll(PDO::FETCH_COLUMN, 0);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $selected_students = $_POST['students'] ?? [];

    $deleteStmt = $pdo->prepare("DELETE FROM TutorStudent WHERE TutorID = :tutor_id");
    $deleteStmt->execute(['tutor_id' => $tutor_id]);

    $insertStmt = $pdo->prepare("INSERT INTO TutorStudent (TutorID, StudentID) VALUES (:tutor_id, :student_id)");
    foreach ($selected_students as $student_id) {
        $insertStmt->execute(['tutor_id' => $tutor_id, 'student_id' => $student_id]);
    }

    header("Location: manage_tutor_students.php?tutor_id=".$tutor_id."&updated=true");
    exit();
}

$updated = isset($_GET['updated']) && $_GET['updated'] === 'true';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students for Tutor <?php echo htmlspecialchars($tutor['TutorName']); ?></title>
    <link rel="stylesheet" href="student_management_style.css">
</head>
<body>
    <div class="container">
        <h2>Manage Students for Tutor <?php echo htmlspecialchars($tutor['TutorName']); ?></h2>
        <?php if ($updated): ?>
            <p class="success">Student assignments have been successfully updated!</p>
        <?php endif; ?>
        
        <form method="POST" action="">
            <p>Select the students you want to assign to a Tutor. <?php echo htmlspecialchars($tutor['TutorName']); ?>:</p>
            <div class="checkbox-container">
                <?php foreach ($all_students as $student): ?>
                    <div>
                        <input type="checkbox" name="students[]" id="student_<?php echo $patient['StudentID']; ?>" 
                            value="<?php echo $student['StudentID']; ?>" 
                            <?php echo in_array($student['StudentID'], $assigned_students) ? 'checked' : ''; ?>>
                        <label for="student_<?php echo $student['StudentID']; ?>">
                            <?php echo htmlspecialchars($student['StudentName']); ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
            <br>
            <button type="submit" class="button">Update Student Assignments</button>
        </form>
        <br>
        <a href="manage_tutor.php?tutor_id=<?php echo urlencode($tutor_id); ?>" class="button">Back to Manage Tutor</a>
    </div>
</body>
</html>
