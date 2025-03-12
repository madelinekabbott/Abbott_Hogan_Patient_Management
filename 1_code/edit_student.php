<?php
session_start();
require 'db_connect.php';
include 'header.php';

$isAdmin = isset($_SESSION['admin_id']);
$isTutor = isset($_SESSION['tutor_id']);

if (!$isAdmin && !$isTutor) {
    header("Location: login.php");
    exit();
}

$patient_id = $_GET['student_id'] ?? null;

if (!$student_id) {
    echo "No Student specified.";
    exit();
}

try {
    if ($isTutor) {
        $doctor_id = $_SESSION['tutor_id'];
        $stmt = $pdo->prepare("
            SELECT 
                StudentName, DOB, Address, City, 
                ContactNumber, StudentInformation, 
                Prescriptions, Diagnoses, PreferredPharmacy
            FROM 
                Student
            JOIN 
                TutorStudent ON Student.StudentID = TutorStudent.StudentID
            WHERE 
                Student.StudentID = :student_id 
                AND TutorStudent.TutorID = :tutor_id
        ");
        $stmt->execute(['student_id' => $student_id, 'tutor_id' => $tutor_id]);
    } else {
        $stmt = $pdo->prepare("
            SELECT 
                StudentName, DOB, Address, City, 
                ContactNumber, StudentInformation, 
                Prescriptions, Diagnoses, PreferredPharmacy
            FROM 
                Student
            WHERE 
                Student.StudentID = :student_id
        ");
        $stmt->execute(['student_id' => $student_id]);
    }

    $student = $stmt->fetch();

    if (!$student) {
        echo "Student record not found or access is restricted.";
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $studentName = $_POST['student_name'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $contactNumber = $_POST['contact_number'];
    $studentInformation = $_POST['student_information'];
    $prescriptions = $_POST['prescriptions'];
    $diagnoses = $_POST['diagnoses'];
    $preferredPharmacy = $_POST['preferred_pharmacy'];

    try {
        $updateStmt = $pdo->prepare("
            UPDATE Student
            SET StudentName = :student_name, DOB = :dob, Address = :address, 
                City = :city, ContactNumber = :contact_number, 
                StudentInformation = :student_information, Prescriptions = :prescriptions, 
                Diagnoses = :diagnoses, PreferredPharmacy = :preferred_pharmacy 
            WHERE StudentID = :student_id
        ");
        
        $updateStmt->execute([
            'student_name' => $studentName,
            'dob' => $dob,
            'address' => $address,
            'city' => $city,
            'contact_number' => $contactNumber,
            'student_information' => $studentInformation,
            'prescriptions' => $prescriptions,
            'diagnoses' => $diagnoses,
            'preferred_pharmacy' => $preferredPharmacy,
            'student_id' => $student_id
        ]);

        header("Location: view_student.php?student_id=" . $student_id . "&updated=true");
        exit();
    } catch (PDOException $e) {
        echo "Database Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link rel="stylesheet" href="student_management_style.css"> 
</head>
<body>
    <div class="container">
        <h2 class="header">Edit Student Information</h2>
        <form action="" method="post">
            <div class="form-group text">
                <label for="student_name">Student Name:</label>
                <input type="text" name="student_name" id="student_name" value="<?php echo htmlspecialchars($student['StudentName']); ?>" required>
            </div>
            <div class="form-group text">
                <label for="dob">Date of Birth:</label>
                <input type="date" name="dob" id="dob" value="<?php echo htmlspecialchars($student['DOB']); ?>" required>
            </div>
            <div class="form-group text">
                <label for="address">Address:</label>
                <input type="text" name="address" id="address" value="<?php echo htmlspecialchars($student['Address']); ?>" required>
            </div>
            <div class="form-group text">
                <label for="city">City:</label>
                <input type="text" name="city" id="city" value="<?php echo htmlspecialchars($student['City']); ?>" required>
            </div>
            <div class="form-group text">
                <label for="contact_number">Contact Number:</label>
                <input type="text" name="contact_number" id="contact_number" value="<?php echo htmlspecialchars($student['ContactNumber']); ?>" required>
            </div>
            <div class="form-group text">
                <label for="patient_information">Additional Information:</label>
                <textarea name="patient_information" id="patient_information" rows="4"><?php echo htmlspecialchars($student['StudentInformation']); ?></textarea>
            </div>
            <div class="form-group text">
                <label for="prescriptions">Prescriptions:</label>
                <textarea name="prescriptions" id="prescriptions" rows="4"><?php echo htmlspecialchars($student['Prescriptions']); ?></textarea>
            </div>
            <div class="form-group text">
                <label for="diagnoses">Diagnoses:</label>
                <textarea name="diagnoses" id="diagnoses" rows="4"><?php echo htmlspecialchars($student['Diagnoses']); ?></textarea>
            </div>
            <div class="form-group text">
                <label for="preferred_pharmacy">Preferred Pharmacy:</label>
                <input type="text" name="preferred_pharmacy" id="preferred_pharmacy" value="<?php echo htmlspecialchars($student['PreferredPharmacy']); ?>">
            </div>
            <div class="form-group text">
                <button type="submit" class="button">Update Student Information</button>
            </div>
        </form>
        <a href="view_patient.php?patient_id=<?php echo $student_id; ?>" class="button">Cancel</a>
    </div>
</body>
</html>
