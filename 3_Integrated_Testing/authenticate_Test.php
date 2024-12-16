<?php

namespace Zubair\TestProject;

use PHPUnit\Framework\TestCase;

final class authenticate_Test extends TestCase
{


<?php
session_start();
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];  
    $password = $_POST['password'];
    $role = $_POST['role'];        

    if ($role === 'doctor') {
        $stmt = $pdo->prepare("SELECT * FROM Doctor WHERE DoctorID = :username AND password = :password");
    } elseif ($role === 'admin') {
        $stmt = $pdo->prepare("SELECT * FROM Admin WHERE AdminID = :username AND password = :password");
    } else {
        header("Location: login.php?error=1");
        exit();
    }

    $stmt->execute(['username' => $username, 'password' => $password]);
    $user = $stmt->fetch();

    if ($user) {
        if ($role === 'doctor') {
            $_SESSION['doctor_id'] = $user['DoctorID'];
            $_SESSION['doctor_name'] = $user['DoctorName'];
            header("Location: doctor_dashboard.php");  
        } elseif ($role === 'admin') {
            $_SESSION['admin_id'] = $user['AdminID'];
            $_SESSION['admin_name'] = $user['AdminName'];
            header("Location: admin_dashboard.php"); 
        }
        exit();
    } else {
        if ($role === 'doctor') {
            header("Location: login.php?error=doctor");
        } elseif ($role === 'admin') {
            header("Location: login.php?error=admin");
        }
        exit();
    }
}
?>
}