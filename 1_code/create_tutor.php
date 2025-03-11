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
    $tutorID    = $_POST['tutor_id'];
    $tutorName  = $_POST['tutor_name'];
    $department  = $_POST['department'];
    $password    = $_POST['password'];
    $dob         = $_POST['dob'];
    $address     = $_POST['address'];
    $email       = $_POST['email'];
    $phoneNumber = $_POST['phone_number'];
    $hashed_password = $password;

    $stmt = $pdo->prepare("
        INSERT INTO Tutor (
            TutorID,
            TutorName,
            Department,
            password,
            DOB,
            Address,
            Email,
            PhoneNumber
        ) VALUES (
            :tutor_id,
            :tutor_name,
            :department,
            :password,
            :dob,
            :address,
            :email,
            :phone_number
        )
    ");

    try {
        $stmt->execute([
            'tutor_id'    => $doctorID,
            'tutor_name'  => $doctorName,
            'department'   => $department,
            'password'     => $hashed_password,
            'dob'          => $dob,
            'address'      => $address,
            'email'        => $email,
            'phone_number' => $phoneNumber
        ]);

        header("Location: manage_tutors.php?created=true");
        exit();
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $errorMessage = "That TutorID is already in use.";
        } else {
            $errorMessage = "Error creating tutor: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Tutor</title>
    <link rel="stylesheet" href="student_management_style.css">
</head>
<body>
    <?php if (!empty($errorMessage)): ?>
        <script>alert("<?php echo htmlspecialchars($errorMessage); ?>");</script>
    <?php endif; ?>

    <div class="container">
        <h2 class="header">Create New Tutor</h2>
        <form action="" method="POST">
            <table class="form-table">
                <tr>
                    <td><label for="tutor_id">Tutor ID:</label></td>
                    <td><input type="text" name="tutor_id" id="tutor_id" style="width: 200px;" required></td>
                </tr>
                <tr>
                    <td><label for="tutor_name">Tutor Name:</label></td>
                    <td><input type="text" name="tutor_name" id="tutor_name" style="width: 200px;" required></td>
                </tr>
                <tr>
                    <td><label for="department">Department:</label></td>
                    <td><input type="text" name="department" id="department" style="width: 200px;" required></td>
                </tr>
                <tr>
                    <td><label for="password">Password:</label></td>
                    <td><input type="password" name="password" id="password" style="width: 200px;" required></td>
                </tr>
                <tr>
                    <td><label for="dob">Date of Birth:</label></td>
                    <td><input type="date" name="dob" id="dob" required></td>
                </tr>
                <tr>
                    <td><label for="address">Address:</label></td>
                    <td><input type="text" name="address" id="address" style="width: 200px;" required></td>
                </tr>
                <tr>
                    <td><label for="email">Email:</label></td>
                    <td><input type="email" name="email" id="email" style="width: 200px;" required></td>
                </tr>
                <tr>
                    <td><label for="phone_number">Phone Number:</label></td>
                    <td><input type="text" name="phone_number" id="phone_number" style="width: 200px;" required></td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;">
                        <button type="submit" class="button">Create Tutor</button>
                        <a href="manage_tutors.php" class="button">Cancel</a>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</body>
</html>
