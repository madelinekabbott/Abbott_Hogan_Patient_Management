<?php
session_start();
require 'db_connect.php';

$loginError = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $doctor_id = $_POST['doctor_id'];
    $password = $_POST['password'];

    // Check if credentials match any doctor in the database
    $stmt = $pdo->prepare("SELECT * FROM Doctor WHERE DoctorID = :doctor_id AND password = :password");
    $stmt->execute(['doctor_id' => $doctor_id, 'password' => $password]);
    $doctor = $stmt->fetch();

    if ($doctor) {
        $_SESSION['doctor_id'] = $doctor['DoctorID'];
        $_SESSION['doctor_name'] = $doctor['DoctorName'];
        header("Location: dashboard.php");  // Redirect to the dashboard on success
        exit();
    } else {
        header("Location: login.php?error=1");
    }
}
?>
