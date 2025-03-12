<?php
session_start();
require 'db_connect.php';
include 'header.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$selected_tutor_id = $_GET['tutor_id'] ?? null;

$stmt = $pdo->query("SELECT TutorID, TutorName FROM Tutor ORDER BY TutorName ASC");
$all_tutors = $stmt->fetchAll(PDO::FETCH_ASSOC);

$doctor = null;
$patients = [];

if ($selected_tutor_id) {
    $stmt = $pdo->prepare("
        SELECT TutorID, TutorName, Department, password, DOB, Address, Email, PhoneNumber
        FROM Tutor
        WHERE TutorID = :tutor_id
    ");
    $stmt->execute(['tutor_id' => $selected_tutor_id]);
    $tutor = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($tutor) {
        $stmt = $pdo->prepare("
            SELECT Student.StudentID, Student.StudentName, Student.ContactNumber
            FROM Student
            JOIN TutorStudent ON Student.StudentID = TutorStudent.StudentID
            WHERE TutorStudent.TutorID = :tutor_id
            ORDER BY Student.StudentName ASC
        ");
        $stmt->execute(['tutor_id' => $selected_tutor_id]);
        $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

function censorPassword($password) {
    if (empty($password)) {
        return '';
    }
    return str_repeat('*', 8);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tutors</title>
    <link rel="stylesheet" href="student_management_style.css"> 
</head>
<body>
    <div class="container">
        <h2>Manage Tutors</h2>
        <p>Select a tutor from the dropdown to view their information.</p>

        <form action="manage_tutors.php" method="GET">
            <label for="tutor_id">Tutor:</label>
            <select name="tutor_id" id="tutor_id" required>
                <option value="">-- Select a Tutor --</option>
                <?php foreach ($all_tutors as $tut): ?>
                    <option value="<?php echo htmlspecialchars($tut['TutorID']); ?>" 
                        <?php echo ($selected_tutor_id == $tut['TutorID']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($tut['TutorName']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="submit" value="View Tutor" class="button">
        </form>

        <?php if ($selected_tutor_id && $tutor): ?>
            <h3>Tutor Information</h3>
            <p><strong>Tutor ID:</strong> <?php echo htmlspecialchars($tutor['TutorID']); ?></p>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($tutor['TutorName']); ?></p>
            <p><strong>Department:</strong> <?php echo htmlspecialchars($tutor['Department']); ?></p>
            <p><strong>Password:</strong> 
                <span id="passwordField" data-password="<?php echo htmlspecialchars($tutor['password']); ?>">
                    <?php echo censorPassword($tutor['password']); ?>
                </span>
                <button type="button" id="togglePasswordBtn">Show password?</button>
            </p>
            <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($tutor['DOB']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($tutor['Address']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($tutor['Email']); ?></p>
            <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($tutor['PhoneNumber']); ?></p>

            <a href="edit_tutor.php?tutor_id=<?php echo urlencode($tutor['TutorID']); ?>" class="button">Edit Tutor Information</a>

            <h3>Patients</h3>
            <?php if (!empty($students)): ?>
                <ul class="students">
                    <?php foreach ($students as $student): ?>
                        <li>
                            <?php echo htmlspecialchars($student['StudentName']); ?> - 
                            <?php echo htmlspecialchars($student['ContactNumber']); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>This tutor has no students assigned currently.</p>
            <?php endif; ?>

            <!-- Button to manage students under this doctor -->
            <a href="manage_tutor_students.php?tutor_id=<?php echo urlencode($tutor['TutorID']); ?>" class="button">
                Manage Students for Tutor. <?php echo htmlspecialchars($doctor['TutorName']); ?>
            </a>

        <?php elseif ($selected_tutor_id && !$tutor): ?>
            <p>No tutor found with the specified ID.</p>
        <?php endif; ?>

        <hr>
        <a href="create_tutor.php" class="button">Create New Tutor</a>
        
    </div>

    <script>
        const toggleBtn = document.getElementById('togglePasswordBtn');
        const passwordField = document.getElementById('passwordField');

        let isPasswordHidden = true; // current state is hidden (asterisks)

        toggleBtn.addEventListener('click', function() {
            if (isPasswordHidden) {
                // Show the actual password
                passwordField.textContent = passwordField.getAttribute('data-password');
                toggleBtn.textContent = "Hide password?";
            } else {
                // Hide the password again (censored)
                passwordField.textContent = "********";
                toggleBtn.textContent = "Show password?";
            }
            isPasswordHidden = !isPasswordHidden;
        });
    </script>
</body>
</html>
