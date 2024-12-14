<?php
$host = 'localhost'; 
$db = 'patient_management';  
$user = 'SystemDesignUser';  
$pass = 'aws012kL33kn';  

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
