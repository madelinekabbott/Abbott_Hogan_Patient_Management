<?php
session_start();
require 'db_connect.php';
include 'header.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$edit_doctor_id = $_GET['doctor_id'] ?? null;

if (!$edit_doctor_id) {
    echo "No doctor specified.";
    exit();
}

$stmt = $pdo->prepare("
    SELECT DoctorName, Department, DOB, Address, Email, PhoneNumber, password
    FROM Doctor
    WHERE DoctorID = :doctor_id
");
$stmt->execute(['doctor_id' => $edit_doctor_id]);
$doctor = $stmt->fetch();

if (!$doctor) {
    echo "Doctor record not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doctorName = $_POST['doctor_name'];
    $department = $_POST['department'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phone_number'];
    $newPassword = $_POST['password'];

    if (empty($newPassword)) {
        $updateStmt = $pdo->prepare("
            UPDATE Doctor 
            SET DoctorName = :doctor_name, Department = :department, DOB = :dob, Address = :address, 
                Email = :email, PhoneNumber = :phone_number
            WHERE DoctorID = :doctor_id
        ");
        $params = [
            'doctor_name' => $doctorName,
            'department' => $department,
            'dob' => $dob,
            'address' => $address,
            'email' => $email,
            'phone_number' => $phoneNumber,
            'doctor_id' => $edit_doctor_id
        ];
    } else {
        $hashed_password = $newPassword;

        $updateStmt = $pdo->prepare("
            UPDATE Doctor 
            SET DoctorName = :doctor_name, Department = :department, DOB = :dob, Address = :address, 
                Email = :email, PhoneNumber = :phone_number, password = :password
            WHERE DoctorID = :doctor_id
        ");
        $params = [
            'doctor_name' => $doctorName,
            'department' => $department,
            'dob' => $dob,
            'address' => $address,
            'email' => $email,
            'phone_number' => $phoneNumber,
            'password' => $hashed_password,
            'doctor_id' => $edit_doctor_id
        ];
    }

    $updateStmt->execute($params);

    header("Location: manage_doctors.php?doctor_id=" . $edit_doctor_id . "&updated=true");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Doctor</title>
    <link rel="stylesheet" href="patient_management_style.css"> 
</head>
<body>
    <div class="container">
        <h2 class="header">Edit Doctor Information</h2>
        <form action="" method="post">
            <div class="form-group text">
                <label for="doctor_name">Doctor Name:</label>
                <input type="text" name="doctor_name" id="doctor_name" value="<?php echo htmlspecialchars($doctor['DoctorName']); ?>" required>
            </div>
            <div class="form-group text">
                <label for="department">Department:</label>
                <input type="text" name="department" id="department" value="<?php echo htmlspecialchars($doctor['Department']); ?>" required>
            </div>
            <div class="form-group text">
                <label for="dob">Date of Birth:</label>
                <input type="date" name="dob" id="dob" value="<?php echo htmlspecialchars($doctor['DOB']); ?>" required>
            </div>
            <div class="form-group text">
                <label for="address">Address:</label>
                <input type="text" name="address" id="address" value="<?php echo htmlspecialchars($doctor['Address']); ?>" required>
            </div>
            <div class="form-group text">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($doctor['Email']); ?>" required>
            </div>
            <div class="form-group text">
                <label for="phone_number">Phone Number:</label>
                <input type="text" name="phone_number" id="phone_number" value="<?php echo htmlspecialchars($doctor['PhoneNumber']); ?>" required>
            </div>
            <div class="form-group text">
                <label for="password">New Password (leave blank to keep the same):</label>
                <input type="password" name="password" id="password">
            </div>
            <div class="form-group text">
                <button type="submit" class="button">Update Doctor Information</button>
            </div>
        </form>
        <a href="manage_doctors.php?doctor_id=<?php echo urlencode($edit_doctor_id); ?>" class="button">Cancel</a>
    </div>
</body>
</html>
