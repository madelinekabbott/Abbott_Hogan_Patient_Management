<?php
session_start();
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];  
    $password = $_POST['password'];
    $role = $_POST['role'];        

    if ($role === 'tutor') {
        $stmt = $pdo->prepare("SELECT * FROM Tutor WHERE TutorID = :username AND password = :password");
    } elseif ($role === 'admin') {
        $stmt = $pdo->prepare("SELECT * FROM Admin WHERE AdminID = :username AND password = :password");
    } else {
        header("Location: login.php?error=1");
        exit();
    }

    $stmt->execute(['username' => $username, 'password' => $password]);
    $user = $stmt->fetch();

    if ($user) {
        if ($role === 'tutor') {
            $_SESSION['tutor_id'] = $user['DoctorID'];
            $_SESSION['tutor_name'] = $user['DoctorName'];
            header("Location: doctor_dashboard.php");  
        } elseif ($role === 'admin') {
            $_SESSION['admin_id'] = $user['AdminID'];
            $_SESSION['admin_name'] = $user['AdminName'];
            header("Location: admin_dashboard.php"); 
        }
        exit();
    } else {
        if ($role === 'tutor') {
            header("Location: login.php?error=tutor");
        } elseif ($role === 'admin') {
            header("Location: login.php?error=admin");
        }
        exit();
    }
}
?>
