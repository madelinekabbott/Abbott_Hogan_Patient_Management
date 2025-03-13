<?php
session_start();
require 'db_connect.php';
include 'header.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$edit_tutor_id = $_GET['tutor_id'] ?? null;

if (!$edit_tutor_id) {
    echo "No tutor specified.";
    exit();
}

$stmt = $pdo->prepare("
    SELECT TutorName, Department, DOB, Address, Email, PhoneNumber, password
    FROM Tutor
    WHERE TutorID = :tutor_id
");
$stmt->execute(['tutor_id' => $edit_tutor_id]);
$tutor = $stmt->fetch();

if (!$tutor) {
    echo "Tutor record not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tutorName = $_POST['Tutor_name'];
    $department = $_POST['department'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phone_number'];
    $newPassword = $_POST['password'];

    if (empty($newPassword)) {
        $updateStmt = $pdo->prepare("
            UPDATE Tutor 
            SET TutorName = :tutor_name, Department = :department, DOB = :dob, Address = :address, 
                Email = :email, PhoneNumber = :phone_number
            WHERE TutorID = :tutor_id
        ");
        $params = [
            'tutor_name' => $tutorName,
            'department' => $department,
            'dob' => $dob,
            'address' => $address,
            'email' => $email,
            'phone_number' => $phoneNumber,
            'tutor_id' => $edit_tutor_id
        ];
    } else {
        $hashed_password = $newPassword;

        $updateStmt = $pdo->prepare("
            UPDATE Tutor 
            SET TutorName = :tutor_name, Department = :department, DOB = :dob, Address = :address, 
                Email = :email, PhoneNumber = :phone_number, password = :password
            WHERE TutorID = :tutor_id
        ");
        $params = [
            'tutor_name' => $tutorName,
            'department' => $department,
            'dob' => $dob,
            'address' => $address,
            'email' => $email,
            'phone_number' => $phoneNumber,
            'password' => $hashed_password,
            'tutor_id' => $edit_tutor_id
        ];
    }

    $updateStmt->execute($params);

    header("Location: manage_tutors.php?tutor_id=" . $edit_tutor_id . "&updated=true");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Doctor</title>
    <link rel="stylesheet" href="student_management_style.css"> 
</head>
<body>
    <div class="container">
        <h2 class="header">Edit Tutor Information</h2>
        <form action="" method="post">
            <div class="form-group text">
                <label for="tutor_name">Tutor Name:</label>
                <input type="text" name="tutor_name" id="tutor_name" value="<?php echo htmlspecialchars($tutor['TutorName']); ?>" required>
            </div>
            <div class="form-group text">
                <label for="department">Department:</label>
                <input type="text" name="department" id="department" value="<?php echo htmlspecialchars($tutor['Department']); ?>" required>
            </div>
            <div class="form-group text">
                <label for="dob">Date of Birth:</label>
                <input type="date" name="dob" id="dob" value="<?php echo htmlspecialchars($tutor['DOB']); ?>" required>
            </div>
            <div class="form-group text">
                <label for="address">Address:</label>
                <input type="text" name="address" id="address" value="<?php echo htmlspecialchars($tutor['Address']); ?>" required>
            </div>
            <div class="form-group text">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($tutor['Email']); ?>" required>
            </div>
            <div class="form-group text">
                <label for="phone_number">Phone Number:</label>
                <input type="text" name="phone_number" id="phone_number" value="<?php echo htmlspecialchars($tutor['PhoneNumber']); ?>" required>
            </div>
            <div class="form-group text">
                <label for="password">New Password (leave blank to keep the same):</label>
                <input type="password" name="password" id="password">
            </div>
            <div class="form-group text">
                <button type="submit" class="button">Update Tutor Information</button>
            </div>
        </form>
        <a href="manage_tutors.php?tutor_id=<?php echo urlencode($edit_doctor_id); ?>" class="button">Cancel</a>
    </div>
</body>
</html>
