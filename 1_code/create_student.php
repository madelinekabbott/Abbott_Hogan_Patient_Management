<?php
session_start();
require 'db_connect.php';
include 'header.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$errorMessage = ""; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId          = $_POST['student_id'];
    $studentName        = $_POST['student_name'];
    $address            = $_POST['address'];
    $city               = $_POST['city'];
    $contactNumber      = $_POST['contact_number'];
    $dob                = $_POST['dob'];
    $studentInformation = $_POST['student_information'] ?? null;
    $class              = $_POST['class'] ?? null;
    $grade          = $_POST['grade'] ?? null;
    $preferredStudyMethod  = $_POST['preferred_study_method'] ?? null;

    $stmt = $pdo->prepare("
        INSERT INTO Student (
            StudentID, 
            StudentName, 
            Address, 
            City, 
            ContactNumber, 
            DOB, 
            StudentInformation, 
            Class, 
            Grade, 
            PreferredStudyMethod
        ) VALUES (
            :student_id, 
            :student_name, 
            :address, 
            :city, 
            :contact_number, 
            :dob, 
            :student_information, 
            :class, 
            :grade, 
            :preferred_study_method
        )
    ");

    try {
        $stmt->execute([
            'student_id'           => $studentId,
            'student_name'         => $studentName,
            'address'              => $address,
            'city'                 => $city,
            'contact_number'       => $contactNumber,
            'dob'                  => $dob,
            'student_information'  => $studentInformation,
            'class'                => $class,
            'grade'                => $grade,
            'preferred_study_method'   => $preferredStudyMethod
        ]);

        header("Location: manage_students.php?created=true");
        exit();
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $errorMessage = "That StudentID is already in use.";
        } else {
            $errorMessage = "Error inserting student: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Student</title>
    <link rel="stylesheet" href="student_management_style.css">
</head>
<body>
    <?php if (!empty($errorMessage)): ?>
        <script>alert("<?php echo htmlspecialchars($errorMessage); ?>");</script>
    <?php endif; ?>

    <div class="container">
        <h2 class="header">Create New Student</h2>
        <form action="" method="POST">
            <table class="form-table">
                <!-- Student ID -->
                <tr>
                    <td><label for="student_id">Student ID:</label></td>
                    <td><input type="text" name="student_id" id="student_id" style="width: 200px;" required></td>
                </tr>
                <!-- Student Name -->
                <tr>
                    <td><label for="student_name">Student Name:</label></td>
                    <td><input type="text" name="student_name" id="student_name" style="width: 200px;" required></td>
                </tr>
                <!-- Address -->
                <tr>
                    <td><label for="address">Address:</label></td>
                    <td><input type="text" name="address" id="address" style="width: 200px;" required></td>
                </tr>
                <!-- City -->
                <tr>
                    <td><label for="city">City:</label></td>
                    <td><input type="text" name="city" id="city" style="width: 200px;" required></td>
                </tr>
                <!-- Contact Number -->
                <tr>
                    <td><label for="contact_number">Contact Number:</label></td>
                    <td><input type="text" name="contact_number" id="contact_number" style="width: 200px;" required></td>
                </tr>
                <!-- Date of Birth -->
                <tr>
                    <td><label for="dob">Date of Birth:</label></td>
                    <td><input type="date" name="dob" id="dob" required></td>
                </tr>
                <!-- Student Information (optional) -->
                <tr>
                    <td><label for="student_information">Student Information:</label></td>
                    <td><textarea name="student_information" id="student_information" rows="3"></textarea></td>
                </tr>
                <!-- Class (optional) -->
                <tr>
                    <td><label for="class">Class:</label></td>
                    <td><textarea name="class" id="class" rows="3"></textarea></td>
                </tr>
                <!-- Grade (optional) -->
                <tr>
                    <td><label for="grade">Grade:</label></td>
                    <td><textarea name="grade" id="grade" rows="3"></textarea></td>
                </tr>
                <!-- Preferred Study Mehtod (optional) -->
                <tr>
                    <td><label for="preferred_study_method">Preferred Study Method:</label></td>
                    <td><input type="text" name="preferred_study_method" id="preferred_study_method" style="width: 200px;"></td>
                </tr>
                <!-- Submit / Cancel -->
                <tr>
                    <td colspan="2" style="text-align:center;">
                        <button type="submit" class="button">Create Patient</button>
                        <a href="manage_patients.php" class="button">Cancel</a>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</body>
</html>
