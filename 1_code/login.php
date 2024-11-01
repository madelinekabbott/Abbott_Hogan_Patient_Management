<?php
    include 'header_no_dash.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
    <link rel="stylesheet" href="patient_management_style.css">
    <script>
        // Display an alert if there's an error in the URL
        function showErrorPopup() {
            alert("Invalid Doctor ID or password.");
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Log In</h2>
        <form action="authenticate.php" method="POST">
            <label for="doctor_id">Doctor ID:</label>
            <input type="text" id="doctor_id" name="doctor_id" required><br><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br><br>

            <input type="submit" value="Log In" class="button">
        </form>
    </div>
    <?php
    if (isset($_GET['error']) && $_GET['error'] == 1) {
        echo "<script>showErrorPopup();</script>";
    }
    ?>
</body>
</html>
